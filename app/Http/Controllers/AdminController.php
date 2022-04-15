<?php namespace App\Http\Controllers;

use App\User;
use App\Rooms;
use App\Profit;
use App\Deposit;
use App\Withdraw;
use App\Settings;
use App\Jackpot;
use App\JackpotBets;
use App\Wheel;
use App\WheelBets;
use App\Crash;
use App\CrashBets;
use App\CoinFlip;
use App\Battle;
use App\BattleBets;
use App\Dice;
use App\Promocode;
use App\Filter;
use App\Bonus;
use App\Exchanges;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use DB;
use Storage;

class AdminController extends Controller {
	
	const CHAT_CHANNEL = 'chat.message';
    const NEW_MSG_CHANNEL = 'new.msg';
    const CLEAR = 'chat.clear';
	const DELETE_MSG_CHANNEL = 'del.msg';
	
	protected $ssl_fix = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]];

    public function __construct() {
        parent::__construct();
		$jackpot_easy = Jackpot::where('room', 'easy')->orderBy('id', 'desc')->first();
		$jackpot_medium = Jackpot::where('room', 'medium')->orderBy('id', 'desc')->first();
		$jackpot_hard = Jackpot::where('room', 'hard')->orderBy('id', 'desc')->first();
        view()->share('chances_easy', $this->getChancesOfGame($jackpot_easy->id));
        view()->share('chances_medium', $this->getChancesOfGame($jackpot_medium->id));
        view()->share('chances_hard', $this->getChancesOfGame($jackpot_hard->id));
    }
	
	public function index() {
		$pay_today = Deposit::where('updated_at', '>=', Carbon::today())->where('status', 1)->sum('amount');
		$pay_week = Deposit::where('updated_at', '>=', Carbon::now()->subDays(7))->where('status', 1)->sum('amount');
		$pay_month = Deposit::where('updated_at', '>=', Carbon::now()->subDays(30))->where('status', 1)->sum('amount');
		$pay_all = Deposit::where('status', 1)->sum('amount');
        $with_req = Withdraw::where('status', 0)->orderBy('id', 'desc')->sum('value');
        $usersCount = User::count();
		$profit_jackpot = Profit::where('game', 'jackpot')->where('created_at', '>=', Carbon::today())->sum('sum');
		$profit_pvp = Profit::where('game', 'pvp')->where('created_at', '>=', Carbon::today())->sum('sum');
		$profit_battle = Profit::where('game', 'battle')->where('created_at', '>=', Carbon::today())->sum('sum');
		$profit_wheel = Profit::where('game', 'wheel')->where('created_at', '>=', Carbon::today())->sum('sum');
		$profit_dice = Profit::where('game', 'dice')->where('created_at', '>=', Carbon::today())->sum('sum');
		$profit_crash = Profit::where('game', 'crash')->where('created_at', '>=', Carbon::today())->sum('sum');
		$profit_exchange = Profit::where('game', 'exchange')->where('created_at', '>=', Carbon::today())->sum('sum');
		$profit = Profit::where('created_at', '>=', Carbon::today())->sum('sum');
		$profit_ref = Profit::where('game', 'ref')->where('created_at', '>=', Carbon::today())->sum('sum');
        $fake = User::where('fake', 1)->orderBy('id', 'desc')->get();
		$users = User::orderBy('id', 'desc')->where('fake', 0)->limit(10)->get();
        $userTop = User::where(['is_admin' => 0, 'is_youtuber' => 0, 'fake' => 0])->where('balance', '!=', 0)->orderBy('balance', 'desc')->limit(20)->get();
        
        $deposit = Deposit::where('status', 1)->orderBy('id', 'desc')->limit(10)->get();
		
        $last_dep = [];
        foreach($deposit as $d) {
            $user = User::where('id', $d->user_id)->first();
            $last_dep[] = [
                'id' => $user->id,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'sum' => $d->amount,
                'date' => $d->updated_at
            ];
        }
		
		return view('admin.index', compact('pay_today', 'pay_week', 'pay_month', 'pay_all', 'with_req', 'usersCount', 'profit_jackpot', 'profit_pvp', 'profit_battle', 'profit_wheel', 'profit_dice', 'profit_crash', 'profit_exchange', 'profit', 'profit_ref', 'fake', 'last_dep', 'users', 'userTop'));
    }
	
	public function users() {
		return view('admin.users');
    }
    
    public function user($id) {
        $user = User::where('id', $id)->first();
		$pay = Deposit::where('user_id', $user->id)->where('status', 1)->sum('amount');
		$withdraw = Withdraw::where('user_id', $user->id)->where('status', 1)->sum('value');
		
		$jackpotWin = Jackpot::where(['winner_id' => $user->id])->where('status', 3)->sum('winner_balance');
		$wheelWin = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel.id', 'wheel_bets.game_id', 'wheel_bets.win_sum')
			->where('wheel.status', 3)
			->where('wheel_bets.balance', 'balance')
			->where(['wheel_bets.user_id' => $user->id, 'wheel_bets.win' => 1])
			->groupBy('wheel_bets.game_id', 'wheel_bets.win_sum')
			->get()->sum('win_sum');
		$crashWin = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash.id', 'crash_bets.round_id', 'crash_bets.won')
			->where('crash.status', 2)
			->where('crash_bets.balType', 'balance')
			->where(['crash_bets.user_id' => $user->id, 'crash_bets.status' => 1])
			->groupBy('crash_bets.round_id', 'crash_bets.won')
			->get()->sum('won');
		$coinWin = CoinFlip::where('winner_id', $user->id)->where('balType', 'balance')->sum('bank')/2;
		$coinWin = CoinFlip::where('winner_id', $user->id)->where('balType', 'balance')->sum('winner_sum')-$coinWin;
		$battleWin = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle.id', 'battle_bets.game_id', 'battle_bets.price')
			->where('battle.status', 3)
			->where('battle_bets.balType', 'balance')
			->where(['battle_bets.user_id' => $user->id, 'battle_bets.win' => 1])
			->groupBy('battle_bets.game_id', 'battle_bets.price')
			->get()->sum('price');
		$battleWin = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle.id', 'battle_bets.game_id', 'battle_bets.win_sum')
			->where('battle.status', 3)
			->where('battle_bets.balType', 'balance')
			->where(['battle_bets.user_id' => $user->id, 'battle_bets.win' => 1])
			->groupBy('battle_bets.game_id', 'battle_bets.win_sum')
			->get()->sum('win_sum')-$battleWin;
		$diceWin = Dice::where(['user_id' => $user->id, 'balType' => 'balance', 'win' => 1])->sum('win_sum');
		$betWin = $jackpotWin+$wheelWin+$crashWin+$coinWin+$battleWin+$diceWin;
		
		$jackpotLose = JackpotBets::join('jackpot', 'jackpot.id', '=', 'jackpot_bets.game_id')
			->select('jackpot.status', 'jackpot.id', 'jackpot_bets.game_id', 'jackpot_bets.win', 'jackpot_bets.sum')
			->where('jackpot.status', 3)
			->where('jackpot_bets.balance', 'balance')
			->where(['user_id' => $user->id, 'win' => 0])
			->groupBy('jackpot_bets.game_id', 'jackpot_bets.win', 'jackpot_bets.sum')
			->get()->sum('sum');
		$wheelLose = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel.id', 'wheel_bets.game_id', 'wheel_bets.price')
			->where('wheel.status', 3)
			->where('wheel_bets.balance', 'balance')
			->where(['wheel_bets.user_id' => $user->id, 'wheel_bets.win' => 0])
			->groupBy('wheel_bets.game_id', 'wheel_bets.price')
			->get()->sum('price');
		$crashLose = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash.id', 'crash_bets.round_id', 'crash_bets.price')
			->where('crash.status', 2)
			->where('crash_bets.balType', 'balance')
			->where(['crash_bets.user_id' => $user->id, 'crash_bets.status' => 0])
			->groupBy('crash_bets.round_id', 'crash_bets.price')
			->get()->sum('price');
		$coinLose1 = CoinFlip::where('winner_id', '!=', $user->id)->where('balType', 'balance')->where('heads', $user->id)->where('status', 1)->sum('bank')/2;
		$coinLose2 = CoinFlip::where('winner_id', '!=', $user->id)->where('balType', 'balance')->where('tails', $user->id)->where('status', 1)->sum('bank')/2;
		$coinLose = $coinLose1+$coinLose2;
		$battleLose = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle.id', 'battle_bets.game_id', 'battle_bets.price')
			->where('battle.status', 3)
			->where('battle_bets.balType', 'balance')
			->where(['battle_bets.user_id' => $user->id, 'battle_bets.win' => 0])
			->groupBy('battle_bets.game_id', 'battle_bets.price')
			->get()->sum('price');
		$diceLose = Dice::where(['user_id' => $user->id, 'balType' => 'balance', 'win' => 0])->sum('sum');
		$betLose = $jackpotLose+$wheelLose+$crashLose+$coinLose+$battleLose+$diceLose;
		
		$exchanges = round(Exchanges::where('user_id', $user->id)->sum('sum')/$this->settings->exchange_curs, 2);
		
		return view('admin.user', compact('user', 'pay', 'withdraw', 'exchanges', 'jackpotWin', 'jackpotLose', 'wheelWin', 'wheelLose', 'crashWin', 'crashLose', 'coinWin', 'coinLose', 'battleWin', 'battleLose', 'diceWin', 'diceLose', 'betWin', 'betLose')); 
    }
	
	public function userSave(Request $r) {
        $admin = 0;
        $moder = 0;
        $youtuber = 0;
		$banchat = null;
        $time = 0;
        if($r->get('id') == null) return back()->with('error', 'Could not find user with this ID!');
        if($r->get('balance') == null) return back()->with('error', 'The "balance" field cannot be empty!');
        if($r->get('bonus') == null) return back()->with('error', 'The "bonus" field cannot be empty!');
		
        if($r->get('priv') == 'admin') $admin = 1;
        if($r->get('priv') == 'moder') $moder = 1;
        if($r->get('priv') == 'youtuber') $youtuber = 1;
        if(!is_null($r->get('time'))) $time = $r->get('time');
		
		if($r->get('banchat') != null) $banchat = Carbon::parse($r->get('banchat'))->getTimestamp();
        
        User::where('id', $r->get('id'))->update([
            'balance' => $r->get('balance'),
            'bonus' => $r->get('bonus'),
            'is_admin' => $admin,
            'is_moder' => $moder,
            'is_youtuber' => $youtuber,
            'ban' => $r->get('ban'),
            'ban_reason' => $r->get('ban_reason'),
            'banchat' => $banchat,
            'banchat_reason' => $r->get('banchat_reason'),
            'time' => $time
        ]);
		
        return back()->with('success', 'User saved!');
    }
	
    public function usersAjax() {
        return datatables(User::query()->where('fake', 0))->toJson();
    }
    
	public function bots() {
        $bots = User::where('fake', 1)->get();
		return view('admin.bots', compact('bots')); 
    }
	
	public function getVKUser(Request $r) {
        $vk_url = $r->get('url');
        $old_url = ($vk_url);
        $url = explode('/', trim($old_url,'/'));
        $url_parse = array_pop($url);
        $url_last = preg_replace('/&?id+/i', '', $url_parse);
        $runfile = 'https://api.vk.com/method/users.get?v=5.80&user_ids='.$url_last.'&fields=photo_max&lang=0&access_token='.$this->settings->vk_service_key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $runfile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $user = curl_exec($ch);
        curl_close($ch);
        $user = json_decode($user);
        $user = $user->response;
		return $user; 
    }
	
	public function bonus() {
		$bonuses = Bonus::get();
        return view('admin.bonus', compact('bonuses'));
    }
    
    public function bonusNew(Request $r) {
        $sum = $r->get('sum');
        $type = $r->get('type');
        $bg = $r->get('bg');
        $color = $r->get('color');
        $status = $r->get('status');
        if(!$sum) return redirect()->route('admin.bonus')->with('error', 'The "amount" field cannot be empty!');
        if(!$bg) return redirect()->route('admin.bonus')->with('error', 'The "background color" field cannot be empty!');
        if(!$color) return redirect()->route('admin.bonus')->with('error', 'The "text color" field cannot be empty!');
        
        Bonus::create([
            'sum' => $sum,
            'type' => $type,
            'bg' => $bg,
            'color' => $color,
            'status' => $status
        ]);

        return redirect()->route('admin.bonus')->with('success', 'Bonus created!');
    }
    
    public function bonusSave(Request $r) {
        $id = $r->get('id');
        $sum = $r->get('sum');
        $type = $r->get('type');
        $bg = $r->get('bg');
        $color = $r->get('color');
        $status = $r->get('status');
        if(!$sum) return redirect()->route('admin.bonus')->with('error', 'The "amount" field cannot be empty!');
        if(!$bg) return redirect()->route('admin.bonus')->with('error', 'The "background color" field cannot be empty!');
        if(!$color) return redirect()->route('admin.bonus')->with('error', 'The "text color" field cannot be empty!');
        
        Bonus::where('id', $id)->update([
            'sum' => $sum,
            'type' => $type,
            'bg' => $bg,
            'color' => $color,
            'status' => $status
        ]);

        return redirect()->route('admin.bonus')->with('success', 'Bonus updated!');
    }
    
    public function bonusDelete($id) {
        if(!$id) return redirect()->route('admin.bonus')->with('error', 'Could not find such bonus!');
        Bonus::where('id', $id)->delete();
        
        return redirect()->route('admin.bonus')->with('success', 'Bonus removed!');
    }
	
	public function promo() {
		$promos = Promocode::get();
        return view('admin.promo', compact('promos'));
    }
    
    public function promoNew(Request $r) {
        $code = $r->get('code');
        $type = $r->get('type');
        $limit = $r->get('limit');
        $amount = $r->get('amount');
        $count_use = $r->get('count_use');
        $have = Promocode::where('code', $code)->first();
        if(!$code) return redirect()->route('admin.promo')->with('error', 'The "сode" field cannot be empty!');
        if(!$amount) return redirect()->route('admin.promo')->with('error', 'The "amount" field cannot be empty!');
        if(!$count_use) return redirect()->route('admin.promo')->with('error', 'Field "number of activations" can not be empty!');
        if($have) return redirect()->route('admin.promo')->with('error', 'This code already exists');
        
        Promocode::create([
            'code' => $code,
            'type' => $type,
            'limit' => $limit,
            'amount' => $amount,
            'count_use' => $count_use
        ]);

        return redirect()->route('admin.promo')->with('success', 'Promo code created!');
    }
    
    public function promoSave(Request $r) {
        $id = $r->get('id');
        $code = $r->get('code');
        $type = $r->get('type');
        $limit = $r->get('limit');
        $amount = $r->get('amount');
        $count_use = $r->get('count_use');
        $have = Promocode::where('code', $code)->where('id', '!=', $id)->first();
        if(!$id) return redirect()->route('admin.promo')->with('error', 'This ID could not be found!');
        if(!$code) return redirect()->route('admin.promo')->with('error', 'The "сode" field cannot be empty!');
        if(!$amount) return redirect()->route('admin.promo')->with('error', 'The "amount" field cannot be empty!');
        if(!$count_use) $count_use = 0;
        if($have) return redirect()->route('admin.promo')->with('error', 'This code already exists');
        
        Promocode::where('id', $id)->update([
            'code' => $code,
            'type' => $type,
            'limit' => $limit,
            'amount' => $amount,
            'count_use' => $count_use
        ]);

        return redirect()->route('admin.promo')->with('success', 'Promo code updated!');
    }
    
    public function promoDelete($id) {
        if(!$id) return redirect()->route('admin.promo')->with('error', 'Could not find such promo code!');
        Promocode::where('id', $id)->delete();
        
        return redirect()->route('admin.promo')->with('success', 'Promo code removed!');
    }
	
	public function filter() {
		$filters = Filter::get();
        return view('admin.filter', compact('filters'));
    }
    
    public function filterNew(Request $r) {
        $word = $r->get('word');
        $have = Filter::where('word', $word)->first();
        if(!$word) return redirect()->route('admin.filter')->with('error', 'The "filter" field cannot be empty!');
        if($have) return redirect()->route('admin.filter')->with('error', 'This filter already exists');
        
        Filter::create([
            'word' => $word
        ]);

        return redirect()->route('admin.filter')->with('success', 'Filter is created!');
    }
    
    public function filterSave(Request $r) {
        $word = $r->get('word');
        $have = Filter::where('word', $word)->first();
        if(!$id) return redirect()->route('admin.filter')->with('error', 'This ID could not be found!');
        if(!$word) return redirect()->route('admin.filter')->with('error', 'The "filter" field cannot be empty!');
        if($have) return redirect()->route('admin.filter')->with('error', 'This filter already exists');
        
        Filter::where('id', $id)->update([
            'word' => $word
        ]);

        return redirect()->route('admin.filter')->with('success', 'Filter updated!');
    }
    
    public function filterDelete($id) {
        if(!$id) return redirect()->route('admin.filter')->with('error', 'Could not find such filter!');
        Filter::where('id', $id)->delete();
        
        return redirect()->route('admin.filter')->with('success', 'Filter removed!');
    }
    
    public function withdraws() {
        $list = Withdraw::where('status', 0)->get();
        $withdraws = [];
        foreach($list as $itm) {
            $user = User::where('id', $itm->user_id)->first();
            $withdraws[] = [
                'id' => $itm->id,
                'user_id' => $user->id,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'system' => $itm->system,
                'wallet' => $itm->wallet,
                'value' => $itm->value,
                'status' => $itm->status
            ];
        }
        
        $list2 = Withdraw::where('status', 1)->get();
        $finished = [];
        foreach($list2 as $itm) {
            $user = User::where('id', $itm->user_id)->first();
            $finished[] = [
                'id' => $itm->id,
                'user_id' => $user->id,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'system' => $itm->system,
                'wallet' => $itm->wallet,
                'value' => $itm->value,
                'status' => $itm->status
            ];
        }
        
        return view('admin.withdraws', compact('withdraws', 'finished'));
    }
	
	public function withdrawSend($id) {
        $withdraw = Withdraw::where('id', $id)->first();
		if($withdraw->status > 0) return redirect()->route('admin.withdraws')->with('error', 'This withdraw has already been processed or canceled');
		
		if($withdraw->system == 'perfectmoney') {
			$descripion = 'Withdraw from '.$this->settings->domain. ' number: '.$withdraw->id;
			$url = file_get_contents('https://perfectmoney.is/acct/confirm.asp?AccountID=' . urlencode(trim($this->settings->pm_uid)) . '&PassPhrase=' . urlencode(trim($this->settings->pm_pass)) . '&Payer_Account=' . urlencode(trim($this->settings->pm_usd_wallet)) . '&Payee_Account=' . urlencode(trim($withdraw->wallet)) . '&Amount=' . $withdraw->value . '&Memo=' . urlencode(trim($descripion)) . '&PAYMENT_ID=' . urlencode(trim($withdraw->id)), false, stream_context_create($this->ssl_fix));
			
			if(!$url) return redirect()->route('admin.withdraws')->with('error', 'Connection error');
			if(!preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/", $url, $result, PREG_SET_ORDER)) return redirect()->route('admin.withdraws')->with('error', 'Invalid output');
			
			$data = [];
			foreach($result as $item) {
				if($item[1] == 'ERROR') {
					return redirect()->route('admin.withdraws')->with('error', $item[2]);
				} else {
					$data['data'][$item[1]] = $item[2];
				}
			}

			$data['status'] = 'success';
			
			$withdraw->status = 1;
			$withdraw->save();
		} else {
			$req = array(
				'amount' => $withdraw->value,
				'currency' => $withdraw->system,
				'currency2' => 'USD',
				'address' => $withdraw->wallet,
				'auto_confirm' => 1
			);
			
			$res = parent::api_call('create_withdrawal', $req);
			
			if($res['error'] == 'ok') {
				$withdraw->status = 1;
				$withdraw->save();
			} else {
				return redirect()->route('admin.withdraws')->with('success', $res['error']);
			}
		}
		
		return redirect()->route('admin.withdraws')->with('success', 'Withdraw queued!');
	}
    
    public function withdrawReturn($id) {
        $withdraw = Withdraw::where('id', $id)->first();
		if($withdraw->status > 0) return redirect()->route('admin.withdraws')->with('error', 'This withdraw has already been processed or canceled');
        $user = User::where('id', $withdraw->user_id)->first();
        
        $user->balance += $withdraw->value;
        $user->requery += $withdraw->value;
        $user->save();
        $withdraw->status = 2;
        $withdraw->save();
			
		$this->redis->publish('updateBalance', json_encode([
			'unique_id' => $user->unique_id,
			'balance'	=> $user->balance
		]));
        
        return redirect()->route('admin.withdraws')->with('success', 'You returned '.$withdraw->valueWithCom.'$ on balance for '.$user->username);
    }
	
	public function getUserByMonth() {
		$chart = User::select(DB::raw('DATE_FORMAT(created_at, "%d.%m") as date'), DB::raw('count(*) as count'))
			->where('fake', 0)
			->whereMonth('created_at', '=', date('m'))
			->groupBy('date')
			->get();
		
		return $chart;
	}
	
	public function getDepsByMonth() {
		$chart = Deposit::where('status', 1)->select(DB::raw('DATE_FORMAT(created_at, "%d.%m") as date'), DB::raw('SUM(amount) as sum'))
			->whereMonth('created_at', '=', date('m'))
			->groupBy('date')
			->get();
		
		return $chart;
	}
    
    public function socketStart() {
        putenv('HOME='.storage_path('app'));
        $start_socket = new Process('pm2 start '.storage_path('bot/app.js'));
        $start_socket->run();
        $start_socket->start();

    	return response()->json([
            'type'	=> 'success',
            'msg'	=> 'Socket started!'
        ]);
    }
    
    public function socketStop() {
        putenv('HOME='.storage_path('app'));
        $stop_socket = new Process('pm2 stop '.storage_path('bot/app.js')); 
        $dell_proc = new Process('pm2 delete app'); 

        $stop_socket->start(); 
        $dell_proc->start();

    	return response()->json([
            'type'	=> 'success',
            'msg'	=> 'Socket stoped!'
        ]);
    }
    
    public function settings() {
		$rooms = Rooms::get();
		return view('admin.settings', compact('rooms')); 
    }
    
    public function settingsSave(Request $r) {
		Settings::where('id', 1)->update([
            'domain' => $r->get('domain'),
			'sitename' => $r->get('sitename'),
			'title' => $r->get('title'),
			'description' => $r->get('description'),
			'keywords' => $r->get('keywords'),
			'vk_url' => $r->get('vk_url'),
			'vk_support_link' => $r->get('vk_support_link'),
			'vk_service_key' => $r->get('vk_service_key'),
			'censore_replace' => $r->get('censore_replace'),
			'chat_dep' => $r->get('chat_dep'),
			'merchant_id' => $r->get('merchant_id'),
			'ipn_secret' => $r->get('ipn_secret'),
			'public_key' => $r->get('public_key'),
			'private_key' => $r->get('private_key'),
			'coinpayments_fee' => $r->get('coinpayments_fee'),
			'coinpayments_min' => $r->get('coinpayments_min'),
			'pm_uid' => $r->get('pm_uid'),
			'pm_pass' => $r->get('pm_pass'),
			'pm_usd_wallet' => $r->get('pm_usd_wallet'),
			'pm_passphrase' => $r->get('pm_passphrase'),
			'pm_fee' => $r->get('pm_fee'),
			'pm_min' => $r->get('pm_min'),
			'profit_koef' => $r->get('profit_koef'),
			'jackpot_commission' => $r->get('jackpot_commission'),
			'wheel_timer' => $r->get('wheel_timer'),
			'wheel_min_bet' => $r->get('wheel_min_bet'),
			'wheel_max_bet' => $r->get('wheel_max_bet'),
			'crash_min_bet' => $r->get('crash_min_bet'),
			'crash_max_bet' => $r->get('crash_max_bet'),
			'crash_timer' => $r->get('crash_timer'),
			'battle_timer' => $r->get('battle_timer'),
			'battle_min_bet' => $r->get('battle_min_bet'),
			'battle_max_bet' => $r->get('battle_max_bet'),
			'battle_commission' => $r->get('battle_commission'),
			'dice_min_bet' => $r->get('dice_min_bet'),
			'dice_max_bet' => $r->get('dice_max_bet'),
			'flip_commission' => $r->get('flip_commission'),
			'flip_min_bet' => $r->get('flip_min_bet'),
			'flip_max_bet' => $r->get('flip_max_bet'),
			'exchange_min' => $r->get('exchange_min'),
			'exchange_curs' => $r->get('exchange_curs'),
			'ref_perc' => $r->get('ref_perc'),
			'ref_sum' => $r->get('ref_sum'),
			'min_ref_withdraw' => $r->get('min_ref_withdraw'),
			'min_dep' => $r->get('min_dep'),
			'max_dep' => $r->get('max_dep'),
			'min_dep_withdraw' => $r->get('min_dep_withdraw'),
			'bonus_group_time' => $r->get('bonus_group_time'),
			'max_active_ref' => $r->get('max_active_ref')
        ]);
		
		$rooms = Rooms::get();
		
		foreach($rooms as $room) {
			Rooms::where('name', $room->name)->update([
				'time' => $r->get('time_'.$room->name),
				'min' => $r->get('min_'.$room->name),
				'max' => $r->get('max_'.$room->name),
				'bets' => $r->get('bets_'.$room->name)
			]);
		}
		return redirect()->route('admin.settings')->with('success', 'Settings saved!');
    }
	
	public function getBanned() {
		$users = User::where('banchat', '!=', NULL)->select('username', 'avatar', 'unique_id', 'banchat', 'banchat_reason')->get();
		if(is_null($users)) return response()->json(['success' => false, 'msg' => 'Could not find users!', 'type' => 'error']);
		return response()->json(['success' => true, 'users' => $users]);
	}
	
	public function add_message(Request $r) {
        $val = \Validator::make($r->all(), [
            'message' => 'required|string|max:255'
        ],[
            'required' => 'Message cannot be empty!',
            'string' => 'The message must be a string!',
            'max' => 'The maximum message size of 255 characters.',
        ]);
        $error = $val->errors();

        if($val->fails()){
            return response()->json(['message' => $error->first('message'), 'status' => 'error']);
        }
        
		$user = User::where('user_id', $r->get('user_id'))->first();
		
        $messages = $r->get('message');
        if(\Cache::has('addmsg.user.' . $user->id)) return response()->json(['message' => 'You send messages too often!', 'status' => 'error']);
        \Cache::put('addmsg.user.' . $user->id, '', 0.05);
        $nowtime = time();
        $banchat = $user->banchat;
        $lasttime = $nowtime - $banchat;
        
        if($banchat >= $nowtime) {
            return response()->json(['message' => 'You are blocked to: '.date("d.m.Y H:i:s", $banchat), 'status' => 'error']);
        } else {
            User::where('user_id', $user->user_id)->update(['banchat' => null]);
        }
		
        $time = date('H:i', time());
        $moder = $user->is_moder;
        $youtuber = $user->is_youtuber;
        $admin = 0;
        $ban = $user->banchat;
		$unique_id = $user->unique_id;
        $username = htmlspecialchars($user->username);
        $avatar = $user->avatar;

        function object_to_array($data) {
            if (is_array($data) || is_object($data)) {
                $result = array();
                foreach ($data as $key => $value) {
                    $result[$key] = object_to_array($value);
                }
                return $result;
            }
            return $data;
        }

        $words = file_get_contents(dirname(__FILE__) . '/words.json');
        $words = object_to_array(json_decode($words));

        foreach ($words as $key => $value) {
            $messages = str_ireplace($key, $value, $messages);
        }

		if(preg_match("/href|url|http|https|www|.ru|.com|.net|.info|csgo|winner|ru|xyz|com|net|info|.org/i", $messages)) {
			return response()->json(['message' => 'No links allowed!', 'status' => 'error']);
		}
        $returnValue = ['unique_id' => $unique_id, 'avatar' => $avatar, 'time2' => Carbon::now()->getTimestamp(), 'time' => $time, 'messages' => htmlspecialchars($messages), 'username' => $username, 'ban' => $ban, 'admin' => $admin, 'moder' => $user->is_moder, 'youtuber' => $user->is_youtuber];
        $this->redis->rpush(self::CHAT_CHANNEL, json_encode($returnValue));
        $this->redis->publish(self::NEW_MSG_CHANNEL, json_encode($returnValue));
		return response()->json(['message' => 'Your message has been successfully sent!', 'status' => 'success']);
	}
	
	public static function getChancesOfGame($gameid) {
		$game = Jackpot::where('id', $gameid)->first();
        $users = [];
        if(!$game) return;
        $bets = JackpotBets::where('game_id', $game->id)->orderBy('id', 'desc')->get();
        foreach($bets as $bet) {
            $find = 0;
            foreach($users as $user) if($user == $bet->user_id) $find++;
            if($find == 0) $users[] = $bet->user_id;
        }
        
        // get chances
        $chances = [];
        foreach($users as $user) {
            $user   = User::where('id', $user)->first();
            $value  = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->sum('sum');
            $price  = JackpotBets::where('game_id', $game->id)->sum('sum');
            $chance = round(($value/$price)*100);
			
            $chances[] = [
                'game_id'   => $game->id,
                'id'        => $user->id,
                'username'  => $user->username,
                'avatar'    => $user->avatar,
                'sum'    	=> $value,
                'chance'    => round($chance, 2)
            ];
        }
        
        usort($chances, function($a, $b) {
            return ($b['chance']-$a['chance']); 
        });
        
        return $chances;
    }
	
	public function getParam() {
		return [
			'fake' => $this->settings->fakebets
		];
    }
	
	public function getOnline() {
		$user = 0;
		if($this->settings->fakebets) {
			$now = Carbon::now()->format('H');
			if($now < 06) $time = 4;
			if($now >= 06 && $now < 12) $time = 1;
			if($now >= 12 && $now < 18) $time = 2;
			if($now >= 18) $time = 3;
			$user = User::where(['fake' => 1, 'time' => $time])->count();
			$userAll = User::where(['fake' => 1, 'time' => 0])->count();
			if(!is_null($userAll)) $user += $userAll;
		}
		
        return $user;
    }
	
	public function getCurrency() {
		$req = array(
			'short' => 1,
			'accepted' => 1
		);
		$res = parent::api_call('rates', $req);
		
		if($res['result'] == null) return ['success' => false];
		if($res['error'] != 'ok') return ['success' => false];
			
		$res = $res['result'];
		
		$list = [];
		foreach($res as $name => $l) {
			$list[$name] = [
				'rate' => $res[$name]['rate_btc']
			];
		}
		
		Storage::disk('local')->put('currency.json', json_encode($list));
		
		return ['success' => true];
	}
	
	public function convert($amount, $currency) {
		$all = file_get_contents("https://blockchain.info/ticker");
		$res = json_decode($all);
		$btcrate = $res->USD->last;
		$bcoin = round($amount/$btcrate, 8);
		
		$req = array(
			'short' => 1,
			'accepted' => 1
		);
		$res = parent::api_call('rates', $req);
		
		if($res['error'] == 'ok') {
			$res = $res['result'][$currency]['rate_btc'];
			$res = round($bcoin/$res, 8);
		} else {
			while($res['error'] == 'ok') {
				$res = parent::api_call('rates', $req);
			}
			
			$res = $res['result'][$currency]['rate_btc'];
			$res = round($bcoin/$res, 8);
		}
		
		return $res;
	}
}