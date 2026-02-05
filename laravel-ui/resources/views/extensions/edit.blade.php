@extends('layouts.app')

@section('title', 'Edit Extension')

@section('content')
<div class="mb-6">
    <a href="{{ route('extensions.index') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Extensions
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-2xl font-bold text-gray-800 mb-6">Edit Extension: {{ $extension->extension }}</h3>

    <form action="{{ route('extensions.update', $extension->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Extension Number -->
            <div>
                <label for="extension" class="block text-sm font-medium text-gray-700 mb-2">Extension Number *</label>
                <input type="text" name="extension" id="extension" value="{{ old('extension', $extension->extension) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('extension') border-red-500 @enderror">
                @error('extension')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input type="text" name="description" id="description" value="{{ old('description', $extension->description) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">
                @error('description')
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
                    <option value="{{ $domain->id }}" {{ old('domain_id', $extension->domain_id) == $domain->id ? 'selected' : '' }}>
                        {{ $domain->name }}
                    </option>
                    @endforeach
                </select>
                @error('domain_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- User -->
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Assign to User</label>
                <select name="user_id" id="user_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('user_id') border-red-500 @enderror">
                    <option value="">Select User (Optional)</option>
                    @foreach($users ?? [] as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $extension->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->username }})
                    </option>
                    @endforeach
                </select>
                @error('user_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    SIP Password <span class="text-gray-500 text-xs">(leave blank to keep current)</span>
                </label>
                <input type="text" name="password" id="password" value="{{ old('password') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <button type="button" onclick="generatePassword()" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-random mr-1"></i> Generate Password
                </button>
            </div>

            <!-- Voicemail Enabled -->
            <div class="flex items-center">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="voicemail_enabled" value="1" {{ old('voicemail_enabled', $extension->voicemail_enabled ?? true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Enable Voicemail</span>
                </label>
            </div>
        </div>

        <!-- Enabled Status -->
        <div class="mt-6">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="enabled" value="1" {{ old('enabled', $extension->enabled ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-700">Extension Enabled</span>
            </label>
        </div>

        <!-- Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('extensions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Update Extension
            </button>
        </div>
    </form>
</div>

<script>
function generatePassword() {
    const length = 16;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    document.getElementById('password').value = password;
}
</script>
@endsection
