<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Shortcut;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function username()
    {
        return 'username';
    }

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $shortcuts = Shortcut::where('status', '1')->orderBy('title', 'asc')->get();
        return view('auth.login', [
            'shortcuts' => $shortcuts
        ]);
    }

    public function authenticate(Request $request)
    {
        $validator = $request->validate([
            'nik' => 'required|max:12',
            'password' => 'required|max:255'
        ]);

        // dd('hmm');

        if (Auth::attempt([
            'username' => $request['nik'],
            'password' => $request['password'],
            'status' => '1'], true)) {
            return response()->json(['success' => '1'], 200);
        }else{
            return response()->json(['success' => '0'], 401);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['success' => 1], 200);
    }
}
