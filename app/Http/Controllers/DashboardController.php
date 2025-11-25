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
        Carbon::setLocale('id');
        $tanggalHariIni = Carbon::now()->isoFormat('dddd, D MMMM Y');
        $user = Auth::user();

        // Avatar
        $avatar_path = $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&background=random&color=ffffff&size=128&bold=true';

        // 1. TUGAS AKTIF (Belum Selesai)
        $tugas_aktif = $user->tugas()->where('status', '!=', 'selesai')->orderBy('deadline', 'asc')->get();

        // 2. TUGAS RIWAYAT (Selesai)
        $tugas_riwayat = $user->tugas()->where('status', 'selesai')->orderBy('updated_at', 'desc')->get();

        // 3. ACARA AKTIF (Mendatang)
        $acara_aktif = $user->acara()->where('tanggal', '>=', now()->toDateString())->orderBy('tanggal', 'asc')->get();

        // 4. ACARA RIWAYAT (Lewat)
        $acara_riwayat = $user->acara()->where('tanggal', '<', now()->toDateString())->orderBy('tanggal', 'desc')->get();

        // Greeting logic
        $hour = date('H');
        if ($hour >= 5 && $hour < 12) {
            $greeting = 'Hi kamu';
            $icon = '☀️';
        } elseif ($hour >= 12 && $hour < 15) {
            $greeting = 'Selamat Siang';
            $icon = '🌤️';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
            $icon = '🌇';
        } else {
            $greeting = 'Selamat Malam';
            $icon = '🌙';
        }

        return view('dashboard', [
            'tanggalHariIni' => $tanggalHariIni,
            'user'           => $user,
            'avatar_path'    => $avatar_path,
            'tugas'          => $tugas_aktif,
            'riwayat_tugas'  => $tugas_riwayat, // <-- Data Penting
            'acara'          => $acara_aktif,
            'riwayat_acara'  => $acara_riwayat, // <-- Data Penting
            'greeting'       => $greeting,
            'icon'           => $icon
        ]);
    }
}
