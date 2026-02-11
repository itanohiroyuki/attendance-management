<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function adminLoginForm()
    {
        return view('admin.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (!auth()->user()->is_admin) {
                Auth::logout();
                return back()->withErrors(['email' => '管理者ではありません']);
            }

            return redirect('/admin/attendance/list');
        }

        return back()->withErrors(['email' => 'ログイン失敗']);
    }
}
