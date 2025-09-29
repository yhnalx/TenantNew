<?php
// app/Http/Controllers/LandingController.php
namespace App\Http\Controllers;

use App\Models\Unit;

class LandingController extends Controller
{
    public function index()
    {
        // Hardcoded demo data (you can replace this with DB later)
        $units = collect([
            new Unit(['name' => 'Room 101', 'status' => 'vacant']),
            new Unit(['name' => 'Room 102', 'status' => 'occupied']),
            new Unit(['name' => 'Room 103', 'status' => 'vacant']),
            new Unit(['name' => 'Room 104', 'status' => 'occupied']),
        ]);

        // Count vacant rooms
        $vacantCount = $units->where('status', 'vacant')->count();

        return view('landing', compact('units', 'vacantCount'));
    }
}
