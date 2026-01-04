@extends('layouts.app')

@section('title', 'My Presentation QR Code')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Presentation QR Code</h1>
                <p class="text-gray-600 mt-1">{{ $registration->event->title }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                    Approved Presenter
                </span>
            </div>
        </div>
    </div>

    <!-- Attendance Status -->
    @if($attendance)
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
            <div>
                <h3 class="font-semibold text-green-900">Attendance Recorded</h3>
                <p class="text-green-700 text-sm">Checked in on {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        <!-- QR Code Display -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-qrcode text-purple-600 mr-2"></i>
                Your Presentation QR Code
            </h2>
            
            @if($qrCode && $qrCode->qr_image_url)
                <div class="bg-gray-50 p-6 rounded-lg flex items-center justify-center mb-4">
                    <img src="{{ $qrCode->qr_image_url }}" 
                         alt="Presentation QR Code" 
                         class="w-64 h-64 object-contain">
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('participant.presentation-qr.download', $registration->event_id) }}" 
                       class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Download QR Code
                    </a>
                    
                    <button onclick="printQR()" 
                            class="block w-full bg-gray-600 text-white text-center py-3 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>Print QR Code
                    </button>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                    <p>QR code not yet generated</p>
                    <p class="text-sm mt-2">Please contact the event organizer</p>
                </div>
            @endif
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Instructions
            </h2>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold mr-3">1</div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Download or Print</h3>
                        <p class="text-gray-600 text-sm">Save the QR code on your phone or print it out</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold mr-3">2</div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Arrive on Event Day</h3>
                        <p class="text-gray-600 text-sm">Bring your QR code to the event venue</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold mr-3">3</div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Check-In</h3>
                        <p class="text-gray-600 text-sm">Show your QR code at the registration desk for scanning</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold mr-3">4</div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Get Certificate</h3>
                        <p class="text-gray-600 text-sm">After checking in, you'll be eligible for a presentation certificate</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Presentation Details -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Presentation Details</h2>
        
        <div class="grid md:grid-cols-2 gap-4">
            @if($registration->presentation_queue)
            <div>
                <span class="text-sm text-gray-600">Queue Number:</span>
                <p class="font-semibold text-gray-900">{{ $registration->presentation_queue }}</p>
            </div>
            @endif
            
            @if($registration->presentation_time)
            <div>
                <span class="text-sm text-gray-600">Presentation Time:</span>
                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($registration->presentation_time)->format('M d, Y h:i A') }}</p>
            </div>
            @endif
            
            @if($registration->presentation_location)
            <div>
                <span class="text-sm text-gray-600">Location:</span>
                <p class="font-semibold text-gray-900">{{ $registration->presentation_location }}</p>
            </div>
            @endif
            
            @if($registration->average_score)
            <div>
                <span class="text-sm text-gray-600">Average Score:</span>
                <p class="font-semibold text-gray-900">{{ number_format($registration->average_score, 2) }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function printQR() {
    window.print();
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .bg-gray-50, .bg-gray-50 * {
        visibility: visible;
    }
    .bg-gray-50 {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
}
</style>
@endsection
