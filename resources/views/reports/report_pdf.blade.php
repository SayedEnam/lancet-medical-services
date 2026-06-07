<!doctype html>
<html>
<head><meta charset="utf-8"><title>Report</title></head>
<body style="font-family: DejaVu Sans, sans-serif;">
  <h2>Lancet - Medical Services</h2>
  <h3>Diagnostic Report #{{ $report->id }}</h3>
  <p><strong>Type:</strong> {{ $report->type }}</p>
  <p><strong>Status:</strong> {{ $report->status }}</p>
  <p><strong>Test Order ID:</strong> {{ $report->test_order_id }}</p>
  <p><strong>QR Token:</strong> {{ $report->qr_code }}</p>
  <p><strong>Generated At:</strong> {{ now() }}</p>
</body>
</html>
