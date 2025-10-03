<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    /**
     * Show all tenants grouped by status
     */
    public function tenants()
    {
        $pendingTenants = User::where('role', 'tenant')
            ->where('status', 'pending')
            ->get();

        $approvedTenantList = User::where('role', 'tenant')
            ->where('status', 'approved')
            ->get();

        $rejectedTenantList = User::where('role', 'tenant')
            ->where('status', 'rejected')
            ->get();

        return view('manager.tenants', compact(
            'pendingTenants',
            'approvedTenantList',
            'rejectedTenantList'
        ));
    }

    /**
     * Approve a tenant application
     */
    // public function approve($id)
    // {
    //     $user = User::find($id);

    //     if (!$user || $user->role !== 'tenant') {
    //         return redirect()->back()->with('error', 'Invalid tenant.');
    //     }

    //     $user->status = 'approved';
    //     $user->rejection_reason = null; // clear old reason if any
    //     $user->save();

    //     return redirect()->back()->with('success', 'Tenant approved successfully.');
    // }

    public function approve($id)
    {
        $user = User::find($id);

        if (!$user || $user->role !== 'tenant') {
            return redirect()->back()->with('error', 'Invalid tenant.');
        }

        $user->status = 'approved';
        $user->rejection_reason = null;
        $user->save();

        $startDate = Carbon::today();
        $endDate   = $startDate->copy()->addYear();

        Lease::create([
            'user_id'       => $user->id,
            'unit_id'       => $user->tenantApplication->unit_id ?? null,
            'lea_start_date'=> $startDate,
            'lea_end_date'  => $endDate,
            'lea_status'    => 'active',
            'room_no'       => $user->tenantApplication->room_no ?? null,
        ]);
        
        return redirect()->back()->with('success', 'Tenant approved successfully and lease created.');
    }


    /**
     * Reject a tenant application
     */
    public function reject(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'rejection_reason' => 'required|string|max:255',
        ]);

        // Find tenant by ID from hidden field
        $tenant = User::find($request->tenant_id);

        // Ensure tenant exists and is role=tenant
        if (!$tenant || $tenant->role !== 'tenant') {
            return redirect()->back()->with('error', 'Invalid tenant.');
        }

        // Update status and reason
        $tenant->status = 'rejected';
        $tenant->rejection_reason = $request->rejection_reason;
        $tenant->save();

        return redirect()->back()->with('warning', 'Tenant rejected successfully.');
    }



}
