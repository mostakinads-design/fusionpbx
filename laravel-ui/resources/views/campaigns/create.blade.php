@extends('layouts.app')

@section('title', 'Create Campaign')

@section('content')
<div class="mb-6">
    <a href="{{ route('campaigns.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Campaigns
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-2xl font-bold text-gray-800 mb-6">Create New Campaign</h3>

    <form action="{{ route('campaigns.store') }}" method="POST">
        @csrf

        <!-- Basic Information -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Campaign Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="domain_id" class="block text-sm font-medium text-gray-700 mb-2">Domain *</label>
                    <select name="domain_id" id="domain_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('domain_id') border-red-500 @enderror">
                        <option value="">Select Domain</option>
                        @foreach($domains ?? [] as $domain)
                        <option value="{{ $domain->id }}" {{ old('domain_id') == $domain->id ? 'selected' : '' }}>
                            {{ $domain->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('domain_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="caller_id_name" class="block text-sm font-medium text-gray-700 mb-2">Caller ID Name</label>
                    <input type="text" name="caller_id_name" id="caller_id_name" value="{{ old('caller_id_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('caller_id_name') border-red-500 @enderror">
                    @error('caller_id_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="caller_id_number" class="block text-sm font-medium text-gray-700 mb-2">Caller ID Number</label>
                    <input type="text" name="caller_id_number" id="caller_id_number" value="{{ old('caller_id_number') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('caller_id_number') border-red-500 @enderror">
                    @error('caller_id_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- AI Configuration -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">AI Configuration</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="ai_provider" class="block text-sm font-medium text-gray-700 mb-2">AI Provider *</label>
                    <select name="ai_provider" id="ai_provider" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ai_provider') border-red-500 @enderror">
                        <option value="">Select Provider</option>
                        <option value="openai" {{ old('ai_provider') == 'openai' ? 'selected' : '' }}>OpenAI</option>
                        <option value="anthropic" {{ old('ai_provider') == 'anthropic' ? 'selected' : '' }}>Anthropic</option>
                        <option value="google" {{ old('ai_provider') == 'google' ? 'selected' : '' }}>Google AI</option>
                    </select>
                    @error('ai_provider')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ai_model" class="block text-sm font-medium text-gray-700 mb-2">AI Model *</label>
                    <input type="text" name="ai_model" id="ai_model" value="{{ old('ai_model', 'gpt-4') }}" required
                           placeholder="e.g., gpt-4, claude-3-opus, gemini-pro"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ai_model') border-red-500 @enderror">
                    @error('ai_model')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="ai_prompt" class="block text-sm font-medium text-gray-700 mb-2">AI Prompt *</label>
                    <textarea name="ai_prompt" id="ai_prompt" rows="6" required
                              placeholder="Enter the instructions for the AI agent. Example: You are a friendly sales representative calling to schedule appointments..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ai_prompt') border-red-500 @enderror">{{ old('ai_prompt') }}</textarea>
                    @error('ai_prompt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ai_voice" class="block text-sm font-medium text-gray-700 mb-2">AI Voice</label>
                    <select name="ai_voice" id="ai_voice"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ai_voice') border-red-500 @enderror">
                        <option value="alloy" {{ old('ai_voice', 'alloy') == 'alloy' ? 'selected' : '' }}>Alloy</option>
                        <option value="echo" {{ old('ai_voice') == 'echo' ? 'selected' : '' }}>Echo</option>
                        <option value="fable" {{ old('ai_voice') == 'fable' ? 'selected' : '' }}>Fable</option>
                        <option value="onyx" {{ old('ai_voice') == 'onyx' ? 'selected' : '' }}>Onyx</option>
                        <option value="nova" {{ old('ai_voice') == 'nova' ? 'selected' : '' }}>Nova</option>
                        <option value="shimmer" {{ old('ai_voice') == 'shimmer' ? 'selected' : '' }}>Shimmer</option>
                    </select>
                    @error('ai_voice')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ai_temperature" class="block text-sm font-medium text-gray-700 mb-2">Temperature (0.0 - 1.0)</label>
                    <input type="number" name="ai_temperature" id="ai_temperature" 
                           value="{{ old('ai_temperature', 0.7) }}" step="0.1" min="0" max="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ai_temperature') border-red-500 @enderror">
                    @error('ai_temperature')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Campaign Settings -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Campaign Settings</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="max_concurrent_calls" class="block text-sm font-medium text-gray-700 mb-2">Max Concurrent Calls</label>
                    <input type="number" name="max_concurrent_calls" id="max_concurrent_calls" 
                           value="{{ old('max_concurrent_calls', 1) }}" min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_concurrent_calls') border-red-500 @enderror">
                    @error('max_concurrent_calls')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="retry_attempts" class="block text-sm font-medium text-gray-700 mb-2">Retry Attempts</label>
                    <input type="number" name="retry_attempts" id="retry_attempts" 
                           value="{{ old('retry_attempts', 3) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('retry_attempts') border-red-500 @enderror">
                    @error('retry_attempts')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="retry_delay" class="block text-sm font-medium text-gray-700 mb-2">Retry Delay (minutes)</label>
                    <input type="number" name="retry_delay" id="retry_delay" 
                           value="{{ old('retry_delay', 60) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('retry_delay') border-red-500 @enderror">
                    @error('retry_delay')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('campaigns.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Create Campaign
            </button>
        </div>
    </form>
</div>
@endsection
