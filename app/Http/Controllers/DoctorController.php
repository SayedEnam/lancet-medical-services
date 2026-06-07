<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $q = Doctor::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        return response()->json($q->latest()->paginate(20));
    }

    public function store(StoreDoctorRequest $request)
    {
        $item = Doctor::create($request->validated());
        return response()->json($item, 201);
    }

    public function show(int $id)
    {
        return response()->json(Doctor::findOrFail($id));
    }

    public function update(UpdateDoctorRequest $request, int $id)
    {
        $item = Doctor::findOrFail($id);
        $item->update($request->validated());
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        Doctor::findOrFail($id)->delete();
        return response()->json(['message' => 'Doctor deleted']);
    }
}
