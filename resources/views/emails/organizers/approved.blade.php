@component('mail::message')
# Account Approved

Dear {{ $organizer->contact_person_name }},

Congratulations! Your event organizer account for **{{ $organizer->org_name }}** has been approved. You can now log in to your dashboard and start creating events.

@component('mail::button', ['url' => route('organizer.login')])
Login to Your Account
@endcomponent

What's Next:
- Log in to your account
- Complete your organization profile
- Start creating and managing events
- Access organizer tools and features

If you have any questions, please don't hesitate to contact us.

Thank you for choosing our platform!

Best regards,<br>
{{ config('app.name') }}
@endcomponent