<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Import Model User
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Cek apakah user adalah Admin (Misal: User ID 1 adalah admin)
        if (Auth::id() !== 1) {
            // Jika bukan admin, lempar ke halaman dashboard biasa atau error 403
            return redirect()->route('dashboard')->with('error', 'Anda bukan Admin!');
        }

        // 2. Ambil Semua Data User, urutkan dari yang terbaru
        $users = User::orderBy('created_at', 'desc')->get();

        // 3. Hitung Total User
        $total_users = $users->count();

        // 4. Kirim ke View
        return view('admin.dashboard', compact('users', 'total_users'));
    }
}