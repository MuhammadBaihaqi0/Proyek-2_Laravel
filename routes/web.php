<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\AcaraController;
use App\Http\Controllers\PomodoroController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroy_avatar'])->name('profile.destroy_avatar');

    Route::resource('tugas', TugasController::class);
    Route::resource('acara', AcaraController::class);
    Route::patch('/tugas/{id}/selesai', [TugasController::class, 'selesai'])->name('tugas.selesai');
    //Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard_admin');

    // START: RUTE BARU UNTUK ANALITIK
    Route::get('/statistik-tugas', [DashboardController::class, 'indexAnalytics'])->name('statistik.tugas');
    Route::get('/laporan-bulanan', [DashboardController::class, 'indexAnalytics'])->name('laporan.bulanan');
    Route::get('/progress-overview', [DashboardController::class, 'indexAnalytics'])->name('progress.overview');
// END: RUTE BARU UNTUK ANALITIK
});

Route::middleware('auth')->group(function(){
    Route::post('/pomodoro/start', [PomodoroController::class, 'start']);
    Route::post('/pomodoro/finish', [PomodoroController::class, 'finish']);
    Route::post('/pomodoro/focus-ended', [PomodoroController::class, 'focusEnded']);
});
require __DIR__ . '/auth.php';
