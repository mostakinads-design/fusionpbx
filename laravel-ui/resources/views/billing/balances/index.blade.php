@extends('layout')
@section('title', 'User Balances')
@section('content')
<div class="mb-8 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-900">User Balances</h1>
    <a href="{{ route('billing.topup.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
        Top-Up
    </a>
</div>
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($balances as $balance)
            <tr>
                <td class="px-6 py-4">{{ $balance->user->username ?? 'N/A' }}</td>
                <td class="px-6 py-4 font-bold text-green-600">{{ $balance->currency }} {{ number_format($balance->balance, 2) }}</td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('billing.topup.index', ['user_uuid' => $balance->user_uuid]) }}" class="text-green-600 hover:text-green-900">Top-Up</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No balances found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $balances->links() }}</div>
@endsection
