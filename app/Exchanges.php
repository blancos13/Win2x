<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Exchanges extends Model {

    protected $table = 'exchanges';

    protected $fillable = ['user_id', 'sum'];

    protected $hidden = ['created_at', 'updated_at'];

}