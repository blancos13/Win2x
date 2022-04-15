<?php namespace App\Http\Controllers;

use App\User;
use App\Dice;
use App\Profit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DB;

class DiceController extends Controller {
	
    public function __construct() {
        parent::__construct();
		DB::connection()->getPdo()->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    }
	
	public function index() {
		$list = Dice::limit(9)->orderBy('id', 'desc')->get();
		$last = Dice::orderBy('id', 'desc')->first();
		$hash = bin2hex(random_bytes(16));
		if($this->user) $this->redis->set('dice.hash.' . $this->user->id, $hash);
		$sum = Dice::orderBy('id', 'desc')->sum('sum');
		$betsCount = ($last ? $last->id : 0);
		$betsSum = ($sum ? $sum : 0);
		$game = [];
		foreach($list as $l) {
			$user = User::where('id', $l->user_id)->first();
			
			$game[] = [
				'unique_id' => $user->unique_id,
				'avatar' => $user->avatar,
				'username' => $user->username,
				'sum' => $l->sum,
				'num' => $l->num,
				'vip' => $l->vip,
				'perc' => $l->perc,
				'win' => $l->win,
				'win_sum' => $l->win_sum,
				'balType' => $l->balType,
				'hash' => $l->hash
			];
		}
        return view('pages.dice', compact('game', 'betsCount', 'betsSum', 'hash'));
	}
	
	public function play(Request $r) {
		if($this->user->ban) return;
		$profit = Profit::calc();
		$perc = preg_replace('/[^0-9.]/', '', $r->perc);
        $sum = preg_replace('/[^0-9.]/', '', round($r->sum, 2));
        $balType = $r->balance;
		
		if($balType != 'balance' && $balType != 'bonus') return response()->json(['type' => 'error', 'msg' => 'Unable to determine your balance type!']);
		if($sum < $this->settings->dice_min_bet) return response()->json(['type' => 'error', 'msg' => 'Minimum bet amount '.$this->settings->dice_min_bet.'$!']);
		if($sum > $this->settings->dice_max_bet) return response()->json(['type' => 'error', 'msg' => 'Maximum bet amount '.$this->settings->dice_max_bet.'$!']);
		if($sum > $this->user[$balType]) return response()->json(['type' => 'error', 'msg' => 'You do not have enough coins to bet!']);
		if(!$perc) return response()->json(['type' => 'error', 'msg' => 'You have not entered a chance to win!']);
		if(!$sum) return response()->json(['type' => 'error', 'msg' => 'You have not entered a bid amount!']);
		if($perc < 1) return response()->json(['type' => 'error', 'msg' => 'You entered the wrong chance!']);
		if($perc > 95) return response()->json(['type' => 'error', 'msg' => 'You entered the wrong chance!']);
		
		DB::beginTransaction();

		try {
			$chance = round($perc, 2);
			$vip = round(96/$chance, 2);
			$rand = rand(0, 10000);
			$generate = $rand / 100;

			if($sum == round($sum*$vip, 2)) {
				DB::rollback();
				return response()->json(['type' => 'error', 'msg' => 'Your bet is equal to the win!']);
			}
			
			if($profit['now'] < $profit['need'] && mt_rand(1, 10) > 7 || $profit['now'] && mt_rand(1, 10) > 7) {
				while($perc >= $generate) {
					$chance = round($perc, 2);
					$vip = round(96/$chance,2);
					$rand = rand(0, 10000);
					$generate = $rand / 100;
				}
			}
		
			$win = 0;
			$win_sum = 0;
			$profit = 0;

			if($perc >= $generate) {
				$win = 1;
				$win_sum += round($sum*$vip, 2)-$sum;
				$profit -= round($sum*$vip, 2)-$sum;
				
				if($balType == 'balance') {
					$this->user->requery += round($win_sum-$sum, 2);
					$this->user->save();
					
					if($this->user->ref_id) {
						$ref = User::where('unique_id', $this->user->ref_id)->first();
						if($ref) {
							$ref_sum = round($win_sum/100*$this->settings->ref_perc, 2);
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
				}
			} else {
				$win = 0;
				$win_sum -= round($sum, 2);
				$profit += round($sum, 2);
			}

			$this->user[$balType] += $win_sum;
			$this->user->save();

			$hash = $this->redis->get('dice.hash.' . $this->user->id);

			Dice::create([
				'user_id' => $this->user->id,
				'sum' => $sum,
				'perc' => $chance,
				'vip' => $vip,
				'num' => $generate,
				'win' => $win,
				'win_sum' => $win_sum,
				'balType' => $balType,
				'hash' => $hash
			]);
			
			if($balType == 'balance') {
				Profit::create([
					'game' => 'dice',
					'sum' => $profit
				]);
			}
			
			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return response()->json(['type' => 'error', 'msg' => 'Unknown error!']);
		}
		
		$lastGame = Dice::orderBy('id', 'desc')->first();
		$betsSum = Dice::orderBy('id', 'desc')->sum('sum');
		$betsCount = ($lastGame ? $lastGame->id : 0);
		$betsSumToday = ($betsSum ? $betsSum : 0);
		
		$this->redis->publish('dice', json_encode([
            'unique_id' => $this->user->unique_id,
            'avatar' => $this->user->avatar,
            'username' => $this->user->username,
			'sum' => $sum,
			'num' => $generate,
			'vip' => $vip,
			'perc' => $chance,
			'win' => $win,
			'win_sum' => round($win_sum, 2),
			'betsSum' => $betsSumToday,
			'betsCount' => $betsCount,
			'balType' => $balType,
			'hash' => $hash
        ]));
		
		if($balType == 'balance') {
			$this->redis->publish('updateBalance', json_encode([
				'unique_id' => $this->user->unique_id,
				'balance'	=> round($this->user->balance, 2)
			]));
		}

		if($balType == 'bonus') {
			$this->redis->publish('updateBonus', json_encode([
				'unique_id' => $this->user->unique_id,
				'bonus'		=> round($this->user->bonus, 2)
			]));
		}
		
		$this->redis->del('dice.hash.' . $this->user->id);
		
		$newHash = bin2hex(random_bytes(16));
		$this->redis->set('dice.hash.' . $this->user->id, $newHash);
		
		return [
			'status' => 'success',
			'chislo' => $generate,
			'chance' => $chance,
			'win' => $win,
			'hash' => $newHash
		];
	}
	
	public function addBetFake() {
		$user = $this->getUser();
		
		if(!$user) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Dice] Failed to retrieve user!'
        ];
		
		$perc = round((1 + (95-1)) * mt_rand(0, 2147483647) / 2147483647, 2);
		
		$sum = $this->settings->dice_min_bet+mt_rand($this->settings->fake_min_bet * 2, $this->settings->fake_max_bet * 2) / 2;
		$bl = ['balance', 'bonus'];
		$bl_true = $bl[array_rand($bl)];
		
        $balType = $bl_true;
		
		if($balType != 'balance' && $balType != 'bonus') return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Dice] Unable to determine your balance type!'
        ];
		
		DB::beginTransaction();

		try {
			if($sum < $this->settings->dice_min_bet) {
				DB::rollback();
				return [
					'success' => false,
					'fake' => $this->settings->fakebets,
					'msg' => '[Dice] Minimum bet amount '.$this->settings->dice_min_bet.'$!'
				];
			}
			if($sum > $this->settings->dice_max_bet) {
				DB::rollback();
				return [
					'success' => false,
					'fake' => $this->settings->fakebets,
					'msg' => '[Dice] Maximum bet amount '.$this->settings->dice_max_bet.'$!'
				];
			}
			if(!$perc) {
				DB::rollback();
				return [
					'success' => false,
					'fake' => $this->settings->fakebets,
					'msg' => '[Dice] You have not entered a chance to win!'
				];
			}
			if(!$sum) {
				DB::rollback();
				return [
					'success' => false,
					'fake' => $this->settings->fakebets,
					'msg' => '[Dice] You have not entered a bid amount!'
				];
			}
			if($perc < 1) {
				DB::rollback();
				return [
					'success' => false,
					'fake' => $this->settings->fakebets,
					'msg' => '[Dice] You have not entered a chance to win!'
				];
			}
			if($perc > 95) {
				DB::rollback();
				return [
					'success' => false,
					'fake' => $this->settings->fakebets,
					'msg' => '[Dice] You have not entered a chance to win!'
				];
			}
			$chance = round($perc, 2);
			$vip = round(96/$chance, 2);
			$rand = rand(0, 10000);
			$generate = $rand / 100;

			if($sum == round($sum*$vip, 2)) {
				DB::rollback();
				return [
					'success' => false,
					'fake' => $this->settings->fakebets,
					'msg' => '[Dice] Your bet is equal to the win!'
				];
			}
		
			$win = 0;
			$win_sum = 0;

			if($perc >= $generate) {
				$win = 1;
				$win_sum += round($sum*$vip, 2)-$sum;
			} else {
				$win = 0;
				$win_sum -= round($sum, 2);
			}

			$hash = bin2hex(random_bytes(16));

			Dice::create([
				'user_id' => $user->id,
				'sum' => $sum,
				'perc' => $chance,
				'vip' => $vip,
				'num' => $generate,
				'win' => $win,
				'win_sum' => $win_sum,
				'balType' => $balType,
				'hash' => $hash,
				'fake' => 1
			]);
			
			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return [
				'success' => false,
				'fake' => $this->settings->fakebets,
				'msg' => '[Dice] Unknown error!'
			];
		}
		
		$this->redis->publish('dice', json_encode([
            'unique_id' => $user->unique_id,
            'avatar' => $user->avatar,
            'username' => $user->username,
			'sum' => $sum,
			'num' => $generate,
			'vip' => $vip,
			'perc' => $chance,
			'win' => $win,
			'win_sum' => round($win_sum, 2),
			'balType' => $balType,
			'hash' => $hash
        ]));
		
		return [
            'success' => true,
			'fake' => $this->settings->fakebets,
            'msg' => '[Dice] Bet is made!'
        ];
	}
	
	public function adminBet(Request $r) {
		$user = User::where('user_id', $r->get('user'))->first();
		
		$perc = preg_replace('/[^0-9.]/', '', $r->perc);
        $sum = preg_replace('/[^0-9.]/', '', round($r->sum, 2));
        $balType = $r->balance;
		
		if($balType != 'balance' && $balType != 'bonus') return response()->json(['type' => 'error', 'msg' => 'Unable to determine your balance type!']);
		
		DB::beginTransaction();

		try {
			if($sum < $this->settings->dice_min_bet) {
				DB::rollback();
				return response()->json(['type' => 'error', 'msg' => 'Minimum bet amount '.$this->settings->dice_min_bet.'$!']); 
			}
			if($sum > $this->settings->dice_max_bet) {
				DB::rollback();
				return response()->json(['type' => 'error', 'msg' => 'Maximum bet amount '.$this->settings->dice_max_bet.'$!']);
			}
			if(!$perc) {
				DB::rollback();
				return response()->json(['type' => 'error', 'msg' => 'You have not entered a chance to win!']);
			}
			if(!$sum) {
				DB::rollback();
				return response()->json(['type' => 'error', 'msg' => 'You have not entered a bid amount!']);
			}
			if($perc < 1) {
				DB::rollback();
				return response()->json(['type' => 'error', 'msg' => 'You entered the wrong chance!']);
			}
			if($perc > 95) {
				DB::rollback();
				return response()->json(['type' => 'error', 'msg' => 'You entered the wrong chance!']);
			}
			$chance = round($perc, 2);
			$vip = round(96/$chance, 2);
			$rand = rand(0, 10000);
			$generate = $rand / 100;

			if($sum == round($sum*$vip, 2)) {
				DB::rollback();
				return response()->json(['type' => 'error', 'msg' => 'Your bet is equal to the win!']);
			}
		
			$win = 0;
			$win_sum = 0;

			if($perc >= $generate) {
				$win = 1;
				$win_sum += round($sum*$vip, 2)-$sum;
			} else {
				$win = 0;
				$win_sum -= round($sum, 2);
			}

			$hash = bin2hex(random_bytes(16));

			Dice::create([
				'user_id' => $user->id,
				'sum' => $sum,
				'perc' => $chance,
				'vip' => $vip,
				'num' => $generate,
				'win' => $win,
				'win_sum' => $win_sum,
				'balType' => $balType,
				'hash' => $hash,
				'fake' => 1
			]);
			
			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return response()->json(['type' => 'error', 'msg' => 'Unknown error!']);
		}
		
		$this->redis->publish('dice', json_encode([
            'unique_id' => $user->unique_id,
            'avatar' => $user->avatar,
            'username' => $user->username,
			'sum' => $sum,
			'num' => $generate,
			'vip' => $vip,
			'perc' => $chance,
			'win' => $win,
			'win_sum' => round($win_sum, 2),
			'balType' => $balType,
			'hash' => $hash
        ]));
		
		return [
            'success' => true,
			'type' => 'success',
            'msg' => '[Dice] Bet is made!'
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