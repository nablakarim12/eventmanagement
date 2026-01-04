@extends('organizer.layouts.app')

@section('title', 'Edit Innovation Competition Event')
@section('page-title', 'Edit Innovation Competition Event')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-6xl mx-auto">
        <form action="{{ route('organizer.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
            @csrf
            @method('PUT')
            
            <!-- Hidden fields -->
            <input type="hidden" name="is_free" value="0">
            <input type="hidden" name="is_public" value="1">
            <input type="hidden" name="allow_waitlist" value="0">
            <input type="hidden" name="requires_approval" value="0">
            <input type="hidden" name="event_form_type" value="innovation">
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="font-semibold text-red-800 mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>Please correct the following errors:
                    </p>
                    <ul class="list-disc list-inside text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Section 1: Basic Event Information -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-blue-500">
                    Basic Event Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Title: <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category: <span class="text-red-500">*</span></label>
                            <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    @if($category->name === 'Innovation Competition')
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }} selected>{{ $category->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Description: <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 resize-none" placeholder="Describe your innovation competition event" required>{{ old('description', $event->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Innovation Categories: <span class="text-red-500">*</span></label>
                            <div id="categories-container" class="space-y-2">
                                @php
                                    $savedCategories = old('innovation_categories', $event->innovation_categories ?? []);
                                    if (empty($savedCategories)) $savedCategories = [''];
                                @endphp
                                @foreach($savedCategories as $index => $category)
                                <div class="category-item flex items-center gap-2">
                                    <input type="text" name="innovation_categories[]" value="{{ $category }}" class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="e.g., High School, University" required>
                                    <button type="button" class="remove-category {{ count($savedCategories) <= 1 ? 'hidden' : '' }} px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-category" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors text-sm">
                                <i class="fas fa-plus mr-1"></i> Add Another Category
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Add participant categories (e.g., High School, Secondary School, University).</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Innovation Themes: <span class="text-red-500">*</span></label>
                            <div id="themes-container" class="space-y-2">
                                @php
                                    $savedThemes = old('innovation_theme', $event->innovation_theme ?? []);
                                    if (empty($savedThemes)) $savedThemes = [''];
                                @endphp
                                @foreach($savedThemes as $index => $theme)
                                <div class="theme-item flex items-center gap-2">
                                    <input type="text" name="innovation_theme[]" value="{{ $theme }}" class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="e.g., Artificial Intelligence" required>
                                    <button type="button" class="remove-theme {{ count($savedThemes) <= 1 ? 'hidden' : '' }} px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-theme" class="mt-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors text-sm">
                                <i class="fas fa-plus mr-1"></i> Add Another Theme
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Add innovation themes/topics (e.g., AI, IoT, Green Technology, Smart Cities). Participants will choose from these.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Location, Price, and Participants -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-blue-500">
                    Location, Price & Participants
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
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
                                <input type="text" name="city" value="{{ old('city', $event->city) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="Enter city" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country: <span class="text-red-500">*</span></label>
                                <input type="text" name="country" value="{{ old('country', $event->country) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="Enter country" required>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Participants: <span class="text-red-500">*</span></label>
                            <input type="number" name="max_participants" value="{{ old('max_participants', $event->max_participants) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="Enter maximum participants" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Registration Fee (RM): <span class="text-red-500">*</span></label>
                            <input type="number" name="registration_fee" value="{{ old('registration_fee', $event->registration_fee ?? '0') }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                            <p class="text-xs text-gray-500 mt-1">Enter 0 for free events</p>
                            <p class="text-xs text-blue-600 mt-1 font-medium"><i class="fas fa-info-circle mr-1"></i>Fee is only payable by participants who are accepted after paper/abstract submission</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Poster / Banner</label>
                            
                            @if($event->featured_image)
                                <div class="mb-3">
                                    <p class="text-xs text-gray-600 mb-2">Current Poster:</p>
                                    <img src="{{ $event->featured_image }}" 
                                         alt="{{ $event->title }}"
                                         class="w-full max-h-48 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                                </div>
                            @endif
                            
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors" id="drop-zone">
                                <div id="upload-area" class="space-y-3">
                                    <label for="featured_image" class="cursor-pointer block">
                                        <div class="mx-auto w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-3">
                                            <span class="text-blue-600 hover:text-blue-700 font-medium">
                                                Click to upload
                                            </span>
                                            or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">JPG/PNG • Under 2MB</p>
                                        <p class="text-xs text-gray-400 mt-1">Recommended: 1920×1080px (16:9) or 1200×630px</p>
                                    </label>
                                </div>
                                <div id="preview-area" class="hidden">
                                    <img id="preview-image" src="" alt="Preview" class="max-w-full h-32 object-cover rounded-lg mx-auto">
                                    <p class="text-sm text-green-600 mt-2">
                                        <i class="fas fa-check-circle mr-1"></i>New image ready for upload
                                    </p>
                                </div>
                                <input type="file" id="featured_image" name="featured_image" accept="image/*" class="hidden">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Delivery Mode Selection -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-blue-500">
                    Event Delivery Mode
                </h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Delivery Mode: <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative flex items-center p-4 border-2 {{ old('delivery_mode', $event->delivery_mode) == 'face_to_face' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }} rounded-lg cursor-pointer hover:border-blue-500 transition-colors delivery-mode-option">
                            <input type="radio" name="delivery_mode" value="face_to_face" class="mr-3" required {{ old('delivery_mode', $event->delivery_mode) == 'face_to_face' ? 'checked' : '' }}>
                            <div>
                                <div class="font-semibold text-gray-900">On-Site Only</div>
                                <div class="text-xs text-gray-500">Physical event at venue</div>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 {{ old('delivery_mode', $event->delivery_mode) == 'online' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }} rounded-lg cursor-pointer hover:border-blue-500 transition-colors delivery-mode-option">
                            <input type="radio" name="delivery_mode" value="online" class="mr-3" required {{ old('delivery_mode', $event->delivery_mode) == 'online' ? 'checked' : '' }}>
                            <div>
                                <div class="font-semibold text-gray-900">Online Only</div>
                                <div class="text-xs text-gray-500">Virtual event</div>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 {{ old('delivery_mode', $event->delivery_mode) == 'hybrid' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }} rounded-lg cursor-pointer hover:border-blue-500 transition-colors delivery-mode-option">
                            <input type="radio" name="delivery_mode" value="hybrid" class="mr-3" required {{ old('delivery_mode', $event->delivery_mode) == 'hybrid' ? 'checked' : '' }}>
                            <div>
                                <div class="font-semibold text-gray-900">Hybrid (Both)</div>
                                <div class="text-xs text-gray-500">On-Site + Online with different dates</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 4: On-Site Event Details (Conditional) -->
            <div id="f2f-section" class="mb-8 hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-green-500">
                    <i class="fas fa-users mr-2"></i>On-Site Event Details
                </h2>
                
                <div class="space-y-6">
                    <!-- Deadlines Section -->
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-calendar-check mr-2"></i>Event Deadlines
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Participant Registration Deadline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs mr-2">1</span>
                                    Participant Registration Deadline:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="f2f_registration_deadline" id="f2f_registration_deadline" 
                                       value="{{ old('f2f_registration_deadline', $event->f2f_registration_deadline ? $event->f2f_registration_deadline->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                                <p class="text-xs text-gray-500 mt-1">Must be today or later</p>
                                <p id="f2f_registration_deadline_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Jury Registration Deadline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-orange-500 text-white rounded-full text-xs mr-2">2</span>
                                    Jury Registration Deadline:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="f2f_jury_registration_deadline" id="f2f_jury_registration_deadline" 
                                       value="{{ old('f2f_jury_registration_deadline', $event->f2f_jury_registration_deadline ? $event->f2f_jury_registration_deadline->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                                <p class="text-xs text-gray-500 mt-1">Must be today or later</p>
                                <p id="f2f_jury_deadline_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Submission Deadline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-purple-500 text-white rounded-full text-xs mr-2">3</span>
                                    Submission Deadline (Paper/Abstract):
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="f2f_submission_deadline" id="f2f_submission_deadline" 
                                       value="{{ old('f2f_submission_deadline', $event->f2f_submission_deadline ? $event->f2f_submission_deadline->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                                <p class="text-xs text-gray-500 mt-1">Must be after participant registration deadline</p>
                                <p id="f2f_submission_deadline_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Acceptance Notification Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white rounded-full text-xs mr-2">4</span>
                                    Acceptance Notification Date:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="f2f_acceptance_notification_date" id="f2f_acceptance_notification_date" 
                                       value="{{ old('f2f_acceptance_notification_date', $event->f2f_acceptance_notification_date ? $event->f2f_acceptance_notification_date->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                                <p class="text-xs text-gray-500 mt-1">Must be after submission deadline</p>
                                <p id="f2f_acceptance_notification_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Extended Deadlines Section (Optional) -->
                    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-clock mr-2"></i>Extended Deadlines (Optional)
                            </h3>
                            <button type="button" id="toggle-f2f-extended-deadlines" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-plus-circle mr-1"></i>Add Extended Deadlines
                            </button>
                        </div>
                        
                        <div id="f2f-extended-deadlines-section" class="hidden space-y-4">
                            <p class="text-sm text-gray-600 mb-4">If you need to extend any deadline, fill in the fields below. Extended deadlines must be later than the original deadlines.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Extended Registration Deadline -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Extended Participant Registration:
                                    </label>
                                    <input type="datetime-local" name="f2f_extended_registration_deadline" id="f2f_extended_registration_deadline" 
                                           value="{{ old('f2f_extended_registration_deadline') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Must be after original registration deadline</p>
                                    <p id="f2f_extended_registration_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                                </div>

                                <!-- Extended Jury Registration -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Extended Jury Registration:
                                    </label>
                                    <input type="datetime-local" name="f2f_extended_jury_deadline" id="f2f_extended_jury_deadline" 
                                           value="{{ old('f2f_extended_jury_deadline') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Must be after original jury deadline</p>
                                    <p id="f2f_extended_jury_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                                </div>

                                <!-- Extended Submission Deadline -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Extended Submission Deadline:
                                    </label>
                                    <input type="datetime-local" name="f2f_extended_submission_deadline" id="f2f_extended_submission_deadline" 
                                           value="{{ old('f2f_extended_submission_deadline') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Must be after original submission deadline</p>
                                    <p id="f2f_extended_submission_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                                </div>

                                <!-- Extended Notification Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Extended Acceptance Notification:
                                    </label>
                                    <input type="datetime-local" name="f2f_extended_notification_date" id="f2f_extended_notification_date" 
                                           value="{{ old('f2f_extended_notification_date') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Must be after original notification date</p>
                                    <p id="f2f_extended_notification_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment & Event Dates Section -->
                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-calendar-alt mr-2"></i>Payment & Event Dates
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Payment Deadline -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-red-500 text-white rounded-full text-xs mr-2">5</span>
                                    Payment Deadline:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="f2f_payment_deadline_new" id="f2f_payment_deadline" 
                                       value="{{ old('f2f_payment_deadline_new', $event->f2f_payment_deadline ? $event->f2f_payment_deadline->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required>
                                <p class="text-xs text-gray-500 mt-1">Must be after all deadlines (original or extended if provided)</p>
                                <p id="f2f_payment_deadline_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Event Start Date & Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-teal-500 text-white rounded-full text-xs mr-2">6</span>
                                    On-Site Start Date & Time:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="f2f_start_datetime" id="f2f_start_date" 
                                       value="{{ old('f2f_start_datetime', $event->f2f_start_date ? $event->f2f_start_date->format('Y-m-d\TH:i') : '') }}" 
                                       class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 w-full" required>
                                <input type="hidden" name="f2f_start_date" id="f2f_start_date_hidden">
                                <input type="hidden" name="f2f_start_time" id="f2f_start_time_hidden">
                                <p class="text-xs text-gray-500 mt-1">Must be after payment deadline</p>
                                <p id="f2f_start_date_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Event End Date & Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-teal-500 text-white rounded-full text-xs mr-2">7</span>
                                    On-Site End Date & Time:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="f2f_end_datetime" id="f2f_end_date" 
                                       value="{{ old('f2f_end_datetime', $event->f2f_end_date ? $event->f2f_end_date->format('Y-m-d\TH:i') : '') }}" 
                                       class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 w-full" required>
                                <input type="hidden" name="f2f_end_date" id="f2f_end_date_hidden">
                                <input type="hidden" name="f2f_end_time" id="f2f_end_time_hidden">
                                <p class="text-xs text-gray-500 mt-1">Must be at least 2 hours after start date</p>
                                <p id="f2f_end_date_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Online Event Details (Conditional) -->
            <div id="online-section" class="mb-8 hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-purple-500">
                    <i class="fas fa-laptop mr-2"></i>Online Event Details
                </h2>
                
                <div class="space-y-6">
                    <!-- Deadlines Section -->
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-calendar-check mr-2"></i>Event Deadlines
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Participant Registration Deadline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs mr-2">1</span>
                                    Participant Registration Deadline:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="online_registration_deadline" id="online_registration_deadline" 
                                       value="{{ old('online_registration_deadline', $event->online_registration_deadline ? $event->online_registration_deadline->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Must be today or later</p>
                                <p id="online_registration_deadline_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Jury Registration Deadline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-orange-500 text-white rounded-full text-xs mr-2">2</span>
                                    Jury Registration Deadline:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="online_jury_registration_deadline_new" id="online_jury_registration_deadline" 
                                       value="{{ old('online_jury_registration_deadline_new', $event->online_jury_registration_deadline ? $event->online_jury_registration_deadline->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Must be today or later</p>
                                <p id="online_jury_deadline_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Submission Deadline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-purple-500 text-white rounded-full text-xs mr-2">3</span>
                                    Submission Deadline (Paper/Abstract):
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="online_submission_deadline" id="online_submission_deadline" 
                                       value="{{ old('online_submission_deadline', $event->online_submission_deadline ? $event->online_submission_deadline->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Must be after participant registration deadline</p>
                                <p id="online_submission_deadline_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Acceptance Notification Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white rounded-full text-xs mr-2">4</span>
                                    Acceptance Notification Date:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="online_acceptance_notification_date_new" id="online_acceptance_notification_date" 
                                       value="{{ old('online_acceptance_notification_date_new', $event->online_acceptance_notification_date ? $event->online_acceptance_notification_date->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Must be after submission deadline</p>
                                <p id="online_acceptance_notification_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Extended Deadlines Section (Optional) -->
                    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-clock mr-2"></i>Extended Deadlines (Optional)
                            </h3>
                            <button type="button" id="toggle-online-extended-deadlines" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                <i class="fas fa-plus-circle mr-1"></i>Add Extended Deadlines
                            </button>
                        </div>
                        
                        <div id="online-extended-deadlines-section" class="hidden space-y-4">
                            <p class="text-sm text-gray-600 mb-4">If you need to extend any deadline, fill in the fields below. Extended deadlines must be later than the original deadlines.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Extended Registration Deadline -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Extended Participant Registration:
                                    </label>
                                    <input type="datetime-local" name="online_extended_registration_deadline" id="online_extended_registration_deadline" 
                                           value="{{ old('online_extended_registration_deadline') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Must be after original registration deadline</p>
                                    <p id="online_extended_registration_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                                </div>

                                <!-- Extended Jury Registration -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Extended Jury Registration:
                                    </label>
                                    <input type="datetime-local" name="online_extended_jury_deadline" id="online_extended_jury_deadline" 
                                           value="{{ old('online_extended_jury_deadline') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Must be after original jury deadline</p>
                                    <p id="online_extended_jury_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                                </div>

                                <!-- Extended Submission Deadline -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Extended Submission Deadline:
                                    </label>
                                    <input type="datetime-local" name="online_extended_submission_deadline" id="online_extended_submission_deadline" 
                                           value="{{ old('online_extended_submission_deadline') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Must be after original submission deadline</p>
                                    <p id="online_extended_submission_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                                </div>

                                <!-- Extended Notification Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Extended Acceptance Notification:
                                    </label>
                                    <input type="datetime-local" name="online_extended_notification_date" id="online_extended_notification_date" 
                                           value="{{ old('online_extended_notification_date') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Must be after original notification date</p>
                                    <p id="online_extended_notification_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment, Platform & Event Dates Section -->
                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-calendar-alt mr-2"></i>Payment, Platform & Event Dates
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Payment Deadline -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-red-500 text-white rounded-full text-xs mr-2">5</span>
                                    Payment Deadline:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="online_payment_deadline_new" id="online_payment_deadline" 
                                       value="{{ old('online_payment_deadline_new', $event->online_payment_deadline ? $event->online_payment_deadline->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Must be after all deadlines (original or extended if provided)</p>
                                <p id="online_payment_deadline_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Online Platform URL -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-video mr-2"></i>Online Platform URL:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="url" name="online_platform_url" id="online_platform_url" 
                                       value="{{ old('online_platform_url', $event->online_platform_link) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" 
                                       placeholder="e.g., https://zoom.us/j/123456789">
                                <p class="text-xs text-gray-500 mt-1">Meeting link (Zoom, Teams, Google Meet, etc.)</p>
                            </div>

                            <!-- Event Start Date & Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-teal-500 text-white rounded-full text-xs mr-2">6</span>
                                    Online Start Date & Time:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="online_start_datetime" id="online_start_date" 
                                       value="{{ old('online_start_datetime', $event->online_start_date ? $event->online_start_date->format('Y-m-d\TH:i') : '') }}" 
                                       class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 w-full">
                                <input type="hidden" name="online_start_date" id="online_start_date_hidden">
                                <input type="hidden" name="online_start_time" id="online_start_time_hidden">
                                <p class="text-xs text-gray-500 mt-1">Must be after payment deadline</p>
                                <p id="online_start_date_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>

                            <!-- Event End Date & Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-teal-500 text-white rounded-full text-xs mr-2">7</span>
                                    Online End Date & Time:
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="online_end_datetime" id="online_end_date" 
                                       value="{{ old('online_end_datetime', $event->online_end_date ? $event->online_end_date->format('Y-m-d\TH:i') : '') }}" 
                                       class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 w-full">
                                <input type="hidden" name="online_end_date" id="online_end_date_hidden">
                                <input type="hidden" name="online_end_time" id="online_end_time_hidden">
                                <p class="text-xs text-gray-500 mt-1">Must be at least 2 hours after start date</p>
                                <p id="online_end_date_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 text-center border-t pt-6">
                <button type="submit" name="status" value="published" class="bg-green-600 hover:bg-green-700 text-white px-12 py-3 rounded-lg text-lg font-medium transition-colors shadow-lg">
                    <i class="fas fa-check mr-2"></i>Create Innovation Event
                </button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
(function() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeForm);
    } else {
        initializeForm();
    }
    
    function initializeForm() {
        // Image upload and preview functionality
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('featured_image');
    const uploadArea = document.getElementById('upload-area');
    const previewArea = document.getElementById('preview-area');
    const previewImage = document.getElementById('preview-image');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) handleFile(file);
    });

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
        if (file.size > 2 * 1024 * 1024) {
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

    // Delivery Mode Toggle Functionality
    const deliveryModeInputs = document.querySelectorAll('input[name="delivery_mode"]');
    const f2fSection = document.getElementById('f2f-section');
    const onlineSection = document.getElementById('online-section');
    const deliveryOptions = document.querySelectorAll('.delivery-mode-option');

    deliveryModeInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Update visual selection
            deliveryOptions.forEach(opt => {
                opt.classList.remove('border-blue-500', 'bg-blue-50');
            });
            this.closest('.delivery-mode-option').classList.add('border-blue-500', 'bg-blue-50');

            // Show/hide sections based on selection
            const mode = this.value;
            
            if (mode === 'face_to_face') {
                f2fSection.classList.remove('hidden');
                onlineSection.classList.add('hidden');
                setRequiredFields('f2f', true);
                setRequiredFields('online', false);
            } else if (mode === 'online') {
                f2fSection.classList.add('hidden');
                onlineSection.classList.remove('hidden');
                setRequiredFields('f2f', false);
                setRequiredFields('online', true);
            } else if (mode === 'hybrid') {
                f2fSection.classList.remove('hidden');
                onlineSection.classList.remove('hidden');
                setRequiredFields('f2f', true);
                setRequiredFields('online', true);
            }
        });
    });
    
    // Trigger delivery mode selection on page load if old value exists
    const checkedDeliveryMode = document.querySelector('input[name="delivery_mode"]:checked');
    if (checkedDeliveryMode) {
        checkedDeliveryMode.dispatchEvent(new Event('change'));
    }

    function setRequiredFields(type, isRequired) {
        const prefix = type === 'f2f' ? 'f2f' : 'online';
        
        // Define all required fields for each type
        let fieldNames = [];
        
        if (type === 'f2f') {
            fieldNames = [
                'f2f_participant_registration_deadline',
                'f2f_jury_registration_deadline',
                'f2f_submission_deadline',
                'f2f_acceptance_notification_date',
                'f2f_payment_deadline_new',
                'f2f_start_date',
                'f2f_end_date',
                'f2f_start_time',
                'f2f_end_time',
                'venue',
                'city',
                'country'
            ];
        } else if (type === 'online') {
            fieldNames = [
                'online_registration_deadline',
                'online_jury_registration_deadline_new',
                'online_submission_deadline',
                'online_acceptance_notification_date_new',
                'online_payment_deadline_new',
                'online_platform_url',
                'online_start_datetime',
                'online_end_datetime'
            ];
        }
        
        fieldNames.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                if (isRequired) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            }
        });
    }

    // Date validation
    const today = new Date().toISOString().slice(0, 10);
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.min = today;
    });

    // On-Site date sync and validation
    const f2fStartDate = document.querySelector('input[name="f2f_start_date"]');
    const f2fEndDate = document.querySelector('input[name="f2f_end_date"]');
    const f2fStartTime = document.getElementById('f2f_start_time');
    const f2fEndTime = document.getElementById('f2f_end_time');
    
    function validateF2FEndDateTime() {
        if (f2fStartDate && f2fEndDate && f2fStartTime && f2fEndTime && 
            f2fStartDate.value && f2fEndDate.value && f2fStartTime.value && f2fEndTime.value) {
            
            const startDateTime = new Date(f2fStartDate.value + 'T' + f2fStartTime.value);
            const endDateTime = new Date(f2fEndDate.value + 'T' + f2fEndTime.value);
            const f2fEndWarning = document.getElementById('f2f_end_date_warning');
            
            const timeDiff = (endDateTime - startDateTime) / (1000 * 60 * 60); // difference in hours
            
            if (timeDiff < 2) {
                f2fEndWarning.textContent = '⚠️ Event end time must be at least 2 hours after start time';
                f2fEndWarning.classList.remove('hidden');
            } else {
                f2fEndWarning.classList.add('hidden');
            }
        }
    }
    
    if (f2fStartDate) {
        f2fStartDate.addEventListener('change', function() {
            if (f2fEndDate) {
                f2fEndDate.min = this.value;
                if (f2fEndDate.value && f2fEndDate.value < this.value) {
                    f2fEndDate.value = this.value;
                }
            }
            validateF2FEndDateTime();
        });
    }
    
    if (f2fEndDate) f2fEndDate.addEventListener('change', validateF2FEndDateTime);
    if (f2fStartTime) f2fStartTime.addEventListener('change', validateF2FEndDateTime);
    if (f2fEndTime) f2fEndTime.addEventListener('change', validateF2FEndDateTime);

    // Online date sync and validation
    const onlineStartDate = document.querySelector('input[name="online_start_date"]');
    const onlineEndDate = document.querySelector('input[name="online_end_date"]');
    const onlineStartTime = document.getElementById('online_start_time');
    const onlineEndTime = document.getElementById('online_end_time');
    
    function validateOnlineEndDateTime() {
        if (onlineStartDate && onlineEndDate && onlineStartTime && onlineEndTime && 
            onlineStartDate.value && onlineEndDate.value && onlineStartTime.value && onlineEndTime.value) {
            
            const startDateTime = new Date(onlineStartDate.value + 'T' + onlineStartTime.value);
            const endDateTime = new Date(onlineEndDate.value + 'T' + onlineEndTime.value);
            const onlineEndWarning = document.getElementById('online_end_date_warning');
            
            const timeDiff = (endDateTime - startDateTime) / (1000 * 60 * 60); // difference in hours
            
            if (timeDiff < 2) {
                onlineEndWarning.textContent = '⚠️ Event end time must be at least 2 hours after start time';
                onlineEndWarning.classList.remove('hidden');
            } else {
                onlineEndWarning.classList.add('hidden');
            }
        }
    }
    
    if (onlineStartDate) {
        onlineStartDate.addEventListener('change', function() {
            if (onlineEndDate) {
                onlineEndDate.min = this.value;
                if (onlineEndDate.value && onlineEndDate.value < this.value) {
                    onlineEndDate.value = this.value;
                }
            }
            validateOnlineEndDateTime();
        });
    }
    
    if (onlineEndDate) onlineEndDate.addEventListener('change', validateOnlineEndDateTime);
    if (onlineStartTime) onlineStartTime.addEventListener('change', validateOnlineEndDateTime);
    if (onlineEndTime) onlineEndTime.addEventListener('change', validateOnlineEndDateTime);

    // Innovation Categories Management
    const categoriesContainer = document.getElementById('categories-container');
    const addCategoryBtn = document.getElementById('add-category');
    let categoryCount = 1;

    console.log('Categories Container:', categoriesContainer);
    console.log('Add Category Button:', addCategoryBtn);

    if (addCategoryBtn) {
        addCategoryBtn.addEventListener('click', function(e) {
            console.log('Add Category button clicked!');
            e.preventDefault();
            e.stopPropagation();
            categoryCount++;
            const categoryItem = document.createElement('div');
            categoryItem.className = 'category-item flex items-center gap-2';
            categoryItem.innerHTML = `
                <input type="text" name="innovation_categories[]" class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="e.g., Secondary School, Vocational" required>
                <button type="button" class="remove-category px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            categoriesContainer.appendChild(categoryItem);
            updateRemoveButtons();
        });
    }

    // Event delegation for remove buttons
    if (categoriesContainer) {
        categoriesContainer.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-category');
            if (removeBtn) {
                const categoryItem = removeBtn.closest('.category-item');
                categoryItem.remove();
                categoryCount--;
                updateRemoveButtons();
            }
        });
    }

    function updateRemoveButtons() {
        const items = categoriesContainer.querySelectorAll('.category-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-category');
            if (items.length === 1) {
                removeBtn.classList.add('hidden');
            } else {
                removeBtn.classList.remove('hidden');
            }
        });
    }

    // Initialize remove buttons
    updateRemoveButtons();

    // Innovation Themes Management
    const themesContainer = document.getElementById('themes-container');
    const addThemeBtn = document.getElementById('add-theme');
    let themeCount = 1;

    console.log('Themes Container:', themesContainer);
    console.log('Add Theme Button:', addThemeBtn);

    if (addThemeBtn) {
        addThemeBtn.addEventListener('click', function(e) {
            console.log('Add Theme button clicked!');
            e.preventDefault();
            e.stopPropagation();
            themeCount++;
            const themeItem = document.createElement('div');
            themeItem.className = 'theme-item flex items-center gap-2';
            themeItem.innerHTML = `
                <input type="text" name="innovation_theme[]" class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" placeholder="e.g., Green Technology, Smart Cities" required>
                <button type="button" class="remove-theme px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            themesContainer.appendChild(themeItem);
            updateThemeRemoveButtons();
        });
    }

    // Event delegation for remove theme buttons
    if (themesContainer) {
        themesContainer.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-theme');
            if (removeBtn) {
                const themeItem = removeBtn.closest('.theme-item');
                themeItem.remove();
                themeCount--;
                updateThemeRemoveButtons();
            }
        });
    }

    function updateThemeRemoveButtons() {
        const items = themesContainer.querySelectorAll('.theme-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-theme');
            if (items.length === 1) {
                removeBtn.classList.add('hidden');
            } else {
                removeBtn.classList.remove('hidden');
            }
        });
    }

    // Initialize theme remove buttons
    updateThemeRemoveButtons();

    // Date Validation Logic for New Innovation Date Structure
    const registrationDeadline = document.getElementById('registration_deadline');
    const paymentDeadline = document.getElementById('payment_deadline');
    
    // F2F date fields
    const f2fRegistrationDeadline = document.getElementById('f2f_registration_deadline');
    const f2fJuryDeadline = document.getElementById('f2f_jury_registration_deadline');
    const f2fSubmissionDeadline = document.getElementById('f2f_submission_deadline');
    const f2fAcceptanceNotification = document.getElementById('f2f_acceptance_notification_date');
    const f2fPaymentDeadline = document.getElementById('f2f_payment_deadline');
    const f2fExtendedRegistration = document.getElementById('f2f_extended_registration_deadline');
    const f2fExtendedJury = document.getElementById('f2f_extended_jury_deadline');
    const f2fExtendedSubmission = document.getElementById('f2f_extended_submission_deadline');
    const f2fExtendedNotification = document.getElementById('f2f_extended_notification_date');
    // f2fStartDate and f2fEndDate already declared above

    // Toggle extended deadlines section
    const toggleF2fExtended = document.getElementById('toggle-f2f-extended-deadlines');
    const f2fExtendedSection = document.getElementById('f2f-extended-deadlines-section');
    
    if (toggleF2fExtended) {
        toggleF2fExtended.addEventListener('click', function() {
            f2fExtendedSection.classList.toggle('hidden');
            const icon = this.querySelector('i');
            if (f2fExtendedSection.classList.contains('hidden')) {
                icon.className = 'fas fa-plus-circle mr-1';
                this.innerHTML = '<i class="fas fa-plus-circle mr-1"></i>Add Extended Deadlines';
            } else {
                icon.className = 'fas fa-minus-circle mr-1';
                this.innerHTML = '<i class="fas fa-minus-circle mr-1"></i>Hide Extended Deadlines';
            }
        });
    }

    // Online date fields
    const onlineRegistrationDeadline = document.getElementById('online_registration_deadline');
    const onlineJuryDeadline = document.getElementById('online_jury_registration_deadline');
    const onlineSubmissionDeadline = document.getElementById('online_submission_deadline');
    const onlineAcceptanceNotification = document.getElementById('online_acceptance_notification_date');
    const onlinePaymentDeadline = document.getElementById('online_payment_deadline');
    const onlineExtendedRegistration = document.getElementById('online_extended_registration_deadline');
    const onlineExtendedJury = document.getElementById('online_extended_jury_deadline');
    const onlineExtendedSubmission = document.getElementById('online_extended_submission_deadline');
    const onlineExtendedNotification = document.getElementById('online_extended_notification_date');
    // onlineStartDate and onlineEndDate already declared above

    // Toggle online extended deadlines section
    const toggleOnlineExtended = document.getElementById('toggle-online-extended-deadlines');
    const onlineExtendedSection = document.getElementById('online-extended-deadlines-section');
    
    if (toggleOnlineExtended) {
        toggleOnlineExtended.addEventListener('click', function() {
            onlineExtendedSection.classList.toggle('hidden');
            const icon = this.querySelector('i');
            if (onlineExtendedSection.classList.contains('hidden')) {
                icon.className = 'fas fa-plus-circle mr-1';
                this.innerHTML = '<i class="fas fa-plus-circle mr-1"></i>Add Extended Deadlines';
            } else {
                icon.className = 'fas fa-minus-circle mr-1';
                this.innerHTML = '<i class="fas fa-minus-circle mr-1"></i>Hide Extended Deadlines';
            }
        });
    }

    function validateDates() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // ===== FACE-TO-FACE DATE VALIDATION =====
        
        // 1. F2F Registration Deadline >= Today
        if (f2fRegistrationDeadline && f2fRegistrationDeadline.value) {
            const regDate = new Date(f2fRegistrationDeadline.value);
            const warning = document.getElementById('f2f_registration_deadline_warning');
            if (regDate < today) {
                warning.textContent = '⚠️ Registration deadline must be today or later';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 2. F2F Jury Registration Deadline >= Today
        if (f2fJuryDeadline && f2fJuryDeadline.value) {
            const juryDate = new Date(f2fJuryDeadline.value);
            const warning = document.getElementById('f2f_jury_deadline_warning');
            if (juryDate < today) {
                warning.textContent = '⚠️ Jury registration deadline must be today or later';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 3. F2F Submission Deadline > Registration Deadline
        if (f2fSubmissionDeadline && f2fSubmissionDeadline.value && f2fRegistrationDeadline && f2fRegistrationDeadline.value) {
            const submissionDate = new Date(f2fSubmissionDeadline.value);
            const regDate = new Date(f2fRegistrationDeadline.value);
            const warning = document.getElementById('f2f_submission_deadline_warning');
            if (submissionDate <= regDate) {
                warning.textContent = '⚠️ Submission deadline must be after participant registration deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 4. F2F Acceptance Notification > Submission Deadline
        if (f2fAcceptanceNotification && f2fAcceptanceNotification.value && f2fSubmissionDeadline && f2fSubmissionDeadline.value) {
            const acceptDate = new Date(f2fAcceptanceNotification.value);
            const submissionDate = new Date(f2fSubmissionDeadline.value);
            const warning = document.getElementById('f2f_acceptance_notification_warning');
            if (acceptDate <= submissionDate) {
                warning.textContent = '⚠️ Acceptance notification must be after submission deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 5. Extended Deadlines Validation (if provided)
        if (f2fExtendedRegistration && f2fExtendedRegistration.value && f2fRegistrationDeadline && f2fRegistrationDeadline.value) {
            const extReg = new Date(f2fExtendedRegistration.value);
            const origReg = new Date(f2fRegistrationDeadline.value);
            const warning = document.getElementById('f2f_extended_registration_warning');
            if (extReg <= origReg) {
                warning.textContent = '⚠️ Extended deadline must be after original registration deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        if (f2fExtendedJury && f2fExtendedJury.value && f2fJuryDeadline && f2fJuryDeadline.value) {
            const extJury = new Date(f2fExtendedJury.value);
            const origJury = new Date(f2fJuryDeadline.value);
            const warning = document.getElementById('f2f_extended_jury_warning');
            if (extJury <= origJury) {
                warning.textContent = '⚠️ Extended deadline must be after original jury deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        if (f2fExtendedSubmission && f2fExtendedSubmission.value && f2fSubmissionDeadline && f2fSubmissionDeadline.value) {
            const extSub = new Date(f2fExtendedSubmission.value);
            const origSub = new Date(f2fSubmissionDeadline.value);
            const warning = document.getElementById('f2f_extended_submission_warning');
            if (extSub <= origSub) {
                warning.textContent = '⚠️ Extended deadline must be after original submission deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        if (f2fExtendedNotification && f2fExtendedNotification.value && f2fAcceptanceNotification && f2fAcceptanceNotification.value) {
            const extNot = new Date(f2fExtendedNotification.value);
            const origNot = new Date(f2fAcceptanceNotification.value);
            const warning = document.getElementById('f2f_extended_notification_warning');
            if (extNot <= origNot) {
                warning.textContent = '⚠️ Extended notification must be after original notification date';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 6. F2F Payment Deadline > All Deadlines (use extended if provided, otherwise original)
        if (f2fPaymentDeadline && f2fPaymentDeadline.value) {
            const paymentDate = new Date(f2fPaymentDeadline.value);
            const warning = document.getElementById('f2f_payment_deadline_warning');
            let latestDeadline = null;
            
            // Determine the latest deadline to check against
            const regDeadline = f2fExtendedRegistration && f2fExtendedRegistration.value 
                ? new Date(f2fExtendedRegistration.value) 
                : (f2fRegistrationDeadline && f2fRegistrationDeadline.value ? new Date(f2fRegistrationDeadline.value) : null);
            
            const juryDeadline = f2fExtendedJury && f2fExtendedJury.value 
                ? new Date(f2fExtendedJury.value) 
                : (f2fJuryDeadline && f2fJuryDeadline.value ? new Date(f2fJuryDeadline.value) : null);
            
            const subDeadline = f2fExtendedSubmission && f2fExtendedSubmission.value 
                ? new Date(f2fExtendedSubmission.value) 
                : (f2fSubmissionDeadline && f2fSubmissionDeadline.value ? new Date(f2fSubmissionDeadline.value) : null);
            
            const notDate = f2fExtendedNotification && f2fExtendedNotification.value 
                ? new Date(f2fExtendedNotification.value) 
                : (f2fAcceptanceNotification && f2fAcceptanceNotification.value ? new Date(f2fAcceptanceNotification.value) : null);
            
            // Find the latest of all deadlines
            [regDeadline, juryDeadline, subDeadline, notDate].forEach(date => {
                if (date && (!latestDeadline || date > latestDeadline)) {
                    latestDeadline = date;
                }
            });
            
            if (latestDeadline && paymentDate <= latestDeadline) {
                warning.textContent = '⚠️ Payment deadline must be after all other deadlines';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 7. F2F Event Start > Payment Deadline
        if (f2fStartDate && f2fStartDate.value && f2fPaymentDeadline && f2fPaymentDeadline.value) {
            const startDate = new Date(f2fStartDate.value);
            const paymentDate = new Date(f2fPaymentDeadline.value);
            const warning = document.getElementById('f2f_start_date_warning');
            if (startDate <= paymentDate) {
                warning.textContent = '⚠️ Event start must be after payment deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 8. F2F Event End >= Event Start + 2 hours
        if (f2fEndDate && f2fEndDate.value && f2fStartDate && f2fStartDate.value) {
            const endDate = new Date(f2fEndDate.value);
            const startDate = new Date(f2fStartDate.value);
            const minEndDate = new Date(startDate.getTime() + (2 * 60 * 60 * 1000)); // Add 2 hours
            const warning = document.getElementById('f2f_end_date_warning');
            if (endDate < minEndDate) {
                warning.textContent = '⚠️ Event must last at least 2 hours';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // ===== ONLINE DATE VALIDATION =====
        
        // 1. Online Registration Deadline >= Today
        if (onlineRegistrationDeadline && onlineRegistrationDeadline.value) {
            const regDate = new Date(onlineRegistrationDeadline.value);
            const warning = document.getElementById('online_registration_deadline_warning');
            if (regDate < today) {
                warning.textContent = '⚠️ Registration deadline must be today or later';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 2. Online Jury Registration Deadline >= Today
        if (onlineJuryDeadline && onlineJuryDeadline.value) {
            const juryDate = new Date(onlineJuryDeadline.value);
            const warning = document.getElementById('online_jury_deadline_warning');
            if (juryDate < today) {
                warning.textContent = '⚠️ Jury registration deadline must be today or later';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 3. Online Submission Deadline > Registration Deadline
        if (onlineSubmissionDeadline && onlineSubmissionDeadline.value && onlineRegistrationDeadline && onlineRegistrationDeadline.value) {
            const submissionDate = new Date(onlineSubmissionDeadline.value);
            const regDate = new Date(onlineRegistrationDeadline.value);
            const warning = document.getElementById('online_submission_deadline_warning');
            if (submissionDate <= regDate) {
                warning.textContent = '⚠️ Submission deadline must be after participant registration deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 4. Online Acceptance Notification > Submission Deadline
        if (onlineAcceptanceNotification && onlineAcceptanceNotification.value && onlineSubmissionDeadline && onlineSubmissionDeadline.value) {
            const acceptDate = new Date(onlineAcceptanceNotification.value);
            const submissionDate = new Date(onlineSubmissionDeadline.value);
            const warning = document.getElementById('online_acceptance_notification_warning');
            if (acceptDate <= submissionDate) {
                warning.textContent = '⚠️ Acceptance notification must be after submission deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 5. Extended Deadlines Validation (if provided)
        if (onlineExtendedRegistration && onlineExtendedRegistration.value && onlineRegistrationDeadline && onlineRegistrationDeadline.value) {
            const extReg = new Date(onlineExtendedRegistration.value);
            const origReg = new Date(onlineRegistrationDeadline.value);
            const warning = document.getElementById('online_extended_registration_warning');
            if (extReg <= origReg) {
                warning.textContent = '⚠️ Extended deadline must be after original registration deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        if (onlineExtendedJury && onlineExtendedJury.value && onlineJuryDeadline && onlineJuryDeadline.value) {
            const extJury = new Date(onlineExtendedJury.value);
            const origJury = new Date(onlineJuryDeadline.value);
            const warning = document.getElementById('online_extended_jury_warning');
            if (extJury <= origJury) {
                warning.textContent = '⚠️ Extended deadline must be after original jury deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        if (onlineExtendedSubmission && onlineExtendedSubmission.value && onlineSubmissionDeadline && onlineSubmissionDeadline.value) {
            const extSub = new Date(onlineExtendedSubmission.value);
            const origSub = new Date(onlineSubmissionDeadline.value);
            const warning = document.getElementById('online_extended_submission_warning');
            if (extSub <= origSub) {
                warning.textContent = '⚠️ Extended deadline must be after original submission deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        if (onlineExtendedNotification && onlineExtendedNotification.value && onlineAcceptanceNotification && onlineAcceptanceNotification.value) {
            const extNot = new Date(onlineExtendedNotification.value);
            const origNot = new Date(onlineAcceptanceNotification.value);
            const warning = document.getElementById('online_extended_notification_warning');
            if (extNot <= origNot) {
                warning.textContent = '⚠️ Extended notification must be after original notification date';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 6. Online Payment Deadline > All Deadlines (use extended if provided, otherwise original)
        if (onlinePaymentDeadline && onlinePaymentDeadline.value) {
            const paymentDate = new Date(onlinePaymentDeadline.value);
            const warning = document.getElementById('online_payment_deadline_warning');
            let latestDeadline = null;
            
            // Determine the latest deadline to check against
            const regDeadline = onlineExtendedRegistration && onlineExtendedRegistration.value 
                ? new Date(onlineExtendedRegistration.value) 
                : (onlineRegistrationDeadline && onlineRegistrationDeadline.value ? new Date(onlineRegistrationDeadline.value) : null);
            
            const juryDeadline = onlineExtendedJury && onlineExtendedJury.value 
                ? new Date(onlineExtendedJury.value) 
                : (onlineJuryDeadline && onlineJuryDeadline.value ? new Date(onlineJuryDeadline.value) : null);
            
            const subDeadline = onlineExtendedSubmission && onlineExtendedSubmission.value 
                ? new Date(onlineExtendedSubmission.value) 
                : (onlineSubmissionDeadline && onlineSubmissionDeadline.value ? new Date(onlineSubmissionDeadline.value) : null);
            
            const notDate = onlineExtendedNotification && onlineExtendedNotification.value 
                ? new Date(onlineExtendedNotification.value) 
                : (onlineAcceptanceNotification && onlineAcceptanceNotification.value ? new Date(onlineAcceptanceNotification.value) : null);
            
            // Find the latest of all deadlines
            [regDeadline, juryDeadline, subDeadline, notDate].forEach(date => {
                if (date && (!latestDeadline || date > latestDeadline)) {
                    latestDeadline = date;
                }
            });
            
            if (latestDeadline && paymentDate <= latestDeadline) {
                warning.textContent = '⚠️ Payment deadline must be after all other deadlines';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 7. Online Event Start > Payment Deadline
        if (onlineStartDate && onlineStartDate.value && onlinePaymentDeadline && onlinePaymentDeadline.value) {
            const startDate = new Date(onlineStartDate.value);
            const paymentDate = new Date(onlinePaymentDeadline.value);
            const warning = document.getElementById('online_start_date_warning');
            if (startDate <= paymentDate) {
                warning.textContent = '⚠️ Event start must be after payment deadline';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
        
        // 8. Online Event End >= Event Start + 2 hours
        if (onlineEndDate && onlineEndDate.value && onlineStartDate && onlineStartDate.value) {
            const endDate = new Date(onlineEndDate.value);
            const startDate = new Date(onlineStartDate.value);
            const minEndDate = new Date(startDate.getTime() + (2 * 60 * 60 * 1000)); // Add 2 hours
            const warning = document.getElementById('online_end_date_warning');
            if (endDate < minEndDate) {
                warning.textContent = '⚠️ Event must last at least 2 hours';
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
    }

    // Add event listeners for F2F date validation
    if (f2fRegistrationDeadline) f2fRegistrationDeadline.addEventListener('change', validateDates);
    if (f2fJuryDeadline) f2fJuryDeadline.addEventListener('change', validateDates);
    if (f2fSubmissionDeadline) f2fSubmissionDeadline.addEventListener('change', validateDates);
    if (f2fAcceptanceNotification) f2fAcceptanceNotification.addEventListener('change', validateDates);
    if (f2fPaymentDeadline) f2fPaymentDeadline.addEventListener('change', validateDates);
    if (f2fExtendedRegistration) f2fExtendedRegistration.addEventListener('change', validateDates);
    if (f2fExtendedJury) f2fExtendedJury.addEventListener('change', validateDates);
    if (f2fExtendedSubmission) f2fExtendedSubmission.addEventListener('change', validateDates);
    if (f2fExtendedNotification) f2fExtendedNotification.addEventListener('change', validateDates);
    if (f2fStartDate) f2fStartDate.addEventListener('change', validateDates);
    if (f2fEndDate) f2fEndDate.addEventListener('change', validateDates);

    // Add event listeners for Online date validation
    if (onlineRegistrationDeadline) onlineRegistrationDeadline.addEventListener('change', validateDates);
    if (onlineJuryDeadline) onlineJuryDeadline.addEventListener('change', validateDates);
    if (onlineSubmissionDeadline) onlineSubmissionDeadline.addEventListener('change', validateDates);
    if (onlineAcceptanceNotification) onlineAcceptanceNotification.addEventListener('change', validateDates);
    if (onlinePaymentDeadline) onlinePaymentDeadline.addEventListener('change', validateDates);
    if (onlineExtendedRegistration) onlineExtendedRegistration.addEventListener('change', validateDates);
    if (onlineExtendedJury) onlineExtendedJury.addEventListener('change', validateDates);
    if (onlineExtendedSubmission) onlineExtendedSubmission.addEventListener('change', validateDates);
    if (onlineExtendedNotification) onlineExtendedNotification.addEventListener('change', validateDates);
    if (onlineStartDate) onlineStartDate.addEventListener('change', validateDates);
    if (onlineEndDate) onlineEndDate.addEventListener('change', validateDates);
    if (f2fEndDate) f2fEndDate.addEventListener('change', validateDates);

    // Form submission debugging
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            
            // Check form validity
            if (!form.checkValidity()) {
                console.error('Form validation failed');
                // Find invalid fields
                const invalidFields = form.querySelectorAll(':invalid');
                console.log('Invalid fields:', invalidFields);
                
                let errorMessages = [];
                invalidFields.forEach(field => {
                    console.log('Invalid field:', field.name, field.validationMessage);
                    const label = field.labels && field.labels[0] ? field.labels[0].textContent : field.name;
                    errorMessages.push(`${label}: ${field.validationMessage}`);
                });
                
                e.preventDefault();
                
                // Show alert with missing fields
                alert('Please fill in all required fields:\n\n' + errorMessages.join('\n'));
                
                // Scroll to first invalid field
                if (invalidFields.length > 0) {
                    invalidFields[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => {
                        invalidFields[0].focus();
                    }, 500);
                }
                return false;
            }
            
            console.log('Form is valid, submitting...');
        });
        
        // Log when form button is clicked
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                console.log('Submit button clicked');
            });
        }
    }

    // ===== SPLIT DATETIME FIELDS FOR BACKWARD COMPATIBILITY =====
    // This code splits the datetime-local inputs into separate date and time fields
    // for backward compatibility with the database schema
    
    // Function to split datetime-local value into date and time
    function splitDateTime(datetimeValue) {
        if (!datetimeValue) return { date: '', time: '' };
        const parts = datetimeValue.split('T');
        return {
            date: parts[0] || '',
            time: parts[1] || ''
        };
    }
    
    // Function to populate hidden date/time fields from datetime-local inputs
    function populateDateTimeFields() {
        // Split F2F datetime fields
        const f2fStartDatetime = document.querySelector('input[name="f2f_start_datetime"]');
        const f2fEndDatetime = document.querySelector('input[name="f2f_end_datetime"]');
        
        if (f2fStartDatetime && f2fStartDatetime.value) {
            const f2fStart = splitDateTime(f2fStartDatetime.value);
            const f2fStartDateField = document.querySelector('input[name="f2f_start_date"]');
            const f2fStartTimeField = document.querySelector('input[name="f2f_start_time"]');
            if (f2fStartDateField) f2fStartDateField.value = f2fStart.date;
            if (f2fStartTimeField) f2fStartTimeField.value = f2fStart.time;
        }
        
        if (f2fEndDatetime && f2fEndDatetime.value) {
            const f2fEnd = splitDateTime(f2fEndDatetime.value);
            const f2fEndDateField = document.querySelector('input[name="f2f_end_date"]');
            const f2fEndTimeField = document.querySelector('input[name="f2f_end_time"]');
            if (f2fEndDateField) f2fEndDateField.value = f2fEnd.date;
            if (f2fEndTimeField) f2fEndTimeField.value = f2fEnd.time;
        }
        
        // Split Online datetime fields
        const onlineStartDatetime = document.querySelector('input[name="online_start_datetime"]');
        const onlineEndDatetime = document.querySelector('input[name="online_end_datetime"]');
        
        if (onlineStartDatetime && onlineStartDatetime.value) {
            const onlineStart = splitDateTime(onlineStartDatetime.value);
            const onlineStartDateField = document.querySelector('input[name="online_start_date"]');
            const onlineStartTimeField = document.querySelector('input[name="online_start_time"]');
            if (onlineStartDateField) onlineStartDateField.value = onlineStart.date;
            if (onlineStartTimeField) onlineStartTimeField.value = onlineStart.time;
        }
        
        if (onlineEndDatetime && onlineEndDatetime.value) {
            const onlineEnd = splitDateTime(onlineEndDatetime.value);
            const onlineEndDateField = document.querySelector('input[name="online_end_date"]');
            const onlineEndTimeField = document.querySelector('input[name="online_end_time"]');
            if (onlineEndDateField) onlineEndDateField.value = onlineEnd.date;
            if (onlineEndTimeField) onlineEndTimeField.value = onlineEnd.time;
        }
    }
    
    // Call this function when datetime fields change and before form submission
    const datetimeFields = document.querySelectorAll('input[type="datetime-local"]');
    datetimeFields.forEach(field => {
        field.addEventListener('change', populateDateTimeFields);
    });
    
    // Also call when form is about to be submitted (before validation)
    if (form) {
        // Use capture phase to run before other handlers
        form.addEventListener('submit', populateDateTimeFields, true);
    }
}
})();
</script>
@endsection
