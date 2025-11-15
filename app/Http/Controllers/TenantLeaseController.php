<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Unit;
use Illuminate\Http\Request;

class TenantLeaseController extends Controller
{
    public function index()
    {
        $leases = Lease::where('user_id', auth()->id())->with('unit')->get();
        $availableUnits = Unit::where('status', 'vacant')->get();

        return view('tenant.leasemanagement', compact('leases', 'availableUnits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
        ]);

        $unit = Unit::find($request->unit_id);

        // Create a new lease for this tenant
        Lease::create([
            'user_id' => auth()->id(),
            'unit_id' => $unit->id,
            'lea_start_date' => $request->start_date,
            'lea_end_date' => now()->addYear(),
            'lea_status' => 'pending',
        ]);

        return redirect()->route('tenant.leases')
            ->with('success', 'Your lease application has been submitted!');
    }

}