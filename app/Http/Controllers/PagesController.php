<?php namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Jackpot;
use App\JackpotBets;
use App\Wheel;
use App\WheelBets;
use App\Crash;
use App\CrashBets;
use App\CoinFlip;
use App\Bomb;
use App\Battle;
use App\BattleBets;
use App\Dice;
use App\Bonus;
use App\BonusLog;
use App\Deposit;
use App\Exchanges;
use App\Withdraw;
use App\Profit;
use App\Promocode;
use App\PromoLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DB;
use App\Lib\coinPayments;
use App\Lib\CoinPaymentHosted;

class PagesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
		DB::connection()->getPdo()->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    }
	
	public function faq() {
		return view('pages.faq');
	}
	
	public function profileHistory() {
		$pays = Deposit::where(['user_id' => $this->user->id])->orderBy('id', 'desc')->limit(20)->get();
        $withdraws = Withdraw::where('user_id', $this->user->id)->orderBy('id', 'desc')->limit(20)->get();
		return view('pages.profileHistory', compact('pays', 'withdraws'));
	}
	
	public function free() {
		$rotate = 0;
		$bonuses = Bonus::get();
		foreach($bonuses as $key => $b) {
			$bonuses[$key]['rotate'] = $rotate;
			$rotate += 360 / $bonuses->count();
		}
		$max = Bonus::where('type', 'group')->max('sum');
		$max_refs = Bonus::where('type', 'refs')->max('sum');
		
		$bonusLog = BonusLog::where(['user_id' => $this->user->id, 'type' => 'group'])->orderBy('id', 'desc')->first();
		$check = 0;
		if($bonusLog) {
			if($bonusLog->remaining) {
				$nowtime = time();
				$time = $bonusLog->remaining;
				$lasttime = $nowtime - $time;
				if($time >= $nowtime) {
					$check = 1;
				}
			}
			$bonusLog->status = 2;
			$bonusLog->save();
		}
		
		$activeRefs = 0;
		$refs = User::where(['ban' => 0, 'ref_id' => $this->user->unique_id])->get();
		foreach($refs as $a) {
			$pay = Deposit::where(['user_id' => $a->id, 'status' => 1])->sum('amount');
			if($pay >= 5) $activeRefs += 1;
		}
		
		$refLog = BonusLog::where(['user_id' => $this->user->id, 'type' => 'refs', 'status' => 3])->count();
		
		return view('pages.free', compact('bonuses', 'max', 'max_refs', 'check', 'activeRefs', 'refLog'));
	}
	
	public function freeGetWheel(Request $r) {
		$type = $r->get('type');
		$bonuses = Bonus::select('bg', 'sum', 'color')->where('type', $type)->get();
		$list = [];
		foreach($bonuses as $b) {
			$list[] = [
				'sum' => $b->sum,
				'bgColor' => $b->bg,
				'iconColor' => $b->color
			];
		}
		$bonusLog = BonusLog::where('user_id', $this->user->id)->where('type', $type)->orderBy('id', 'desc')->first();
		$remaining = isset($bonusLog) ? $bonusLog->remaining : 0;
		$data = [
			'data' => $list,
			'remaining' => $remaining,
			'type' => $type
		];
		return $data;
	}
	
	public function freeSpin(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		$validator = \Validator::make($r->all(), [
            'recapcha' => 'required|captcha',
        ]);
		if($validator->fails()) return response()->json(['success' => false, 'msg' => 'You didn`t pass the test. I`m not a robot!', 'type' => 'error']);
		$type = $r->get('type');
		if($type == 'group') {
			$bonuses = Bonus::select('bg', 'sum', 'color', 'status')->where('type', $type)->get();

			$bonusLog = BonusLog::where('user_id', $this->user->id)->where('type', $type)->orderBy('id', 'desc')->first();
			if($bonusLog) {
				if($bonusLog->remaining) {
					$nowtime = time();
					$time = $bonusLog->remaining;
					$lasttime = $nowtime - $time;
					if($time >= $nowtime) {
						return [
							'success' => false,
							'msg' => 'The next bonus You can get: '.date("d.m.Y H:i:s", $time),
							'type' => 'error'
						];
					}
				}
				$bonusLog->status = 2;
				$bonusLog->save();
			}

			$start = (360/$bonuses->count())/2;
			foreach($bonuses as $key => $b) {
				$bonuses[$key]['start'] = $start;
				$start += 360/$bonuses->count();
			}

			$list = [];
			foreach($bonuses as $b) {
				if($b->status == 1) $list[] = [
					'sum' => $b->sum,
					'start' => $b->start
				];
			}
			$win = $list[array_rand($list)];

			$remaining = Carbon::now()->addMinutes($this->settings->bonus_group_time)->getTimestamp();

			BonusLog::create([
				'user_id' => $this->user->id,
				'sum' => $win['sum'],
				'remaining' => $remaining,
				'status' => 1,
				'type' => $type
			]);

			$this->user->bonus += $win['sum'];
			$this->user->save();

			$this->redis->publish('updateBonusAfter', json_encode([
				'unique_id'	=> $this->user->unique_id,
				'bonus' 	=> round($this->user->bonus, 2),
				'timer' 	=> 5
			]));
		}
		
		if($type == 'refs') {
			$bonuses = Bonus::select('bg', 'sum', 'color', 'status')->where('type', $type)->get();
		
			$activeRefs = 0;
			$refs = User::where(['ban' => 0, 'ref_id' => $this->user->unique_id])->get();
			foreach($refs as $a) {
				$pay = Deposit::where(['user_id' => $a->id, 'status' => 1])->sum('amount');
				if($pay >= 5) $activeRefs += 1;
			}
			
			if($activeRefs < $this->settings->max_active_ref) return response()->json(['success' => false, 'msg' => 'Not enough active referrals. '.$activeRefs.'/'.$this->settings->max_active_ref.'!', 'type' => 'error']);

			$bonusLog = BonusLog::where('user_id', $this->user->id)->where('type', $type)->orderBy('id', 'desc')->first();
			if($bonusLog) {
				if($bonusLog->status == 3) return response()->json(['success' => false, 'msg' => 'You have already received this bonus!', 'type' => 'error']);
			}

			$start = (360/$bonuses->count())/2;
			foreach($bonuses as $key => $b) {
				$bonuses[$key]['start'] = $start;
				$start += 360/$bonuses->count();
			}

			$list = [];
			foreach($bonuses as $b) {
				if($b->status == 1) $list[] = [
					'sum' => $b->sum,
					'start' => $b->start
				];
			}
			$win = $list[array_rand($list)];

			$remaining = 0;
			
			BonusLog::create([
				'user_id' => $this->user->id,
				'sum' => $win['sum'],
				'remaining' => $remaining,
				'status' => 3,
				'type' => $type
			]);

			$this->user->bonus += $win['sum'];
			$this->user->save();

			$this->redis->publish('updateBonusAfter', json_encode([
				'unique_id'	=> $this->user->unique_id,
				'bonus' 	=> round($this->user->bonus, 2),
				'timer' 	=> 5
			]));
		}
		
		$this->redis->publish('bonus', json_encode([
			'unique_id' => $this->user->unique_id,
            'rotate' => 1440+$win['start']
        ]));
		
		return response()->json(['success' => true, 'msg' => 'Spin!', 'type' => 'success', 'remaining' => $remaining, 'bonusType' => $type]);
	}
	
	public function promoActivate(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['success' => false, 'msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		
		$code = strtolower(htmlspecialchars($r->get('code')));
        if(!$code) return response()->json(['success' => false, 'msg' => 'You don`t entered a code!', 'type' => 'error']);
		
        $promocode = Promocode::where('code', $code)->first();
        if(!$promocode) return response()->json(['success' => false, 'msg' => 'This code does not exist!', 'type' => 'error']);
		
		$money = $promocode->amount;
		$check = PromoLog::where('user_id', $this->user->id)->where('code', $code)->first();

		if($check) return response()->json(['success' => false, 'msg' => 'You already activated this code!', 'type' => 'error']);
		if($promocode->limit == 1 && $promocode->count_use <= 0) return response()->json(['success' => false, 'msg' => 'This code is no longer valid!', 'type' => 'error']);
		if($promocode->user_id == $this->user->id) return response()->json(['success' => false, 'msg' => 'You can`t activate your promo code!', 'type' => 'error']);

		if($promocode->type == 'balance') {
			$this->user->balance += $money;
			$this->user->save();
			
			Profit::create([
				'game' => 'ref',
				'sum' => -$money
			]);
			
			$this->redis->publish('updateBalance', json_encode([
				'unique_id' => $this->user->unique_id,
				'balance'	=> $this->user->balance
			]));
		}

		if($promocode->type == 'bonus') {
			$this->user->bonus += $money;
			$this->user->save();
			
			$this->redis->publish('updateBonus', json_encode([
				'unique_id' => $this->user->unique_id,
				'bonus'	=> $this->user->bonus
			]));
		}

		if($promocode->limit == 1 && $promocode->count_use > 0){
			$promocode->count_use -= 1;
			$promocode->save();
		}

		PromoLog::insert([
			'user_id' => $this->user->id,
			'sum' => $money,
			'code' => $code,
			'type' => $promocode->type
		]);
		
		return response()->json(['success' => true, 'msg' => 'Code activated!', 'type' => 'success']);
	}
	
	public function affiliate() {
		return view('pages.affiliate');
	}
	
	public function affiliateGet() {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		if($this->user->ref_money < $this->settings->min_ref_withdraw) return response()->json(['success' => false, 'msg' => 'Minimum getting amount '. $this->settings->min_ref_withdraw .'$!', 'type' => 'error']);
		
		DB::beginTransaction();

        try {
			$this->user->bonus += $this->user->ref_money;
			$this->user->ref_money = 0;
			$this->user->save();
			
			DB::commit();
		} catch(Exception $e) {
            DB::rollback();
			return ['msg' => 'Something went wrong...', 'type' => 'error'];
        }
		
		$this->redis->publish('updateBonus', json_encode([
			'unique_id' => $this->user->unique_id,
			'bonus' 	=> round($this->user->bonus, 2)
		]));
		
		return response()->json(['success' => true, 'msg' => 'Coins transferred to Your bonus score!', 'type' => 'success']);
	}
	
	public function exchange(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		$sum = floatval($r->get('sum'));
		
		if($sum < $this->settings->exchange_min) return ['msg' => 'Minimum exchange amount '.$this->settings->exchange_min.'$!', 'type' => 'error'];
		if($this->user->bonus < $sum) return ['msg' => 'There are not enough funds on the bonus score!', 'type' => 'error'];
		
		DB::beginTransaction();
		try {

			$exchange = new Exchanges();
			$exchange->user_id = $this->user->id;
			$exchange->sum = $sum;
			$exchange->save();
			
			$curs = round($sum/$this->settings->exchange_curs, 2);
			$this->user->bonus -= $sum;
			$this->user->balance += $curs;
			$this->user->save();
			
			Profit::create([
				'game' => 'exchange',
				'sum' => $sum-$curs
			]);
			
			$this->redis->publish('updateBalance', json_encode([
				'unique_id' => $this->user->unique_id,
				'balance'	=> round($this->user->balance, 2)
			]));
			
			$this->redis->publish('updateBonus', json_encode([
				'unique_id' => $this->user->unique_id,
				'bonus'		=> round($this->user->bonus, 2)
			]));

			DB::commit();
		} catch (\PDOException $e){
			DB::rollback();
			return ['msg' => 'Something went wrong...', 'type' => 'error'];
		}
		
		return ['msg' => 'You exchanged '.$sum.' bonuses on '.$curs.'$!', 'type' => 'success'];
	}
	
	public function fairCheck(Request $r) {
		$hash = $r->get('hash');
		if(!$hash) return [
			'success' => false,
			'type' => 'error',
			'msg' => 'Field cannot be empty!'
		];
		$jackpot = Jackpot::where(['hash' => $hash, 'status' => 3])->first();
		$wheel = Wheel::where(['hash' => $hash, 'status' => 3])->first();
		$crash = Crash::where(['hash' => $hash, 'status' => 2])->first();
		$coin = CoinFlip::where(['hash' => $hash, 'status' => 1])->first();
		$battle = Battle::where(['hash' => $hash, 'status' => 3])->first();
		$dice = Dice::where('hash', $hash)->first();
		
		if(!is_null($jackpot)) {
			$info = [
				'id' => $jackpot->game_id,
				'number' => $jackpot->winner_ticket
			];
		} elseif(!is_null($wheel)) {
			$info = [
				'id' => $wheel->id,
				'number' => ($wheel->winner_color == 'black' ? 2 : ($wheel->winner_color == 'red' ? 3 : ($wheel->winner_color == 'green' ? 5 : 50)))
			];
		} elseif(!is_null($crash)) {
			$info = [
				'id' => $crash->id,
				'number' => $crash->multiplier
			];
		} elseif(!is_null($coin)) {
			$info = [
				'id' => $coin->id,
				'number' => $coin->winner_ticket
			];
		} elseif(!is_null($battle)) {
			$info = [
				'id' => $battle->id,
				'number' => $battle->winner_ticket
			];
		} elseif(!is_null($dice)) {
			$info = [
				'id' => $dice->id,
				'number' => $dice->num
			];
		} else {
			return [
				'success' => false,
				'type' => 'error',
				'msg' => 'Unknown hash or round still pending!'
			];
		}
		
		return [
			'success' => true,
			'type' => 'success',
			'msg' => 'Hash finded!',
			'round' => $info['id'],
			'number' => $info['number']
		];
	}
	
	public function unbanMe() {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		if(!$this->user->banchat) return [
			'success' => false,
			'type' => 'error',
			'msg' => 'You are not banned in chat!'
		];
		if($this->user->balance < 0.50) return [
			'success' => false,
			'type' => 'error',
			'msg' => 'You do not have enough money to unlock!'
		];
		
		$this->user->balance -= 50;
		$this->user->banchat = null;
		$this->user->save();
		
		$returnValue = ['unique_id' => $this->user->unique_id, 'ban' => 0];
		$this->redis->publish('ban.msg', json_encode($returnValue));
		
		$this->redis->publish('updateBalance', json_encode([
            'unique_id' => $this->user->unique_id,
            'balance' 	=> $this->user->balance
        ]));
		
		return [
			'success' => false,
			'type' => 'success',
			'msg' => 'You are unlocked in chat!'
		];
	}
	
	public function getUser(Request $r) {
		if(is_null($r->get('id'))) return response()->json(['success' => false, 'msg' => 'User could not be found!', 'type' => 'error']);
		$user = User::where('unique_id', $r->get('id'))->select('username', 'avatar', 'unique_id', 'id')->first();
		if(is_null($user)) return response()->json(['success' => false, 'msg' => 'User could not be found!', 'type' => 'error']);
		
		$jackpotSum = JackpotBets::join('jackpot', 'jackpot.id', '=', 'jackpot_bets.game_id')
			->select('jackpot.status', 'jackpot_bets.sum')
			->where('jackpot.status', 3)
			->where(['jackpot_bets.user_id' => $user->id])
			->sum('jackpot_bets.sum');
		$wheelSum = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel_bets.price')
			->where('wheel.status', 3)
			->where(['wheel_bets.user_id' => $user->id])
			->sum('wheel_bets.price');
		$crashSum = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash_bets.price')
			->where('crash.status', 2)
			->where(['crash_bets.user_id' => $user->id])
			->sum('price');
		$coinSum = CoinFlip::where('heads', $user->id)->orWhere('tails', $user->id)->where('status', 1)->sum('bank')/2;
		$bombSum = Bomb::where('user1', $user->id)->orWhere('user2', $user->id)->where('status', 1)->sum('bank')/2;
		$battleSum = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle_bets.price')
			->where('battle.status', 3)
			->where(['battle_bets.user_id' => $user->id])
			->sum('battle_bets.price');
		$diceSum = Dice::where('user_id', $user->id)->sum('sum');
		$betAmount = $jackpotSum+$wheelSum+$crashSum+$coinSum+$battleSum+$diceSum+$bombSum;
		
		$jackpotCount = JackpotBets::join('jackpot', 'jackpot.id', '=', 'jackpot_bets.game_id')
			->select('jackpot.status', 'jackpot.id', 'jackpot_bets.game_id')
			->where('jackpot.status', 3)
			->where(['jackpot_bets.user_id' => $user->id])
			->groupBy('jackpot_bets.game_id')
			->get()->count();
		$wheelCount = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel.id', 'wheel_bets.game_id')
			->where('wheel.status', 3)
			->where(['wheel_bets.user_id' => $user->id])
			->groupBy('wheel_bets.game_id')
			->get()->count();
		$crashCount = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash.id', 'crash_bets.round_id')
			->where('crash.status', 2)
			->where(['crash_bets.user_id' => $user->id])
			->groupBy('crash_bets.round_id')
			->get()->count();
		$coinCount1 = CoinFlip::where('heads', $user->id)->where('status', 1)->count();
		$coinCount2 = CoinFlip::where('tails', $user->id)->where('status', 1)->count();
		$coinCount = $coinCount1+$coinCount2;
		$bombCount1 = Bomb::where('user1', $user->id)->where('status', 1)->count();
		$bombCount2 = Bomb::where('user2', $user->id)->where('status', 1)->count();
		$bombount = $bombCount1+$bombCount2;
		$battleCount = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle.id', 'battle_bets.game_id')
			->where('battle.status', 3)
			->where(['battle_bets.user_id' => $user->id])
			->groupBy('battle_bets.game_id')
			->get()->count();
		$diceCount = Dice::where('user_id', $user->id)->count();
		$betCount = $jackpotCount+$wheelCount+$crashCount+$coinCount+$battleCount+$diceCount+$bombount;
		
		$jackpotWin = Jackpot::where(['winner_id' => $user->id])->where('status', 3)->count();
		$wheelWin = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel.id', 'wheel_bets.game_id')
			->where('wheel.status', 3)
			->where(['wheel_bets.user_id' => $user->id, 'wheel_bets.win' => 1])
			->groupBy('wheel_bets.game_id')
			->get()->count();
		$crashWin = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash.id', 'crash_bets.round_id')
			->where('crash.status', 2)
			->where(['crash_bets.user_id' => $user->id, 'crash_bets.status' => 1])
			->groupBy('crash_bets.round_id')
			->get()->count();
		$coinWin = CoinFlip::where('winner_id', $user->id)->count();
		$bombWin = Bomb::where('winner_id', $user->id)->count();
		$battleWin = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle.id', 'battle_bets.game_id')
			->where('battle.status', 3)
			->where(['battle_bets.user_id' => $user->id, 'battle_bets.win' => 1])
			->groupBy('battle_bets.game_id')
			->get()->count();
		$diceWin = Dice::where(['user_id' => $user->id, 'win' => 1])->count();
		$betWin = $jackpotWin+$wheelWin+$crashWin+$coinWin+$battleWin+$diceWin+$bombWin;
		
		$jackpotLose = JackpotBets::join('jackpot', 'jackpot.id', '=', 'jackpot_bets.game_id')
			->select('jackpot.status', 'jackpot.id', 'jackpot_bets.game_id', 'jackpot_bets.win')
			->where('jackpot.status', 3)
			->where(['user_id' => $user->id, 'win' => 0])
			->groupBy('jackpot_bets.game_id', 'jackpot_bets.win')
			->get()->count();
		$wheelLose = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel.id', 'wheel_bets.game_id')
			->where('wheel.status', 3)
			->where(['wheel_bets.user_id' => $user->id, 'wheel_bets.win' => 0])
			->groupBy('wheel_bets.game_id')
			->get()->count();
		$crashLose = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash.id', 'crash_bets.round_id')
			->where('crash.status', 2)
			->where(['crash_bets.user_id' => $user->id, 'crash_bets.status' => 0])
			->groupBy('crash_bets.round_id')
			->get()->count();
		$coinLose1 = CoinFlip::where('winner_id', '!=', $user->id)->where('heads', $user->id)->where('status', 1)->count();
		$coinLose2 = CoinFlip::where('winner_id', '!=', $user->id)->where('tails', $user->id)->where('status', 1)->count();
		$coinLose = $coinLose1+$coinLose2;
		$bombLose1 = Bomb::where('winner_id', '!=', $user->id)->where('user1', $user->id)->where('status', 1)->count();
		$bombLose2 = Bomb::where('winner_id', '!=', $user->id)->where('user2', $user->id)->where('status', 1)->count();
		$bombLose = $bombLose1+$bombLose2;
		$battleLose = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle.id', 'battle_bets.game_id')
			->where('battle.status', 3)
			->where(['battle_bets.user_id' => $user->id, 'battle_bets.win' => 0])
			->groupBy('battle_bets.game_id')
			->get()->count();
		$diceLose = Dice::where(['user_id' => $user->id, 'win' => 0])->count();
		$betLose = $jackpotLose+$wheelLose+$crashLose+$coinLose+$battleLose+$diceLose+$bombLose;
		
		$info = [
			'unique_id' => $user->unique_id,
			'avatar' => $user->avatar,
			'username' => $user->username,
			'betAmount' => round($betAmount, 2),
			'totalGames' => $betCount,
			'wins' => $betWin,
			'lose' => $betLose
		];
		
		return response()->json(['success' => true, 'info' => $info]);
	}
	
	public function pay(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		
		if($r->get('amount') < $this->settings->min_dep) return response()->json(['success' => false, 'msg' => 'Minimum deposit amount '.$this->settings->min_dep.'$!', 'type' => 'error']);
		if(!$r->get('type')) return response()->json(['success' => false, 'msg' => 'You have not chosen a payment system!', 'type' => 'error']);
		
		$amount = floatval($r->get('amount'));
		
		if($r->get('type') == 'coinpayments') {
			$usdamo = $amount + ($amount/100*$this->settings->fee_dep);

			$depo['user_id'] = $this->user->id;
			$depo['amount'] = $amount;
			$depo['usd_amo'] = round($usdamo, 2);
			$depo['trx'] = str_random(16);
			$depo['try'] = 0;
			$depo['status'] = 0;

			$data = Deposit::create($depo);
			
			$amon = $data->amount;
			$usd = $data->usd_amo;

			$callbackUrl = route('result.cp');
			$successUrl = route('success');
			$failUrl = route('fail');
			$CP = new coinPayments();
			$CP->setMerchantId($this->settings->merchant_id);
			$CP->setSecretKey($this->settings->ipn_secret);
			$ntrc = $data->trx;

			$form = $CP->createPayment('Purchase Coin', 'USD',  $usd, $ntrc, $callbackUrl, $successUrl, $failUrl);
			$pt = 'All coin';

			return response()->json(['success' => true, 'form' => $form]);
		} elseif($r->get('type') == 'perfectmoney') {
			if(is_null($this->settings->pm_usd_wallet)) return response()->json(['success' => false, 'msg' => 'Perfect Money not activated!', 'type' => 'error']);
			$usdamo = $amount + ($amount/100*$this->settings->fee_dep);

			$depo['user_id'] = $this->user->id;
			$depo['amount'] = $amount;
			$depo['usd_amo'] = round($usdamo, 2);
			$depo['trx'] = str_random(16);
			$depo['try'] = 0;
			$depo['status'] = 0;

			$data = Deposit::create($depo);
			
			$endpoint = 'https://perfectmoney.is/api/step1.asp';
			
			$res = [
				'PAYEE_ACCOUNT' => $this->settings->pm_usd_wallet,
				'PAYEE_NAME' => $this->settings->domain,
				'PAYMENT_ID' => $data->trx,
				'PAYMENT_AMOUNT' => round($usdamo, 2),
				'PAYMENT_UNITS' => 'USD',
				'STATUS_URL' => route('result.pm'),
				'PAYMENT_URL' => route('success'),
				'PAYMENT_URL_METHOD' => 'GET',
				'NOPAYMENT_URL' => route('fail'),
				'NOPAYMENT_URL_METHOD' => 'GET',
				'SUGGESTED_MEMO' => $this->user->username,
				'BAGGAGE_FIELDS' => 'IDENT'
			];
			
			$form = $this->createForm($endpoint, $res);
			
			return response()->json(['success' => true, 'form' => $form]);
			
			return view('pages.payPM', compact('res'));
		} else {
			return response()->json(['success' => false, 'msg' => 'Error!', 'type' => 'error']);
		}
	}
	
	public function createForm($endpoint, $fields) {
		$text = '<form action="'.$endpoint.'" method="post" id="coinPayForm">';

		foreach($fields as $name => $value) {
			$text .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
		}

		return $text.'</form>';

	}
	
	public function result_cp(Request $r) {
        $track = $r->custom;
        $status = $r->status;

        $data = Deposit::where('trx', $track)->orderBy('id','DESC')->first();

        if($data->status == 0) {
			if($status >= 100 || $status == 2) {
				$this->userDataUpdate($data);
            }
        }
	}
	
	public function result_pm(Request $r) {
		$passphrase = strtoupper(md5($this->settings->pm_passphrase));
        
        define('ALTERNATE_PHRASE_HASH', $passphrase);
        define('PATH_TO_LOG', '/var/log/');
        $string =
        $_POST['PAYMENT_ID'] . ':' . $_POST['PAYEE_ACCOUNT'] . ':' .
        $_POST['PAYMENT_AMOUNT'] . ':' . $_POST['PAYMENT_UNITS'] . ':' .
        $_POST['PAYMENT_BATCH_NUM'] . ':' .
        $_POST['PAYER_ACCOUNT'] . ':' . ALTERNATE_PHRASE_HASH . ':' .
        $_POST['TIMESTAMPGMT'];
        
        $hash = strtoupper(md5($string));
        $hash2 = $_POST['V2_HASH'];

        if($hash == $hash2) {
            $amo = $_POST['PAYMENT_AMOUNT'];
            $unit = $_POST['PAYMENT_UNITS'];
            $track = $_POST['PAYMENT_ID'];
            
            $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            
            if($_POST['PAYEE_ACCOUNT'] == $this->settings->pm_usd_wallet && $unit == 'USD' && $data->status == 0) {
                $this->userDataUpdate($data);               
            }
        }
	}
	
	public function userDataUpdate($data) { 
        if($data->status == 0) {
            $data->status = 1;
            $data->update();
    
            $user = User::where('id', $data->user_id)->first();
            $user->balance += $data->amount;
            $user->update();
			
			$this->redis->publish('updateBalance', json_encode([
				'unique_id'    => $user->unique_id,
				'balance' => round($user->balance, 2)
			]));
        }
    }
	
	public function userWithdraw(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		
		if($this->settings->min_dep_withdraw) {
			$dep = Deposit::where('user_id', $this->user->id)->where('status', 1)->sum('amount');
			if($dep < $this->settings->min_dep_withdraw) return ['success' => false, 'msg' => 'To withdraw You need deposite '.$this->settings->min_dep_withdraw.'$!', 'type' => 'error'];
		}
		$system = htmlspecialchars($r->get('system'));
		$wallet = htmlspecialchars($r->get('wallet'));
		$value = htmlspecialchars($r->get('value'));
		$sum = round(str_replace('/[^.0-9]/', '', $value), 2) ?? null;
		$com = null;
		$com_sum = null;
		$min = null;
		$max = 2000;
		if($system == 'perfectmoney') {
			$com = $this->settings->pm_fee; 
			$min = $this->settings->pm_min;	
        } else {
			$com = $this->settings->coinpayments_fee; 
			$min = $this->settings->coinpayments_min;
        }
		
		$sumCom = round($sum-($sum/100*$com), 2);
		
		if($this->user->requery < $sum) return ['success' => false, 'msg' => 'Available for output '.$this->user->requery.'$!', 'type' => 'error'];
		if(empty($wallet)) return ['success' => false, 'msg' => 'Do not enter a wallet number!', 'type' => 'error'];
		if($sum < 0) return ['success' => false, 'msg' => 'Do not enter a withdrawal amount!', 'type' => 'error'];
		if(is_null($com)) return ['success' => false, 'msg' => 'Failed to calculate commission!', 'type' => 'error'];
		if($sum < $min) return ['success' => false, 'msg' => 'Minimum withdrawal amount '.$min.'$!', 'type' => 'error'];
		if($sum > $max) return ['success' => false, 'msg' => 'Maximum withdrawal amount '.$max.'$!', 'type' => 'error'];
		
		if($sum > $this->user->balance) return ['success' => false, 'msg' => 'Not enough funds to withdraw!', 'type' => 'error'];
		
		Withdraw::insert([
            'user_id' => $this->user->id,
            'value' => $sum,
            'valueWithCom' => $sumCom,
            'system' => $system,
            'wallet' => $wallet
        ]);
		
		$this->user->balance -= $sum;
		$this->user->requery -= $sum;
		$this->user->save();
		
		$this->redis->publish('updateBalance', json_encode([
            'unique_id'    => $this->user->unique_id,
            'balance' => round($this->user->balance, 2)
        ]));
		
        return ['success' => true, 'msg' => 'Payment is made to the you wallet! Expect to receive the money.', 'type' => 'success'];
	}
	
	public function success() {
		return redirect()->route('index')->with('success', 'Your balance has been successfully replenished!');
	}
	
	public function fail() {
		return redirect()->route('index')->with('error', 'An error occurred while adding funds to your balance!');
	}
	
	public function userWithdrawCancel(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['success' => false, 'msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		$id = $r->get('id');
        $withdraw = Withdraw::where('id', $id)->first();
		
		if($withdraw->status > 0) return response()->json(['success' => false, 'msg' => 'You cannot cancel this withdraw!', 'type' => 'error']);
		if($withdraw->user_id != $this->user->id) return response()->json(['success' => false, 'msg' => 'You cannot cancel another user`s withdraw!', 'type' => 'error']);
		
		$this->user->balance += $withdraw->value;
		$this->user->requery += $withdraw->value;
        $this->user->save();
        $withdraw->status = 2;
        $withdraw->save();
		
		return response()->json(['success' => true, 'msg' => 'You canceled the withdrawal on '.$withdraw->valueWithCom.'$', 'type' => 'success', 'id' => $id]);
	}
}