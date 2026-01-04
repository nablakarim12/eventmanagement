@extends('organizer.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-start justify-between">
                <div>
                    <a href="{{ route('organizer.events.show', $event) }}" class="text-purple-600 hover:text-purple-800 mb-2 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Event
                    </a>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        Evaluation Rubric
                    </h1>
                    <p class="text-gray-600 mt-2">{{ $event->title }}</p>
                </div>
                
                @if($categories->count() > 0)
                    <!-- Regenerate Button -->
                    <div class="flex gap-3">
                        <a href="{{ route('organizer.events.rubrics.edit', $event) }}" 
                           class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-lg hover:shadow-xl inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Rubric
                        </a>
                        <a href="{{ route('organizer.events.rubrics.generate-ai', $event) }}" 
                           class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-lg hover:shadow-xl inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Regenerate Rubric
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Success/Info Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-lg shadow">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-semibold">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        @if($categories->count() > 0)
            <!-- Rubric Categories Display -->
            <div class="space-y-6">
                @foreach($categories as $category)
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                        <!-- Category Header -->
                        <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold text-white">{{ $category->name }}</h2>
                                    @if($category->description)
                                        <p class="text-purple-100 text-sm mt-1">{{ $category->description }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-white">{{ $category->max_score }}</div>
                                    <div class="text-sm text-purple-100">marks</div>
                                    @if($category->weight > 1)
                                        <div class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white bg-opacity-20 text-white">
                                            Weight: ×{{ $category->weight }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Category Items -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                            No
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Item
                                        </th>
                                        @if($category->items->first() && $category->items->first()->scoreLevels->count() > 0)
                                            @php
                                                $maxLevel = $category->items->first()->scoreLevels->max('level');
                                            @endphp
                                            @for($i = 1; $i <= $maxLevel; $i++)
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ $i }}
                                                </th>
                                            @endfor
                                        @endif
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Max Score
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($category->items as $index => $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-semibold text-sm">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-gray-900">{{ $item->name }}</div>
                                                @if($item->description)
                                                    <div class="text-sm text-gray-500 mt-1">{{ $item->description }}</div>
                                                @endif
                                            </td>
                                            @if($item->scoreLevels->count() > 0)
                                                @php
                                                    $maxLevel = $item->scoreLevels->max('level');
                                                @endphp
                                                @for($level = 1; $level <= $maxLevel; $level++)
                                                    @php
                                                        $scoreLevel = $item->scoreLevels->firstWhere('level', $level);
                                                    @endphp
                                                    <td class="px-4 py-4 text-sm text-gray-700">
                                                        @if($scoreLevel)
                                                            <div class="max-w-xs">{{ $scoreLevel->description }}</div>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                @endfor
                                            @endif
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                                    {{ $item->max_score }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="{{ $category->items->first() && $category->items->first()->scoreLevels->count() > 0 ? $category->items->first()->scoreLevels->max('level') + 2 : 2 }}" class="px-6 py-3 text-right font-bold text-gray-900">
                                            Total:
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-bold bg-purple-200 text-purple-900">
                                                {{ $category->items->sum('max_score') }} / {{ $category->max_score }}
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Overall Summary -->
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
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="text-center py-16 px-6">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <h3 class="mt-4 text-2xl font-semibold text-gray-900">No Rubric Created Yet</h3>
                    <p class="mt-2 text-gray-600 max-w-md mx-auto">
                        Create evaluation criteria for jury/reviewers to assess submissions. You can use default criteria or create custom ones.
                    </p>
                    <div class="mt-8 flex justify-center gap-4">
                        <a href="{{ route('organizer.events.rubrics.generate-ai', $event) }}" 
                           class="bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white px-8 py-4 rounded-lg font-semibold transition duration-300 shadow-lg hover:shadow-xl inline-flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Generate with AI
                        </a>
                        <a href="{{ route('organizer.events.rubrics.edit', $event) }}" 
                           class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-lg font-semibold transition duration-300 shadow-lg hover:shadow-xl inline-flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Custom Rubric
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Info Card -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">About Evaluation Rubrics</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>Criteria:</strong> Define what aspects will be evaluated</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>Max Score:</strong> Set the maximum points for each criterion (1-100)</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>Weight:</strong> Assign importance to criteria (higher weight = more important)</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>Order:</strong> Criteria are displayed in the order you set</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
