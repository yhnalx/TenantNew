<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function dashboard()
    {
        // Hardcoded total units for now
        $totalUnits = 50;
        
        // Calculate occupied & vacant units
        $occupiedUnits = User::where('role', 'tenant')->where('status', 'approved')->count();
        $vacantUnits = $totalUnits - $occupiedUnits;

        // Pending & rejected tenants
        $pendingApplications = User::where('role', 'tenant')->where('status', 'pending')->count();
        $approvedTenants = $occupiedUnits;
        $rejectedTenants = User::where('role', 'tenant')->where('status', 'rejected')->count();

        // Get tenant lists
        $pendingTenants = User::where('role', 'tenant')->where('status', 'pending')->get();
        $approvedTenantList = User::where('role', 'tenant')->where('status', 'approved')->get();

        return view('manager.dashboard', compact(
            'totalUnits',
            'occupiedUnits',
            'vacantUnits',
            'pendingApplications',
            'approvedTenants',
            'rejectedTenants',
            'pendingTenants',
            'approvedTenantList'
        ));
    }

    public function approve($id)
    {
        $user = User::find($id);
        if (!$user || $user->role !== 'tenant') {
            return redirect()->back()->with('error', 'Invalid tenant.');
        }

        $user->status = 'approved';
        $user->save();

        return redirect()->back()->with('success', 'Tenant approved successfully.');
    }

    public function reject($id)
    {
        $user = User::find($id);
        if (!$user || $user->role !== 'tenant') {
            return redirect()->back()->with('error', 'Invalid tenant.');
        }

        $user->status = 'rejected';
        $user->save();

        return redirect()->back()->with('warning', 'Tenant rejected.');
    }
}
