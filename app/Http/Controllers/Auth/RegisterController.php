<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate user input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'regex:/^[a-zA-Z0-9]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email'
            ],
            'contact'  => ['required', 'regex:/^[0-9]{10,15}$/'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ]);


        // Create user - always tenant by default
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'contact_number' => $request->contact,
            'password' => Hash::make($request->password),
            'role'     => 'tenant',   // default role
            'status'   => 'pending',  // default status
        ]);

        // Redirect to login (no auto-login)
        return redirect()->route('login')
            ->with('success', 'Tenant account created successfully! Please wait for manager approval before logging in.');
    }
}
