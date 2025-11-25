@component('mail::message')
# Application Status Update

Dear {{ $organizer->contact_person_name }},

We have reviewed your event organizer application for **{{ $organizer->org_name }}**. Unfortunately, we are unable to approve your application at this time.

## Reason for Rejection:
{{ $reason }}

You can submit a new application addressing these concerns at any time.

@component('mail::button', ['url' => route('organizer.register')])
Submit New Application
@endcomponent

If you have any questions about this decision or need clarification, please feel free to contact our support team.

Thank you for your interest in our platform.

Best regards,<br>
{{ config('app.name') }}
@endcomponent