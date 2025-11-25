@extends('layouts.app')

@section('title', 'Register for ' . $event->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Register for Event</h1>
        <h2 class="text-xl text-blue-600 mb-4">{{ $event->title }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
            <div>
                <i class="fas fa-calendar text-blue-500 mr-2"></i>
                <strong>Date:</strong> {{ $event->start_date->format('F j, Y') }}
            </div>
            <div>
                <i class="fas fa-clock text-blue-500 mr-2"></i>
                <strong>Time:</strong> {{ $event->start_date->format('g:i A') }}
            </div>
            <div>
                <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                <strong>Location:</strong> {{ $event->location }}
            </div>
            @if($event->registration_fee > 0)
            <div>
                <i class="fas fa-dollar-sign text-blue-500 mr-2"></i>
                <strong>Fee:</strong> ${{ number_format($event->registration_fee, 2) }}
            </div>
            @else
            <div>
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <strong>Free Event</strong>
            </div>
            @endif
        </div>
    </div>

    <!-- Registration Form -->
    <form action="{{ route('events.register.store', $event) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
        @csrf

        <!-- Role Selection -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <label class="block text-gray-700 text-lg font-semibold mb-3">
                <i class="fas fa-user-tag text-blue-500 mr-2"></i>
                Register As *
            </label>
            <div class="space-y-3">
                @php
                    $selectedRole = old('role', $preselectedRole ?? null);
                @endphp
                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors {{ $selectedRole == 'participant' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                    <input type="radio" name="role" value="participant" class="form-radio h-5 w-5 text-blue-600" {{ $selectedRole == 'participant' ? 'checked' : '' }} required>
                    <div class="ml-3">
                        <span class="text-gray-900 font-medium">Participant</span>
                        <p class="text-sm text-gray-600">Attend the event as a participant</p>
                    </div>
                </label>

                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-green-50 transition-colors {{ $selectedRole == 'jury' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                    <input type="radio" name="role" value="jury" class="form-radio h-5 w-5 text-green-600" {{ $selectedRole == 'jury' ? 'checked' : '' }} required>
                    <div class="ml-3">
                        <span class="text-gray-900 font-medium">Jury Member</span>
                        <p class="text-sm text-gray-600">Apply to be a jury member (requires qualification)</p>
                    </div>
                </label>

                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-purple-50 transition-colors {{ $selectedRole == 'both' ? 'border-purple-500 bg-purple-50' : 'border-gray-300' }}">
                    <input type="radio" name="role" value="both" class="form-radio h-5 w-5 text-purple-600" {{ $selectedRole == 'both' ? 'checked' : '' }} required>
                    <div class="ml-3">
                        <span class="text-gray-900 font-medium">Both Participant & Jury</span>
                        <p class="text-sm text-gray-600">Register for both roles (requires qualification)</p>
                    </div>
                </label>
            </div>
            @error('role')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Jury Qualification Section (Hidden by default) -->
        <div id="jury-qualification-section" class="mb-6 pb-6 border-b border-gray-200" style="display: none;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-gavel text-green-500 mr-2"></i>
                Jury Qualification
            </h3>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Please provide your qualifications and supporting documents. Your application will be reviewed by the organizer.
                </p>
            </div>

            <!-- Qualification Summary -->
            <div class="mb-4">
                <label for="jury_qualification_summary" class="block text-gray-700 font-medium mb-2">
                    Qualification Summary *
                </label>
                <textarea 
                    id="jury_qualification_summary" 
                    name="jury_qualification_summary" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Briefly describe why you are qualified to be a jury member...">{{ old('jury_qualification_summary') }}</textarea>
                @error('jury_qualification_summary')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Institution -->
            <div class="mb-4">
                <label for="jury_institution" class="block text-gray-700 font-medium mb-2">
                    Institution/Organization *
                </label>
                <input 
                    type="text" 
                    id="jury_institution" 
                    name="jury_institution" 
                    value="{{ old('jury_institution') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="e.g., University of Technology">
                @error('jury_institution')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Position -->
            <div class="mb-4">
                <label for="jury_position" class="block text-gray-700 font-medium mb-2">
                    Position/Title *
                </label>
                <input 
                    type="text" 
                    id="jury_position" 
                    name="jury_position" 
                    value="{{ old('jury_position') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="e.g., Associate Professor">
                @error('jury_position')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Years of Experience -->
            <div class="mb-4">
                <label for="jury_years_experience" class="block text-gray-700 font-medium mb-2">
                    Years of Experience *
                </label>
                <input 
                    type="number" 
                    id="jury_years_experience" 
                    name="jury_years_experience" 
                    value="{{ old('jury_years_experience') }}"
                    min="0"
                    max="99"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="e.g., 5">
                @error('jury_years_experience')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Expertise Areas -->
            <div class="mb-4">
                <label for="jury_expertise_areas" class="block text-gray-700 font-medium mb-2">
                    Areas of Expertise *
                </label>
                <input 
                    type="text" 
                    id="jury_expertise_areas" 
                    name="jury_expertise_areas" 
                    value="{{ old('jury_expertise_areas') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="e.g., Machine Learning, Data Science, AI">
                <p class="text-sm text-gray-500 mt-1">Separate multiple areas with commas</p>
                @error('jury_expertise_areas')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Experience Description -->
            <div class="mb-4">
                <label for="jury_experience" class="block text-gray-700 font-medium mb-2">
                    Relevant Experience *
                </label>
                <textarea 
                    id="jury_experience" 
                    name="jury_experience" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Describe your relevant experience, publications, projects, etc...">{{ old('jury_experience') }}</textarea>
                @error('jury_experience')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Supporting Documents -->
            <div class="mb-4">
                <label for="jury_qualification_documents" class="block text-gray-700 font-medium mb-2">
                    Supporting Documents *
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-500 transition-colors">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                    <input 
                        type="file" 
                        id="jury_qualification_documents" 
                        name="jury_qualification_documents[]" 
                        multiple
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        class="hidden">
                    <label for="jury_qualification_documents" class="cursor-pointer">
                        <span class="text-blue-600 hover:underline">Click to upload</span>
                        <span class="text-gray-600"> or drag and drop</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-2">CV, certificates, publications, etc. (PDF, DOC, DOCX, JPG, PNG - Max 5 files)</p>
                </div>
                <div id="file-list" class="mt-3 space-y-2"></div>
                @error('jury_qualification_documents')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('jury_qualification_documents.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Additional Information
            </h3>

            <!-- Special Requirements -->
            <div class="mb-4">
                <label for="special_requirements" class="block text-gray-700 font-medium mb-2">
                    Special Requirements
                </label>
                <textarea 
                    id="special_requirements" 
                    name="special_requirements" 
                    rows="3" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Any special requirements or accessibility needs...">{{ old('special_requirements') }}</textarea>
                @error('special_requirements')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dietary Restrictions -->
            <div class="mb-4">
                <label for="dietary_restrictions" class="block text-gray-700 font-medium mb-2">
                    Dietary Restrictions
                </label>
                <input 
                    type="text" 
                    id="dietary_restrictions" 
                    name="dietary_restrictions" 
                    value="{{ old('dietary_restrictions') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., Vegetarian, Halal, No nuts">
                @error('dietary_restrictions')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-phone text-red-500 mr-2"></i>
                Emergency Contact
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="emergency_contact_name" class="block text-gray-700 font-medium mb-2">
                        Contact Name
                    </label>
                    <input 
                        type="text" 
                        id="emergency_contact_name" 
                        name="emergency_contact_name" 
                        value="{{ old('emergency_contact_name') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Full name">
                    @error('emergency_contact_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="emergency_contact_phone" class="block text-gray-700 font-medium mb-2">
                        Contact Phone
                    </label>
                    <input 
                        type="tel" 
                        id="emergency_contact_phone" 
                        name="emergency_contact_phone" 
                        value="{{ old('emergency_contact_phone') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="+1 234 567 8900">
                    @error('emergency_contact_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="mb-6">
            <label class="flex items-start">
                <input 
                    type="checkbox" 
                    name="terms_accepted" 
                    value="1"
                    {{ old('terms_accepted') ? 'checked' : '' }}
                    class="form-checkbox h-5 w-5 text-blue-600 mt-1" 
                    required>
                <span class="ml-3 text-gray-700">
                    I agree to the terms and conditions of this event and understand that my registration is subject to organizer approval. *
                </span>
            </label>
            @error('terms_accepted')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button 
                type="submit" 
                class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                <i class="fas fa-check mr-2"></i>
                Complete Registration
            </button>
            <a 
                href="{{ route('events.show', $event->slug ?? $event->id) }}" 
                class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-400 transition-colors text-center">
                <i class="fas fa-times mr-2"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const jurySection = document.getElementById('jury-qualification-section');
    const juryFields = jurySection.querySelectorAll('input[type="text"], input[type="number"], textarea, input[type="file"]');
    const fileInput = document.getElementById('jury_qualification_documents');
    const fileList = document.getElementById('file-list');

    // Toggle jury section visibility
    function toggleJurySection() {
        const selectedRole = document.querySelector('input[name="role"]:checked')?.value;
        const showJurySection = selectedRole === 'jury' || selectedRole === 'both';
        
        jurySection.style.display = showJurySection ? 'block' : 'none';
        
        // Make jury fields required/optional based on role
        juryFields.forEach(field => {
            if (field.type === 'file') {
                field.required = showJurySection;
            } else if (!field.name.includes('jury_qualification_documents')) {
                field.required = showJurySection;
            }
        });
    }

    // Add event listeners to role radios
    roleRadios.forEach(radio => {
        radio.addEventListener('change', toggleJurySection);
    });

    // Initial toggle
    toggleJurySection();

    // File input handling
    fileInput.addEventListener('change', function(e) {
        fileList.innerHTML = '';
        const files = Array.from(this.files);
        
        if (files.length > 5) {
            alert('Maximum 5 files allowed');
            this.value = '';
            return;
        }
        
        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg';
            fileItem.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-file-${getFileIcon(file.name)} text-blue-500 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">${file.name}</p>
                        <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                    </div>
                </div>
                <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    });

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        if (ext === 'pdf') return 'pdf';
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'image';
        if (['doc', 'docx'].includes(ext)) return 'word';
        return 'alt';
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    window.removeFile = function(index) {
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        fileInput.dispatchEvent(new Event('change'));
    };
});
</script>
@endpush
@endsection
