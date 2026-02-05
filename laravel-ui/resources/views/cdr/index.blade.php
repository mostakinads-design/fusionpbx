@extends('layouts.app')

@section('title', 'Call Detail Records')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Call Detail Records (CDR)</h3>
        <p class="text-gray-600">View and analyze call records</p>
    </div>
    <button onclick="exportCDR()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg inline-flex items-center">
        <i class="fas fa-download mr-2"></i> Export CSV
    </button>
</div>

<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Total Calls</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalCalls ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Answered</p>
        <p class="text-3xl font-bold text-green-600">{{ $answeredCalls ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Missed</p>
        <p class="text-3xl font-bold text-red-600">{{ $missedCalls ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-sm text-gray-600 mb-1">Total Duration</p>
        <p class="text-3xl font-bold text-blue-600">{{ gmdate('H:i:s', $totalDuration ?? 0) }}</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="{{ route('cdr.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
            <input type="date" name="start_date" id="start_date" value="{{ request('start_date', date('Y-m-d', strtotime('-7 days'))) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
            <input type="date" name="end_date" id="end_date" value="{{ request('end_date', date('Y-m-d')) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label for="direction" class="block text-sm font-medium text-gray-700 mb-2">Direction</label>
            <select name="direction" id="direction" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Directions</option>
                <option value="inbound" {{ request('direction') == 'inbound' ? 'selected' : '' }}>Inbound</option>
                <option value="outbound" {{ request('direction') == 'outbound' ? 'selected' : '' }}>Outbound</option>
                <option value="local" {{ request('direction') == 'local' ? 'selected' : '' }}>Local</option>
            </select>
        </div>
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                   placeholder="Phone number or caller..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div class="md:col-span-4 flex justify-end space-x-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-search mr-2"></i> Filter
            </button>
            @if(request()->hasAny(['start_date', 'end_date', 'direction', 'search']))
            <a href="{{ route('cdr.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                <i class="fas fa-times mr-2"></i> Clear
            </a>
            @endif
        </div>
    </form>
</div>

<!-- CDR Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Direction</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Caller</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cdrs as $cdr)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>{{ $cdr->start_stamp ? $cdr->start_stamp->format('Y-m-d') : 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ $cdr->start_stamp ? $cdr->start_stamp->format('H:i:s') : '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($cdr->direction == 'inbound')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            <i class="fas fa-arrow-down mr-1"></i> Inbound
                        </span>
                        @elseif($cdr->direction == 'outbound')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-arrow-up mr-1"></i> Outbound
                        </span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Local
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $cdr->caller_id_name ?? 'Unknown' }}</div>
                        <div class="text-sm text-gray-500">{{ $cdr->caller_id_number ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $cdr->destination_number ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ gmdate('H:i:s', $cdr->duration ?? 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($cdr->hangup_cause == 'NORMAL_CLEARING' || $cdr->hangup_cause == 'ANSWER')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Answered
                        </span>
                        @elseif($cdr->hangup_cause == 'NO_ANSWER' || $cdr->hangup_cause == 'USER_NOT_REGISTERED')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            No Answer
                        </span>
                        @elseif($cdr->hangup_cause == 'USER_BUSY')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Busy
                        </span>
                        @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ $cdr->hangup_cause ?? 'Unknown' }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('cdr.show', $cdr->id) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i> View
                        </a>
                        @if($cdr->recording_file)
                        <a href="{{ route('cdr.play-recording', $cdr->id) }}" class="text-green-600 hover:text-green-900 ml-3" title="Play Recording">
                            <i class="fas fa-play-circle"></i> Play
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-phone-volume text-4xl mb-4 text-gray-400"></i>
                        <p class="text-lg">No call records found</p>
                        <p class="text-sm">Try adjusting your filters</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($cdrs->hasPages())
<div class="mt-6">
    {{ $cdrs->links() }}
</div>
@endif

<script>
function exportCDR() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = '{{ route("cdr.index") }}?' + params.toString();
}
</script>
@endsection
