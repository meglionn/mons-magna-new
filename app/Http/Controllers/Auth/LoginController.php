<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('Email', $data['email'])
            ->orWhere('Username', $data['email'])
            ->first();

        if (!$user || !Hash::check($data['password'], $user->Password)) {
            return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
        }

        // Log the user in using the user instance
        Auth::login($user);

        return redirect()->intended(route('order'));
    }
}
