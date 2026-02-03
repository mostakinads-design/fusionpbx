<?php

namespace App\Http\Controllers;

use App\Models\CallRecord;
use App\Models\Domain;
use App\Models\Extension;
use Illuminate\Http\Request;

class CallRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CallRecord::with('domain', 'extension')
            ->orderBy('start_stamp', 'desc');

        // Filter by domain
        if ($request->has('domain_uuid') && !empty($request->domain_uuid)) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        // Filter by extension
        if ($request->has('extension_uuid') && !empty($request->extension_uuid)) {
            $query->where('extension_uuid', $request->extension_uuid);
        }

        // Filter by direction
        if ($request->has('direction') && !empty($request->direction)) {
            $query->where('direction', $request->direction);
        }

        // Filter by date range
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('start_stamp', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('start_stamp', '<=', $request->end_date);
        }

        $callRecords = $query->paginate(25);
        $domains = Domain::all();
        $extensions = Extension::all();
        
        return view('call-records.index', compact('callRecords', 'domains', 'extensions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(CallRecord $callRecord)
    {
        $callRecord->load('domain', 'extension');
        return view('call-records.show', compact('callRecord'));
    }
}
