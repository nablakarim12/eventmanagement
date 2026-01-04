@extends('organizer.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('organizer.template-certificates.index', $event) }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Certificates
        </a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900">Upload {{ ucfirst($type) }} Certificate Template</h1>
        <p class="mt-2 text-gray-600">{{ $event->title }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Instructions -->
        <div class="mb-8 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
            <div class="flex">
                <svg class="h-6 w-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Template Instructions</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Upload a certificate template image (PNG/JPG, max 5MB)</li>
                            <li>Leave blank spaces for: Name, Role, Event Name, and Date</li>
                            <li>Set coordinates (X, Y) in pixels from top-left corner</li>
                            <li>Choose font size and color for each text field</li>
                            <li>Preview your template before uploading</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if($template)
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                <p class="text-sm text-yellow-800">
                    ⚠️ A template already exists. Uploading a new one will replace the current template.
                </p>
            </div>
        @endif

        <form action="{{ route('organizer.template-certificates.upload', [$event, $type]) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf

            <!-- Template File Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Certificate Template Image *</label>
                <div class="flex items-center justify-center w-full">
                    <label for="template_file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="uploadPlaceholder">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500">PNG or JPG (MAX. 5MB)</p>
                        </div>
                        <img id="imagePreview" class="hidden max-h-60 w-auto object-contain">
                        <input id="template_file" name="template_file" type="file" class="hidden" accept="image/png,image/jpeg,image/jpg" required onchange="previewImage(event)" />
                    </label>
                </div>
                @error('template_file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Name Settings -->
                <div class="col-span-2 border-2 border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded mr-2">1</span>
                        Participant Name Position
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">X Position (px) *</label>
                            <input type="number" name="name_x" value="{{ old('name_x', $template->name_x ?? 100) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Y Position (px) *</label>
                            <input type="number" name="name_y" value="{{ old('name_y', $template->name_y ?? 200) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Font Size *</label>
                            <input type="number" name="name_font_size" value="{{ old('name_font_size', $template->name_font_size ?? 36) }}" min="10" max="100" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Text Color *</label>
                            <input type="color" name="name_color" value="{{ old('name_color', $template->name_color ?? '#000000') }}" required class="w-full h-10 border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Role Settings -->
                <div class="col-span-2 border-2 border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2 py-1 rounded mr-2">2</span>
                        Role Position
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">X Position (px) *</label>
                            <input type="number" name="role_x" value="{{ old('role_x', $template->role_x ?? 100) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Y Position (px) *</label>
                            <input type="number" name="role_y" value="{{ old('role_y', $template->role_y ?? 250) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Font Size *</label>
                            <input type="number" name="role_font_size" value="{{ old('role_font_size', $template->role_font_size ?? 24) }}" min="10" max="100" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Text Color *</label>
                            <input type="color" name="role_color" value="{{ old('role_color', $template->role_color ?? '#000000') }}" required class="w-full h-10 border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Event Name Settings -->
                <div class="col-span-2 border-2 border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded mr-2">3</span>
                        Event Name Position
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">X Position (px) *</label>
                            <input type="number" name="event_name_x" value="{{ old('event_name_x', $template->event_name_x ?? 100) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Y Position (px) *</label>
                            <input type="number" name="event_name_y" value="{{ old('event_name_y', $template->event_name_y ?? 300) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Font Size *</label>
                            <input type="number" name="event_name_font_size" value="{{ old('event_name_font_size', $template->event_name_font_size ?? 28) }}" min="10" max="100" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Text Color *</label>
                            <input type="color" name="event_name_color" value="{{ old('event_name_color', $template->event_name_color ?? '#000000') }}" required class="w-full h-10 border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Date Settings -->
                <div class="col-span-2 border-2 border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded mr-2">4</span>
                        Event Date Position
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">X Position (px) *</label>
                            <input type="number" name="date_x" value="{{ old('date_x', $template->date_x ?? 100) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Y Position (px) *</label>
                            <input type="number" name="date_y" value="{{ old('date_y', $template->date_y ?? 350) }}" min="0" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Font Size *</label>
                            <input type="number" name="date_font_size" value="{{ old('date_font_size', $template->date_font_size ?? 20) }}" min="10" max="100" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Text Color *</label>
                            <input type="color" name="date_color" value="{{ old('date_color', $template->date_color ?? '#000000') }}" required class="w-full h-10 border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('organizer.template-certificates.index', $event) }}" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Upload Template
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('uploadPlaceholder').classList.add('hidden');
            const preview = document.getElementById('imagePreview');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
