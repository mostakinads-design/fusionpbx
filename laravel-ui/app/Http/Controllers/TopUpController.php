<?php

namespace App\Http\Controllers;

use App\Models\UserBalance;
use App\Models\TopUpPackage;
use App\Models\BalanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TopUpController extends Controller
{
    /**
     * Show top-up packages
     */
    public function index(Request $request)
    {
        $userBalance = null;
        
        if ($request->has('user_uuid')) {
            $userBalance = UserBalance::where('user_uuid', $request->user_uuid)->first();
        }

        $packages = TopUpPackage::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('price')
            ->get();
        
        return view('billing.topup.index', compact('packages', 'userBalance'));
    }

    /**
     * Process top-up
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'user_uuid' => 'required|exists:v_users,user_uuid',
            'package_uuid' => 'nullable|exists:topup_packages,package_uuid',
            'custom_amount' => 'nullable|numeric|min:1',
            'payment_method' => 'required|string|in:credit_card,paypal,bank_transfer,cash',
        ]);

        $balance = UserBalance::firstOrCreate(
            ['user_uuid' => $validated['user_uuid']],
            [
                'balance_uuid' => (string) Str::uuid(),
                'domain_uuid' => \App\Models\FusionUser::find($validated['user_uuid'])->domain_uuid,
                'balance' => 0,
                'currency' => 'USD',
            ]
        );

        // Determine amount to add
        $amountToAdd = 0;
        $description = 'Top-up';

        if (isset($validated['package_uuid'])) {
            $package = TopUpPackage::findOrFail($validated['package_uuid']);
            $amountToAdd = $package->total_amount;
            $description = "Top-up: {$package->package_name}";
        } elseif (isset($validated['custom_amount'])) {
            $amountToAdd = $validated['custom_amount'];
            $description = 'Custom top-up';
        }

        if ($amountToAdd <= 0) {
            return redirect()->back()->with('error', 'Invalid amount');
        }

        // Create transaction
        $transaction = BalanceTransaction::create([
            'transaction_uuid' => (string) Str::uuid(),
            'balance_uuid' => $balance->balance_uuid,
            'user_uuid' => $validated['user_uuid'],
            'transaction_type' => 'credit',
            'amount' => $amountToAdd,
            'balance_before' => $balance->balance,
            'balance_after' => $balance->balance + $amountToAdd,
            'description' => $description,
            'payment_method' => $validated['payment_method'],
            'status' => 'completed', // In production, this would be 'pending' until payment is confirmed
            'metadata' => [
                'package_uuid' => $validated['package_uuid'] ?? null,
            ],
        ]);

        // Update balance
        $balance->increment('balance', $amountToAdd);

        return redirect()->route('topup.success', ['transaction' => $transaction->transaction_uuid])
            ->with('success', 'Top-up successful! Amount added: $' . number_format($amountToAdd, 2));
    }

    /**
     * Show success page
     */
    public function success($transactionUuid)
    {
        $transaction = BalanceTransaction::with('user', 'balance')
            ->findOrFail($transactionUuid);
        
        return view('billing.topup.success', compact('transaction'));
    }

    /**
     * Manage top-up packages (admin)
     */
    public function managePackages()
    {
        $packages = TopUpPackage::with('domain')
            ->orderBy('display_order')
            ->get();
        
        return view('billing.topup.manage', compact('packages'));
    }

    /**
     * Create new package
     */
    public function createPackage(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'package_name' => 'required|string|max:255',
            'package_description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'bonus_amount' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'validity_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        $validated['package_uuid'] = (string) Str::uuid();
        $validated['bonus_amount'] = $validated['bonus_amount'] ?? 0;
        $validated['is_active'] = $request->has('is_active');
        $validated['display_order'] = $validated['display_order'] ?? 0;
        
        TopUpPackage::create($validated);

        return redirect()->route('topup.manage')
            ->with('success', 'Package created successfully.');
    }

    /**
     * Update package
     */
    public function updatePackage(Request $request, TopUpPackage $package)
    {
        $validated = $request->validate([
            'package_name' => 'required|string|max:255',
            'package_description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'bonus_amount' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'validity_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        $package->update($validated);

        return redirect()->route('topup.manage')
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Delete package
     */
    public function deletePackage(TopUpPackage $package)
    {
        $package->delete();

        return redirect()->route('topup.manage')
            ->with('success', 'Package deleted successfully.');
    }
}
