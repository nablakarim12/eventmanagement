@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">My Event Registrations</h1>

        @if($registrations->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No registrations yet</h3>
                <p class="text-gray-600 mb-6">You haven't registered for any events.</p>
                <a href="{{ route('events.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    Browse Events
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($registrations as $registration)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-xl font-semibold text-gray-900">
                                        {{ $registration->event->event_name }}
                                    </h3>
                                    
                                    <!-- Role Badge -->
                                    @if($registration->role === 'participant')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Participant
                                        </span>
                                    @elseif($registration->role === 'jury')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Jury
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Both
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 mb-3">
                                    <div>
                                        <span class="font-medium">Date:</span>
                                        {{ \Carbon\Carbon::parse($registration->event->event_date)->format('M j, Y') }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Code:</span>
                                        <span class="font-mono">{{ $registration->registration_code }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Registered:</span>
                                        {{ $registration->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <!-- Status Badges -->
                                <div class="flex gap-2 flex-wrap">
                                    <!-- Approval Status -->
                                    @if($registration->approved_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Approved
                                        </span>
                                    @elseif($registration->rejected_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ✗ Rejected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⏱ Pending
                                        </span>
                                    @endif

                                    <!-- Check-In Status -->
                                    @if($registration->checked_in_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            ✓ Checked In
                                        </span>
                                    @endif

                                    <!-- QR Code Ready -->
                                    @if($registration->qr_code)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            📱 QR Ready
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col gap-2 ml-4">
                                <a href="{{ route('dashboard.registrations.show', $registration) }}" 
                                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-200 text-center whitespace-nowrap">
                                    View Details
                                </a>
                                
                                @if($registration->qr_code)
                                    <a href="{{ route('dashboard.registrations.show', $registration) }}#qr-code" 
                                       class="inline-block bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-200 text-center whitespace-nowrap">
                                        View QR
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
