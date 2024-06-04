<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
                Item,
                Unit
            };
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Assuming this method will be used to display a view for managing units
        $units = Unit::where('status','active')->orderBy('id', 'desc')->get();
        return view('admin.item.index',compact('units'));
    }


    /**
     * Retrieve all Items.
     */
    public function allitems()
    {
        try {
            // Fetch units ordered by ID in descending order
            $item = Item::select('items.*', 'units.unit_code', 'units.unit_name')->leftJoin('units', 'items.id', '=', 'units.id')->orderBy('id', 'desc')->get();
            return response()->json(['data' => $item]);
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
                'itemname' => 'required|string|max:255|unique:items,name',
                'description' => 'required|string',
                'unit' => 'required',
            ]);

            if ($validator->fails()) {
                // Check if unit with the same name and code exists (including soft deleted ones)
                $itemCheck = Item::withTrashed()->where('name', $request->itemname)->first();

                if (!empty($itemCheck)) {
                    // If found, restore the soft deleted record
                    $record = Item::withTrashed()->find($itemCheck->id);
                    $record->restore();
                    return response()->json(['success' => 'Unit added successfully']);
                } else {
                    // If validation fails and no duplicate found, return validation errors
                    return response()->json(['errors' => $validator->errors()->all()]);
                }
            }

            // If validation passes, create a new unit
            Item::create([
                'name' => $request->itemname,
                'description' => $request->description,
                'unit_id' => $request->unit,
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
            $ItemId = $request->ItemId;
            $status = $request->status;

            // Update status of the unit
            Item::where('id', $ItemId)->update(['status' => $status]);

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
            $itemId = $request->itemId;

            // Find and delete the unit
            $unit = Item::find($itemId);
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
            $itemId = $request->itemId;

            // Find the unit
            $item = Item::find($itemId);
            return response()->json(['success' => true, 'data' => $item]);
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
                'itemName' => 'required|string|max:255',
                'description' => 'required|string',
                'unit' => 'required',
            ]);


            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->all()]);
            }

            // Update unit data
            $Itemdata = [
                'name' => $request->itemName,
                'description' => $request->description,
                'unit_id' => $request->unit,
            ];

            // Update the unit
            Item::where('id', $request->ItemId)->update($Itemdata);

            return response()->json(['success' => 'Item Edit successfully']);
        } catch (\Throwable $th) {
            dd($th); // Again, consider logging instead of dumping
        }
    }
}
