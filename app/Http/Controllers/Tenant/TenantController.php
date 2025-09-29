<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\TenantApplication;
use Illuminate\Support\Facades\Auth;

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

    

    // public function dashboard()
    // {
    //     $tenant = Auth::user();

    //     // Check if the tenant has already submitted a completed application
    //     $tenantApplication = TenantApplication::where('user_id', $user->id)->first();
    //     $showApplicationModal = !$tenantApplication || !$tenantApplication->is_complete;


    //     // Dummy payments collection
    //     $payments = collect([
    //         (object)[
    //             'payment_date' => '2025-09-01',
    //             'type' => 'rent',
    //             'amount' => 5000
    //         ],
    //         (object)[
    //             'payment_date' => '2025-09-15',
    //             'type' => 'utilities',
    //             'amount' => 1200
    //         ],
    //         (object)[
    //             'payment_date' => '2025-10-01',
    //             'type' => 'rent',
    //             'amount' => 5000
    //         ],
    //     ]);

    //     // Dummy maintenance requests collection
    //     $requests = collect([
    //         (object)[
    //             'request_date' => '2025-09-05',
    //             'status' => 'pending',
    //             'description' => 'Leaky faucet'
    //         ],
    //         (object)[
    //             'request_date' => '2025-09-10',
    //             'status' => 'completed',
    //             'description' => 'Broken light bulb'
    //         ],
    //     ]);

    //     return view('tenant.home', compact(
    //         'tenant',
    //         'payments',
    //         'requests',
    //         'hasSubmittedApplication'
    //     ));
    // }

    public function dashboard()
    {
        $user = Auth::user();

        // Check if the tenant has completed the application
        $tenantApplication = TenantApplication::where('user_id', $user->id)->first();
        $showApplicationModal = !$tenantApplication || !$tenantApplication->is_complete;

        // Fetch tenant payments from DB
        $payments = Payment::where('tenant_id', $user->id)->get();

        // Fetch tenant requests from DB
        $requests = MaintenanceRequest::where('tenant_id', $user->id)->get();

        return view('tenant.home', compact('payments', 'requests', 'showApplicationModal'));
    }


    public function payments()
    {
        $tenant = Auth::user();

        $payments = Payment::where('user_id', $tenant->id)
                            ->orderBy('pay_date', 'desc')
                            ->get();

        return view('tenant.payments', compact('payments'));
    }

    public function requests()
    {
        // Placeholder until MaintenanceRequest model exists
        $requests = collect(); // empty collection
        return view('tenant.request', compact('requests'));
    }

    public function submitApplication(Request $request)
    {
        $user = Auth::user();

        try {
            // Validate the form
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'contact_number' => 'required|string|max:20',
                'current_address' => 'required|string|max:255',
                'birthdate' => 'required|date',
                'unit_type' => 'required|string',
                'move_in_date' => 'required|date',
                'reason' => 'required|string',
                'employment_status' => 'required|string',
                'employer_school' => 'required|string|max:255',
                'emergency_name' => 'required|string|max:255',
                'emergency_number' => 'required|string|max:20',
                'emergency_relationship' => 'required|string|max:50',
                'valid_id' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'id_picture' => 'required|image|max:2048',
            ]);

            // Handle file uploads
            if ($request->hasFile('valid_id')) {
                $validated['valid_id_path'] = $request->file('valid_id')->store('tenant_ids', 'public');
            }

            if ($request->hasFile('id_picture')) {
                $validated['id_picture_path'] = $request->file('id_picture')->store('tenant_photos', 'public');
            }

            // Save or update tenant application
            $tenantApplication = TenantApplication::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($validated, ['is_complete' => true])
            );

            // Redirect to dashboard after successful submission
            return redirect()->route('tenant.dashboard')
                ->with('success', 'Tenant application submitted successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Server error: ' . $e->getMessage())
                ->withInput();
        }
    }


}
