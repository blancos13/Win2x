<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Settings;
use App\Profit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
	protected function validator(array $data)
    {
		$data['ip'] = request()->ip();
		
        return Validator::make($data, [
			'ip' => 'required|ip|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'user_id' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'g-recaptcha-response' => 'required|captcha',
		],
        [
            'ip.unique' => 'This IP is already registered in the system!',
            'g-recaptcha-response.required' => 'You didn`t pass the test. I`m not a robot!',
            'g-recaptcha-response.captcha'  => 'You didn`t pass the test. I`m not a robot!',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
		$settings = Settings::where('id', 1)->first();
		$ref_id = Cookie::get('ref');
		$ref = User::where('unique_id', $ref_id)->first();
		$plus = 0;
		
		if(!is_null($ref)) {
			$ref->link_reg += 1;
			$ref->save();
			$plus = $settings->ref_sum;
			if($plus > 0) Profit::create([
				'game' => 'ref',
				'sum' => -$plus
			]);
		} else $ref_id = null;
        return User::create([
			'unique_id' => bin2hex(random_bytes(6)),
            'user_id' => $data['user_id'],
            'password' => Hash::make($data['password']),
            'email' => $data['email'],
            'username' => $data['username'],
            'avatar' => '/img/no_avatar.jpg',
			'ip' => request()->ip(),
			'balance' => $plus,
			'ref_id' => $ref_id
        ]);
    }
}
