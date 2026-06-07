<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index()
    {
        return response()->json(Patient::latest()->paginate(20));
    }

    public function store(StorePatientRequest $request)
    {
        $item = Patient::create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id)
    {
        return response()->json(Patient::findOrFail($id));
    }

    public function update(UpdatePatientRequest $request, int $id)
    {
        $item = Patient::findOrFail($id);
        $item->update($request->validated());
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        Patient::findOrFail($id)->delete();
        return response()->json(['message' => 'Patient deleted']);
    }
}
