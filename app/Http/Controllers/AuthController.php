<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Check if already logged in
        if ($this->checkAuth()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->username;
        $password = md5($request->password); // Using MD5 for compatibility
        
        $admin = Admin::where('username', $username)
                     ->where('password', $password)
                     ->first();

        if ($admin) {
            $rememberMe = $request->has('remember');
            $token = $admin->generateAuthToken($rememberMe);
            
            // Store in session
            session([
                'admin' => $admin->username,
                'admin_id' => $admin->id,
                'admin_token' => $token,
            ]);

            return redirect()->route('dashboard')->with('success', 'Login successful!');
        }

        return back()->withErrors(['credentials' => 'Invalid credentials'])->withInput();
    }

    public function logout()
    {
        // Clear token from database
        if (session('admin_token')) {
            $admin = Admin::where('auth_token', session('admin_token'))->first();
            if ($admin) {
                $admin->clearAuthToken();
            }
        }

        // Clear session
        Session::flush();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }

    /**
     * Check if user is authenticated
     */
    private function checkAuth(): bool
    {
        if (!session('admin') || !session('admin_token')) {
            return false;
        }

        $admin = Admin::where('auth_token', session('admin_token'))->first();
        
        if ($admin && $admin->isTokenValid()) {
            return true;
        }

        Session::flush();
        return false;
    }
}