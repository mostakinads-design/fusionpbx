<?php

namespace App\Http\Controllers;

use App\Models\XmlCdr;
use Illuminate\Http\Request;

class CdrController extends Controller
{
    public function index(Request $request)
    {
        $query = XmlCdr::with(['domain', 'extension']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('caller_id_number', 'like', "%{$search}%")
                  ->orWhere('destination_number', 'like', "%{$search}%")
                  ->orWhere('caller_id_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        if ($request->has('date_from')) {
            $query->whereDate('start_stamp', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('start_stamp', '<=', $request->date_to);
        }

        if ($request->has('hangup_cause')) {
            $query->where('hangup_cause', $request->hangup_cause);
        }

        $cdrs = $query->orderBy('start_stamp', 'desc')->paginate(15);

        return view('cdr.index', compact('cdrs'));
    }

    public function show(XmlCdr $cdr)
    {
        $cdr->load(['domain', 'extension']);
        return view('cdr.show', compact('cdr'));
    }

    public function export(Request $request)
    {
        $query = XmlCdr::with(['domain', 'extension']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('caller_id_number', 'like', "%{$search}%")
                  ->orWhere('destination_number', 'like', "%{$search}%")
                  ->orWhere('caller_id_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        if ($request->has('date_from')) {
            $query->whereDate('start_stamp', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('start_stamp', '<=', $request->date_to);
        }

        if ($request->has('hangup_cause')) {
            $query->where('hangup_cause', $request->hangup_cause);
        }

        $cdrs = $query->orderBy('start_stamp', 'desc')->get();

        $filename = 'cdr_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($cdrs) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'UUID',
                'Domain',
                'Direction',
                'Caller ID Name',
                'Caller ID Number',
                'Destination Number',
                'Start Time',
                'Answer Time',
                'End Time',
                'Duration',
                'Billsec',
                'Hangup Cause',
                'Recording File',
            ]);

            foreach ($cdrs as $cdr) {
                fputcsv($file, [
                    $cdr->xml_cdr_uuid,
                    $cdr->domain->domain_name ?? '',
                    $cdr->direction,
                    $cdr->caller_id_name,
                    $cdr->caller_id_number,
                    $cdr->destination_number,
                    $cdr->start_stamp,
                    $cdr->answer_stamp,
                    $cdr->end_stamp,
                    $cdr->duration,
                    $cdr->billsec,
                    $cdr->hangup_cause,
                    $cdr->recording_file,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
