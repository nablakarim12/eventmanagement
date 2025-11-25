<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .event-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .event-details h3 {
            margin-top: 0;
            color: #667eea;
        }
        .info-row {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 120px;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Congratulations!</h1>
            <p>Your Jury Application Has Been Approved</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $userName }},</p>
            
            <p>We are delighted to inform you that your application to serve as a <span class="badge">JURY MEMBER</span> has been <strong>approved</strong>!</p>
            
            <p>You have been selected to be part of the judging panel for the following event:</p>
            
            <div class="event-details">
                <h3>📋 Event Details</h3>
                <div class="info-row">
                    <span class="label">Event:</span>
                    <span>{{ $eventTitle }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Date:</span>
                    <span>{{ $eventDate }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Time:</span>
                    <span>{{ $eventTime }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Location:</span>
                    <span>{{ $eventLocation }}</span>
                </div>
            </div>
            
            <p><strong>What's Next?</strong></p>
            <ul>
                <li>You will receive further instructions and judging criteria closer to the event date</li>
                <li>Please mark your calendar and ensure your availability</li>
                <li>Review any materials sent by the organizers carefully</li>
                <li>Contact the organizer if you have any questions</li>
            </ul>
            
            <p>Thank you for your willingness to contribute your expertise and time to this event. Your participation will be invaluable!</p>
            
            <p>If you have any questions or concerns, please don't hesitate to contact us.</p>
            
            <div class="footer">
                <p>Best regards,<br>
                <strong>{{ $organizerName }}</strong><br>
                Event Organizer</p>
                
                <p style="margin-top: 20px; font-size: 12px; color: #999;">
                    This is an automated email from EventSphere. Please do not reply to this email.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
