<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
// use App\Models\MaintenanceRequest;

class ReportsController extends Controller
{
    public function index()
    {
        $reports = [
            'active-tenants'       => 'Active Tenants',
            'payment-history'      => 'Payment History',
            'lease-summary'        => 'Lease Information (coming soon)',
            'maintenance-requests' => 'Maintenance Requests (coming soon)',
        ];

        return view('manager.reports.index', compact('reports'));
    }

    public function show(Request $request, $report)
    {
        $total = 0;              // always defined
        $currentFilter = '';     // always defined
        $data = collect();
        $title = '';

        switch ($report) {
            case 'active-tenants':
                $data = User::with('tenantApplication')
                    ->where('role', 'tenant')
                    ->get();
                $title = "List of Tenants (Leases Coming Soon)";
                break;

            case 'payment-history':
                $query = Payment::with('tenant');

                if ($request->filled('payment_for')) {
                    $query->where('payment_for', $request->payment_for);
                    $currentFilter = $request->payment_for;
                }

                $total = (clone $query)->sum('pay_amount');
                $data = $query->orderBy('pay_date', 'desc')->paginate(10);

                $title = "Payment History per Tenant";
                break;

            case 'lease-summary':
                $title = "Lease Summary (Coming Soon)";
                break;

            case 'maintenance-requests':
                $title = "Maintenance Requests (Coming Soon)";
                break;

            default:
                abort(404, 'Report not found.');
        }

        return view('manager.reports.show', compact(
            'data', 'title', 'report', 'total', 'currentFilter'
        ));
    }
}
