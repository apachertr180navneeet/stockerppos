<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Assuming this method will be used to display a view for managing units
        return view('admin.unit.index');
    }

    /**
     * Retrieve all units.
     */
    public function allunit()
    {
        try {
            // Fetch units ordered by ID in descending order
            $unit = Unit::orderBy('id', 'desc')->get();
            return response()->json(['data' => $unit]);
        } catch (\Throwable $th) {
            // It's good to handle exceptions, but consider logging them instead of dumping them
            dd($th);
        }
    }

    /**
     * Store a newly created unit.
     */
    public function store(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'unitName' => 'required|string|max:255|unique:units,unit_name',
                'unitCode' => 'required|string|unique:units,unit_code',
            ]);

            if ($validator->fails()) {
                // Check if unit with the same name and code exists (including soft deleted ones)
                $unitCheck = Unit::withTrashed()->where('unit_name', $request->unitName)
                    ->where('unit_code', $request->unitCode)->first();

                if (!empty($unitCheck)) {
                    // If found, restore the soft deleted record
                    $record = Unit::withTrashed()->find($unitCheck->id);
                    $record->restore();
                    return response()->json(['success' => 'Unit added successfully']);
                } else {
                    // If validation fails and no duplicate found, return validation errors
                    return response()->json(['errors' => $validator->errors()->all()]);
                }
            }

            // If validation passes, create a new unit
            Unit::create([
                'unit_code' => $request->unitCode,
                'unit_name' => $request->unitName,
            ]);

            return response()->json(['success' => 'Unit added successfully']);
        } catch (\Throwable $th) {
            dd($th); // Again, consider logging instead of dumping
        }
    }

    /**
     * Update the status of a unit.
     */
    public function status(Request $request)
    {
        try {
            // Get unit ID and status from request
            $unitid = $request->unitId;
            $status = $request->status;

            // Update status of the unit
            Unit::where('id', $unitid)->update(['status' => $status]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) { // Changed to \Exception for consistency
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a unit.
     */
    public function delete(Request $request)
    {
        try {
            // Get unit ID from request
            $unitId = $request->unitId;

            // Find and delete the unit
            $unit = Unit::find($unitId);
            $unit->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) { // Changed to \Exception for consistency
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Retrieve a unit for editing.
     */
    public function edit(Request $request)
    {
        try {
            // Get unit ID from request
            $unitId = $request->unitId;

            // Find the unit
            $unit = Unit::find($unitId);
            return response()->json(['success' => true, 'data' => $unit]);
        } catch (\Exception $e) { // Changed to \Exception for consistency
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a unit.
     */
    public function update(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'unitName' => 'required|string|max:255|unique:units,unit_name',
                'unitCode' => 'required|string|unique:units,unit_code',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->all()]);
            }

            // Update unit data
            $Unitdata = [
                'unit_code' => $request->unitCode,
                'unit_name' => $request->unitName,
            ];

            // Update the unit
            Unit::where('id', $request->unitId)->update($Unitdata);

            return response()->json(['success' => 'Unit Edit successfully']);
        } catch (\Throwable $th) {
            dd($th); // Again, consider logging instead of dumping
        }
    }
}
