@extends('organizer.layouts.app')

@section('title', 'Material Details')
@section('page-title', 'Material Details')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('organizer.materials.index') }}" class="text-gray-400 hover:text-gray-500">
                        Materials
                    </a>
                </li>
                <li class="flex">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">{{ $material->title }}</span>
                </li>
            </ol>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $material->title }}</h1>
                <p class="text-gray-600 mt-2">{{ $material->event->title }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('organizer.materials.edit', $material) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <button type="button" onclick="deleteMaterial()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Material Preview -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">File Preview</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div class="mx-auto h-20 w-20 flex items-center justify-center rounded-lg {{ $material->getFileTypeColor() }} mb-4">
                            <svg class="h-10 w-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                {!! $material->getFileTypeIcon() !!}
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">{{ $material->original_filename }}</h4>
                        <p class="text-sm text-gray-500 mb-4">{{ $material->getFileSizeFormatted() }} • {{ $material->getFileType() }}</p>
                        
                        @if($material->is_downloadable)
                            <a href="{{ route('organizer.materials.download', $material) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download
                            </a>
                        @else
                            <p class="text-sm text-gray-500">Downloads not allowed for this material</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($material->description)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Description</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 whitespace-pre-line">{{ $material->description }}</p>
                </div>
            </div>
            @endif

            <!-- Download Analytics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Download Analytics</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ $material->downloads_count }}</div>
                            <div class="text-sm text-gray-500">Total Downloads</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $material->unique_downloads_count }}</div>
                            <div class="text-sm text-gray-500">Unique Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">
                                {{ $material->event->registrations_count > 0 ? number_format(($material->unique_downloads_count / $material->event->registrations_count) * 100, 1) : '0' }}%
                            </div>
                            <div class="text-sm text-gray-500">Download Rate</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Downloads -->
            @if($recentDownloads->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Downloads</h3>
                </div>
                <div class="overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentDownloads as $download)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <svg class="h-4 w-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $download->user ? $download->user->name : 'Guest User' }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $download->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $download->created_at->format('M j, Y g:i A') }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Material Info -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Material Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Event</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ route('organizer.events.show', $material->event) }}" class="text-indigo-600 hover:text-indigo-500">
                                {{ $material->event->title }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Access Level</dt>
                        <dd class="mt-1">
                            @if($material->access_type === 'public')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Public
                                </span>
                            @elseif($material->access_type === 'registered_only')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Registered Only
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Checked-in Only
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">File Size</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->getFileSizeFormatted() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">File Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->getFileType() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->created_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @if($material->available_from)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Available From</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->available_from->format('M j, Y g:i A') }}</dd>
                    </div>
                    @endif
                    @if($material->available_until)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Available Until</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $material->available_until->format('M j, Y g:i A') }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('organizer.materials.download', $material) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download File
                    </a>
                    <a href="{{ route('organizer.events.show', $material->event) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6a2 2 0 012 2v10a2 2 0 01-2 2H8a2 2 0 01-2-2V9a2 2 0 012-2z" />
                        </svg>
                        View Event
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Delete Material</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to delete this material? This action cannot be undone and will remove the file permanently.</p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDeleteModal()" 
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <form method="POST" action="{{ route('organizer.materials.destroy', $material) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteMaterial() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endsection