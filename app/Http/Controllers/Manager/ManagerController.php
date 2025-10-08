<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Unit;
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

    // public function approve($id)
    // {
    //     $user = User::find($id);

    //     if (!$user || $user->role !== 'tenant') {
    //         return redirect()->back()->with('error', 'Invalid tenant.');
    //     }

    //     $tenantApp = $user->tenantApplication;

    //     if (!$tenantApp) {
    //         return redirect()->back()->with('error', 'Tenant application not found.');
    //     }

    //     // Approve tenant
    //     $user->status = 'approved';
    //     $user->rejection_reason = null;
    //     $user->save();

    //     // Lease period
    //     $startDate = Carbon::today();
    //     $endDate   = $startDate->copy()->addYear();

    //     // Assign unit
    //     $unit = Unit::find($tenantApp->unit_id);

    //     if (!$unit) {
    //         return redirect()->back()->with('error', 'Selected unit not found.');
    //     }

    //     // Mark unit as occupied
    //     $unit->status = 'occupied';
    //     $unit->save();

    //     // Create Lease
    //     $lease = Lease::create([
    //         'user_id'        => $user->id,
    //         'unit_id'        => $unit->id,
    //         'lea_start_date' => $startDate,
    //         'lea_end_date'   => $endDate,
    //         'lea_status'     => 'active',
    //         'room_no'        => $unit->room_no, // fetch from unit
    //     ]);

    //     // Determine unit type and rent
    //     $unitType = $tenantApp->unit_type ?? 'Studio';
    //     $monthlyRent = 0;
    //     $depositAmount = 0;
    //     $monthlyUtilities = 0; // fixed utility fee, adjust if needed

    //     switch ($unitType) {
    //         case 'Studio':
    //             $monthlyRent = 7500;
    //             break;
    //         case 'One Bedroom':
    //             $monthlyRent = 10000;
    //             break;
    //         case 'Two Bedroom':
    //             $monthlyRent = 12000;
    //             break;
    //         default:
    //             $monthlyRent = 7500;
    //     }

    //     // Deposit = 1 month rent + 1 month advance rent
    //     $depositAmount = $monthlyRent * 2;

    //     // Update tenant financial info
    //     $user->rent_amount     = $monthlyRent;
    //     $user->utility_amount  = $monthlyUtilities;
    //     $user->deposit_amount  = $depositAmount;
    //     $user->rent_balance    = $depositAmount; // total initial amount due
    //     $user->utility_balance = $monthlyUtilities; // utilities due
    //     $user->save();

    //     return redirect()->back()->with('success', 'Tenant approved successfully. Lease and financial info initialized, and unit marked as occupied.');
    // }

    public function approve($id)
    {
        $user = User::find($id);

        if (!$user || $user->role !== 'tenant') {
            return redirect()->back()->with('error', 'Invalid tenant.');
        }

        $tenantApp = $user->tenantApplication;

        if (!$tenantApp) {
            return redirect()->back()->with('error', 'Tenant application not found.');
        }

        // Approve tenant
        $user->status = 'approved';
        $user->rejection_reason = null;
        $user->save();

        // Lease period
        $startDate = Carbon::today();
        $endDate   = $startDate->copy()->addYear();

        // Lease duration text
        $leaseTermText = sprintf(
            '1 Year Lease (%s – %s)',
            $startDate->format('M d, Y'),
            $endDate->format('M d, Y')
        );

        // Assign unit
        $unit = Unit::find($tenantApp->unit_id);

        if (!$unit) {
            return redirect()->back()->with('error', 'Selected unit not found.');
        }

        // Mark unit as occupied
        $unit->status = 'occupied';
        $unit->save();

        // Create Lease
        $lease = Lease::create([
            'user_id'        => $user->id,
            'unit_id'        => $unit->id,
            'lea_start_date' => $startDate,
            'lea_end_date'   => $endDate,
            'lea_status'     => 'active',
            'room_no'        => $unit->room_no,
            'lea_terms'      => $leaseTermText, // ✅ Added lease term text
        ]);

        // Determine unit type and rent
        $unitType = $tenantApp->unit_type ?? 'Studio';
        $monthlyRent = 0;
        $depositAmount = 0;
        $monthlyUtilities = 0;

        switch ($unitType) {
            case 'Studio':
                $monthlyRent = 7500;
                break;
            case 'One Bedroom':
                $monthlyRent = 10000;
                break;
            case 'Two Bedroom':
                $monthlyRent = 12000;
                break;
            default:
                $monthlyRent = 7500;
        }

        // Deposit = 1 month rent + 1 month advance rent
        $depositAmount = $monthlyRent * 2;

        // Update tenant financial info
        $user->rent_amount     = $monthlyRent;
        $user->utility_amount  = $monthlyUtilities;
        $user->deposit_amount  = $depositAmount;
        $user->rent_balance    = $depositAmount;
        $user->utility_balance = $monthlyUtilities;
        $user->save();

        return redirect()->back()->with('success', 'Tenant approved successfully. Lease and financial info initialized, and unit marked as occupied.');
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
