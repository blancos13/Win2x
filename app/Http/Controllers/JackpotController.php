<?php namespace App\Http\Controllers;

use App\User;
use App\Rooms;
use App\Profit;
use App\Jackpot;
use App\JackpotBets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DB;

class JackpotController extends Controller {
	
    public function __construct(Request $r) {
		parent::__construct();
		$rooms = Rooms::where('status', 0)->orderBy('id', 'desc')->get();
		foreach($rooms as $s) {
			$room = Rooms::where('name', $r->get('room'))->first();
			if(!$room) $this->room = $s->name;
			else $this->room = $room->name;
		}
		$this->game = Jackpot::where('room', $this->room)->orderBy('game_id', 'desc')->first();
		if(!$this->game) Jackpot::create([
			'room' => $this->room,
			'game_id' => 1,
			'hash' => bin2hex(random_bytes(16))
		]);
		view()->share('rooms', $rooms);
		view()->share('room', $this->room);
		DB::connection()->getPdo()->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    }
	
	public function getRooms() {
		$room = Rooms::where('status', 0)->get();
		
		return $room;
	}
	
	public function index() {
		return view('pages.jackpot');
	}
	
	public function newGame(Request $r) {
		$room = Rooms::where('name', $r->get('room'))->first();
		if(is_null($room)) return response()->json([
			'success' => false,
			'msg' => 'Could not find the room you want to bet in!'
		]);
		
        $game = Jackpot::create([
			'room' => $room->name,
			'game_id' => $this->game->game_id+1,
			'hash' => bin2hex(random_bytes(16))
		]);
		
		return response()->json([
			'success' => true,
			'data' => [
				'game'     	 => [
					'game_id'	=> $game->game_id,
					'id'   	 	=> $game->id,
					'hash'	 	=> $game->hash,
				],
				'time'  	 => $room->time
			]
		]);
	}

	public function parseJackpotGame($id) {
		$game = Jackpot::where('id', $id)->first();
		if(!$game) return null;

		$room = Rooms::where('name', $game->room)->first();
		
		$bets = JackpotBets::where('game_id', $game->id)->orderBy('id', 'asc')->get();

		$returnPrice = 0;
		$lastTicket = 0;
		$returnBets = [];
		$returnAmount = [];
		$returnUsers = [];
		
		foreach($bets as $bet) {
			$user = (isset($returnUsers[$bet->user_id])) ? $returnUsers[$bet->user_id] : User::where('id', $bet->user_id)->first();
			if($user) {
				$betSum = $bet->sum;
				if($bet->balance == 'bonus') $betSum = round($bet->sum/$this->settings->exchange_curs, 2);
				$lastTicket++;
				$returnUsers[$user->id] = $user;
				$returnUsers[$user->id]->color = $bet->color;
				$returnBets[] = [
					'user' => [
						'id' => $user->unique_id,
						'user_id' => $user->id,
						'username' => $user->username,
						'avatar' => $user->avatar
					],
					'bet' => [
						'amount' => $bet->sum,
						'color' => $bet->color,
						'balance' => $bet->balance,
						'from' => floor($lastTicket),
						'to' => floor($lastTicket+($betSum*100))
					],
					'chart' => [
						'src' => $user->avatar,
						'width' => 35,
						'height' => 35
					]
				];

				if(!isset($returnAmount[$user->unique_id])) $returnAmount[$user->unique_id] = $betSum; else $returnAmount[$user->unique_id] += $betSum;

				$lastTicket += $betSum*100;
				$returnPrice += $betSum;
			}
		}
		
		foreach($returnBets as $key => $bet) {
			$returnBets[$key]['bet']['chance'] = number_format(($returnAmount[$bet['user']['id']]/$returnPrice)*100, 2);
		}
		
		$returnChances = [];
		foreach($returnUsers as $key => $user) {
			$returnChances[] = [
				'game_id' => $game->id,
				'sum' => $returnAmount[$user->unique_id],
				'user' => [
					'id' => $user->id,
					'username' => $user->username,
					'avatar' => $user->avatar
				],
				'color' => $user->color,
				'chance' => number_format(($returnAmount[$user->unique_id]/$returnPrice)*100, 2)
			];
		}

		$circleStart = 0;
		$circleAll = 0;
		foreach($returnChances as $key => $ch) {
			$userChance = $ch['chance']/100;
			$circleAll += $userChance;
			$returnChances[$key]['circle'] = [
				'color' => $ch['color'],
				'start' => $circleStart,
				'end' => $circleStart + (360*$userChance)
			];

			$circleStart = $returnChances[$key]['circle']['end'];
		}

		foreach($returnUsers as $key => $user) $returnUsers[$key]['circleValue'] = ($user['chance']/$circleAll)*100;

		return [
			'success' => true,
			'data' => [
				'id' => $game->id,
				'game_id' => $game->game_id,
				'hash' => $game->hash,
				'amount' => $returnPrice,
				'chances' => $returnChances,
				'bets' => array_reverse($returnBets),
				'time' => $room->time,
				'room' => $room->name,
				'min' => $room->min,
				'max' => $room->max
			]
		];
	}

	public function initRoom(Request $r) {
		$room = Rooms::where('name', $r->get('room'))->first();
		if(is_null($room)) return [
			'success' => false
		];

		$game = Jackpot::where('room', $room->name)->orderBy('id', 'desc')->first();
		if(is_null($game)) return [
			'success' => false
		];

		return $this->parseJackpotGame($game->id);
	}

	public function newBet(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Wait before previous action!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		if($this->user->ban) return;
		$room = Rooms::where('name', $r->get('room'))->first();
		if(is_null($room)) return [
			'success' => false,
			'msg' => 'Could not find the room you want to bet in!'
		];

		$game = Jackpot::where('room', $room->name)->orderBy('id', 'desc')->first();
		if(is_null($game)) return [
			'success' => false,
			'msg' => 'Failed to find a game in the room '.$room->name
		];

		if($game->status > 1) return [
			'success' => false,
			'msg' => 'Bets in this game are closed!'
		];
		
		$user = User::where('id', $this->user->id)->first();
		$userbets = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->get();
		
		if($userbets->count() >= $room->bets) return [
			'success' => false,
			'msg' => 'You can`t do more '.$room->bets.' bets per game!'
		];
			
		if(floatval($r->get('amount')) < $room->min) return [
			'success' => false,
			'msg' => 'Minimum bet amount '.$room->min.'$!'
		];

		if(floatval($r->get('amount')) > ($room->max - $userbets->sum('sum'))) return [
			'success' => false,
			'msg' => 'You can`t bet more '.$room->max.'$ per game!'
		];

		DB::beginTransaction();

		try {
				
			if(is_null($user)) {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'Unknown error!'
				];
			}

			$balance = ($r->get('balance') == 'balance') ? 'balance' : 'bonus';
			$betType = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->where('balance', '!=', $balance)->count();
			
			if($betType > 0) {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'You have already placed a bet with '. (($balance == 'balance') ? 'bonus' : 'money') .' score!'
				];
			}
			if($balance != 'balance' && $balance != 'bonus') {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'Unable to determine your balance type!'
				];
			}

			if($user[$balance] < floatval($r->get('amount'))) {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'Insufficient funds!'
				];
			}

			DB::table('users')->where('id', $user->id)->update([
				$balance => $user[$balance]-floatval($r->get('amount'))
			]);

			$bet = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->first();
			DB::table('jackpot_bets')->insert([
				'room' => $room->name,
				'game_id' => $game->id,
				'user_id' => $user->id,
				'sum' => floatval($r->get('amount')),
				'color' => ($bet) ? $bet->color : $this->getRandomColor(),
				'balance' => $r->get('balance')
			]);

			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return [
				'success' => false,
				'msg' => 'Unknown error!'
			];
		}

		$user = User::where('id', $this->user->id)->first();
		if($balance == 'balance') $this->redis->publish('updateBalance', json_encode([
			'unique_id' => $user->unique_id,
			'balance' 	=> round($user->balance, 2)
		]));
		if($balance == 'bonus') $this->redis->publish('updateBonus', json_encode([
			'unique_id' => $user->unique_id,
			'bonus' 	=> round($user->bonus, 2)
		]));

		$data = $this->parseJackpotGame($game->id);
		if($data['success'] && count($data['data']['chances']) >= 2 && $game->status < 1) {
			Jackpot::where('id', $game->id)->update([
				'status' => 1
			]);
			$this->redis->publish('jackpot.timer', json_encode([
				'room' => $room->name,
				'time' => $room->time,
				'game' => $game->id
			]));
		}
			
		$this->redis->publish('jackpot', json_encode([
			'type' => 'update',
			'room' => $room->name,
			'data' => $data
		]));

		return [
			'success' => true,
			'msg' => 'Your bet has entered the game!'
		];
	}

	public function adminBet(Request $r) {
		$room = Rooms::where('name', $r->get('room'))->first();
		$user = User::where('user_id', $r->get('user'))->first();
		if(is_null($room)) return [
			'success' => false,
			'msg' => 'Could not find the room you want to bet in!'
		];
		if(is_null($user)) return [
			'success' => false,
			'msg' => 'User could not be found!'
		];

		$game = Jackpot::where('room', $room->name)->orderBy('id', 'desc')->first();
		if(is_null($game)) return [
			'success' => false,
			'msg' => 'Failed to find a game in the room '.$room->name
		];

		if($game->status > 1) return [
			'success' => false,
			'msg' => 'Bets in this game are closed!'
		];

		DB::beginTransaction();

		try {
			$userbets = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->get();

			if($userbets->count() >= $room->bets) {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'You can`t do more '.$room->bets.' bets per game!'
				];
			}
			
			if(floatval($r->get('amount')) < $room->min) {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'Minimum bet amount '.$room->min.'$!'
				];
			}
			
			if(floatval($r->get('amount')) > ($room->max - $userbets->sum('sum'))) {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'You can`t bet more '.$room->max.'$ per game!'
				];
			}
				
			if(is_null($user)) {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'Unknown error!'
				];
			}

			$balance = ($r->get('balance') == 'balance') ? 'balance' : 'bonus';
			$betType = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->where('balance', '!=', $balance)->count();
			
			if($betType > 0) {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'You have already placed a bet with '. (($balance == 'balance') ? 'bonus' : 'money') .' score!'
				];
			}
			if($balance != 'balance' && $balance != 'bonus') {
				DB::rollback();
				return [
					'success' => false,
					'msg' => 'Unable to determine your balance type!'
				];
			}

			$bet = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->first();
			DB::table('jackpot_bets')->insert([
				'room' => $room->name,
				'game_id' => $game->id,
				'user_id' => $user->id,
				'sum' => floatval($r->get('amount')),
				'color' => ($bet) ? $bet->color : $this->getRandomColor(),
				'balance' => $r->get('balance'),
				'fake' => 1
			]);

			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return [
				'success' => false,
				'msg' => 'Unknown error!'
			];
		}

		$data = $this->parseJackpotGame($game->id);
		if($data['success'] && count($data['data']['chances']) >= 2 && $game->status < 1) {
			Jackpot::where('id', $game->id)->update([
				'status' => 1
			]);
			$this->redis->publish('jackpot.timer', json_encode([
				'room' => $room->name,
				'time' => $room->time,
				'game' => $game->id
			]));
		}
			
		$this->redis->publish('jackpot', json_encode([
			'type' => 'update',
			'room' => $room->name,
			'data' => $data
		]));

		return [
			'success' => true,
			'msg' => 'Your bet has entered the game!'
		];
	}
	
	public function addBetFake() {
		$room = Rooms::where('status', 0)->inRandomOrder()->first();
		$user = $this->getUser();
		
		$game = Jackpot::where('room', $room->name)->orderBy('id', 'desc')->first();
		
		if(is_null($room)) return [
			'success' => false,
            'fake' => $this->settings->fakebets,
			'msg' => '[ROOM #'.$room->name.'] Could not find the room you want to bet in!'
		];
		if(is_null($user)) return [
			'success' => false,
            'fake' => $this->settings->fakebets,
			'msg' => '[ROOM #'.$room->name.'] User could not be found!'
		];
		if(is_null($game)) return [
			'success' => false,
            'fake' => $this->settings->fakebets,
			'msg' => '[ROOM #'.$room->name.'] Failed to find a game!'
		];

		if($game->status > 1) return [
			'success' => false,
            'fake' => $this->settings->fakebets,
			'msg' => '[ROOM #'.$room->name.'] Bets in this game are closed!'
		];
		
		$sum = $room->min+mt_rand($this->settings->fake_min_bet * 2, $this->settings->fake_max_bet * 2) / 2;

		DB::beginTransaction();

		try {
			$userbets = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->get();

			if($userbets->count() >= $room->bets) {
				DB::rollback();
				return [
					'success' => false,
            		'fake' => $this->settings->fakebets,
					'msg' => '[ROOM #'.$room->name.'] You can`t do more '.$room->bets.' bets per game!'
				];
			}
			
			if(floatval($sum) < $room->min) {
				DB::rollback();
				return [
					'success' => false,
            		'fake' => $this->settings->fakebets,
					'msg' => '[ROOM #'.$room->name.'] Minimum bet amount '.$room->min.'$!'
				];
			}
			
			if(floatval($sum) > ($room->max - $userbets->sum('sum'))) {
				DB::rollback();
				return [
					'success' => false,
            		'fake' => $this->settings->fakebets,
					'msg' => '[ROOM #'.$room->name.'] You can`t bet more '.$room->max.'$ per game!'
				];
			}
				
			if(is_null($user)) {
				DB::rollback();
				return [
					'success' => false,
            		'fake' => $this->settings->fakebets,
					'msg' => '[ROOM #'.$room->name.'] Unknown error!'
				];
			}

			$bl = ['balance', 'bonus'];
			$bl_true = $bl[array_rand($bl)];
			
			$balance = ($bl_true == 'balance') ? 'balance' : 'bonus';
			$betType = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->where('balance', '!=', $balance)->count();
			
			if($betType > 0) {
				DB::rollback();
				return [
					'success' => false,
            		'fake' => $this->settings->fakebets,
					'msg' => '[ROOM #'.$room->name.'] You have already placed a bet with '. (($balance == 'balance') ? 'bonus' : 'money') .' score!'
				];
			}
			if($balance != 'balance' && $balance != 'bonus') {
				DB::rollback();
				return [
					'success' => false,
            		'fake' => $this->settings->fakebets,
					'msg' => '[ROOM #'.$room->name.'] Unable to determine your balance type!'
				];
			}

			$bet = JackpotBets::where('game_id', $game->id)->where('user_id', $user->id)->first();
			DB::table('jackpot_bets')->insert([
				'room' => $room->name,
				'game_id' => $game->id,
				'user_id' => $user->id,
				'sum' => floatval($sum),
				'color' => ($bet) ? $bet->color : $this->getRandomColor(),
				'balance' => $balance,
				'fake' => 1
			]);

			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return [
				'success' => false,
				'fake' => $this->settings->fakebets,
				'msg' => '[ROOM #'.$room->name.'] Unknown error!'
			];
		}

		$data = $this->parseJackpotGame($game->id);
		if($data['success'] && count($data['data']['chances']) >= 2 && $game->status < 1) {
			Jackpot::where('id', $game->id)->update([
				'status' => 1
			]);
			$this->redis->publish('jackpot.timer', json_encode([
				'room' => $room->name,
				'time' => $room->time,
				'game' => $game->id
			]));
		}
			
		$this->redis->publish('jackpot', json_encode([
			'type' => 'update',
			'room' => $room->name,
			'data' => $data
		]));

		return [
			'success' => true,
			'fake' => $this->settings->fakebets,
			'msg' => '[ROOM #'.$room->name.'] Your bet has entered the game!'
		];
	}

	public function getSlider(Request $r) {
		$room = Rooms::where('name', $r->get('room'))->first();
		if(!$room) return [
			'success' => false,
			'msg' => 'Could not find room '.$r->get('room')
		];

		$game = Jackpot::where('room', $room->name)->orderBy('id', 'desc')->first();
		if(!$game) return [
			'success' => false,
			'msg' => 'Failed to find a game in the room '.$room->name
		];

		if($game->id != $r->get('game')) return [
			'success' => false,
			'msg' => 'Found game #'.$game->id.'. Not constitute #'.$r->get('game')
		];

		$data = $this->parseJackpotGame($game->id);
		if(!$data['success']) return [
			'success' => false,
			'msg' => 'Unknown error! Repeat...',
			'retry' => true
		];

		$data = $data['data'];
		
		$winnerBet = null;
		if(!$game->winner_id) {
			$winnerTicket = ($game->winner_ticket > 0) ? $game->winner_ticket : mt_rand(0, $data['bets'][0]['bet']['to']);
		} else {
			$winner2 = [];
			foreach($data['bets'] as $key => $d) {
				if($game->winner_id == $data['bets'][$key]['user']['user_id']) $winner2[] = $d['bet'];
			}
			$winner2 = $winner2[array_rand($winner2)];
			$winnerTicket = mt_rand($winner2['from'], $winner2['to']);
		}
		foreach($data['bets'] as $bet) if($bet['bet']['from'] <= $winnerTicket && $bet['bet']['to'] >= $winnerTicket) $winnerBet = $bet;
		if(is_null($winnerBet)) return [
			'success' => false,
			'msg' => 'Could not find the winning bet! Repeat..',
			'retry' => true
		];

		DB::beginTransaction();
		try {
			DB::table('jackpot')->where('id', $game->id)->update([
				'winner_id' => $winnerBet['user']['user_id'],
				'winner_ticket' => $winnerTicket,
				'status' => 2
			]);
			
			$winner_money = $this->sendMoney($winnerBet['user']['user_id'], $game->id);

			DB::commit();
		} catch(Exception $e) {
			DB::rollback();
			return [
				'success' => false,
				'msg' => 'Unknown error! Repeat...',
				'retry' => true
			];
		}
		
		$rotate = [];
		foreach($data['chances'] as $key => $d) {
			if($winnerBet['user']['user_id'] == $data['chances'][$key]['user']['id']) $rotate = $d['circle'];
		}
		$cords = null;
		$center = $rotate['end']-$rotate['start'];
		if(floor($center) > 1) $cords = mt_rand(floor($rotate['start']), floor($rotate['end']));
		if(floor($center) < 1) $cords = $rotate['start'] + ($center/2);

		$this->redis->publish('jackpot', json_encode([
			'type' => 'slider',
			'room' => $room->name,
			'data' => [
				'cords' => 1440+$cords,
				'winner_id' => $winnerBet['user']['id'],
				'winner_name' => $winnerBet['user']['username'],
				'winner_avatar' => $winnerBet['user']['avatar'],
				'winner_balance' => $winner_money[0],
				'winner_bonus' => $winner_money[1],
				'ticket' => $winnerTicket
			]
		]));

		return [
			'success' => true
		];
	}
	
	private function sendMoney($user_id, $game_id) {
		$game = Jackpot::where('id', $game_id)->first();
		$bet = JackpotBets::where('game_id', $game->id)->where('user_id', $user_id)->first();
		
		$money_bets = JackpotBets::where('game_id', $game->id)->where('balance', 'balance')->sum('sum');
		$bonus_bets = JackpotBets::where('game_id', $game->id)->where('balance', 'bonus')->sum('sum');
		
		$w_bet_money = JackpotBets::where('game_id', $game->id)->where('user_id', $game->winner_id)->where('balance', 'balance')->sum('sum');
		$w_bet_bonus = JackpotBets::where('game_id', $game->id)->where('user_id', $game->winner_id)->where('balance', 'bonus')->sum('sum');
		
        $sum_money = round($w_bet_money + (($money_bets - $w_bet_money) - ($money_bets - $w_bet_money)/100*$this->settings->jackpot_commission), 2);
        $sum_bonus = round($w_bet_bonus + (($bonus_bets - $w_bet_bonus) - ($bonus_bets - $w_bet_bonus)/100*$this->settings->jackpot_commission), 2);
		
		$comission = round(($money_bets - $w_bet_money)/100*$this->settings->jackpot_commission, 2);
		
		$sum = [$sum_money, $sum_bonus];
		$user = User::where(['id' => $user_id, 'fake' => 0])->first();
		
		if(!is_null($user)) {
			if($bet->balance == 'balance') {
				$user->balance += $sum_money;
				$user->bonus += $sum_bonus;
				$user->requery += round(($money_bets - $w_bet_money) - ($money_bets - $w_bet_money)/100*$this->settings->jackpot_commission, 2);
				$user->save();

				if($user->ref_id) {
					$ref = User::where('unique_id', $user->ref_id)->first();
					if($ref) {
						$ref_sum = round((($money_bets - $w_bet_money) - ($money_bets - $w_bet_money)/100*$this->settings->jackpot_commission)/100*$this->settings->ref_perc, 2);
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
				
				if($comission > 0) Profit::create([
					'game' => 'jackpot',
					'sum' => $comission
				]);

				$this->redis->publish('updateBalanceAfter', json_encode([
					'unique_id'	=> $user->unique_id,
					'balance'	=> round($user->balance, 2),
					'timer'		=> 8
				]));

				$this->redis->publish('updateBonusAfter', json_encode([
					'unique_id'	=> $user->unique_id,
					'bonus' 	=> round($user->bonus, 2),
					'timer'		=> 8
				]));
			}
			
			if($bet->balance == 'bonus') {
				$user->bonus += $sum_money+$sum_bonus;
				$user->save();

				$this->redis->publish('updateBonusAfter', json_encode([
					'unique_id'	=> $user->unique_id,
					'bonus'		=> round($user->bonus, 2),
					'timer'		=> 8
				]));
			}
		} else {
			$sum = [$sum_money, $sum_bonus];
			if($money_bets > 0) Profit::create([
				'game' => 'jackpot',
				'sum' => $money_bets
			]);
		}
		
		JackpotBets::where(['game_id' => $game->id, 'user_id' => $bet->user_id])->update([
			'win' => 1
		]);
		
        $game->winner_balance = $sum_money;
        $game->winner_bonus = $sum_bonus;
        $game->status = 3;
		$game->save();
		
		return $sum;
	}
	
	private function getRandomColor() {
        $color = str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
		return $color;
	}
	
	public function getGame(Request $r) {
		$room = $r->get('room');
		$option = Rooms::where('name', $room)->first();
		$game = Jackpot::where('room', $room)->orderBy('id', 'desc')->first();

        return response()->json([
            'room'      => $room,
            'game'      => $game->id,
            'time'      => $option->time,
            'status'    => $game->status
        ]);
    }
	
	public function history() {
		return view('pages.jackpotHistory');
    }
	
	public function initHistory(Request $r) {
		$room = Rooms::where('name', $r->get('room'))->first();
		if(is_null($room)) return [
			'success' => false
		];
		
        $games = Jackpot::where('room', $room->name)->where('status', 3)->where('updated_at', '>=', Carbon::today())->orderBy('game_id', 'desc')->limit(30)->get();
		
		$history = [];
        foreach($games as $game) {
            $winner = User::where('id', $game->winner_id)->first();
			$price = JackpotBets::where('game_id', $game->id)->sum('sum');
			$bet = JackpotBets::where('game_id', $game->id)->where('user_id', $winner->id)->sum('sum');
			$chance = round($bet/$price*100, 2);
			if(isset($winner)) {
				$history[] = [
					'game_id' => $game->game_id,
					'winner_id' => $winner->unique_id,
					'winner_name' => $winner->username,
					'winner_avatar' => $winner->avatar,
					'winner_chance' => $chance,
					'winner_balance' => $game->winner_balance,
					'winner_bonus' => $game->winner_bonus,
					'winner_ticket' => $game->winner_ticket,
					'hash' => $game->hash
				];
			}
        }
		
		return ['success' => true, 'history' => $history];
    }
	
	public function gotThis(Request $r) {
		$game_id = $r->get('game_id');
		$game = Jackpot::where('id', $game_id)->first();
		$userid = $r->get('user_id');
		$user = User::where('id', $userid)->first();
		$bets = JackpotBets::where(['game_id' => $game_id, 'user_id' => $user->id])->first();
		
		if(!$game->id) return [
			'msg'       => 'Failed to get game number!',
			'type'      => 'error'
		];
		
		if($game->status == 3) return [
			'msg'       => 'The game has already begun!',
			'type'      => 'error'
		];
		
		if(!$userid) return [
			'msg'       => 'Failed to get player ID!',
			'type'      => 'error'
		];
		
		if(is_null($bets)) return [
			'msg'       => 'This player did not bet!',
			'type'      => 'error'
		];
		
		Jackpot::where('id', $game_id)->update([
			'winner_id' => $user->id
		]);
		
		return [
			'msg'       => 'You pick player '.$user->username.' in the game!',
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