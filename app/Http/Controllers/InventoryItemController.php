<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryItemRequest;
use App\Http\Requests\UpdateInventoryItemRequest;
use App\Models\InventoryItem;

class InventoryItemController extends Controller
{
    public function index()
    {
        return response()->json(InventoryItem::latest()->paginate(50));
    }

    public function store(StoreInventoryItemRequest $request)
    {
        return response()->json(InventoryItem::create($request->validated()), 201);
    }

    public function show(int $id)
    {
        return response()->json(InventoryItem::findOrFail($id));
    }

    public function update(UpdateInventoryItemRequest $request, int $id)
    {
        $item = InventoryItem::findOrFail($id);
        $item->update($request->validated());
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        InventoryItem::findOrFail($id)->delete();
        return response()->json(['message' => 'Inventory item deleted']);
    }
}
