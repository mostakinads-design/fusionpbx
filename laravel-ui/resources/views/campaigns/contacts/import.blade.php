@extends('layouts.app')

@section('title', 'Import Campaign Contacts')

@section('content')
<div class="mb-6">
    <a href="{{ route('campaigns.show', $campaign->id) }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Campaign
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-2xl font-bold text-gray-800 mb-6">Import Contacts for: {{ $campaign->name }}</h3>

    <!-- Instructions -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6">
        <h4 class="text-lg font-semibold text-blue-800 mb-2">
            <i class="fas fa-info-circle mr-2"></i> Import Instructions
        </h4>
        <ul class="list-disc list-inside text-blue-800 space-y-2">
            <li>Upload a CSV file with contact information</li>
            <li>Required columns: <strong>name, phone</strong></li>
            <li>Optional columns: <strong>email, custom_field1, custom_field2, custom_field3</strong></li>
            <li>First row should contain column headers</li>
            <li>Phone numbers should be in E.164 format (e.g., +1234567890)</li>
            <li>Maximum file size: 10MB</li>
        </ul>
    </div>

    <!-- Sample CSV -->
    <div class="bg-gray-50 border border-gray-300 rounded-lg p-6 mb-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-3">Sample CSV Format:</h4>
        <pre class="bg-white p-4 rounded border border-gray-200 text-sm overflow-x-auto">name,phone,email,custom_field1
John Doe,+1234567890,john@example.com,VIP Customer
Jane Smith,+0987654321,jane@example.com,Regular Customer
Bob Johnson,+1122334455,bob@example.com,New Lead</pre>
        <a href="{{ route('campaigns.contacts.download-template', $campaign->id) }}" class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800">
            <i class="fas fa-download mr-2"></i> Download Sample Template
        </a>
    </div>

    <!-- Upload Form -->
    <form action="{{ route('campaigns.contacts.upload', $campaign->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-6">
            <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">Select CSV File *</label>
            <div class="flex items-center justify-center w-full">
                <label for="csv_file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                        <p class="text-xs text-gray-500">CSV file (MAX. 10MB)</p>
                        <p id="file-name" class="mt-2 text-sm text-blue-600 font-medium"></p>
                    </div>
                    <input id="csv_file" name="csv_file" type="file" accept=".csv" required class="hidden" onchange="displayFileName(this)" />
                </label>
            </div>
            @error('csv_file')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Options -->
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Import Options</h4>
            <div class="space-y-3">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="skip_duplicates" value="1" {{ old('skip_duplicates', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Skip duplicate phone numbers</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="validate_phones" value="1" {{ old('validate_phones', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Validate phone number format</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="start_immediately" value="1" {{ old('start_immediately') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Start campaign immediately after import</span>
                </label>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('campaigns.show', $campaign->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-upload mr-2"></i> Import Contacts
            </button>
        </div>
    </form>
</div>

<script>
function displayFileName(input) {
    const fileNameDisplay = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        fileNameDisplay.textContent = 'Selected: ' + input.files[0].name;
    } else {
        fileNameDisplay.textContent = '';
    }
}
</script>
@endsection
