<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Bomb extends Model {
    protected $table = 'bomb';
    
    protected $fillable = ['user1', 'user2', 'color', 'bank', 'winner_id', 'winner_sum', 'balType', 'hash', 'status'];
    
    protected $hidden = ['created_at', 'updated_at'];
}
