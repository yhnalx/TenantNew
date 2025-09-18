<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    // Manager Overview
    public function dashboard()
{
    $totalUnits = 50; // hardcoded
    $occupiedUnits = User::where('role', 'tenant')->where('status', 'approved')->count();
    $vacantUnits = $totalUnits - $occupiedUnits;
    $pendingApplications = User::where('role', 'tenant')->where('status', 'pending')->count();
    $approvedTenants = $occupiedUnits;
    $rejectedTenants = User::where('role', 'tenant')->where('status', 'rejected')->count();

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


    // Other pages
    public function reports(Request $request)
    {
        $month = $request->input('month'); // format: YYYY-MM
        $query = User::where('role', 'tenant');

        if ($month) {
            $query->whereMonth('created_at', date('m', strtotime($month)))
                ->whereYear('created_at', date('Y', strtotime($month)));
        }

        $tenants = $query->get();

        $totalUnits = 50; // Example
        $occupiedUnits = $tenants->where('status', 'approved')->count();
        $vacantUnits = $totalUnits - $occupiedUnits;

        $tenantStatus = [
            'approved' => $tenants->where('status', 'approved')->count(),
            'pending'  => $tenants->where('status', 'pending')->count(),
            'rejected' => $tenants->where('status', 'rejected')->count(),
        ];

        return view('manager.reports', compact(
            'tenantStatus', 
            'occupiedUnits', 
            'vacantUnits',
            'totalUnits',
            'month'
        ));
    }


    public function settings()
    {
        return view('manager.settings');
    }

    public function manageTenants()
    {
        $pendingTenants = User::where('role', 'tenant')
                            ->where('status', 'pending')
                            ->get();

        $approvedTenantList = User::where('role', 'tenant')
                                ->where('status', 'approved')
                                ->get();

        return view('manager.tenants', compact('pendingTenants', 'approvedTenantList'));
    }

}
