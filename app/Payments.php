<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model {
	
    protected $table = 'payments';
	
	protected $fillable = ['user_id', 'secret', 'order_id', 'sum', 'status', 'system'];
    
    protected $hidden = ['created_at', 'updated_at'];
    
}