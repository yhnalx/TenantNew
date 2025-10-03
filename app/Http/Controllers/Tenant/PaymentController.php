<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // Tenant\PaymentController.php
    public function index()
    {
        $payments = Payment::where('tenant_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if Deposit payment already exists
        $depositExists = $payments->where('payment_for', 'Deposit')->count() > 0;

        return view('tenant.payments', compact('payments', 'depositExists'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'pay_method'   => 'required|string',
            'payment_for'  => 'required|string',
            'pay_amount'   => 'required|numeric|min:1',
            'proof'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'account_no'   => 'nullable|string|max:255',
        ]);

        $pathToProof = null;
        if ($request->hasFile('proof')) {
            $pathToProof = $request->file('proof')->store('proofs', 'public');
        }

        $lease = \App\Models\Lease::where('tenant_id', Auth::id())->first();

        // Generate reference number (example: PAY-20251003-5F3A2)
        $referenceNumber = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

        Payment::create([
            'tenant_id'        => Auth::id(),
            'lease_id'         => $lease?->id,
            'pay_date'         => now(),
            'pay_amount'       => $request->pay_amount,
            'pay_method'       => $request->pay_method,
            'pay_status'       => 'Pending',
            'proof'            => $pathToProof,
            'payment_for'      => $request->payment_for,
            'account_no'       => $request->account_no,
            'reference_number' => $referenceNumber,
        ]);

        return redirect()->route('tenant.payments')
            ->with('success', 'Payment submitted successfully! Reference Number: ' . $referenceNumber);
    }

}
