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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
        .event-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ef4444;
        }
        .reason-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
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
            <h1>Update on Your Jury Application</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $userName }},</p>
            
            <p>Thank you for your interest in serving as a jury member for <strong>{{ $eventTitle }}</strong>.</p>
            
            <p>After careful review of your application, we regret to inform you that we are unable to approve your jury application at this time.</p>
            
            @if($reason)
            <div class="reason-box">
                <strong>Reason:</strong><br>
                {{ $reason }}
            </div>
            @endif
            
            <p>We appreciate your interest and encourage you to:</p>
            <ul>
                <li>Participate in the event as an attendee</li>
                <li>Apply for future opportunities</li>
                <li>Contact us if you have questions about this decision</li>
            </ul>
            
            <p>Thank you for your understanding and continued interest in our events.</p>
            
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
