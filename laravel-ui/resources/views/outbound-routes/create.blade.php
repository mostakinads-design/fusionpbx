@extends('layout')
@section('title', 'Create Outbound Route')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">📞 Create Outbound Route</h1>
    <p class="mt-2 text-gray-600">Configure routing rules with AI optimization</p>
</div>

<div class="bg-white shadow rounded-lg p-6 max-w-4xl">
    <form method="POST" action="{{ route('outbound-routes.store') }}">
        @csrf
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2">Domain *</label>
                <select name="domain_uuid" required class="w-full rounded-md border-gray-300">
                    <option value="">Select Domain</option>
                    @foreach($domains as $domain)
                        <option value="{{ $domain->domain_uuid }}">{{ $domain->domain_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Route Name *</label>
                <input type="text" name="route_name" required class="w-full rounded-md border-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2">Route Order *</label>
                <input type="number" name="route_order" required min="0" value="0" class="w-full rounded-md border-gray-300">
                <p class="text-xs text-gray-500 mt-1">Lower numbers = higher priority</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Destination Pattern *</label>
                <input type="text" name="destination_pattern" required placeholder="^(\d{11})$" class="w-full rounded-md border-gray-300">
                <p class="text-xs text-gray-500 mt-1">Regex pattern (X = any digit)</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Route Type *</label>
                <select name="route_type" required class="w-full rounded-md border-gray-300">
                    <option value="voice">Voice Only</option>
                    <option value="sms">SMS Only</option>
                    <option value="both">Voice & SMS</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2">Gateway Name *</label>
                <input type="text" name="gateway_name" required class="w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Dial Prefix</label>
                <input type="text" name="dial_prefix" class="w-full rounded-md border-gray-300" placeholder="e.g., 1">
            </div>
        </div>

        <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
            <h3 class="font-semibold text-lg mb-4">🤖 AI-Powered Routing</h3>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="ai_routing_enabled" id="ai_routing" class="rounded mr-2">
                    <span class="font-medium">Enable AI Route Optimization</span>
                </label>
                <p class="text-xs text-gray-600 ml-6 mt-1">AI will analyze call patterns and optimize routing automatically</p>
            </div>

            <div id="ai_options" class="hidden grid grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center p-3 border rounded bg-white">
                        <input type="checkbox" name="ai_cost_optimization" class="rounded mr-2">
                        <div>
                            <div class="font-medium">💰 Cost Optimization</div>
                            <div class="text-xs text-gray-600">Prefer lower-cost routes</div>
                        </div>
                    </label>
                </div>
                <div>
                    <label class="flex items-center p-3 border rounded bg-white">
                        <input type="checkbox" name="ai_quality_optimization" class="rounded mr-2">
                        <div>
                            <div class="font-medium">⭐ Quality Optimization</div>
                            <div class="text-xs text-gray-600">Prefer higher-quality routes</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium mb-2">Caller ID Name</label>
                <input type="text" name="caller_id_name" class="w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Caller ID Number</label>
                <input type="text" name="caller_id_number" class="w-full rounded-md border-gray-300">
            </div>
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" checked class="rounded mr-2">
                <span class="font-medium">Route Active</span>
            </label>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('outbound-routes.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded">Create Route</button>
        </div>
    </form>
</div>

<script>
document.getElementById('ai_routing').addEventListener('change', function() {
    document.getElementById('ai_options').classList.toggle('hidden', !this.checked);
});
</script>
@endsection
