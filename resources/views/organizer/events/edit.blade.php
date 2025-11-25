@extends('organizer.layouts.app')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('organizer.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
            @csrf
            @method('PUT')
            
            <!-- Hidden fields -->
            <input type="hidden" name="is_free" value="0">
            <input type="hidden" name="is_public" value="1">
            <input type="hidden" name="allow_waitlist" value="0">
            <input type="hidden" name="requires_approval" value="0">
            
            <!-- Event Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Event Title</label>
                <input type="text" 
                       name="title" 
                       id="title"
                       value="{{ old('title', $event->title) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror" 
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Event Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Event Description</label>
                <textarea name="description" 
                         id="description"
                         rows="4"
                         class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror" 
                         required>{{ old('description', $event->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category and Status Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Event Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category_id" 
                            id="category_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Event Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" 
                            id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Date and Time Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time</label>
                    <input type="datetime-local" 
                           name="start_date" 
                           id="start_date"
                           value="{{ old('start_date', $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror" 
                           required>
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date & Time</label>
                    <input type="datetime-local" 
                           name="end_date" 
                           id="end_date"
                           value="{{ old('end_date', $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location and Capacity Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Venue Name</label>
                    <input type="text" 
                           name="location" 
                           id="location"
                           value="{{ old('location', $event->venue_name) }}"
                           placeholder="Enter venue name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror" 
                           required>
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Participants -->
            <!-- Capacity Row -->
            <div class="mb-6">
                <div>
                    <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">Maximum Participants</label>
                    <input type="number" 
                           name="max_participants" 
                           id="max_participants"
                           value="{{ old('max_participants', $event->max_participants) }}"
                           min="1"
                           placeholder="Enter maximum number of participants"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('max_participants') border-red-500 @enderror">
                    @error('max_participants')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Registration Deadline Row -->
            <div class="mb-6">
                <div>
                    <label for="registration_deadline" class="block text-sm font-medium text-gray-700 mb-2">Registration Deadline</label>
                    <input type="datetime-local" 
                           name="registration_deadline" 
                           id="registration_deadline"
                           value="{{ old('registration_deadline', $event->registration_deadline ? \Carbon\Carbon::parse($event->registration_deadline)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('registration_deadline') border-red-500 @enderror">
                    <p class="text-sm text-gray-600 mt-1">Last date and time for participants to register for this event</p>
                    @error('registration_deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Price and Current Image Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price (Leave empty for free events)</label>
                    <input type="number" 
                           name="price" 
                           id="price"
                           step="0.01"
                           min="0"
                           value="{{ old('price', $event->price) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image Display -->
                @if($event->featured_image)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                    <img src="{{ asset('storage/' . $event->featured_image) }}" 
                         alt="{{ $event->title }}"
                         class="w-32 h-32 object-cover rounded-lg border">
                </div>
                @endif
            </div>

            <!-- Event Image Upload -->
            <div class="mb-6">
                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                    Event Poster / Banner
                </label>
                
                <!-- Current Image Preview -->
                @if($event->featured_image)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Poster</label>
                    <div class="relative inline-block">
                        <img src="{{ asset('storage/' . $event->featured_image) }}" 
                             alt="{{ $event->title }}"
                             class="w-48 h-28 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                        <div class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded text-xs">
                            Current
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- File Input -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                    <div class="mb-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    
                    <input type="file" 
                           name="featured_image" 
                           id="featured_image"
                           accept="image/*"
                           class="hidden"
                           onchange="previewImage(this)">
                           
                    <label for="featured_image" class="cursor-pointer">
                        <span class="mt-2 block text-sm font-medium text-gray-900">
                            {{ $event->featured_image ? 'Upload New Poster' : 'Upload Event Poster' }}
                        </span>
                        <span class="mt-1 block text-sm text-gray-500">
                            Click to browse or drag and drop
                        </span>
                    </label>
                    
                    <!-- Preview Area -->
                    <div id="imagePreview" class="mt-4 hidden">
                        <div class="relative inline-block">
                            <img id="previewImg" class="w-48 h-28 object-cover rounded-lg border-2 border-blue-400 shadow-sm">
                            <div class="absolute top-2 right-2 bg-blue-500 text-white px-2 py-1 rounded text-xs">
                                New
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Preview of new poster</p>
                    </div>
                </div>
                
                @error('featured_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    📏 Recommended: 1200×675 px (16:9 ratio) • 📁 JPG/PNG • 📦 Under 2MB
                </p>
            </div>

            <script>
            function previewImage(input) {
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    
                    // Validate file size (2MB = 2048KB)
                    if (file.size > 2048 * 1024) {
                        alert('File size must be less than 2MB');
                        input.value = '';
                        preview.classList.add('hidden');
                        return;
                    }
                    
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Please select an image file');
                        input.value = '';
                        preview.classList.add('hidden');
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.classList.add('hidden');
                }
            }
            </script>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('organizer.events.index') }}" 
                   class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                    Back to Events
                </a>
                
                <div class="flex space-x-4">
                    <button type="submit" name="action" value="save"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Event
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-set end date to be after start date
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        const endDateInput = document.getElementById('end_date');
        
        if (endDateInput.value === '' || new Date(endDateInput.value) <= startDate) {
            const endDate = new Date(startDate.getTime() + (2 * 60 * 60 * 1000)); // Add 2 hours
            endDateInput.value = endDate.toISOString().slice(0, 16);
        }
    });

    // Auto-set registration start to current time if not set
    if (document.getElementById('registration_start').value === '') {
        document.getElementById('registration_start').value = new Date().toISOString().slice(0, 16);
    }
</script>
@endsection