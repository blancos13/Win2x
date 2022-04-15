<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';
	
	protected $fillable = ['domain', 'sitename', 'title', 'description', 'keywords', 'vk_url', 'vk_support_link', 'vk_service_key', 'censore_replace', 'chat_dep', 'fakebets', 'fake_min_bet', 'fake_max_bet', 'merchant_id', 'ipn_secret', 'public_key', 'private_key', 'coinpayments_fee', 'coinpayments_min', 'pm_uid', 'pm_pass', 'pm_usd_wallet', 'pm_passphrase', 'pm_fee', 'pm_min', 'profit_koef', 'jackpot_commission', 'wheel_timer', 'wheel_min_bet', 'wheel_max_bet', 'wheel_rotate', 'wheel_rotate2', 'wheel_rotate_start', 'crash_min_bet', 'crash_max_bet', 'crash_timer', 'battle_timer', 'battle_min_bet', 'battle_max_bet', 'battle_commission', 'dice_min_bet', 'dice_max_bet', 'flip_commission', 'flip_min_bet', 'flip_max_bet', 'bomb_commission', 'bomb_min_bet', 'bomb_max_bet', 'exchange_min', 'exchange_curs', 'ref_perc', 'ref_sum', 'min_ref_withdraw', 'min_dep', 'max_dep', 'min_dep_withdraw', 'bonus_group_time', 'max_active_ref'];
    
    public $timestamps = false;
    
}