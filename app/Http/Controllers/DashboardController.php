<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Acara;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Setup Tanggal
        Carbon::setLocale('id');
        $tanggalHariIni = Carbon::now()->isoFormat('dddd, D MMMM Y');

        $user = Auth::user();

        // -------------------------------------------------------
        // LOGIKA FOTO PROFIL PINTAR 🧠
        // -------------------------------------------------------
        if ($user->avatar) {
            // Jika user SUDAH punya foto di database, pakai foto itu
            $avatar_path = asset('storage/' . $user->avatar);
        } else {
            // Jika BELUM mengganti (masih kosong), pakai Inisial Nama dari UI Avatars
            // Contoh: Baihaqi -> B,  Muhammad Baihaqi -> MB
            $avatar_path = 'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&background=random&color=ffffff&size=128&bold=true';
        }
        // -------------------------------------------------------

        // 2. Ambil Data Tugas (Pending)
        $all_tasks = $user->tugas()
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline', 'asc')
            ->get();

        // 3. Ambil Data Acara (Mendatang)
        $all_events = $user->acara()
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('dashboard', [
            'tanggalHariIni' => $tanggalHariIni,
            'user'           => $user,
            'tugas'          => $all_tasks,
            'acara'          => $all_events,
            'avatar_path'    => $avatar_path, // <-- Variabel ini yang dikirim ke View
        ]);
    }
}
