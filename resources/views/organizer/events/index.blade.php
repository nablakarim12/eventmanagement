@extends('organizer.layouts.app')

@section('title', 'My Events')
@section('page-title', 'My Events')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-600 mt-2">Manage your events</p>
        </div>
        
        <a href="{{ route('organizer.events.create') }}" 
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Event
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('organizer.events.index') }}" class="flex flex-wrap gap-4">
            <div>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div>
                <select name="category" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search events..." 
                       class="border border-gray-300 rounded-lg px-3 py-2">
            </div>
            
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Filter
            </button>
            
            @if(request()->anyFilled(['status', 'category', 'search']))
                <a href="{{ route('organizer.events.index') }}" 
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    Clear
                </a>
            @endif
        </form>
    </div>

    @if($events->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($events as $event)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    @if($event->featured_image)
                        <img src="{{ asset('storage/' . $event->featured_image) }}" 
                             alt="{{ $event->title }}" 
                             class="w-full h-48 object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center" style="display:none;">
                            <div class="text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v2a1 1 0 01-1 1h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V8H3a1 1 0 01-1-1V5a1 1 0 011-1h4z"></path>
                                </svg>
                                <p class="text-xs text-gray-500">Image not found</p>
                            </div>
                        </div>
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v2a1 1 0 01-1 1h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V8H3a1 1 0 01-1-1V5a1 1 0 011-1h4z"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold text-gray-800 line-clamp-2">{{ $event->title }}</h3>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($event->status === 'published') bg-green-100 text-green-800
                                @elseif($event->status === 'draft') bg-yellow-100 text-yellow-800
                                @elseif($event->status === 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $event->description }}</p>
                        
                        <div class="space-y-2 text-sm text-gray-500 mb-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z"></path>
                                </svg>
                                {{ $event->category->name ?? 'No category' }}
                            </div>
                            
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z"></path>
                                </svg>
                                {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'TBD' }}
                            </div>
                            
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $event->venue_name ?? 'Online' }}
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div class="text-sm">
                                @if($event->registration_fee > 0)
                                    <span class="font-semibold text-green-600">RM {{ number_format($event->registration_fee, 2) }}</span>
                                @else
                                    <span class="font-semibold text-green-600">Free</span>
                                @endif
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('organizer.events.show', $event) }}" 
                                   class="text-green-600 hover:text-green-800" 
                                   title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('organizer.events.edit', $event) }}" 
                                   class="text-blue-600 hover:text-blue-800"
                                   title="Edit Event">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <form action="{{ route('organizer.events.duplicate', $event) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-800" title="Duplicate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </form>
                                
                                <form action="{{ route('organizer.events.destroy', $event) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this event?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete Event">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $events->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v2a1 1 0 01-1 1h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V8H3a1 1 0 01-1-1V5a1 1 0 011-1h4z"></path>
            </svg>
            
            <h3 class="text-lg font-medium text-gray-900 mb-2">No events found</h3>
            <p class="text-gray-500 mb-6">
                @if(request()->anyFilled(['status', 'category', 'search']))
                    No events match your current filters. Try adjusting your search criteria.
                @else
                    You haven't created any events yet. Create your first event to get started!
                @endif
            </p>
            
            <div class="space-x-4">
                <a href="{{ route('organizer.events.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create New Event
                </a>
                
                @if(request()->anyFilled(['status', 'category', 'search']))
                    <a href="{{ route('organizer.events.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection