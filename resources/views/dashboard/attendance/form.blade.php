@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Manual Check-In</h1>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                    Backup Method
                </span>
            </div>
            
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Use this form if you're unable to scan the QR code for check-in. Please fill in all required information accurately.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Event Details -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Event Details</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex">
                        <span class="font-medium text-gray-600 w-32">Event:</span>
                        <span class="text-gray-800">{{ $event->title }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-medium text-gray-600 w-32">Date:</span>
                        <span class="text-gray-800">{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-medium text-gray-600 w-32">Location:</span>
                        <span class="text-gray-800">{{ $event->location }}</span>
                    </div>
                    <div class="flex">
                        <span class="font-medium text-gray-600 w-32">Your Role:</span>
                        <span class="text-gray-800">
                            @if($registration->registration_type === 'both')
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-semibold">Participant & Jury</span>
                            @elseif($registration->registration_type === 'jury')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">Jury</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Participant</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex">
                        <span class="font-medium text-gray-600 w-32">Registration #:</span>
                        <span class="text-gray-800 font-mono">{{ $registration->registration_code }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Check-in Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Check-In Information</h2>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('dashboard.attendance.submit', $event) }}" method="POST">
                @csrf
                <input type="hidden" name="registration_id" value="{{ $registration->id }}">

                <!-- Full Name -->
                <div class="mb-6">
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="full_name" 
                        id="full_name" 
                        value="{{ old('full_name', Auth::user()->name) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('full_name') border-red-500 @enderror"
                        placeholder="Enter your full name as registered"
                    >
                    @error('full_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-1">Must match your account name exactly</p>
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', Auth::user()->email) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="Enter your email address"
                    >
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-1">Must match your account email exactly</p>
                </div>

                <!-- Reason for Manual Check-in -->
                <div class="mb-6">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Manual Check-in <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="reason" 
                        id="reason" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('reason') border-red-500 @enderror"
                    >
                        <option value="">Select a reason</option>
                        <option value="qr_not_working" {{ old('reason') === 'qr_not_working' ? 'selected' : '' }}>QR Code Not Working</option>
                        <option value="forgot_qr" {{ old('reason') === 'forgot_qr' ? 'selected' : '' }}>Forgot QR Code</option>
                        <option value="technical_issue" {{ old('reason') === 'technical_issue' ? 'selected' : '' }}>Technical Issue with Scanner</option>
                        <option value="other" {{ old('reason') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('reason')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Notes -->
                <div class="mb-6">
                    <label for="additional_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Notes (Optional)
                    </label>
                    <textarea 
                        name="additional_notes" 
                        id="additional_notes" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('additional_notes') border-red-500 @enderror"
                        placeholder="Any additional information..."
                    >{{ old('additional_notes') }}</textarea>
                    @error('additional_notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror>
                </div>

                <!-- Warning Notice -->
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">
                                <strong>Important:</strong> By submitting this form, you confirm that you are physically present at the event location. False attendance submissions may result in registration cancellation.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <a 
                        href="{{ route('dashboard.registrations.show', $registration) }}" 
                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-semibold"
                    >
                        Submit Check-In
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Need Help?</h3>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>If the QR scanner is available at the venue, please try using it first</span>
                </li>
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Your name and email must exactly match your registered account</span>
                </li>
                <li class="flex items-start">
                    <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Contact event organizers if you continue to experience issues</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
