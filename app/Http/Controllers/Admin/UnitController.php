<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;


use App\Models\{
    Unit
};

use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.unit.index');
    }


    public function allunit()
    {
        try {
            $unit = Unit::orderBy('id', 'desc')->get();
            return response()->json(['data' => $unit]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'unitName' => 'required|string|max:255|unique:units,unit_name',
                'unitCode' => 'required|string|unique:units,unit_code',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()->all()]);
            }


            $Unitdata = [
                'unit_code' => $request->unitCode,
                'unit_name' => $request->unitName,
            ];

            Unit::create($Unitdata);

            return response()->json(['success' => 'Unit added successfully']);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

}
