@extends('organizer.layouts.app')

@section('title', 'Edit Material')
@section('page-title', 'Edit Material')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                    <a href="{{ route('organizer.materials.show', $material) }}" class="ml-4 text-gray-400 hover:text-gray-500">
                        {{ $material->title }}
                    </a>
                </li>
                <li class="flex">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">Edit</span>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">Edit Material</h1>
        <p class="text-gray-600 mt-2">Update material information and settings</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('organizer.materials.update', $material) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <!-- Current File Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Current File</h3>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 flex items-center justify-center rounded-lg {{ $material->getFileTypeColor() }}">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                {!! $material->getFileTypeIcon() !!}
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $material->original_filename }}</p>
                        <p class="text-sm text-gray-500">{{ $material->getFileSizeFormatted() }} • {{ $material->getFileType() }}</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('organizer.materials.download', $material) }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-500">Download</a>
                    </div>
                </div>
            </div>

            <!-- Material Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Material Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $material->title) }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('title') border-red-300 @enderror"
                       placeholder="e.g., Event Handbook, Presentation Slides">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                          placeholder="Brief description of the material...">{{ old('description', $material->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Replace File (Optional) -->
            <div>
                <label for="file" class="block text-sm font-medium text-gray-700">Replace File (Optional)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Upload a new file</span>
                                <input id="file" name="file" type="file" class="sr-only" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PDF, DOC, PPT, XLS, Images up to 50MB</p>
                        <p class="text-xs text-gray-400">Leave empty to keep current file</p>
                    </div>
                </div>
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Access Control -->
            <div>
                <label for="access_type" class="block text-sm font-medium text-gray-700">Access Level *</label>
                <select name="access_type" id="access_type" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('access_type') border-red-300 @enderror">
                    <option value="public" {{ old('access_type', $material->access_type) == 'public' ? 'selected' : '' }}>Public - Anyone can access</option>
                    <option value="registered_only" {{ old('access_type', $material->access_type) == 'registered_only' ? 'selected' : '' }}>Registered Only - Only registered participants</option>
                    <option value="checked_in_only" {{ old('access_type', $material->access_type) == 'checked_in_only' ? 'selected' : '' }}>Checked-in Only - Only participants who attended</option>
                </select>
                @error('access_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Availability Period -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="available_from" class="block text-sm font-medium text-gray-700">Available From</label>
                    <input type="datetime-local" name="available_from" id="available_from" 
                           value="{{ old('available_from', $material->available_from?->format('Y-m-d\TH:i')) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('available_from') border-red-300 @enderror">
                    @error('available_from')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Leave blank to make available immediately</p>
                </div>

                <div>
                    <label for="available_until" class="block text-sm font-medium text-gray-700">Available Until</label>
                    <input type="datetime-local" name="available_until" id="available_until" 
                           value="{{ old('available_until', $material->available_until?->format('Y-m-d\TH:i')) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('available_until') border-red-300 @enderror">
                    @error('available_until')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Leave blank for no expiration</p>
                </div>
            </div>

            <!-- Options -->
            <div class="space-y-4">
                <div class="flex items-center">
                    <input id="is_downloadable" name="is_downloadable" type="checkbox" value="1" 
                           {{ old('is_downloadable', $material->is_downloadable) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_downloadable" class="ml-2 block text-sm text-gray-900">
                        Allow downloads
                    </label>
                </div>
                <p class="text-xs text-gray-500 ml-6">Uncheck to only allow viewing without downloading</p>
            </div>

            <!-- Download Statistics -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Download Statistics</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-600">{{ $material->downloads_count }}</div>
                        <div class="text-xs text-gray-500">Total Downloads</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $material->unique_downloads_count }}</div>
                        <div class="text-xs text-gray-500">Unique Users</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">
                            {{ $material->event->registrations_count > 0 ? number_format(($material->unique_downloads_count / $material->event->registrations_count) * 100, 1) : '0' }}%
                        </div>
                        <div class="text-xs text-gray-500">Download Rate</div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('organizer.materials.show', $material) }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Material
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// File upload preview
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        
        // Update the upload area with file info
        const uploadArea = document.querySelector('.border-dashed');
        uploadArea.innerHTML = `
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-gray-600">
                    <p class="font-medium text-gray-900">${fileName}</p>
                    <p class="text-gray-500">${fileSize} MB</p>
                </div>
                <button type="button" onclick="clearFile()" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Choose different file
                </button>
                <p class="text-xs text-gray-400">This will replace the current file</p>
            </div>
        `;
        uploadArea.classList.remove('border-dashed');
        uploadArea.classList.add('border-solid', 'border-green-300', 'bg-green-50');
    }
});

function clearFile() {
    document.getElementById('file').value = '';
    location.reload(); // Simple way to reset the upload area
}
</script>
@endsection