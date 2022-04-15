<?php namespace App\Http\Controllers;

use App\User;
use App\Jackpot;
use App\Wheel;
use App\Crash;
use App\CoinFlip;
use App\Battle;
use App\Dice;
use App\Settings;
use App\Withdraw;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Redis;
use Auth;
use DB;
use Carbon\Carbon;
use Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $user;
	public $ch = null;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            view()->share('u', $this->user);
            return $next($request);
        });
        Carbon::setLocale('en');
        $this->redis = Redis::connection();
		$this->settings = Settings::first();
        view()->share('gws', $this->getWithSettings());
        view()->share('messages', $this->chatMessage());
        view()->share('stats', $this->stats());
		view()->share('settings', $this->settings);
		view()->share('btcr', $this->btcr());
		view()->share('rates', $this->rates());
    }
	
	public function getWithSettings() {
        $settings = Settings::where('id', 1)->select('vk_url', 'bonus_group_time', 'max_active_ref', 'exchange_min', 'exchange_curs', 'pm_fee', 'pm_min', 'coinpayments_fee', 'coinpayments_min')->first();
        return $settings;
    }
	
	public function chatMessage() {
        $messages = ChatController::chat();
        return $messages;
    }
	
	public function stats() {
        $countUsers = User::count();
        $countUsersToday = User::where('created_at', '>=', Carbon::today())->count();
		$jackpot = Jackpot::where('status', 3)->orderBy('id', 'desc')->count();
		$wheel = Wheel::where('status', 3)->orderBy('id', 'desc')->count();
		$crash = Crash::where('status', 2)->orderBy('id', 'desc')->count();
		$coin = CoinFlip::where('status', 1)->orderBy('id', 'desc')->count();
		$battle = Battle::where('status', 3)->orderBy('id', 'desc')->count();
		$dice = Dice::orderBy('id', 'desc')->count();
		$totalGames = $jackpot+$wheel+$crash+$coin+$battle+$dice;
		$totalWithdraw = Withdraw::where('status', 1)->sum('value');
		
		$data = [
			'countUsers' => $countUsers,
			'countUsersToday' => $countUsersToday,
			'totalGames' => $totalGames,
			'totalWithdraw' => $totalWithdraw
		];
        return $data;
    }
	
	public function btcr() {
        $all = file_get_contents("https://blockchain.info/ticker");
		$res = json_decode($all);
		$btcrate = $res->USD->last;
		
		return $btcrate;
    }
	
	public function rates() {
        $content = Storage::disk('local')->get('currency.json');
		
		return $content;
    }
	
	public function api_call($cmd, $req = array()) {
    	$req['version'] = 1;
		$req['cmd'] = $cmd;
		$req['key'] = $this->settings->public_key;
		$req['format'] = 'json';
		$post_data = http_build_query($req, '', '&');
		$hmac = hash_hmac('sha512', $post_data, $this->settings->private_key);
		if($this->ch === null) {
			$this->ch = curl_init('https://www.coinpayments.net/api.php');
			curl_setopt($this->ch, CURLOPT_FAILONERROR, TRUE);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		}
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('HMAC: '.$hmac));
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_data);
	    
		$data = curl_exec($this->ch);                
		if($data !== FALSE) {
			if(PHP_INT_SIZE < 8 && version_compare(PHP_VERSION, '5.4.0') >= 0) {
				$dec = json_decode($data, TRUE, 512, JSON_BIGINT_AS_STRING);
			} else {
				$dec = json_decode($data, TRUE);
			}
			if($dec !== NULL && count($dec)) {
				return $dec;
			} else {
				return array('error' => 'Unable to parse JSON result ('.json_last_error().')');
			}
		} else {
			return array('error' => 'cURL error: '.curl_error($this->ch));
		}
	}
}