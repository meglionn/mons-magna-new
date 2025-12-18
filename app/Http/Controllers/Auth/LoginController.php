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

        // PENTING: Log the user in menggunakan Auth facade
        Auth::login($user);
        
        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Redirect based on user role
        switch ($user->Role) {
            case 'Keuangan':
                return redirect()->route('financial');
            case 'Produksi':
                return redirect()->route('order');
            case 'Admin':
            case 'Owner':
                return redirect()->route('order');
            default:
                return redirect()->route('order');
        }
    }
}