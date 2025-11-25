@extends('admin.layouts.app')

@section('title', 'Material Details')
@section('page-title', 'Material Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.materials.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Materials
        </a>
    </div>
    
    <!-- Material Details -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">{{ $material->title }}</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Description -->
                    @if($material->description)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Description</h4>
                        <p class="text-gray-600">{{ $material->description }}</p>
                    </div>
                    @endif
                    
                    <!-- File Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">File Information</h4>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">File Name:</span>
                                <span class="text-sm text-gray-900">{{ basename($material->file_path) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">File Size:</span>
                                <span class="text-sm text-gray-900">{{ number_format(\Storage::size($material->file_path) / 1024, 1) }} KB</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">File Type:</span>
                                <span class="text-sm text-gray-900">{{ strtoupper(pathinfo($material->file_path, PATHINFO_EXTENSION)) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Downloads:</span>
                                <span class="text-sm text-gray-900">{{ number_format($material->download_count) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex space-x-4 pt-4">
                        <a href="{{ route('admin.materials.download', $material) }}" 
                           class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>Download Material
                        </a>
                        <form action="{{ route('admin.materials.destroy', $material) }}" 
                              method="POST" class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this material? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-2"></i>Delete Material
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Event Information -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 mb-3">Event Information</h4>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-blue-700">Event:</span>
                                <p class="text-sm text-blue-600">{{ $material->event->title }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-blue-700">Date:</span>
                                <p class="text-sm text-blue-600">{{ $material->event->start_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-blue-700">Status:</span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($material->event->status === 'active') bg-green-100 text-green-800
                                    @elseif($material->event->status === 'draft') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($material->event->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Organizer Information -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-green-800 mb-3">Organizer Information</h4>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-green-700">Name:</span>
                                <p class="text-sm text-green-600">{{ $material->event->organizer->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-green-700">Email:</span>
                                <p class="text-sm text-green-600">{{ $material->event->organizer->email }}</p>
                            </div>
                            @if($material->event->organizer->organization)
                            <div>
                                <span class="text-sm font-medium text-green-700">Organization:</span>
                                <p class="text-sm text-green-600">{{ $material->event->organizer->organization }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Material Stats -->
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-purple-800 mb-3">Material Statistics</h4>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-purple-700">Type:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($material->type === 'resource') bg-blue-100 text-blue-800
                                    @elseif($material->type === 'handout') bg-green-100 text-green-800
                                    @elseif($material->type === 'presentation') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($material->type) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-purple-700">Created:</span>
                                <p class="text-sm text-purple-600">{{ $material->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-purple-500">{{ $material->created_at->diffForHumans() }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-purple-700">Last Updated:</span>
                                <p class="text-sm text-purple-600">{{ $material->updated_at->format('M d, Y') }}</p>
                                <p class="text-xs text-purple-500">{{ $material->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection