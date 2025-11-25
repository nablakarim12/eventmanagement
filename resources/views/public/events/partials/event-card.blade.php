<div class="event-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
    <!-- Event Image -->
    <div class="relative h-48 bg-gradient-to-r from-blue-500 to-indigo-600 overflow-hidden">
        @if($event->image)
            <img src="{{ asset('storage/' . $event->image) }}" 
                 alt="{{ $event->title }}" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <div class="text-center text-white">
                    <i class="fas fa-calendar-alt text-4xl mb-2 opacity-70"></i>
                    <p class="text-sm opacity-70">{{ $event->category->name ?? 'Event' }}</p>
                </div>
            </div>
        @endif
        
        <!-- Date Badge -->
        <div class="absolute top-4 left-4 bg-white bg-opacity-95 backdrop-blur-sm rounded-lg px-3 py-2 text-center shadow-lg">
            <div class="text-xs font-medium text-gray-600 uppercase">
                {{ $event->start_date->format('M') }}
            </div>
            <div class="text-lg font-bold text-gray-900">
                {{ $event->start_date->format('d') }}
            </div>
        </div>
        
        <!-- Price Badge -->
        @if($event->is_free || $event->registration_fee == 0)
            <div class="absolute top-4 right-4 bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium shadow-lg">
                Free
            </div>
        @else
            <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium shadow-lg">
                ${{ number_format($event->registration_fee, 0) }}
            </div>
        @endif
        
        <!-- Status Badge -->
        @if($event->start_date->isPast())
            <div class="absolute bottom-4 right-4 bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                Past Event
            </div>
        @elseif($event->start_date->isToday())
            <div class="absolute bottom-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-medium animate-pulse">
                Today
            </div>
        @elseif($event->start_date->isTomorrow())
            <div class="absolute bottom-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                Tomorrow
            </div>
        @endif
    </div>
    
    <!-- Event Content -->
    <div class="p-6 flex-1 flex flex-col">
        <!-- Category -->
        @if($event->category)
            <div class="mb-2">
                <a href="{{ route('events.category', $event->category->slug ?? $event->category->id) }}" 
                   class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium hover:bg-blue-200 transition-colors">
                    {{ $event->category->name }}
                </a>
            </div>
        @endif
        
        <!-- Title -->
        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2">
            <a href="{{ route('events.show', $event->slug ?? $event->id) }}" class="hover:underline">
                {{ $event->title }}
            </a>
        </h3>
        
        <!-- Description -->
        <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-1">
            {{ Str::limit(strip_tags($event->description), 120) }}
        </p>
        
        <!-- Event Details -->
        <div class="space-y-2 mb-4">
            <!-- Date & Time -->
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-clock w-4 mr-3 text-gray-400"></i>
                <span>
                    {{ $event->start_date->format('M d, Y') }} at {{ $event->start_date->format('g:i A') }}
                    @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                        - {{ $event->end_date->format('M d, Y') }}
                    @endif
                </span>
            </div>
            
            <!-- Location -->
            @if($event->venue_name)
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-map-marker-alt w-4 mr-3 text-gray-400"></i>
                    <span class="line-clamp-1">{{ $event->venue_name }}@if($event->city), {{ $event->city }}@endif</span>
                </div>
            @endif
            
            <!-- Organizer -->
            @if($event->organizer)
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-user w-4 mr-3 text-gray-400"></i>
                    <span>{{ $event->organizer->name }}</span>
                </div>
            @endif
            
            <!-- Capacity -->
            @if($event->max_participants)
                @php
                    $registeredCount = $event->current_participants ?? 0;
                    $spotsLeft = $event->max_participants - $registeredCount;
                    $percentFull = ($registeredCount / $event->max_participants) * 100;
                @endphp
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-users w-4 mr-3 text-gray-400"></i>
                    <span>
                        {{ $registeredCount }}/{{ $event->max_participants }} registered
                        @if($spotsLeft <= 10 && $spotsLeft > 0)
                            <span class="text-orange-600 font-medium">({{ $spotsLeft }} spots left)</span>
                        @elseif($spotsLeft <= 0)
                            <span class="text-red-600 font-medium">(Full)</span>
                        @endif
                    </span>
                </div>
            @endif
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('events.show', $event->slug ?? $event->id) }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm transition-colors">
                <span>Learn More</span>
                <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
            
            @if(!$event->start_date->isPast() && ($event->max_participants ? ($event->current_participants ?? 0) < $event->max_participants : true))
                <a href="{{ route('events.show', $event->slug ?? $event->id) }}#register" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    @if($event->is_free || $event->registration_fee == 0)
                        Register Free
                    @else
                        Register - ${{ number_format($event->registration_fee, 0) }}
                    @endif
                </a>
            @elseif($event->max_participants && ($event->current_participants ?? 0) >= $event->max_participants)
                <span class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                    Event Full
                </span>
            @else
                <span class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                    Event Ended
                </span>
            @endif
        </div>
    </div>
</div>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>