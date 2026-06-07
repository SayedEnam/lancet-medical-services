<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        return response()->json(Payment::with('invoice')->latest()->paginate(50));
    }

    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();

        $invoice = Invoice::findOrFail($data['invoice_id']);

        $payment = Payment::create($data);

        $totalPaid = (float) $invoice->payments()->sum('amount');
        $invoice->paid_amount = $totalPaid;
        $invoice->due_amount = max(0, (float) $invoice->total_amount - $totalPaid);
        $invoice->status = $invoice->due_amount <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'due');
        $invoice->save();

        return response()->json($payment->load('invoice'), 201);
    }

    public function show(int $id)
    {
        return response()->json(Payment::with('invoice')->findOrFail($id));
    }

    public function update(StorePaymentRequest $request, int $id)
    {
        $item = Payment::findOrFail($id);
        $item->update($request->validated());

        $invoice = Invoice::findOrFail($item->invoice_id);
        $totalPaid = (float) $invoice->payments()->sum('amount');
        $invoice->paid_amount = $totalPaid;
        $invoice->due_amount = max(0, (float) $invoice->total_amount - $totalPaid);
        $invoice->status = $invoice->due_amount <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'due');
        $invoice->save();

        return response()->json($item->load('invoice'));
    }

    public function destroy(int $id)
    {
        $item = Payment::findOrFail($id);
        $invoiceId = $item->invoice_id;
        $item->delete();

        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $totalPaid = (float) $invoice->payments()->sum('amount');
            $invoice->paid_amount = $totalPaid;
            $invoice->due_amount = max(0, (float) $invoice->total_amount - $totalPaid);
            $invoice->status = $invoice->due_amount <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'due');
            $invoice->save();
        }

        return response()->json(['message' => 'Payment deleted']);
    }
}
