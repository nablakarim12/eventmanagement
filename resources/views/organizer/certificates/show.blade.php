@extends('organizer.layouts.app')

@section('title', 'Certificate Details')
@section('page-title', 'Certificate Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('organizer.certificates.index') }}" class="text-gray-400 hover:text-gray-500">
                        Certificates
                    </a>
                </li>
                <li class="flex">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">Certificate Details</span>
                </li>
            </ol>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Certificate Details</h1>
                <p class="text-gray-600 mt-2">{{ $certificate->user->name }} - {{ $certificate->event->title }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('organizer.certificates.download', $certificate) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF
                </a>
                @if(!$certificate->email_sent_at)
                <button onclick="emailCertificate()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Send Email
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Certificate Preview -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Certificate Preview</h3>
                </div>
                <div class="p-6">
                    <!-- Certificate Design Preview -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-8 border-2 border-indigo-200">
                        <div class="text-center space-y-6">
                            <!-- Header -->
                            <div class="space-y-2">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-indigo-900">EventSphere</h2>
                                <h3 class="text-xl font-semibold text-gray-800">Certificate of Attendance</h3>
                            </div>

                            <!-- Content -->
                            <div class="space-y-4">
                                <p class="text-lg text-gray-700">This is to certify that</p>
                                <p class="text-3xl font-bold text-indigo-900">{{ $certificate->user->name }}</p>
                                <p class="text-lg text-gray-700">has successfully attended</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ $certificate->event->title }}</p>
                                <p class="text-base text-gray-600">
                                    held on {{ $certificate->event->event_date->format('F j, Y') }}
                                </p>
                                <p class="text-base text-gray-600">
                                    with {{ $certificate->attendance_hours }} hours of attendance
                                </p>
                            </div>

                            <!-- Footer -->
                            <div class="flex justify-between items-end pt-8">
                                <div class="text-left">
                                    <div class="border-t border-gray-400 pt-2">
                                        <p class="text-sm text-gray-600">Event Organizer</p>
                                        <p class="text-sm font-medium text-gray-800">{{ $certificate->event->organizer->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="border-t border-gray-400 pt-2">
                                        <p class="text-sm text-gray-600">Date Issued</p>
                                        <p class="text-sm font-medium text-gray-800">{{ $certificate->generated_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificate ID -->
                            <div class="text-xs text-gray-500 border-t pt-4">
                                Certificate ID: {{ $certificate->certificate_number }}
                                @if($certificate->verification_url)
                                    <br>Verify at: {{ $certificate->verification_url }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Download Options -->
                    <div class="mt-6 flex justify-center space-x-4">
                        <a href="{{ route('organizer.certificates.download', $certificate) }}?format=pdf" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                            </svg>
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Information -->
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Certificate Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Participant</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->user->name }}</dd>
                        <dd class="text-sm text-gray-500">{{ $certificate->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Event</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ route('organizer.events.show', $certificate->event) }}" class="text-indigo-600 hover:text-indigo-500">
                                {{ $certificate->event->title }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Certificate Number</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $certificate->certificate_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Attendance Hours</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $certificate->attendance_hours }} hours
                            @if($certificate->event->min_attendance_hours)
                                <span class="text-gray-500">(Required: {{ $certificate->event->min_attendance_hours }}h)</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Generated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->generated_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @if($certificate->email_sent_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email Sent</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $certificate->email_sent_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Status</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Generated</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                Complete
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Email Sent</span>
                            @if($certificate->email_sent_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Sent
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Pending
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Downloads</span>
                            <span class="text-sm font-medium text-gray-900">{{ $certificate->download_count }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification -->
            @if($certificate->verification_url)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Verification</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Verification URL</label>
                            <div class="mt-1 flex">
                                <input type="text" id="verification-url" value="{{ $certificate->verification_url }}" readonly
                                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50">
                                <button onclick="copyVerificationUrl()" 
                                        class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <a href="{{ $certificate->verification_url }}" target="_blank"
                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Verify Certificate
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Download History -->
            @if($certificate->download_count > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Download History</h3>
                </div>
                <div class="p-6">
                    <div class="text-sm text-gray-600">
                        <p>Downloads: {{ $certificate->download_count }}</p>
                        @if($certificate->last_downloaded_at)
                            <p>Last downloaded: {{ $certificate->last_downloaded_at->format('M j, Y g:i A') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="notification-container" class="fixed top-4 right-4 z-50" style="display: none;">
    <div id="notification" class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg id="notification-icon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p id="notification-message" class="text-sm font-medium"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="hideNotification()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function emailCertificate() {
    if (confirm('Send certificate email to participant?')) {
        fetch(`{{ route('organizer.certificates.email', $certificate) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Email sent successfully');
                setTimeout(() => location.reload(), 2000);
            } else {
                showNotification('error', 'Error sending email: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'An error occurred while sending email');
        });
    }
}

function copyVerificationUrl() {
    const url = document.getElementById('verification-url').value;
    navigator.clipboard.writeText(url).then(() => {
        showNotification('success', 'Verification URL copied to clipboard');
    }).catch(() => {
        showNotification('error', 'Failed to copy URL');
    });
}

function showNotification(type, message) {
    const container = document.getElementById('notification-container');
    const notification = document.getElementById('notification');
    const icon = document.getElementById('notification-icon');
    const messageEl = document.getElementById('notification-message');
    
    messageEl.textContent = message;
    
    if (type === 'success') {
        icon.className = 'h-6 w-6 text-green-400';
        notification.className = 'max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4 border-green-400';
    } else {
        icon.className = 'h-6 w-6 text-red-400';
        notification.className = 'max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4 border-red-400';
    }
    
    container.style.display = 'block';
    setTimeout(hideNotification, 5000);
}

function hideNotification() {
    document.getElementById('notification-container').style.display = 'none';
}
</script>
@endsection