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
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .certificate-container {
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 1200px;
            width: 100%;
        }
        .certificate-image {
            width: 100%;
            height: auto;
            display: block;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3B82F6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .print-button:hover {
            background: #2563EB;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-button {
                display: none;
            }
            .certificate-container {
                box-shadow: none;
                max-width: none;
            }
        }
        .info-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 500px;
        }
        .info-overlay h2 {
            color: #1F2937;
            margin-bottom: 15px;
        }
        .info-overlay p {
            color: #6B7280;
            margin-bottom: 10px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button">🖨️ Print Certificate</button>
    
    <div class="certificate-container">
        @if($template->custom_template_url)
            <img src="{{ $template->custom_template_url }}" alt="Certificate" class="certificate-image">
            
            <div class="info-overlay">
                <h2>{{ $certificate->participant_name }}</h2>
                <p><strong>Role:</strong> {{ $certificate->participant_role }}</p>
                <p><strong>Event:</strong> {{ $certificate->event_name }}</p>
                @if($certificate->event_date)
                    <p><strong>Date:</strong> {{ $certificate->event_date }}</p>
                @endif
                @if($certificate->award_name)
                    <p><strong>Awards:</strong> {{ $certificate->award_name }}</p>
                @endif
            </div>
        @else
            <div class="info-overlay">
                <h2>Template Not Found</h2>
                <p>The custom certificate template has not been uploaded yet.</p>
            </div>
        @endif
    </div>
</body>
</html>
