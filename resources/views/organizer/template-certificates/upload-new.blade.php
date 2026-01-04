@extends('organizer.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('organizer.template-certificates.attendees', $event) }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Attendees List
        </a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900">Upload Certificate Template</h1>
        <p class="mt-2 text-gray-600">{{ $event->title }} - {{ $attendeesCount }} attendees will receive certificates</p>
    </div>

    <!-- Template Guidelines -->
    <div class="mb-8 bg-gradient-to-r from-blue-50 to-purple-50 border-2 border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <svg class="h-6 w-6 text-blue-600 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-blue-900">Certificate Template Guidelines</h3>
                <div class="mt-3 text-sm text-blue-800 space-y-2">
                    <p><strong>📄 Format:</strong> Upload as <strong>PNG or JPG image</strong> (recommended for best quality)</p>
                    <p><strong>📐 Size:</strong> Must be <strong>A4 Portrait</strong> (2480 x 3508 pixels at 300 DPI) or (1754 x 2480 pixels at 210 DPI)</p>
                    <p><strong>🎨 Design:</strong> Create your certificate design in Canva, Photoshop, or any design tool</p>
                    <p><strong>✏️ Blank Spaces:</strong> Leave blank spaces for these 4 fields (system will auto-fill):</p>
                    <ul class="ml-6 mt-2 space-y-1 list-disc">
                        <li><strong>Name:</strong> Participant's full name</li>
                        <li><strong>Role:</strong> Participant / Reviewer / Both</li>
                        <li><strong>Event Name:</strong> {{ $event->title }}</li>
                        <li><strong>Date:</strong> {{ $event->start_date->format('F d, Y') }}</li>
                    </ul>
                    <p class="mt-3"><strong>💡 Tip:</strong> You can create one template for both Participants and Reviewers, or separate templates for each role.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <p class="text-sm font-semibold text-red-700 mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('organizer.template-certificates.upload', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Participant Template -->
                <div class="border-2 border-dashed border-blue-300 rounded-lg p-6 bg-blue-50">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Participant Certificate Template</h3>
                    <p class="text-sm text-blue-700 mb-4">For attendees who presented their papers</p>
                    
                    @if($participantTemplate)
                        <div class="mb-4">
                            <img src="{{ $participantTemplate->template_url }}" alt="Current Template" class="max-w-full h-48 object-contain border rounded bg-white">
                            <p class="mt-2 text-xs text-green-600">✓ Template already uploaded</p>
                        </div>
                    @endif
                    
                    <div id="participant_preview_area"></div>
                    
                    <label for="participant_template" class="flex flex-col items-center justify-center w-full h-32 border-2 border-blue-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-blue-50">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="participant_upload_text">
                            <svg class="w-8 h-8 mb-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-xs text-blue-600"><span class="font-semibold">Click to upload</span> or drag</p>
                            <p class="text-xs text-blue-500">PNG or JPG (MAX. 5MB)</p>
                        </div>
                        <input id="participant_template" name="participant_template" type="file" class="hidden" accept="image/png,image/jpeg,image/jpg" onchange="previewParticipant(this)" />
                    </label>
                    @error('participant_template')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reviewer Template -->
                <div class="border-2 border-dashed border-purple-300 rounded-lg p-6 bg-purple-50">
                    <h3 class="text-lg font-semibold text-purple-900 mb-4">Reviewer Certificate Template</h3>
                    <p class="text-sm text-purple-700 mb-4">For paper reviewers/jury members</p>
                    
                    @if($reviewerTemplate)
                        <div class="mb-4">
                            <img src="{{ $reviewerTemplate->template_url }}" alt="Current Template" class="max-w-full h-48 object-contain border rounded bg-white">
                            <p class="mt-2 text-xs text-green-600">✓ Template already uploaded</p>
                        </div>
                    @endif
                    
                    <div id="reviewer_preview_area"></div>
                    
                    <label for="reviewer_template" class="flex flex-col items-center justify-center w-full h-32 border-2 border-purple-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-purple-50">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="reviewer_upload_text">
                            <svg class="w-8 h-8 mb-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-xs text-purple-600"><span class="font-semibold">Click to upload</span> or drag</p>
                            <p class="text-xs text-purple-500">PNG or JPG (MAX. 5MB)</p>
                        </div>
                        <input id="reviewer_template" name="reviewer_template" type="file" class="hidden" accept="image/png,image/jpeg,image/jpg" onchange="previewReviewer(this)" />
                    </label>
                    @error('reviewer_template')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Text Positioning Configuration -->
            <div class="border-t-2 border-gray-200 pt-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Text Position Configuration</h3>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>📐 A4 Portrait Dimensions:</strong> 210mm (width) × 297mm (height)<br>
                        Set X and Y positions in millimeters from the top-left corner where text should appear.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name Configuration -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-3 flex items-center">
                            <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded mr-2">1</span>
                            Participant Name
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">X Position (mm)</label>
                                <input type="number" step="0.1" name="name_x" value="{{ old('name_x', $participantTemplate->name_x ?? 50) }}" min="0" max="210" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Y Position (mm)</label>
                                <input type="number" step="0.1" name="name_y" value="{{ old('name_y', $participantTemplate->name_y ?? 100) }}" min="0" max="297" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Font Size</label>
                                <input type="number" name="name_font_size" value="{{ old('name_font_size', $participantTemplate->name_font_size ?? 48) }}" min="10" max="100" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Color</label>
                                <input type="color" name="name_color" value="{{ old('name_color', $participantTemplate->name_color ?? '#000000') }}" required class="w-full h-9 border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Role Configuration -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h4 class="font-semibold text-purple-900 mb-3 flex items-center">
                            <span class="bg-purple-500 text-white text-xs font-bold px-2 py-1 rounded mr-2">2</span>
                            Role
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">X Position (mm)</label>
                                <input type="number" step="0.1" name="role_x" value="{{ old('role_x', $participantTemplate->role_x ?? 50) }}" min="0" max="210" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Y Position (mm)</label>
                                <input type="number" step="0.1" name="role_y" value="{{ old('role_y', $participantTemplate->role_y ?? 120) }}" min="0" max="297" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Font Size</label>
                                <input type="number" name="role_font_size" value="{{ old('role_font_size', $participantTemplate->role_font_size ?? 32) }}" min="10" max="100" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Color</label>
                                <input type="color" name="role_color" value="{{ old('role_color', $participantTemplate->role_color ?? '#000000') }}" required class="w-full h-9 border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Event Name Configuration -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-semibold text-green-900 mb-3 flex items-center">
                            <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded mr-2">3</span>
                            Event Name
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">X Position (mm)</label>
                                <input type="number" step="0.1" name="event_name_x" value="{{ old('event_name_x', $participantTemplate->event_name_x ?? 50) }}" min="0" max="210" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Y Position (mm)</label>
                                <input type="number" step="0.1" name="event_name_y" value="{{ old('event_name_y', $participantTemplate->event_name_y ?? 140) }}" min="0" max="297" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Font Size</label>
                                <input type="number" name="event_name_font_size" value="{{ old('event_name_font_size', $participantTemplate->event_name_font_size ?? 36) }}" min="10" max="100" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Color</label>
                                <input type="color" name="event_name_color" value="{{ old('event_name_color', $participantTemplate->event_name_color ?? '#000000') }}" required class="w-full h-9 border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>

                    <!-- Date Configuration -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-semibold text-yellow-900 mb-3 flex items-center">
                            <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded mr-2">4</span>
                            Event Date
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">X Position (mm)</label>
                                <input type="number" step="0.1" name="date_x" value="{{ old('date_x', $participantTemplate->date_x ?? 50) }}" min="0" max="210" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Y Position (mm)</label>
                                <input type="number" step="0.1" name="date_y" value="{{ old('date_y', $participantTemplate->date_y ?? 160) }}" min="0" max="297" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Font Size</label>
                                <input type="number" name="date_font_size" value="{{ old('date_font_size', $participantTemplate->date_font_size ?? 28) }}" min="10" max="100" required class="w-full text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Color</label>
                                <input type="color" name="date_color" value="{{ old('date_color', $participantTemplate->date_color ?? '#000000') }}" required class="w-full h-9 border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('organizer.template-certificates.attendees', $event) }}" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                @if($participantTemplate || $reviewerTemplate)
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-md hover:from-green-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 font-medium">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Generate Certificates
                    </button>
                @else
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                        Upload & Generate Certificates
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
function previewParticipant(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewArea = document.getElementById('participant_preview_area');
            previewArea.innerHTML = `
                <div class="mb-4">
                    <img src="${e.target.result}" alt="Preview" class="max-w-full h-48 object-contain border rounded bg-white">
                    <p class="mt-2 text-xs text-green-600">✓ Participant template selected: ${file.name}</p>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
}

function previewReviewer(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewArea = document.getElementById('reviewer_preview_area');
            previewArea.innerHTML = `
                <div class="mb-4">
                    <img src="${e.target.result}" alt="Preview" class="max-w-full h-48 object-contain border rounded bg-white">
                    <p class="mt-2 text-xs text-green-600">✓ Reviewer template selected: ${file.name}</p>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
