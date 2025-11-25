@extends('organizer.layouts.app')

@section('title', 'Generate QR Code')
@section('page-title', 'Generate QR Code')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('organizer.qr-codes.index') }}" class="text-gray-400 hover:text-gray-500">
                        QR Codes
                    </a>
                </li>
                <li class="flex">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">Generate QR Code</span>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">Generate QR Code</h1>
        <p class="text-gray-600 mt-2">Create QR codes for event attendance tracking and registration</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Form Section -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">QR Code Configuration</h3>
            </div>
            
            <form action="{{ route('organizer.qr-codes.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Event Selection -->
                <div>
                    <label for="event_id" class="block text-sm font-medium text-gray-700">Event *</label>
                    <select name="event_id" id="event_id" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('event_id') border-red-300 @enderror"
                            onchange="updatePreview()">
                        <option value="">Select an event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ ($selectedEvent && $selectedEvent->id == $event->id) || old('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }} - {{ $event->event_date->format('M j, Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('event_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- QR Code Type -->
                <div>
                    <label for="qr_type" class="block text-sm font-medium text-gray-700">QR Code Type *</label>
                    <select name="qr_type" id="qr_type" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('qr_type') border-red-300 @enderror"
                            onchange="updatePreview()">
                        <option value="check_in" {{ old('qr_type', 'check_in') == 'check_in' ? 'selected' : '' }}>Check-in Only</option>
                        <option value="check_out" {{ old('qr_type') == 'check_out' ? 'selected' : '' }}>Check-out Only</option>
                        <option value="both" {{ old('qr_type') == 'both' ? 'selected' : '' }}>Check-in/Check-out</option>
                        <option value="registration" {{ old('qr_type') == 'registration' ? 'selected' : '' }}>Registration</option>
                    </select>
                    @error('qr_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        <span id="type-description">Allow participants to check in to the event</span>
                    </p>
                </div>

                <!-- QR Code Label -->
                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700">Label</label>
                    <input type="text" name="label" id="label" value="{{ old('label') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('label') border-red-300 @enderror"
                           placeholder="e.g., Main Entrance, VIP Check-in"
                           onchange="updatePreview()">
                    @error('label')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Optional: Add a descriptive label for this QR code</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                              placeholder="Additional details about this QR code...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expiration -->
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiration Date</label>
                    <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('expires_at') border-red-300 @enderror">
                    @error('expires_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Leave blank for no expiration</p>
                </div>

                <!-- Usage Limit -->
                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700">Usage Limit</label>
                    <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}" min="1"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('usage_limit') border-red-300 @enderror"
                           placeholder="e.g., 100">
                    @error('usage_limit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Maximum number of times this QR code can be scanned</p>
                </div>

                <!-- Advanced Settings -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Advanced Settings</h4>
                    
                    <!-- Custom URL -->
                    <div class="mb-4">
                        <label for="custom_url" class="block text-sm font-medium text-gray-700">Custom URL</label>
                        <input type="url" name="custom_url" id="custom_url" value="{{ old('custom_url') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('custom_url') border-red-300 @enderror"
                               placeholder="https://example.com/redirect">
                        @error('custom_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Redirect users to this URL after scanning</p>
                    </div>

                    <!-- Options -->
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 ml-6">QR code will be active immediately after creation</p>
                        
                        <div class="flex items-center">
                            <input id="allow_multiple_scans" name="allow_multiple_scans" type="checkbox" value="1" 
                                   {{ old('allow_multiple_scans', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="allow_multiple_scans" class="ml-2 block text-sm text-gray-900">
                                Allow multiple scans per user
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 ml-6">Users can scan this QR code multiple times</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('organizer.qr-codes.index') }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4.01M12 8h4.01M12 8V4.01" />
                        </svg>
                        Generate QR Code
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        <div class="space-y-6">
            <!-- QR Code Preview -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Preview</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div id="qr-preview" class="inline-block p-6 bg-gray-50 rounded-lg">
                            <div class="w-48 h-48 mx-auto flex items-center justify-center bg-white rounded-lg border-2 border-dashed border-gray-300">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4.01M12 8h4.01M12 8V4.01"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">QR Code Preview</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p id="preview-label" class="text-sm font-medium text-gray-900">Select an event to see preview</p>
                            <p id="preview-type" class="text-sm text-gray-500">QR Code Type</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code Info -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">QR Code Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Format</dt>
                            <dd class="mt-1 text-sm text-gray-900">SVG / PNG</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Size</dt>
                            <dd class="mt-1 text-sm text-gray-900">Scalable</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Error Correction</dt>
                            <dd class="mt-1 text-sm text-gray-900">Medium (15%)</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Encoding</dt>
                            <dd class="mt-1 text-sm text-gray-900">UTF-8</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Tips for QR Codes</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Test QR codes before printing to ensure they scan correctly</li>
                                <li>Print QR codes large enough to be easily scanned (minimum 2x2 cm)</li>
                                <li>Use high contrast backgrounds for better scanning</li>
                                <li>Set expiration dates for security and event management</li>
                                <li>Monitor scan statistics to track usage patterns</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updatePreview() {
    const eventSelect = document.getElementById('event_id');
    const typeSelect = document.getElementById('qr_type');
    const labelInput = document.getElementById('label');
    const previewLabel = document.getElementById('preview-label');
    const previewType = document.getElementById('preview-type');
    const typeDescription = document.getElementById('type-description');
    
    // Update type description
    const descriptions = {
        'check_in': 'Allow participants to check in to the event',
        'check_out': 'Allow participants to check out of the event', 
        'both': 'Allow participants to check in and check out of the event',
        'registration': 'Allow participants to register for the event on-site'
    };
    
    typeDescription.textContent = descriptions[typeSelect.value] || '';
    
    // Update preview
    if (eventSelect.selectedIndex > 0) {
        const eventText = eventSelect.options[eventSelect.selectedIndex].text;
        const eventName = eventText.split(' - ')[0];
        const label = labelInput.value || 'QR Code';
        const type = typeSelect.options[typeSelect.selectedIndex].text;
        
        previewLabel.textContent = `${eventName} - ${label}`;
        previewType.textContent = type;
    } else {
        previewLabel.textContent = 'Select an event to see preview';
        previewType.textContent = 'QR Code Type';
    }
}

// Update preview when form changes
document.getElementById('event_id').addEventListener('change', updatePreview);
document.getElementById('qr_type').addEventListener('change', updatePreview);
document.getElementById('label').addEventListener('input', updatePreview);

// Initialize preview
updatePreview();
</script>
@endsection