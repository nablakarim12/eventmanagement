<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->participant_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            background: #f3f4f6;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-wrapper {
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 0;
            overflow: hidden;
        }

        /* Modern accent bar */
        .accent-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 15mm;
            background: linear-gradient(90deg, {{ $template->primary_color ?? '#2563eb' }}, {{ $template->secondary_color ?? '#1e40af' }});
        }

        /* Decorative corner */
        .corner-decoration {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0;
            height: 0;
            border-left: 80mm solid transparent;
            border-bottom: 80mm solid {{ $template->primary_color ?? '#2563eb' }};
            opacity: 0.05;
        }

        .content {
            position: relative;
            padding: 35mm 40mm;
            z-index: 1;
        }

        .logo {
            text-align: left;
            margin-bottom: 15mm;
        }

        .logo img {
            max-height: 20mm;
            max-width: 70mm;
        }

        .certificate-title {
            font-size: 44pt;
            font-weight: 700;
            color: {{ $template->primary_color ?? '#2563eb' }};
            margin-bottom: 3mm;
            letter-spacing: 2px;
        }

        .certificate-subtitle {
            font-size: 18pt;
            color: {{ $template->text_color ?? '#1f2937' }};
            margin-bottom: 20mm;
            font-weight: 300;
            letter-spacing: 1px;
        }

        .presented-to {
            font-size: 13pt;
            margin-bottom: 6mm;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
        }

        .recipient-name {
            font-size: 38pt;
            font-weight: 700;
            color: {{ $template->text_color ?? '#1f2937' }};
            margin-bottom: 12mm;
            position: relative;
            display: inline-block;
        }

        .recipient-name::after {
            content: '';
            position: absolute;
            bottom: -3mm;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, {{ $template->primary_color ?? '#2563eb' }}, transparent);
        }

        .recognition-text {
            font-size: 12pt;
            margin-bottom: 4mm;
            color: #6b7280;
        }

        .role-text {
            font-size: 20pt;
            font-weight: 600;
            color: {{ $template->secondary_color ?? '#1e40af' }};
            margin-bottom: 8mm;
        }

        .event-name {
            font-size: 18pt;
            font-weight: 600;
            margin-bottom: 6mm;
            color: {{ $template->text_color ?? '#1f2937' }};
        }

        .event-date {
            font-size: 12pt;
            color: #9ca3af;
            margin-bottom: 15mm;
        }

        .signature-section {
            margin-top: 15mm;
            display: flex;
            align-items: center;
            gap: 10mm;
        }

        .signature-box {
            text-align: center;
        }

        .signature-img {
            max-height: 12mm;
            margin-bottom: 2mm;
        }

        .signature-line {
            width: 50mm;
            border-top: 2px solid {{ $template->text_color ?? '#1f2937' }};
            margin: 0 auto 2mm;
        }

        .signature-name {
            font-size: 11pt;
            font-weight: 600;
        }

        .signature-title {
            font-size: 9pt;
            color: #6b7280;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: {{ $template->primary_color ?? '#2563eb' }};
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background: {{ $template->secondary_color ?? '#1e40af' }};
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .certificate-wrapper {
                box-shadow: none;
                padding: 0;
            }

            .print-button {
                display: none;
            }

            .certificate {
                page-break-after: always;
            }

            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Print Certificate</button>

    <div class="certificate-wrapper">
        <div class="certificate">
            <div class="accent-bar"></div>
            <div class="corner-decoration"></div>
            
            <div class="content">
                @if($template->logo_url)
                <div class="logo">
                    <img src="{{ $template->logo_url }}" alt="Logo">
                </div>
                @endif

                <div class="certificate-title">Certificate</div>
                <div class="certificate-subtitle">Of Participation</div>

                <div class="presented-to">Presented to</div>
                
                <div class="recipient-name">{{ $certificate->participant_name }}</div>

                <div class="recognition-text">For participating as</div>
                
                <div class="role-text">{{ $certificate->participant_role }}</div>

                <div class="recognition-text">at the event</div>

                <div class="event-name">{{ $certificate->event_name }}</div>

                <div class="event-date">{{ $certificate->event_date }}</div>

                @if($template->signature_url || $template->signature_name)
                <div class="signature-section">
                    <div class="signature-box">
                        @if($template->signature_url)
                            <img src="{{ $template->signature_url }}" alt="Signature" class="signature-img">
                        @endif
                        <div class="signature-line"></div>
                        @if($template->signature_name)
                            <div class="signature-name">{{ $template->signature_name }}</div>
                        @endif
                        @if($template->signature_title)
                            <div class="signature-title">{{ $template->signature_title }}</div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
