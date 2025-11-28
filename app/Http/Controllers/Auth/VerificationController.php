<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function verify($token)
    {
        $user = User::where('EmailVerificationToken', $token)->first();

        if (!$user) {
            return redirect()->route('welcome')->with('error', 'Token verifikasi tidak valid atau sudah digunakan.');
        }

        $user->EmailVerifiedAt = now();
        $user->EmailVerificationToken = null;
        $user->save();

        // Optionally, log the user in after verification
        Auth::login($user);

        return redirect()->route('welcome')->with('success', 'Email berhasil diverifikasi. Anda telah masuk.');
    }
}
