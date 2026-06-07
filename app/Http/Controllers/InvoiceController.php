<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        return response()->json(Invoice::with('patient')->latest()->paginate(50));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->validated();

        $subTotal = (float) ($data['sub_total'] ?? 0);
        $discount = (float) ($data['discount'] ?? 0);
        $vat = (float) ($data['vat'] ?? 0);
        $paid = (float) ($data['paid_amount'] ?? 0);

        $total = max(0, $subTotal - $discount + $vat);
        $due = max(0, $total - $paid);

        $data['invoice_no'] = $data['invoice_no'] ?? ('INV-' . now()->format('ymd') . '-' . Str::upper(Str::random(4)));
        $data['total_amount'] = $total;
        $data['due_amount'] = $due;
        $data['status'] = $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'due');

        $item = Invoice::create($data);

        return response()->json($item->load('patient'), 201);
    }

    public function show(int $id)
    {
        return response()->json(Invoice::with(['patient', 'payments'])->findOrFail($id));
    }

    public function update(UpdateInvoiceRequest $request, int $id)
    {
        $item = Invoice::findOrFail($id);

        $item->fill($request->validated());

        $subTotal = (float) $item->sub_total;
        $discount = (float) ($item->discount ?? 0);
        $vat = (float) ($item->vat ?? 0);
        $paid = (float) ($item->paid_amount ?? 0);

        $total = max(0, $subTotal - $discount + $vat);
        $due = max(0, $total - $paid);

        $item->total_amount = $total;
        $item->due_amount = $due;
        $item->status = $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'due');
        $item->save();

        return response()->json($item->load('patient'));
    }

    public function destroy(int $id)
    {
        Invoice::findOrFail($id)->delete();

        return response()->json(['message' => 'Invoice deleted']);
    }
}
