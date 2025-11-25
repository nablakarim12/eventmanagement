@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('dashboard.registrations') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                ← Back to My Registrations
            </a>
        </div>

        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Registration Details</h1>
            <p class="text-gray-600 mt-2">{{ $registration->event->event_name }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Registration Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Registration Status</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-gray-500">Registration Code</span>
                            <p class="font-mono text-lg font-semibold">{{ $registration->registration_code }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-500">Role</span>
                            <p class="mt-1">
                                @if($registration->role === 'participant')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        Participant
                                    </span>
                                @elseif($registration->role === 'jury')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Jury
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        Both (Participant & Jury)
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-500">Approval Status</span>
                            <p class="mt-1">
                                @if($registration->approved_at)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        ✓ Approved
                                    </span>
                                @elseif($registration->rejected_at)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        ✗ Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        ⏱ Pending Approval
                                    </span>
                                @endif
                            </p>
                        </div>

                        @if($registration->checked_in_at)
                        <div>
                            <span class="text-sm text-gray-500">Check-In Status</span>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    ✓ Checked In
                                </span>
                                <span class="text-sm text-gray-600 ml-2">
                                    on {{ $registration->checked_in_at->format('F j, Y \a\t h:i A') }}
                                </span>
                            </p>
                        </div>
                        @endif

                        @if($registration->rejected_at && $registration->rejected_reason)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <span class="text-sm font-semibold text-red-800">Rejection Reason:</span>
                            <p class="text-sm text-red-700 mt-1">{{ $registration->rejected_reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Event Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Event Details</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500">Event Name</span>
                            <p class="font-semibold">{{ $registration->event->event_name }}</p>
                        </div>
                        
                        <div>
                            <span class="text-sm text-gray-500">Date & Time</span>
                            <p class="font-semibold">
                                {{ \Carbon\Carbon::parse($registration->event->event_date)->format('l, F j, Y') }}
                                @if($registration->event->start_time)
                                    at {{ \Carbon\Carbon::parse($registration->event->start_time)->format('h:i A') }}
                                @endif
                            </p>
                        </div>
                        
                        @if($registration->event->location)
                        <div>
                            <span class="text-sm text-gray-500">Location</span>
                            <p class="font-semibold">{{ $registration->event->location }}</p>
                        </div>
                        @endif
                        
                        @if($registration->event->description)
                        <div>
                            <span class="text-sm text-gray-500">Description</span>
                            <p class="text-gray-700">{{ $registration->event->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- QR Code -->
                @if($registration->approved_at && $registration->qr_image_path)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Your QR Code</h3>
                    
                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('storage/' . $registration->qr_image_path) }}" 
                             alt="Registration QR Code" 
                             class="w-64 h-64 border-4 border-gray-200 rounded-lg">
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-blue-800 text-center">
                            <strong>Scan this QR code at the event</strong> to check in automatically!
                        </p>
                    </div>
                    
                    @if(!$registration->checked_in_at)
                    <div class="text-center">
                        <a href="{{ route('qr.scan.registration', $registration->qr_code) }}" 
                           target="_blank"
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-200">
                            Test QR Code
                        </a>
                    </div>
                    @endif
                    
                    <!-- Download QR Button -->
                    <div class="mt-4 text-center">
                        <a href="{{ asset('storage/' . $registration->qr_image_path) }}" 
                           download="qr-{{ $registration->registration_code }}.png"
                           class="text-sm text-gray-600 hover:text-gray-800 underline">
                            Download QR Code
                        </a>
                    </div>
                </div>
                @elseif($registration->approved_at)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <p class="text-sm text-yellow-800 text-center">
                        Your QR code is being generated. Please refresh this page in a moment.
                    </p>
                </div>
                @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <p class="text-sm text-gray-600 text-center">
                            Your QR code will be generated once your registration is approved by the event organizer.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('events.show', $registration->event->slug) }}" 
                           class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                            View Event Page
                        </a>
                        
                        @if(!$registration->cancelled_at && !$registration->checked_in_at)
                        <form action="{{ route('dashboard.registrations.cancel', $registration) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to cancel this registration?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-100 hover:bg-red-200 text-red-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Cancel Registration
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
