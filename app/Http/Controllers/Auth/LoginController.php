<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return $this->authenticated($request, Auth::user())
                    ?: redirect()->intended('/login');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Redirect users after login based on role.
     */
    protected function authenticated(Request $request, $user)
    {
        // 5 role sesuai use case skripsi
        switch ($user->role) {
            case 'qc':
                return redirect('/qc/dashboard');
            case 'gudang':
                return redirect('/gudang/dashboard');
            case 'keuangan':
                return redirect('/keuangan/dashboard');
            case 'kasir':
                return redirect('/kasir/dashboard');
            case 'driver':
                return redirect('/driver/dashboard');
            default:
                return redirect('/login');
        }
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
