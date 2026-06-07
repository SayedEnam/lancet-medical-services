<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $q = Employee::with('user');

        if ($request->filled('status')) {
            $q->where('employment_status', $request->string('status')->toString());
        }

        return response()->json($q->latest()->paginate(50));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $item = Employee::create($request->validated());
        return response()->json($item->load('user'), 201);
    }

    public function show(int $id)
    {
        return response()->json(Employee::with('user')->findOrFail($id));
    }

    public function update(UpdateEmployeeRequest $request, int $id)
    {
        $item = Employee::findOrFail($id);
        $item->update($request->validated());
        return response()->json($item->load('user'));
    }

    public function destroy(int $id)
    {
        Employee::findOrFail($id)->delete();
        return response()->json(['message' => 'Employee deleted']);
    }
}
