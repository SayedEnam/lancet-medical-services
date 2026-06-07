<?php

namespace App\Http\Controllers;

use App\Models\TestResult;
use Illuminate\Http\Request;

class TestResultController extends Controller
{
    public function index()
    {
        return response()->json(TestResult::latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $item = TestResult::create($request->all());
        activity('TestResult')->event('created')->log('TestResult created');
        return response()->json($item, 201);
    }

    public function show(int $id)
    {
        return response()->json(TestResult::findOrFail($id));
    }

    public function update(Request $request, int $id)
    {
        $item = TestResult::findOrFail($id);
        $item->update($request->all());
        activity('TestResult')->event('updated')->log('TestResult updated');
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        $item = TestResult::findOrFail($id);
        $item->delete();
        activity('TestResult')->event('deleted')->log('TestResult deleted');
        return response()->json(['message' => 'TestResult deleted']);
    }
}
