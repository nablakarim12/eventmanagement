@extends('organizer.layouts.app')

@section('title', 'Event Details')
@section('page-title', 'Event Details')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            @if($event->featured_image)
            <div class="h-64 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $event->featured_image) }}')"></div>
            @else
            <div class="h-64 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                <div class="text-center text-white">
                    <i class="fas fa-calendar-alt text-6xl mb-4"></i>
                    <h2 class="text-2xl font-bold">{{ $event->title }}</h2>
                </div>
            </div>
            @endif
            
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                        <p class="text-gray-600 mt-1">{{ $event->category->name ?? 'No Category' }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($event->status === 'published') bg-green-100 text-green-800
                            @elseif($event->status === 'draft') bg-yellow-100 text-yellow-800
                            @elseif($event->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Date & Time -->
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-calendar mr-3 text-blue-500"></i>
                        <div>
                            <p class="font-semibold">Date & Time</p>
                            <p>{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</p>
                            <p class="text-sm">{{ \Carbon\Carbon::parse($event->start_date)->format('h:i A') }} - 
                               {{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('h:i A') : 'TBD' }}</p>
                        </div>
                    </div>
                    
                    <!-- Registration Deadline -->
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-clock mr-3 text-orange-500"></i>
                        <div>
                            <p class="font-semibold">Registration Deadline</p>
                            @if($event->registration_deadline)
                                <p>{{ \Carbon\Carbon::parse($event->registration_deadline)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->registration_deadline)->format('h:i A') }}</p>
                            @else
                                <p class="text-sm text-gray-500">Not set</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Location -->
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-map-marker-alt mr-3 text-red-500"></i>
                        <div>
                            <p class="font-semibold">Location</p>
                            <p>{{ $event->venue_name }}</p>
                            @if($event->venue_address)
                                <p class="text-sm text-gray-500">{{ $event->venue_address }}</p>
                            @endif
                            @if($event->city || $event->country)
                                <p class="text-sm text-gray-500">{{ $event->city }}@if($event->city && $event->country), @endif{{ $event->country }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Price -->
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-tag mr-3 text-green-500"></i>
                        <div>
                            <p class="font-semibold">Price</p>
                            <p class="text-lg font-bold">{{ $event->price ? '$' . number_format($event->price, 2) : 'Free' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>Event Description
                    </h3>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $event->description }}</p>
                    </div>
                </div>

                <!-- Registration Statistics -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-users mr-2 text-green-500"></i>Registration Statistics
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">{{ $event->registrations->count() }}</p>
                            <p class="text-sm text-gray-500">Total Registered</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-green-600">{{ $event->registrations->where('status', 'confirmed')->count() }}</p>
                            <p class="text-sm text-gray-500">Confirmed</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-yellow-600">{{ $event->registrations->where('status', 'pending')->count() }}</p>
                            <p class="text-sm text-gray-500">Pending</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-gray-600">{{ $event->max_participants ? max(0, $event->max_participants - $event->registrations->count()) : '∞' }}</p>
                            <p class="text-sm text-gray-500">Remaining</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-bolt mr-2 text-purple-500"></i>Quick Actions
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('organizer.registrations.index', ['event' => $event->id]) }}" 
                           class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors flex items-center">
                            <i class="fas fa-users mr-2"></i>View Registrations
                        </a>
                        <a href="{{ route('organizer.attendance.event', $event) }}" 
                           class="bg-green-100 text-green-700 px-4 py-2 rounded-lg hover:bg-green-200 transition-colors flex items-center">
                            <i class="fas fa-clipboard-check mr-2"></i>Attendance
                        </a>
                        
                        <a href="{{ route('organizer.events.jury-assignments.index', $event) }}" 
                           class="bg-purple-100 text-purple-700 px-4 py-2 rounded-lg hover:bg-purple-200 transition-colors flex items-center">
                            <i class="fas fa-user-tie mr-2"></i>Jury Assignments
                        </a>
                        
                        @if($event->registrations->count() > 0)
                        <a href="{{ route('organizer.certificates.event', $event) }}" 
                           class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg hover:bg-yellow-200 transition-colors flex items-center">
                            <i class="fas fa-certificate mr-2"></i>Certificates
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Event Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Information</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Maximum Participants</p>
                            <p class="text-lg">{{ $event->max_participants ?? 'Unlimited' }}</p>
                        </div>
                        
                        @if($event->registration_start)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Registration Opens</p>
                            <p class="text-lg">{{ \Carbon\Carbon::parse($event->registration_start)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        
                        @if($event->registration_end)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Registration Closes</p>
                            <p class="text-lg">{{ \Carbon\Carbon::parse($event->registration_end)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Created</p>
                            <p class="text-lg">{{ $event->created_at->format('M d, Y') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Last Updated</p>
                            <p class="text-lg">{{ $event->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('organizer.events.edit', $event) }}" 
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>Edit Event
                        </a>
                        
                        <a href="{{ route('organizer.events.duplicate', $event) }}" 
                           class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-copy mr-2"></i>Duplicate Event
                        </a>
                        
                        <form action="{{ route('organizer.events.destroy', $event) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>Delete Event
                            </button>
                        </form>
                        
                        <a href="{{ route('organizer.events.index') }}" 
                           class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection