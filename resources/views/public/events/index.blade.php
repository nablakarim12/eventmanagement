@extends('public.layouts.app')

@section('title', 'Browse Events')

@section('content')
<!-- Hero Section with Search -->
<div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">Discover Amazing Events</h1>
            <p class="text-xl text-blue-100 mb-8">Find conferences, competitions, and networking opportunities</p>
            
            <!-- Advanced Search Form -->
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6 text-gray-900">
                <form method="GET" action="{{ route('events.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search events..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="date_filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">All Dates</option>
                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="tomorrow" {{ request('date_filter') == 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                            <option value="this_week" {{ request('date_filter') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="upcoming" {{ request('date_filter') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Results -->
<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <!-- Filter Bar -->
    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
        <div class="flex flex-wrap items-center gap-4">
            <h2 class="text-2xl font-bold text-gray-900">
                @if(request('search'))
                    Search Results for "{{ request('search') }}"
                @else
                    Upcoming Events
                @endif
            </h2>
            
            @if($events->total() > 0)
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $events->total() }} {{ Str::plural('event', $events->total()) }}
                </span>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <!-- Additional Filters -->
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Sort by:</label>
                <select onchange="updateSort(this.value)" class="text-sm border border-gray-300 rounded px-3 py-1 focus:ring-2 focus:ring-blue-500">
                    <option value="start_date" {{ request('sort') == 'start_date' ? 'selected' : '' }}>Date</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Price</option>
                    <option value="created" {{ request('sort') == 'created' ? 'selected' : '' }}>Recently Added</option>
                </select>
            </div>

            <!-- View Toggle -->
            <div class="flex border border-gray-300 rounded overflow-hidden">
                <button onclick="switchView('grid')" id="gridViewBtn" class="px-3 py-1 text-sm bg-blue-600 text-white">
                    <i class="fas fa-th-large"></i>
                </button>
                <button onclick="switchView('list')" id="listViewBtn" class="px-3 py-1 text-sm bg-gray-100 text-gray-600 hover:bg-gray-200">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    @if(request()->hasAny(['search', 'category', 'date_filter', 'city', 'price_filter']))
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="text-sm text-gray-600">Active filters:</span>
            
            @if(request('search'))
                <span class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                    Search: "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-2 hover:text-blue-600">×</a>
                </span>
            @endif
            
            @if(request('category'))
                <span class="inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                    {{ $categories->find(request('category'))->name ?? 'Category' }}
                    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="ml-2 hover:text-green-600">×</a>
                </span>
            @endif
            
            @if(request('date_filter'))
                <span class="inline-flex items-center bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                    {{ ucfirst(str_replace('_', ' ', request('date_filter'))) }}
                    <a href="{{ request()->fullUrlWithQuery(['date_filter' => null]) }}" class="ml-2 hover:text-purple-600">×</a>
                </span>
            @endif
            
            <a href="{{ route('events.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Clear all</a>
        </div>
    @endif

    <!-- Events Grid/List -->
    @if($events->count() > 0)
        <div id="eventsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                @include('public.events.partials.event-card', ['event' => $event])
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $events->appends(request()->query())->links() }}
        </div>
    @else
        <!-- No Events Found -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="mb-4">
                    <i class="fas fa-calendar-times text-6xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No events found</h3>
                <p class="text-gray-600 mb-6">
                    @if(request()->hasAny(['search', 'category', 'date_filter']))
                        Try adjusting your search criteria or <a href="{{ route('events.index') }}" class="text-blue-600 hover:underline">browse all events</a>.
                    @else
                        There are no upcoming events at the moment. Check back soon for new events!
                    @endif
                </p>
                @if(request()->hasAny(['search', 'category', 'date_filter']))
                    <a href="{{ route('events.index') }}" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                        <i class="fas fa-calendar mr-2"></i>
                        Browse All Events
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location = url;
}

function switchView(viewType) {
    const container = document.getElementById('eventsContainer');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (viewType === 'grid') {
        container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
        gridBtn.className = 'px-3 py-1 text-sm bg-blue-600 text-white';
        listBtn.className = 'px-3 py-1 text-sm bg-gray-100 text-gray-600 hover:bg-gray-200';
    } else {
        container.className = 'space-y-6';
        listBtn.className = 'px-3 py-1 text-sm bg-blue-600 text-white';
        gridBtn.className = 'px-3 py-1 text-sm bg-gray-100 text-gray-600 hover:bg-gray-200';
        
        // Add list styling to cards
        container.querySelectorAll('.event-card').forEach(card => {
            card.className = card.className.replace('flex-col', 'flex-row');
        });
    }
    
    localStorage.setItem('eventsView', viewType);
}

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('eventsView') || 'grid';
    switchView(savedView);
});
</script>
@endsection