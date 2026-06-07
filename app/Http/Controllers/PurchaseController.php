<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\InventoryItem;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function index()
    {
        return response()->json(Purchase::with(['supplier', 'inventoryItem'])->latest()->paginate(50));
    }

    public function store(StorePurchaseRequest $request)
    {
        $data = $request->validated();
        $data['total_cost'] = (float) $data['quantity'] * (float) $data['unit_cost'];

        $purchase = Purchase::create($data);

        $item = InventoryItem::findOrFail($data['inventory_item_id']);
        $item->stock = (int) $item->stock + (int) $data['quantity'];
        $item->save();

        return response()->json($purchase->load(['supplier', 'inventoryItem']), 201);
    }

    public function show(int $id)
    {
        return response()->json(Purchase::with(['supplier', 'inventoryItem'])->findOrFail($id));
    }

    public function update(StorePurchaseRequest $request, int $id)
    {
        $purchase = Purchase::findOrFail($id);
        return response()->json($purchase);
    }

    public function destroy(int $id)
    {
        Purchase::findOrFail($id)->delete();
        return response()->json(['message' => 'Purchase deleted']);
    }
}
