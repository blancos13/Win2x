<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class WheelBets extends Model {
	
    protected $table = 'wheel_bets';
    
    protected $fillable = ['user_id', 'game_id', 'price', 'color', 'win', 'balance', 'win_sum', 'fake'];
    
    protected $hidden = ['created_at', 'updated_at'];
}
