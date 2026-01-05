<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Acara;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PomodoroSession;

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
    public function indexAnalytics(Request $request, $year = null, $month = null) // PARAMETER DITAMBAHKAN
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
             $user = Auth::user();
    $today = Carbon::today();

    /**
     * ===============================
     * TOTAL FOKUS HARI INI
     * ===============================
     */
    $totalFocusTodaySeconds = PomodoroSession::where('user_id', $user->id)
        ->where('type', 'focus')
        ->whereDate('started_at', $today)
        ->sum('duration_seconds');

    $totalFocusTodayMinutes = round($totalFocusTodaySeconds / 60);

    /**
     * ===============================
     * FOKUS MINGGU INI (HARIAN)
     * ===============================
     */
    $startOfWeek = Carbon::now()->startOfWeek(); // Senin
    $endOfWeek   = Carbon::now()->endOfWeek();   // Minggu

    $labels = [];
    $dataFocusMinutes = [];

    for ($date = $startOfWeek->copy(); $date <= $endOfWeek; $date->addDay()) {
        $labels[] = $date->translatedFormat('D');

        $minutes = PomodoroSession::where('user_id', $user->id)
            ->where('type', 'focus')
            ->whereDate('started_at', $date)
            ->sum('duration_seconds');

        $dataFocusMinutes[] = round($minutes / 60);
    }

    /**
     * ===============================
     * RATA-RATA FOKUS MINGGU INI
     * ===============================
     */
    $totalWeekMinutes = array_sum($dataFocusMinutes);
    $avgFocusMinutes = round($totalWeekMinutes / 7);

    return view('analytics.statistik', [
        'labels'                => $labels,
        'dataFocusMinutes'      => $dataFocusMinutes,
        'totalFocusTodayMinutes'=> $totalFocusTodayMinutes,
        'avgFocusMinutes'       => $avgFocusMinutes,
    ]);
        }
        /**
         * ===============================
         * LAPORAN BULANAN (DIPERBAIKI)
         * ===============================
         */
        if ($routeName === 'laporan.bulanan') {

            // --- Tentukan Bulan Target (PERBAIKAN ERROR INVALID DATE EXCEPTION) ---
            if ($year && $month) {
                $targetDate = Carbon::parse("{$year}-{$month}-01");
            } else {
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
            } elseif ($totalTugas === 2 && $totalAcara > 3) {
                $iconKesimpulan = '🥳';
                $kesimpulan = 'Kebanyakan Nongkrong';
                $saran = 'Bulan ini penuh kegiatan sosial/hiburan, tetapi nol tugas. Seimbangkan kesenangan dan kewajiban!';
            } else {
                $iconKesimpulan = '⚠️';
                $kesimpulan = 'Perlu Perhatian';
                $saran = 'Tidak ada data tugas yang dapat dianalisis secara efektif. Mulai kembali dengan tugas kecil untuk membangun momentum.';
            }

            // 6. Generate Bulan yang Tersedia
            $taskMonths = Tugas::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'))
                ->where('user_id', $user->id)->distinct()->get();

            $acaraMonths = Acara::select(DB::raw('YEAR(tanggal) as year'), DB::raw('MONTH(tanggal) as month'))
                ->where('user_id', $user->id)->distinct()->get();

            $allMonths = $taskMonths->merge($acaraMonths);

            $availableMonths = $allMonths->unique(function ($item) {
                return $item['year'] . '-' . $item['month'];
            })
                ->sortByDesc(function ($item) {
                    return $item['year'] * 100 + $item['month'];
                })
                ->map(function ($item) {
                    $date = Carbon::createFromDate($item['year'], $item['month'], 1);
                    return [
                        'year' => $item['year'],
                        'month' => $item['month'],
                        'label' => $date->isoFormat('MMMM Y'),
                    ];
                })->values();

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
                'kesimpulan',
                'saran',
                'iconKesimpulan'
            ));
        }

        /**
         * ===============================
         * PROGRESS OVERVIEW (DIPERBAIKI)
         * ===============================
         */
        if ($routeName === 'progress.overview') {

            // 1. Data untuk chart kumulatif (12 bulan terakhir)
            $tasksCompletedAllTime = $user->tugas()
                ->where('status', 'selesai')
                ->orderBy('updated_at', 'asc')
                ->get();

            $labels = [];
            $cumulativeData = [];
            $currentCumulativeCount = 0;

            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthName = $month->isoFormat('MMM YY');
                $labels[] = $monthName;

                $tasksThisMonth = $tasksCompletedAllTime->filter(function ($task) use ($month) {
                    return $task->updated_at && $task->updated_at->format('Y-m') === $month->format('Y-m');
                })->count();

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

        return redirect()->route('dashboard');
    }
}