@extends('organizer.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('organizer.events.rubrics.index', $event) }}" class="text-purple-600 hover:text-purple-800 mb-2 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Rubric
            </a>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                {{ $categories->count() > 0 ? 'Edit' : 'Create' }} Evaluation Rubric
            </h1>
            <p class="text-gray-600 mt-2">{{ $event->title }}</p>
        </div>

        <!-- Form -->
        <form action="{{ route('organizer.events.rubrics.update', $event) }}" method="POST" id="rubricForm">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white">Rubric Builder</h2>
                    <p class="text-purple-100 text-sm mt-1">Build categories, items, and score level descriptions</p>
                </div>

                <div class="p-6">
                    @if($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                            <p class="font-semibold mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Categories Container -->
                    <div id="categoriesContainer" class="space-y-6">
                        @forelse($categories as $catIndex => $category)
                            <div class="category-block border-4 border-purple-200 rounded-xl p-6 bg-purple-50" data-cat-index="{{ $catIndex }}">
                                <!-- Category Header -->
                                <div class="flex items-start gap-4 mb-4 pb-4 border-b-2 border-purple-300">
                                    <div class="flex-1 grid grid-cols-12 gap-4">
                                        <div class="col-span-12 md:col-span-5">
                                            <label class="block text-sm font-semibold text-purple-900 mb-1">Category Name *</label>
                                            <input type="text" 
                                                   name="categories[{{ $catIndex }}][name]" 
                                                   value="{{ old("categories.{$catIndex}.name", $category->name) }}"
                                                   class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                                   placeholder="e.g., Poster, Presentation, Product"
                                                   required>
                                        </div>
                                        <div class="col-span-4 md:col-span-2">
                                            <label class="block text-sm font-semibold text-purple-900 mb-1">Max Score *</label>
                                            <input type="number" 
                                                   name="categories[{{ $catIndex }}][max_score]" 
                                                   value="{{ old("categories.{$catIndex}.max_score", $category->max_score) }}"
                                                   class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg"
                                                   min="1" 
                                                   required>
                                        </div>
                                        <div class="col-span-4 md:col-span-2">
                                            <label class="block text-sm font-semibold text-purple-900 mb-1">Weight *</label>
                                            <input type="number" 
                                                   name="categories[{{ $catIndex }}][weight]" 
                                                   value="{{ old("categories.{$catIndex}.weight", $category->weight) }}"
                                                   class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg"
                                                   min="1" 
                                                   required>
                                        </div>
                                        <div class="col-span-4 md:col-span-3 flex items-end">
                                            <button type="button" class="remove-category text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg font-semibold">
                                                Remove Category
                                            </button>
                                        </div>
                                        <div class="col-span-12">
                                            <label class="block text-sm font-semibold text-purple-900 mb-1">Description</label>
                                            <textarea name="categories[{{ $catIndex }}][description]" 
                                                      rows="2"
                                                      class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg resize-none"
                                                      placeholder="Describe this category...">{{ old("categories.{$catIndex}.description", $category->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Items Container -->
                                <div class="items-container space-y-4 mb-4" data-cat-index="{{ $catIndex }}">
                                    @foreach($category->items as $itemIndex => $item)
                                        <div class="item-block border-2 border-blue-200 rounded-lg p-4 bg-white" data-item-index="{{ $itemIndex }}">
                                            <!-- Item Header -->
                                            <div class="grid grid-cols-12 gap-4 mb-3">
                                                <div class="col-span-12 md:col-span-7">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                                                    <input type="text" 
                                                           name="categories[{{ $catIndex }}][items][{{ $itemIndex }}][name]" 
                                                           value="{{ old("categories.{$catIndex}.items.{$itemIndex}.name", $item->name) }}"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                                           placeholder="e.g., Abstract, Methodology"
                                                           required>
                                                </div>
                                                <div class="col-span-6 md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Score *</label>
                                                    <input type="number" 
                                                           name="categories[{{ $catIndex }}][items][{{ $itemIndex }}][max_score]" 
                                                           value="{{ old("categories.{$catIndex}.items.{$itemIndex}.max_score", $item->max_score) }}"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg item-max-score"
                                                           min="1"
                                                           max="100" 
                                                           required>
                                                </div>
                                                <div class="col-span-6 md:col-span-3 flex items-end">
                                                    <button type="button" class="remove-item text-red-500 hover:bg-red-50 px-3 py-2 rounded-lg text-sm">
                                                        Remove Item
                                                    </button>
                                                </div>
                                                <!-- Item Description -->
                                                <div class="col-span-12">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Description (Optional)</label>
                                                    <textarea name="categories[{{ $catIndex }}][items][{{ $itemIndex }}][description]" 
                                                              rows="2"
                                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none text-sm"
                                                              placeholder="e.g., The content of abstract should include: I) Introduction II) Problem statement III) Objective...">{{ old("categories.{$catIndex}.items.{$itemIndex}.description", $item->description) }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Score Levels -->
                                            <div class="score-levels-container bg-gray-50 rounded-lg p-3" data-item-index="{{ $itemIndex }}">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Score Level Descriptions (1-<span class="max-level">{{ $item->max_score }}</span>)
                                                </label>
                                                <div class="score-levels-list space-y-2">
                                                    @php
                                                        $maxScore = $item->max_score;
                                                    @endphp
                                                    @for($level = 1; $level <= $maxScore; $level++)
                                                        @php
                                                            $scoreLevel = $item->scoreLevels->firstWhere('level', $level);
                                                        @endphp
                                                        <div class="flex items-center gap-2">
                                                            <span class="flex-shrink-0 w-12 text-center font-semibold text-gray-700">{{ $level }}</span>
                                                            <input type="text" 
                                                                   name="categories[{{ $catIndex }}][items][{{ $itemIndex }}][score_levels][{{ $level }}]" 
                                                                   value="{{ old("categories.{$catIndex}.items.{$itemIndex}.score_levels.{$level}", $scoreLevel ? $scoreLevel->description : '') }}"
                                                                   class="flex-1 px-3 py-1 border border-gray-300 rounded text-sm"
                                                                   placeholder="Description for level {{ $level }}">
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Add Item Button -->
                                <button type="button" class="add-item w-full bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg font-medium" data-cat-index="{{ $catIndex }}">
                                    + Add Item to {{ $category->name }}
                                </button>
                            </div>
                        @empty
                            <!-- Will be populated by JavaScript -->
                        @endforelse
                    </div>

                    <!-- Add Category Button -->
                    <div class="mt-6">
                        <button type="button" 
                                id="addCategory"
                                class="w-full bg-purple-100 hover:bg-purple-200 text-purple-700 px-6 py-4 rounded-lg font-semibold border-2 border-dashed border-purple-300 hover:border-purple-400">
                            + Add Category
                        </button>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-8 flex items-center justify-between gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('organizer.events.rubrics.index', $event) }}" 
                           class="px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl">
                            Save Rubric
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let categoryCount = {{ $categories->count() }};
    const container = document.getElementById('categoriesContainer');
    const addCategoryBtn = document.getElementById('addCategory');

    // Add Category
    addCategoryBtn.addEventListener('click', function() {
        const catIndex = categoryCount;
        const categoryHTML = `
            <div class="category-block border-4 border-purple-200 rounded-xl p-6 bg-purple-50" data-cat-index="${catIndex}">
                <div class="flex items-start gap-4 mb-4 pb-4 border-b-2 border-purple-300">
                    <div class="flex-1 grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-5">
                            <label class="block text-sm font-semibold text-purple-900 mb-1">Category Name *</label>
                            <input type="text" name="categories[${catIndex}][name]" class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg" placeholder="e.g., Poster" required>
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="block text-sm font-semibold text-purple-900 mb-1">Max Score *</label>
                            <input type="number" name="categories[${catIndex}][max_score]" value="30" class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg" min="1" required>
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="block text-sm font-semibold text-purple-900 mb-1">Weight *</label>
                            <input type="number" name="categories[${catIndex}][weight]" value="1" class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg" min="1" required>
                        </div>
                        <div class="col-span-4 md:col-span-3 flex items-end">
                            <button type="button" class="remove-category text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg font-semibold">Remove Category</button>
                        </div>
                        <div class="col-span-12">
                            <label class="block text-sm font-semibold text-purple-900 mb-1">Description</label>
                            <textarea name="categories[${catIndex}][description]" rows="2" class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg resize-none"></textarea>
                        </div>
                    </div>
                </div>
                <div class="items-container space-y-4 mb-4" data-cat-index="${catIndex}"></div>
                <button type="button" class="add-item w-full bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg font-medium" data-cat-index="${catIndex}">+ Add Item</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', categoryHTML);
        categoryCount++;
    });

    // Add Item
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-item')) {
            const catIndex = e.target.dataset.catIndex;
            const itemsContainer = e.target.closest('.category-block').querySelector('.items-container');
            const itemIndex = itemsContainer.children.length;
            
            const itemHTML = `
                <div class="item-block border-2 border-blue-200 rounded-lg p-4 bg-white" data-item-index="${itemIndex}">
                    <div class="grid grid-cols-12 gap-4 mb-3">
                        <div class="col-span-12 md:col-span-7">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                            <input type="text" name="categories[${catIndex}][items][${itemIndex}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="e.g., Abstract" required>
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Score *</label>
                            <input type="number" name="categories[${catIndex}][items][${itemIndex}][max_score]" value="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg item-max-score" min="1" max="100" required>
                        </div>
                        <div class="col-span-6 md:col-span-3 flex items-end">
                            <button type="button" class="remove-item text-red-500 hover:bg-red-50 px-3 py-2 rounded-lg text-sm">Remove Item</button>
                        </div>
                        <div class="col-span-12">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Item Description (Optional)</label>
                            <textarea name="categories[${catIndex}][items][${itemIndex}][description]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none text-sm" placeholder="e.g., The content of abstract should include: I) Introduction II) Problem statement..."></textarea>
                        </div>
                    </div>
                    <div class="score-levels-container bg-gray-50 rounded-lg p-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Score Level Descriptions (1-<span class="max-level">5</span>)</label>
                        <div class="score-levels-list space-y-2">
                            ${generateScoreLevels(5, catIndex, itemIndex)}
                        </div>
                    </div>
                </div>
            `;
            itemsContainer.insertAdjacentHTML('beforeend', itemHTML);
            
            // Add event listener for the new max score input
            const newItem = itemsContainer.lastElementChild;
            const maxScoreInput = newItem.querySelector('.item-max-score');
            maxScoreInput.addEventListener('input', function() {
                updateScoreLevels(this);
            });
        }
    });

    // Helper function to generate score level inputs
    function generateScoreLevels(maxScore, catIndex, itemIndex) {
        let html = '';
        for (let level = 1; level <= maxScore; level++) {
            html += `
                <div class="flex items-center gap-2">
                    <span class="flex-shrink-0 w-12 text-center font-semibold text-gray-700">${level}</span>
                    <input type="text" name="categories[${catIndex}][items][${itemIndex}][score_levels][${level}]" class="flex-1 px-3 py-1 border border-gray-300 rounded text-sm" placeholder="Description for level ${level}">
                </div>
            `;
        }
        return html;
    }

    // Function to update score levels when max score changes
    function updateScoreLevels(maxScoreInput) {
        const maxScore = parseInt(maxScoreInput.value) || 5;
        if (maxScore < 1 || maxScore > 100) return;
        
        const itemBlock = maxScoreInput.closest('.item-block');
        const scoreLevelsContainer = itemBlock.querySelector('.score-levels-list');
        const maxLevelSpan = itemBlock.querySelector('.max-level');
        
        // Update label
        if (maxLevelSpan) {
            maxLevelSpan.textContent = maxScore;
        }
        
        // Get category and item indices from the input name
        const nameAttr = maxScoreInput.getAttribute('name');
        const matches = nameAttr.match(/categories\[(\d+)\]\[items\]\[(\d+)\]/);
        if (!matches) return;
        
        const catIndex = matches[1];
        const itemIndex = matches[2];
        
        // Get existing values
        const existingInputs = scoreLevelsContainer.querySelectorAll('input[type="text"]');
        const existingValues = {};
        existingInputs.forEach(input => {
            const levelMatch = input.getAttribute('name').match(/\[(\d+)\]$/);
            if (levelMatch) {
                existingValues[levelMatch[1]] = input.value;
            }
        });
        
        // Regenerate score levels
        let html = '';
        for (let level = 1; level <= maxScore; level++) {
            const existingValue = existingValues[level] || '';
            html += `
                <div class="flex items-center gap-2">
                    <span class="flex-shrink-0 w-12 text-center font-semibold text-gray-700">${level}</span>
                    <input type="text" name="categories[${catIndex}][items][${itemIndex}][score_levels][${level}]" value="${existingValue}" class="flex-1 px-3 py-1 border border-gray-300 rounded text-sm" placeholder="Description for level ${level}">
                </div>
            `;
        }
        scoreLevelsContainer.innerHTML = html;
    }

    // Add event listeners to existing max score inputs
    document.querySelectorAll('.item-max-score').forEach(input => {
        input.addEventListener('input', function() {
            updateScoreLevels(this);
        });
    });

    // Remove Category
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-category')) {
            if (container.children.length <= 1) {
                alert('You must have at least one category!');
                return;
            }
            e.target.closest('.category-block').remove();
        }
    });

    // Remove Item
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-block').remove();
        }
    });
});
</script>
@endsection
