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
            font-family: 'Georgia', serif;
            background: #f3f4f6;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-wrapper {
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: white;
            padding: 40mm;
            border: 3px solid {{ $template->primary_color ?? '#2563eb' }};
        }

        /* Inner decorative border */
        .certificate::before {
            content: '';
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 1px solid {{ $template->secondary_color ?? '#93c5fd' }};
            pointer-events: none;
        }

        .logo {
            text-align: center;
            margin-bottom: 20mm;
        }

        .logo img {
            max-height: 25mm;
            max-width: 80mm;
        }

        .certificate-title {
            text-align: center;
            font-size: 48pt;
            font-weight: bold;
            color: {{ $template->primary_color ?? '#2563eb' }};
            margin-bottom: 15mm;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .certificate-subtitle {
            text-align: center;
            font-size: 20pt;
            color: {{ $template->text_color ?? '#1f2937' }};
            margin-bottom: 20mm;
            font-style: italic;
        }

        .certificate-body {
            text-align: center;
            color: {{ $template->text_color ?? '#1f2937' }};
        }

        .presented-to {
            font-size: 16pt;
            margin-bottom: 8mm;
        }

        .recipient-name {
            font-size: 36pt;
            font-weight: bold;
            color: {{ $template->primary_color ?? '#2563eb' }};
            margin-bottom: 12mm;
            border-bottom: 2px solid {{ $template->primary_color ?? '#2563eb' }};
            display: inline-block;
            padding: 0 20mm 3mm;
        }

        .recognition-text {
            font-size: 14pt;
            margin-bottom: 5mm;
            line-height: 1.6;
        }

        .role-text {
            font-size: 18pt;
            font-weight: bold;
            color: {{ $template->secondary_color ?? '#1e40af' }};
            margin-bottom: 10mm;
        }

        .event-name {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 8mm;
        }

        .event-date {
            font-size: 14pt;
            color: #6b7280;
            margin-bottom: 15mm;
        }

        .signature-section {
            margin-top: 20mm;
            text-align: center;
        }

        .signature-img {
            max-height: 15mm;
            margin-bottom: 3mm;
        }

        .signature-line {
            width: 60mm;
            border-top: 2px solid {{ $template->text_color ?? '#1f2937' }};
            margin: 0 auto 2mm;
        }

        .signature-name {
            font-size: 12pt;
            font-weight: bold;
        }

        .signature-title {
            font-size: 10pt;
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .print-button:hover {
            background: {{ $template->secondary_color ?? '#1e40af' }};
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
            @if($template->logo_url)
            <div class="logo">
                <img src="{{ $template->logo_url }}" alt="Logo">
            </div>
            @endif

            <div class="certificate-title">Certificate</div>
            <div class="certificate-subtitle">of Participation</div>

            <div class="certificate-body">
                <div class="presented-to">This certificate is proudly presented to</div>
                
                <div class="recipient-name">{{ $certificate->participant_name }}</div>

                <div class="recognition-text">For participating as</div>
                
                <div class="role-text">{{ $certificate->participant_role }}</div>

                <div class="recognition-text">at the event</div>

                <div class="event-name">{{ $certificate->event_name }}</div>

                <div class="event-date">Held on {{ $certificate->event_date }}</div>
            </div>

            @if($template->signature_url)
            <div class="signature-section">
                <img src="{{ $template->signature_url }}" alt="Signature" class="signature-img">
                <div class="signature-line"></div>
                @if($template->signature_name)
                    <div class="signature-name">{{ $template->signature_name }}</div>
                @endif
                @if($template->signature_title)
                    <div class="signature-title">{{ $template->signature_title }}</div>
                @endif
            </div>
            @endif
        </div>
    </div>
</body>
</html>
