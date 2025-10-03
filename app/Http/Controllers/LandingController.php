<?php
// app/Http/Controllers/LandingController.php
namespace App\Http\Controllers;

use App\Models\Unit;

class LandingController extends Controller
{
    public function index()
    {
        // Fetch all units from the database
        $units = Unit::all();

        // Count vacant rooms
        $vacantCount = $units->where('status', 'vacant')->count();

        return view('landing', compact('units', 'vacantCount'));
    }
}
