<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PomodoroSession;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PomodoroController extends Controller
{
    public function start(Request $request)
    {
        $user = Auth::user();
        $taskId = $request->input('task_id');

        $session = PomodoroSession::create([
            'user_id' => $user->id,
            'task_id' => $taskId,
            'started_at' => Carbon::now(),
            'type' => 'focus',
        ]);

        return response()->json([
            'ok' => true,
            'session_id' => $session->id
        ]);
    }

    public function finish(Request $request)
    {
        $user = Auth::user();
        $taskId = $request->input('task_id');

        Log::info('Pomodoro finish called', ['user_id' => $user->id, 'task_id' => $taskId]);

        $session = PomodoroSession::where('user_id', $user->id)
            ->where('type', 'focus')
            ->whereNull('ended_at')
            ->when($taskId, fn($q) => $q->where('task_id', $taskId))
            ->latest('started_at')
            ->first();

        if (!$session) {
            Log::warning('No running focus session found', ['user_id' => $user->id, 'task_id' => $taskId]);
            return response()->json([
                'ok' => false,
                'message' => 'No running focus session found'
            ], 404);
        }

        $endedAt = Carbon::now();
        $duration = $endedAt->diffInSeconds($session->started_at);

        $session->update([
            'ended_at' => $endedAt,
            'duration_seconds' => $duration,
        ]);

        // Refresh untuk pastikan nilai ter-update
        $session->refresh();

        Log::info('Pomodoro session finished', [
            'session_id' => $session->id,
            'duration_seconds' => $session->duration_seconds,
            'ended_at' => $session->ended_at,
            'task_id' => $session->task_id
        ]);

        //agar bisa di track untuk statistik tugas
        return response()->json([
            'ok' => true,
            'session_id' => $session->id,
            'duration_seconds' => $session->duration_seconds,
            'ended_at' => $session->ended_at->toDateTimeString()
        ]);
    }
}
