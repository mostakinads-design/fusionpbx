<?php

namespace App\Http\Controllers;

use App\Models\XmlCdr;
use App\Services\AiAgentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CdrController extends Controller
{
    protected $aiService;

    public function __construct(AiAgentService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display CDR list
     */
    public function index(Request $request)
    {
        $query = XmlCdr::query();

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('start_stamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('start_stamp', '<=', $request->end_date);
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('caller_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('caller_id_number', 'like', "%{$request->caller_id}%")
                  ->orWhere('caller_id_name', 'like', "%{$request->caller_id}%");
            });
        }

        if ($request->filled('destination')) {
            $query->where('destination_number', 'like', "%{$request->destination}%");
        }

        // Order by start time descending
        $query->orderBy('start_stamp', 'desc');

        // Paginate results
        $cdrs = $query->paginate(50);

        return view('cdr.index', compact('cdrs'));
    }

    /**
     * Get CDR data as JSON for API/AJAX requests
     */
    public function getData(Request $request)
    {
        $query = XmlCdr::query();

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('start_stamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('start_stamp', '<=', $request->end_date);
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        // Order by start time descending
        $query->orderBy('start_stamp', 'desc');

        // Paginate results
        $cdrs = $query->paginate($request->get('per_page', 50));

        return response()->json($cdrs);
    }

    /**
     * Show single CDR details
     */
    public function show($id)
    {
        $cdr = XmlCdr::findOrFail($id);
        
        return view('cdr.show', compact('cdr'));
    }

    /**
     * Get AI analysis for a call
     */
    public function analyze($id)
    {
        $cdr = XmlCdr::findOrFail($id);
        
        $callData = [
            'caller_id_name' => $cdr->caller_id_name,
            'caller_id_number' => $cdr->caller_id_number,
            'destination_number' => $cdr->destination_number,
            'duration' => $cdr->duration,
            'hangup_cause' => $cdr->hangup_cause,
            'direction' => $cdr->direction,
        ];

        $analysis = $this->aiService->analyzeCall($callData);

        return response()->json($analysis);
    }

    /**
     * Get statistics dashboard data
     */
    public function statistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(7));
        $endDate = $request->get('end_date', now());

        $stats = [
            'total_calls' => XmlCdr::whereBetween('start_stamp', [$startDate, $endDate])->count(),
            'answered_calls' => XmlCdr::whereBetween('start_stamp', [$startDate, $endDate])
                                     ->where('hangup_cause', 'NORMAL_CLEARING')->count(),
            'missed_calls' => XmlCdr::whereBetween('start_stamp', [$startDate, $endDate])
                                    ->whereNotIn('hangup_cause', ['NORMAL_CLEARING'])->count(),
            'total_duration' => XmlCdr::whereBetween('start_stamp', [$startDate, $endDate])
                                      ->sum('duration'),
            'avg_duration' => XmlCdr::whereBetween('start_stamp', [$startDate, $endDate])
                                    ->avg('duration'),
            'calls_by_day' => XmlCdr::whereBetween('start_stamp', [$startDate, $endDate])
                                    ->selectRaw('DATE(start_stamp) as date, COUNT(*) as count')
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->get(),
        ];

        // Get AI summary if enabled
        if (config('services.ai_agent.enabled')) {
            $recentCdrs = XmlCdr::whereBetween('start_stamp', [$startDate, $endDate])->get();
            $stats['ai_summary'] = $this->aiService->generateCallSummary($recentCdrs);
        }

        return response()->json($stats);
    }

    /**
     * Export CDR data
     */
    public function export(Request $request)
    {
        $query = XmlCdr::query();

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('start_stamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('start_stamp', '<=', $request->end_date);
        }

        $cdrs = $query->orderBy('start_stamp', 'desc')->get();

        // Generate CSV
        $filename = 'cdr_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($cdrs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Start Time', 'Caller ID Name', 'Caller ID Number', 
                'Destination', 'Direction', 'Duration', 'Status'
            ]);

            foreach ($cdrs as $cdr) {
                fputcsv($file, [
                    $cdr->start_stamp,
                    $cdr->caller_id_name,
                    $cdr->caller_id_number,
                    $cdr->destination_number,
                    $cdr->direction,
                    $cdr->duration,
                    $cdr->hangup_cause,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

