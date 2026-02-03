<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\FusionUser;
use App\Models\Extension;
use App\Models\CallRecord;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_domains' => Domain::count(),
            'active_domains' => Domain::where('domain_enabled', true)->count(),
            'total_users' => FusionUser::count(),
            'active_users' => FusionUser::where('user_enabled', true)->count(),
            'total_extensions' => Extension::count(),
            'active_extensions' => Extension::where('enabled', true)->count(),
            'total_calls_today' => CallRecord::whereDate('start_stamp', today())->count(),
            'total_calls_week' => CallRecord::whereBetween('start_stamp', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        // Get recent calls
        $recentCalls = CallRecord::with('domain', 'extension')
            ->orderBy('start_stamp', 'desc')
            ->limit(10)
            ->get();

        // Get call statistics by direction
        $callsByDirection = CallRecord::select('direction', DB::raw('count(*) as total'))
            ->whereDate('start_stamp', '>=', now()->subDays(7))
            ->groupBy('direction')
            ->get();

        // Get top extensions by call volume
        $topExtensions = Extension::withCount([
            'callRecords' => function($query) {
                $query->whereDate('start_stamp', '>=', now()->subDays(7));
            }
        ])
        ->orderBy('call_records_count', 'desc')
        ->limit(10)
        ->get();

        return view('dashboard', compact('stats', 'recentCalls', 'callsByDirection', 'topExtensions'));
    }
}
