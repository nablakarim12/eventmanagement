@extends('public.layouts.app')

@section('title', $event->title . ' - Event Details')

@section('meta')
    <meta name="description" content="{{ Str::limit(strip_tags($event->description), 160) }}">
    <meta property="og:title" content="{{ $event->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($event->description), 160) }}">
    <meta property="og:type" content="event">
    @if($event->image)
        <meta property="og:image" content="{{ asset('storage/' . $event->image) }}">
    @endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('events.index') }}" class="hover:text-blue-600">Events</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            @if($event->category)
                <li><a href="{{ route('events.category', $event->category->slug ?? $event->category->id) }}" class="hover:text-blue-600">{{ $event->category->name }}</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
            @endif
            <li class="text-gray-900 font-medium">{{ Str::limit($event->title, 50) }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Event Header -->
            <div class="mb-8">
                <!-- Event Image -->
                @if($event->image)
                    <div class="mb-6 rounded-lg overflow-hidden shadow-lg">
                        <img src="{{ asset('storage/' . $event->image) }}" 
                             alt="{{ $event->title }}" 
                             class="w-full h-64 sm:h-80 object-cover">
                    </div>
                @endif

                <!-- Title and Category -->
                <div class="mb-4">
                    @if($event->category)
                        <a href="{{ route('events.category', $event->category->slug ?? $event->category->id) }}" 
                           class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium hover:bg-blue-200 transition-colors mb-3">
                            {{ $event->category->name }}
                        </a>
                    @endif
                    
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>
                    
                    <!-- Event Status -->
                    @if($event->start_date->isPast())
                        <div class="inline-flex items-center bg-gray-100 text-gray-800 px-4 py-2 rounded-full text-sm font-medium">
                            <i class="fas fa-history mr-2"></i>
                            Event Ended
                        </div>
                    @elseif($event->start_date->isToday())
                        <div class="inline-flex items-center bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-medium">
                            <i class="fas fa-clock mr-2"></i>
                            Happening Today
                        </div>
                    @elseif($event->start_date->isTomorrow())
                        <div class="inline-flex items-center bg-orange-100 text-orange-800 px-4 py-2 rounded-full text-sm font-medium">
                            <i class="fas fa-clock mr-2"></i>
                            Tomorrow
                        </div>
                    @else
                        <div class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-medium">
                            <i class="fas fa-calendar mr-2"></i>
                            Upcoming Event
                        </div>
                    @endif
                </div>

                <!-- Quick Info Bar -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Date -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 mb-1">
                                {{ $event->start_date->format('d') }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $event->start_date->format('M Y') }}
                            </div>
                        </div>
                        
                        <!-- Time -->
                        <div class="text-center">
                            <div class="text-lg font-semibold text-gray-900 mb-1">
                                {{ $event->start_date->format('g:i A') }}
                            </div>
                            <div class="text-sm text-gray-600">
                                Start Time
                            </div>
                        </div>
                        
                        <!-- Registration Deadline -->
                        <div class="text-center">
                            @if($event->registration_deadline)
                                @php
                                    $deadline = \Carbon\Carbon::parse($event->registration_deadline);
                                    $isDeadlinePassed = $deadline->isPast();
                                    $daysUntilDeadline = now()->diffInDays($deadline, false);
                                @endphp
                                <div class="text-lg font-semibold mb-1 {{ $isDeadlinePassed ? 'text-red-600' : ($daysUntilDeadline <= 3 ? 'text-orange-600' : 'text-green-600') }}">
                                    @if($isDeadlinePassed)
                                        <i class="fas fa-times-circle"></i> Closed
                                    @elseif($daysUntilDeadline <= 0)
                                        <i class="fas fa-clock"></i> Today
                                    @elseif($daysUntilDeadline == 1)
                                        <i class="fas fa-clock"></i> Tomorrow
                                    @else
                                        <i class="fas fa-calendar"></i> {{ $daysUntilDeadline }} days
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600">
                                    Registration {{ $isDeadlinePassed ? 'Closed' : 'Deadline' }}
                                </div>
                                @if(!$isDeadlinePassed)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $deadline->format('M j, g:i A') }}
                                    </div>
                                @endif
                            @else
                                <div class="text-lg font-semibold text-gray-600 mb-1">
                                    <i class="fas fa-infinity"></i> Open
                                </div>
                                <div class="text-sm text-gray-600">
                                    No Deadline
                                </div>
                            @endif
                        </div>
                        
                        <!-- Price -->
                        <div class="text-center">
                            <div class="text-lg font-semibold text-gray-900 mb-1">
                                @if($event->is_free || $event->registration_fee == 0)
                                    Free
                                @else
                                    ${{ number_format($event->registration_fee, 0) }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-600">
                                Registration
                            </div>
                        </div>
                        
                        <!-- Availability -->
                        <div class="text-center">
                            @if($event->max_participants)
                                @php
                                    $registeredCount = $event->current_participants ?? 0;
                                    $spotsLeft = $event->max_participants - $registeredCount;
                                @endphp
                                <div class="text-lg font-semibold mb-1 {{ $spotsLeft <= 0 ? 'text-red-600' : ($spotsLeft <= 10 ? 'text-orange-600' : 'text-green-600') }}">
                                    {{ $spotsLeft > 0 ? $spotsLeft : 0 }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    Spots Left
                                </div>
                            @else
                                <div class="text-lg font-semibold text-green-600 mb-1">
                                    Unlimited
                                </div>
                                <div class="text-sm text-gray-600">
                                    Capacity
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Description -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">About This Event</h2>
                <div class="prose prose-lg max-w-none">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>

            <!-- Event Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Event Details</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-600 mb-1">Start Date & Time</dt>
                        <dd class="text-lg text-gray-900">
                            {{ $event->start_date->format('l, F d, Y \a\t g:i A') }}
                        </dd>
                    </div>
                    
                    @if($event->end_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-600 mb-1">End Date & Time</dt>
                        <dd class="text-lg text-gray-900">
                            {{ $event->end_date->format('l, F d, Y \a\t g:i A') }}
                        </dd>
                    </div>
                    @endif
                    
                    @if($event->venue_name || $event->venue_address)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-600 mb-1">Location</dt>
                        <dd class="text-lg text-gray-900 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                            {{ $event->venue_name }}@if($event->venue_address), {{ $event->venue_address }}@endif@if($event->city), {{ $event->city }}@endif
                            <a href="https://maps.google.com/?q={{ urlencode(($event->venue_name ? $event->venue_name . ' ' : '') . ($event->venue_address ? $event->venue_address . ' ' : '') . ($event->city ? $event->city : '')) }}" 
                               target="_blank" 
                               class="ml-3 text-blue-600 hover:text-blue-700 text-sm">
                                <i class="fas fa-external-link-alt mr-1"></i>View on Map
                            </a>
                        </dd>
                    </div>
                    @endif
                    
                    @if($event->organizer)
                    <div>
                        <dt class="text-sm font-medium text-gray-600 mb-1">Organizer</dt>
                        <dd class="text-lg text-gray-900">{{ $event->organizer->name }}</dd>
                    </div>
                    @endif
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-600 mb-1">Registration Fee</dt>
                        <dd class="text-lg text-gray-900 font-semibold">
                            @if($event->is_free || $event->registration_fee == 0)
                                <span class="text-green-600">Free</span>
                            @else
                                ${{ number_format($event->registration_fee, 2) }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Registration Card -->
            <div id="register" class="bg-white rounded-lg shadow-lg border border-gray-200 p-6 mb-6 sticky top-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Register for Event</h3>
                
                @if($event->start_date->isPast())
                    <!-- Event Ended -->
                    <div class="text-center py-6">
                        <i class="fas fa-calendar-times text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600 mb-4">This event has already ended.</p>
                        <a href="{{ route('events.index') }}" 
                           class="inline-flex items-center bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-calendar mr-2"></i>
                            Browse Other Events
                        </a>
                    </div>
                @elseif($event->registration_deadline && \Carbon\Carbon::parse($event->registration_deadline)->isPast())
                    <!-- Registration Deadline Passed -->
                    <div class="text-center py-6">
                        <i class="fas fa-clock text-4xl text-red-400 mb-3"></i>
                        <p class="text-gray-600 mb-2">Registration deadline has passed.</p>
                        <p class="text-sm text-gray-500 mb-4">
                            Deadline was {{ \Carbon\Carbon::parse($event->registration_deadline)->format('M j, Y g:i A') }}
                        </p>
                        <button class="w-full bg-gray-100 text-gray-600 py-3 px-6 rounded-lg cursor-not-allowed" disabled>
                            <i class="fas fa-ban mr-2"></i>
                            Registration Closed
                        </button>
                        <p class="text-xs text-gray-500 mt-2">Contact organizer for late registration inquiries</p>
                    </div>
                @elseif($event->max_participants && ($event->current_participants ?? 0) >= $event->max_participants)
                    <!-- Event Full -->
                    <div class="text-center py-6">
                        <i class="fas fa-users text-4xl text-red-400 mb-3"></i>
                        <p class="text-gray-600 mb-4">This event is currently full.</p>
                        <button class="w-full bg-gray-100 text-gray-600 py-3 px-6 rounded-lg cursor-not-allowed" disabled>
                            <i class="fas fa-users mr-2"></i>
                            Event Full
                        </button>
                        <p class="text-sm text-gray-500 mt-2">Check back later for cancellations</p>
                    </div>
                @else
                    <!-- Available for Registration -->
                    <div class="space-y-4">
                        <!-- Deadline Warning -->
                        @if($event->registration_deadline)
                            @php
                                $deadline = \Carbon\Carbon::parse($event->registration_deadline);
                                $daysUntilDeadline = now()->diffInDays($deadline, false);
                                $hoursUntilDeadline = now()->diffInHours($deadline, false);
                            @endphp
                            @if($daysUntilDeadline <= 3 && !$deadline->isPast())
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>
                                        <div>
                                            <p class="text-sm font-medium text-orange-800">
                                                @if($daysUntilDeadline <= 0 && $hoursUntilDeadline <= 24)
                                                    Registration closes in {{ $hoursUntilDeadline }} hours!
                                                @elseif($daysUntilDeadline == 1)
                                                    Registration closes tomorrow at {{ $deadline->format('g:i A') }}!
                                                @else
                                                    Registration closes in {{ $daysUntilDeadline }} days!
                                                @endif
                                            </p>
                                            <p class="text-xs text-orange-600 mt-1">
                                                Deadline: {{ $deadline->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-blue-700">Registration Fee:</span>
                                <span class="text-xl font-bold text-blue-700">
                                    @if($event->registration_fee > 0)
                                        ${{ number_format($event->registration_fee, 2) }}
                                    @else
                                        Free
                                    @endif
                                </span>
                            </div>
                            
                            @if($event->max_participants)
                                @php
                                    $registeredCount = $event->current_participants ?? 0;
                                    $spotsLeft = $event->max_participants - $registeredCount;
                                @endphp
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-blue-600">Spots Available:</span>
                                    <span class="font-medium {{ $spotsLeft <= 10 ? 'text-orange-600' : 'text-green-600' }}">
                                        {{ $spotsLeft }} / {{ $event->max_participants }}
                                    </span>
                                </div>
                                
                                @if($spotsLeft <= 10)
                                    <div class="mt-2 text-xs text-orange-600 font-medium">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Limited spots remaining!
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        @guest
                            <a href="{{ route('login') }}" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-lg text-center block">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Login to Register
                            </a>
                        @else
                            <a href="{{ route('events.register', $event) }}" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-lg text-center block">
                                <i class="fas fa-user-plus mr-2"></i>
                                @if($event->is_free || $event->registration_fee == 0)
                                    Register for Free
                                @else
                                    Register Now - ${{ number_format($event->registration_fee, 0) }}
                                @endif
                            </a>
                        @endguest
                        
                        <div class="text-center">
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Secure registration powered by EventSphere
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Share Event -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Share This Event</h3>
                <div class="flex justify-center space-x-3">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                       target="_blank"
                       class="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($event->title) }}" 
                       target="_blank"
                       class="bg-blue-400 text-white p-3 rounded-lg hover:bg-blue-500 transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" 
                       target="_blank"
                       class="bg-blue-800 text-white p-3 rounded-lg hover:bg-blue-900 transition-colors">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <button onclick="copyToClipboard()" 
                            class="bg-gray-600 text-white p-3 rounded-lg hover:bg-gray-700 transition-colors"
                            title="Copy link">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>

            <!-- Contact Organizer -->
            @if($event->organizer && $event->organizer->email)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Questions?</h3>
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-user text-gray-600"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $event->organizer->name }}</div>
                        <div class="text-sm text-gray-600">Event Organizer</div>
                    </div>
                </div>
                <a href="mailto:{{ $event->organizer->email }}?subject=Question about {{ $event->title }}" 
                   class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors text-center block">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact Organizer
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.add('bg-green-600');
        button.classList.remove('bg-gray-600');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('bg-green-600');
            button.classList.add('bg-gray-600');
        }, 2000);
    });
}
</script>
@endsection