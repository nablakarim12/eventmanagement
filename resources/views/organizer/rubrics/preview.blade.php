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
                Rubric Preview
            </h1>
            <p class="text-gray-600 mt-2">This is how jury/reviewers will see the evaluation form</p>
        </div>

        <!-- Preview Notice -->
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-lg shadow">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-semibold">Preview Mode</p>
                    <p class="text-sm">This is a read-only preview. Jury members will see this form when evaluating submissions.</p>
                </div>
            </div>
        </div>

        @if($categories->count() > 0)
            <!-- Mock Review Form -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white">Paper/Submission Evaluation</h2>
                    <p class="text-purple-100 text-sm mt-1">Sample Submission Title - How Jury Will Score</p>
                </div>

                <div class="p-6">
                    <!-- Evaluation by Categories -->
                    @foreach($categories as $category)
                        <div class="mb-8 bg-purple-50 rounded-xl p-6 border-2 border-purple-200">
                            <!-- Category Header -->
                            <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-purple-300">
                                <h3 class="text-2xl font-bold text-purple-900">{{ $category->name }}</h3>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-purple-700">{{ $category->max_score }} marks</div>
                                    @if($category->weight > 1)
                                        <div class="text-sm text-purple-600">Weight: ×{{ $category->weight }}</div>
                                    @endif
                                </div>
                            </div>

                            @if($category->description)
                                <p class="text-sm text-gray-700 mb-4 italic">{{ $category->description }}</p>
                            @endif

                            <!-- Matrix Table -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                                    <thead class="bg-purple-100">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-700 border-b border-r border-gray-300" style="width: 25%;">
                                                Item
                                            </th>
                                            @if($category->items->first() && $category->items->first()->scoreLevels->count() > 0)
                                                @php
                                                    $maxLevel = $category->items->first()->scoreLevels->max('level');
                                                @endphp
                                                @for($level = 1; $level <= $maxLevel; $level++)
                                                    <th class="px-4 py-3 text-center text-sm font-bold text-gray-700 border-b border-r border-gray-300">
                                                        {{ $level }}
                                                    </th>
                                                @endfor
                                            @endif
                                            <th class="px-4 py-3 text-center text-sm font-bold text-gray-700 border-b border-gray-300">
                                                Score
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->items as $index => $item)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 border-b border-r border-gray-300">
                                                    <div class="font-semibold text-gray-900">{{ $item->name }}</div>
                                                    @if($item->description)
                                                        <div class="text-xs text-gray-600 mt-1">{{ $item->description }}</div>
                                                    @endif
                                                    <div class="text-xs text-purple-700 mt-1">Max: {{ $item->max_score }} pts</div>
                                                </td>
                                                @if($item->scoreLevels->count() > 0)
                                                    @php
                                                        $maxLevel = $item->scoreLevels->max('level');
                                                    @endphp
                                                    @for($level = 1; $level <= $maxLevel; $level++)
                                                        @php
                                                            $scoreLevel = $item->scoreLevels->firstWhere('level', $level);
                                                        @endphp
                                                        <td class="px-3 py-3 text-xs text-gray-700 border-b border-r border-gray-300 align-top">
                                                            <label class="flex items-start cursor-pointer hover:bg-purple-50 p-2 rounded">
                                                                <input type="radio" 
                                                                       name="item_{{ $index }}_score" 
                                                                       value="{{ $level }}"
                                                                       disabled
                                                                       class="mt-1 mr-2 text-purple-600">
                                                                <span>{{ $scoreLevel ? $scoreLevel->description : '-' }}</span>
                                                            </label>
                                                        </td>
                                                    @endfor
                                                @endif
                                                <td class="px-4 py-3 border-b border-gray-300 text-center">
                                                    <input type="number" 
                                                           disabled
                                                           min="0"
                                                           max="{{ $item->max_score }}"
                                                           class="w-16 px-2 py-1 border border-gray-300 rounded text-center font-semibold"
                                                           placeholder="0">
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="bg-purple-100 font-bold">
                                            <td colspan="{{ $category->items->first() && $category->items->first()->scoreLevels->count() > 0 ? $category->items->first()->scoreLevels->max('level') + 1 : 1 }}" 
                                                class="px-4 py-3 text-right border-t-2 border-gray-400">
                                                Category Total:
                                            </td>
                                            <td class="px-4 py-3 text-center text-lg border-t-2 border-gray-400">
                                                <span class="text-purple-900">0 / {{ $category->max_score }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <!-- Overall Score Display -->
                    <div class="mt-8 bg-gradient-to-r from-purple-100 to-blue-100 rounded-xl p-6 border-2 border-purple-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Total Score</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Sum of all categories
                                    @if($categories->some(fn($c) => $c->weight > 1))
                                        (with weights applied)
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-5xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                    0
                                </div>
                                <div class="text-sm text-gray-700 font-semibold">
                                    out of {{ $categories->sum('max_score') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="mt-8 space-y-4">
                        <h3 class="text-xl font-bold text-gray-900">Feedback & Comments</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">General Comments</label>
                            <textarea disabled
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white"
                                      placeholder="Provide feedback for the participants..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confidential Comments 
                                <span class="text-xs text-gray-500">(Only organizers can see)</span>
                            </label>
                            <textarea disabled
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white"
                                      placeholder="Private comments for organizers..."></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons (Disabled) -->
                    <div class="mt-8 flex justify-end gap-4 pt-6 border-t border-gray-200">
                        <button disabled class="px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-400 bg-gray-100 cursor-not-allowed">
                            Save as Draft
                        </button>
                        <button disabled class="bg-gray-400 text-white px-8 py-3 rounded-lg font-semibold cursor-not-allowed">
                            Submit Review
                        </button>
                    </div>
                </div>
            </div>

            <!-- Rubric Summary -->
            <div class="mt-8 bg-white rounded-xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Rubric Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $categories->count() }}</div>
                        <div class="text-sm text-gray-600 mt-1">Categories</div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $categories->sum(fn($c) => $c->items->count()) }}</div>
                        <div class="text-sm text-gray-600 mt-1">Total Items</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $categories->sum('max_score') }}</div>
                        <div class="text-sm text-gray-600 mt-1">Total Marks</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-orange-600">{{ $categories->where('is_active', true)->count() }}</div>
                        <div class="text-sm text-gray-600 mt-1">Active Categories</div>
                    </div>
                </div>
            </div>
        @else
            <!-- No Criteria -->
            <div class="bg-white rounded-xl shadow-xl p-12 text-center">
                <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-4 text-xl font-semibold text-gray-900">No Criteria to Preview</h3>
                <p class="mt-2 text-gray-600">Please add evaluation criteria first.</p>
                <a href="{{ route('organizer.events.rubrics.edit', $event) }}" 
                   class="mt-4 inline-block bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-300">
                    Create Rubric
                </a>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-6 text-center">
            <a href="{{ route('organizer.events.rubrics.index', $event) }}" 
               class="inline-flex items-center px-6 py-3 bg-white border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition duration-300 shadow">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Rubric Management
            </a>
        </div>
    </div>
</div>
@endsection
