<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'in:tenant,manager' // optional dropdown or hidden field
        ]);

        // Create user (default to tenant if role not provided)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role ?? 'tenant',
            'status'   => $request->role === 'tenant' ? 'pending' : 'approved'
        ]);

        // Auto-login after registration
        Auth::login($user);

        // Redirect based on role
        if ($user->role === 'manager') {
            return redirect()->route('manager.dashboard')
                ->with('success', 'Manager account created successfully!');
        }

        return redirect()->route('tenant.home')
            ->with('success', 'Tenant account created successfully! Please wait for approval.');
    }
}
