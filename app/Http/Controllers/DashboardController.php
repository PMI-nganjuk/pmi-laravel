<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Mock some high-end telemetry data for the dashboard based on the user's role
        $stats = [
            'total_users' => 124,
            'active_sessions' => 12,
            'system_load' => '0.45%',
            'monthly_donations' => 'Rp 45.230.000',
            'pending_approvals' => 5,
            'cash_balance' => 'Rp 1.250.800.000',
            'expenses_this_month' => 'Rp 14.500.000',
            'submitted_tasks' => 18,
            'completed_reports' => 8,
        ];

        return view('dashboard', compact('user', 'stats'));
    }
}
