<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Wheel extends Model {
	
	const STATUS_NOT_STARTED = 0;
    const STATUS_PLAYING = 1;
    const STATUS_PRE_FINISH = 2;
    const STATUS_FINISHED = 3;
	
    protected $table = 'wheel';
    
    protected $fillable = ['winnder_color', 'price', 'status', 'hash', 'profit', 'ranked'];
    
    protected $hidden = ['created_at', 'updated_at'];
}
