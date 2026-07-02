<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    //Menampilkan halaman dashboard admin
    public function index()
    {
        return view('admin.dashboard');
    }

    // Halaman login admin
    public function login()
    {
        return view('admin.login');
    }

    //Proses login admin
    public function authenticate(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        // Login sederhana sementara
        if ($username == 'admin' && $password == 'admin123') {

            session([
                'admin_login' => true,
                'admin_name' => 'Administrator'
            ]);

            return redirect('/admin');
        }

        return back()->with('error', 'Username atau password salah');
    }

    //Logout admin
    public function logout()
    {
        session()->forget('admin_login');

        return redirect('/login');
    }
}