<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Illuminate\Support\Collection;

class TenantController extends Controller
{
    // public function dashboard()
    // {
    //     $tenant = Auth::user();

    //     // Fetch payments for this tenant
    //     $payments = Payment::where('user_id', $tenant->id)
    //                         ->orderBy('payment_date', 'desc')
    //                         ->get();

    //     // No MaintenanceRequest model yet, so use empty collection
    //     $requests = collect();

    //     return view('tenant.home', compact('tenant', 'payments', 'requests'));
    // }

    public function dashboard()
    {
        // Dummy payments collection
        $payments = collect([
            (object)[
                'payment_date' => '2025-09-01',
                'type' => 'rent',
                'amount' => 5000
            ],
            (object)[
                'payment_date' => '2025-09-15',
                'type' => 'utilities',
                'amount' => 1200
            ],
            (object)[
                'payment_date' => '2025-10-01',
                'type' => 'rent',
                'amount' => 5000
            ],
        ]);

        // Dummy maintenance requests collection
        $requests = collect([
            (object)[
                'request_date' => '2025-09-05',
                'status' => 'pending',
                'description' => 'Leaky faucet'
            ],
            (object)[
                'request_date' => '2025-09-10',
                'status' => 'completed',
                'description' => 'Broken light bulb'
            ],
        ]);

        // Pass the dummy data to the Blade
        return view('tenant.home', compact('payments', 'requests'));
    }

    public function payments()
    {
        $tenant = Auth::user();

        $payments = Payment::where('user_id', $tenant->id)
                            ->orderBy('payment_date', 'desc')
                            ->get();

        return view('tenant.payments', compact('payments'));
    }

    public function requests()
    {
        // Placeholder until MaintenanceRequest model exists
        $requests = collect(); // empty collection
        return view('tenant.request', compact('requests'));
    }

}
