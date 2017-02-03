<?php

namespace Modules\Users\Http\Controllers;

use Validator;
use SweetAlert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nwidart\Modules\Routing\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * Class AuthController
 * @package Modules\Users\Http\Controllers
 */
class AuthController extends Controller {

    use AuthenticatesUsers;


    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->redirectTo = env('URI_AFTER_LOGIN', '/dashboard');
    }


    public function login()
    {
        return view('users::auth.login');
    }


    public function postLogin(Request $request)
    {
        $this->validateLogin($request);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }
        $this->incrementLoginAttempts($request);
        SweetAlert::error($this->getFailedLoginMessage(), 'Ups!')->autoclose(3500);
        return redirect($this->loginPath());
    }


    public function authenticated($request, $user)
    {
        if(!$user->active) {
            Auth::logout();
            SweetAlert::error('El usuario esta inactivo, por favor contacta al administrador del sistema', 'Ups!')->autoclose(3500);
            return redirect($this->loginPath());
        }
        $u = Auth::user();
        $u->last_login = Carbon::now();
        $u->save();
        return redirect()->intended($this->redirectPath());
    }

    public function getLogout()
    {
        Auth::logout();
        if(Auth::check()){
            Auth::logout();
        }
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }
	
}