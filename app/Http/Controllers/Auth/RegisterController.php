<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use App\Mail\VerifyEmail;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function show()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:Owner,Admin,Produksi,Keuangan',
        ]);

        // Enforce single Owner - TEMPORARILY DISABLED for registration
        // if ($data['role'] === 'Owner' && User::where('Role', 'Owner')->exists()) {
        //     return back()->withErrors(['role' => 'Sudah ada Owner terdaftar. Hanya satu Owner yang diperbolehkan.'])->withInput();
        // }

        // Create user using existing DB column names
        $token = Str::random(64);

        // Build create array based on whether the verification columns exist in DB
        $createData = [
            'Username' => $data['email'],
            'Password' => Hash::make($data['password']),
            'NamaLengkap' => $data['name'],
            'Email' => $data['email'],
            'Role' => $data['role'],
        ];

        // Only include verification fields if the columns actually exist
        if (Schema::hasColumn('users', 'EmailVerificationToken') && Schema::hasColumn('users', 'EmailVerifiedAt')) {
            $createData['EmailVerificationToken'] = $token;
            $createData['EmailVerifiedAt'] = null;
        }

        $user = User::create($createData);

        // If verification columns exist, send verification email
        if (Schema::hasColumn('users', 'EmailVerificationToken')) {
            try {
                Mail::to($user->Email)->send(new VerifyEmail($user, $token));
            } catch (\Throwable $e) {
                return redirect()->route('welcome')->with('success', 'Registrasi berhasil, tetapi pengiriman email verifikasi gagal. Hubungi admin.');
            }

            return redirect()->route('welcome')->with('success', 'Registrasi berhasil. Silakan periksa email Anda untuk tautan verifikasi.');
        }

        return redirect()->route('welcome')->with('success', 'Registrasi berhasil.');
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        // Require password confirmation
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, $user->Password)) {
            return back()->withErrors(['password' => 'Password tidak sesuai.']);
        }

        // Get all customers associated with this user
        $customers = Customer::where('UserID', $user->UserID)->get();
        
        // Delete all orders and related data for each customer
        foreach ($customers as $customer) {
            $orders = Order::where('CustomerID', $customer->CustomerID)->get();
            
            foreach ($orders as $order) {
                // Delete order details
                $order->orderDetails()->delete();
                
                // Delete custom details
                $order->customDetail()->delete();
                
                // Delete production records
                $order->produksi()->delete();
                
                // Delete transactions
                $order->transactions()->delete();
                
                // Delete the order itself
                $order->delete();
            }
            
            // Delete the customer
            $customer->delete();
        }
        
        // Delete the user account
        $user->delete();
        
        Auth::logout();
        
        return redirect()->route('welcome')->with('success', 'Akun Anda telah dihapus secara permanen.');
    }
}
