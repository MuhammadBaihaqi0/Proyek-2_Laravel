<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// Tidak perlu 'use App\Models\Tugas' jika berada di folder yang sama (App\Models),
// tapi dibiarkan juga tidak apa-apa.

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',      // Wajib untuk Laravel Default
        'email',     // Wajib untuk Laravel Default
        'username',  // Tambahan Anda
        'password',
        'avatar',    // Tambahan Anda
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* -------------------------------------------------------------------------- */
    /* RELASI DATABASE                              */
    /* -------------------------------------------------------------------------- */

    // Relasi: Satu User punya banyak Tugas
    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    // Relasi: Satu User punya banyak Acara
    public function acara()
    {
        return $this->hasMany(Acara::class);
    }
}