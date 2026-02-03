@extends('layout')
@section('title', 'Create DID')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">📞 Add New DID</h1>
</div>
<div class="bg-white shadow rounded-lg p-6 max-w-4xl">
    <form method="POST" action="{{ route('dids.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
                <label class="block text-sm font-medium mb-2">DID Number *</label>
                <input type="text" name="did_number" required class="w-full rounded-md border-gray-300">
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Routing Type *</label>
            <div class="grid grid-cols-3 gap-4">
                <label class="flex items-center p-3 border rounded cursor-pointer">
                    <input type="radio" name="routing_type" value="voice_only" required class="mr-2">
                    <span>📞 Voice Only</span>
                </label>
                <label class="flex items-center p-3 border rounded cursor-pointer">
                    <input type="radio" name="routing_type" value="sms_only" class="mr-2">
                    <span>💬 SMS Only</span>
                </label>
                <label class="flex items-center p-3 border rounded cursor-pointer">
                    <input type="radio" name="routing_type" value="voice_and_sms" class="mr-2">
                    <span>📞💬 Both</span>
                </label>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Destination Type *</label>
                <select name="destination_type" required class="w-full rounded-md border-gray-300">
                    <option value="">Select Type</option>
                    <option value="extension">Extension</option>
                    <option value="ivr">IVR Menu</option>
                    <option value="queue">Call Queue</option>
                    <option value="external">External Number</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Destination Number *</label>
                <input type="text" name="destination_number" required class="w-full rounded-md border-gray-300">
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Description</label>
            <textarea name="did_description" rows="2" class="w-full rounded-md border-gray-300"></textarea>
        </div>
        
        <div class="flex items-center gap-4 mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="record_calls" class="rounded mr-2">
                <span>Record Calls</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_active" checked class="rounded mr-2">
                <span>Active</span>
            </label>
        </div>
        
        <div class="flex justify-end gap-4">
            <a href="{{ route('dids.index') }}" class="bg-gray-600 text-white py-2 px-6 rounded">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded">Create DID</button>
        </div>
    </form>
</div>
@endsection
