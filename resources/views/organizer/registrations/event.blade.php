@extends('organizer.layouts.app')

@section('title', ucfirst($eventType) . ' Event Registrations - ' . $event->title)
@section('page-title', ucfirst($eventType) . ' Event Registrations')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('organizer.registrations.index') }}" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-list mr-2"></i>All Registrations
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">{{ Str::limit($event->title, 50) }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center">
            @if($event->featured_image)
                <img src="{{ str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . $event->featured_image) }}" 
                     alt="{{ $event->title }}"
                     class="w-24 h-24 rounded-lg object-cover">
            @else
                <div class="w-24 h-24 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white text-3xl"></i>
                </div>
            @endif
            <div class="ml-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                <p class="text-gray-600 mt-1">{{ $event->registration_code }}</p>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="text-sm text-gray-500">
                        <i class="far fa-calendar mr-1"></i>
                        {{ \Carbon\Carbon::parse($event->f2f_start_date ?? $event->start_date)->format('M d, Y') }}
                    </span>
                    @if($event->delivery_mode)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            {{ ucfirst(str_replace('_', ' ', $event->delivery_mode)) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                <p class="text-sm text-gray-600 mt-1">Total</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-center">
                <p class="text-3xl font-bold text-green-600">{{ $stats['confirmed'] }}</p>
                <p class="text-sm text-gray-600 mt-1">Confirmed</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-center">
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                <p class="text-sm text-gray-600 mt-1">Pending</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-center">
                <p class="text-3xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
                <p class="text-sm text-gray-600 mt-1">Cancelled</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-center">
                <p class="text-3xl font-bold text-indigo-600">{{ $stats['participants'] }}</p>
                <p class="text-sm text-gray-600 mt-1">Participants</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-center">
                <p class="text-3xl font-bold text-purple-600">{{ $stats['reviewers'] }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ $secondaryRoleLabel }}</p>
            </div>
        </div>
    </div>

    <!-- Participants Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b border-gray-200 bg-indigo-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-user mr-2 text-indigo-600"></i>
                        Participants ({{ $stats['participants'] }})
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $stats['participants_pending'] }} pending · {{ $stats['participants_confirmed'] }} confirmed
                    </p>
                </div>
            </div>
        </div>

        @if($participants->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($participants as $registration)
                    <a href="{{ route('organizer.registrations.show', $registration->id) }}" 
                       class="block p-6 hover:bg-gray-50 transition duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold text-lg">
                                            {{ strtoupper(substr($registration->user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $registration->user->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $registration->user->email }}</p>
                                    @if($registration->selected_category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mt-1">
                                            <i class="fas fa-tag mr-1"></i>{{ $registration->selected_category }}
                                        </span>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        Registered: {{ $registration->created_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <!-- Status Badge -->
                                @if($registration->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @elseif($registration->status === 'confirmed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i> Confirmed
                                    </span>
                                @elseif($registration->status === 'cancelled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i> Cancelled
                                    </span>
                                @endif

                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-user-slash text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">No participants registered yet</p>
            </div>
        @endif
    </div>

    <!-- Reviewers/Jury Section -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 bg-purple-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas {{ $eventType === 'innovation' ? 'fa-gavel' : 'fa-user-graduate' }} mr-2 text-purple-600"></i>
                        {{ $secondaryRoleLabel }} ({{ $stats['reviewers'] }})
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $stats['reviewers_pending'] }} pending · {{ $stats['reviewers_confirmed'] }} confirmed
                    </p>
                </div>
            </div>
        </div>

        @if($reviewers->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($reviewers as $registration)
                    <a href="{{ route('organizer.registrations.show', $registration->id) }}" 
                       class="block p-6 hover:bg-gray-50 transition duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                        <span class="text-purple-600 font-semibold text-lg">
                                            {{ strtoupper(substr($registration->user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $registration->user->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $registration->user->email }}</p>
                                    @if($registration->selected_category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mt-1">
                                            <i class="fas fa-tag mr-1"></i>{{ $registration->selected_category }}
                                        </span>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        Registered: {{ $registration->created_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <!-- Status Badge -->
                                @if($registration->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @elseif($registration->status === 'confirmed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i> Confirmed
                                    </span>
                                @elseif($registration->status === 'cancelled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i> Cancelled
                                    </span>
                                @endif

                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas {{ $eventType === 'innovation' ? 'fa-gavel' : 'fa-user-graduate' }} text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">No {{ strtolower($secondaryRoleLabel) }} registered yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
