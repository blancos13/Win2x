<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $table = 'users';

    protected $fillable = [
        'unique_id', 'username', 'avatar', 'user_id', 'password', 'email', 'balance', 'bonus', 'requery', 'ip', 'is_admin', 'is_moder', 'is_youtuber', 'fake', 'time', 'banchat', 'banchat_reason', 'ban', 'ban_reason', 'link_trans', 'link_reg', 'ref_id', 'ref_money', 'ref_money_all'
    ];

    protected $hidden = ['remember_token'];
	
	static function getUser($id) {
		$user = self::select('username', 'avatar', 'unique_id')->where('id', $id)->first();
		return $user;
	}
	
	static function findRef($id) {
		$user = self::select('id', 'username', 'avatar', 'unique_id')->where('unique_id', $id)->first();
		return $user;
	}
}
