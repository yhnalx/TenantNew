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
            'lease-summary'        => 'Lease Information (coming soon)',
            'maintenance-requests' => 'Maintenance Requests',
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
                $query = \App\Models\MaintenanceRequest::with('tenant');

                // ✅ Apply filters if provided
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                if ($request->filled('urgency')) {
                    $query->where('urgency', $request->urgency);
                }

                // ✅ Count total for summary
                $total = (clone $query)->count();

                // ✅ Paginate data
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
