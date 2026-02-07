@extends('layouts.app')

@section('title', 'Edit Call Center Queue')

@section('content')
<div class="mb-6">
    <a href="{{ route('call-center-queues.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Queues
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-2xl font-bold text-gray-800 mb-6">Edit Queue: {{ $queue->name }}</h3>

    <form action="{{ route('call-center-queues.update', $queue->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Queue Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $queue->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="extension" class="block text-sm font-medium text-gray-700 mb-2">Extension *</label>
                    <input type="text" name="extension" id="extension" value="{{ old('extension', $queue->extension) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('extension') border-red-500 @enderror">
                    @error('extension')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $queue->description) }}</textarea>
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
                        <option value="{{ $domain->id }}" {{ old('domain_id', $queue->domain_id) == $domain->id ? 'selected' : '' }}>
                            {{ $domain->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('domain_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="strategy" class="block text-sm font-medium text-gray-700 mb-2">Strategy *</label>
                    <select name="strategy" id="strategy" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('strategy') border-red-500 @enderror">
                        <option value="">Select Strategy</option>
                        <option value="ring-all" {{ old('strategy', $queue->strategy) == 'ring-all' ? 'selected' : '' }}>Ring All</option>
                        <option value="longest-idle-agent" {{ old('strategy', $queue->strategy) == 'longest-idle-agent' ? 'selected' : '' }}>Longest Idle Agent</option>
                        <option value="round-robin" {{ old('strategy', $queue->strategy) == 'round-robin' ? 'selected' : '' }}>Round Robin</option>
                        <option value="top-down" {{ old('strategy', $queue->strategy) == 'top-down' ? 'selected' : '' }}>Top Down</option>
                        <option value="agent-with-least-talk-time" {{ old('strategy', $queue->strategy) == 'agent-with-least-talk-time' ? 'selected' : '' }}>Agent with Least Talk Time</option>
                        <option value="agent-with-fewest-calls" {{ old('strategy', $queue->strategy) == 'agent-with-fewest-calls' ? 'selected' : '' }}>Agent with Fewest Calls</option>
                        <option value="sequentially-by-agent-order" {{ old('strategy', $queue->strategy) == 'sequentially-by-agent-order' ? 'selected' : '' }}>Sequentially by Agent Order</option>
                        <option value="random" {{ old('strategy', $queue->strategy) == 'random' ? 'selected' : '' }}>Random</option>
                    </select>
                    @error('strategy')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Queue Settings -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Queue Settings</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="timeout" class="block text-sm font-medium text-gray-700 mb-2">Timeout (seconds)</label>
                    <input type="number" name="timeout" id="timeout" value="{{ old('timeout', $queue->timeout ?? 30) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('timeout') border-red-500 @enderror">
                    @error('timeout')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_wait_time" class="block text-sm font-medium text-gray-700 mb-2">Max Wait Time (seconds)</label>
                    <input type="number" name="max_wait_time" id="max_wait_time" value="{{ old('max_wait_time', $queue->max_wait_time ?? 300) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_wait_time') border-red-500 @enderror">
                    @error('max_wait_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_wait_time_with_no_agent" class="block text-sm font-medium text-gray-700 mb-2">Max Wait with No Agent</label>
                    <input type="number" name="max_wait_time_with_no_agent" id="max_wait_time_with_no_agent" value="{{ old('max_wait_time_with_no_agent', $queue->max_wait_time_with_no_agent ?? 60) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_wait_time_with_no_agent') border-red-500 @enderror">
                    @error('max_wait_time_with_no_agent')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tier_rules_apply" class="block text-sm font-medium text-gray-700 mb-2">Tier Rules Apply</label>
                    <select name="tier_rules_apply" id="tier_rules_apply"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="true" {{ old('tier_rules_apply', $queue->tier_rules_apply ?? 'false') == 'true' ? 'selected' : '' }}>True</option>
                        <option value="false" {{ old('tier_rules_apply', $queue->tier_rules_apply ?? 'false') == 'false' ? 'selected' : '' }}>False</option>
                    </select>
                </div>

                <div>
                    <label for="tier_rule_wait_second" class="block text-sm font-medium text-gray-700 mb-2">Tier Rule Wait (seconds)</label>
                    <input type="number" name="tier_rule_wait_second" id="tier_rule_wait_second" value="{{ old('tier_rule_wait_second', $queue->tier_rule_wait_second ?? 30) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="tier_rule_wait_multiply_level" class="block text-sm font-medium text-gray-700 mb-2">Tier Rule Multiply Level</label>
                    <select name="tier_rule_wait_multiply_level" id="tier_rule_wait_multiply_level"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="true" {{ old('tier_rule_wait_multiply_level', $queue->tier_rule_wait_multiply_level ?? 'false') == 'true' ? 'selected' : '' }}>True</option>
                        <option value="false" {{ old('tier_rule_wait_multiply_level', $queue->tier_rule_wait_multiply_level ?? 'false') == 'false' ? 'selected' : '' }}>False</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Music and Announcements -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Music and Announcements</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="moh_sound" class="block text-sm font-medium text-gray-700 mb-2">Music On Hold</label>
                    <input type="text" name="moh_sound" id="moh_sound" value="{{ old('moh_sound', $queue->moh_sound) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="announce_sound" class="block text-sm font-medium text-gray-700 mb-2">Announce Sound</label>
                    <input type="text" name="announce_sound" id="announce_sound" value="{{ old('announce_sound', $queue->announce_sound) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="announce_frequency" class="block text-sm font-medium text-gray-700 mb-2">Announce Frequency (seconds)</label>
                    <input type="number" name="announce_frequency" id="announce_frequency" value="{{ old('announce_frequency', $queue->announce_frequency ?? 60) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="record_template" class="block text-sm font-medium text-gray-700 mb-2">Record Template</label>
                    <input type="text" name="record_template" id="record_template" value="{{ old('record_template', $queue->record_template) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Options -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Options</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="enabled" value="1" {{ old('enabled', $queue->enabled ?? true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Queue Enabled</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="record" value="1" {{ old('record', $queue->record ?? false) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Record Calls</span>
                </label>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('call-center-queues.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Update Queue
            </button>
        </div>
    </form>
</div>
@endsection
