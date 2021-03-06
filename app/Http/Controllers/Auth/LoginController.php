<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
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
    protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected function credentials(Request $request)
    {
        $credential = $request->only($this->username(), 'password');
        $credential["status"] = 1;
        return $credential;
    }
    protected function authenticated(Request $request, $user)
    {
        session()->flash('message', "Welcome! ".$user->name.(($user->position)?' ('.$user->position.')':''). ".");
        if($user->position == "Data Manager") {
            $this->redirectTo = route("DataEntry.Home");
        }
        
    }
}
