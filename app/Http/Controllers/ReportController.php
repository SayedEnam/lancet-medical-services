<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $q = Report::with('testOrder');

        if ($request->filled('status')) {
            $q->where('status', $request->string('status')->toString());
        }

        if ($request->filled('type')) {
            $q->where('type', $request->string('type')->toString());
        }

        return response()->json($q->latest()->paginate(50));
    }

    public function store(StoreReportRequest $request)
    {
        $payload = $request->validated();
        $payload['status'] = $payload['status'] ?? 'processing';
        $payload['qr_code'] = Str::uuid()->toString();

        $item = Report::create($payload);

        return response()->json($item->load('testOrder'), 201);
    }

    public function show(int $id)
    {
        return response()->json(Report::with('testOrder')->findOrFail($id));
    }

    public function update(UpdateReportRequest $request, int $id)
    {
        $item = Report::findOrFail($id);
        $item->update($request->validated());

        return response()->json($item->load('testOrder'));
    }

    public function destroy(int $id)
    {
        $item = Report::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Report deleted']);
    }

    public function setStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:processing,pending_approval,approved,delivered'],
        ]);

        $item = Report::findOrFail($id);
        $item->update(['status' => $data['status']]);

        return response()->json($item);
    }

    public function generatePdf(int $id)
    {
        $report = Report::with('testOrder')->findOrFail($id);

        $pdf = Pdf::loadHTML(view('reports.report_pdf', compact('report'))->render());
        $path = 'reports/report_' . $report->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $report->update(['pdf_path' => $path]);

        return response()->json([
            'message' => 'PDF generated',
            'download_url' => route('reports.download', $report->id),
        ]);
    }

    public function downloadPdf(int $id)
    {
        $report = Report::findOrFail($id);

        if (! $report->pdf_path || ! Storage::disk('public')->exists($report->pdf_path)) {
            return response()->json(['message' => 'PDF not found'], 404);
        }

        return Storage::disk('public')->download($report->pdf_path, 'report_' . $report->id . '.pdf');
    }

    public function verifyQr(string $token)
    {
        $report = Report::where('qr_code', $token)->first();

        if (! $report) {
            return response()->json(['valid' => false, 'message' => 'Invalid QR code'], 404);
        }

        return response()->json([
            'valid' => true,
            'report' => $report,
        ]);
    }
}
