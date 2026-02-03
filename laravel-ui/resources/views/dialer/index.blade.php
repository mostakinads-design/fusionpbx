@extends('layout')

@section('title', 'Auto Dialer - FusionPBX Laravel UI')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Auto Dialer</h1>
    <p class="mt-2 text-gray-600">Modern call center dialer with AI agent integration</p>
</div>

<!-- Agent Selection -->
<div class="bg-white shadow rounded-lg p-6 mb-8">
    <h2 class="text-xl font-semibold mb-4">Select Agent</h2>
    <form method="GET" action="{{ route('dialer.index') }}" class="flex gap-4">
        <select name="agent_uuid" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Select an agent...</option>
            @foreach($agents as $agentOption)
                <option value="{{ $agentOption->agent_uuid }}" {{ $agent && $agent->agent_uuid === $agentOption->agent_uuid ? 'selected' : '' }}>
                    {{ $agentOption->agent_name }} ({{ ucfirst($agentOption->agent_type) }}) - {{ ucfirst($agentOption->agent_status) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
            Select
        </button>
    </form>
</div>

@if($agent)
<!-- Agent Info -->
<div class="bg-white shadow rounded-lg p-6 mb-8">
    <h2 class="text-xl font-semibold mb-4">Agent: {{ $agent->agent_name }}</h2>
    <div class="flex items-center gap-4">
        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $agent->agent_type === 'ai' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
            {{ ucfirst($agent->agent_type) }} Agent
        </span>
        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $agent->agent_status === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
            {{ ucfirst(str_replace('_', ' ', $agent->agent_status)) }}
        </span>
    </div>
</div>

@if($nextContact)
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Next Contact</h2>
    <div class="space-y-4">
        <div class="bg-gray-50 p-4 rounded">
            <p class="text-lg font-semibold">{{ $nextContact->first_name }} {{ $nextContact->last_name }}</p>
            <p class="text-lg">{{ $nextContact->phone_number }}</p>
            <p class="text-sm text-gray-600">Campaign: {{ $nextContact->campaign->campaign_name }}</p>
        </div>
        <button onclick="alert('Call initiated!')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
            📞 Call Now
        </button>
    </div>
</div>
@else
<div class="text-center py-8 text-gray-500">
    <p>No contacts available to dial</p>
</div>
@endif

@else
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
    <p class="text-sm text-yellow-700">Please select an agent to start using the dialer.</p>
</div>
@endif
@endsection
