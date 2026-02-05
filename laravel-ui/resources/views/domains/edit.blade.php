@extends('layouts.app')

@section('title', 'Edit Domain')

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Domains
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-2xl font-bold text-gray-800 mb-6">Edit Domain: {{ $domain->name }}</h3>

    <form action="{{ route('domains.update', $domain->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Domain Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Domain Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $domain->name) }}" required
                       placeholder="example.com"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input type="text" name="description" id="description" value="{{ old('description', $domain->description) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Enabled Status -->
        <div class="mt-6">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="enabled" value="1" {{ old('enabled', $domain->enabled ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-700">Domain Enabled</span>
            </label>
        </div>

        <!-- Domain Statistics -->
        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Domain Statistics</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Users</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $domain->users_count ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Extensions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $domain->extensions_count ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Queues</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $domain->queues_count ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Campaigns</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $domain->campaigns_count ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('domains.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Update Domain
            </button>
        </div>
    </form>
</div>
@endsection
