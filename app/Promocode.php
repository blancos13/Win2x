<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model {

    protected $table = 'promocode';

    protected $fillable = ['user_id', 'code', 'limit', 'amount', 'count_use', 'type'];

    protected $hidden = ['created_at', 'updated_at'];
}