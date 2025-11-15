<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use App\Models\Notification;
// use App\Models\MaintenanceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;



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
    public function viewReportPdf(Request $request, $report)
    {
        switch ($report) {

            // ---------------- PAYMENT HISTORY ----------------
            case 'payment-history':
                $query = Payment::with('tenant');

                if ($request->filled('payment_for')) {
                    $query->where('payment_for', $request->payment_for);
                }

                $payments = $query->orderBy('pay_date', 'desc')->get();

                $pdf = Pdf::loadView('reports.pdf.payment-history', [
                    'payments' => $payments
                ])->setPaper('a4', 'landscape');

                return $pdf->stream('payment-history.pdf');

            // ---------------- TENANT REPORT ----------------
            case 'active-tenants':
                $pendingTenants = User::with('tenantApplication')
                    ->where('role', 'tenant')->where('status', 'pending')->get();
                $approvedTenants = User::with(['tenantApplication', 'leases' => function($q) {
                    $q->where('lea_status', 'active')->latest('created_at');
                }])->where('role', 'tenant')->where('status', 'approved')->get();
                $rejectedTenants = User::with('tenantApplication')
                    ->where('role', 'tenant')->where('status', 'rejected')->get();

                $pdf = Pdf::loadView('reports.pdf.active-tenants', [
                    'pendingTenants' => $pendingTenants,
                    'approvedTenants' => $approvedTenants,
                    'rejectedTenants' => $rejectedTenants
                ])->setPaper('a4', 'landscape');

                return $pdf->stream('active-tenants.pdf');

            // ---------------- MAINTENANCE REQUESTS ----------------
            case 'maintenance-requests':
                // Start query with join
                $query = DB::table('maintenance_requests')
                    ->join('tenant_applications', 'maintenance_requests.tenant_id', '=', 'tenant_applications.user_id')
                    ->select(
                        'tenant_applications.full_name as tenant_name',
                        'maintenance_requests.room_no',
                        'maintenance_requests.unit_type',
                        'maintenance_requests.description',
                        'maintenance_requests.supposed_date',
                        'maintenance_requests.status',
                        'maintenance_requests.urgency',
                        'maintenance_requests.issue_image',
                        'maintenance_requests.created_at'
                    );

                // ✅ Apply filters (same as your index)
                if ($request->filled('status')) {
                    $query->where('maintenance_requests.status', $request->status);
                }

                if ($request->filled('urgency')) {
                    $query->where('maintenance_requests.urgency', $request->urgency);
                }

                // ✅ Live search (works across tenant + maintenance columns)
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('tenant_applications.full_name', 'like', "%{$search}%")
                        ->orWhere('maintenance_requests.description', 'like', "%{$search}%")
                        ->orWhere('maintenance_requests.status', 'like', "%{$search}%")
                        ->orWhere('maintenance_requests.urgency', 'like', "%{$search}%")
                        ->orWhere('maintenance_requests.room_no', 'like', "%{$search}%")
                        ->orWhere('maintenance_requests.unit_type', 'like', "%{$search}%");
                    });
                }

                // ✅ Execute query and export
                $requests = $query->orderBy('maintenance_requests.created_at', 'desc')->get();

                $pdf = Pdf::loadView('reports.pdf.maintenance-requests', [
                    'requests' => $requests
                ])->setPaper('a4', 'landscape');

                return $pdf->stream('maintenance-requests.pdf');


            // ---------------- LEASE SUMMARY ----------------
            case 'lease-summary':
                $query = User::with([
                    'tenantApplication',
                    'leases' => function($q) {
                        $q->where('lea_status', 'active')->latest('created_at');
                    }
                ])
                ->where('role', 'tenant')
                ->where('status', 'approved');

                $total = $query->count();
                $data = $query->get();

                $pdf = Pdf::loadView('reports.pdf.lease-summary', [
                    'data' => $data,
                    'total' => $total
                ])->setPaper('a4', 'landscape');

                return $pdf->stream('lease-summary.pdf');

            default:
                abort(404, 'Report not available for PDF view.');
        }
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
                // ✅ Search filter (includes full_name, unit_type, and employment_status)
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->whereHas('tenantApplication', function($subQ) use ($search) {
                            $subQ->where('full_name', 'like', "%{$search}%")  // Search tenant full_name
                                ->orWhere('unit_type', 'like', "%{$search}%")
                                ->orWhere('employment_status', 'like', "%{$search}%");
                        });
                    });
                    $currentFilter = $request->search;
                }
                $total = $query->count();
                $data = $query->paginate(10);
                $title = "Active Tenants";
                break;

            case 'payment-history':
                $query = Payment::with('tenant');

                // ✅ Search filter (replaces dropdown)
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where('payment_for', 'like', "%{$search}%");
                    $currentFilter = $request->search;
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

                // ✅ New search filter for tenant names
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where('name', 'like', "%{$search}%");
                    $currentFilter = $request->search;
                }

                $total = $query->count();
                $data = $query->paginate(10);
                $title = "List of Active Lease";
                break;

            case 'maintenance-requests':
                // No changes needed - already has search
                $query = \App\Models\MaintenanceRequest::query();

                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                if ($request->filled('urgency')) {
                    $query->where('urgency', $request->urgency);
                }

                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('urgency', 'like', "%{$search}%");
                    });
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

    // public function show(Request $request, $report)
    // {
    //     $total = 0;
    //     $currentFilter = '';
    //     $data = collect();
    //     $title = '';

    //     switch ($report) {
    //         case 'active-tenants':
    //             $query = User::with(['tenantApplication', 'leases' => function($q) {
    //                 $q->where('lea_status', 'active')->latest('created_at');
    //             }])->where('role', 'tenant')->where('status', 'approved');

    //             // Filter by Unit Type
    //             if ($request->filled('unit_type')) {
    //                 $query->whereHas('tenantApplication', function($q) use ($request) {
    //                     $q->where('unit_type', $request->unit_type);
    //                 });
    //             }

    //             // Filter by Employment Status
    //             if ($request->filled('employment_status')) {
    //                 $query->whereHas('tenantApplication', function($q) use ($request) {
    //                     $q->where('employment_status', $request->employment_status);
    //                 });
    //             }

    //             $total = $query->count();
    //             $data = $query->paginate(10);

    //             $title = "Active Tenants";
    //             $currentFilter = $request->only(['unit_type', 'employment_status']);
    //             break;


    //         case 'payment-history':
    //             $query = Payment::with('tenant');

    //             if ($request->filled('payment_for')) {
    //                 $query->where('payment_for', $request->payment_for);
    //                 $currentFilter = $request->payment_for;
    //             }

    //             $total = (clone $query)->sum('pay_amount');
    //             $data = $query->orderBy('pay_date', 'desc')->paginate(10);
    //             $title = "Payment History per Tenant";
    //             break;

    //         case 'lease-summary':
    //             $query = User::with(['tenantApplication', 'leases' => function($q) {
    //                 $q->where('lea_status', 'active')->latest('created_at');
    //             }])
    //             ->where('role', 'tenant')
    //             ->where('status', 'approved');

    //             $total = $query->count();
    //             $data = $query->paginate(10);
    //             $title = "List of Active Lease";
    //             break;

    //         case 'maintenance-requests':
    //             $query = \App\Models\MaintenanceRequest::query();

    //             // Filters
    //             if ($request->filled('status')) {
    //                 $query->where('status', $request->status);
    //             }
    //             if ($request->filled('urgency')) {
    //                 $query->where('urgency', $request->urgency);
    //             }

    //             // ✅ Live search (without relations)
    //             if ($request->filled('search')) {
    //                 $search = $request->search;
    //                 $query->where(function ($q) use ($search) {
    //                     $q->where('description', 'like', "%{$search}%")
    //                     ->orWhere('status', 'like', "%{$search}%")
    //                     ->orWhere('urgency', 'like', "%{$search}%");
    //                 });
    //             }

    //             $total = (clone $query)->count();
    //             $data = $query->orderBy('created_at', 'desc')->paginate(10);
    //             $title = "Maintenance Requests";
    //             break;

    //         default:
    //             abort(404, 'Report not found.');
    //     }

    //     return view('manager.reports.show', compact(
    //         'data', 'title', 'report', 'total', 'currentFilter'
    //     ));
    // }



    // public function export(Request $request, $report)
    // {
    //     switch ($report) {
    //         // ---------------- Payment History ----------------
    //         case 'payment-history':
    //             $query = Payment::with('tenant');

    //             if ($request->filled('payment_for')) {
    //                 $query->where('payment_for', $request->payment_for);
    //             }

    //             $payments = $query->orderBy('pay_date', 'desc')->get();

    //             $filename = "payment-history-" . now()->format('Y-m-d_H-i-s') . ".csv";

    //             $response = new StreamedResponse(function () use ($payments) {
    //                 $handle = fopen('php://output', 'w');

    //                 // CSV header
    //                 fputcsv($handle, ['Reference_number', 'Tenant', 'Amount', 'Date', 'Purpose', 'Status']);

    //                 foreach ($payments as $payment) {
    //                     fputcsv($handle, [
    //                         $payment->reference_number,
    //                         $payment->tenant->name ?? 'N/A',
    //                         $payment->pay_amount,
    //                         optional($payment->pay_date)->format('Y-m-d'),
    //                         ucfirst($payment->payment_for),
    //                         $payment->pay_status,
    //                     ]);
    //                 }

    //                 fclose($handle);
    //             });

    //             $response->headers->set('Content-Type', 'text/csv');
    //             $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

    //             return $response;

    //         // ---------------- Tenants Export ----------------
    //         case 'active-tenants':
    //             $pendingTenants = User::with('tenantApplication')
    //                 ->where('role', 'tenant')->where('status', 'pending')->get();
    //             $approvedTenants = User::with(['tenantApplication', 'leases' => function($q) {
    //                 $q->where('lea_status', 'active')->latest('created_at');
    //             }])->where('role', 'tenant')->where('status', 'approved')->get();
    //             $rejectedTenants = User::with('tenantApplication')
    //                 ->where('role', 'tenant')->where('status', 'rejected')->get();

    //             $filename = "tenants-export-" . now()->format('Y-m-d_H-i-s') . ".csv";

    //             $response = new StreamedResponse(function () use ($pendingTenants, $approvedTenants, $rejectedTenants) {
    //                 $handle = fopen('php://output', 'w');

    //                 // Export date
    //                 fputcsv($handle, ['Date of Export', now()->format('Y-m-d H:i:s')]);
    //                 fputcsv($handle, []);

    //                 // --- Pending Tenants ---
    //                 fputcsv($handle, ['Pending Tenants']);
    //                 fputcsv($handle, ['Full Name','Email','Contact Number','Unit Type','Employment Status','Source of Income','Emergency Name','Emergency Number']);
    //                 foreach ($pendingTenants as $tenant) {
    //                     $app = $tenant->tenantApplication;
    //                     fputcsv($handle, [
    //                         $tenant->name,
    //                         $tenant->email,
    //                         $app->contact_number ?? 'N/A',
    //                         $app->unit_type ?? 'N/A',
    //                         $app->employment_status ?? 'N/A',
    //                         $app->source_of_income ?? 'N/A',
    //                         $app->emergency_name ?? 'N/A',
    //                         $app->emergency_number ?? 'N/A',
    //                     ]);
    //                 }
    //                 fputcsv($handle, []);

    //                 // --- Approved Tenants ---
    //                 fputcsv($handle, ['Approved Tenants']);
    //                 fputcsv($handle, ['Full Name','Email','Contact Number','Unit Type','Employment Status','Source of Income','Emergency Name','Emergency Number','Lease Start','Lease End']);
    //                 foreach ($approvedTenants as $tenant) {
    //                     $app = $tenant->tenantApplication;
    //                     $lease = $tenant->leases->first(); // latest active lease
    //                     fputcsv($handle, [
    //                         $tenant->name,
    //                         $tenant->email,
    //                         $app->contact_number ?? 'N/A',
    //                         $app->unit_type ?? 'N/A',
    //                         $app->employment_status ?? 'N/A',
    //                         $app->source_of_income ?? 'N/A',
    //                         $app->emergency_name ?? 'N/A',
    //                         $app->emergency_number ?? 'N/A',
    //                         $lease?->lea_start_date ?? 'N/A',
    //                         $lease?->lea_end_date ?? 'N/A',
    //                     ]);
    //                 }
    //                 fputcsv($handle, []);

    //                 // --- Rejected Tenants ---
    //                 fputcsv($handle, ['Rejected Tenants']);
    //                 fputcsv($handle, ['Full Name','Email','Contact Number','Unit Type','Employment Status','Source of Income','Emergency Name','Emergency Number','Rejection Reason']);
    //                 foreach ($rejectedTenants as $tenant) {
    //                     $app = $tenant->tenantApplication;
    //                     fputcsv($handle, [
    //                         $tenant->name,
    //                         $tenant->email,
    //                         $app->contact_number ?? 'N/A',
    //                         $app->unit_type ?? 'N/A',
    //                         $app->employment_status ?? 'N/A',
    //                         $app->source_of_income ?? 'N/A',
    //                         $app->emergency_name ?? 'N/A',
    //                         $app->emergency_number ?? 'N/A',
    //                         $tenant->rejection_reason ?? 'N/A',
    //                     ]);
    //                 }

    //                 fclose($handle);
    //             });

    //             $response->headers->set('Content-Type', 'text/csv');
    //             $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

    //             return $response;

    //         default:
    //             abort(404, 'Export not available for this report.');
    //     }
    // }

    // Landscape orientation
    public function export(Request $request, $report)
    {
        switch ($report) {
            // ---------------- PAYMENT HISTORY ----------------
            case 'payment-history':
                $query = Payment::with('tenant');

                if ($request->filled('payment_for')) {
                    $query->where('payment_for', $request->payment_for);
                }

                $payments = $query->orderBy('pay_date', 'desc')->get();

                $pdf = Pdf::loadView('reports.pdf.payment-history', compact('payments'))
                    ->setPaper('a4', 'landscape');

                $filename = "payment-history-" . now()->format('Y-m-d_H-i-s') . ".pdf";
                return $pdf->download($filename);

            // ---------------- ACTIVE TENANTS ----------------
            case 'active-tenants':
                $pendingTenants = User::with('tenantApplication')
                    ->where('role', 'tenant')->where('status', 'pending')->get();
                $approvedTenants = User::with(['tenantApplication', 'leases' => function ($q) {
                    $q->where('lea_status', 'active')->latest('created_at');
                }])->where('role', 'tenant')->where('status', 'approved')->get();
                $rejectedTenants = User::with('tenantApplication')
                    ->where('role', 'tenant')->where('status', 'rejected')->get();

                $pdf = Pdf::loadView('reports.pdf.active-tenants', compact(
                    'pendingTenants', 'approvedTenants', 'rejectedTenants'
                ))->setPaper('a4', 'landscape');

                $filename = "tenants-export-" . now()->format('Y-m-d_H-i-s') . ".pdf";
                return $pdf->download($filename);

            default:
                abort(404, 'Export not available for this report.');
        }
    }


    // A4 Orientation
    // public function export(Request $request, $report)
    // {
    //     switch ($report) {
    //         // ---------------- PAYMENT HISTORY ----------------
    //         case 'payment-history':
    //             $query = Payment::with('tenant');

    //             if ($request->filled('payment_for')) {
    //                 $query->where('payment_for', $request->payment_for);
    //             }

    //             $payments = $query->orderBy('pay_date', 'desc')->get();

    //             $pdf = Pdf::loadView('reports.pdf.payment-history', compact('payments'))
    //                 ->setPaper('a4', 'landscape'); // A4 + landscape orientation

    //             $filename = 'payment-history-' . now()->format('Y-m-d_H-i-s') . '.pdf';
    //             return $pdf->download($filename);

    //         // ---------------- ACTIVE TENANTS ----------------
    //         case 'active-tenants':
    //             $pendingTenants = User::with('tenantApplication')
    //                 ->where('role', 'tenant')->where('status', 'pending')->get();

    //             $approvedTenants = User::with([
    //                 'tenantApplication',
    //                 'leases' => function ($q) {
    //                     $q->where('lea_status', 'active')->latest('created_at');
    //                 }
    //             ])->where('role', 'tenant')->where('status', 'approved')->get();

    //             $rejectedTenants = User::with('tenantApplication')
    //                 ->where('role', 'tenant')->where('status', 'rejected')->get();

    //             $pdf = Pdf::loadView('reports.pdf.active-tenants', compact(
    //                 'pendingTenants', 'approvedTenants', 'rejectedTenants'
    //             ))->setPaper('a4', 'portrait'); // A4 + portrait orientation

    //             $filename = 'active-tenants-' . now()->format('Y-m-d_H-i-s') . '.pdf';
    //             return $pdf->download($filename);

    //         default:
    //             abort(404, 'Export not available for this report.');
    //     }
    // }


    // public function updatePaymentStatus(Request $request, Payment $payment)
    // {
    //     $request->validate([
    //         'pay_status' => 'required|in:Pending,Accepted',
    //     ]);

    //     $payment->update(['pay_status' => $request->pay_status]);

    //     return redirect()->back()->with('success', 'Payment status updated successfully.');
    // }

    public function updatePaymentStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'pay_status' => 'required|in:Pending,Accepted',
        ]);
        $oldStatus = $payment->pay_status;
        $newStatus = $request->pay_status;
        // Only apply deductions/credits if accepting the payment
        if ($newStatus === 'Accepted' && $oldStatus !== 'Accepted') {
            $tenant = $payment->tenant;  // Ensure Payment model has: public function tenant() { return $this->belongsTo(User::class, 'tenant_id'); }
            $finalAmount = $payment->pay_amount;
            // 🧠 Apply credit BEFORE payment deduction (if applicable)
            $creditUsed = 0;
            if (in_array($payment->payment_for, ['Rent', 'Utilities']) && $tenant->user_credit > 0) {
                if ($payment->payment_for === 'Rent' && $tenant->rent_balance > 0) {
                    $creditUsed = min($tenant->user_credit, $tenant->rent_balance);
                    $tenant->rent_balance -= $creditUsed;
                } elseif ($payment->payment_for === 'Utilities' && $tenant->utility_balance > 0) {
                    $creditUsed = min($tenant->user_credit, $tenant->utility_balance);
                    $tenant->utility_balance -= $creditUsed;
                }
                $tenant->user_credit -= $creditUsed;
                if ($creditUsed > 0) {
                    $this->createNotification($tenant->id, 'Credit Applied', "₱{$creditUsed} of your credits were automatically applied to this {$payment->payment_for} payment.");
                }
            }
            // 🧾 Deduct the remaining payment from the correct balance
            switch ($payment->payment_for) {
                case 'Deposit':
                    $tenant->deposit_amount = max(0, $tenant->deposit_amount - $finalAmount);
                    if ($tenant->deposit_amount <= 0) {
                        $tenant->rent_balance = 0;
                    }
                    $this->createNotification($tenant->id, 'Deposit Paid', "Your deposit payment of ₱{$finalAmount} has been received. You can now access Maintenance Requests.");
                    break;
                case 'Rent':
                    $tenant->rent_balance = max(0, $tenant->rent_balance - $finalAmount);
                    $this->createNotification($tenant->id, 'Rent Payment Received', "Your rent payment of ₱{$finalAmount} has been received. Remaining rent balance: ₱{$tenant->rent_balance}.");
                    break;
                case 'Utilities':
                    $tenant->utility_balance = max(0, $tenant->utility_balance - $finalAmount);
                    $this->createNotification($tenant->id, 'Utility Payment Received', "Your utility payment of ₱{$finalAmount} has been received.");
                    break;
                case 'Other':
                    $tenant->user_credit += $payment->pay_amount;
                    $this->createNotification($tenant->id, 'Credit Added', "Your payment of ₱{$payment->pay_amount} for Advance Payment has been added as credit. You now have ₱{$tenant->user_credit} in credits.");
                    break;
            }
            // Save tenant updates
            $tenant->save();
        }
        // Update the payment status
        $payment->update(['pay_status' => $newStatus]);
        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    protected function createNotification($userId, $title, $message)
    {
        Notification::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'is_read' => false,  // Matches your model
        ]);
    }

}
