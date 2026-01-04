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
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .certificate-wrapper {
            position: relative;
            max-width: 1200px;
            width: 100%;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .certificate-image {
            width: 100%;
            height: auto;
            display: block;
        }
        .text-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        /* Based on your prompt: Place participant name under "This is certify that" */
        .participant-name {
            position: absolute;
            top: 34%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 40px;
            font-weight: bold;
            color: #000;
            text-align: center;
            width: 80%;
            font-family: 'Georgia', serif;
        }
        /* Based on your prompt: Place project name under "With the project" */
        .project-name {
            position: absolute;
            top: 51%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 28px;
            color: #333;
            text-align: center;
            width: 70%;
            font-family: 'Arial', sans-serif;
        }
        /* Based on your prompt: Place event name under "has successfully participated in" */
        .event-name {
            position: absolute;
            top: 65%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 18px;
            color: #000;
            text-align: center;
            width: 60%;
            font-family: 'Arial', sans-serif;
        }
        /* Based on your prompt: Place event date under "Held On" */
        .event-date {
            position: absolute;
            top: 78%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 16px;
            color: #000;
            text-align: center;
            font-family: 'Arial', sans-serif;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #8B5CF6;
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
            background: #7C3AED;
        }
        .ai-badge {
            position: fixed;
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            z-index: 1000;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-button, .ai-badge {
                display: none;
            }
            .certificate-wrapper {
                box-shadow: none;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="ai-badge">✨ AI-Generated</div>
    <button onclick="window.print()" class="print-button">🖨️ Print Certificate</button>
    
    <div class="certificate-wrapper">
        @if($template->template_file_url)
            <img src="{{ $template->template_file_url }}" alt="Certificate Template" class="certificate-image">
            
            <div class="text-overlay">
                <!-- Participant Name -->
                <div class="participant-name">{{ $certificate->participant_name }}</div>
                
                <!-- Project Name -->
                @if($certificate->project_name)
                <div class="project-name">{{ $certificate->project_name }}</div>
                @endif
                
                <!-- Event Name -->
                <div class="event-name">{{ $certificate->event_name }}</div>
                
                <!-- Event Date -->
                @if($certificate->event_date)
                <div class="event-date">{{ $certificate->event_date }}</div>
                @endif
            </div>
        @else
            <div style="padding: 40px; text-align: center;">
                <h2>Template Not Found</h2>
                <p>The AI certificate template has not been uploaded yet.</p>
            </div>
        @endif
    </div>
</body>
</html>
