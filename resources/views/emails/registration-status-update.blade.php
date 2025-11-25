<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status Update</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            margin: 10px 0;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-attended {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .event-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">EventSphere</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Registration Status Update</p>
        </div>

        <div class="content">
            <h2>Hello {{ $user->name }},</h2>
            
            <p>We're writing to inform you about an update to your event registration:</p>

            <div class="event-details">
                <h3 style="margin-top: 0; color: #333;">{{ $event->title }}</h3>
                <p><strong>Event Date:</strong> {{ \Carbon\Carbon::parse($event->start_date)->format('F j, Y \a\t g:i A') }}</p>
                <p><strong>Location:</strong> {{ $event->location }}</p>
                <p><strong>Registration Code:</strong> {{ $registration->registration_code }}</p>
            </div>

            <p><strong>Status Update:</strong></p>
            <span class="status-badge status-{{ $registration->status }}">
                {{ ucfirst($registration->status) }}
            </span>

            @if($registration->status === 'confirmed')
                <p>Great news! Your registration has been confirmed. We're excited to see you at the event!</p>
                
                @if($event->start_date > now())
                    <p><strong>What's Next:</strong></p>
                    <ul>
                        <li>Save the date: {{ \Carbon\Carbon::parse($event->start_date)->format('F j, Y \a\t g:i A') }}</li>
                        <li>Location: {{ $event->location }}</li>
                        <li>Arrive 15 minutes early for check-in</li>
                        <li>Bring this email or your registration code for verification</li>
                    </ul>
                @endif

            @elseif($registration->status === 'pending')
                <p>Your registration is currently pending review. We'll notify you once it's been processed.</p>
                
            @elseif($registration->status === 'cancelled')
                <p>Unfortunately, your registration has been cancelled. If you have any questions about this change, please contact the event organizer.</p>
                
                @if($registration->payment_status === 'paid')
                    <p><em>If you made a payment, a refund will be processed within 5-7 business days.</em></p>
                @endif

            @elseif($registration->status === 'attended')
                <p>Thank you for attending {{ $event->title }}! We hope you had a great experience.</p>
                <p>We'd love to hear your feedback about the event. Stay tuned for our follow-up survey.</p>
            @endif

            @if($registration->status !== 'cancelled')
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('user.registrations.show', $registration->id) }}" class="btn">View Registration Details</a>
                </div>
            @endif

            @if($event->organizer_contact_email)
                <p><strong>Questions?</strong> Contact the event organizer at: 
                   <a href="mailto:{{ $event->organizer_contact_email }}">{{ $event->organizer_contact_email }}</a>
                </p>
            @endif
        </div>

        <div class="footer">
            <p>This email was sent by EventSphere regarding your event registration.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p style="margin-top: 15px;">
                <strong>EventSphere</strong><br>
                Your Premier Event Management Platform
            </p>
        </div>
    </div>
</body>
</html>