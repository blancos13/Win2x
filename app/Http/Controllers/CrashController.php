<?php namespace App\Http\Controllers;

use App\User;
use App\Profit;
use App\Crash;
use App\CrashBets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DB;

class CrashController extends Controller {
	
    public function __construct() {
        parent::__construct();
        $this->game = Crash::orderBy('id', 'desc')->first();
		DB::connection()->getPdo()->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    }
	
	public function index() {
		if(is_null($this->game)) $this->game = Crash::create([
			'hash' => $this->getSecret()
 		]);
        $game = [
            'id' => $this->game->id,
            'hash' => $this->game->hash,
            'price' => CrashBets::where('round_id', $this->game->id)->sum('price'),
            'bets' => $this->getBets()
        ];
        $bet = ($this->user) ? CrashBets::where('user_id', $this->user->id)->where('round_id', $this->game->id)->where('status', 0)->first() : null;
        $history = $this->getHistory();
        return view('pages.crash', compact('game', 'bet', 'history'));
	}
	
	private function getBets()
    {
        $list = CrashBets::where('round_id',  $this->game->id)->orderBy('id', 'desc')->get();
        $bets = [];
        foreach($list as $bet)
        {
            $user = User::where('id', $bet->user_id)->first();
            if(!is_null($user)) $bets[] = [
                'user' => [
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'unique_id' => $user->unique_id
                ],
                'price' => $bet->price,
                'withdraw' => round($bet->withdraw, 2),
                'color' => $this->getNumberColor($bet->withdraw),
                'won' => round($bet->won, 2),
                'balType' => $bet->balType,
                'status' => $bet->status
            ];
        }

        return $bets;
    }

    private function getNumberColor($n) {
        if($n > 6.49) return '#037cf3';
        if($n > 4.49) return '#1337d4';
        if($n > 2.99) return '#7118d4';
        if($n > 1.99) return '#a8128f';
        return '#cf1213';
    }

    public function newBet(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		if($this->user->ban) return;
        if($this->game->status > 0) return [
            'success' => false,
            'msg' => 'Bets in this round are closed!'
        ];
		
		$balType = $r->get('balType');
		
		if($balType != 'balance' && $balType != 'bonus') return [
            'success' => false,
            'msg' => 'Unable to determine your balance type!'
        ];
		
        if(floatval($r->get('bet')) < $this->settings->crash_min_bet) return [
            'success' => false,
            'msg' => 'Minimum bet amount - ' . $this->settings->crash_min_bet
        ];

        if($this->settings->crash_max_bet > 0 && $this->settings->crash_max_bet < floatval($r->get('bet'))) return [
            'success' => false,
            'msg' => 'Maximum bet amount - ' . $this->settings->crash_max_bet
        ];

        if(floatval($r->get('withdraw')) > 10000 && floatval($r->get('withdraw')) < 0) return [
            'success' => false,
            'msg' => 'You have entered an incorrect value for auto-withdraw!'
        ];

        if($this->user[$balType] < floatval($r->get('bet'))) return [
            'success' => false,
            'msg' => 'Not enough balance!'
        ];

        DB::beginTransaction();

        try {
            $bet = DB::table('crash_bets')
                        ->where('user_id', $this->user->id)
                        ->where('round_id', $this->game->id)
                        ->first();
                    
            if(!is_null($bet)) 
            {
                DB::rollback();
                return [
                    'success' => false,
                    'msg' => 'You have already placed a bet in this round!'
                ];
            }
			
			if($balType == 'balance') {
				DB::table('users')->where('id', $this->user->id)->update([
					'balance' => $this->user->balance-floatval($r->get('bet'))
				]);
			}
			
			if($balType == 'bonus') {
				DB::table('users')->where('id', $this->user->id)->update([
					'bonus' => $this->user->bonus-floatval($r->get('bet'))
				]);
			}

            DB::table('crash_bets')->insert([
                'user_id' => $this->user->id,
                'round_id' => $this->game->id,
                'price' => floatval($r->get('bet')),
                'withdraw' => floatval($r->get('withdraw')),
                'balType' => $balType
            ]);

            DB::commit();

            // success commit
            $this->redis->publish('crash', json_encode([
                'type' => 'bet',
                'bets' => $this->getBets(),
                'price' => CrashBets::where('round_id', $this->game->id)->sum('price')
            ]));


            $this->user = User::find($this->user->id);
			if($balType == 'balance') {
				$this->redis->publish('updateBalance', json_encode([
					'unique_id' => $this->user->unique_id,
					'balance' 	=> round($this->user->balance, 2)
				]));
			}
			if($balType == 'bonus') {
				$this->redis->publish('updateBonus', json_encode([
					'unique_id'	=> $this->user->unique_id,
					'bonus'		=> round($this->user->bonus, 2)
				]));
			}
			
            return [
                'success' => true,
                'msg' => 'Your bid is approved!',
                'bet' => floatval($r->get('bet'))
            ];
        } catch(Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'msg' => 'Something went wrong...'
            ];
        }
    }
	
	public function roundToTheNearestAnything($value, $roundTo) {
		$mod = $value%$roundTo;
		return $value+($mod<($roundTo/2)?-$mod:$roundTo-$mod);
	}
	
	public function random_float($min, $max, $includeMax) {
		return $min + \mt_rand(0, (\mt_getrandmax() - ($includeMax ? 0 : 1))) / \mt_getrandmax() * ($max - $min);
	}
	
	private function getUser() {
        $user = User::where('fake', 1)->inRandomOrder()->first();
        return $user;
    }
	
    public function addBetFake() {
		$user = $this->getUser();
		$o = [5, 10, 15];
		$ar_o = array_rand($o, 2);
		$sum = $this->roundToTheNearestAnything(mt_rand($this->settings->double_fake_min, $this->settings->double_fake_max), $o[$ar_o[0]]);
		$withdraw = 1.15; //round($this->random_float(1.15, 25, true), 2)
		$countBet = CrashBets::where(['user_id' => $user->id, 'round_id' => $this->game->id])->count();
		
        if($this->game->status > 0) return [
            'success' => false,
            'fake' => $this->settings->fake,
            'msg' => '[Crash] Bets in this round are closed!'
        ];
		if($countBet == 5) return [
            'success' => false,
            'fake' => $this->settings->fake,
            'msg' => '[Crash] This user is already involved!'
        ];
        if(floatval($sum) < $this->settings->crash_min_bet) return [
            'success' => false,
            'fake' => $this->settings->fake,
            'msg' => '[Crash] Minimum bet amount - ' . $this->settings->crash_min_bet
        ];

        if($this->settings->crash_max_bet > 0 && $this->settings->crash_max_bet < floatval($sum)) return [
            'success' => false,
            'fake' => $this->settings->fake,
            'msg' => '[Crash] Maximum bet amount - ' . $this->settings->crash_max_bet
        ];

        DB::beginTransaction();

        try {
            $bet = DB::table('crash_bets')
				->where('user_id', $user->id)
				->where('round_id', $this->game->id)
				->first();
                    
            if(!is_null($bet)) 
            {
                DB::rollback();
                return [
                    'success' => false,
					'fake' => $this->settings->fake,
                    'msg' => '[Crash] You have already placed a bet in this round!'
                ];
            }

            DB::table('crash_bets')->insert([
                'user_id' => $user->id,
                'round_id' => $this->game->id,
                'price' => floatval($sum),
                'withdraw' => floatval($withdraw),
                'fake' => 1
            ]);

            DB::commit();

            // success commit
            $this->redis->publish('crash', json_encode([
                'type' => 'bet',
                'bets' => $this->getBets(),
                'price' => CrashBets::where('round_id', $this->game->id)->sum('price')
            ]));
			
            return [
                'success' => true,
				'fake' => $this->settings->fake,
                'msg' => '[Crash] Your bid is approved!'
            ];
        } catch(Exception $e) {
            DB::rollback();
            return [
                'success' => false,
				'fake' => $this->settings->fake,
                'msg' => '[Crash] Something went wrong...'
            ];
        }
    }

    public function startSlider() {
        if($this->game->status == 1) return ['multiplier' => $this->game->multiplier, 'status' => $this->game->status];
        $this->game->status = 1;
        $this->game->save();

        $this->game->multiplier = $this->getFloat();
        $this->game->save();

        return ['multiplier' => $this->game->multiplier, 'status' => $this->game->status];
    }

    public function getFloat() {
		$profit = Profit::calc();
        $lastZero = Crash::where('multiplier', 1)->orderBy('id', 'desc')->first();
        if(!is_null($lastZero) && ($lastZero->id >= ($this->game->id+mt_rand(3, 10)))) return 1;

        $list = [];
        for($i = 0; $i < 50; $i++) $list[] = 1;
        for($i = 0; $i < 25; $i++) $list[] = 2;
        for($i = 0; $i < 10; $i++) $list[] = 3;
        for($i = 0; $i < 9; $i++) $list[] = 4;
        for($i = 0; $i < 3; $i++) $list[] = 5;
        for($i = 0; $i < 2; $i++) $list[] = 10;
        for($i = 0; $i < 1; $i++) $list[] = 100;
        shuffle($list);
		
		if($this->game->multiplier) return $this->game->multiplier; 
        $m = $list[mt_rand(0, count($list)-1)];
        if($m > 1) $m = mt_rand(1, $m);
		if($profit['need'] < $profit['now'] && mt_rand(1, 10) > 5 || $profit['now'] < 0 && mt_rand(1, 10) > 5) return '1.'.mt_rand(0,4).mt_rand(0,9);
        if($m == 1 && $profit['need'] < $profit['now'] || $profit['now'] < 0) return $list[0].'.0'.mt_rand(0,9);
		$num = round($m.'.'.mt_rand(0,9).mt_rand(1,9), 2);
        return $num;
    }

    private function isTrue($chance) {
        $list = [];
        for($i = 0; $i < $chance; $i++) $list[] = true;
        for($i = 0; $i < (100-$chance); $i++) $list[] = false;
        shuffle($list);
        return $list[mt_rand(0, count($list)-1)];
    }

    public function Cashout() {
        if($this->game->status == 0) return [
            'success' => false,
            'msg' => 'Wait for the round to start!'
        ];
        
        if($this->game->status == 2) return [
            'success' => false,
            'msg' => 'This round is already closed!'
        ];

        $bet = CrashBets::where('user_id', $this->user->id)->where('round_id', $this->game->id)->first();
        if(is_null($bet)) return [
            'success' => false,
            'msg' => 'You did not bet in this round!'
        ];

        if($bet->status == 1) return [
            'success' => false,
            'msg' => 'You have already withdrawn your bet!'
        ];

        DB::beginTransaction();
        
        try {
            $cashout = floatval($this->redis->get('cashout'));
            if($cashout == 0) {
                DB::rollback();
                return [
                    'success' => false,
                    'msg' => 'You cannot withdraw your bet! The round has not started yet, or has already ended...'
                ];
            }

            $float = floatval($this->redis->get('float'));
            if($bet->withdraw > 0 && $bet->withdraw < $float && $bet->withdraw < $this->game->multiplier) $float = $bet->withdraw;
            if($float <= 0 && $bet->withdraw < $float && $bet->withdraw < $this->game->multiplier) $float = $bet->withdraw;
            if($float <= 0) {
                DB::rollback();
                return [
                    'success' => false,
                    'msg' => 'Something went wrong! The multiplier is zero!'
                ];
            }
			
			if($bet->balType == 'balance') {
				DB::table('users')
                    ->where('id', $this->user->id)
                    ->update([
                        'balance' => $this->user->balance+round($bet->price*$float, 2)
                    ]);
				
				$this->user->requery += round(($bet->price*$float)-$bet->price, 2);
				$this->user->save();

				if($this->user->ref_id) {
					$ref = User::where('unique_id', $this->user->ref_id)->first();
					if($ref) {
						$ref_sum = round((($bet->price*$float) - $bet->price)/100*$this->settings->ref_perc, 2);
						$ref->ref_money += $ref_sum;
						$ref->ref_money_all += $ref_sum;
						$ref->save();
						
						Profit::create([
							'game' => 'ref',
							'sum' => -$ref_sum
						]);
					}
				}
			}
			if($bet->balType == 'bonus') {
				DB::table('users')
                    ->where('id', $this->user->id)
                    ->update([
                        'bonus' => $this->user->bonus+round($bet->price*$float, 2)
                    ]);
			}

            DB::table('crash_bets')
                    ->where('id', $bet->id)
                    ->update([
                        'withdraw' => $float,
                        'won' => round($bet->price*$float, 2),
                        'status' => 1
                    ]);

            DB::commit();

            $this->redis->publish('crash', json_encode([
                'type' => 'bet',
                'bets' => $this->getBets(),
                'price' => CrashBets::where('round_id', $this->game->id)->sum('price')
            ]));

            $this->user = User::find($this->user->id);
			if($bet->balType == 'balance') {
				$this->redis->publish('updateBalance', json_encode([
					'unique_id' => $this->user->unique_id,
					'balance'	=> round($this->user->balance, 2)
				]));
			}
			if($bet->balType == 'bonus') {
				$this->redis->publish('updateBonus', json_encode([
					'unique_id'	=> $this->user->unique_id,
					'bonus'		=> round($this->user->bonus, 2)
				]));
			}

            return [
                'success' => true,
                'msg' => 'Congratulations! You have multiplied your bet by x'. $float .' and get '. round($bet->price*$float, 2) .'$!'
            ];

        } catch(Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'msg' => 'Something went wrong...'
            ];
        }
    }

    public function newGame() {
        $this->game->status = 2;
        $this->game->save();

        $bets = CrashBets::where('round_id', $this->game->id)
                        ->where('withdraw', '>', 0)
                        ->where('status', 0)
                        ->get();
                
        DB::beginTransaction();
        try {
            foreach($bets as $bet) {
                $user = DB::table('users')->where('fake', 0)->where('id', $bet->user_id)->first();
                if(!is_null($user) && $bet->withdraw < $this->game->multiplier) {
                    $user = User::where('fake', 0)->where('id', $bet->user_id)->first();
					$user[$bet->balType] += round($bet->price*$bet->withdraw, 2);
					
					if($bet->balType == 'balance') {
						$user->requery += round(($bet->price*$bet->withdraw)-$bet->price, 2);
						$user->save();
					}
					
					if($bet->balType == 'balance' && $user->ref_id) {
						$ref = User::where('unique_id', $user->ref_id)->first();
						if($ref) {
							$ref_sum = round((($bet->price*$bet->withdraw) - $bet->price)/100*$this->settings->ref_perc, 2);
							$ref->ref_money += $ref_sum;
							$ref->ref_money_all += $ref_sum;
							$ref->save();

							Profit::create([
								'game' => 'ref',
								'sum' => -$ref_sum
							]);
						}
					}
                }
            }
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
        }

        $bets = CrashBets::where('fake', 0)->where('round_id', $this->game->id)->get();
        $total = 0;
        foreach($bets as $bet) if($bet->status == 1 && $bet->balType == 'balance') $total -= $bet->won-$bet->price; else $total += $bet->price;
		
		if($total != 0) Profit::create([
			'game' => 'crash',
			'sum' => $total
		]);

        $this->game = Crash::create([
            'hash' => $this->getSecret()
        ]);

        $this->redis->publish('crash', json_encode([
            'type' => 'game',
            'id' => $this->game->id,
            'hash' => $this->game->hash,
            'history' => $this->getHistory()
        ]));
		
        return [
            'success' => true,
            'id' => $this->game->id
        ];
    }

    private function getHistory() {
        $list = Crash::select('multiplier', 'hash')->where('status', 2)->orderBy('id', 'desc')->limit(10)->get();
        for($i = 0; $i < count($list); $i++) $list[$i]->color = $this->getColor($list[$i]->multiplier);
        return $list;
    }

    private function getColor($float) {
        if($float > 6.49) return '#eebef1';
        if($float > 4.49) return '#dcd0ff';
        if($float > 2.49) return '#ccccff';
        if($float > 1.49) return '#afdafc';
        return '#a6caf0';
    }

    private function getSecret() {
        $str = bin2hex(random_bytes(16));
        $game = Crash::where('hash', $str)->first();
        if($game) return $this->getSecret();
        return $str;
    }

    public function init() {
        return response()->json([
            'id' => $this->game->id,
            'status' => $this->game->status,
            'timer' => $this->settings->crash_timer
        ]);
    }

    public function getBank() {
        $crash = CrashBets::where('round_id', $this->game->id)->sum('price');
        return $crash ? $crash : 0;
    }
	
	public function gotThis(Request $r) {
		$multiplier = $r->get('multiplier');
		
		if($this->game->status >= 2) return [
			'msg'       => 'The game has started, you can not tweak!',
			'type'      => 'error'
		];
        
		if(!$this->game->id) return [
			'msg'       => 'Failed to get game number!',
			'type'      => 'error'
		];
		
		if(!$multiplier) return [
			'msg'       => 'Failed to get multiplier!',
			'type'      => 'error'
		];

		Crash::where('id', $this->game->id)->update([
			'multiplier'      => $multiplier
		]);
		
		return [
			'msg'       => 'You set x'.$multiplier.'!',
			'type'      => 'success'
		];
	}
}