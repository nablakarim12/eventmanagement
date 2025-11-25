@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="text-gray-600 mt-2">Here's what's happening with your event registrations.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Registrations -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Registrations</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalRegistrations }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Events</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $upcomingEvents }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Past Events -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-history text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Past Events</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $pastEvents }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Upcoming Events Section -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Your Upcoming Events</h2>
            </div>
            <div class="p-6">
                @if($upcomingRegisteredEvents->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingRegisteredEvents as $registration)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 mb-1">
                                            <a href="{{ route('events.show', $registration->event->slug) }}" class="hover:text-blue-600">
                                                {{ $registration->event->title }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">{{ $registration->event->short_description }}</p>
                                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $registration->event->start_date->format('M d, Y') }}
                                            </span>
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $registration->event->start_date->format('g:i A') }}
                                            </span>
                                            @if($registration->event->category)
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                                    {{ $registration->event->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $registration->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                               ($registration->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="text-sm text-gray-500">
                                        Registration Code: <span class="font-mono">{{ $registration->registration_code }}</span>
                                    </div>
                                    <a href="{{ route('dashboard.registrations.show', $registration) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('dashboard.registrations') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                            View All Registrations →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500 mb-4">No upcoming events registered.</p>
                        <a href="{{ route('events.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Browse Events
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
            </div>
            <div class="p-6">
                @if($recentRegistrations->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentRegistrations as $registration)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        Registered for {{ $registration->event->title }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $registration->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $registration->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                           ($registration->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($registration->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-history text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500">No recent activity.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('events.index') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                <i class="fas fa-search text-blue-600 mr-3"></i>
                <span class="font-medium text-gray-900">Browse Events</span>
            </a>
            <a href="{{ route('dashboard.registrations') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-colors">
                <i class="fas fa-list text-green-600 mr-3"></i>
                <span class="font-medium text-gray-900">My Registrations</span>
            </a>
            <a href="{{ route('dashboard.profile') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors">
                <i class="fas fa-user text-purple-600 mr-3"></i>
                <span class="font-medium text-gray-900">Edit Profile</span>
            </a>
        </div>
    </div>
</div>
@endsection