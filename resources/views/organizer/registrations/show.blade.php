@extends('organizer.layouts.app')

@section('title', 'Registration Details')
@section('page-title', 'Registration Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Registration Details</h1>
            <p class="text-gray-600 mt-1">{{ $registration->event->title }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('organizer.registrations.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Registration Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Participant Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user mr-2 text-blue-500"></i>Participant Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <p class="text-gray-900 font-medium">{{ $registration->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <p class="text-gray-900">{{ $registration->user->email }}</p>
                        </div>
                        @if($registration->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <p class="text-gray-900">{{ $registration->phone }}</p>
                            </div>
                        @endif
                        @if($registration->emergency_contact)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
                                <p class="text-gray-900">{{ $registration->emergency_contact }}</p>
                            </div>
                        @endif
                        @if($registration->dietary_requirements)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dietary Requirements</label>
                                <p class="text-gray-900">{{ $registration->dietary_requirements }}</p>
                            </div>
                        @endif
                        @if($registration->special_notes)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Special Notes</label>
                                <p class="text-gray-900">{{ $registration->special_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Registration Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-calendar-check mr-2 text-green-500"></i>Registration Details
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Code</label>
                            <p class="text-gray-900 font-mono text-sm">{{ $registration->registration_code }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Date</label>
                            <p class="text-gray-900">{{ $registration->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Role</label>
                            <div class="flex flex-wrap gap-2">
                                @if($registration->role === 'both')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-user mr-2"></i>Participant
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-gavel mr-2"></i>Jury
                                    </span>
                                @elseif($registration->role === 'participant')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-user mr-2"></i>Participant
                                    </span>
                                @elseif($registration->role === 'jury')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-gavel mr-2"></i>Jury
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-question mr-2"></i>{{ ucfirst($registration->role ?? 'Unknown') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($registration->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($registration->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($registration->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($registration->status === 'attended') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($registration->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($registration->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($registration->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($registration->payment_status === 'failed') bg-red-100 text-red-800
                                @elseif($registration->payment_status === 'refunded') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($registration->payment_status) }}
                            </span>
                        </div>
                        @if($registration->amount_paid)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
                                <p class="text-gray-900 font-semibold">${{ number_format($registration->amount_paid, 2) }}</p>
                            </div>
                        @endif
                        @if($registration->checked_in_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Time</label>
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($registration->checked_in_at)->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Event Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>Event Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Title</label>
                            <p class="text-gray-900 font-medium">{{ $registration->event->title }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Date</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($registration->event->start_date)->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <p class="text-gray-900">{{ $registration->event->location }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Price</label>
                            <p class="text-gray-900">${{ number_format($registration->event->registration_fee, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <!-- Jury Qualification (visible if registered as jury) -->
            @if($registration->isJury())
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-id-badge mr-2 text-indigo-500"></i>Jury Qualification
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        @if($registration->jury_qualification_summary)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Qualification Summary</label>
                            <p class="text-gray-900">{{ $registration->jury_qualification_summary }}</p>
                        </div>
                        @endif

                        @if($registration->jury_experience)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Experience</label>
                            <p class="text-gray-900">{{ $registration->jury_experience }}</p>
                        </div>
                        @endif

                        @if($registration->jury_expertise_areas)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expertise Areas</label>
                            <p class="text-gray-900">{{ $registration->jury_expertise_areas }}</p>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if($registration->jury_institution)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Institution</label>
                                <p class="text-gray-900">{{ $registration->jury_institution }}</p>
                            </div>
                            @endif

                            @if($registration->jury_position)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                <p class="text-gray-900">{{ $registration->jury_position }}</p>
                            </div>
                            @endif

                            @if(!is_null($registration->jury_years_experience))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Years of Experience</label>
                                <p class="text-gray-900">{{ $registration->jury_years_experience }} year(s)</p>
                            </div>
                            @endif
                        </div>

                        @php
                            // Check for documents in new format (array) or old format (single file)
                            $documents = [];
                            
                            if (!empty($registration->jury_qualification_documents)) {
                                // New format: multiple documents as array
                                $documents = $registration->jury_qualification_documents;
                            } elseif (!empty($registration->certificate_path)) {
                                // Old format: single certificate_path from friend's system
                                $documents = [$registration->certificate_path];
                            }
                        @endphp

                        @if(!empty($documents))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-file-alt mr-1"></i>Submitted Documents ({{ count($documents) }})
                            </label>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                @foreach($documents as $index => $doc)
                                    @php
                                        // Always use local storage URL
                                        $url = \Illuminate\Support\Facades\Storage::disk('public')->url($doc);
                                        
                                        $fileName = !empty($registration->certificate_filename) && $index === 0 
                                            ? $registration->certificate_filename 
                                            : basename($doc);
                                        $extension = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
                                        $isPdf = $extension === 'pdf';
                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                    @endphp
                                    <div class="flex items-center justify-between bg-white p-3 rounded border border-gray-200 hover:border-indigo-300 transition">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                @if($isPdf)
                                                    <div class="w-10 h-10 bg-red-100 rounded flex items-center justify-center">
                                                        <i class="fas fa-file-pdf text-red-600 text-lg"></i>
                                                    </div>
                                                @elseif($isImage)
                                                    <div class="w-10 h-10 bg-blue-100 rounded flex items-center justify-center">
                                                        <i class="fas fa-file-image text-blue-600 text-lg"></i>
                                                    </div>
                                                @else
                                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                                                        <i class="fas fa-file text-gray-600 text-lg"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $fileName }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ count($documents) > 1 ? 'Document ' . ($index + 1) : 'Jury Certificate' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ $url }}" target="_blank" 
                                               class="inline-flex items-center px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded transition">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                            <a href="{{ $url }}" download 
                                               class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition">
                                                <i class="fas fa-download mr-1"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Submitted Documents</label>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mb-2"></i>
                                <p class="text-sm text-yellow-800">No documents have been submitted yet.</p>
                            </div>
                        </div>
                        @endif

                        <!-- Jury Approval Section -->
                        <div class="border-t pt-4 mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-check-circle mr-1"></i>Jury Eligibility Review
                            </label>
                            
                            @if($registration->approval_status === 'approved')
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-check-circle text-green-600 text-xl mr-2"></i>
                                        <span class="text-green-800 font-semibold">Approved as Jury Member</span>
                                    </div>
                                    @if($registration->jury_approval_notes)
                                        <p class="text-sm text-green-700 mt-2"><strong>Notes:</strong> {{ $registration->jury_approval_notes }}</p>
                                    @endif
                                    @if($registration->jury_reviewed_at)
                                        <p class="text-xs text-green-600 mt-2">
                                            Reviewed on {{ \Carbon\Carbon::parse($registration->jury_reviewed_at)->format('F j, Y \a\t g:i A') }}
                                        </p>
                                    @endif
                                </div>
                            @elseif($registration->approval_status === 'rejected')
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-times-circle text-red-600 text-xl mr-2"></i>
                                        <span class="text-red-800 font-semibold">Jury Application Rejected</span>
                                    </div>
                                    @if($registration->rejected_reason || $registration->jury_approval_notes)
                                        <p class="text-sm text-red-700 mt-2">
                                            <strong>Reason:</strong> {{ $registration->rejected_reason ?? $registration->jury_approval_notes }}
                                        </p>
                                    @endif
                                    @if($registration->jury_reviewed_at)
                                        <p class="text-xs text-red-600 mt-2">
                                            Reviewed on {{ \Carbon\Carbon::parse($registration->jury_reviewed_at)->format('F j, Y \a\t g:i A') }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <i class="fas fa-hourglass-half text-blue-600 text-xl mr-2"></i>
                                        <span class="text-blue-800 font-semibold">Pending Review</span>
                                    </div>
                                    <p class="text-sm text-blue-700 mb-3">This jury application is awaiting your review and approval decision.</p>
                                    
                                    <!-- Quick Approval Buttons -->
                                    <div class="flex space-x-3">
                                        <form method="POST" action="{{ route('organizer.approvals.approve', $registration->id) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center justify-center"
                                                    onclick="return confirm('Approve this jury member?')">
                                                <i class="fas fa-check mr-2"></i>Approve as Jury
                                            </button>
                                        </form>
                                        <button type="button" 
                                                onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center justify-center">
                                            <i class="fas fa-times mr-2"></i>Reject Application
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($registration->jury_reviewed_at && ($registration->jury_approval_notes || $registration->rejected_reason))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Review Notes</label>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-gray-900">{{ $registration->jury_approval_notes ?? $registration->rejected_reason }}</p>
                                <div class="text-sm text-gray-500 mt-2">
                                    Reviewed at: {{ \Carbon\Carbon::parse($registration->jury_reviewed_at)->format('F j, Y \a\t g:i A') }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>Quick Actions
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    @if($registration->status !== 'attended')
                        <form method="POST" action="{{ route('organizer.registrations.check-in', $registration->id) }}">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>Check In Participant
                            </button>
                        </form>
                    @endif

                    <a href="mailto:{{ $registration->user->email }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope mr-2"></i>Send Email
                    </a>

                    <button onclick="window.print()" 
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>Print Details
                    </button>
                </div>
            </div>

            <!-- Status Management -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-cog mr-2 text-gray-500"></i>Status Management
                    </h2>
                </div>
                <div class="p-6">
                    <!-- Update Registration Status -->
                    <form method="POST" action="{{ route('organizer.registrations.update-status', $registration->id) }}" class="mb-4">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registration Status</label>
                        <div class="flex space-x-2">
                            <select name="status" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="pending" {{ $registration->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $registration->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="cancelled" {{ $registration->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="attended" {{ $registration->status === 'attended' ? 'selected' : '' }}>Attended</option>
                            </select>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm">
                                Update
                            </button>
                        </div>
                    </form>

                    <!-- Update Payment Status -->
                    <form method="POST" action="{{ route('organizer.registrations.update-payment', $registration->id) }}">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                        <div class="flex space-x-2">
                            <select name="payment_status" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="pending" {{ $registration->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $registration->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $registration->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $registration->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Custom Message -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-comment mr-2 text-orange-500"></i>Send Message
                    </h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('organizer.registrations.send-message') }}">
                        @csrf
                        <input type="hidden" name="registration_ids[]" value="{{ $registration->id }}">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Message subject...">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea name="message" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                      placeholder="Your message..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                            <i class="fas fa-paper-plane mr-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Jury Application Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>Reject Jury Application
                </h3>
                <button onclick="document.getElementById('rejectModal').classList.add('hidden')" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('organizer.approvals.reject', $registration->id) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
                    <textarea name="rejected_reason" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                              placeholder="Please provide a reason for rejecting this jury application..."></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('rejectModal').classList.add('hidden')"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                        Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media print {
    .lg\:col-span-1:last-child {
        display: none;
    }
    .container {
        max-width: none !important;
    }
}
</style>
@endsection