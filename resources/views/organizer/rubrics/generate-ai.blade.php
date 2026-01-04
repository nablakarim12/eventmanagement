@extends('organizer.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('organizer.events.rubrics.index', $event->id) }}" class="text-indigo-600 hover:text-indigo-800 mb-2 inline-block">
            ← Back to Rubric
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Generate Rubric with AI</h1>
        <p class="text-gray-600 mt-1">{{ $event->event_name }}</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- AI Generation Options -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('organizer.events.rubrics.process-ai', $event->id) }}" method="POST" enctype="multipart/form-data" id="aiGenerationForm">
                @csrf

                <!-- Header Info -->
                <div class="mb-6 bg-gradient-to-r from-green-50 to-teal-50 rounded-lg p-4 border-l-4 border-green-500">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-green-600 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900">📄 Upload Your Document + 💬 Explain Your Needs</h3>
                            <p class="text-sm text-gray-700 mt-1">
                                Upload your existing rubric document (PDF/Excel/Image) AND add a text explanation. 
                                AI will read both to create the perfect rubric structure for you!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div id="uploadSection" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Rubric Document (Optional)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-indigo-500 transition">
                        <input type="file" 
                               name="file" 
                               id="fileInput" 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" 
                               class="hidden"
                               onchange="previewRubricFile(this)">
                        <label for="fileInput" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">
                                <span class="font-medium text-indigo-600 hover:text-indigo-500">Click to upload</span> or drag and drop
                            </p>
                            <div class="mt-2 flex flex-wrap justify-center gap-2">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                    📄 PDF
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    📘 DOC/DOCX
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                    📊 XLS/XLSX
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    🖼️ JPG/PNG
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 font-semibold">Maximum file size: 10MB</p>
                        </label>
                        
                        <!-- File Preview -->
                        <div id="filePreview" class="mt-4 hidden">
                            <div class="inline-block bg-green-50 border-2 border-green-500 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-left">
                                        <p id="fileName" class="text-sm font-bold text-gray-900"></p>
                                        <p id="fileSize" class="text-xs text-gray-600"></p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-green-600 font-medium mt-2">✅ File uploaded successfully!</p>
                        </div>
                    </div>
                    
                    @error('file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <p class="mt-2 text-xs text-gray-600">
                        💡 <strong>Best results:</strong> Images (JPG/PNG) and PDFs work great with AI vision. Word/Excel files will have text extracted first.
                    </p>
                </div>

                <!-- Text Description Section -->
                <div id="descriptionSection" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Explain Your Rubric (Required)</label>
                    <textarea name="description" id="descriptionInput" rows="6" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Example: This rubric is for evaluating innovation projects. It should include poster presentation (30 points), oral presentation (30 points), and product demo (40 points). For each category, evaluate creativity, technical quality, and commercial potential. Use a 5-point scale for detailed items."></textarea>
                    <p class="mt-2 text-xs text-gray-600">
                        💡 <strong>Tip:</strong> Explain what the uploaded document contains, what categories you need, scoring ranges, and special requirements. AI will combine your document structure with your instructions.
                    </p>
                </div>

                <!-- Examples Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">📚 Example Prompts</h3>
                    <div class="space-y-2 text-sm text-gray-700">
                        <p class="cursor-pointer hover:text-indigo-600" onclick="setDescription(this)">
                            • "Create a rubric for academic paper evaluation with Abstract, Methodology, Results, and Conclusion sections. Use 5-point scale for each."
                        </p>
                        <p class="cursor-pointer hover:text-indigo-600" onclick="setDescription(this)">
                            • "Generate rubric for innovation competition evaluating poster (30%), presentation (30%), and product demo (40%). Include commercial viability criteria."
                        </p>
                        <p class="cursor-pointer hover:text-indigo-600" onclick="setDescription(this)">
                            • "Design a comprehensive rubric for research projects covering problem statement, literature review, methodology, results, discussion, and future work. Total 100 points."
                        </p>
                    </div>
                </div>

                <!-- Generate Button -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('organizer.events.rubrics.index', $event->id) }}" class="text-gray-600 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-semibold shadow-lg flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Generate with AI</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- How it Works Section -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">🤖 How AI Generation Works</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl font-bold text-indigo-600">1</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Upload or Describe</h4>
                <p class="text-sm text-gray-600">Provide your rubric document or describe your evaluation criteria</p>
            </div>
            <div class="text-center">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl font-bold text-purple-600">2</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">AI Analysis</h4>
                <p class="text-sm text-gray-600">AI extracts structure, categories, items, and scoring levels</p>
            </div>
            <div class="text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl font-bold text-green-600">3</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">Review & Confirm</h4>
                <p class="text-sm text-gray-600">Preview the generated rubric and make any final adjustments</p>
            </div>
        </div>
    </div>
</div>

<script>
// File preview function (simple like poster upload)
function previewRubricFile(input) {
    const preview = document.getElementById('filePreview');
    const fileNameDisplay = document.getElementById('fileName');
    const fileSizeDisplay = document.getElementById('fileSize');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
        
        // Check file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB');
            input.value = '';
            preview.classList.add('hidden');
            return;
        }
        
        // Get file extension and icon
        const extension = file.name.split('.').pop().toUpperCase();
        const fileIcons = {
            'PDF': '📄',
            'DOC': '📘',
            'DOCX': '📘',
            'XLS': '📊',
            'XLSX': '📊',
            'JPG': '🖼️',
            'JPEG': '🖼️',
            'PNG': '🖼️'
        };
        const icon = fileIcons[extension] || '📎';
        
        // Show file info
        fileNameDisplay.textContent = `${icon} ${file.name}`;
        fileSizeDisplay.textContent = `Size: ${fileSizeMB}MB • Type: ${extension}`;
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
}

// Set description from example
function setDescription(element) {
    const text = element.textContent.replace('• "', '').replace('"', '');
    document.getElementById('descriptionInput').value = text;
}

// Form submission - show loading state
document.getElementById('aiGenerationForm').addEventListener('submit', function(e) {
    const description = document.getElementById('descriptionInput').value.trim();
    
    if (description.length < 20) {
        e.preventDefault();
        alert('Please provide at least 20 characters in the description to help AI understand your rubric requirements.');
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="ml-2">Generating rubric...</span>
    `;
});
</script>

@endsection
