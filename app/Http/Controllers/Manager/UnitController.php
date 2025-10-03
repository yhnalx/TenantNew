<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Show all units with add form.
     */
    public function index()
    {
        $units = Unit::all();
        return view('manager.unitsetup', compact('units'));
    }

    /**
     * Store a new unit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'room_no' => 'required|string|max:50|unique:units,room_no',
            'room_price' => 'required|numeric|min:0',
            'status' => 'required|in:vacant,occupied',
        ]);

        Unit::create([
            'type' => $request->type,
            'room_no' => $request->room_no,
            'room_price' => $request->room_price,
            'status' => $request->status,
        ]);

        return redirect()->route('manager.units.index')->with('success', 'Unit added successfully.');
    }

    /**
     * Edit a unit.
     */
    public function edit($id)
    {
        return response()->json(Unit::findOrFail($id));
    }


    /**
     * Update a unit.
     */
    /**
 * Update a unit.
 */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'room_no' => 'required|string|max:50|unique:units,room_no,' . $unit->id,
            'room_price' => 'required|numeric|min:0',
            'status' => 'required|in:vacant,occupied',
        ]);

        $unit->update($request->only(['type', 'room_no', 'room_price', 'status']));

        return redirect()->route('manager.units.index')
            ->with('success', 'Unit updated successfully.');
    }

    /**
     * Delete a unit.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('manager.units.index')
            ->with('success', 'Unit deleted successfully.');
    }


}
