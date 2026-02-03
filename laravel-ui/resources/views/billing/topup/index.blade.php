@extends('layout')
@section('title', 'Top-Up Balance')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">💰 Top-Up Balance</h1>
</div>
@if($userBalance)
<div class="bg-blue-600 text-white shadow rounded-lg p-6 mb-8">
    <h2 class="text-lg">Current Balance</h2>
    <p class="text-4xl font-bold mt-2">${{ number_format($userBalance->balance, 2) }}</p>
</div>
@endif
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Custom Amount Top-Up</h2>
    <form method="POST" action="{{ route('billing.topup.process') }}" class="max-w-md">
        @csrf
        <input type="hidden" name="user_uuid" value="{{ request('user_uuid') ?? ($userBalance->user_uuid ?? '') }}">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Amount (USD)</label>
            <input type="number" name="custom_amount" min="1" step="0.01" required class="w-full rounded-md border-gray-300" placeholder="Enter amount">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Payment Method</label>
            <select name="payment_method" required class="w-full rounded-md border-gray-300">
                <option value="">Select...</option>
                <option value="credit_card">💳 Credit Card</option>
                <option value="paypal">PayPal</option>
                <option value="cash">Cash</option>
            </select>
        </div>
        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg">
            Top-Up Now
        </button>
    </form>
</div>
@endsection
