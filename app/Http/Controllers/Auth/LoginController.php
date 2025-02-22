<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function attemptLogin(Request $request)
    {
        // Get credentials from request
        $credentials = $this->credentials($request);

        // Attempt login with additional condition
        $user = \App\Models\User::where('email', $credentials['email'])->where('client_id', "!=", null)->first();

        if (!$user) {
            return false;
        }

        // Check if client_id is not null
        if (is_null($user->client_id)) {
            return false;
        }

        // Proceed with login attempt
        return Auth::attempt($credentials, $request->filled('remember'));
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Credentials do not match our records.']);
    }

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = url()->previous();
        $this->middleware('guest', ['except' => 'logout']);
    }
}
