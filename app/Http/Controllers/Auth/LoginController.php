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
        $this->middleware('guest')->except(['logout', 'autoLogin']);
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
            'status' => '1'
        ], true)) {
            return response()->json([
                'success' => 1,
                'redirect' => session()->pull('url.intended', url('/'))
            ], 200);
        } else {
            return response()->json(['success' => '0'], 401);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['success' => 1], 200);
    }

    public function autoLogin(Request $request)
    {
        $token = $request->query('token');
        $redirectUrl = $request->query('redirect', '/');

        if (!$token) {
            return redirect('/login')->with('error', 'Token auto-login tidak ditemukan.');
        }

        try {
            $secret = env('SSO_SECRET_KEY', 'mybas_sso_secret_key_9988');
            $decoded = base64_decode($token);
            if (strpos($decoded, '::') !== false) {
                list($encrypted_data, $base64_iv) = explode('::', $decoded, 2);
                $iv = base64_decode($base64_iv);
                $decrypted = openssl_decrypt($encrypted_data, 'aes-256-cbc', $secret, 0, $iv);
                $data = json_decode($decrypted, true);

                if ($data && isset($data['username']) && isset($data['expires_at'])) {
                    // Cek kadaluwarsa token (misal: 5 menit)
                    if (time() <= $data['expires_at']) {
                        $user = \App\User::where('username', $data['username'])->where('status', '1')->first();
                        if ($user) {
                            Auth::login($user, true);
                            return redirect($redirectUrl);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("AutoLogin failed: " . $e->getMessage());
        }

        return redirect('/login')->with('error', 'Token auto-login tidak valid atau kadaluwarsa.');
    }

    public static function generateSsoToken($username)
    {
        $secret = env('SSO_SECRET_KEY', 'mybas_sso_secret_key_9988');
        $data = [
            'username' => $username,
            'expires_at' => time() + 300 // 5 menit
        ];
        $plain = json_encode($data);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($plain, 'aes-256-cbc', $secret, 0, $iv);
        
        return base64_encode($encrypted . '::' . base64_encode($iv));
    }
}
