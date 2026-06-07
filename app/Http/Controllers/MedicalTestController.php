<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalTestRequest;
use App\Http\Requests\UpdateMedicalTestRequest;
use App\Models\MedicalTest;
use Illuminate\Http\Request;

class MedicalTestController extends Controller
{
    public function index(Request $request)
    {
        $q = MedicalTest::with('category');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $q->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('test_category_id')) {
            $q->where('test_category_id', $request->integer('test_category_id'));
        }

        return response()->json($q->latest()->paginate(50));
    }

    public function store(StoreMedicalTestRequest $request)
    {
        $item = MedicalTest::create($request->validated());
        return response()->json($item->load('category'), 201);
    }

    public function show(int $id)
    {
        return response()->json(MedicalTest::with('category')->findOrFail($id));
    }

    public function update(UpdateMedicalTestRequest $request, int $id)
    {
        $item = MedicalTest::findOrFail($id);
        $item->update($request->validated());
        return response()->json($item->load('category'));
    }

    public function destroy(int $id)
    {
        MedicalTest::findOrFail($id)->delete();
        return response()->json(['message' => 'Medical test deleted']);
    }
}
