@extends('layouts.app')

@section('title', 'CDR Records - FusionPBX Admin Panel')

@section('content')
<div x-data="cdrList()" x-init="init()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Call Detail Records</h1>
        <p class="mt-1 text-sm text-gray-600">View and analyze call history with AI-powered insights</p>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('cdr.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
            <div>
                <label for="direction" class="block text-sm font-medium text-gray-700">Direction</label>
                <select name="direction" id="direction" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All</option>
                    <option value="inbound" {{ request('direction') === 'inbound' ? 'selected' : '' }}>Inbound</option>
                    <option value="outbound" {{ request('direction') === 'outbound' ? 'selected' : '' }}>Outbound</option>
                    <option value="local" {{ request('direction') === 'local' ? 'selected' : '' }}>Local</option>
                </select>
            </div>
            
            <div>
                <label for="caller_id" class="block text-sm font-medium text-gray-700">Caller ID</label>
                <input type="text" name="caller_id" id="caller_id" value="{{ request('caller_id') }}" 
                       placeholder="Search caller..." 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    🔍 Filter
                </button>
            </div>
        </form>
        
        <div class="mt-4 flex justify-between items-center">
            <a href="{{ route('cdr.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">Clear Filters</a>
            <a href="{{ route('cdr.export') }}?{{ http_build_query(request()->all()) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                📥 Export CSV
            </a>
        </div>
    </div>

    <!-- CDR Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Time
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Caller
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Destination
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Direction
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Duration
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cdrs as $cdr)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $cdr->start_stamp ? $cdr->start_stamp->format('Y-m-d H:i:s') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $cdr->caller_id_name ?: 'Unknown' }}</div>
                            <div class="text-sm text-gray-500">{{ $cdr->caller_id_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $cdr->destination_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $cdr->direction === 'inbound' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $cdr->direction === 'outbound' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $cdr->direction === 'local' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($cdr->direction) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ gmdate('H:i:s', $cdr->duration) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cdr->hangup_cause === 'NORMAL_CLEARING')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Answered
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ✗ {{ $cdr->hangup_cause }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button @click="analyzeCall('{{ $cdr->xml_cdr_uuid }}')" 
                                    class="text-indigo-600 hover:text-indigo-900">
                                🤖 AI Analyze
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No CDR records found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $cdrs->links() }}
        </div>
    </div>

    <!-- AI Analysis Modal -->
    <div x-show="showAnalysis" x-cloak 
         class="fixed z-10 inset-0 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAnalysis = false"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="text-2xl">🤖</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                AI Call Analysis
                            </h3>
                            <div class="mt-2">
                                <div x-show="analysisLoading" class="text-center py-4">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                                    <p class="mt-2 text-sm text-gray-500">Analyzing with AI...</p>
                                </div>
                                <div x-show="!analysisLoading && analysisResult" class="text-sm text-gray-500 whitespace-pre-wrap" x-html="analysisResult"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showAnalysis = false" 
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cdrList() {
    return {
        showAnalysis: false,
        analysisLoading: false,
        analysisResult: '',
        init() {
            console.log('CDR List initialized');
        },
        async analyzeCall(cdrId) {
            this.showAnalysis = true;
            this.analysisLoading = true;
            this.analysisResult = '';
            
            try {
                const response = await fetch(`/cdr/${cdrId}/analyze`);
                const data = await response.json();
                
                if (data.success) {
                    this.analysisResult = data.analysis;
                } else {
                    this.analysisResult = 'Error: ' + (data.error || 'Unable to analyze call');
                }
            } catch (error) {
                this.analysisResult = 'Error: Failed to connect to AI service';
                console.error('Analysis error:', error);
            } finally {
                this.analysisLoading = false;
            }
        }
    }
}
</script>
@endpush
