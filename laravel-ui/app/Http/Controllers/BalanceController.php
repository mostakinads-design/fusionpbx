<?php

namespace App\Http\Controllers;

use App\Models\UserBalance;
use App\Models\BalanceTransaction;
use App\Models\FusionUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BalanceController extends Controller
{
    /**
     * Display user balance dashboard
     */
    public function index(Request $request)
    {
        $query = UserBalance::with('user', 'domain');

        if ($request->has('user_uuid')) {
            $query->where('user_uuid', $request->user_uuid);
        }

        $balances = $query->paginate(15);
        $users = FusionUser::all();
        
        return view('billing.balances.index', compact('balances', 'users'));
    }

    /**
     * Show balance details for a specific user
     */
    public function show(UserBalance $balance)
    {
        $balance->load('user', 'domain', 'transactions');
        
        $stats = [
            'total_credits' => $balance->transactions()
                ->where('transaction_type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_debits' => $balance->transactions()
                ->where('transaction_type', 'debit')
                ->where('status', 'completed')
                ->sum('amount'),
            'transaction_count' => $balance->transactions()->count(),
            'last_transaction' => $balance->transactions()->latest()->first(),
        ];

        $transactions = $balance->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        
        return view('billing.balances.show', compact('balance', 'stats', 'transactions'));
    }

    /**
     * Create balance for a user
     */
    public function create()
    {
        $users = FusionUser::whereDoesntHave('balance')->get();
        return view('billing.balances.create', compact('users'));
    }

    /**
     * Store new balance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_uuid' => 'required|exists:v_users,user_uuid|unique:user_balances,user_uuid',
            'balance' => 'required|numeric|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'low_balance_alert_threshold' => 'nullable|numeric|min:0',
        ]);

        $user = FusionUser::findOrFail($validated['user_uuid']);
        
        $validated['balance_uuid'] = (string) Str::uuid();
        $validated['domain_uuid'] = $user->domain_uuid;
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['low_balance_alert_threshold'] = $validated['low_balance_alert_threshold'] ?? 10;
        
        $balance = UserBalance::create($validated);

        // Create initial transaction
        if ($validated['balance'] > 0) {
            BalanceTransaction::create([
                'transaction_uuid' => (string) Str::uuid(),
                'balance_uuid' => $balance->balance_uuid,
                'user_uuid' => $user->user_uuid,
                'transaction_type' => 'credit',
                'amount' => $validated['balance'],
                'balance_before' => 0,
                'balance_after' => $validated['balance'],
                'description' => 'Initial balance',
                'status' => 'completed',
            ]);
        }

        return redirect()->route('balances.index')
            ->with('success', 'Balance created successfully.');
    }

    /**
     * Update balance settings
     */
    public function update(Request $request, UserBalance $balance)
    {
        $validated = $request->validate([
            'credit_limit' => 'nullable|numeric|min:0',
            'auto_recharge_enabled' => 'boolean',
            'auto_recharge_amount' => 'nullable|numeric|min:0',
            'auto_recharge_threshold' => 'nullable|numeric|min:0',
            'low_balance_alert_threshold' => 'nullable|numeric|min:0',
        ]);

        $validated['auto_recharge_enabled'] = $request->has('auto_recharge_enabled');
        
        $balance->update($validated);

        return redirect()->route('balances.show', $balance)
            ->with('success', 'Balance settings updated successfully.');
    }

    /**
     * Show transactions for a user
     */
    public function transactions(Request $request)
    {
        $query = BalanceTransaction::with('user', 'balance');

        if ($request->has('user_uuid')) {
            $query->where('user_uuid', $request->user_uuid);
        }

        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(25);
        $users = FusionUser::all();
        
        return view('billing.transactions.index', compact('transactions', 'users'));
    }
}
