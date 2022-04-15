<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Jackpot extends Model {
	
	const STATUS_NOT_STARTED = 0;
    const STATUS_PLAYING = 1;
    const STATUS_PRE_FINISH = 2;
    const STATUS_FINISHED = 3;
	
    protected $table = 'jackpot';
    
    protected $fillable = ['room', 'game_id', 'winner_id', 'winner_ticket', 'winner_balance', 'winner_bonus', 'hash', 'status'];
    
    protected $hidden = ['created_at', 'updated_at'];
}
