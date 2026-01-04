@extends('organizer.layouts.app')

@section('title', 'Edit Academic Conference Event')
@section('page-title', 'Edit Academic Conference Event')

@section('content')
<style>
    /* Ensure no sticky buttons */
    button[type="submit"] {
        position: relative !important;
    }
</style>
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
            <input type="hidden" name="event_form_type" value="conference">
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="font-bold text-red-700 mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Section 1: Basic Event Information -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-purple-500">
                    <i class="fas fa-info-circle mr-2"></i>Basic Event Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Title: <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" placeholder="e.g., International Conference on AI & Machine Learning 2025" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category: <span class="text-red-500">*</span></label>
                            <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Description: <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500 resize-none" placeholder="Describe your academic conference, topics, objectives, and expected outcomes..." required>{{ old('description', $event->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Conference Topics/Categories: <span class="text-red-500">*</span></label>
                            <div id="categories-container" class="space-y-2">
                                @if(old('conference_categories', $event->conference_categories) && count(old('conference_categories', $event->conference_categories)) > 0)
                                    @foreach(old('conference_categories', $event->conference_categories) as $index => $category)
                                        <div class="category-item flex items-center gap-2">
                                            <input type="text" name="conference_categories[]" value="{{ $category }}" class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" placeholder="e.g., Artificial Intelligence, Machine Learning" required>
                                            <button type="button" class="remove-category {{ count(old('conference_categories', $event->conference_categories)) == 1 ? 'hidden' : '' }} px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="category-item flex items-center gap-2">
                                        <input type="text" name="conference_categories[]" value="" class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" placeholder="e.g., Artificial Intelligence, Machine Learning" required>
                                        <button type="button" class="remove-category hidden px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" id="add-category" class="mt-2 px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors text-sm">
                                <i class="fas fa-plus mr-1"></i> Add Another Topic
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Add conference topics/tracks (e.g., AI, IoT, Blockchain, Sustainability). Participants will select relevant tracks when submitting papers.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Location, Price & Participants -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-purple-500">
                    <i class="fas fa-map-marker-alt mr-2"></i>Location, Price & Participants
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue Name: <span class="text-red-500">*</span></label>
                            <input type="text" name="venue_name" value="{{ old('venue_name', $event->venue_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" placeholder="Enter venue/university name" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue Address: <span class="text-red-500">*</span></label>
                            <textarea name="venue_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500 resize-none" placeholder="Enter complete venue address" required>{{ old('venue_address', $event->venue_address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City: <span class="text-red-500">*</span></label>
                                <input type="text" name="city" value="{{ old('city', $event->city) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" placeholder="Enter city" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country: <span class="text-red-500">*</span></label>
                                <input type="text" name="country" value="{{ old('country', $event->country) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" placeholder="Enter country" required>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Participants: <span class="text-red-500">*</span></label>
                            <input type="number" name="max_participants" value="{{ old('max_participants', $event->max_participants) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" placeholder="Enter maximum number of participants" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Registration Fee (RM): <span class="text-red-500">*</span></label>
                            <input type="number" name="registration_fee" value="{{ old('registration_fee', $event->registration_fee ?? '0') }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" required>
                            <p class="text-xs text-gray-500 mt-1">Enter 0 for free events</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status: <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" required>
                                <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Poster / Banner:</label>
                    
                    @if($event->featured_image)
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Current Poster:</p>
                        <img src="{{ str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . $event->featured_image) }}" alt="Current Poster" class="max-w-xs h-32 object-cover rounded-lg border-2 border-gray-300">
                    </div>
                    @endif
                    
                    <label for="featured_image" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-purple-400 transition-colors cursor-pointer block" id="drop-zone">
                        <div id="upload-area" class="space-y-3">
                            <div class="mx-auto w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">
                                    <span class="text-purple-600 hover:text-purple-700 font-medium">
                                        {{ $event->featured_image ? 'Click to upload new poster' : 'Click to upload' }}
                                    </span>
                                    or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Recommended: 1920×1080px (16:9) or 1200×630px • JPG/PNG • Max 2MB
                                </p>
                            </div>
                        </div>
                        
                        <div id="preview-area" class="hidden">
                            <img id="preview-image" src="" alt="Preview" class="max-w-full h-32 object-cover rounded-lg mx-auto">
                            <p class="text-sm text-green-600 mt-2">
                                <i class="fas fa-check-circle mr-1"></i>
                                New image ready for upload
                            </p>
                        </div>
                        
                        <input type="file" id="featured_image" name="featured_image" accept="image/*" class="hidden">
                    </label>
                </div>
            </div>

            <!-- Section 3: Delivery Mode Selection -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-purple-500">
                    Event Delivery Mode
                </h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Delivery Mode: <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 transition-colors delivery-mode-option">
                            <input type="radio" name="delivery_mode" value="face_to_face" class="mr-3" {{ old('delivery_mode', $event->delivery_mode) == 'face_to_face' ? 'checked' : '' }} required>
                            <div>
                                <div class="font-semibold text-gray-900">On-Site Only</div>
                                <div class="text-xs text-gray-500">Physical event at venue</div>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 transition-colors delivery-mode-option">
                            <input type="radio" name="delivery_mode" value="online" class="mr-3" {{ old('delivery_mode', $event->delivery_mode) == 'online' ? 'checked' : '' }} required>
                            <div>
                                <div class="font-semibold text-gray-900">Online Only</div>
                                <div class="text-xs text-gray-500">Virtual event</div>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 transition-colors delivery-mode-option">
                            <input type="radio" name="delivery_mode" value="hybrid" class="mr-3" {{ old('delivery_mode', $event->delivery_mode) == 'hybrid' ? 'checked' : '' }} required>
                            <div>
                                <div class="font-semibold text-gray-900">Hybrid (Both)</div>
                                <div class="text-xs text-gray-500">On-Site + Online with different dates</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 4: On-Site Details -->
            <div id="f2f-section" class="mb-8" style="display: {{ old('delivery_mode', $event->delivery_mode) == 'face_to_face' || old('delivery_mode', $event->delivery_mode) == 'hybrid' ? 'block' : 'none' }};">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-blue-500">
                    <i class="fas fa-calendar-alt mr-2"></i>On-Site Conference Details
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-purple-500 text-white rounded-full text-xs mr-2">1</span>
                                Reviewer Registration Deadline (On-Site):
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_reviewer_registration_deadline" 
                                   id="f2f_reviewer_registration_deadline"
                                   value="{{ old('f2f_reviewer_registration_deadline', $event->f2f_reviewer_registration_deadline ? \Carbon\Carbon::parse($event->f2f_reviewer_registration_deadline)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Last date for reviewers to register for on-site event</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs mr-2">2</span>
                                Paper Submission Deadline (On-Site):
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_paper_submission_deadline" 
                                   id="f2f_paper_submission_deadline"
                                   value="{{ old('f2f_paper_submission_deadline', $event->f2f_paper_submission_deadline ? \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Due date for on-site participants to submit papers</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs mr-2">3</span>
                                Review/Evaluation Deadline (On-Site):
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_review_deadline" 
                                   id="f2f_review_deadline"
                                   value="{{ old('f2f_review_deadline', $event->f2f_review_deadline ? \Carbon\Carbon::parse($event->f2f_review_deadline)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">When reviewers must complete their evaluations</p>
                            <p id="f2f_review_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs mr-2">4</span>
                                Acceptance Notification Date (On-Site):
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_acceptance_notification_date" 
                                   id="f2f_acceptance_notification_date"
                                   value="{{ old('f2f_acceptance_notification_date', $event->f2f_acceptance_notification_date ? \Carbon\Carbon::parse($event->f2f_acceptance_notification_date)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">When on-site participants will be notified of acceptance</p>
                            <p id="f2f_acceptance_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-orange-500 text-white rounded-full text-xs mr-2">5</span>
                                Payment Deadline (On-Site):
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_payment_deadline" 
                                   id="f2f_payment_deadline"
                                   value="{{ old('f2f_payment_deadline', $event->f2f_payment_deadline ? \Carbon\Carbon::parse($event->f2f_payment_deadline)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Deadline for accepted participants to pay (after acceptance, before event)</p>
                            <p id="f2f_payment_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white rounded-full text-xs mr-2">6</span>
                                On-Site Start Date & Time:
                            </label>
                            <input type="datetime-local" 
                                   name="f2f_start_date" 
                                   id="f2f_start_date" 
                                   value="{{ old('f2f_start_date', $event->f2f_start_date ? \Carbon\Carbon::parse($event->f2f_start_date)->format('Y-m-d\\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <input type="hidden" name="f2f_start_time" value="09:00">
                            <p id="f2f_start_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">On-Site End Date & Time:</label>
                            <input type="datetime-local" 
                                   name="f2f_end_date" 
                                   id="f2f_end_date" 
                                   value="{{ old('f2f_end_date', $event->f2f_end_date ? \Carbon\Carbon::parse($event->f2f_end_date)->format('Y-m-d\\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <input type="hidden" name="f2f_end_time" value="17:00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Online Details -->
            <div id="online-section" class="mb-8" style="display: {{ old('delivery_mode', $event->delivery_mode) == 'online' || old('delivery_mode', $event->delivery_mode) == 'hybrid' ? 'block' : 'none' }};">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-green-500">
                    <i class="fas fa-laptop mr-2"></i>Online Conference Details
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-purple-500 text-white rounded-full text-xs mr-2">1</span>
                                Reviewer Registration Deadline (Online):
                            </label>
                            <input type="datetime-local" 
                                   name="online_reviewer_registration_deadline" 
                                   id="online_reviewer_registration_deadline"
                                   value="{{ old('online_reviewer_registration_deadline', $event->online_reviewer_registration_deadline ? \Carbon\Carbon::parse($event->online_reviewer_registration_deadline)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Last date for reviewers to register for online event</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs mr-2">2</span>
                                Paper Submission Deadline (Online):
                            </label>
                            <input type="datetime-local" 
                                   name="online_paper_submission_deadline" 
                                   id="online_paper_submission_deadline"
                                   value="{{ old('online_paper_submission_deadline', $event->online_paper_submission_deadline ? \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Due date for online participants to submit papers</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs mr-2">3</span>
                                Review/Evaluation Deadline (Online):
                            </label>
                            <input type="datetime-local" 
                                   name="online_review_deadline" 
                                   id="online_review_deadline"
                                   value="{{ old('online_review_deadline', $event->online_review_deadline ? \Carbon\Carbon::parse($event->online_review_deadline)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">When reviewers must complete their evaluations</p>
                            <p id="online_review_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-500 text-white rounded-full text-xs mr-2">4</span>
                                Acceptance Notification Date (Online):
                            </label>
                            <input type="datetime-local" 
                                   name="online_acceptance_notification_date" 
                                   id="online_acceptance_notification_date"
                                   value="{{ old('online_acceptance_notification_date', $event->online_acceptance_notification_date ? \Carbon\Carbon::parse($event->online_acceptance_notification_date)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">When online participants will be notified of acceptance</p>
                            <p id="online_acceptance_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-orange-500 text-white rounded-full text-xs mr-2">5</span>
                                Payment Deadline (Online):
                            </label>
                            <input type="datetime-local" 
                                   name="online_payment_deadline" 
                                   id="online_payment_deadline"
                                   value="{{ old('online_payment_deadline', $event->online_payment_deadline ? \Carbon\Carbon::parse($event->online_payment_deadline)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Deadline for accepted participants to pay (after acceptance, before event)</p>
                            <p id="online_payment_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white rounded-full text-xs mr-2">6</span>
                                Online Start Date & Time:
                            </label>
                            <input type="datetime-local" 
                                   name="online_start_date" 
                                   id="online_start_date" 
                                   value="{{ old('online_start_date', $event->online_start_date ? \Carbon\Carbon::parse($event->online_start_date)->format('Y-m-d\\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-green-500">
                            <input type="hidden" name="online_start_time" value="09:00">
                            <p id="online_start_warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Online End Date & Time:</label>
                            <input type="datetime-local" 
                                   name="online_end_date" 
                                   id="online_end_date" 
                                   value="{{ old('online_end_date', $event->online_end_date ? \Carbon\Carbon::parse($event->online_end_date)->format('Y-m-d\\TH:i') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-green-500">
                            <input type="hidden" name="online_end_time" value="17:00">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Online Platform URL:</label>
                            <input type="url" name="online_platform_url" id="online_platform_url" value="{{ old('online_platform_url', $event->online_platform_url) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-green-500" placeholder="e.g., https://zoom.us/j/123456789">
                            <p class="text-xs text-gray-500 mt-1">Meeting link (Zoom, Teams, Google Meet, etc.)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 text-center">
                <button type="submit" name="status" value="{{ old('status', $event->status) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-12 py-3 rounded text-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Conference Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submit handler to clean empty categories
    const conferenceForm = document.querySelector('form');
    conferenceForm.addEventListener('submit', function(e) {
        // Remove empty category inputs before submitting
        const categoryInputs = document.querySelectorAll('input[name="conference_categories[]"]');
        categoryInputs.forEach(input => {
            if (!input.value || input.value.trim() === '') {
                input.remove();
            }
        });
    });

    // Add/Remove Category functionality
    const categoriesContainer = document.getElementById('categories-container');
    const addCategoryBtn = document.getElementById('add-category');

    addCategoryBtn.addEventListener('click', function() {
        const categoryItem = document.createElement('div');
        categoryItem.className = 'category-item flex items-center gap-2';
        categoryItem.innerHTML = `
            <input type="text" name="conference_categories[]" class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-purple-500" placeholder="e.g., IoT, Blockchain">
            <button type="button" class="remove-category px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        categoriesContainer.appendChild(categoryItem);
        updateRemoveButtons();
    });

    categoriesContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-category')) {
            e.target.closest('.category-item').remove();
            updateRemoveButtons();
        }
    });

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

    updateRemoveButtons();

    // Delivery mode toggle
    const deliveryModes = document.querySelectorAll('input[name="delivery_mode"]');
    const f2fSection = document.getElementById('f2f-section');
    const onlineSection = document.getElementById('online-section');

    function toggleSections() {
        const selectedMode = document.querySelector('input[name="delivery_mode"]:checked');
        if (!selectedMode) return;

        const mode = selectedMode.value;
        
        // Show/hide sections
        f2fSection.style.display = (mode === 'face_to_face' || mode === 'hybrid') ? 'block' : 'none';
        onlineSection.style.display = (mode === 'online' || mode === 'hybrid') ? 'block' : 'none';

        // Handle required fields
        const f2fInputs = f2fSection.querySelectorAll('input');
        const onlineInputs = onlineSection.querySelectorAll('input');

        if (mode === 'face_to_face' || mode === 'hybrid') {
            f2fInputs.forEach(input => input.required = true);
        } else {
            f2fInputs.forEach(input => input.required = false);
        }

        if (mode === 'online' || mode === 'hybrid') {
            onlineInputs.forEach(input => input.required = true);
        } else {
            onlineInputs.forEach(input => input.required = false);
        }
    }

    deliveryModes.forEach(radio => {
        radio.addEventListener('change', toggleSections);
    });

    // Initialize on page load
    toggleSections();

    // Image upload and preview
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
        dropZone.classList.add('border-purple-400', 'bg-purple-50');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-purple-400', 'bg-purple-50');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-purple-400', 'bg-purple-50');
        
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

    // Conference deadline validation with warnings
    const f2fReviewerReg = document.getElementById('f2f_reviewer_registration_deadline');
    const f2fPaperSubmission = document.getElementById('f2f_paper_submission_deadline');
    const f2fReview = document.getElementById('f2f_review_deadline');
    const f2fAcceptance = document.getElementById('f2f_acceptance_notification_date');
    const f2fPayment = document.getElementById('f2f_payment_deadline');
    const f2fStartDate = document.getElementById('f2f_start_date');
    
    const onlineReviewerReg = document.getElementById('online_reviewer_registration_deadline');
    const onlinePaperSubmission = document.getElementById('online_paper_submission_deadline');
    const onlineReview = document.getElementById('online_review_deadline');
    const onlineAcceptance = document.getElementById('online_acceptance_notification_date');
    const onlinePayment = document.getElementById('online_payment_deadline');
    const onlineStartDate = document.getElementById('online_start_date');
    
    const registrationFee = document.querySelector('input[name="registration_fee"]');

    // F2F Validation
    function validateF2FDates() {
        let hasError = false;
        
        // Hide all warnings first
        document.getElementById('f2f_review_warning').classList.add('hidden');
        document.getElementById('f2f_acceptance_warning').classList.add('hidden');
        document.getElementById('f2f_payment_warning').classList.add('hidden');
        document.getElementById('f2f_start_warning').classList.add('hidden');
        
        if (f2fReviewerReg.value && f2fPaperSubmission.value && f2fReview.value) {
            const reviewerDate = new Date(f2fReviewerReg.value);
            const paperDate = new Date(f2fPaperSubmission.value);
            const reviewDate = new Date(f2fReview.value);
            
            if (reviewDate <= reviewerDate || reviewDate <= paperDate) {
                const warning = document.getElementById('f2f_review_warning');
                warning.textContent = '⚠️ Review/Evaluation deadline must be AFTER both Reviewer Registration and Paper Submission deadlines!';
                warning.classList.remove('hidden');
                f2fReview.classList.add('border-red-500');
                hasError = true;
            } else {
                f2fReview.classList.remove('border-red-500');
            }
        }
        
        if (f2fReview.value && f2fAcceptance.value) {
            const reviewDate = new Date(f2fReview.value);
            const acceptanceDate = new Date(f2fAcceptance.value);
            
            if (acceptanceDate <= reviewDate) {
                const warning = document.getElementById('f2f_acceptance_warning');
                warning.textContent = '⚠️ Acceptance notification must be AFTER Review/Evaluation deadline!';
                warning.classList.remove('hidden');
                f2fAcceptance.classList.add('border-red-500');
                hasError = true;
            } else {
                f2fAcceptance.classList.remove('border-red-500');
            }
        }
        
        if (f2fPayment && f2fAcceptance.value && f2fPayment.value) {
            const acceptanceDate = new Date(f2fAcceptance.value);
            const paymentDate = new Date(f2fPayment.value);
            
            if (paymentDate <= acceptanceDate) {
                const warning = document.getElementById('f2f_payment_warning');
                warning.textContent = '⚠️ Payment deadline must be AFTER Acceptance Notification!';
                warning.classList.remove('hidden');
                f2fPayment.classList.add('border-red-500');
                hasError = true;
            } else {
                f2fPayment.classList.remove('border-red-500');
            }
        }
        
        if (f2fStartDate && f2fStartDate.value) {
            const startDate = new Date(f2fStartDate.value);
            let requiredAfterDate = null;
            let requiredAfterLabel = '';
            
            if (f2fPayment && f2fPayment.value) {
                requiredAfterDate = new Date(f2fPayment.value);
                requiredAfterLabel = 'Payment Deadline';
            } else if (f2fAcceptance.value) {
                requiredAfterDate = new Date(f2fAcceptance.value);
                requiredAfterLabel = 'Acceptance Notification Date';
            }
            
            if (requiredAfterDate && startDate <= requiredAfterDate) {
                const warning = document.getElementById('f2f_start_warning');
                warning.textContent = `⚠️ Event start date must be AFTER ${requiredAfterLabel}!`;
                warning.classList.remove('hidden');
                f2fStartDate.classList.add('border-red-500');
                hasError = true;
            } else {
                f2fStartDate.classList.remove('border-red-500');
            }
        }
        
        return !hasError;
    }

    // Online Validation
    function validateOnlineDates() {
        let hasError = false;
        
        document.getElementById('online_review_warning').classList.add('hidden');
        document.getElementById('online_acceptance_warning').classList.add('hidden');
        document.getElementById('online_payment_warning').classList.add('hidden');
        document.getElementById('online_start_warning').classList.add('hidden');
        
        if (onlineReviewerReg.value && onlinePaperSubmission.value && onlineReview.value) {
            const reviewerDate = new Date(onlineReviewerReg.value);
            const paperDate = new Date(onlinePaperSubmission.value);
            const reviewDate = new Date(onlineReview.value);
            
            if (reviewDate <= reviewerDate || reviewDate <= paperDate) {
                const warning = document.getElementById('online_review_warning');
                warning.textContent = '⚠️ Review/Evaluation deadline must be AFTER both Reviewer Registration and Paper Submission deadlines!';
                warning.classList.remove('hidden');
                onlineReview.classList.add('border-red-500');
                hasError = true;
            } else {
                onlineReview.classList.remove('border-red-500');
            }
        }
        
        if (onlineReview.value && onlineAcceptance.value) {
            const reviewDate = new Date(onlineReview.value);
            const acceptanceDate = new Date(onlineAcceptance.value);
            
            if (acceptanceDate <= reviewDate) {
                const warning = document.getElementById('online_acceptance_warning');
                warning.textContent = '⚠️ Acceptance notification must be AFTER Review/Evaluation deadline!';
                warning.classList.remove('hidden');
                onlineAcceptance.classList.add('border-red-500');
                hasError = true;
            } else {
                onlineAcceptance.classList.remove('border-red-500');
            }
        }
        
        if (onlinePayment && onlineAcceptance.value && onlinePayment.value) {
            const acceptanceDate = new Date(onlineAcceptance.value);
            const paymentDate = new Date(onlinePayment.value);
            
            if (paymentDate <= acceptanceDate) {
                const warning = document.getElementById('online_payment_warning');
                warning.textContent = '⚠️ Payment deadline must be AFTER Acceptance Notification!';
                warning.classList.remove('hidden');
                onlinePayment.classList.add('border-red-500');
                hasError = true;
            } else {
                onlinePayment.classList.remove('border-red-500');
            }
        }
        
        if (onlineStartDate && onlineStartDate.value) {
            const startDate = new Date(onlineStartDate.value);
            let requiredAfterDate = null;
            let requiredAfterLabel = '';
            
            if (onlinePayment && onlinePayment.value) {
                requiredAfterDate = new Date(onlinePayment.value);
                requiredAfterLabel = 'Payment Deadline';
            } else if (onlineAcceptance.value) {
                requiredAfterDate = new Date(onlineAcceptance.value);
                requiredAfterLabel = 'Acceptance Notification Date';
            }
            
            if (requiredAfterDate && startDate <= requiredAfterDate) {
                const warning = document.getElementById('online_start_warning');
                warning.textContent = `⚠️ Event start date must be AFTER ${requiredAfterLabel}!`;
                warning.classList.remove('hidden');
                onlineStartDate.classList.add('border-red-500');
                hasError = true;
            } else {
                onlineStartDate.classList.remove('border-red-500');
            }
        }
        
        return !hasError;
    }

    // Attach event listeners
    if (f2fReviewerReg) {
        [f2fReviewerReg, f2fPaperSubmission, f2fReview, f2fAcceptance, f2fPayment, f2fStartDate].forEach(el => {
            if (el) el.addEventListener('change', validateF2FDates);
        });
    }
    
    if (onlineReviewerReg) {
        [onlineReviewerReg, onlinePaperSubmission, onlineReview, onlineAcceptance, onlinePayment, onlineStartDate].forEach(el => {
            if (el) el.addEventListener('change', validateOnlineDates);
        });
    }

    // Auto-set end date same as start date
    if (f2fStartDate) {
        f2fStartDate.addEventListener('change', function() {
            const f2fEndDate = document.getElementById('f2f_end_date');
            if (!f2fEndDate.value) {
                f2fEndDate.value = this.value;
            }
        });
    }

    if (onlineStartDate) {
        onlineStartDate.addEventListener('change', function() {
            const onlineEndDate = document.getElementById('online_end_date');
            if (!onlineEndDate.value) {
                onlineEndDate.value = this.value;
            }
        });
    }
});
</script>
@endsection
