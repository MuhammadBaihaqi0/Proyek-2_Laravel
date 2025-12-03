<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PomodoroSession extends Model
{
    protected $fillable = [
        'user_id', 'task_id', 'started_at', 'ended_at', 'duration_seconds', 'type', 'notes'
    ];

    protected $dates = ['started_at', 'ended_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
