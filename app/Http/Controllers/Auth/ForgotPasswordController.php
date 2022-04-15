<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
	
	protected function validateEmail(Request $r)
	{
		$this->validate($r, [
			'email' => 'required|email',
            'g-recaptcha-response' => 'required|captcha',
        ],
        [
            'g-recaptcha-response.required' => 'You didn`t pass the test. I`m not a robot!',
            'g-recaptcha-response.captcha'  => 'You didn`t pass the test. I`m not a robot!',
        ]);
	}
}
