<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PromoLog extends Model {

    protected $table = 'promo_log';

    protected $fillable = ['user_id', 'sum', 'code', 'type'];

    protected $hidden = ['created_at', 'updated_at'];
}