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
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #f3f4f6;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-wrapper {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 20px;
        }

        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: white;
            padding: 35mm 50mm;
        }

        /* Simple accent line */
        .accent-line {
            position: absolute;
            top: 20mm;
            left: 25mm;
            right: 25mm;
            height: 2px;
            background: {{ $template->primary_color ?? '#2563eb' }};
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .logo {
            margin-bottom: 18mm;
        }

        .logo img {
            max-height: 18mm;
            max-width: 60mm;
        }

        .certificate-title {
            font-size: 36pt;
            font-weight: 300;
            color: {{ $template->text_color ?? '#1f2937' }};
            margin-bottom: 3mm;
            letter-spacing: 8px;
            text-transform: uppercase;
        }

        .certificate-subtitle {
            font-size: 16pt;
            color: #9ca3af;
            margin-bottom: 20mm;
            font-weight: 300;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .presented-to {
            font-size: 11pt;
            margin-bottom: 6mm;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 400;
        }

        .recipient-name {
            font-size: 36pt;
            font-weight: 300;
            color: {{ $template->primary_color ?? '#2563eb' }};
            margin-bottom: 14mm;
            letter-spacing: 1px;
        }

        .recognition-text {
            font-size: 11pt;
            margin-bottom: 4mm;
            color: #9ca3af;
            font-weight: 300;
        }

        .role-text {
            font-size: 18pt;
            font-weight: 400;
            color: {{ $template->text_color ?? '#1f2937' }};
            margin-bottom: 10mm;
        }

        .event-name {
            font-size: 20pt;
            font-weight: 400;
            margin-bottom: 6mm;
            color: {{ $template->text_color ?? '#1f2937' }};
        }

        .event-date {
            font-size: 11pt;
            color: #9ca3af;
            margin-bottom: 18mm;
            font-weight: 300;
        }

        .signature-section {
            margin-top: 18mm;
            padding-top: 10mm;
            border-top: 1px solid #e5e7eb;
        }

        .signature-box {
            display: inline-block;
        }

        .signature-img {
            max-height: 12mm;
            margin-bottom: 3mm;
        }

        .signature-name {
            font-size: 11pt;
            font-weight: 400;
            color: {{ $template->text_color ?? '#1f2937' }};
        }

        .signature-title {
            font-size: 9pt;
            color: #9ca3af;
            margin-top: 1mm;
            font-weight: 300;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: {{ $template->primary_color ?? '#2563eb' }};
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
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
    <button class="print-button" onclick="window.print()">🖨️ Print</button>

    <div class="certificate-wrapper">
        <div class="certificate">
            <div class="accent-line"></div>
            
            <div class="content">
                @if($template->logo_url)
                <div class="logo">
                    <img src="{{ $template->logo_url }}" alt="Logo">
                </div>
                @endif

                <div class="certificate-title">Certificate</div>
                <div class="certificate-subtitle">of Participation</div>

                <div class="presented-to">Awarded to</div>
                
                <div class="recipient-name">{{ $certificate->participant_name }}</div>

                <div class="recognition-text">For participating as</div>
                
                <div class="role-text">{{ $certificate->participant_role }}</div>

                <div class="recognition-text">At the event</div>

                <div class="event-name">{{ $certificate->event_name }}</div>

                <div class="event-date">{{ $certificate->event_date }}</div>

                @if($template->signature_url || $template->signature_name)
                <div class="signature-section">
                    <div class="signature-box">
                        @if($template->signature_url)
                            <img src="{{ $template->signature_url }}" alt="Signature" class="signature-img">
                        @endif
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
