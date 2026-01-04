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
            font-family: 'Times New Roman', serif;
            background: #f3f4f6;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-wrapper {
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 20px;
        }

        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: linear-gradient(to bottom, #ffffff 0%, #fef9f3 100%);
            padding: 15mm;
        }

        /* Elegant double border */
        .outer-border {
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 2px solid {{ $template->primary_color ?? '#b8860b' }};
            padding: 5mm;
        }

        .inner-border {
            position: absolute;
            top: 13mm;
            left: 13mm;
            right: 13mm;
            bottom: 13mm;
            border: 1px solid {{ $template->secondary_color ?? '#daa520' }};
        }

        /* Decorative corners */
        .corner {
            position: absolute;
            width: 20mm;
            height: 20mm;
            border: 2px solid {{ $template->primary_color ?? '#b8860b' }};
        }

        .corner.top-left {
            top: 8mm;
            left: 8mm;
            border-right: none;
            border-bottom: none;
        }

        .corner.top-right {
            top: 8mm;
            right: 8mm;
            border-left: none;
            border-bottom: none;
        }

        .corner.bottom-left {
            bottom: 8mm;
            left: 8mm;
            border-right: none;
            border-top: none;
        }

        .corner.bottom-right {
            bottom: 8mm;
            right: 8mm;
            border-left: none;
            border-top: none;
        }

        .content {
            position: relative;
            padding: 35mm 40mm;
            z-index: 1;
            text-align: center;
        }

        .logo {
            margin-bottom: 15mm;
        }

        .logo img {
            max-height: 22mm;
            max-width: 75mm;
        }

        .certificate-title {
            font-size: 52pt;
            font-weight: bold;
            color: {{ $template->primary_color ?? '#b8860b' }};
            margin-bottom: 8mm;
            letter-spacing: 6px;
            font-style: italic;
        }

        .certificate-subtitle {
            font-size: 22pt;
            color: {{ $template->text_color ?? '#1f2937' }};
            margin-bottom: 18mm;
            font-style: italic;
            font-weight: normal;
        }

        .ornament {
            font-size: 24pt;
            color: {{ $template->secondary_color ?? '#daa520' }};
            margin: 5mm 0;
        }

        .presented-to {
            font-size: 15pt;
            margin-bottom: 8mm;
            color: #6b7280;
            font-style: italic;
        }

        .recipient-name {
            font-size: 40pt;
            font-weight: bold;
            color: {{ $template->text_color ?? '#1f2937' }};
            margin-bottom: 12mm;
            font-style: italic;
            border-bottom: 2px solid {{ $template->primary_color ?? '#b8860b' }};
            display: inline-block;
            padding: 0 25mm 3mm;
        }

        .recognition-text {
            font-size: 13pt;
            margin-bottom: 5mm;
            line-height: 1.6;
            font-style: italic;
        }

        .role-text {
            font-size: 19pt;
            font-weight: bold;
            color: {{ $template->secondary_color ?? '#daa520' }};
            margin-bottom: 10mm;
        }

        .event-name {
            font-size: 21pt;
            font-weight: bold;
            margin-bottom: 8mm;
            font-style: italic;
        }

        .event-date {
            font-size: 13pt;
            color: #6b7280;
            margin-bottom: 18mm;
            font-style: italic;
        }

        .signature-section {
            margin-top: 20mm;
        }

        .signature-box {
            display: inline-block;
        }

        .signature-img {
            max-height: 15mm;
            margin-bottom: 3mm;
        }

        .signature-line {
            width: 65mm;
            border-top: 2px solid {{ $template->text_color ?? '#1f2937' }};
            margin: 0 auto 3mm;
        }

        .signature-name {
            font-size: 13pt;
            font-weight: bold;
            font-style: italic;
        }

        .signature-title {
            font-size: 11pt;
            color: #6b7280;
            font-style: italic;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: {{ $template->primary_color ?? '#b8860b' }};
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
            background: {{ $template->secondary_color ?? '#daa520' }};
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
            <div class="outer-border"></div>
            <div class="inner-border"></div>
            <div class="corner top-left"></div>
            <div class="corner top-right"></div>
            <div class="corner bottom-left"></div>
            <div class="corner bottom-right"></div>
            
            <div class="content">
                @if($template->logo_url)
                <div class="logo">
                    <img src="{{ $template->logo_url }}" alt="Logo">
                </div>
                @endif

                <div class="certificate-title">Certificate</div>
                <div class="ornament">❦</div>
                <div class="certificate-subtitle">of Participation</div>

                <div class="presented-to">This is to certify that</div>
                
                <div class="recipient-name">{{ $certificate->participant_name }}</div>

                <div class="recognition-text">has participated as</div>
                
                <div class="role-text">{{ $certificate->participant_role }}</div>

                <div class="recognition-text">in the event</div>

                <div class="event-name">{{ $certificate->event_name }}</div>

                <div class="event-date">held on {{ $certificate->event_date }}</div>

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
