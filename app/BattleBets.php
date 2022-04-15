<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class BattleBets extends Model
{
    protected $table = 'battle_bets';
    
    protected $hidden = ['updated_at', 'created_at'];
	
	public function user() {
        return $this->belongsTo('App\User');
    }

    public function game() {
        return $this->belongsTo('App\Battle');
    }
}
