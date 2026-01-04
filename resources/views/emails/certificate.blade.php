<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #333333;
            font-size: 22px;
            margin-bottom: 20px;
        }
        .content p {
            color: #666666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .certificate-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
        }
        .certificate-info p {
            margin: 8px 0;
            color: #333333;
        }
        .certificate-info strong {
            color: #667eea;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #999999;
            font-size: 14px;
        }
        .icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🎓</div>
            <h1>Congratulations!</h1>
        </div>
        
        <div class="content">
            <h2>Dear {{ $registration->user->name }},</h2>
            
            <p>We are delighted to send you your certificate for participating in <strong>{{ $event->title }}</strong>!</p>
            
            <div class="certificate-info">
                <p><strong>Event:</strong> {{ $event->title }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}</p>
                @if($registration->juryMappingsAsReviewer->count() > 0)
                    <p><strong>Role:</strong> Jury/Reviewer</p>
                @else
                    <p><strong>Role:</strong> Participant</p>
                @endif
            </div>
            
            <p>Your certificate is attached to this email. You can download and save it for your records.</p>
            
            <p>Thank you for your participation and contribution to making this event a success!</p>
            
            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>{{ $event->organizer_name ?? 'Event Organizer' }}</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p>&copy; {{ date('Y') }} Event Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
                <p>Thank you for your valuable contribution as a jury member. Your expertise and dedication helped make this event a success.</p>
            @else
                @if($certificate->project_name)
                    <p>Your contribution with the project <strong>"{{ $certificate->project_name }}"</strong> has been acknowledged and appreciated.</p>
                @endif
                
                @if($certificate->award_name)
                    <p>🏆 <strong>Special Recognition:</strong> You have been awarded the <strong>{{ $certificate->award_name }}</strong>! Congratulations on this outstanding achievement!</p>
                @endif
            @endif
            
            <div class="certificate-info">
                <p><strong>Event:</strong> {{ $certificate->event_name }}</p>
                @if($certificate->event_date)
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($certificate->event_date)->format('F j, Y') }}</p>
                @endif
                <p><strong>Participant Role:</strong> {{ ucfirst($certificate->certificate_type) }}</p>
                @if($certificate->participant_role)
                    <p><strong>Role:</strong> {{ $certificate->participant_role }}</p>
                @endif
            </div>
            
            <p>You can view and download your certificate by clicking the button below:</p>
            
            <div class="button-container">
                <a href="{{ route('certificates.view', $certificate->id) }}" class="download-button">
                    📄 View & Download Certificate
                </a>
            </div>
            
            <p>Your certificate is also available in your account dashboard. Simply login to your account and navigate to the "My Certificates" section to access all your certificates anytime.</p>
            
            <p>This certificate is a testament to your dedication and contribution. We encourage you to share it on your professional networks and social media platforms.</p>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>
            
            <p>Best regards,<br>
            <strong>Event Management Team</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply directly to this message.</p>
            <p>&copy; {{ date('Y') }} Event Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
