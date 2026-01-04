@extends('layouts.app')

@section('title', 'Submit Paper - ' . $event->title)

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Submit Paper</h1>
            <p class="text-gray-600 mt-2">{{ $event->title }}</p>
        </div>

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

        <form action="{{ route('papers.store', $event) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg p-8">
            @csrf

            <!-- Paper Details Section -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-purple-500">
                    <i class="fas fa-file-alt mr-2"></i>Paper Details
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Paper Title <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            value="{{ old('title') }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" 
                            placeholder="Enter your paper title"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">Maximum 255 characters</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Abstract <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="abstract" 
                            rows="6" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 resize-none"
                            placeholder="Enter your paper abstract (maximum 2000 characters)"
                            required
                        >{{ old('abstract') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Maximum 2000 characters</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Keywords
                        </label>
                        <input 
                            type="text" 
                            name="keywords" 
                            value="{{ old('keywords') }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                            placeholder="e.g., Machine Learning, Artificial Intelligence, Deep Learning"
                        >
                        <p class="text-xs text-gray-500 mt-1">Separate keywords with commas (maximum 500 characters)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Paper (PDF) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="file" 
                            name="paper_file" 
                            accept=".pdf"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">PDF format only, maximum 10MB</p>
                    </div>
                </div>
            </div>

            <!-- Authors Section -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-purple-500">
                    <i class="fas fa-users mr-2"></i>Authors
                </h2>

                <div id="authors-container" class="space-y-6">
                    <!-- First Author (Required) -->
                    <div class="author-item p-6 border-2 border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Author 1 <span class="text-red-500">*</span></h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                                <input 
                                    type="text" 
                                    name="authors[0][name]" 
                                    value="{{ old('authors.0.name') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <input 
                                    type="email" 
                                    name="authors[0][email]" 
                                    value="{{ old('authors.0.email') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Affiliation</label>
                                <input 
                                    type="text" 
                                    name="authors[0][affiliation]" 
                                    value="{{ old('authors.0.affiliation') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                                    placeholder="University/Organization"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <input 
                                    type="text" 
                                    name="authors[0][country]" 
                                    value="{{ old('authors.0.country') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label class="inline-flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="authors[0][is_corresponding]" 
                                        value="1"
                                        class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                        {{ old('authors.0.is_corresponding') ? 'checked' : '' }}
                                    >
                                    <span class="ml-2 text-sm text-gray-700">Corresponding Author</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-author" class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Another Author
                </button>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4 pt-6 border-t">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Paper
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let authorIndex = 1;

document.getElementById('add-author').addEventListener('click', function() {
    const container = document.getElementById('authors-container');
    const authorItem = document.createElement('div');
    authorItem.className = 'author-item p-6 border-2 border-gray-200 rounded-lg bg-gray-50';
    authorItem.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Author ${authorIndex + 1}</h3>
            <button type="button" class="remove-author px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm">
                <i class="fas fa-trash mr-1"></i>Remove
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                <input 
                    type="text" 
                    name="authors[${authorIndex}][name]" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                <input 
                    type="email" 
                    name="authors[${authorIndex}][email]" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Affiliation</label>
                <input 
                    type="text" 
                    name="authors[${authorIndex}][affiliation]" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                    placeholder="University/Organization"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                <input 
                    type="text" 
                    name="authors[${authorIndex}][country]" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                >
            </div>

            <div class="md:col-span-2">
                <label class="inline-flex items-center">
                    <input 
                        type="checkbox" 
                        name="authors[${authorIndex}][is_corresponding]" 
                        value="1"
                        class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">Corresponding Author</span>
                </label>
            </div>
        </div>
    `;
    
    container.appendChild(authorItem);
    authorIndex++;

    // Add event listener to remove button
    authorItem.querySelector('.remove-author').addEventListener('click', function() {
        authorItem.remove();
    });
});
</script>
@endsection
