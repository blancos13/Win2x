<?php namespace App;

use App\Payments;
use App\Settings;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Profit extends Model {
	
    protected $table = 'profit';
	
	protected $fillable = ['game', 'sum'];
    
    protected $hidden = ['created_at', 'updated_at'];

    static function calc() {
		$settings = Settings::first();
        $today = self::where('created_at', '>=', Carbon::today())->sum('sum');
		$need = Payments::where('status', 1)->where('updated_at', '>=', Carbon::today())->sum('sum')*$settings->profit_koef;
		
        return [
			'now' => floatval($today),
			'need' => floatval($need)
		];
    }
    
}