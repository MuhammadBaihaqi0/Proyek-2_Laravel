<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tugas;

class TugasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $validated = $request->validate([
        'nama_tugas' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'deadline' => 'required|date',
    ]);

    // Menggunakan relasi untuk membuat tugas (otomatis mengisi user_id)
    $request->user()->tugas()->create($validated);

    // Kembali ke halaman sebelumnya (dashboard) dengan pesan sukses
    return back()->with('success_message', 'Tugas berhasil ditambahkan!');
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
    // app/Http/Controllers/TugasController.php
public function destroy(Request $request, Tugas $tuga) // Laravel akan otomatis mencari Tugas berdasarkan ID
{
    // Cek apakah user pemilik tugas ini
    if ($request->user()->id !== $tuga->user_id) {
        abort(403); // Akses ditolak
    }

    $tuga->delete();

    return back()->with('success_message', 'Tugas berhasil dihapus!');
}
}
