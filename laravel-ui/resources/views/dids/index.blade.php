@extends('layout')
@section('title', 'DID Management')
@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">📞 DID Management</h1>
        <p class="mt-2 text-gray-600">Manage Direct Inward Dialing numbers</p>
    </div>
    <a href="{{ route('dids.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Add New DID
    </a>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DID Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Routing Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($dids as $did)
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium">{{ $did->did_number }}</div>
                    <div class="text-xs text-gray-500">{{ $did->did_description }}</div>
                </td>
                <td class="px-6 py-4">
                    @if($did->voice_enabled)<span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Voice</span>@endif
                    @if($did->sms_enabled)<span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded ml-1">SMS</span>@endif
                </td>
                <td class="px-6 py-4">
                    <span class="font-medium">{{ ucfirst($did->destination_type) }}:</span> {{ $did->destination_number }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded {{ $did->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $did->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('dids.edit', $did->did_uuid) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                    <form action="{{ route('dids.destroy', $did->did_uuid) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this DID?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No DIDs found. <a href="{{ route('dids.create') }}" class="text-blue-600">Create one</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $dids->links() }}</div>
@endsection
