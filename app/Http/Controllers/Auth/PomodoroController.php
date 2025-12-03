<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PomodoroSession;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PomodoroController extends Controller
{
    public function start(Request $request)
    {
        $user = Auth::user();
        $taskId = $request->input('task_id');

        $session = PomodoroSession::create([
            'user_id' => $user ? $user->id : null,
            'task_id' => $taskId,
            'started_at' => Carbon::now(),
            'type' => 'focus',
        ]);

        return response()->json(['ok' => true, 'session_id' => $session->id]);
    }

    public function finish(Request $request)
    {
        $user = Auth::user();
        $taskId = $request->input('task_id', null);

        $query = PomodoroSession::whereNull('ended_at');
        if ($user) $query->where('user_id', $user->id);
        if ($taskId) $query->where('task_id', $taskId);
        $session = $query->latest('started_at')->first();

        if (!$session) {
            return response()->json(['ok' => false, 'message' => 'No running session found'], 404);
        }

        $endedAt = Carbon::now();
        $duration = $endedAt->diffInSeconds($session->started_at);

        $session->update([
            'ended_at' => $endedAt,
            'duration_seconds' => $duration,
        ]);

        return response()->json(['ok' => true, 'session_id' => $session->id, 'duration' => $duration]);
    }

    public function focusEnded(Request $request)
    {
        $user = Auth::user();
        $mode = $request->input('mode');
        $taskId = $request->input('task_id', null);

        $query = PomodoroSession::whereNull('ended_at');
        if ($user) $query->where('user_id', $user->id);
        if ($taskId) $query->where('task_id', $taskId);
        $session = $query->latest('started_at')->first();

        if (!$session) {
            return response()->json(['ok' => false, 'message' => 'No running session found'], 404);
        }

        $endedAt = Carbon::now();
        $duration = $endedAt->diffInSeconds($session->started_at);

        $session->update([
            'ended_at' => $endedAt,
            'duration_seconds' => $duration,
            'type' => 'focus',
        ]);

        $breakType = ($mode === 'long') ? 'long_break' : 'short_break';
        PomodoroSession::create([
            'user_id' => $user ? $user->id : null,
            'task_id' => $taskId,
            'started_at' => $endedAt,
            'type' => $breakType,
        ]);

        return response()->json(['ok' => true, 'session_id' => $session->id, 'duration' => $duration]);
    }
}
