@extends('organizer.layouts.app')

@section('title', 'Create New Event')
@section('page-title', 'Create New Event')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('organizer.events.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
            @csrf
            
            <!-- Hidden fields -->
            <input type="hidden" name="is_free" value="0">
            <input type="hidden" name="is_public" value="1">
            <input type="hidden" name="allow_waitlist" value="0">
            <input type="hidden" name="requires_approval" value="0">
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Enhanced Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Title:</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category:</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            <option value="">Select</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date:</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date:</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registration Deadline:</label>
                        <input type="date" name="registration_deadline" value="{{ old('registration_deadline') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        <p class="text-sm text-gray-600 mt-1">Last date for participants to register for this event</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Time:</label>
                            <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Time:</label>
                            <input type="time" name="end_time" value="{{ old('end_time', '17:00') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name:</label>
                        <input type="text" name="venue_name" value="{{ old('venue_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="Enter venue name" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Venue Address:</label>
                        <textarea name="venue_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 resize-none" placeholder="Enter complete venue address">{{ old('venue_address') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City:</label>
                            <input type="text" name="city" value="{{ old('city', 'Kuala Lumpur') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country:</label>
                            <input type="text" name="country" value="{{ old('country', 'Malaysia') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Description:</label>
                        <textarea name="description" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 resize-none" placeholder="Enter event description">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Poster / Banner</label>
                        
                        <!-- Drag and Drop Upload Area -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors" 
                             id="drop-zone">
                            
                            <div id="upload-area" class="space-y-3">
                                <div class="mx-auto w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        <label for="featured_image" class="cursor-pointer text-blue-600 hover:text-blue-700 font-medium">
                                            Click to upload
                                        </label>
                                        or drag and drop your poster
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Recommended: 1200×675 px (16:9 ratio) • JPG/PNG • Under 2MB
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Preview Area (hidden initially) -->
                            <div id="preview-area" class="hidden">
                                <img id="preview-image" src="" alt="Preview" class="max-w-full h-32 object-cover rounded-lg mx-auto">
                                <p class="text-sm text-green-600 mt-2">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Image ready for upload
                                </p>
                            </div>
                            
                            <input type="file" 
                                   id="featured_image"
                                   name="featured_image" 
                                   accept="image/*" 
                                   class="hidden">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Participants:</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants', '100') }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registration Fee (RM):</label>
                        <input type="number" name="registration_fee" value="{{ old('registration_fee', '0') }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        <p class="text-xs text-gray-500 mt-1">Enter 0 for free events</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 text-center">
                <button type="submit" name="status" value="published" class="bg-green-600 hover:bg-green-700 text-white px-12 py-3 rounded text-lg font-medium transition-colors">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image upload and preview functionality
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('featured_image');
    const uploadArea = document.getElementById('upload-area');
    const previewArea = document.getElementById('preview-area');
    const previewImage = document.getElementById('preview-image');

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleFile(file);
        }
    });

    // Drag and drop handlers
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFile(files[0]);
        }
    });

    function handleFile(file) {
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) { // 2MB limit
            alert('File size must be less than 2MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            uploadArea.classList.add('hidden');
            previewArea.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});
    const today = new Date().toISOString().slice(0, 10);
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const startTimeInput = document.querySelector('input[name="start_time"]');
    const endTimeInput = document.querySelector('input[name="end_time"]');
    
    if (startDateInput) {
        startDateInput.min = today;
        startDateInput.addEventListener('change', function() {
            if (endDateInput) {
                endDateInput.value = this.value;
                endDateInput.min = this.value;
            }
        });
    }
    
    if (endDateInput) {
        endDateInput.addEventListener('change', function() {
            if (startDateInput) {
                this.min = startDateInput.value;
            }
        });
    }
    
    if (startTimeInput && endTimeInput) {
        startTimeInput.addEventListener('change', function() {
            const [hours, minutes] = this.value.split(':');
            const endHour = (parseInt(hours) + 3) % 24;
            endTimeInput.value = endHour.toString().padStart(2, '0') + ':' + minutes;
        });
    }
});
</script>
@endsection
