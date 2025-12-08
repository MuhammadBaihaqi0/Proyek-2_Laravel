<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcaraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Jika tidak sengaja membuka /acara, kembalikan ke dashboard
        return redirect()->route('dashboard');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input (Sama gaya dengan TugasController)
        $validated = $request->validate([
            'nama_acara' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        // 2. Simpan Acara (Menggunakan relasi user agar user_id otomatis terisi)
        $request->user()->acara()->create($validated);

        // 3. KEMBALI KE DASHBOARD
        // return back() artinya kembali ke halaman di mana form itu berada (Dashboard)
        return redirect()->route('dashboard')->with('success', 'Acara berhasil ditambahkan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Acara $acara)
    {
        // Cek apakah user adalah pemilik acara ini
        if ($request->user()->id !== $acara->user_id) {
            abort(403);
        }

        $acara->delete();

        // Kembali ke Dashboard setelah menghapus
        return redirect()->route('dashboard')->with('success', 'Acara berhasil dihapus!');
    }
}
