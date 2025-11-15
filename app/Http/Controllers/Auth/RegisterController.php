<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TenantApplication;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        $unitTypes = ['Studio', '1-Bedroom', '2-Bedroom', 'Commercial', 'Bed-Spacer'];
        $availableUnits = Unit::where('status', 'vacant')->get();

        return view('auth.register', compact('unitTypes', 'availableUnits'));
    }

    public function register(Request $request)
{
    // Validate input
    $request->validate([
        // User info
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'    => 'required|email',
        'contact'  => 'required|digits_between:10,15',
        'password' => [
            'required','string','min:8','confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
        ],

        // Tenant application
        'current_address' => 'required|string',
        'birthdate'       => 'required|date|before:today',
        'unit_type'       => 'required|string',
        'unit_id'         => 'required|exists:units,id',
        'move_in_date'    => 'required|date|after_or_equal:today',
        'reason'          => 'required|string',
        'employment_status'=> 'required|string',
        'employer_school' => 'required|string',
        'source_of_income'=> 'required|string',
        'emergency_name'  => 'required|string',
        'emergency_number'=> 'required|digits_between:10,15',
        'emergency_relationship'=> 'required|string',
        'valid_id'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'id_picture'      => 'required|image|max:2048',
    ]);

    // Fetch the selected unit
    $unit = Unit::find($request->unit_id);

    // Bed-Spacer specific capacity check
    if ($unit->unit_type === 'Bed-Spacer') {
        if ($unit->no_of_occupants >= $unit->capacity) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'This bed-spacer unit is full. Please select another one.');
        }
    } else {
        // For normal units, ensure it's vacant
        if ($unit->status === 'occupied') {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'The selected room is already occupied. Please choose another one.');
        }
    }

    // Upload files
    $validIdPath = $request->file('valid_id')->store('tenant_ids', 'public');
    $idPicturePath = $request->file('id_picture')->store('tenant_pictures', 'public');

    // Create user
    $user = User::create([
        'first_name'     => $request->first_name,
        'last_name'      => $request->last_name,
        'email'          => $request->email,
        'contact_number' => $request->contact,
        'password'       => Hash::make($request->password),
        'role'           => 'tenant',
        'status'         => 'pending',
        'terms_accepted' => true,
    ]);

    // Create tenant application
    TenantApplication::create([
        'user_id'               => $user->id,
        'full_name'             => $request->first_name . ' ' . $request->last_name,
        'email'                 => $request->email,
        'contact_number'        => $request->contact,
        'current_address'       => $request->current_address,
        'birthdate'             => $request->birthdate,
        'unit_type'             => $request->unit_type,
        'unit_id'               => $request->unit_id,
        'room_no'               => $unit->room_no,
        'move_in_date'          => $request->move_in_date,
        'reason'                => $request->reason,
        'employment_status'     => $request->employment_status,
        'employer_school'       => $request->employer_school,
        'source_of_income'      => $request->source_of_income,
        'emergency_name'        => $request->emergency_name,
        'emergency_number'      => $request->emergency_number,
        'emergency_relationship'=> $request->emergency_relationship,
        'valid_id_path'         => $validIdPath,
        'id_picture_path'       => $idPicturePath,
    ]);

    // Increment number of occupants for Bed-Spacer
    if ($unit->unit_type === 'Bed-Spacer') {
        $unit->increment('no_of_occupants');
    }

    return redirect()->route('login')
                    ->with('success', 'Tenant registration and application submitted successfully! Awaiting approval.');
}


}
