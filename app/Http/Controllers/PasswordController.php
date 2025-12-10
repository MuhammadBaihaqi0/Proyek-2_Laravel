<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    // Menampilkan halaman form ubah password
    public function edit()
    {
        return view('auth.change-password');
    }

    // Memproses perubahan password
    public function update(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed', // 'confirmed' akan mengecek field password_confirmation
        ]);

        // 2. Cek apakah password lama sesuai dengan yang di database
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan salah.']);
        }

        // 3. Update Password
        Auth::user()->update([
            'password' => Hash::make($request->password)
        ]);

        // 4. Redirect dengan pesan sukses
        return back()->with('success', 'Password berhasil diubah!');
    }
}