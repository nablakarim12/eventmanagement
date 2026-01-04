@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('jury.papers.index') }}" class="text-purple-600 hover:text-purple-800 mb-2 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to My Assignments
            </a>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                Paper Review
            </h1>
            <p class="text-gray-600 mt-2">{{ $assignment->paperSubmission->event->title }}</p>
        </div>

        <!-- Paper Information -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">{{ $assignment->paperSubmission->title }}</h2>
                <p class="text-purple-100 text-sm mt-1">Submission Code: {{ $assignment->paperSubmission->submission_code }}</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Authors</h3>
                        <div class="space-y-1">
                            @foreach($assignment->paperSubmission->authors as $author)
                                <p class="text-gray-900">{{ $author->name }}
                                    @if($author->is_corresponding)
                                        <span class="text-xs text-purple-600">(Corresponding)</span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600">{{ $author->affiliation }}</p>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Keywords</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(json_decode($assignment->paperSubmission->keywords, true) ?? [] as $keyword)
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">{{ $keyword }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Abstract</h3>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ $assignment->paperSubmission->abstract }}</p>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('jury.papers.download', $assignment) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition duration-300 shadow">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Paper (PDF)
                    </a>
                    <span class="text-sm text-gray-600">{{ number_format($assignment->paperSubmission->file_size / 1024, 2) }} KB</span>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <form action="{{ route('jury.papers.review.store', $assignment) }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white">Your Evaluation</h2>
                    <p class="text-purple-100 text-sm mt-1">Rate each criterion based on the rubric</p>
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

                    <!-- Dynamic Criteria Scores -->
                    <div class="space-y-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-900">Evaluation Criteria</h3>
                        
                        @foreach($criteria as $criterion)
                            @php
                                $fieldName = $criterion->id ? 'score_' . $criterion->id : strtolower($criterion->name) . '_score';
                                $oldValue = old($fieldName, $review && isset(json_decode($review->custom_scores, true)[$criterion->id]) ? json_decode($review->custom_scores, true)[$criterion->id] : ($review->{$fieldName} ?? ''));
                            @endphp
                            
                            <div class="bg-gray-50 rounded-lg p-5 border-2 border-gray-200">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-gray-900 capitalize">{{ $criterion->name }}</h4>
                                        @if($criterion->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $criterion->description }}</p>
                                        @endif
                                    </div>
                                    <div class="ml-4 text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                                            Max: {{ $criterion->max_score }} pts
                                        </span>
                                        @if($criterion->weight > 1)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 ml-2">
                                                Weight: ×{{ $criterion->weight }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Score Input with Range Slider -->
                                <div class="flex items-center gap-4">
                                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Your Score:</label>
                                    <input type="number" 
                                           name="{{ $fieldName }}" 
                                           value="{{ $oldValue }}"
                                           class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-center font-semibold"
                                           min="0" 
                                           max="{{ $criterion->max_score }}"
                                           step="0.5"
                                           required
                                           onchange="updateScoreBar(this, {{ $criterion->max_score }})">
                                    <span class="text-sm text-gray-500">/ {{ $criterion->max_score }}</span>
                                    
                                    <!-- Visual Score Bar -->
                                    <div class="flex-1 bg-gray-200 rounded-full h-3 ml-4 overflow-hidden">
                                        <div class="score-bar bg-gradient-to-r from-purple-500 to-blue-500 h-3 rounded-full transition-all duration-300" 
                                             style="width: {{ $oldValue ? ($oldValue / $criterion->max_score * 100) : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Qualitative Feedback -->
                    <div class="space-y-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-900">Qualitative Feedback</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Strengths *
                                <span class="text-xs text-gray-500">(What are the strong points of this submission?)</span>
                            </label>
                            <textarea name="strengths" 
                                      rows="4"
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                      placeholder="Highlight the key strengths and positive aspects of this submission...">{{ old('strengths', $review->strengths ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Weaknesses *
                                <span class="text-xs text-gray-500">(What areas need improvement?)</span>
                            </label>
                            <textarea name="weaknesses" 
                                      rows="4"
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                      placeholder="Identify areas that need improvement or clarification...">{{ old('weaknesses', $review->weaknesses ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                General Comments
                                <span class="text-xs text-gray-500">(Optional - Additional feedback for authors)</span>
                            </label>
                            <textarea name="comments" 
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                      placeholder="Additional comments, suggestions, or observations...">{{ old('comments', $review->comments ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confidential Comments for Organizers
                                <span class="text-xs text-gray-500">(Only organizers can see this - not shared with authors)</span>
                            </label>
                            <textarea name="confidential_comments" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none bg-yellow-50"
                                      placeholder="Private notes for event organizers...">{{ old('confidential_comments', $review->confidential_comments ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Recommendation -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Final Recommendation *
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-green-500 transition duration-200 {{ old('recommendation', $review->recommendation ?? '') == 'accept' ? 'border-green-500 bg-green-50' : 'border-gray-300 bg-white' }}">
                                <input type="radio" 
                                       name="recommendation" 
                                       value="accept" 
                                       {{ old('recommendation', $review->recommendation ?? '') == 'accept' ? 'checked' : '' }}
                                       class="mr-3"
                                       required>
                                <div>
                                    <div class="font-semibold text-green-700">✅ Accept</div>
                                    <div class="text-xs text-gray-600">Ready for publication</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-yellow-500 transition duration-200 {{ old('recommendation', $review->recommendation ?? '') == 'minor_revision' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300 bg-white' }}">
                                <input type="radio" 
                                       name="recommendation" 
                                       value="minor_revision" 
                                       {{ old('recommendation', $review->recommendation ?? '') == 'minor_revision' ? 'checked' : '' }}
                                       class="mr-3"
                                       required>
                                <div>
                                    <div class="font-semibold text-yellow-700">📝 Minor Revision</div>
                                    <div class="text-xs text-gray-600">Small changes needed</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-orange-500 transition duration-200 {{ old('recommendation', $review->recommendation ?? '') == 'major_revision' ? 'border-orange-500 bg-orange-50' : 'border-gray-300 bg-white' }}">
                                <input type="radio" 
                                       name="recommendation" 
                                       value="major_revision" 
                                       {{ old('recommendation', $review->recommendation ?? '') == 'major_revision' ? 'checked' : '' }}
                                       class="mr-3"
                                       required>
                                <div>
                                    <div class="font-semibold text-orange-700">🔄 Major Revision</div>
                                    <div class="text-xs text-gray-600">Significant changes</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-red-500 transition duration-200 {{ old('recommendation', $review->recommendation ?? '') == 'reject' ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-white' }}">
                                <input type="radio" 
                                       name="recommendation" 
                                       value="reject" 
                                       {{ old('recommendation', $review->recommendation ?? '') == 'reject' ? 'checked' : '' }}
                                       class="mr-3"
                                       required>
                                <div>
                                    <div class="font-semibold text-red-700">❌ Reject</div>
                                    <div class="text-xs text-gray-600">Not suitable</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('jury.papers.index') }}" 
                           class="px-6 py-3 border-2 border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition duration-300">
                            Cancel
                        </a>
                        <div class="flex gap-3">
                            <button type="submit" 
                                    name="save_as" 
                                    value="draft"
                                    class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition duration-300 shadow">
                                Save as Draft
                            </button>
                            <button type="submit" 
                                    name="save_as" 
                                    value="submit"
                                    class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 shadow-lg hover:shadow-xl inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Submit Review
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Help Info -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <p class="text-sm text-blue-800">
                <strong>Note:</strong> You can save your review as a draft and return to complete it later. Once submitted, you cannot edit it. Please ensure all scores and feedback are accurate before submitting.
            </p>
        </div>
    </div>
</div>

<script>
function updateScoreBar(input, maxScore) {
    const value = parseFloat(input.value) || 0;
    const percentage = (value / maxScore) * 100;
    const scoreBar = input.closest('.bg-gray-50').querySelector('.score-bar');
    if (scoreBar) {
        scoreBar.style.width = percentage + '%';
    }
}

// Initialize score bars on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[type="number"][name^="score_"]').forEach(input => {
        const maxScore = parseFloat(input.getAttribute('max'));
        updateScoreBar(input, maxScore);
    });
});
</script>
@endsection
