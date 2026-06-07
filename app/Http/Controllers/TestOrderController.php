<?php

namespace App\Http\Controllers;

use App\Models\TestOrder;
use Illuminate\Http\Request;

class TestOrderController extends Controller
{
    public function index()
    {
        return response()->json(TestOrder::latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $item = TestOrder::create($request->all());
        activity('TestOrder')->event('created')->log('TestOrder created');
        return response()->json($item, 201);
    }

    public function show(int $id)
    {
        return response()->json(TestOrder::findOrFail($id));
    }

    public function update(Request $request, int $id)
    {
        $item = TestOrder::findOrFail($id);
        $item->update($request->all());
        activity('TestOrder')->event('updated')->log('TestOrder updated');
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        $item = TestOrder::findOrFail($id);
        $item->delete();
        activity('TestOrder')->event('deleted')->log('TestOrder deleted');
        return response()->json(['message' => 'TestOrder deleted']);
    }
}
