<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function show_login_view()
    {
        return view('lab.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ], [
            'phone.required'    => 'رقم الهاتف مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        if (auth()->guard('lab')->attempt(['phone' => $request->phone, 'password' => $request->password, 'is_active' => true])) {
            return redirect()->route('lab.dashboard');
        }

        return redirect()->route('lab.showlogin')
            ->with('error', 'رقم الهاتف أو كلمة المرور غير صحيحة');
    }

    public function logout()
    {
        auth()->guard('lab')->logout();

        return redirect()->route('lab.showlogin');
    }
}
