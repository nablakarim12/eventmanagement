@extends('organizer.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">AI-Generated Rubric Preview</h1>
        <p class="text-gray-600 mt-1">{{ $event->event_name }}</p>
        <div class="mt-2 bg-green-50 border-l-4 border-green-500 p-4">
            <p class="text-sm text-green-700">
                ✅ AI has successfully generated your rubric! Review the structure below and confirm to save it.
            </p>
        </div>
    </div>

    <!-- Rubric Preview -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Rubric Structure</h2>

            @foreach($rubricData['categories'] as $category)
                <div class="mb-8 last:mb-0 border-l-4 border-indigo-500 pl-6">
                    <!-- Category Header -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $category['name'] }}</h3>
                                @if(isset($category['description']))
                                    <p class="text-sm text-gray-600 mt-1">{{ $category['description'] }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-indigo-600">{{ $category['max_score'] }} pts</span>
                            </div>
                        </div>
                    </div>

                    <!-- Items in Category -->
                    @foreach($category['items'] as $item)
                        <div class="mb-4 last:mb-0 bg-gray-50 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $item['name'] }}</h4>
                                    @if(isset($item['description']))
                                        <p class="text-sm text-gray-600 mt-1">{{ $item['description'] }}</p>
                                    @endif
                                </div>
                                <span class="ml-4 text-lg font-bold text-indigo-600">{{ $item['max_score'] }} pts</span>
                            </div>

                            <!-- Score Levels -->
                            @if(isset($item['score_levels']) && count($item['score_levels']) > 0)
                                <div class="ml-4 space-y-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Score Levels:</p>
                                    @foreach($item['score_levels'] as $level)
                                        <div class="flex items-start space-x-3 text-sm">
                                            <span class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full 
                                                @if($level['score'] / $item['max_score'] >= 0.8) bg-green-100 text-green-800
                                                @elseif($level['score'] / $item['max_score'] >= 0.6) bg-blue-100 text-blue-800
                                                @elseif($level['score'] / $item['max_score'] >= 0.4) bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif font-bold">
                                                {{ $level['score'] }}
                                            </span>
                                            <div class="flex-1">
                                                <span class="font-medium text-gray-900">{{ $level['label'] ?? '' }}</span>
                                                @if(isset($level['description']))
                                                    <span class="text-gray-600">- {{ $level['description'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-md p-6 text-white mb-6">
        <h3 class="text-xl font-bold mb-4">Rubric Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-indigo-100">Total Categories</p>
                <p class="text-3xl font-bold">{{ count($rubricData['categories']) }}</p>
            </div>
            <div>
                <p class="text-indigo-100">Total Items</p>
                <p class="text-3xl font-bold">
                    {{ array_sum(array_map(function($cat) { return count($cat['items']); }, $rubricData['categories'])) }}
                </p>
            </div>
            <div>
                <p class="text-indigo-100">Total Score</p>
                <p class="text-3xl font-bold">
                    {{ array_sum(array_column($rubricData['categories'], 'max_score')) }} points
                </p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-between bg-white rounded-lg shadow-md p-6">
        <div>
            <a href="{{ route('organizer.events.rubrics.generate-ai', $event->id) }}" class="text-gray-600 hover:text-gray-900">
                ← Generate Again
            </a>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('organizer.events.rubrics.index', $event->id) }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                Cancel
            </a>
            <form action="{{ route('organizer.events.rubrics.confirm-ai', $event->id) }}" method="POST" id="confirmForm" class="inline">
                @csrf
                <button type="submit" id="confirmBtn" class="px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:from-green-700 hover:to-teal-700 font-semibold shadow-lg flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Confirm & Save Rubric</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Note:</strong> After confirming, this rubric will replace any existing rubric for this event. You can always edit it manually later from the Rubric Management page.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
const form = document.getElementById('confirmForm');
const btn = document.getElementById('confirmBtn');

form.addEventListener('submit', function(e) {
    console.log('Form submitting...');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="ml-2">Saving rubric...</span>
    `;
    // Let the form submit naturally
});
</script>
@endsection
