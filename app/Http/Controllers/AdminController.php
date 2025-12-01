<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        if (Auth::id() !== 1) {
            return redirect()->route('dashboard_admin')->with('error', 'Anda bukan Admin!');
        }

        $users = User::orderBy('created_at', 'desc')->get();

        $total_users = $users->count();

        return view('admin.dashboard_admin', compact('users', 'total_users'));
    }
}