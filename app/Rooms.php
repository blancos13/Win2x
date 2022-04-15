<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    protected $table = 'rooms';
    
    protected $fillable = ['name', 'title', 'min', 'max', 'bets', 'time', 'status'];
    
}
