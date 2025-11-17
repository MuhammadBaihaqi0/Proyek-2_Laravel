<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Acara;

class AcaraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user(); // Menggantikan get_user_id()

        // Logika dari dashboard.php
        $all_tasks = $user->tugas()
                          ->where('status', '!=', 'selesai')
                          ->orderBy('deadline', 'asc')
                          ->get();

        $all_events = $user->acara()
                           ->where('tanggal', '>=', now()->toDateString())
                           ->orderBy('tanggal', 'asc')
                           ->get();

        $completed_tasks = $user->tugas()
                                ->where('status', 'selesai')
                                ->orderBy('deadline', 'desc')
                                ->get();

        $past_events = $user->acara()
                            ->where('tanggal', '<', now()->toDateString())
                            ->orderBy('tanggal', 'desc')
                            ->get();

        // Mengirim data ke view
        return view('dashboard', [
            'username' => $user->username, // Menggantikan get_username()
            'avatar_path' => $user->avatar ? asset('storage/' . $user->avatar) : '/profile_image.php?name=' . urlencode($user->username), // Nanti kita perbaiki
            'all_tasks' => $all_tasks,
            'all_events' => $all_events,
            'completed_tasks' => $completed_tasks,
            'past_events' => $past_events,
            // ... statistik lainnya bisa ditambahkan di sini
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
