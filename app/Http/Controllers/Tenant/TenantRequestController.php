<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceRequest; // 👈 make sure this matches your model name
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class TenantRequestController extends Controller
{
    public function index()
    {
        // Get all maintenance requests of the tenant
        $requests = \App\Models\MaintenanceRequest::where('tenant_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Get tenant's application
        $tenantApplication = \App\Models\TenantApplication::where('user_id', Auth::id())->first();

        $unitType = $tenantApplication?->unit_type ?? '';
        $roomNo   = $tenantApplication?->room_no ?? '';

        return view('tenant.request', compact('requests', 'unitType', 'roomNo'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'urgency' => 'required|string',
            'supposed_date' => 'required|date',
            'unit_type' => 'required|string',
            'room_no' => 'nullable|string', // optional now
        ]);


        \App\Models\MaintenanceRequest::create([
            'tenant_id' => Auth::id(),
            'unit_type' => $request->unit_type,
            'room_no' => $request->room_no,
            'description' => $request->description,
            'urgency' => $request->urgency,
            'supposed_date' => $request->supposed_date,
            'status' => 'Pending',
        ]);

        return redirect()->route('tenant.requests')->with('success', 'Request submitted successfully!');
    }

}
