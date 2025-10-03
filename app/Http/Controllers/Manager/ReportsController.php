<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
// use App\Models\MaintenanceRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ReportsController extends Controller
{
    public function index()
    {
        $reports = [
            'active-tenants'       => 'Active Tenants',
            'payment-history'      => 'Payment History',
            'lease-summary'        => 'Lease Information',
            'maintenance-requests' => 'Maintenance Requests',
        ];

        return view('manager.reports.index', compact('reports'));
    }

    public function show(Request $request, $report)
    {
        $total = 0;
        $currentFilter = '';
        $data = collect();
        $title = '';

        switch ($report) {
            case 'active-tenants':
                $query = User::with(['tenantApplication', 'leases' => function($q) {
                    $q->where('lea_status', 'active')->latest('created_at');
                }])->where('role', 'tenant')->where('status', 'approved');

                // Filter by Unit Type
                if ($request->filled('unit_type')) {
                    $query->whereHas('tenantApplication', function($q) use ($request) {
                        $q->where('unit_type', $request->unit_type);
                    });
                }

                // Filter by Employment Status
                if ($request->filled('employment_status')) {
                    $query->whereHas('tenantApplication', function($q) use ($request) {
                        $q->where('employment_status', $request->employment_status);
                    });
                }

                $total = $query->count();
                $data = $query->paginate(10);

                $title = "Active Tenants";
                $currentFilter = $request->only(['unit_type', 'employment_status']);
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
                $query = User::with(['tenantApplication', 'leases' => function($q) {
                    $q->where('lea_status', 'active')->latest('created_at');
                }])
                ->where('role', 'tenant')
                ->where('status', 'approved');

                $total = $query->count();
                $data = $query->paginate(10);
                $title = "List of Active Lease";
                break;

            case 'maintenance-requests':
                $query = \App\Models\MaintenanceRequest::with('tenant');

                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                if ($request->filled('urgency')) {
                    $query->where('urgency', $request->urgency);
                }

                $total = (clone $query)->count();
                $data = $query->orderBy('created_at', 'desc')->paginate(10);
                $title = "Maintenance Requests";
                break;

            default:
                abort(404, 'Report not found.');
        }

        return view('manager.reports.show', compact(
            'data', 'title', 'report', 'total', 'currentFilter'
        ));
    }



    public function export(Request $request, $report)
    {
        switch ($report) {
            // ---------------- Payment History ----------------
            case 'payment-history':
                $query = Payment::with('tenant');

                if ($request->filled('payment_for')) {
                    $query->where('payment_for', $request->payment_for);
                }

                $payments = $query->orderBy('pay_date', 'desc')->get();

                $filename = "payment-history-" . now()->format('Y-m-d_H-i-s') . ".csv";

                $response = new StreamedResponse(function () use ($payments) {
                    $handle = fopen('php://output', 'w');

                    // CSV header
                    fputcsv($handle, ['Tenant', 'Amount', 'Date', 'Purpose', 'Status']);

                    foreach ($payments as $payment) {
                        fputcsv($handle, [
                            $payment->tenant->name ?? 'N/A',
                            $payment->pay_amount,
                            optional($payment->pay_date)->format('Y-m-d'),
                            ucfirst($payment->payment_for),
                            $payment->pay_status,
                        ]);
                    }

                    fclose($handle);
                });

                $response->headers->set('Content-Type', 'text/csv');
                $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

                return $response;

            // ---------------- Tenants Export ----------------
            case 'active-tenants':
                $pendingTenants = User::with('tenantApplication')
                    ->where('role', 'tenant')->where('status', 'pending')->get();
                $approvedTenants = User::with(['tenantApplication', 'leases' => function($q) {
                    $q->where('lea_status', 'active')->latest('created_at');
                }])->where('role', 'tenant')->where('status', 'approved')->get();
                $rejectedTenants = User::with('tenantApplication')
                    ->where('role', 'tenant')->where('status', 'rejected')->get();

                $filename = "tenants-export-" . now()->format('Y-m-d_H-i-s') . ".csv";

                $response = new StreamedResponse(function () use ($pendingTenants, $approvedTenants, $rejectedTenants) {
                    $handle = fopen('php://output', 'w');

                    // Export date
                    fputcsv($handle, ['Date of Export', now()->format('Y-m-d H:i:s')]);
                    fputcsv($handle, []);

                    // --- Pending Tenants ---
                    fputcsv($handle, ['Pending Tenants']);
                    fputcsv($handle, ['Full Name','Email','Contact Number','Unit Type','Employment Status','Source of Income','Emergency Name','Emergency Number']);
                    foreach ($pendingTenants as $tenant) {
                        $app = $tenant->tenantApplication;
                        fputcsv($handle, [
                            $tenant->name,
                            $tenant->email,
                            $app->contact_number ?? 'N/A',
                            $app->unit_type ?? 'N/A',
                            $app->employment_status ?? 'N/A',
                            $app->source_of_income ?? 'N/A',
                            $app->emergency_name ?? 'N/A',
                            $app->emergency_number ?? 'N/A',
                        ]);
                    }
                    fputcsv($handle, []);

                    // --- Approved Tenants ---
                    fputcsv($handle, ['Approved Tenants']);
                    fputcsv($handle, ['Full Name','Email','Contact Number','Unit Type','Employment Status','Source of Income','Emergency Name','Emergency Number','Lease Start','Lease End']);
                    foreach ($approvedTenants as $tenant) {
                        $app = $tenant->tenantApplication;
                        $lease = $tenant->leases->first(); // latest active lease
                        fputcsv($handle, [
                            $tenant->name,
                            $tenant->email,
                            $app->contact_number ?? 'N/A',
                            $app->unit_type ?? 'N/A',
                            $app->employment_status ?? 'N/A',
                            $app->source_of_income ?? 'N/A',
                            $app->emergency_name ?? 'N/A',
                            $app->emergency_number ?? 'N/A',
                            $lease?->lea_start_date ?? 'N/A',
                            $lease?->lea_end_date ?? 'N/A',
                        ]);
                    }
                    fputcsv($handle, []);

                    // --- Rejected Tenants ---
                    fputcsv($handle, ['Rejected Tenants']);
                    fputcsv($handle, ['Full Name','Email','Contact Number','Unit Type','Employment Status','Source of Income','Emergency Name','Emergency Number','Rejection Reason']);
                    foreach ($rejectedTenants as $tenant) {
                        $app = $tenant->tenantApplication;
                        fputcsv($handle, [
                            $tenant->name,
                            $tenant->email,
                            $app->contact_number ?? 'N/A',
                            $app->unit_type ?? 'N/A',
                            $app->employment_status ?? 'N/A',
                            $app->source_of_income ?? 'N/A',
                            $app->emergency_name ?? 'N/A',
                            $app->emergency_number ?? 'N/A',
                            $tenant->rejection_reason ?? 'N/A',
                        ]);
                    }

                    fclose($handle);
                });

                $response->headers->set('Content-Type', 'text/csv');
                $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

                return $response;

            default:
                abort(404, 'Export not available for this report.');
        }
    }


    public function updatePaymentStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'pay_status' => 'required|in:Pending,Accepted',
        ]);

        $payment->update(['pay_status' => $request->pay_status]);

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

}
