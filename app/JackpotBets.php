<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use DB;

class JackpotBets extends Model
{
    protected $table = 'jackpot_bets';
    
    protected $fillable = ['room', 'game_id', 'user_id', 'sum', 'color', 'balance', 'win', 'fake'];
    
    protected $hidden = ['created_at', 'updated_at'];
}
