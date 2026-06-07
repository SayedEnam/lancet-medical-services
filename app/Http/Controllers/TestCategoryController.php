<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTestCategoryRequest;
use App\Http\Requests\UpdateTestCategoryRequest;
use App\Models\TestCategory;
use Illuminate\Http\Request;

class TestCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = TestCategory::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $q->where('name', 'like', "%{$search}%");
        }

        return response()->json($q->latest()->paginate(50));
    }

    public function store(StoreTestCategoryRequest $request)
    {
        $item = TestCategory::create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id)
    {
        return response()->json(TestCategory::findOrFail($id));
    }

    public function update(UpdateTestCategoryRequest $request, int $id)
    {
        $item = TestCategory::findOrFail($id);
        $item->update($request->validated());
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        TestCategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
