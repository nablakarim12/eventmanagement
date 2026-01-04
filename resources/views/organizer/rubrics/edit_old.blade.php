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
                    <h2 class="text-2xl font-bold text-white">Evaluation Criteria</h2>
                    <p class="text-purple-100 text-sm mt-1">Add, edit, or remove criteria. Drag to reorder.</p>
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

                    <!-- Criteria Container -->
                    <div id="criteriaContainer" class="space-y-4">
                        @forelse($criteria as $index => $criterion)
                            <div class="criteria-row bg-gray-50 border-2 border-gray-200 rounded-lg p-4 hover:border-purple-300 transition duration-200" data-index="{{ $index }}">
                                <div class="flex items-start gap-4">
                                    <!-- Drag Handle -->
                                    <div class="drag-handle cursor-move pt-2 text-gray-400 hover:text-purple-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                        </svg>
                                    </div>

                                    <!-- Order Number -->
                                    <div class="flex-shrink-0 pt-2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-semibold order-number">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>

                                    <!-- Fields -->
                                    <div class="flex-1 grid grid-cols-12 gap-4">
                                        <!-- Criteria Name -->
                                        <div class="col-span-12 md:col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Criteria Name *</label>
                                            <input type="text" 
                                                   name="criteria[{{ $index }}][name]" 
                                                   value="{{ old("criteria.{$index}.name", $criterion->name) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                   placeholder="e.g., Innovation, Quality, Impact"
                                                   required>
                                        </div>

                                        <!-- Max Score -->
                                        <div class="col-span-6 md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Score *</label>
                                            <input type="number" 
                                                   name="criteria[{{ $index }}][max_score]" 
                                                   value="{{ old("criteria.{$index}.max_score", $criterion->max_score) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                   min="1" 
                                                   max="100"
                                                   required>
                                        </div>

                                        <!-- Weight -->
                                        <div class="col-span-6 md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Weight *</label>
                                            <input type="number" 
                                                   name="criteria[{{ $index }}][weight]" 
                                                   value="{{ old("criteria.{$index}.weight", $criterion->weight) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                   min="1" 
                                                   max="10"
                                                   required>
                                        </div>

                                        <!-- Active Toggle -->
                                        <div class="col-span-12 md:col-span-3 flex items-end">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="checkbox" 
                                                       name="criteria[{{ $index }}][is_active]" 
                                                       value="1"
                                                       {{ old("criteria.{$index}.is_active", $criterion->is_active) ? 'checked' : '' }}
                                                       class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                                <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                                            </label>
                                        </div>

                                        <!-- Description -->
                                        <div class="col-span-12">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                            <textarea name="criteria[{{ $index }}][description]" 
                                                      rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                                      placeholder="Describe what this criteria evaluates..."
                                                      maxlength="1000">{{ old("criteria.{$index}.description", $criterion->description) }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="flex-shrink-0 pt-2">
                                        <button type="button" 
                                                class="remove-criteria text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition duration-200"
                                                title="Remove criterion">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- Initial Empty State - will be replaced when user adds first criterion -->
                        @endforelse
                    </div>

                    <!-- Add Criterion Button -->
                    <div class="mt-6">
                        <button type="button" 
                                id="addCriterion"
                                class="w-full bg-purple-100 hover:bg-purple-200 text-purple-700 px-6 py-4 rounded-lg font-semibold transition duration-300 border-2 border-dashed border-purple-300 hover:border-purple-400 inline-flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Criterion
                        </button>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-8 flex items-center justify-between gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('organizer.events.rubrics.index', $event) }}" 
                           class="px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition duration-300">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 shadow-lg hover:shadow-xl inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Rubric
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Help Card -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">💡 Tips for Creating Effective Rubrics</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li><strong>Clear Criteria:</strong> Use specific, measurable criteria names</li>
                <li><strong>Balanced Weights:</strong> Assign higher weights to more important criteria</li>
                <li><strong>Consistent Scales:</strong> Keep max scores consistent (e.g., all 10 or all 100)</li>
                <li><strong>Detailed Descriptions:</strong> Help reviewers understand what each criterion measures</li>
                <li><strong>3-5 Criteria:</strong> Most events work best with 3-5 evaluation criteria</li>
            </ul>
        </div>
    </div>
</div>

<!-- Criterion Template (Hidden) -->
<template id="criterionTemplate">
    <div class="criteria-row bg-gray-50 border-2 border-gray-200 rounded-lg p-4 hover:border-purple-300 transition duration-200" data-index="INDEX">
        <div class="flex items-start gap-4">
            <!-- Drag Handle -->
            <div class="drag-handle cursor-move pt-2 text-gray-400 hover:text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                </svg>
            </div>

            <!-- Order Number -->
            <div class="flex-shrink-0 pt-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-semibold order-number">
                    ORDER
                </span>
            </div>

            <!-- Fields -->
            <div class="flex-1 grid grid-cols-12 gap-4">
                <!-- Criteria Name -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Criteria Name *</label>
                    <input type="text" 
                           name="criteria[INDEX][name]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="e.g., Innovation, Quality, Impact"
                           required>
                </div>

                <!-- Max Score -->
                <div class="col-span-6 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Score *</label>
                    <input type="number" 
                           name="criteria[INDEX][max_score]" 
                           value="10"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           min="1" 
                           max="100"
                           required>
                </div>

                <!-- Weight -->
                <div class="col-span-6 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Weight *</label>
                    <input type="number" 
                           name="criteria[INDEX][weight]" 
                           value="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           min="1" 
                           max="10"
                           required>
                </div>

                <!-- Active Toggle -->
                <div class="col-span-12 md:col-span-3 flex items-end">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="criteria[INDEX][is_active]" 
                               value="1"
                               checked
                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>

                <!-- Description -->
                <div class="col-span-12">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                    <textarea name="criteria[INDEX][description]" 
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                              placeholder="Describe what this criteria evaluates..."
                              maxlength="1000"></textarea>
                </div>
            </div>

            <!-- Remove Button -->
            <div class="flex-shrink-0 pt-2">
                <button type="button" 
                        class="remove-criteria text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition duration-200"
                        title="Remove criterion">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let criteriaCount = {{ $criteria->count() }};
    const container = document.getElementById('criteriaContainer');
    const addButton = document.getElementById('addCriterion');
    const template = document.getElementById('criterionTemplate');

    // Add new criterion
    addButton.addEventListener('click', function() {
        const newCriterion = template.content.cloneNode(true);
        const criterionDiv = newCriterion.querySelector('.criteria-row');
        
        // Replace placeholders
        criterionDiv.innerHTML = criterionDiv.innerHTML
            .replace(/INDEX/g, criteriaCount)
            .replace(/ORDER/g, criteriaCount + 1);
        
        criterionDiv.setAttribute('data-index', criteriaCount);
        container.appendChild(criterionDiv);
        criteriaCount++;
        
        updateOrderNumbers();
    });

    // Remove criterion
    container.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-criteria');
        if (removeBtn) {
            if (container.children.length <= 1) {
                alert('You must have at least one criterion!');
                return;
            }
            removeBtn.closest('.criteria-row').remove();
            reindexCriteria();
            updateOrderNumbers();
        }
    });

    // Reindex criteria after removal
    function reindexCriteria() {
        const rows = container.querySelectorAll('.criteria-row');
        rows.forEach((row, index) => {
            row.setAttribute('data-index', index);
            row.querySelectorAll('input, textarea').forEach(field => {
                const name = field.getAttribute('name');
                if (name) {
                    field.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
        });
        criteriaCount = rows.length;
    }

    // Update order numbers
    function updateOrderNumbers() {
        const rows = container.querySelectorAll('.criteria-row');
        rows.forEach((row, index) => {
            row.querySelector('.order-number').textContent = index + 1;
        });
    }

    // Form validation
    document.getElementById('rubricForm').addEventListener('submit', function(e) {
        if (container.children.length === 0) {
            e.preventDefault();
            alert('Please add at least one evaluation criterion!');
        }
    });
});
</script>
@endsection
