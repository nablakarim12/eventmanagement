@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.organizers.index') }}" class="text-indigo-600 hover:text-indigo-900">
            &larr; Back to Organizers
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold mb-2">{{ $organizer->org_name }}</h1>
                <p class="text-gray-600">{{ $organizer->org_email }}</p>
            </div>
            <div>
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    @if($organizer->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($organizer->status === 'approved') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($organizer->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold mb-2">Contact Information</h3>
                <p class="text-gray-600"><strong>Phone:</strong> {{ $organizer->phone ?: 'Not provided' }}</p>
                <p class="text-gray-600"><strong>Contact Person:</strong> {{ $organizer->contact_person_name }}</p>
                <p class="text-gray-600"><strong>Position:</strong> {{ $organizer->contact_person_position ?: 'Not provided' }}</p>
                @if($organizer->contact_person_phone)
                    <p class="text-gray-600"><strong>Contact Phone:</strong> {{ $organizer->contact_person_phone }}</p>
                @endif
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-2">Registration Details</h3>
                <p class="text-gray-600"><strong>Registered:</strong> {{ $organizer->created_at ? $organizer->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
                @if($organizer->approved_at)
                    <p class="text-gray-600">
                        <strong>{{ $organizer->status === 'approved' ? 'Approved' : 'Rejected' }}:</strong> 
                        {{ $organizer->approved_at ? $organizer->approved_at->format('M d, Y h:i A') : 'N/A' }}
                    </p>
                @endif
                @if($organizer->approved_by)
                    <p class="text-gray-600"><strong>Reviewed by:</strong> Admin #{{ $organizer->approved_by }}</p>
                @endif
            </div>
        </div>

        <!-- Organization Details -->
        @if($organizer->description || $organizer->website || $organizer->address)
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Organization Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        @if($organizer->description)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700">Description</p>
                                <p class="text-gray-600">{{ $organizer->description }}</p>
                            </div>
                        @endif
                        @if($organizer->website)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700">Website</p>
                                <a href="{{ $organizer->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $organizer->website }}
                                </a>
                            </div>
                        @endif
                    </div>
                    <div>
                        @if($organizer->address)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700">Address</p>
                                <p class="text-gray-600">
                                    {{ $organizer->address }}<br>
                                    @if($organizer->city || $organizer->state)
                                        {{ $organizer->city }}{{ $organizer->city && $organizer->state ? ', ' : '' }}{{ $organizer->state }}<br>
                                    @endif
                                    @if($organizer->country || $organizer->postal_code)
                                        {{ $organizer->country }} {{ $organizer->postal_code }}
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Supporting Documents -->
        <div class="border-t border-gray-200 pt-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Supporting Documents</h3>
            @if($organizer->documents && $organizer->documents->count() > 0)
                <div class="space-y-3">
                    @foreach($organizer->documents as $document)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $document->original_name }}</p>
                                    <p class="text-sm text-gray-500">
                                        Uploaded: {{ $document->created_at ? $document->created_at->format('M d, Y h:i A') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ $document->file_path }}" target="_blank" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ $document->file_path }}" download 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No documents uploaded</p>
                </div>
            @endif
        </div>

        @if($organizer->status === 'pending')
            <div class="border-t border-gray-200 pt-6">
                <div class="flex space-x-4">
                    <a href="mailto:{{ $organizer->org_email }}?subject=Event Organizer Account Approved&body=Dear {{ $organizer->contact_person_name }}, %0D%0A%0D%0ACongratulations! Your event organizer account for {{ $organizer->org_name }} has been approved. You can now log in to your dashboard and start creating events.%0D%0A%0D%0ABest regards,%0D%0AEventSphere Team"
                        onclick="event.preventDefault(); approveOrganizer(this.href);"
                        class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 cursor-pointer">
                        Approve
                    </a>

                    <a href="mailto:{{ $organizer->org_email }}?subject=Event Organizer Account Application Update&body=Dear {{ $organizer->contact_person_name }}, %0D%0A%0D%0AWe have reviewed your event organizer application for {{ $organizer->org_name }}. Unfortunately, we are unable to approve your application at this time.%0D%0A%0D%0AReason: [Please specify the reason]%0D%0A%0D%0AThank you for your interest.%0D%0A%0D%0ABest regards,%0D%0AEventSphere Team"
                        onclick="event.preventDefault(); rejectOrganizer(this.href);"
                        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 cursor-pointer">
                        Reject
                    </a>
                </div>

                <script>
                async function approveOrganizer(mailtoLink) {
                    if (confirm('Are you sure you want to approve this organizer?')) {
                        try {
                            const response = await fetch('{{ route('admin.organizers.approve', $organizer) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                // Show email options modal
                                showEmailOptions(mailtoLink, 'approval');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        }
                    }
                }

                function showEmailOptions(mailtoLink, type) {
                    const subject = type === 'approval' ? 'Event Organizer Account Approved' : 'Event Organizer Account Application Update';
                    const recipientEmail = '{{ $organizer->org_email }}';
                    
                    // Extract email body from mailto link
                    const bodyMatch = mailtoLink.match(/body=(.+)$/);
                    const emailBody = bodyMatch ? decodeURIComponent(bodyMatch[1]).replace(/%0D%0A/g, '\n') : '';
                    
                    // Create modal
                    const modal = document.createElement('div');
                    modal.innerHTML = `
                        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="this.remove()">
                            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Send Email Notification</h3>
                                <p class="mb-4 text-gray-600">Choose how you want to send the email:</p>
                                
                                <div class="space-y-3 mb-6">
                                    <button onclick="openGmailCompose('${recipientEmail}', '${encodeURIComponent(subject)}', '${encodeURIComponent(emailBody)}')" 
                                            class="w-full bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                                        Open Gmail Compose
                                    </button>
                                    
                                    <button onclick="window.open('${mailtoLink}', '_blank')" 
                                            class="w-full bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600">
                                        Use Default Email Client
                                    </button>
                                    
                                    <button onclick="copyEmailDetails('${recipientEmail}', '${subject}', \`${emailBody}\`)" 
                                            class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                                        Copy Email Details
                                    </button>
                                </div>
                                
                                <div class="flex justify-end space-x-2">
                                    <button onclick="this.closest('.fixed').remove(); window.location.reload();" 
                                            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                                        Done
                                    </button>
                                    <button onclick="this.closest('.fixed').remove()" 
                                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                }

                function openGmailCompose(to, subject, body) {
                    const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(to)}&su=${subject}&body=${body}`;
                    window.open(gmailUrl, '_blank');
                }

                function copyEmailDetails(to, subject, body) {
                    const emailText = `To: ${to}\nSubject: ${subject}\n\n${body}`;
                    navigator.clipboard.writeText(emailText).then(() => {
                        alert('Email details copied to clipboard!');
                    }).catch(() => {
                        // Fallback for older browsers
                        const textarea = document.createElement('textarea');
                        textarea.value = emailText;
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                        alert('Email details copied to clipboard!');
                    });
                }

                async function rejectOrganizer(mailtoLink) {
                    const reason = prompt('Please enter the rejection reason:');
                    if (reason) {
                        try {
                            const response = await fetch('{{ route('admin.organizers.reject', $organizer) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ rejection_reason: reason })
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                const updatedMailto = mailtoLink.replace('[Please specify the reason]', encodeURIComponent(reason));
                                showEmailOptions(updatedMailto, 'rejection');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        }
                    }
                }
                </script>
                        <div class="mb-4">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="3" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required></textarea>
                        </div>
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                            Confirm Rejection
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($organizer->status === 'rejected' && $organizer->rejection_reason)
            <div class="mt-6 p-4 bg-red-50 rounded-md">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Rejection Reason</h3>
                <p class="text-red-700">{{ $organizer->rejection_reason }}</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleRejectForm() {
    const form = document.getElementById('rejectForm');
    form.classList.toggle('hidden');
}
</script>
@endpush
@endsection