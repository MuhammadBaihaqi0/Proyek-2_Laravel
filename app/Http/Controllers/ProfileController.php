<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage; // Penting untuk menyimpan/menghapus file

class ProfileController extends Controller
{
    /**
     * Menampilkan formulir edit foto profil.
     */
    public function edit(Request $request): View
    {
        // Logika Avatar yang Pintar (sama seperti di DashboardController)
        $user = $request->user();
        if ($user->avatar) {
            // Cek apakah file ada di storage, jika ada gunakan route storage, jika tidak gunakan gravatar
            $storagePath = storage_path('app/public/' . $user->avatar);
            if (file_exists($storagePath)) {
                $avatar_path = route('storage.file', ['path' => $user->avatar]);
            } else {
                $avatar_path = 'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&background=random&color=ffffff&size=128&bold=true';
            }
        } else {
            $avatar_path = 'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&background=random&color=ffffff&size=128&bold=true';
        }

        return view('profile.edit', [
            'user' => $user,
            'avatar_path' => $avatar_path, // Kirim path avatar ke view
        ]);
    }

    /**
     * Menyimpan foto profil baru.
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. Validasi Input (Pastikan itu file gambar)
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Max 2MB
        ]);

        $user = $request->user();

        // 2. Hapus foto lama jika ada
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // 3. Simpan foto baru
        $path = $request->file('avatar')->store('avatars', 'public');

        // 4. Update nama file avatar di database
        $user->avatar = $path;
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Menghapus foto profil (kembali ke inisial).
     */
    public function destroy_avatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Hapus file avatar dari storage jika ada
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Set kolom avatar di database menjadi null
        $user->avatar = null;
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'avatar-deleted');
    }

    // Fungsi destroy() untuk hapus akun tetap ada jika dibutuhkan
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
