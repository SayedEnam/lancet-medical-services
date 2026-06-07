<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        return response()->json(Expense::latest('expense_date')->paginate(50));
    }

    public function store(StoreExpenseRequest $request)
    {
        $item = Expense::create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id)
    {
        return response()->json(Expense::findOrFail($id));
    }

    public function update(UpdateExpenseRequest $request, int $id)
    {
        $item = Expense::findOrFail($id);
        $item->update($request->validated());
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        Expense::findOrFail($id)->delete();
        return response()->json(['message' => 'Expense deleted']);
    }
}
