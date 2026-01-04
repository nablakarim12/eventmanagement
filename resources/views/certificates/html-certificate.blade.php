<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->participant_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: white;
        }
        
        .certificate-container {
            width: 210mm;
            height: 297mm;
            position: relative;
            background: white;
            margin: 0 auto;
            background-image: url('{{ $template->template_url }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
        
        .text-overlay {
            position: absolute;
            text-align: center;
            font-weight: normal;
            z-index: 1;
        }
        
        .participant-name {
            left: {{ $template->name_x }}mm;
            top: {{ $template->name_y }}mm;
            font-size: {{ $template->name_font_size }}px;
            color: {{ $template->name_color }};
        }
        
        .participant-role {
            left: {{ $template->role_x }}mm;
            top: {{ $template->role_y }}mm;
            font-size: {{ $template->role_font_size }}px;
            color: {{ $template->role_color }};
        }
        
        .event-name {
            left: {{ $template->event_name_x }}mm;
            top: {{ $template->event_name_y }}mm;
            font-size: {{ $template->event_name_font_size }}px;
            color: {{ $template->event_name_color }};
        }
        
        .event-date {
            left: {{ $template->date_x }}mm;
            top: {{ $template->date_y }}mm;
            font-size: {{ $template->date_font_size }}px;
            color: {{ $template->date_color }};
        }
        
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .no-print button {
            background: #4F46E5;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .no-print button:hover {
            background: #4338CA;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .certificate-container {
                page-break-after: avoid;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            /* Force background images to print */
            * {
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Print Certificate</button>
    </div>
    
    <div class="certificate-container">
        <img src="{{ $template->template_url }}" alt="Certificate Template" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0;">
        <div class="text-overlay participant-name">{{ $certificate->participant_name }}</div>
        <div class="text-overlay participant-role">{{ $certificate->participant_role }}</div>
        <div class="text-overlay event-name">{{ $certificate->event_name }}</div>
        <div class="text-overlay event-date">{{ $certificate->event_date }}</div>
    </div>
</body>
</html>
