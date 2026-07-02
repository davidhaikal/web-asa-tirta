<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
            'email' => ['required','email'],
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
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'qc':
                return redirect('/qc/dashboard');
            case 'produksi':
                return redirect('/produksi');
            case 'gudang':
                return redirect('/gudang/dashboard');
            case 'keuangan':
                return redirect('/keuangan/dashboard');
            case 'driver':
                return redirect('/driver/dashboard');
            case 'marketing':
                return redirect('/dashboard');
            case 'kasir':
                return redirect('/kasir/dashboard');
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
