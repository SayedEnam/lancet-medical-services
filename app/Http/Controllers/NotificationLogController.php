<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\Http\Request;

class NotificationLogController extends Controller
{
    public function index()
    {
        return response()->json(NotificationLog::latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $item = NotificationLog::create($request->all());
        activity('NotificationLog')->event('created')->log('NotificationLog created');
        return response()->json($item, 201);
    }

    public function show(int $id)
    {
        return response()->json(NotificationLog::findOrFail($id));
    }

    public function update(Request $request, int $id)
    {
        $item = NotificationLog::findOrFail($id);
        $item->update($request->all());
        activity('NotificationLog')->event('updated')->log('NotificationLog updated');
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        $item = NotificationLog::findOrFail($id);
        $item->delete();
        activity('NotificationLog')->event('deleted')->log('NotificationLog deleted');
        return response()->json(['message' => 'NotificationLog deleted']);
    }
}
