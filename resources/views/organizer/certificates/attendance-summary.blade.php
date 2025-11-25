@extends('organizer.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Attendance Summary</h1>
                <p class="mt-2 text-gray-600">Review attendance records for certificate generation</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('organizer.certificates.eligible-attendees', $event) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                    <i class="fas fa-certificate mr-2"></i>
                    Generate Certificates
                </a>
            </div>
        </div>
    </div>

    <!-- Event Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">{{ $event->title }}</h2>
            <div class="mt-2 text-sm text-gray-600">
                <p><i class="fas fa-calendar mr-2"></i>{{ $event->start_date->format('F d, Y') }}</p>
                <p><i class="fas fa-map-marker-alt mr-2"></i>{{ $event->venue_name }}</p>
            </div>
        </div>
    </div>

    <!-- Attendance Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Registered -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Registered</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $attendanceStats['total_registered'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total Checked In -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-qrcode text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Checked In</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $attendanceStats['total_checked_in'] }}</p>
                </div>
            </div>
        </div>

        <!-- Completed Attendance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed Attendance</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $attendanceStats['total_completed'] }}</p>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Attendance Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $attendanceStats['attendance_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-based Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Participants -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user-graduate mr-2 text-blue-500"></i>
                    Participants
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Completed Attendance:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $attendanceStats['participants_completed'] }}</span>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Participants who completed full attendance (check-in + check-out) are eligible for participation certificates.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jury -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-gavel mr-2 text-purple-500"></i>
                    Jury Members
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Completed Attendance:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $attendanceStats['jury_completed'] }}</span>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <p class="text-sm text-purple-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Jury members who completed full attendance are eligible for appreciation certificates.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Generation Info -->
    <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-certificate mr-2 text-green-600"></i>
            Certificate Generation Process
        </h3>
        <div class="space-y-3 text-sm text-gray-700">
            <p><span class="font-semibold">Step 1:</span> QR attendance tracking records who checked in and out</p>
            <p><span class="font-semibold">Step 2:</span> Only attendees with <strong>completed attendance</strong> (both check-in and check-out) are eligible</p>
            <p><span class="font-semibold">Step 3:</span> Generate certificates based on role:</p>
            <ul class="ml-8 space-y-1">
                <li>• <strong>Participants:</strong> Participation Certificate</li>
                <li>• <strong>Jury:</strong> Appreciation Certificate</li>
            </ul>
        </div>
        
        <div class="mt-4 flex space-x-3">
            <a href="{{ route('organizer.certificates.eligible-attendees', $event) }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center text-sm">
                <i class="fas fa-arrow-right mr-2"></i>
                Proceed to Certificate Generation
            </a>
            <a href="{{ route('organizer.events.qr-codes.index', $event) }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center text-sm">
                <i class="fas fa-qrcode mr-2"></i>
                View QR Codes
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any interactive features here
    console.log('Attendance Summary loaded');
});
</script>
@endsection