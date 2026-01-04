@extends('organizer.layouts.app')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('organizer.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8" id="editEventForm">
            @csrf
            @method('PUT')
            
            <!-- Hidden fields -->
            <input type="hidden" name="is_free" value="0">
            <input type="hidden" name="is_public" value="1">
            <input type="hidden" name="allow_waitlist" value="0">
            <input type="hidden" name="requires_approval" value="0">
            
            @php
                $isConferenceEvent = $event->delivery_mode && in_array($event->delivery_mode, ['face_to_face', 'online', 'hybrid']);
            @endphp

            @if($isConferenceEvent)
                <!-- CONFERENCE EVENT EDIT FORM -->
                <input type="hidden" name="event_form_type" value="conference">

                <div class="mb-6 p-4 bg-purple-50 border-l-4 border-purple-500 rounded">
                    <p class="text-purple-800 font-medium">
                        <i class="fas fa-info-circle mr-2"></i>
                        Editing Conference Event - {{ ucfirst(str_replace('_', ' ', $event->delivery_mode)) }} Mode
                    </p>
                </div>

                <!-- Event Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Conference Title <span class="text-red-500">*</span></label>
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
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Conference Description <span class="text-red-500">*</span></label>
                    <textarea name="description" 
                             id="description"
                             rows="4"
                             class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror" 
                             required>{{ old('description', $event->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Event Poster / Banner -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Poster / Banner</label>
                    
                    @if($event->featured_image)
                        <div class="mb-3">
                            <p class="text-xs text-gray-600 mb-2">Current Poster:</p>
                            <img src="{{ $event->featured_image }}" 
                                 alt="Current poster" 
                                 class="w-full max-w-md h-48 object-cover rounded-lg border-2 border-gray-300">
                        </div>
                    @endif
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors" id="drop-zone">
                        <div id="upload-area">
                            <div class="mx-auto w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 mb-1">
                                <label for="featured_image" class="text-blue-600 hover:text-blue-700 cursor-pointer font-medium">Click to upload</label> 
                                or drag and drop
                            </p>
                            <p class="text-sm text-gray-500">JPG/PNG • Under 2MB</p>
                            <p class="text-xs text-gray-400 mt-1">Recommended: 1920×1080px (16:9) or 1200×630px</p>
                        </div>
                        
                        <div id="preview-area" class="hidden">
                            <img id="preview-image" src="" alt="Preview" class="mx-auto max-w-md h-48 object-cover rounded-lg mb-3">
                            <p class="text-green-600 font-medium">
                                <i class="fas fa-check-circle mr-1"></i>New image ready for upload
                            </p>
                            <p class="text-sm text-gray-500 mt-1">Image will be uploaded when you save the event</p>
                        </div>
                        
                        <input type="file" 
                               id="featured_image" 
                               name="featured_image" 
                               accept="image/*"
                               class="hidden">
                    </div>
                    @error('featured_image')
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

                <!-- Delivery Mode -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Mode <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:border-blue-500 transition-all delivery-mode-label
                            {{ old('delivery_mode', $event->delivery_mode) == 'face_to_face' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                            <input type="radio" name="delivery_mode" value="face_to_face" 
                                   class="sr-only delivery-mode-radio" 
                                   {{ old('delivery_mode', $event->delivery_mode) == 'face_to_face' ? 'checked' : '' }}>
                            <div class="text-center">
                                <i class="fas fa-building text-3xl text-blue-600 mb-2"></i>
                                <p class="font-medium">Face to Face</p>
                            </div>
                        </label>

                        <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:border-green-500 transition-all delivery-mode-label
                            {{ old('delivery_mode', $event->delivery_mode) == 'online' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                            <input type="radio" name="delivery_mode" value="online" 
                                   class="sr-only delivery-mode-radio" 
                                   {{ old('delivery_mode', $event->delivery_mode) == 'online' ? 'checked' : '' }}>
                            <div class="text-center">
                                <i class="fas fa-laptop text-3xl text-green-600 mb-2"></i>
                                <p class="font-medium">Online</p>
                            </div>
                        </label>

                        <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition-all delivery-mode-label
                            {{ old('delivery_mode', $event->delivery_mode) == 'hybrid' ? 'border-purple-500 bg-purple-50' : 'border-gray-300' }}">
                            <input type="radio" name="delivery_mode" value="hybrid" 
                                   class="sr-only delivery-mode-radio" 
                                   {{ old('delivery_mode', $event->delivery_mode) == 'hybrid' ? 'checked' : '' }}>
                            <div class="text-center">
                                <i class="fas fa-globe text-3xl text-purple-600 mb-2"></i>
                                <p class="font-medium">Hybrid</p>
                            </div>
                        </label>
                    </div>
                    @error('delivery_mode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Conference Categories -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Conference Categories</label>
                    <div id="conference-categories-container">
                        @if(old('conference_categories', $event->conference_categories))
                            @foreach(old('conference_categories', $event->conference_categories) as $index => $category)
                                <div class="flex gap-2 mb-2 category-input-group">
                                    <input type="text" 
                                           name="conference_categories[]" 
                                           value="{{ $category }}"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Enter category name">
                                    <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 remove-category-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-conference-category-btn" class="mt-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        <i class="fas fa-plus mr-2"></i>Add Category
                    </button>
                    <p class="text-sm text-gray-600 mt-1">Add different categories for paper submissions (e.g., "AI & Machine Learning", "Cybersecurity")</p>
                </div>

                <!-- Face to Face Section -->
                <div id="f2f-section" class="mb-8 p-6 border-2 border-blue-300 rounded-lg bg-blue-50" style="display: {{ old('delivery_mode', $event->delivery_mode) == 'face_to_face' || old('delivery_mode', $event->delivery_mode) == 'hybrid' ? 'block' : 'none' }}">
                    <h3 class="text-xl font-bold text-blue-800 mb-4 flex items-center">
                        <i class="fas fa-building mr-2"></i>Face to Face Mode Settings
                    </h3>

                    <div class="space-y-4">
                        <!-- Reviewer Registration Deadline -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#1</span>
                                Reviewer Registration Deadline
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_reviewer_registration_deadline" 
                                   value="{{ old('f2f_reviewer_registration_deadline', $event->f2f_reviewer_registration_deadline ? \Carbon\Carbon::parse($event->f2f_reviewer_registration_deadline)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Paper Submission Deadline -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#2</span>
                                Paper Submission Deadline
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_paper_submission_deadline" 
                                   value="{{ old('f2f_paper_submission_deadline', $event->f2f_paper_submission_deadline ? \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Review Deadline -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#3</span>
                                Review Deadline
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_review_deadline" 
                                   value="{{ old('f2f_review_deadline', $event->f2f_review_deadline ? \Carbon\Carbon::parse($event->f2f_review_deadline)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Acceptance Notification Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#4</span>
                                Acceptance Notification Date
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_acceptance_notification_date" 
                                   value="{{ old('f2f_acceptance_notification_date', $event->f2f_acceptance_notification_date ? \Carbon\Carbon::parse($event->f2f_acceptance_notification_date)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Payment Deadline -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#5</span>
                                Payment Deadline
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_payment_deadline" 
                                   value="{{ old('f2f_payment_deadline', $event->f2f_payment_deadline ? \Carbon\Carbon::parse($event->f2f_payment_deadline)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Conference Start Date & Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs mr-1">#6</span>
                                Conference Start Date
                            </label>
                            <input type="date" 
                                   name="f2f_start_date" 
                                   value="{{ old('f2f_start_date', $event->f2f_start_date ? \Carbon\Carbon::parse($event->f2f_start_date)->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Start Time</label>
                                    <input type="time" 
                                           name="f2f_start_time" 
                                           value="{{ old('f2f_start_time', $event->f2f_start_time) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">End Time</label>
                                    <input type="time" 
                                           name="f2f_end_time" 
                                           value="{{ old('f2f_end_time', $event->f2f_end_time) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>

                        <!-- Conference End Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-pink-500 text-white px-2 py-1 rounded-full text-xs mr-1">#7</span>
                                Conference End Date
                            </label>
                            <input type="date" 
                                   name="f2f_end_date" 
                                   value="{{ old('f2f_end_date', $event->f2f_end_date ? \Carbon\Carbon::parse($event->f2f_end_date)->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Venue -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name</label>
                            <input type="text" 
                                   name="f2f_venue_name" 
                                   value="{{ old('f2f_venue_name', $event->venue_name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                   placeholder="Enter venue name">
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="f2f_venue_address" 
                                     rows="2"
                                     class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                     placeholder="Enter venue address">{{ old('f2f_venue_address', $event->venue_address) }}</textarea>
                        </div>

                        <!-- City & Country -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" 
                                       name="f2f_city" 
                                       value="{{ old('f2f_city', $event->city) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <input type="text" 
                                       name="f2f_country" 
                                       value="{{ old('f2f_country', $event->country) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Online Section -->
                <div id="online-section" class="mb-8 p-6 border-2 border-green-300 rounded-lg bg-green-50" style="display: {{ old('delivery_mode', $event->delivery_mode) == 'online' || old('delivery_mode', $event->delivery_mode) == 'hybrid' ? 'block' : 'none' }}">
                    <h3 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                        <i class="fas fa-laptop mr-2"></i>Online Mode Settings
                    </h3>

                    <div class="space-y-4">
                        <!-- Reviewer Registration Deadline -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#1</span>
                                Reviewer Registration Deadline
                            </label>
                            <input type="datetime-local" 
                                   name="online_reviewer_registration_deadline" 
                                   value="{{ old('online_reviewer_registration_deadline', $event->online_reviewer_registration_deadline ? \Carbon\Carbon::parse($event->online_reviewer_registration_deadline)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Paper Submission Deadline -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#2</span>
                                Paper Submission Deadline
                            </label>
                            <input type="datetime-local" 
                                   name="online_paper_submission_deadline" 
                                   value="{{ old('online_paper_submission_deadline', $event->online_paper_submission_deadline ? \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Review Deadline -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#3</span>
                                Review Deadline
                            </label>
                            <input type="datetime-local" 
                                   name="online_review_deadline" 
                                   value="{{ old('online_review_deadline', $event->online_review_deadline ? \Carbon\Carbon::parse($event->online_review_deadline)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Acceptance Notification Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#4</span>
                                Acceptance Notification Date
                            </label>
                            <input type="datetime-local" 
                                   name="online_acceptance_notification_date" 
                                   value="{{ old('online_acceptance_notification_date', $event->online_acceptance_notification_date ? \Carbon\Carbon::parse($event->online_acceptance_notification_date)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Payment Deadline -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs mr-1">#5</span>
                                Payment Deadline
                            </label>
                            <input type="datetime-local" 
                                   name="online_payment_deadline" 
                                   value="{{ old('online_payment_deadline', $event->online_payment_deadline ? \Carbon\Carbon::parse($event->online_payment_deadline)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Conference Start Date & Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs mr-1">#6</span>
                                Conference Start Date
                            </label>
                            <input type="date" 
                                   name="online_start_date" 
                                   value="{{ old('online_start_date', $event->online_start_date ? \Carbon\Carbon::parse($event->online_start_date)->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Start Time</label>
                                    <input type="time" 
                                           name="online_start_time" 
                                           value="{{ old('online_start_time', $event->online_start_time) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">End Time</label>
                                    <input type="time" 
                                           name="online_end_time" 
                                           value="{{ old('online_end_time', $event->online_end_time) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>

                        <!-- Conference End Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="bg-pink-500 text-white px-2 py-1 rounded-full text-xs mr-1">#7</span>
                                Conference End Date
                            </label>
                            <input type="date" 
                                   name="online_end_date" 
                                   value="{{ old('online_end_date', $event->online_end_date ? \Carbon\Carbon::parse($event->online_end_date)->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Platform Link -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Online Platform Link</label>
                            <input type="url" 
                                   name="online_platform_link" 
                                   value="{{ old('online_platform_link', $event->online_platform_link) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                   placeholder="https://zoom.us/j/...">
                        </div>
                    </div>
                </div>

            @else
                <!-- INNOVATION EVENT EDIT FORM -->
                <input type="hidden" name="event_form_type" value="innovation">

                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                    <p class="text-green-800 font-medium">
                        <i class="fas fa-lightbulb mr-2"></i>
                        Editing Innovation Event
                    </p>
                </div>

                <!-- Enhanced Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Title: <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category:</label>
                            <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <option value="">Select</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Innovation Categories -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Innovation Categories: <span class="text-red-500">*</span></label>
                            <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-300 rounded p-3">
                                @php
                                    $innovationCategoriesOptions = ['High School', 'voca', 'sec', 'undergraduate', 'postgraduate'];
                                    $selectedInnovationCategories = old('innovation_categories', 
                                        is_array($event->innovation_categories) ? $event->innovation_categories : 
                                        (is_string($event->innovation_categories) && !empty($event->innovation_categories) ? json_decode($event->innovation_categories, true) : [])
                                    ) ?? [];
                                @endphp
                                @foreach($innovationCategoriesOptions as $catOption)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="innovation_categories[]" value="{{ $catOption }}" 
                                               {{ in_array($catOption, $selectedInnovationCategories) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $catOption)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Innovation Themes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Innovation Themes: <span class="text-red-500">*</span></label>
                            <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-300 rounded p-3">
                                @php
                                    $innovationThemesOptions = ['AI', 'b', 'c'];
                                    $selectedInnovationThemes = old('innovation_theme', 
                                        is_array($event->innovation_theme) ? $event->innovation_theme : 
                                        (is_string($event->innovation_theme) && !empty($event->innovation_theme) ? json_decode($event->innovation_theme, true) : [])
                                    ) ?? [];
                                @endphp
                                @foreach($innovationThemesOptions as $themeOption)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="innovation_theme[]" value="{{ $themeOption }}" 
                                               {{ in_array($themeOption, $selectedInnovationThemes) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ ucwords($themeOption) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date: <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" value="{{ old('start_date', $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date: <span class="text-red-500">*</span></label>
                                <input type="date" name="end_date" value="{{ old('end_date', $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Registration Deadline: <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="registration_deadline" value="{{ old('registration_deadline', $event->registration_deadline ? \Carbon\Carbon::parse($event->registration_deadline)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            <p class="text-sm text-gray-600 mt-1">Last date for participants to register for this event</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jury Registration Deadline:</label>
                            <input type="datetime-local" name="jury_registration_deadline" value="{{ old('jury_registration_deadline', $event->jury_registration_deadline ? \Carbon\Carbon::parse($event->jury_registration_deadline)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-sm text-gray-600 mt-1">Deadline for jury members to register</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Submission Deadline:</label>
                            <input type="datetime-local" name="submission_deadline" value="{{ old('submission_deadline', $event->submission_deadline ? \Carbon\Carbon::parse($event->submission_deadline)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-sm text-gray-600 mt-1">Deadline for project submissions</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Acceptance Notification Date:</label>
                            <input type="datetime-local" name="acceptance_notification_date" value="{{ old('acceptance_notification_date', $event->acceptance_notification_date ? \Carbon\Carbon::parse($event->acceptance_notification_date)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-sm text-gray-600 mt-1">When participants will be notified of acceptance</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time: <span class="text-red-500">*</span></label>
                                <input type="time" name="start_time" value="{{ old('start_time', $event->start_time) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time: <span class="text-red-500">*</span></label>
                                <input type="time" name="end_time" value="{{ old('end_time', $event->end_time) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name: <span class="text-red-500">*</span></label>
                            <input type="text" name="venue_name" value="{{ old('venue_name', $event->venue_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="Enter venue name" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue Address: <span class="text-red-500">*</span></label>
                            <textarea name="venue_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 resize-none" placeholder="Enter complete venue address" required>{{ old('venue_address', $event->venue_address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City: <span class="text-red-500">*</span></label>
                                <input type="text" name="city" value="{{ old('city', $event->city) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country: <span class="text-red-500">*</span></label>
                                <input type="text" name="country" value="{{ old('country', $event->country) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Description: <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 resize-none" placeholder="Enter event description" required>{{ old('description', $event->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Poster / Banner</label>
                            
                            <!-- Current Image Preview -->
                            @if($event->featured_image)
                            <div class="mb-3">
                                <p class="text-xs text-gray-600 mb-2">Current Poster:</p>
                                <img src="{{ str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . $event->featured_image) }}" 
                                     alt="{{ $event->title }}"
                                     class="w-full h-32 object-cover rounded-lg border-2 border-gray-300 shadow-sm mb-2">
                            </div>
                            @endif
                            
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
                                            or drag and drop {{ $event->featured_image ? 'a new poster' : 'your poster' }}
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
                                        New image ready for upload
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Participants: <span class="text-red-500">*</span></label>
                            <input type="number" name="max_participants" value="{{ old('max_participants', $event->max_participants) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Registration Fee (RM): <span class="text-red-500">*</span></label>
                            <input type="number" name="registration_fee" value="{{ old('registration_fee', $event->registration_fee) }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            <p class="text-xs text-gray-500 mt-1">Enter 0 for free events</p>
                            <p class="text-xs text-blue-600 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Fee is only payable by participants who are accepted after paper/abstract submission
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-money-bill-wave mr-1 text-emerald-600"></i>
                                Payment Deadline: <span class="text-gray-500">(Optional - for events with fees)</span>
                            </label>
                            <input type="datetime-local" 
                                   name="payment_deadline" 
                                   value="{{ old('payment_deadline', $event->payment_deadline ? \Carbon\Carbon::parse($event->payment_deadline)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-sm text-gray-600 mt-1">Last date for accepted participants to complete payment</p>
                        </div>

                        <!-- Extended Deadlines Section -->
                        <div class="border-t pt-4 mt-4">
                            <h4 class="font-medium text-gray-800 mb-3">
                                <i class="fas fa-clock mr-2 text-blue-600"></i>
                                Extended Deadlines <span class="text-xs text-gray-500">(Optional)</span>
                            </h4>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Extended Registration Deadline:</label>
                                    <input type="datetime-local" name="extended_registration_deadline" value="{{ old('extended_registration_deadline', $event->extended_registration_deadline ? \Carbon\Carbon::parse($event->extended_registration_deadline)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Extended Jury Deadline:</label>
                                    <input type="datetime-local" name="extended_jury_deadline" value="{{ old('extended_jury_deadline', $event->extended_jury_deadline ? \Carbon\Carbon::parse($event->extended_jury_deadline)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Extended Submission Deadline:</label>
                                    <input type="datetime-local" name="extended_submission_deadline" value="{{ old('extended_submission_deadline', $event->extended_submission_deadline ? \Carbon\Carbon::parse($event->extended_submission_deadline)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Extended Notification Date:</label>
                                    <input type="datetime-local" name="extended_notification_date" value="{{ old('extended_notification_date', $event->extended_notification_date ? \Carbon\Carbon::parse($event->extended_notification_date)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Contact & Additional Info -->
                        <div class="border-t pt-4 mt-4">
                            <h4 class="font-medium text-gray-800 mb-3">Contact Information</h4>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email:</label>
                                    <input type="email" name="contact_email" value="{{ old('contact_email', $event->contact_email) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="contact@example.com">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone:</label>
                                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $event->contact_phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="+60123456789">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Website URL:</label>
                                    <input type="url" name="website_url" value="{{ old('website_url', $event->website_url) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="https://example.com">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">State/Province:</label>
                                    <input type="text" name="state" value="{{ old('state', $event->state) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="Enter state/province">
                                </div>
                            </div>
                        </div>

                        <!-- Event Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Status:</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <!-- Event Settings Checkboxes -->
                        <div class="border-t pt-4">
                            <h4 class="font-medium text-gray-800 mb-3">Event Settings</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_public" value="1" {{ old('is_public', $event->is_public) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Make event public</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', $event->requires_approval) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Require approval for registrations</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="allow_waitlist" value="1" {{ old('allow_waitlist', $event->allow_waitlist) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Allow waitlist when event is full</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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

<script>
// Image upload and preview functionality
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('featured_image');
const uploadArea = document.getElementById('upload-area');
const previewArea = document.getElementById('preview-area');
const previewImage = document.getElementById('preview-image');

// File input change handler
if (fileInput) {
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleFile(file);
        }
    });
}

// Drag and drop handlers
if (dropZone) {
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
}

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

// Conference form scripts
const isConferenceEvent = {{ $isConferenceEvent ? 'true' : 'false' }};

if (isConferenceEvent) {
    // Delivery mode toggle
    document.querySelectorAll('.delivery-mode-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const f2fSection = document.getElementById('f2f-section');
            const onlineSection = document.getElementById('online-section');
            
            // Update label styles
            document.querySelectorAll('.delivery-mode-label').forEach(label => {
                label.classList.remove('border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50', 'border-purple-500', 'bg-purple-50');
                label.classList.add('border-gray-300');
            });
            
            this.parentElement.classList.remove('border-gray-300');
            
            if (this.value === 'face_to_face') {
                this.parentElement.classList.add('border-blue-500', 'bg-blue-50');
                f2fSection.style.display = 'block';
                onlineSection.style.display = 'none';
            } else if (this.value === 'online') {
                this.parentElement.classList.add('border-green-500', 'bg-green-50');
                f2fSection.style.display = 'none';
                onlineSection.style.display = 'block';
            } else if (this.value === 'hybrid') {
                this.parentElement.classList.add('border-purple-500', 'bg-purple-50');
                f2fSection.style.display = 'block';
                onlineSection.style.display = 'block';
            }
        });
    });

    // Conference categories management
    document.getElementById('add-conference-category-btn').addEventListener('click', function() {
        const container = document.getElementById('conference-categories-container');
        const newInput = document.createElement('div');
        newInput.className = 'flex gap-2 mb-2 category-input-group';
        newInput.innerHTML = `
            <input type="text" 
                   name="conference_categories[]" 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Enter category name">
            <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 remove-category-btn">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(newInput);
        
        newInput.querySelector('.remove-category-btn').addEventListener('click', function() {
            newInput.remove();
        });
    });

    document.querySelectorAll('.remove-category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.category-input-group').remove();
        });
    });

    // Filter empty categories before submission
    document.getElementById('editEventForm').addEventListener('submit', function(e) {
        const categoryInputs = document.querySelectorAll('input[name="conference_categories[]"]');
        categoryInputs.forEach(input => {
            if (!input.value.trim()) {
                input.remove();
            }
        });
    });
}
</script>
@endsection
