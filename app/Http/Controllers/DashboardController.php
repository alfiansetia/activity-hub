<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Build query based on role
        if ($user->role === 'admin' || $user->role === 'dosen') {
            $query = Activity::query();
        } else {
            $query = Activity::where('company_id', $user->company_id);
        }

        $stats = [
            'total'   => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'accept'  => (clone $query)->where('status', 'accept')->count(),
            'reject'  => (clone $query)->where('status', 'reject')->count(),
        ];

        $recentActivities = Activity::with(['user', 'company'])
            ->when($user->role === 'user', fn($q) => $q->where('company_id', $user->company_id))
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recentActivities'));
    }
}
