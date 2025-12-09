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
        
        if ($routeName === 'statistik.tugas') {
            $user = Auth::user();
            
            // 1. Ambil data Tugas Selesai per Bulan untuk 6 Bulan Terakhir
            $tasksPerMonth = Tugas::select(
                DB::raw('MONTH(updated_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('user_id', $user->id)
            ->where('status', 'selesai')
            ->where('updated_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
            
            // 2. Format Data untuk Grafik
            $labels = [];
            $dataCount = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $monthName = Carbon::now()->subMonths($i)->format('M');
                $monthNumber = Carbon::now()->subMonths($i)->month; 
                $labels[] = $monthName;
                
                $found = $tasksPerMonth->firstWhere('month', $monthNumber);
                $dataCount[] = $found ? $found->total : 0;
            }

            // 3. Ambil Data Ringkasan
            $totalSelesai = $user->tugas()->where('status', 'selesai')->count();
            $totalAktif = $user->tugas()->where('status', '!=', 'selesai')->count();
            
            // Data Waktu Rata-rata: Ganti N/A menjadi "-"
            $avgTime = "-"; 

            return view('analytics.statistik', [
                'labels' => $labels,
                'dataCount' => $dataCount,
                'totalSelesai' => $totalSelesai, // Total keseluruhan yang sudah selesai
                'totalAktif' => $totalAktif,
                'avgTime' => $avgTime, // Menggunakan strip (-)
            ]);
        }
        // Untuk rute lainnya, kembalikan ke dashboard dengan pesan (sementara)
        else {
            $title = '';
            if ($routeName === 'laporan.bulanan') {
                $title = 'Laporan Bulanan';
            } elseif ($routeName === 'progress.overview') {
                $title = 'Progress Overview';
            }
            
            return redirect()->route('dashboard')->with('info', "Halaman '{$title}' belum dibuat. Klik 'Statistik Tugas' untuk melihat halaman kerangka.");
        }
    }
}
