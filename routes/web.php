<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\AcaraController;
use App\Http\Controllers\Auth\PomodoroController;
use App\Http\Controllers\PasswordController;

/*
| Redirect Awal
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
| Dashboard (User)
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
Route Terproteksi (Login Required)
*/
Route::middleware('auth')->group(function () {

    /*
    | Profile
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroy_avatar'])->name('profile.destroy_avatar');

    /*
    | Password
    */
    Route::get('/change-password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');

    /*
    | Tugas & Acara
    */
    Route::resource('tugas', TugasController::class);
    Route::patch('/tugas/{id}/selesai', [TugasController::class, 'selesai'])->name('tugas.selesai');

    Route::resource('acara', AcaraController::class);

    /*
    | Analitik Dashboard
    */
    Route::get('/laporan-bulanan/{year?}/{month?}', [DashboardController::class, 'indexAnalytics'])->name('laporan.bulanan');
    Route::get('/statistik-tugas', [DashboardController::class, 'indexAnalytics'])->name('statistik.tugas');
    Route::get('/progress-overview', [DashboardController::class, 'indexAnalytics'])->name('progress.overview');

    /*
    | Pomodoro
    */
    Route::post('/pomodoro/start', [PomodoroController::class, 'start']);
    Route::post('/pomodoro/finish', [PomodoroController::class, 'finish']);
    Route::post('/pomodoro/focus-ended', [PomodoroController::class, 'focusEnded']);
});

Route::get('/statistik/pomodoro-live', function () {
    $user = Auth::user();
    $today = Carbon\Carbon::today();

    $totalSecondsToday = \App\Models\PomodoroSession::where('user_id', $user->id)
        ->where('type', 'focus')
        ->whereDate('started_at', $today)
        ->sum('duration_seconds');

    $startOfWeek = Carbon\Carbon::now()->startOfWeek();
    $endOfWeek   = Carbon\Carbon::now()->endOfWeek();

    $weekSeconds = \App\Models\PomodoroSession::where('user_id', $user->id)
        ->where('type', 'focus')
        ->whereBetween('started_at', [$startOfWeek, $endOfWeek])
        ->sum('duration_seconds');

    return response()->json([
        'today_minutes' => round($totalSecondsToday / 60),
        'avg_week_minutes' => round(($weekSeconds / 60) / 7),
        'week_total_minutes' => round($weekSeconds / 60)
    ]);
})->middleware('auth');

// DEBUG: Lihat semua pomodoro session untuk user
Route::get('/debug/pomodoro-sessions', function () {
    $user = Auth::user();
    $sessions = \App\Models\PomodoroSession::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    return response()->json([
        'user_id' => $user->id,
        'sessions' => $sessions->toArray()
    ]);
})->middleware('auth');

// Route untuk serve file storage (alternatif symbolic link)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.file');


/*
|--------------------------------------------------------------------------
| Auth Routes (Login, Register, dll)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
