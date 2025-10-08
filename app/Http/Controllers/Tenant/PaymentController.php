<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display tenant payments dashboard
     */
    public function index()
    {
        $tenant = Auth::user();

        $payments = $tenant->payments()->orderBy('created_at', 'desc')->get();

        // Show Deposit option only if deposit is not fully paid
        $depositExists = $tenant->deposit_amount > 0;

        // User balances
        $unpaidRent = $tenant->rent_balance;
        $unpaidUtilities = $tenant->utility_balance;
        $depositBalance = $tenant->deposit_amount;

        return view('tenant.payments', compact(
            'payments',
            'depositExists',
            'unpaidRent',
            'unpaidUtilities',
            'depositBalance'
        ));
    }

    /**
     * Store a new payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'pay_method'  => 'required|string',
            'payment_for' => 'required|string',
            'pay_amount'  => 'required|numeric|min:1',
            'proof'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'account_no'  => 'nullable|string|max:255',
        ]);

        $tenant = Auth::user();
        $lease = Lease::where('user_id', $tenant->id)->latest()->first();

        // Force Deposit payment first
        if ($tenant->deposit_amount > 0 && $request->payment_for !== 'Deposit') {
            return redirect()->back()->with('error', 'You must pay the Deposit first.');
        }

        $pathToProof = $request->hasFile('proof')
            ? $request->file('proof')->store('proofs', 'public')
            : null;

        $referenceNumber = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

        // Create payment record
        $payment = $tenant->payments()->create([
            'lease_id'         => $lease?->id,
            'pay_date'         => now(),
            'pay_amount'       => $request->pay_amount,
            'pay_method'       => $request->pay_method,
            'pay_status'       => 'Paid',
            'proof'            => $pathToProof,
            'payment_for'      => $request->payment_for,
            'account_no'       => $request->account_no,
            'reference_number' => $referenceNumber,
        ]);

        // Update tenant balances
        switch ($request->payment_for) {
            case 'Deposit':
                // Clear deposit
                $tenant->deposit_amount = 0;
                // Clear rent balance because deposit is fully paid
                $tenant->rent_balance = 0;
                break;

            case 'Rent':
                $tenant->rent_balance = max(0, $tenant->rent_balance - $request->pay_amount);
                break;

            case 'Utilities':
                $tenant->utility_balance = max(0, $tenant->utility_balance - $request->pay_amount);
                break;
        }

        $tenant->save();

        return redirect()->route('tenant.payments')
            ->with('success', 'Payment submitted successfully! Reference Number: ' . $referenceNumber);
    }

}
