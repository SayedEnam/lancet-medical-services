<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        return response()->json(Supplier::latest()->paginate(50));
    }

    public function store(StoreSupplierRequest $request)
    {
        return response()->json(Supplier::create($request->validated()), 201);
    }

    public function show(int $id)
    {
        return response()->json(Supplier::findOrFail($id));
    }

    public function update(UpdateSupplierRequest $request, int $id)
    {
        $item = Supplier::findOrFail($id);
        $item->update($request->validated());
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        Supplier::findOrFail($id)->delete();
        return response()->json(['message' => 'Supplier deleted']);
    }
}
