@extends('organizer.layouts.app')

@section('title', ($eventType ? ucfirst($eventType) . ' ' : '') . 'Registrations')
@section('page-title', ($eventType ? ucfirst($eventType) . ' ' : '') . 'Registrations')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <p class="text-xs font-medium text-gray-500 mb-1">Total</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <p class="text-xs font-medium text-gray-500 mb-1">Confirmed</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['confirmed'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center mb-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <p class="text-xs font-medium text-gray-500 mb-1">Pending</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-3">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <p class="text-xs font-medium text-gray-500 mb-1">Cancelled</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['cancelled'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mb-3">
                    <i class="fas fa-user text-indigo-600 text-xl"></i>
                </div>
                <p class="text-xs font-medium text-gray-500 mb-1">Participants</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['participants'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mb-3">
                    <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                </div>
                <p class="text-xs font-medium text-gray-500 mb-1">Jury</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['reviewers'] }}</p>
            </div>
        </div>
    </div>

    <!-- Events with Registrations -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                Events with Registrations
            </h2>
            <p class="text-sm text-gray-600 mt-1">Click on an event to view and manage registrations</p>
        </div>

        @if($eventsWithRegistrations->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($eventsWithRegistrations as $event)
                    <a href="{{ route('organizer.registrations.event', $event->id) }}" 
                       class="flex items-center p-5 hover:bg-gray-50 transition-all duration-150 group">
                        <!-- Event Image/Icon -->
                        <div class="flex-shrink-0">
                            @if($event->featured_image)
                                <img src="{{ str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . $event->featured_image) }}" 
                                     alt="{{ $event->title }}"
                                     class="w-20 h-20 rounded-lg object-cover shadow-sm">
                            @else
                                <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-calendar-alt text-white text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Event Details -->
                        <div class="flex-1 ml-5">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                                {{ $event->title }}
                            </h3>
                            <div class="flex items-center mt-2 space-x-4">
                                <span class="inline-flex items-center text-sm text-gray-600">
                                    <i class="far fa-calendar text-gray-400 mr-1.5"></i>
                                    {{ \Carbon\Carbon::parse($event->f2f_start_date ?? $event->start_date)->format('M d, Y') }}
                                </span>
                                @if($event->delivery_mode)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                        {{ ucfirst(str_replace('_', ' ', $event->delivery_mode)) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="flex items-center space-x-4 ml-6">
                            <!-- Total -->
                            <div class="text-center px-4 border-r border-gray-200">
                                <p class="text-2xl font-bold text-gray-900">{{ $event->total_registrations }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Total</p>
                            </div>

                            <!-- Confirmed Badge -->
                            @if($event->confirmed_registrations > 0)
                                <div class="flex flex-col items-center">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-green-50 text-green-700 border border-green-200">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        {{ $event->confirmed_registrations }} Confirmed
                                    </span>
                                </div>
                            @endif

                            <!-- Participants Count -->
                            @if($event->participants_count > 0)
                                <div class="flex items-center px-3 py-1.5 bg-indigo-50 rounded-lg border border-indigo-200">
                                    <i class="fas fa-user text-indigo-600 mr-2"></i>
                                    <span class="text-sm font-semibold text-indigo-700">{{ $event->participants_count }} Participants</span>
                                </div>
                            @endif

                            <!-- Arrow Icon -->
                            <div class="pl-4">
                                <i class="fas fa-chevron-right text-gray-400 text-lg group-hover:text-blue-600 group-hover:translate-x-1 transition-all"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">No registrations yet</p>
                <p class="text-gray-400 text-sm mt-2">When users register for your events, they will appear here</p>
            </div>
        @endif
    </div>
</div>
@endsection
