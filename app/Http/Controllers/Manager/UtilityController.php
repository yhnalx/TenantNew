<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    // Display list of tenants with utility balances
    public function index()
    {
        $leases = Lease::with('tenant')
            ->whereHas('tenant', fn($q) => $q->where('role', 'tenant')->where('status', 'approved'))
            ->get()
            ->unique('user_id');


        return view('manager.utilities.index', compact('leases'));
    }

    public function updateUtilityBalance(Request $request, $id)
    {
        try {
            \Log::info('UpdateUtilityBalance called', $request->all());  // ✅ Add logging

            $request->validate([
                'utility_balance' => 'required|numeric|min:0',
                'proof_of_utility_billing' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $lease = Lease::findOrFail($id);
            $user = $lease->tenant;

            if (!$user) {
                return response()->json(['success' => false, 'error' => 'Tenant not found'], 404);
            }

            $pathToProof = $request->hasFile('proof_of_utility_billing')
                ? $request->file('proof_of_utility_billing')->store('utility_proofs', 'public')
                : null;

            $user->utility_balance = $request->utility_balance;
            if ($pathToProof) {
                $user->proof_of_utility_billing = $pathToProof;
            }
            $user->save();

            return response()->json([
                'success' => true,
                'new_balance' => $user->utility_balance,
                'message' => 'Utility balance updated successfully!' . ($pathToProof ? ' Proof uploaded.' : '')
            ]);
        } catch (\Exception $e) {
            \Log::error('UpdateUtilityBalance error: ' . $e->getMessage());  // ✅ Enhanced logging
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }



}
