<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class CoinFlip extends Model {
    protected $table = 'flip';
    
    protected $fillable = ['heads', 'heads_from', 'heads_to', 'tails', 'tails_from', 'tails_to', 'bank', 'winner_id', 'winner_ticket', 'winner_sum', 'balType', 'hash', 'status'];
    
    protected $hidden = ['created_at', 'updated_at'];
}
