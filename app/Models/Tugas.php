<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tugas',
        'deskripsi',
        'deadline',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'deadline' => 'datetime',
        'tanggal' => 'datetime',
    ];
}
