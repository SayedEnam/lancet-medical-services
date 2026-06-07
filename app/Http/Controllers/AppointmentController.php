<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Jobs\SendAppointmentBookedNotifications;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        return response()->json(Appointment::latest()->paginate(20));
    }

    public function store(StoreAppointmentRequest $request)
    {
        $item = Appointment::create($request->validated());
        SendAppointmentBookedNotifications::dispatch($item->id);

        return response()->json($item, 201);
    }

    public function show(int $id)
    {
        return response()->json(Appointment::findOrFail($id));
    }

    public function update(UpdateAppointmentRequest $request, int $id)
    {
        $item = Appointment::findOrFail($id);
        $item->update($request->validated());
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        Appointment::findOrFail($id)->delete();
        return response()->json(['message' => 'Appointment deleted']);
    }
}
