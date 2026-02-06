<?php

namespace App\Http\Controllers;

use App\Models\XmlCdr;
use App\Models\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index()
    {
        // Get today's statistics
        $todayStats = $this->getTodayStatistics();
        
        // Get recent calls
        $recentCalls = XmlCdr::orderBy('start_stamp', 'desc')
                            ->limit(10)
                            ->get();

        // Get active extensions count
        $activeExtensions = Extension::where('enabled', 'true')->count();

        return view('dashboard.index', compact('todayStats', 'recentCalls', 'activeExtensions'));
    }

    /**
     * Get today's call statistics
     */
    private function getTodayStatistics()
    {
        $today = now()->startOfDay();
        
        return [
            'total_calls' => XmlCdr::where('start_stamp', '>=', $today)->count(),
            'answered_calls' => XmlCdr::where('start_stamp', '>=', $today)
                                      ->where('hangup_cause', 'NORMAL_CLEARING')
                                      ->count(),
            'missed_calls' => XmlCdr::where('start_stamp', '>=', $today)
                                    ->where('hangup_cause', '!=', 'NORMAL_CLEARING')
                                    ->count(),
            'total_duration' => XmlCdr::where('start_stamp', '>=', $today)
                                      ->sum('duration'),
            'avg_duration' => XmlCdr::where('start_stamp', '>=', $today)
                                    ->avg('duration'),
            'inbound_calls' => XmlCdr::where('start_stamp', '>=', $today)
                                     ->where('direction', 'inbound')
                                     ->count(),
            'outbound_calls' => XmlCdr::where('start_stamp', '>=', $today)
                                      ->where('direction', 'outbound')
                                      ->count(),
        ];
    }

    /**
     * Get real-time dashboard data via AJAX
     */
    public function liveData()
    {
        return response()->json([
            'today' => $this->getTodayStatistics(),
            'recent_calls' => XmlCdr::orderBy('start_stamp', 'desc')
                                    ->limit(10)
                                    ->get(),
        ]);
    }
}

