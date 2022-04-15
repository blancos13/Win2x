<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
	
    public function username() {
        return 'user_id';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $r)
    {
        $this->middleware('guest')->except('logout');
    }
	
	protected function validateLogin(Request $r)
    {
        $this->validate($r, [
            'g-recaptcha-response' => 'required|captcha',
        ],
        [
            'g-recaptcha-response.required' => 'You didn`t pass the test. I`m not a robot!',
            'g-recaptcha-response.captcha'  => 'You didn`t pass the test. I`m not a robot!',
        ]);
    }
	
	public function logout()
    {
        $user = Auth::user();
        Auth::logout();
        Session::flush();
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }
}
