<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // Cari user dengan raw query
        $user = DB::select("
            SELECT * FROM user 
            WHERE email = ?
        ", [$request->email]);

        if (empty($user)) {
            return redirect()->route('login')
                ->with('error', 'Email atau password salah.');
        }

        $user = $user[0];

        // Verify password dengan MD5
        $inputPasswordMd5 = md5($request->password);
        if ($inputPasswordMd5 !== $user->password) {
            return redirect()->route('login')
                ->with('error', 'Email atau password salah.');
        }

        // Login user manually
        Auth::loginUsingId($user->id);

        // Redirect berdasarkan role
        if ($user->role === 'gudang') {
            return redirect()->route('gudang.dashboard')
                ->with('success', 'Selamat datang di Dashboard Gudang!');
        } elseif ($user->role === 'dapur') {
            return redirect()->route('dapur.dashboard')
                ->with('success', 'Selamat datang di Dashboard Dapur!');
        }
        
        // Jika role tidak dikenali
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Role pengguna tidak valid.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Berhasil logout.');
    }
}
