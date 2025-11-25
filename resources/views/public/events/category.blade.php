@extends('public.layouts.app')

@section('title', $category->name . ' Events')

@section('content')
<!-- Category Header -->
<div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Breadcrumb -->
            <nav class="mb-6" aria-label="Breadcrumb">
                <ol class="flex items-center justify-center space-x-2 text-sm text-blue-100">
                    <li><a href="{{ route('events.index') }}" class="hover:text-white">All Events</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-white font-medium">{{ $category->name }}</li>
                </ol>
            </nav>

            <h1 class="text-4xl font-bold mb-4">{{ $category->name }} Events</h1>
            
            @if($category->description)
                <p class="text-xl text-blue-100 mb-6 max-w-3xl mx-auto">
                    {{ $category->description }}
                </p>
            @endif
            
            @if($events->total() > 0)
                <p class="text-blue-100">
                    {{ $events->total() }} {{ Str::plural('event', $events->total()) }} in this category
                </p>
            @endif
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('events.category', $category->slug ?? $category->id) }}" class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search {{ $category->name }} events..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Date Filter -->
            <div>
                <select name="date_filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Dates</option>
                    <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="tomorrow" {{ request('date_filter') == 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                    <option value="this_week" {{ request('date_filter') == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="upcoming" {{ request('date_filter') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                </select>
            </div>
            
            <!-- Price Filter -->
            <div>
                <select name="price_filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Any Price</option>
                    <option value="free" {{ request('price_filter') == 'free' ? 'selected' : '' }}>Free Events</option>
                    <option value="paid" {{ request('price_filter') == 'paid' ? 'selected' : '' }}>Paid Events</option>
                    <option value="under_50" {{ request('price_filter') == 'under_50' ? 'selected' : '' }}>Under $50</option>
                    <option value="under_100" {{ request('price_filter') == 'under_100' ? 'selected' : '' }}>Under $100</option>
                </select>
            </div>
            
            <!-- Search Button -->
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            
            <!-- Clear Filters -->
            @if(request()->hasAny(['search', 'date_filter', 'price_filter']))
                <a href="{{ route('events.category', $category->slug ?? $category->id) }}" 
                   class="text-gray-500 hover:text-gray-700 px-3 py-2">
                    <i class="fas fa-times mr-1"></i>Clear
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Events Content -->
<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <!-- Results Header -->
    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                @if(request('search'))
                    Search Results in {{ $category->name }}
                @else
                    {{ $category->name }} Events
                @endif
            </h2>
            @if(request('search'))
                <p class="text-gray-600 mt-1">
                    Results for "{{ request('search') }}" in {{ $category->name }}
                </p>
            @endif
        </div>

        @if($events->count() > 0)
            <div class="flex items-center gap-4">
                <!-- Sort Options -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Sort:</label>
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
        @endif
    </div>

    <!-- Active Filters Display -->
    @if(request()->hasAny(['search', 'date_filter', 'price_filter']))
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="text-sm text-gray-600">Filters:</span>
            
            @if(request('search'))
                <span class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                    "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-2 hover:text-blue-600">×</a>
                </span>
            @endif
            
            @if(request('date_filter'))
                <span class="inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                    {{ ucfirst(str_replace('_', ' ', request('date_filter'))) }}
                    <a href="{{ request()->fullUrlWithQuery(['date_filter' => null]) }}" class="ml-2 hover:text-green-600">×</a>
                </span>
            @endif
            
            @if(request('price_filter'))
                <span class="inline-flex items-center bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                    {{ ucfirst(str_replace('_', ' ', request('price_filter'))) }}
                    <a href="{{ request()->fullUrlWithQuery(['price_filter' => null]) }}" class="ml-2 hover:text-purple-600">×</a>
                </span>
            @endif
        </div>
    @endif

    <!-- Events Display -->
    @if($events->count() > 0)
        <div id="eventsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($events as $event)
                @include('public.events.partials.event-card', ['event' => $event])
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $events->appends(request()->query())->links() }}
        </div>
    @else
        <!-- No Events Message -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="mb-4">
                    <i class="fas fa-search text-6xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    @if(request()->hasAny(['search', 'date_filter', 'price_filter']))
                        No events found
                    @else
                        No {{ $category->name }} events yet
                    @endif
                </h3>
                <p class="text-gray-600 mb-6">
                    @if(request()->hasAny(['search', 'date_filter', 'price_filter']))
                        Try adjusting your search criteria or browse all {{ $category->name }} events.
                    @else
                        There are currently no events in this category. Check back soon or explore other categories!
                    @endif
                </p>
                <div class="space-y-3">
                    @if(request()->hasAny(['search', 'date_filter', 'price_filter']))
                        <a href="{{ route('events.category', $category->slug ?? $category->id) }}" 
                           class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-list mr-2"></i>
                            View All {{ $category->name }} Events
                        </a>
                    @endif
                    <div>
                        <a href="{{ route('events.index') }}" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Browse All Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Related Categories -->
@if($relatedCategories && $relatedCategories->count() > 0)
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Explore Other Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($relatedCategories as $relatedCategory)
                <a href="{{ route('events.category', $relatedCategory->slug ?? $relatedCategory->id) }}" 
                   class="bg-white rounded-lg p-4 text-center hover:shadow-md transition-shadow border border-gray-200 group">
                    <div class="text-2xl mb-2">
                        @if($relatedCategory->icon)
                            <i class="{{ $relatedCategory->icon }} text-blue-500 group-hover:text-blue-600"></i>
                        @else
                            <i class="fas fa-calendar text-blue-500 group-hover:text-blue-600"></i>
                        @endif
                    </div>
                    <div class="font-medium text-gray-900 text-sm">{{ $relatedCategory->name }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $relatedCategory->events_count ?? 0 }} events</div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
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
        container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8';
        gridBtn.className = 'px-3 py-1 text-sm bg-blue-600 text-white';
        listBtn.className = 'px-3 py-1 text-sm bg-gray-100 text-gray-600 hover:bg-gray-200';
    } else {
        container.className = 'space-y-6 mb-8';
        listBtn.className = 'px-3 py-1 text-sm bg-blue-600 text-white';
        gridBtn.className = 'px-3 py-1 text-sm bg-gray-100 text-gray-600 hover:bg-gray-200';
    }
    
    localStorage.setItem('categoryEventsView', viewType);
}

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('categoryEventsView') || 'grid';
    switchView(savedView);
});
</script>
@endsection