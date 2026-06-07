<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medical Diagnostic Report - Lancet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 40px 35px;
            margin: 0;
        }
        
        /* Main Container with spacing */
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px 0;
        }
        
        /* Header Styles */
        .header {
            border-bottom: 3px solid #1a56db;
            padding-bottom: 25px;
            margin-bottom: 35px;
            overflow: hidden;
        }
        
        .logo-section {
            float: left;
            width: 60%;
        }
        
        .logo-section h1 {
            color: #1a56db;
            font-size: 28px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .logo-section .tagline {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 8px;
        }
        
        .logo-section .address {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.4;
        }
        
        .report-info {
            float: right;
            width: 35%;
            text-align: right;
            padding: 15px;
            background: #f9fafb;
            border-radius: 10px;
            margin-top: 5px;
        }
        
        .report-info p {
            margin: 8px 0;
            font-size: 11px;
        }
        
        .report-info strong {
            color: #1a56db;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-processing {
            background: #fed7aa;
            color: #92400e;
        }
        
        .status-delivered {
            background: #dbeafe;
            color: #1e40af;
        }
        
        /* Clearfix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        /* Title Section */
        .title-section {
            text-align: center;
            margin: 40px 0 35px;
            padding: 25px;
            background: linear-gradient(135deg, #f9fafb 0%, #fff 100%);
            border-radius: 12px;
        }
        
        .title-section h2 {
            color: #1a56db;
            font-size: 22px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }
        
        .report-id-badge {
            display: inline-block;
            background: #1a56db;
            color: white;
            padding: 6px 20px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 12px;
        }
        
        /* Info Grid */
        .info-grid {
            display: table;
            width: 100%;
            margin: 35px 0 40px;
            border-collapse: separate;
            border-spacing: 0 0;
        }
        
        .info-box {
            display: table-cell;
            width: 50%;
            padding: 20px;
            background: #f9fafb;
            border-radius: 10px;
            vertical-align: top;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .info-box:first-child {
            margin-right: 20px;
        }
        
        .info-box h4 {
            color: #1a56db;
            font-size: 15px;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .info-row {
            margin: 12px 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #4b5563;
            display: inline-block;
            min-width: 110px;
        }
        
        .info-value {
            color: #111827;
        }
        
        /* Results Section */
        .results-section {
            margin: 40px 0 35px;
        }
        
        .results-section h3 {
            color: #1a56db;
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0 30px;
            font-size: 11px;
        }
        
        .results-table th {
            background: #1a56db;
            color: white;
            padding: 14px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        .results-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .results-table tr:last-child td {
            border-bottom: none;
        }
        
        .results-table tr:hover {
            background: #f9fafb;
        }
        
        .reference-range {
            font-size: 10px;
            color: #6b7280;
        }
        
        .flag-high {
            color: #dc2626;
            font-weight: bold;
        }
        
        .flag-low {
            color: #f59e0b;
            font-weight: bold;
        }
        
        .flag-normal {
            color: #10b981;
        }
        
        /* Interpretation Section */
        .interpretation {
            background: #fef3c7;
            padding: 20px;
            border-left: 4px solid #f59e0b;
            margin: 35px 0;
            border-radius: 8px;
        }
        
        .interpretation h4 {
            color: #92400e;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .interpretation p {
            line-height: 1.6;
            color: #78350f;
        }
        
        /* QR Section */
        .qr-section {
            text-align: center;
            margin: 40px 0 35px;
            padding: 25px;
            background: #f9fafb;
            border-radius: 12px;
        }
        
        .qr-code {
            margin: 15px 0;
            padding: 10px;
        }
        
        .verification-text {
            margin-top: 15px;
        }
        
        .verification-text strong {
            color: #1a56db;
            font-size: 13px;
        }
        
        .verification-text .link {
            font-size: 10px;
            color: #6b7280;
            word-break: break-all;
            margin-top: 8px;
        }
        
        /* Signature Section */
        .signature-section {
            margin: 50px 0 30px;
            padding-top: 25px;
            text-align: right;
            border-top: 1px dashed #e5e7eb;
        }
        
        .signature-line {
            display: inline-block;
            width: 220px;
            border-top: 1px solid #333;
            margin-top: 35px;
            padding-top: 8px;
            font-size: 11px;
        }
        
        .signature-title {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        /* Disclaimer */
        .disclaimer {
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
            margin-top: 25px;
            padding: 15px;
            background: #fafafa;
            border-radius: 8px;
            line-height: 1.4;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 30px;
                margin: 0;
            }
            
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
            }
            
            .info-box, .qr-section, .interpretation {
                break-inside: avoid;
            }
            
            .results-table tr {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header clearfix">
            <div class="logo-section">
                <h1>⚕️ LANCET</h1>
                <div class="tagline">Diagnostic & Medical Services</div>
                <div class="address">
                    House #123, Road #5, Block #B<br>
                    Banani, Dhaka - 1213, Bangladesh<br>
                    Tel: +880 1234 567890 | Email: info@lancet.com.bd
                </div>
            </div>
            <div class="report-info">
                <p><strong>Report ID:</strong> <span style="font-size: 14px;">#{{ $report->id }}</span></p>
                <p><strong>Report Date:</strong> {{ now()->format('d M Y, h:i A') }}</p>
                <p><strong>Report Type:</strong> {{ ucfirst($report->type) }}</p>
                <p><strong>Status:</strong> 
                    @if($report->status == 'approved')
                        <span class="status-badge status-approved">✓ Approved</span>
                    @elseif($report->status == 'processing')
                        <span class="status-badge status-processing">⏳ Processing</span>
                    @elseif($report->status == 'delivered')
                        <span class="status-badge status-delivered">📋 Delivered</span>
                    @else
                        <span class="status-badge">{{ ucfirst($report->status) }}</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Title Section -->
        <div class="title-section">
            <h2>DIAGNOSTIC LABORATORY REPORT</h2>
            <div class="report-id-badge">Report #{{ $report->id }}</div>
        </div>

        <!-- Patient and Doctor Information -->
        <div class="info-grid">
            <div class="info-box">
                <h4>👤 Patient Information</h4>
                @if($report->testOrder && $report->testOrder->patient)
                    <div class="info-row">
                        <span class="info-label">Patient Name:</span>
                        <span class="info-value">{{ $report->testOrder->patient->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Patient ID:</span>
                        <span class="info-value">PID-{{ $report->testOrder->patient->id ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Age / Gender:</span>
                        <span class="info-value">
                            {{ $report->testOrder->patient->age ?? 'N/A' }} years / 
                            {{ $report->testOrder->patient->gender ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact:</span>
                        <span class="info-value">{{ $report->testOrder->patient->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Registration Date:</span>
                        <span class="info-value">{{ $report->testOrder->patient->created_at->format('d M Y') ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="info-row">Patient information not available</div>
                @endif
            </div>
            
            <div class="info-box">
                <h4>👨‍⚕️ Order & Clinical Information</h4>
                @if($report->testOrder)
                    <div class="info-row">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">ORD-{{ $report->testOrder->id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value">{{ $report->testOrder->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Type:</span>
                        <span class="info-value">{{ ucfirst($report->testOrder->priority ?? 'Routine') }}</span>
                    </div>
                    @if($report->testOrder->doctor)
                        <div class="info-row">
                            <span class="info-label">Referring Doctor:</span>
                            <span class="info-value">Dr. {{ $report->testOrder->doctor->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Doctor's Contact:</span>
                            <span class="info-value">{{ $report->testOrder->doctor->phone ?? 'N/A' }}</span>
                        </div>
                    @endif
                @endif
                @if($report->digital_signature)
                    <div class="info-row">
                        <span class="info-label">Digital Signature:</span>
                        <span class="info-value">{{ $report->digital_signature }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Test Results -->
        <div class="results-section">
            <h3>📊 Laboratory Test Results</h3>
            <table class="results-table">
                <thead>
                    <tr>
                        <th style="width: 35%">Test Name</th>
                        <th style="width: 20%">Result</th>
                        <th style="width: 30%">Reference Range</th>
                        <th style="width: 15%">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @if($report->testOrder && $report->testOrder->medicalTests && $report->testOrder->medicalTests->count() > 0)
                        @foreach($report->testOrder->medicalTests as $test)
                            <tr>
                                <td><strong>{{ $test->name ?? 'N/A' }}</strong></td>
                                <td>
                                    {{ $test->pivot->result_value ?? 'Pending' }}
                                    @if(isset($test->pivot->flag))
                                        <span class="flag-{{ $test->pivot->flag }}"> ({{ ucfirst($test->pivot->flag) }})</span>
                                    @endif
                                </td>
                                <td class="reference-range">{{ $test->reference_range ?? 'N/A' }}</td>
                                <td>{{ $test->unit ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px;">No test results available for this report</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Clinical Interpretation -->
        @if($report->interpretation ?? false)
        <div class="interpretation">
            <h4>📝 Clinical Interpretation & Notes</h4>
            <p>{{ $report->interpretation }}</p>
        </div>
        @endif

        <!-- QR Code Verification -->
        <div class="qr-section">
            <div class="qr-code">
                {!! QrCode::size(130)->generate(url('/reports/verify/' . $report->qr_code)) !!}
            </div>
            <div class="verification-text">
                <strong>Verify Report Authenticity</strong><br>
                <span style="font-size: 11px;">Scan QR code to verify this report is genuine</span><br>
                <div class="link">Verification Link: {{ url('/reports/verify/' . $report->qr_code) }}</div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-line">
                <strong>Dr. {{ config('app.lab_director', 'Md. Rahman') }}</strong>
            </div>
            <div class="signature-title">
                Chief Pathologist & Laboratory Director<br>
                Lancet Diagnostic Center
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Report Generation Note:</strong> This is a computer-generated report and does not require a physical signature.</p>
            <p><strong>Report Validity:</strong> For any discrepancies, please contact the laboratory within 7 days of report issuance.</p>
            <p>Generated on: {{ now()->format('d M Y h:i:s A') }} | Authorized by: Lancet Diagnostic System</p>
        </div>

        <!-- Disclaimer -->
        <div class="disclaimer">
            <strong>⚠️ Medical Disclaimer:</strong> This laboratory report is for informational purposes only and should be interpreted by a qualified healthcare provider. 
            The results should not be used as a substitute for professional medical advice, diagnosis, or treatment. 
            Always seek the advice of your physician or other qualified health provider with any questions you may have regarding a medical condition.
        </div>
    </div>
</body>
</html>