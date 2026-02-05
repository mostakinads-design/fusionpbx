@extends('layouts.app')

@section('title', 'Edit Call Center Agent')

@section('content')
<div class="mb-6">
    <a href="{{ route('call-center-agents.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Agents
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-2xl font-bold text-gray-800 mb-6">Edit Agent: {{ $agent->name }}</h3>

    <form action="{{ route('call-center-agents.update', $agent->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Agent Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Agent Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $agent->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Extension -->
            <div>
                <label for="extension" class="block text-sm font-medium text-gray-700 mb-2">Extension *</label>
                <input type="text" name="extension" id="extension" value="{{ old('extension', $agent->extension) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('extension') border-red-500 @enderror">
                @error('extension')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Domain -->
            <div>
                <label for="domain_id" class="block text-sm font-medium text-gray-700 mb-2">Domain *</label>
                <select name="domain_id" id="domain_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('domain_id') border-red-500 @enderror">
                    <option value="">Select Domain</option>
                    @foreach($domains ?? [] as $domain)
                    <option value="{{ $domain->id }}" {{ old('domain_id', $agent->domain_id) == $domain->id ? 'selected' : '' }}>
                        {{ $domain->name }}
                    </option>
                    @endforeach
                </select>
                @error('domain_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Agent Type *</label>
                <select name="type" id="type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                    <option value="callback" {{ old('type', $agent->type) == 'callback' ? 'selected' : '' }}>Callback</option>
                    <option value="uuid-standby" {{ old('type', $agent->type) == 'uuid-standby' ? 'selected' : '' }}>UUID Standby</option>
                </select>
                @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact -->
            <div>
                <label for="contact" class="block text-sm font-medium text-gray-700 mb-2">Contact</label>
                <input type="text" name="contact" id="contact" value="{{ old('contact', $agent->contact) }}"
                       placeholder="[call_timeout=10]user/1001@domain.com"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contact') border-red-500 @enderror">
                @error('contact')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select name="status" id="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                    <option value="Available" {{ old('status', $agent->status) == 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="On Break" {{ old('status', $agent->status) == 'On Break' ? 'selected' : '' }}>On Break</option>
                    <option value="Do Not Disturb" {{ old('status', $agent->status) == 'Do Not Disturb' ? 'selected' : '' }}>Do Not Disturb</option>
                    <option value="Logged Out" {{ old('status', $agent->status) == 'Logged Out' ? 'selected' : '' }}>Logged Out</option>
                </select>
                @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Max No Answer -->
            <div>
                <label for="max_no_answer" class="block text-sm font-medium text-gray-700 mb-2">Max No Answer</label>
                <input type="number" name="max_no_answer" id="max_no_answer" value="{{ old('max_no_answer', $agent->max_no_answer ?? 3) }}" min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_no_answer') border-red-500 @enderror">
                @error('max_no_answer')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Number of missed calls before agent is logged out (0 = unlimited)</p>
            </div>

            <!-- Wrap Up Time -->
            <div>
                <label for="wrap_up_time" class="block text-sm font-medium text-gray-700 mb-2">Wrap Up Time (seconds)</label>
                <input type="number" name="wrap_up_time" id="wrap_up_time" value="{{ old('wrap_up_time', $agent->wrap_up_time ?? 10) }}" min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('wrap_up_time') border-red-500 @enderror">
                @error('wrap_up_time')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('call-center-agents.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Update Agent
            </button>
        </div>
    </form>
</div>
@endsection
