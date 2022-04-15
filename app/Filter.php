<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model {
	
    protected $table = 'filter';
    
    protected $fillable = ['word'];
    
    protected $hidden = ['created_at', 'updated_at'];
}
