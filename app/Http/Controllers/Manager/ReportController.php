<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('manager.reports.index');
    }

    public function show($report)
    {
        switch ($report) {
            case 'active-tenants':
                $data = Tenant::with('lease.property')->whereHas('lease', fn($q) => $q->where('status', 'active'))->get();
                $title = "List of Tenants with Active Lease and Assigned Property";
                break;

            case 'payment-history':
                $data = Payment::with('tenant.lease')->orderBy('pay_date', 'desc')->get();
                $title = "Payment History per Tenant with Lease Info and Dates";
                break;

            case 'lease-summary':
                $data = Lease::with('tenant.property')->get();
                $title = "Lease Summary – Start Date, End Date, and Monthly Rent";
                break;

            case 'deposit-details':
                $data = Lease::select('id','tenant_id','security_deposit','deposit_status')->with('tenant')->get();
                $title = "Deposit Details with Status";
                break;

            case 'maintenance-requests':
                $data = MaintenanceRequest::with('tenant')->get();
                $title = "Maintenance Requests per Tenant with Status";
                break;

            case 'monthly-rent-summary':
                $data = Lease::select('id','tenant_id','monthly_rent','start_date','end_date')->with('tenant')->get();
                $title = "Monthly Rent Summary with Lease Duration";
                break;

            case 'unpaid-overdue':
                $data = Payment::with('tenant')
                    ->where('status', 'unpaid')
                    ->orWhere(function($q) {
                        $q->where('status', 'pending')->where('due_date', '<', now());
                    })
                    ->get();
                $title = "Unpaid and Overdue Payments Based on Monthly Due Dates";
                break;

            case 'expected-rent':
                $data = Lease::with('tenant')->get();
                $title = "Expected Rent Collection with Lease Duration";
                break;

            case 'tenants-by-property-type':
                $data = Tenant::with('lease.property')->get()->groupBy(fn($tenant) => $tenant->lease->property->type ?? 'Unassigned');
                $title = "Tenants Grouped by Property Type";
                break;

            default:
                abort(404);
        }

        return view('manager.reports.show', compact('data','title','report'));
    }
}
