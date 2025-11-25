@extends('admin.layouts.app')

@section('title', 'Event Materials Management')
@section('page-title', 'Event Materials Management')

@section('content')
<div class="space-y-6">
    <!-- Header Section with Stats -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Materials Overview</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-file-alt text-2xl text-blue-600 mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Total Materials</p>
                        <p class="text-xl font-semibold text-gray-800">{{ $materials->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-download text-2xl text-green-600 mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Total Downloads</p>
                        <p class="text-xl font-semibold text-gray-800">{{ $materials->sum('download_count') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-calendar text-2xl text-purple-600 mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Active Events</p>
                        <p class="text-xl font-semibold text-gray-800">{{ $events->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-orange-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-file-pdf text-2xl text-orange-600 mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">PDF Materials</p>
                        <p class="text-xl font-semibold text-gray-800">{{ $materials->where('type', 'resource')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Materials</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by title, description, or event..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event</label>
                    <select name="event_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Material Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="resource" {{ request('type') === 'resource' ? 'selected' : '' }}>Resource</option>
                        <option value="handout" {{ request('type') === 'handout' ? 'selected' : '' }}>Handout</option>
                        <option value="presentation" {{ request('type') === 'presentation' ? 'selected' : '' }}>Presentation</option>
                        <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
            
            @if(request()->hasAny(['search', 'event_id', 'type']))
                <div class="flex justify-end">
                    <a href="{{ route('admin.materials.index') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </a>
                </div>
            @endif
        </form>
    </div>
    
    <!-- Materials List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Downloads</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organizer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($materials as $material)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $material->title }}</div>
                                    @if($material->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($material->description, 60) }}</div>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ strtoupper(pathinfo($material->file_path, PATHINFO_EXTENSION)) }} - {{ number_format(\Storage::size($material->file_path) / 1024, 1) }} KB
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $material->event->title }}</div>
                                <div class="text-sm text-gray-500">{{ $material->event->start_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($material->type === 'resource') bg-blue-100 text-blue-800
                                    @elseif($material->type === 'handout') bg-green-100 text-green-800
                                    @elseif($material->type === 'presentation') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($material->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-download text-gray-400 mr-2"></i>
                                    <span class="text-sm text-gray-900">{{ number_format($material->download_count) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $material->event->organizer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $material->event->organizer->organization ?? 'Individual' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $material->created_at->format('M d, Y') }}<br>
                                <span class="text-xs text-gray-400">{{ $material->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.materials.show', $material) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.materials.download', $material) }}" 
                                       class="text-green-600 hover:text-green-900" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('admin.materials.destroy', $material) }}" 
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this material?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-file-alt text-4xl mb-4"></i>
                                    <p>No materials found</p>
                                    @if(request()->hasAny(['search', 'event_id', 'type']))
                                        <p class="text-sm mt-2">Try adjusting your filters</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($materials->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $materials->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection