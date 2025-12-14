<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Acara;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    // Method untuk halaman analitik

    public function indexAnalytics(Request $request)
    {
        $routeName = $request->route()->getName();
        $user = Auth::user();
        Carbon::setLocale('id');

        // ================= STATISTIK TUGAS =================
        if ($routeName === 'statistik.tugas') {

            // 1️⃣ Grafik: Tugas Selesai per Bulan (6 Bulan Terakhir)
            $labels = [];
            $dataCount = [];

            for ($i = 5; $i >= 0; $i--) {
                $bulan = Carbon::now()->subMonths($i);

                $labels[] = $bulan->translatedFormat('M');

                $jumlah = Tugas::where('user_id', $user->id)
                    ->where('status', 'selesai')
                    ->whereYear('updated_at', $bulan->year)
                    ->whereMonth('selesai_pada', $bulan->month)
                    ->count();

                $dataCount[] = $jumlah;
            }

            // 2️⃣ Ringkasan
            $totalSelesai = $user->tugas()
                ->where('status', 'selesai')
                ->count();

            $totalAktif = $user->tugas()
                ->where('status', '!=', 'selesai')
                ->count();

            // 3️⃣ Waktu Rata-rata Penyelesaian (MENIT)
            $durasi = Tugas::where('user_id', $user->id)
                ->where('status', 'selesai')
                ->whereNotNull('selesai_pada')
                ->get()
                ->map(function ($tugas) {
                    return Carbon::parse($tugas->created_at)
                        ->diffInMinutes(Carbon::parse($tugas->selesai_pada));
                });

            $avgTime = $durasi->count()
                ? round($durasi->avg()) . ' menit'
                : '-';

            return view('analytics.statistik', [
                'labels'        => $labels,
                'dataCount'     => $dataCount,
                'totalSelesai'  => $totalSelesai,
                'totalAktif'    => $totalAktif,
                'avgTime'       => $avgTime,
            ]);
        }

        // ================= LAPORAN BULANAN =================
        elseif ($routeName === 'laporan.bulanan') {
            return view('analytics.laporan_bulanan');
        }

        // ================= PROGRESS OVERVIEW =================
        elseif ($routeName === 'progress.overview') {
            return view('analytics.progress_overview');
        }

        return redirect()->route('dashboard')
            ->with('error', 'Rute analytics tidak dikenal.');
    }
}
