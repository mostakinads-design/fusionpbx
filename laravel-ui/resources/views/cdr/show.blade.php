@extends('layouts.app')

@section('title', 'Call Detail')

@section('content')
<div class="mb-6">
    <a href="{{ route('cdr.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to CDR
    </a>
</div>

<!-- Call Overview -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Call Details</h3>
            <p class="text-gray-600 mt-1">{{ $cdr->start_stamp ? $cdr->start_stamp->format('F j, Y \a\t g:i A') : 'N/A' }}</p>
        </div>
        @if($cdr->direction == 'inbound')
        <span class="px-4 py-2 text-lg font-semibold rounded-full bg-blue-100 text-blue-800">
            <i class="fas fa-arrow-down mr-2"></i> Inbound Call
        </span>
        @elseif($cdr->direction == 'outbound')
        <span class="px-4 py-2 text-lg font-semibold rounded-full bg-green-100 text-green-800">
            <i class="fas fa-arrow-up mr-2"></i> Outbound Call
        </span>
        @else
        <span class="px-4 py-2 text-lg font-semibold rounded-full bg-gray-100 text-gray-800">
            Local Call
        </span>
        @endif
    </div>

    <!-- Call Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Caller Information -->
        <div class="border-l-4 border-blue-500 pl-4">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Caller</h4>
            <div class="space-y-2">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="text-lg font-medium text-gray-900">{{ $cdr->caller_id_name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Number</p>
                    <p class="text-lg font-medium text-gray-900">{{ $cdr->caller_id_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Extension</p>
                    <p class="text-lg font-medium text-gray-900">{{ $cdr->caller_extension ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Destination Information -->
        <div class="border-l-4 border-green-500 pl-4">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Destination</h4>
            <div class="space-y-2">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="text-lg font-medium text-gray-900">{{ $cdr->destination_name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Number</p>
                    <p class="text-lg font-medium text-gray-900">{{ $cdr->destination_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Extension</p>
                    <p class="text-lg font-medium text-gray-900">{{ $cdr->destination_extension ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Duration</p>
        <p class="text-2xl font-bold text-gray-800">{{ gmdate('H:i:s', $cdr->duration ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Bill Time</p>
        <p class="text-2xl font-bold text-gray-800">{{ gmdate('H:i:s', $cdr->billsec ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Wait Time</p>
        <p class="text-2xl font-bold text-gray-800">
            {{ $cdr->answer_stamp && $cdr->start_stamp ? gmdate('H:i:s', $cdr->answer_stamp->diffInSeconds($cdr->start_stamp)) : '00:00:00' }}
        </p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Status</p>
        @if($cdr->hangup_cause == 'NORMAL_CLEARING' || $cdr->hangup_cause == 'ANSWER')
        <p class="text-lg font-bold text-green-600">Answered</p>
        @elseif($cdr->hangup_cause == 'NO_ANSWER')
        <p class="text-lg font-bold text-yellow-600">No Answer</p>
        @elseif($cdr->hangup_cause == 'USER_BUSY')
        <p class="text-lg font-bold text-red-600">Busy</p>
        @else
        <p class="text-lg font-bold text-gray-600">{{ $cdr->hangup_cause ?? 'Unknown' }}</p>
        @endif
    </div>
</div>

<!-- Recording -->
@if($cdr->recording_file)
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-microphone mr-2"></i> Call Recording
    </h4>
    <div class="flex items-center space-x-4">
        <audio controls class="w-full">
            <source src="{{ route('cdr.play-recording', $cdr->id) }}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
        <a href="{{ route('cdr.download-recording', $cdr->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg whitespace-nowrap">
            <i class="fas fa-download mr-2"></i> Download
        </a>
    </div>
</div>
@endif

<!-- Technical Details -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">Technical Details</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-600">Call UUID</p>
            <p class="text-sm font-mono text-gray-900">{{ $cdr->uuid ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Start Time</p>
            <p class="text-sm text-gray-900">{{ $cdr->start_stamp ? $cdr->start_stamp->format('Y-m-d H:i:s') : 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Answer Time</p>
            <p class="text-sm text-gray-900">{{ $cdr->answer_stamp ? $cdr->answer_stamp->format('Y-m-d H:i:s') : 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">End Time</p>
            <p class="text-sm text-gray-900">{{ $cdr->end_stamp ? $cdr->end_stamp->format('Y-m-d H:i:s') : 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Hangup Cause</p>
            <p class="text-sm text-gray-900">{{ $cdr->hangup_cause ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Domain</p>
            <p class="text-sm text-gray-900">{{ $cdr->domain->name ?? 'N/A' }}</p>
        </div>
        @if($cdr->codec)
        <div>
            <p class="text-sm text-gray-600">Codec</p>
            <p class="text-sm text-gray-900">{{ $cdr->codec }}</p>
        </div>
        @endif
        @if($cdr->user_agent)
        <div>
            <p class="text-sm text-gray-600">User Agent</p>
            <p class="text-sm text-gray-900">{{ $cdr->user_agent }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
