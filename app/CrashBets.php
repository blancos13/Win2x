<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrashBets extends Model 
{
    protected $table = 'crash_bets';

    protected $fillable = ['user_id', 'round_id', 'price', 'withdraw', 'won', 'status', 'fake', 'balType'];
}