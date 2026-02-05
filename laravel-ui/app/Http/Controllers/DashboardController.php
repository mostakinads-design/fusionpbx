<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Extension;
use App\Models\Domain;
use App\Models\CallCenterQueue;
use App\Models\CallCenterAgent;
use App\Models\Campaign;
use App\Models\XmlCdr;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $statistics = [
            'total_users' => User::count(),
            'total_extensions' => Extension::count(),
            'total_domains' => Domain::count(),
            'total_queues' => CallCenterQueue::count(),
            'total_agents' => CallCenterAgent::count(),
            'active_campaigns' => Campaign::where('status', 'running')->count(),
            'total_calls_today' => XmlCdr::whereDate('start_stamp', today())->count(),
            'answered_calls_today' => XmlCdr::whereDate('start_stamp', today())
                ->whereNotNull('answer_stamp')->count(),
        ];

        return view('dashboard.index', compact('statistics'));
    }
}
