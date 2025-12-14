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
    /**
     * ===============================
     * DASHBOARD UTAMA
     * ===============================
     */
    public function index()
    {
        Carbon::setLocale('id');

        $user = Auth::user();
        $tanggalHariIni = Carbon::now()->translatedFormat('l, d F Y');

        // Avatar
        $avatar_path = $user->avatar
            ? asset('storage/' . $user->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&background=random&color=ffffff&size=128&bold=true';

        // Tugas Aktif
        $tugas_aktif = $user->tugas()
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline', 'asc')
            ->get();

        // Tugas Selesai
        $tugas_riwayat = $user->tugas()
            ->where('status', 'selesai')
            ->orderBy('selesai_pada', 'desc')
            ->get();

        // Acara Mendatang
        $acara_aktif = $user->acara()
            ->where('tanggal', '>=', now())
            ->orderBy('tanggal', 'asc')
            ->get();

        // Acara Terlewat
        $acara_riwayat = $user->acara()
            ->where('tanggal', '<', now())
            ->orderBy('tanggal', 'desc')
            ->get();

        // Greeting
        $hour = now()->hour;
        if ($hour >= 5 && $hour < 12) {
            $greeting = 'Selamat Pagi';
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
            'riwayat_tugas'  => $tugas_riwayat,
            'acara'          => $acara_aktif,
            'riwayat_acara'  => $acara_riwayat,
            'greeting'       => $greeting,
            'icon'           => $icon,
        ]);
    }

    /**
     * ===============================
     * ANALYTICS / STATISTIK
     * ===============================
     */
    public function indexAnalytics(Request $request)
    {
        Carbon::setLocale('id');

        $user = Auth::user();
        $routeName = $request->route()->getName();

        /**
         * ===============================
         * STATISTIK TUGAS
         * ===============================
         */
        if ($routeName === 'statistik.tugas') {

            $labels = [];
            $dataCount = [];

            for ($i = 5; $i >= 0; $i--) {
                $bulan = Carbon::now()->subMonths($i);

                $labels[] = $bulan->translatedFormat('M Y');

                $jumlah = Tugas::where('user_id', $user->id)
                    ->where('status', 'selesai')
                    ->whereYear('selesai_pada', $bulan->year)
                    ->whereMonth('selesai_pada', $bulan->month)
                    ->count();

                $dataCount[] = $jumlah;
            }

            // Ringkasan
            $totalSelesai = $user->tugas()->where('status', 'selesai')->count();
            $totalAktif   = $user->tugas()->where('status', '!=', 'selesai')->count();

            // Rata-rata waktu pengerjaan (menit)
            $avgMinutes = $user->tugas()
                ->where('status', 'selesai')
                ->whereNotNull('selesai_pada')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, selesai_pada)) as avg'))
                ->value('avg');

            $avgTime = $avgMinutes
                ? round($avgMinutes / 60, 1) . ' jam'
                : '-';

            return view('analytics.statistik', [
                'labels'        => $labels,
                'dataCount'     => $dataCount,
                'totalSelesai'  => $totalSelesai,
                'totalAktif'    => $totalAktif,
                'avgTime'       => $avgTime,
            ]);
        }

        /**
         * ===============================
         * LAPORAN BULANAN
         * ===============================
         */
        if ($routeName === 'laporan.bulanan') {
            return view('analytics.laporan_bulanan');
        }

        /**
         * ===============================
         * PROGRESS OVERVIEW
         * ===============================
         */
        if ($routeName === 'progress.overview') {
            return view('analytics.progress_overview');
        }

        return redirect()->route('dashboard');
    }
}
