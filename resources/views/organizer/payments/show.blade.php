@extends('organizer.layouts.app')

@section('title', 'Payment Details - ' . $registration->user->name)

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('organizer.payment-verification.event', $registration->event_id) }}" class="text-blue-600 hover:text-blue-800 mb-3 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Back to Event Payments
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Verification</h1>
            <p class="text-gray-600">Review payment proof and take action</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3 mt-0.5"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-red-800">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column: Registration Details -->
            <div class="space-y-6">
                <!-- Participant Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2"></i>Participant Information
                    </h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="text-base text-gray-900 font-medium">{{ $registration->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="text-base text-gray-900">{{ $registration->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Role</dt>
                            <dd>
                                @if($registration->role === 'participant')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Participant</span>
                                @elseif($registration->role === 'jury')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Jury</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Both</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Event Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-calendar-alt mr-2"></i>Event Information
                    </h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Event Name</dt>
                            <dd class="text-base text-gray-900 font-medium">{{ $registration->event->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Registration Fee</dt>
                            <dd class="text-xl text-gray-900 font-bold">RM {{ number_format($registration->event->registration_fee, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Registered At</dt>
                            <dd class="text-base text-gray-900">{{ $registration->created_at->format('M d, Y H:i A') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Payment Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Payment Status
                    </h2>
                    <div class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Current Status</dt>
                            <dd>
                                @if($registration->payment_status === 'approved')
                                    <span class="px-4 py-2 text-sm font-medium rounded-lg bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i>Paid
                                    </span>
                                @elseif($registration->payment_status === 'rejected')
                                    <span class="px-4 py-2 text-sm font-medium rounded-lg bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-2"></i>Rejected
                                    </span>
                                @else
                                    <span class="px-4 py-2 text-sm font-medium rounded-lg bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-2"></i>Pending Verification
                                    </span>
                                @endif
                            </dd>
                        </div>

                        @if($registration->payment_approved_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Verified At</dt>
                            <dd class="text-base text-gray-900">{{ \Carbon\Carbon::parse($registration->payment_approved_at)->format('M d, Y H:i A') }}</dd>
                        </div>
                        @endif

                        @if($registration->payment_notes)
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <dt class="text-sm font-medium text-red-700 mb-1">Rejection Reason</dt>
                            <dd class="text-sm text-red-900">{{ $registration->payment_notes }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Payment Proof & Actions -->
            <div class="space-y-6">
                <!-- Payment Proof -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-receipt mr-2"></i>Payment Proof
                    </h2>
                    @if($registration->payment_receipt_path)
                        <div class="mb-4">
                            @php
                                $isPdf = str_contains(strtolower($registration->payment_receipt_path), '.pdf');
                            @endphp
                            
                            @if($isPdf)
                                <!-- PDF File - Show download option -->
                                <div class="border-2 border-orange-300 rounded-lg p-6 text-center bg-gradient-to-br from-orange-50 to-yellow-50">
                                    <div class="mb-4">
                                        <i class="fas fa-file-pdf text-orange-500" style="font-size: 60px;"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-2">PDF Payment Receipt Submitted</h3>
                                    <p class="text-gray-600 mb-4">Cannot preview PDF - Participant must resubmit as image</p>
                                    
                                    <a href="{{ $registration->payment_receipt_path }}" 
                                       download 
                                       class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-download mr-2"></i>Try Download PDF
                                    </a>
                                    
                                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-left">
                                        <p class="text-red-800">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <strong>Action Required:</strong> Reject this payment and ask participant to resubmit as JPG/PNG image only.
                                        </p>
                                    </div>
                                </div>
                            @else
                                <!-- Image File -->
                                <div class="text-center">
                                    <img src="{{ $registration->payment_receipt_path }}" 
                                         alt="Payment Proof" 
                                         class="w-full rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 mb-3"
                                         onclick="window.open(this.src, '_blank')"
                                         onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22><rect fill=%22%23f3f4f6%22 width=%22400%22 height=%22300%22/><text x=%2250%%22 y=%2250%%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2220%22 fill=%22%23ef4444%22>Failed to load image</text><text x=%2250%%22 y=%2260%%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2214%22 fill=%22%23666%22>Cloudinary authentication required</text></svg>'; this.parentElement.innerHTML += '<div class=\'mt-3 p-3 bg-red-50 border border-red-200 rounded text-sm\'><p class=\'text-red-800\'><i class=\'fas fa-exclamation-triangle mr-2\'></i><strong>Image failed to load.</strong> The file may require Cloudinary authentication.</p><a href=\'' + this.dataset.original + '\' target=\'_blank\' class=\'text-blue-600 hover:underline\'>Try opening in new tab</a></div>';"
                                         data-original="{{ $registration->payment_receipt_path }}">
                                    <p class="text-xs text-gray-500 mt-2">Click image to view full size</p>
                                    <p class="text-xs text-gray-400 mt-1 break-all">URL: {{ $registration->payment_receipt_path }}</p>
                                </div>
                            @endif
                        </div>
                        @if($registration->payment_submitted_at)
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-clock mr-2"></i>Submitted: {{ \Carbon\Carbon::parse($registration->payment_submitted_at)->format('M d, Y H:i A') }}
                        </p>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-image text-gray-300 text-5xl mb-3"></i>
                            <p class="text-gray-500">No payment proof uploaded yet</p>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                @if($registration->payment_receipt_path && $registration->payment_status !== 'approved')
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-tasks mr-2"></i>Actions
                    </h2>

                    <!-- Approve Button -->
                    <form action="{{ route('organizer.payment-verification.approve', $registration->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Approve this payment? This will mark the registration as PAID.')"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                            <i class="fas fa-check-circle mr-2"></i>Approve Payment
                        </button>
                    </form>

                    <!-- Reject Button & Form -->
                    <div>
                        <button type="button" 
                                onclick="document.getElementById('rejectForm').classList.toggle('hidden')"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                            <i class="fas fa-times-circle mr-2"></i>Reject Payment
                        </button>

                        <form id="rejectForm" 
                              action="{{ route('organizer.payment-verification.reject', $registration->id) }}" 
                              method="POST" 
                              class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            @csrf
                            <label class="block text-sm font-medium text-red-900 mb-2">
                                Reason for Rejection <span class="text-red-600">*</span>
                            </label>
                            <textarea name="rejection_reason" 
                                      rows="4" 
                                      required
                                      placeholder="Please explain why this payment is being rejected (minimum 10 characters)"
                                      class="w-full px-3 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                            <p class="text-xs text-red-700 mt-2 mb-3">
                                <i class="fas fa-info-circle mr-1"></i>The participant will see this reason and can resubmit payment proof.
                            </p>
                            <div class="flex space-x-3">
                                <button type="button" 
                                        onclick="document.getElementById('rejectForm').classList.add('hidden')"
                                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to reject this payment?')"
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg">
                                    Submit Rejection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @elseif($registration->payment_status === 'approved')
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <i class="fas fa-check-circle text-green-600 text-5xl mb-3"></i>
                    <h3 class="text-lg font-semibold text-green-900 mb-2">Payment Approved</h3>
                    <p class="text-green-700">This payment has been verified and approved.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
