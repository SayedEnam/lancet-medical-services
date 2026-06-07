<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index()
    {
        return response()->json(Prescription::latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $item = Prescription::create($request->all());
        activity('Prescription')->event('created')->log('Prescription created');
        return response()->json($item, 201);
    }

    public function show(int $id)
    {
        return response()->json(Prescription::findOrFail($id));
    }

    public function update(Request $request, int $id)
    {
        $item = Prescription::findOrFail($id);
        $item->update($request->all());
        activity('Prescription')->event('updated')->log('Prescription updated');
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        $item = Prescription::findOrFail($id);
        $item->delete();
        activity('Prescription')->event('deleted')->log('Prescription deleted');
        return response()->json(['message' => 'Prescription deleted']);
    }
}
