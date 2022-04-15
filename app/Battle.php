<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
	const STATUS_NOT_STARTED = 0;
    const STATUS_PLAYING = 1;
    const STATUS_PRE_FINISH = 2;
    const STATUS_FINISHED = 3;
	
    protected $table = 'battle';
    
    protected $fillable = ['price', 'commission', 'winner_team', 'winner_factor', 'winner_ticket', 'status', 'hash'];
    
    protected $hidden = ['updated_at', 'created_at', 'finished_at'];
	
    public function bets()
    {
        return $this->hasMany('App\BattleBets');
    }
	
	public function users()
    {
        return self::join('battle_bets', 'battle.id', '=', 'battle_bets.game_id')
            ->join('users', 'battle_bets.user_id', '=', 'users.id')
            ->where('battle.id', $this->id)
            ->groupBy('users.id')
            ->select('users.avatar')
            ->get();
    }
}
