<?php namespace App\Http\Controllers;

use App\User;
use App\Profit;
use App\Wheel;
use App\WheelBets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DB;

class WheelController extends Controller {
	
    public function __construct() {
        parent::__construct();
		$this->game = Wheel::orderBy('id', 'desc')->first();
		if(is_null($this->game)) $this->game = Wheel::create([
			'hash' => bin2hex(random_bytes(16))
		]);
        view()->share('game', $this->game);
		view()->share('time', $this->getTime());
		DB::connection()->getPdo()->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    }
	
	public function index() {
        $bets = $this->getBets();
        $history = $this->getHistory();
		$rotate = $this->settings->wheel_rotate2;
        $coldwn = $this->settings->wheel_rotate_start-time()+$this->settings->wheel_timer;
        if($this->game->status == 2 && $coldwn > 0) $rotate += ($this->settings->wheel_rotate-$this->settings->wheel_rotate2)*(1-($coldwn/7));
        $rotate2 = $this->settings->wheel_rotate;
        return view('pages.wheel', compact('bets', 'history', 'rotate', 'rotate2', 'coldwn'));
    }
	
	public function newGame() {
		$this->settings->wheel_rotate = $this->settings->wheel_rotate-(floor($this->settings->wheel_rotate/360)*360);
        $this->settings->wheel_rotate2 = $this->settings->wheel_rotate;
        $this->settings->save();
		
		DB::beginTransaction();
		try {
		
			$game = Wheel::create([
				'hash' => bin2hex(random_bytes(16))
			]);
			
			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return [
				'success' => false,
				'fake' => $this->settings->fakebets,
				'msg' => '[Wheel] Unknown error!'
			];
		}
		
        $this->emit([
            'type' => 'newGame',
            'id' => $game->id,
            'hash' => $game->hash,
            'slider' => [
                'rotate' => $this->settings->wheel_rotate,
                'time' => $this->getTime()
            ],
            'history' => [
                'color' => $this->game->winner_color,
                'hash' => $this->game->hash
            ]
        ]);
		
		return response()->json([
			'id' => $game->id
        ]);
    }
	
	public function newBet(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		if($this->user->ban) return;
		$price = preg_replace('/[^0-9.]/', '', $r->sum);
		$color = $r->color;
		$balance = $r->balance;
		if(is_null($color)) return response()->json(['type' => 'error', 'msg' => 'You have not chosen a multiplier!']);
		if($color != 'black' && $color != 'red' && $color != 'green' && $color != 'yellow') return response()->json(['type' => 'error', 'msg' => 'Unable to determine the multiplier selected!']);
		if($price < $this->settings->wheel_min_bet) return response()->json(['type' => 'error', 'msg' => 'Minimum bet amount '.$this->settings->wheel_min_bet.'!']);
		if($price > $this->settings->wheel_max_bet) return response()->json(['type' => 'error', 'msg' => 'Maximum bet amount '.$this->settings->wheel_max_bet.'!']);
		if($balance != 'balance' && $balance != 'bonus') return response()->json(['type' => 'error', 'msg' => '!']);
		if($this->game->status > 1) return response()->json(['type' => 'error', 'msg' => 'Bets in this game are closed!']);
		if($balance == 'balance' && $this->user->balance < $price) return response()->json(['type' => 'error', 'msg' => 'Not enough balance!']);
		if($balance == 'bonus' && $this->user->bonus < $price) return response()->json(['type' => 'error', 'msg' => 'Not enough balance!']);
		
		$bets = WheelBets::where([
            'user_id' => $this->user->id,
            'game_id' => $this->game->id
        ])->select('color', 'balance')->groupBy('color', 'balance')->get();
		
		$countbets = WheelBets::where('game_id', $this->game->id)->where('user_id', $this->user->id)->count();
		if($countbets >= 3) return response()->json(['msg' => 'Only 3 bets allowed!', 'type' => 'error']);
		
		foreach($bets as $b) {
			if($balance != $b->balance) return response()->json(['type' => 'error', 'msg' => 'You have already placed a bet with '. (($balance == 'balance') ? 'bonus' : 'money') .' score!']);
			if($color != $b->color) return response()->json(['type' => 'error', 'msg' => 'You cannot bet on this multiplier!']);
		}
		
		DB::beginTransaction();
		try {
			$bet = new WheelBets();
			$bet->user_id = $this->user->id;
			$bet->game_id = $this->game->id;
			$bet->price = $price;
			$bet->color = $color;
			$bet->balance = $balance;
			$bet->save();
			
			$this->game->price += $price;
			$this->game->save();
			
			if($balance == 'balance') {
				$this->user->balance -= $price;
				$this->user->save();
				
				$this->redis->publish('updateBalance', json_encode([
					'unique_id' => $this->user->unique_id,
					'balance' 	=> round($this->user->balance, 2)
				]));
			}

			if($balance == 'bonus') {
				$this->user->bonus -= $price;
				$this->user->save();
				
				$this->redis->publish('updateBonus', json_encode([
					'unique_id' => $this->user->unique_id,
					'bonus' 	=> round($this->user->bonus, 2)
				]));
			}
			
			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return response()->json(['type' => 'error', 'msg' => 'Unknown error!']);
		}
        
        $this->emit([
            'type' => 'bets',
            'bets' => $this->getBets()
        ]);

        if($this->game->status == 0) $this->startTimer();
		
		return response()->json(['type' => 'success', 'msg' => 'Your bet is accepted!']);
	}
	
	public function adminBet(Request $r) {
		$user = User::where('user_id', $r->user)->first();
		if(is_null($user)) return response()->json(['type' => 'error', 'msg' => 'User could not be found!']);
		$price = preg_replace('/[^0-9.]/', '', $r->sum);
		$color = $r->color;
		$balance = $r->balance;
		if(is_null($color)) return response()->json(['type' => 'error', 'msg' => 'You have not chosen a multiplier!']);
		if($color != 'black' && $color != 'red' && $color != 'green' && $color != 'yellow') return response()->json(['type' => 'error', 'msg' => 'Unable to determine the multiplier selected!']);
		if($price < $this->settings->wheel_min_bet) return response()->json(['type' => 'error', 'msg' => 'Minimum bet amount '.$this->settings->wheel_min_bet.'!']);
		if($price > $this->settings->wheel_max_bet) return response()->json(['type' => 'error', 'msg' => 'Maximum bet amount '.$this->settings->wheel_max_bet.'!']);
		if($balance != 'balance' && $balance != 'bonus') return response()->json(['type' => 'error', 'msg' => 'Unable to determine your balance type!']);
		if($this->game->status > 1) return response()->json(['type' => 'error', 'msg' => 'Bets in this game are closed!']);
		
		$bets = WheelBets::where([
            'user_id' => $user->id,
            'game_id' => $this->game->id
        ])->select('color', 'balance')->groupBy('color', 'balance')->get();
		
		$countbets = WheelBets::where('game_id', $this->game->id)->where('user_id', $user->id)->count();
		if($countbets >= 3) return response()->json(['msg' => 'Only 3 bets allowed!', 'type' => 'error']);
		
		foreach($bets as $b) {
			if($balance != $b->balance) return response()->json(['type' => 'error', 'msg' => 'You have already placed a bet with '. (($balance == 'balance') ? 'bonus' : 'money') .' score!']);
			if($color != $b->color) return response()->json(['type' => 'error', 'msg' => 'You cannot bet on this multiplier!']);
		}
		
		DB::beginTransaction();
		try {
			$bet = new WheelBets();
			$bet->user_id = $user->id;
			$bet->game_id = $this->game->id;
			$bet->price = $price;
			$bet->color = $color;
			$bet->balance = $balance;
			$bet->fake = 1;
			$bet->save();
			
			$this->game->price += $price;
			$this->game->save();
			
			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return response()->json(['type' => 'error', 'msg' => 'Unknown error!']);
		}
        
        $this->emit([
            'type' => 'bets',
            'bets' => $this->getBets()
        ]);

        if($this->game->status == 0) $this->startTimer();
		
		return response()->json(['type' => 'success', 'msg' => 'Your bet is accepted!']);
	}
	
	public function addBetFake() {
		if($this->game->status > 1) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Wheel] Bets in this game are closed!'
        ];
		$user = $this->getUser();
		if(!$user) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Wheel] Failed to retrieve user!'
        ];
		$clrs = ['black', 'red', 'green', 'yellow'];
		$clrs_true = $clrs[array_rand($clrs)];
		$bl = ['balance', 'bonus'];
		$bl_true = $bl[array_rand($bl)];
		$price = $this->settings->wheel_min_bet+mt_rand($this->settings->fake_min_bet * 2, $this->settings->fake_max_bet * 2) / 2;
		$color = $clrs_true;
		$balance = $bl_true;
		if(is_null($color)) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Wheel] You have not chosen a multiplier!'
        ];
		if($color != 'black' && $color != 'red' && $color != 'green' && $color != 'yellow') return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Wheel] Unable to determine the multiplier selected!'
        ];
		if($price < $this->settings->wheel_min_bet) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Wheel] Minimum bet amount '.$this->settings->wheel_min_bet.'!'
        ];
		if($price > $this->settings->wheel_max_bet) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Wheel] Maximum bet amount '.$this->settings->wheel_max_bet.'!'
        ];
		if($balance != 'balance' && $balance != 'bonus') return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Wheel] Unable to determine your balance type!'
        ];
		if($this->game->status > 1) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Wheel] Bets in this game are closed!'
        ];
		
		$bets = WheelBets::where([
            'user_id' => $user->id,
            'game_id' => $this->game->id
        ])->select('color', 'balance')->groupBy('color', 'balance')->get();
		
		$countbets = WheelBets::where('game_id', $this->game->id)->where('user_id', $user->id)->count();
		if($countbets >= 3) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Wheel] Only 3 bets allowed!'
        ];
		
		foreach($bets as $b) {
			if($balance != $b->balance) return [
				'success' => false,
				'fake' => $this->settings->fakebets,
				'msg' => '[Wheel] You have already placed a bet with '. (($balance == 'balance') ? 'bonus' : 'money') .' score!'
			];
			if($color != $b->color) return [
				'success' => false,
				'fake' => $this->settings->fakebets,
				'msg' => '[Wheel] You cannot bet on this multiplier!'
			];
		}
		
		DB::beginTransaction();
		try {
			$bet = new WheelBets();
			$bet->user_id = $user->id;
			$bet->game_id = $this->game->id;
			$bet->price = $price;
			$bet->color = $color;
			$bet->balance = $balance;
			$bet->fake = 1;
			$bet->save();
			
			$this->game->price += $price;
			$this->game->save();
			
			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return [
				'success' => false,
				'fake' => $this->settings->fakebets,
				'msg' => '[Wheel] Unknown error!'
			];
		}
        
        $this->emit([
            'type' => 'bets',
            'bets' => $this->getBets()
        ]);

        if($this->game->status == 0) $this->startTimer();
		
		return [
			'success' => true,
			'fake' => $this->settings->fakebets,
			'msg' => '[Wheel] Your bet is accepted!'
		];
	}

    private function startTimer() {
        if($this->game->status > 0) return;

        return $this->emit([
            'type' => 'wheel_timer',
            'timer' => $this->getTime()
        ]);
    }
	
	public function getTime() {
        $min = floor($this->settings->wheel_timer/60);
        $sec = floor($this->settings->wheel_timer-($min*60));
		
        if($min == 0) $min = '00';
        if($sec == 0) $sec = '00';
        if(($min > 0) && ($min < 10)) $min = '0'.$min;
        if(($sec > 0) && ($sec < 10)) $sec = '0'.$sec;
        return [$min, $sec, $this->settings->wheel_timer];
    }

    public function getPosition($color) {
        $list = [
            [0,			'yellow',	50],
            [6.5,		'green',	5],
            [60.3,		'green',	5],
            [73.7,		'green',	5],
            [126.9,		'green',	5],
            [140.2,		'green',	5],
            [219.5,		'green',	5],
            [232.7,		'green',	5],
            [285.9,		'green',	5],
            [299.3,		'green',	5],
            [352.9,		'green',	5],
            [19.7,		'red',		3],
            [32.9,		'red',		3],
            [46.3,		'red',		3],
            [86.5,		'red',		3],
            [99.7,		'red',		3],
            [113.2,		'red',		3],
            [153.1,		'red',		3],
            [166.4,		'red',		3],
            [179.8,		'red',		3],
            [193.1,		'red',		3],
            [206.3,		'red',		3],
            [246.4,		'red',		3],
            [259.7,		'red',		3],
            [273.1,		'red',		3],
            [313,		'red',		3],
            [326.4,		'red',		3],
            [339.7,		'red',		3],
            [13.1,		'black',	2],
            [26.4,		'black',	2],
            [39.6,		'black',	2],
            [53.1,		'black',	2],
            [66.3,		'black',	2],
            [79.7,		'black',	2],
            [93.1,		'black',	2],
            [119.7,		'black',	2],
            [133.1,		'black',	2],
            [146.3,		'black',	2],
            [159.7,		'black',	2],
            [172.9,		'black',	2],
            [186.3,		'black',	2],
            [199.7,		'black',	2],
            [212.9,		'black',	2],
            [226.3,		'black',	2],
            [239.7,		'black',	2],
            [252.9,		'black',	2],
            [266.3,		'black',	2],
            [279.7,		'black',	2],
            [292.9,		'black',	2],
            [306.3,		'black',	2],
            [319.7,		'black',	2],
            [332.9,		'black',	2],
            [346.3,		'black',	2]
        ];
		
		if($this->game->winner_color !== null) $color = $this->game->winner_color;
		
		$filter = array_filter($list, function($var) use($color) {
			return ($var[1] == $color);
		});
		shuffle($filter);
		
		$с = $filter[mt_rand(0, count($filter)-1)];
		
        return $с;
    }
	
	public function getSlider() {
		$profit = Profit::calc();
		$profit_wheel = Profit::where('game', 'wheel')->where('created_at', '>=', Carbon::today())->sum('sum');
		$ranked = 0;
		$color = [];
		for($i = 0; $i < 47.9; $i++) $color[] = 'black';
		for($i = 0; $i < 40; $i++) $color[] = 'red';
		for($i = 0; $i < 12; $i++) $color[] = 'green';
		for($i = 0; $i < 0.1; $i++) $color[] = 'yellow';
		shuffle($color);

		$wcolor = $color[mt_rand(0, count($color)-1)];
		
		$checkUser = WheelBets::where(['fake' => 0, 'game_id' => $this->game->id])->orderBy('id', 'desc')->count();
		if($checkUser >= 1 && $this->game->ranked != 1) {
			if($profit['now'] < $profit['need'] || $profit_wheel < 0) {
				$lastYellow = Wheel::where('status', 3)->where('winner_color', 'yellow')->first();
				$prices = $this->getPrices();
				$pricesList = [];
				$colors = ['black', 'red', 'green'];
				foreach($colors as $color) $pricesList[] = [
					'color' => $color,
					'value' => ((isset($prices[$color])) ? $prices[$color] : 0)*(($color == 'black') ? 2 : (($color == 'red') ? 3 : 5))
				];
				
				shuffle($pricesList);
				
				usort($pricesList, function($a, $b) {
					return($a['value']-$b['value']);
				});
				
				if(!is_null($lastYellow) && ($lastYellow->id >= ($this->game->id+mt_rand(130, 200)))) {
					$wcolor = 'yellow';
				} elseif(is_null($lastYellow) && $this->game->id >= mt_rand(130, 200)) {
					$wcolor = 'yellow';
				} else {
					$min = reset($pricesList);
					if($min['color'] == 'black') $wcolor = 'black';
					elseif($min['color'] == 'red') $wcolor = 'red';
					elseif($min['color'] == 'green') $wcolor = 'green';
				}
				$ranked = 1;
			}
		}
		
		$box = $this->getPosition($wcolor);
		$rotate = ((floor($this->settings->roulette_rotate/360)*360)+360)+(360*2)+$box[0];

        $this->game->winner_color = $box[1];
        $this->game->ranked = $ranked;
        $this->game->profit = $this->sendMoney($this->game->id, $box[1]);
        $this->game->save();

        $this->settings->wheel_rotate = $rotate;
        $this->settings->wheel_rotate_start = time();
        $this->settings->save();

        $this->emit([
            'type' => 'slider',
            'slider' => [
                'rotate' => $this->settings->wheel_rotate,
                'color' => $this->game->winner_color,
            ]
        ]);
		
		return response()->json(['color' => $this->game->winner_color, 'time' => 10000]);
	}
	
	private function sendMoney($game_id, $color) {
		$bets = WheelBets::select(DB::raw('SUM(price) as price'), 'user_id', 'balance')->where('game_id', $game_id)->where('color', $color)->groupBy('user_id', 'balance')->get();
		$multiplier = ($color == 'black') ? 2 : (($color == 'red') ? 3 : (($color == 'green') ? 5 : 50));
		$profit = WheelBets::where(['game_id' => $game_id, 'balance' => 'balance', 'fake' => 0])->sum('price');
        foreach($bets as $b) {
            $user = User::where(['id' => $b->user_id, 'fake' => 0])->first();
            if(!is_null($user)) {
				if($b->balance == 'balance') {
					$user->balance += $b->price*$multiplier;
					$user->requery += round(($b->price*$multiplier)-$b->price, 2);
					$user->save();
				
					$profit -= $b->price*$multiplier;

					if($user->ref_id) {
						$ref = User::where('unique_id', $user->ref_id)->first();
						if($ref) {
							$ref_sum = round((($b->price*$multiplier) - $b->price)/100*$this->settings->ref_perc, 2);
							if($ref_sum > 0) {
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
					
					$this->redis->publish('updateBalanceAfter', json_encode([
						'unique_id' => $user->unique_id,
						'balance'	=> round($user->balance, 2),
						'timer'		=> 10
					]));
				}
				if($b->balance == 'bonus') {
					$user->bonus += $b->price*$multiplier;
					$user->save();
					
					$this->redis->publish('updateBonusAfter', json_encode([
						'unique_id' => $user->unique_id,
						'bonus'		=> round($user->bonus, 2),
						'timer'		=> 10
					]));
				}
            } else {
				$profit = 0;
			}
        }
		
		Profit::create([
			'game' => 'wheel',
			'sum' => $profit
		]);
		
		$betUsers = WheelBets::where('game_id', $game_id)->where('color', $color)->get();
		foreach($betUsers as $b) {
			$b->win = 1;
			$b->win_sum = ($b->price*$multiplier)-$b->price;
			$b->save();
		}
		
		return $profit;
	}

    private function getPrices() {
        $query = WheelBets::where('game_id', $this->game->id)
                    ->select(DB::raw('SUM(price) as value'), 'color')
                    ->groupBy('color')
                    ->get();

        $list = [];
        foreach($query as $l) $list[$l->color] = $l->value;
        return $list;
    }

    private function getBets() {
        $bets = WheelBets::where('wheel_bets.game_id', $this->game->id)
				->select('wheel_bets.user_id', DB::raw('SUM(wheel_bets.price) as sum'), 'users.unique_id', 'users.username', 'users.avatar', 'wheel_bets.color', 'wheel_bets.balance')
				->join('users', 'users.id', '=', 'wheel_bets.user_id')
				->groupBy('wheel_bets.user_id', 'wheel_bets.color', 'wheel_bets.balance')
				->orderBy('sum', 'desc')
				->get();
        return $bets;
    }

    public function getHistory() {
        $query = Wheel::where('status', 3)->select('winner_color', 'id', 'hash')->orderBy('id','desc')->limit(15)->get();
        return $query;
    }

    public function updateStatus(Request $r) {
        $this->game->status = $r->get('status');
        $this->game->save();
        
		return response()->json(['success' => true]);
    }

    public function getGame() {
		return response()->json(['id' => $this->game->id, 'status' => $this->game->status, 'timer' => $this->getTime()]);
    }

    private function emit($array) {
        return $this->redis->publish('wheel', json_encode($array));
    }
	
	public function gotThis(Request $r) {
		$color = $r->get('color');
		$number = '';
		
		if($this->game->status > 1) return [
			'msg'       => 'The game has started, you can not pick!',
			'type'      => 'error'
		];
        
		if(!$this->game->id) return [
			'msg'       => 'Failed to get game number!',
			'type'      => 'error'
		];
		
		if(!$color) return [
			'msg'       => 'Failed to get color!',
			'type'      => 'error'
		];
		
		$list = [
            [0,			'yellow',	50],
            [6.5,		'green',	5],
            [60.3,		'green',	5],
            [73.7,		'green',	5],
            [126.9,		'green',	5],
            [140.2,		'green',	5],
            [219.5,		'green',	5],
            [232.7,		'green',	5],
            [285.9,		'green',	5],
            [299.3,		'green',	5],
            [352.9,		'green',	5],
            [19.7,		'red',		3],
            [32.9,		'red',		3],
            [46.3,		'red',		3],
            [86.5,		'red',		3],
            [99.7,		'red',		3],
            [113.2,		'red',		3],
            [153.1,		'red',		3],
            [166.4,		'red',		3],
            [179.8,		'red',		3],
            [193.1,		'red',		3],
            [206.3,		'red',		3],
            [246.4,		'red',		3],
            [259.7,		'red',		3],
            [273.1,		'red',		3],
            [313,		'red',		3],
            [326.4,		'red',		3],
            [339.7,		'red',		3],
            [13.1,		'black',	2],
            [26.4,		'black',	2],
            [39.6,		'black',	2],
            [53.1,		'black',	2],
            [66.3,		'black',	2],
            [79.7,		'black',	2],
            [93.1,		'black',	2],
            [119.7,		'black',	2],
            [133.1,		'black',	2],
            [146.3,		'black',	2],
            [159.7,		'black',	2],
            [172.9,		'black',	2],
            [186.3,		'black',	2],
            [199.7,		'black',	2],
            [212.9,		'black',	2],
            [226.3,		'black',	2],
            [239.7,		'black',	2],
            [252.9,		'black',	2],
            [266.3,		'black',	2],
            [279.7,		'black',	2],
            [292.9,		'black',	2],
            [306.3,		'black',	2],
            [319.7,		'black',	2],
            [332.9,		'black',	2],
            [346.3,		'black',	2]
        ];
		
		if($this->game->winner_color !== null) $color = $this->game->winner_color;
		
		$filter = array_filter($list, function($var) use($color) {
			return ($var[1] == $color);
		});
		shuffle($filter);
		
		$с = $filter[mt_rand(0, count($filter)-1)];

		Wheel::where('id', $this->game->id)->update([
			'winner_color' => $с[1],
			'ranked' => 1
		]);
		
		return [
			'msg'       => 'You set '.$color.' color!',
			'type'      => 'success'
		];
	}
	
	private function getUser() {
        $user = User::where('fake', 1)->inRandomOrder()->first();
		if($user->time != 0) {
			$now = Carbon::now()->format('H');
			if($now < 06) $time = 4;
			if($now >= 06 && $now < 12) $time = 1;
			if($now >= 12 && $now < 18) $time = 2;
			if($now >= 18) $time = 3;
        	$user = User::where(['fake' => 1, 'time' => $time])->inRandomOrder()->first();
		}
        return $user;
    }
	
}