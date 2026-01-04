@extends('organizer.layouts.app')

@section('title', ($eventType ?? null) ? ucfirst($eventType) . ' Certificate Management' : 'Certificate Management')
@section('page-title', ($eventType ?? null) ? ucfirst($eventType) . ' Certificate Management' : 'Certificate Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ $eventType ? ucfirst($eventType) . ' ' : '' }}Certificate Management</h1>
        <p class="text-gray-600 mt-2">Manage certificates for your events</p>
    </div>

    @if($events->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="text-6xl mb-4">📋</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Events with Attendance Yet</h3>
            <p class="text-gray-600">Events will appear here once attendees check in.</p>
        </div>
    @else
        <!-- Events List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-certificate mr-2 text-blue-600"></i>
                    Events with Certificates
                </h2>
                <p class="text-sm text-gray-600 mt-1">Click on an event to manage certificates for attendees</p>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($events as $event)
                    <a href="{{ route('organizer.simple-certificates.attendees', $event) }}" 
                       class="flex items-center p-5 hover:bg-gray-50 transition-all duration-150 group">
                        <!-- Event Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center shadow-sm">
                                <i class="fas fa-certificate text-white text-2xl"></i>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div class="flex-1 ml-5">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                {{ $event->title }}
                            </h3>
                            <div class="flex items-center mt-2">
                                <span class="inline-flex items-center text-sm text-gray-600">
                                    <i class="far fa-calendar text-gray-400 mr-1.5"></i>
                                    {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="flex items-center space-x-3 ml-6">
                            <!-- Total Registrations -->
                            <div class="text-center px-4 border-r border-gray-200">
                                <p class="text-2xl font-bold text-gray-900">{{ $event->registrations_count }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Total</p>
                            </div>

                            <!-- Checked In -->
                            <div class="flex items-center px-3 py-1.5 bg-blue-50 rounded-lg border border-blue-200">
                                <i class="fas fa-check text-blue-600 mr-2"></i>
                                <span class="text-sm font-semibold text-blue-700">{{ $event->checked_in_count }} Checked In</span>
                            </div>

                            <!-- Certificates Generated -->
                            <div class="flex items-center px-3 py-1.5 bg-green-50 rounded-lg border border-green-200">
                                <i class="fas fa-certificate text-green-600 mr-2"></i>
                                <span class="text-sm font-semibold text-green-700">{{ $event->generated_certificates_count }} Generated</span>
                            </div>

                            <!-- Arrow Icon -->
                            <div class="pl-4">
                                <i class="fas fa-chevron-right text-gray-400 text-lg group-hover:text-blue-600 group-hover:translate-x-1 transition-all"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
