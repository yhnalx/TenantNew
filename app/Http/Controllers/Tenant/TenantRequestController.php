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
            'description'   => 'required|string',
            'urgency'       => 'required|string',
            'supposed_date' => 'required|date',
            'unit_type'     => 'required|string',
            'room_no'       => 'nullable|string',
            'issue_image'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ✅ Store the image (same pattern as your working example)
        $issueImagePath = null;
        if ($request->hasFile('issue_image')) {
            $issueImagePath = $request->file('issue_image')->store('issues', 'public');
        }

        // ✅ Save to database
        \App\Models\MaintenanceRequest::create([
            'tenant_id'     => Auth::id(),
            'unit_type'     => $request->unit_type,
            'room_no'       => $request->room_no,
            'description'   => $request->description,
            'urgency'       => $request->urgency,
            'supposed_date' => $request->supposed_date,
            'status'        => 'Pending',
            'issue_image'   => $issueImagePath, // ✅ path stored here
        ]);

        return redirect()->route('tenant.requests')->with('success', 'Request submitted successfully!');
    }

    public function cancel($id)
    {
        $request = \App\Models\MaintenanceRequest::where('tenant_id', Auth::id())
                        ->where('id', $id)
                        ->firstOrFail();

        if (in_array($request->status, ['Pending', 'In Progress'])) {
            $request->update(['status' => 'Cancelled']);
            return redirect()->back()->with('success', 'Request cancelled successfully.');
        }

        return redirect()->back()->with('error', 'Unable to cancel this request.');
    }




    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'description' => 'required|string',
    //         'urgency' => 'required|string',
    //         'supposed_date' => 'required|date',
    //         'unit_type' => 'required|string',
    //         'room_no' => 'nullable|string', // optional now
    //         'issue_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // ✅ validation for image
    //     ]);

    //     $pathToIssue = $request->hasFile('issue_image')
    //     ? $request->file('issue_image')->store('issues', 'public')
    //     : null;

    //     \App\Models\MaintenanceRequest::create([
    //         'tenant_id' => Auth::id(),
    //         'unit_type' => $request->unit_type,
    //         'room_no' => $request->room_no,
    //         'description' => $request->description,
    //         'urgency' => $request->urgency,
    //         'supposed_date' => $request->supposed_date,
    //         'status' => 'Pending',
    //         'issue_image'   => $pathToIssue, // ✅ save path
    //     ]);

    //     return redirect()->route('tenant.requests')->with('success', 'Request submitted successfully!');
    // }

}
