<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceRequest; // 👈 make sure this matches your model name
use Illuminate\Support\Facades\Auth;

class TenantRequestController extends Controller
{
    public function index()
    {
        // ✅ Get only the current tenant's requests, newest first
        $requests = MaintenanceRequest::where('tenant_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tenant.request', compact('requests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description'   => 'required|string',
            'urgency'       => 'required|in:low,mid,high',
            'supposed_date' => 'required|date|after_or_equal:today',
        ]);

        MaintenanceRequest::create([
            'tenant_id'     => Auth::id(),
            'description'   => $validated['description'],
            'urgency'       => $validated['urgency'],
            'supposed_date' => $validated['supposed_date'],
            'status'        => 'Pending',
        ]);

        return redirect()->route('tenant.requests')
            ->with('success', 'Request submitted successfully!');
    }
}
