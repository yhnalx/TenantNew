<?php
// app/Http/Controllers/LandingController.php
namespace App\Http\Controllers;

use App\Models\Unit;

class LandingController extends Controller
{
    public function index()
    {
        // Fetch all units
        $units = Unit::all();

        // Count vacant rooms
        $vacantCount = $units->where('status', 'vacant')->count();

        // Get only available (vacant) units for the modal dropdown
        $availableUnits = Unit::where('status', 'vacant')->get();

        // Pass everything to the view
        return view('landing', compact('units', 'vacantCount', 'availableUnits'));
    }
}
