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
    public function indexAnalytics(Request $request, $year = null, $month = null)
    {
        Carbon::setLocale('id'); // Pastikan nama bulan dalam Bahasa Indonesia
        $routeName = $request->route()->getName();
        $user = Auth::user();
        
        // --- LOGIKA UNTUK STATISTIK TUGAS (6 BULAN TERAKHIR) ---
        if ($routeName === 'statistik.tugas') {
            $tasksPerMonth = Tugas::select(
                DB::raw('MONTH(updated_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('user_id', $user->id)
            ->where('status', 'selesai')
            ->where('updated_at', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
            
            $labels = [];
            $dataCount = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthName = $month->format('M');
                $monthNumber = $month->month; 
                $labels[] = $monthName;
                
                $found = $tasksPerMonth->firstWhere('month', $monthNumber);
                $dataCount[] = $found ? $found->total : 0;
            }

            $totalSelesai = $user->tugas()->where('status', 'selesai')->count();
            $totalAktif = $user->tugas()->where('status', '!=', 'selesai')->count();
            $avgTime = "-"; // Menggunakan strip (-)

            return view('analytics.statistik', compact('labels', 'dataCount', 'totalSelesai', 'totalAktif', 'avgTime'));
        } 
        
        // --- LOGIKA UNTUK LAPORAN BULANAN (DIPERBARUI DENGAN KESIMPULAN) ---
        elseif ($routeName === 'laporan.bulanan') {
            
            // Tentukan Bulan yang digunakan, default ke bulan saat ini jika parameter kosong
            if ($year && $month) {
                // Coba buat tanggal dari YYYY-MM-01
                $targetDate = Carbon::parse("{$year}-{$month}-01");
            } else {
                // Jika tidak ada parameter, gunakan bulan saat ini
                $targetDate = Carbon::now();
            }
            
            $startOfMonth = $targetDate->startOfMonth()->toDateString();
            $endOfMonth = $targetDate->endOfMonth()->toDateString();
            
            // Data Bulan dan Tahun saat ini
            $currentMonthYear = $targetDate->isoFormat('MMMM Y');
            $selectedYear = $targetDate->year;
            $selectedMonth = $targetDate->month;
            
            // 1. Total Tugas dibuat
            $totalTugasBulanIni = $user->tugas()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // 2. Tugas selesai, termasuk detailnya
            $tugasSelesaiBulanIni = $user->tugas()
                ->where('status', 'selesai')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                ->orderBy('updated_at', 'desc')
                ->get();

            // 3. Total Acara, termasuk detailnya
            $acaraBulanIni = $user->acara()
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->orderBy('tanggal', 'asc')
                ->get();

            // 4. Hitung Rasio
            $rasioPenyelesaianBulanIni = ($totalTugasBulanIni > 0) 
                ? round(($tugasSelesaiBulanIni->count() / $totalTugasBulanIni) * 100) 
                : 0;

            // 5. LOGIKA KESIMPULAN PRODUKTIVITAS
            $kesimpulan = '';
            $saran = '';
            $iconKesimpulan = '';
            
            $rasio = $rasioPenyelesaianBulanIni;
            $totalTugas = $totalTugasBulanIni;
            $totalAcara = $acaraBulanIni->count();

            if ($totalTugas === 0 && $totalAcara === 0) {
                $iconKesimpulan = '😴';
                $kesimpulan = 'Sangat Santai';
                $saran = 'Bulan ini sangat sepi. Ayo segera tentukan agenda atau buat tugas baru untuk memaksimalkan waktu Anda!';
            } elseif ($rasio >= 90 && $totalTugas >= 10) {
                $iconKesimpulan = '👑';
                $kesimpulan = 'Produktivitas Juara!';
                $saran = 'Pertahankan performa luar biasa ini! Anda efisien dan mampu menyelesaikan hampir semua target.';
            } elseif ($rasio >= 70 && $totalTugas >= 5) {
                $iconKesimpulan = '💪';
                $kesimpulan = 'Sangat Produktif';
                $saran = 'Kinerja yang baik! Sedikit dorongan lagi untuk mencapai tingkat penyelesaian sempurna.';
            } elseif ($rasio >= 50) {
                $iconKesimpulan = '🧐';
                $kesimpulan = 'Cukup Produktif';
                $saran = 'Progres Anda lumayan, tetapi banyak tugas yang tertinggal. Coba identifikasi penyebab penundaan.';
            } elseif ($rasio > 0 && $rasio < 50) {
                $iconKesimpulan = '🐌';
                $kesimpulan = 'Kurang Produktif';
                $saran = 'Rasio penyelesaian Anda rendah. Fokus pada prioritas dan batasi gangguan non-esensial.';
            } elseif ($totalTugas === 0 && $totalAcara > 3) {
                $iconKesimpulan = '🥳';
                $kesimpulan = 'Kebanyakan Nongkrong';
                $saran = 'Bulan ini penuh kegiatan sosial/hiburan, tetapi nol tugas. Seimbangkan kesenangan dan kewajiban!';
            } else {
                $iconKesimpulan = '⚠️';
                $kesimpulan = 'Perlu Perhatian';
                $saran = 'Tidak ada data tugas yang dapat dianalisis secara efektif. Mulai kembali dengan tugas kecil untuk membangun momentum.';
            }
            
            // 6. Generate Bulan yang Tersedia (HANYA bulan yang memiliki data)
            $uniqueMonths = collect();

            // Ambil bulan unik dari tabel tugas
            $taskMonths = Tugas::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month')
            )
            ->where('user_id', $user->id)
            ->distinct()
            ->get();
            
            // Ambil bulan unik dari tabel acara
            $acaraMonths = Acara::select(
                DB::raw('YEAR(tanggal) as year'),
                DB::raw('MONTH(tanggal) as month')
            )
            ->where('user_id', $user->id)
            ->distinct()
            ->get();

            // Gabungkan kedua koleksi
            $allMonths = $taskMonths->merge($acaraMonths);

            // Proses untuk mendapatkan daftar bulan/tahun unik yang sudah diformat dan diurutkan
            $availableMonths = $allMonths->unique(function ($item) {
                return $item['year'] . '-' . $item['month'];
            })
            ->sortByDesc(function ($item) {
                // Urutkan berdasarkan tahun dan bulan (terbaru di atas)
                return $item['year'] * 100 + $item['month'];
            })
            ->map(function ($item) {
                // Format tanggal untuk label
                $date = Carbon::createFromDate($item['year'], $item['month'], 1);
                return [
                    'year' => $item['year'],
                    'month' => $item['month'],
                    'label' => $date->isoFormat('MMMM Y'),
                ];
            })->values(); // Reset kunci array

            // Jika tidak ada data sama sekali, tambahkan bulan saat ini sebagai fallback
            if ($availableMonths->isEmpty()) {
                 $availableMonths->push([
                    'year' => Carbon::now()->year,
                    'month' => Carbon::now()->month,
                    'label' => Carbon::now()->isoFormat('MMMM Y'),
                ]);
            }

            return view('analytics.laporan_bulanan', compact(
                'totalTugasBulanIni', 
                'tugasSelesaiBulanIni', 
                'acaraBulanIni',
                'rasioPenyelesaianBulanIni',
                'currentMonthYear',
                'selectedYear',
                'selectedMonth',
                'availableMonths',
                'kesimpulan',    // <-- Variabel Baru
                'saran',         // <-- Variabel Baru
                'iconKesimpulan' // <-- Variabel Baru
            ));
        }
        
        // --- LOGIKA BARU UNTUK PROGRESS OVERVIEW (KUMULATIF 12 BULAN) ---
        elseif ($routeName === 'progress.overview') {
            
            // 1. Data untuk chart kumulatif (12 bulan terakhir)
            $tasksCompletedAllTime = $user->tugas()
                ->where('status', 'selesai')
                ->orderBy('updated_at', 'asc')
                ->get();
            
            $labels = [];
            $cumulativeData = [];
            $currentCumulativeCount = 0;
            
            // Hitung data 12 bulan
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthName = $month->isoFormat('MMM YY'); 
                $labels[] = $monthName;

                // Hitung tugas selesai dalam bulan spesifik ini
                $tasksThisMonth = $tasksCompletedAllTime->filter(function ($task) use ($month) {
                    return $task->updated_at && $task->updated_at->format('Y-m') === $month->format('Y-m');
                })->count();
                
                // Jumlahkan secara kumulatif
                $currentCumulativeCount += $tasksThisMonth;
                $cumulativeData[] = $currentCumulativeCount;
            }

            // 2. Hitung Overall Progress Percentage (sejak awal)
            $totalTasksAllTime = $user->tugas()->count();
            $totalTasksCompletedAllTime = $user->tugas()->where('status', 'selesai')->count();

            $overallProgressPercent = ($totalTasksAllTime > 0)
                ? round(($totalTasksCompletedAllTime / $totalTasksAllTime) * 100)
                : 0;
            
            return view('analytics.progress_overview', compact(
                'labels', 
                'cumulativeData', 
                'overallProgressPercent'
            ));
        }
        
        // --- DEFAULT FALLBACK ---
        else {
            return redirect()->route('dashboard')->with('error', "Rute analytics tidak dikenal.");
        }
    }
}