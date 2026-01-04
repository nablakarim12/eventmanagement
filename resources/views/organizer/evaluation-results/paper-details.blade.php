@extends('organizer.layouts.app')

@section('title', ($eventType ?? null ? ucfirst($eventType) . ' ' : '') . 'Paper Evaluation Details')
@section('page-title', ($eventType ?? null ? ucfirst($eventType) . ' ' : '') . 'Paper Evaluation Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('organizer.evaluation-results.show-event', $event->id) }}" class="text-indigo-600 hover:text-indigo-800 mb-2 inline-block">
                    ← Back to Event Results
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->event_name }}</h1>
                <p class="text-gray-600 mt-1">Detailed Evaluation Breakdown</p>
            </div>
        </div>
    </div>

    <!-- Paper Information Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Paper Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Title</p>
                <p class="text-lg font-semibold text-gray-900">{{ $paper->title ?? 'Untitled' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Author</p>
                <p class="text-lg font-semibold text-gray-900">{{ $paper->author_name ?? 'Unknown' }}</p>
                @if($paper->author_email)
                    <p class="text-sm text-gray-600">{{ $paper->author_email }}</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500">Paper ID</p>
                <p class="text-lg font-semibold text-gray-900">#{{ $paper->id }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Evaluators</p>
                <p class="text-lg font-semibold text-gray-900">{{ count($evaluatorScores) }} Jury Members</p>
            </div>
            @if($eventType === 'innovation')
            <div>
                <p class="text-sm text-gray-500">Category</p>
                <p class="text-lg font-semibold text-gray-900">{{ $paper->selected_category ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Theme</p>
                <p class="text-lg font-semibold text-gray-900">{{ $paper->selected_theme ?? 'N/A' }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Award Assignment Section (Innovation Only) -->
    @if($eventType === 'innovation' && isset($awards) && $awards->count() > 0)
    <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl shadow-xl p-6 mb-6 border border-amber-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-trophy text-amber-600 mr-3"></i>
                    Award Assignment
                </h2>
                <p class="text-gray-600 mt-1">Assign awards to this participant</p>
            </div>
        </div>

        @php
            $userAwards = isset($participantAwards) ? $participantAwards->where('user_id', $paper->user_id) : collect();
        @endphp

        @if($userAwards->count() > 0)
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Current Awards:</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($userAwards as $pa)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium text-white shadow-md" 
                      style="background-color: {{ $pa->award->color }};">
                    <i class="fas fa-medal mr-2"></i>{{ $pa->award->name }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        <button onclick="openAwardModal()" 
                class="bg-gradient-to-r from-amber-600 to-yellow-600 text-white px-6 py-3 rounded-lg hover:from-amber-700 hover:to-yellow-700 transition-all shadow-md font-semibold">
            <i class="fas fa-trophy mr-2"></i>{{ $userAwards->count() > 0 ? 'Manage Awards' : 'Assign Awards' }}
        </button>
    </div>
    @endif

    <!-- Individual Jury Evaluations -->
    @foreach($evaluatorScores as $evaluatorId => $evaluatorData)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Jury Header -->
            <div class="border-b pb-4 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $evaluatorData['name'] }}</h3>
                        <p class="text-sm text-gray-600">{{ $evaluatorData['email'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total Score</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ number_format($evaluatorData['total_score'], 1) }}</p>
                    </div>
                </div>
            </div>

            <!-- Scores by Category -->
            @foreach($categories as $category)
                <div class="mb-6 last:mb-0">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 mb-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-bold text-gray-900">{{ $category->name }}</h4>
                            <span class="text-lg font-bold text-indigo-600">
                                {{ number_format($evaluatorData['category_totals'][$category->id] ?? 0, 1) }} / {{ $category->max_score }}
                            </span>
                        </div>
                    </div>

                    <!-- Items in this category -->
                    @if(isset($rubricItems[$category->id]))
                        <div class="space-y-3 ml-4">
                            @foreach($rubricItems[$category->id] as $item)
                                @php
                                    $scoreData = $evaluatorData['scores'][$item->id] ?? null;
                                @endphp
                                <div class="border-l-4 border-indigo-300 pl-4 py-2">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                                            @if($item->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $item->description }}</p>
                                            @endif
                                            @if($scoreData && $scoreData->comment)
                                                <div class="mt-2 bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                                                    <p class="text-sm font-medium text-yellow-800">Comment:</p>
                                                    <p class="text-sm text-yellow-700">{{ $scoreData->comment }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 text-right">
                                            @if($scoreData)
                                                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-bold
                                                    @if(($scoreData->score / $item->max_score) >= 0.8) bg-green-100 text-green-800
                                                    @elseif(($scoreData->score / $item->max_score) >= 0.6) bg-blue-100 text-blue-800
                                                    @elseif(($scoreData->score / $item->max_score) >= 0.4) bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $scoreData->score }} / {{ $item->max_score }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">Not scored</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    @if(count($evaluatorScores) === 0)
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No Evaluations Yet</h3>
            <p class="mt-1 text-gray-500">This paper has not been evaluated by any jury members yet.</p>
        </div>
    @endif

    <!-- Summary Section -->
    @if(count($evaluatorScores) > 0)
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-md p-6 text-white mb-6">
            <h3 class="text-xl font-bold mb-4">Evaluation Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-indigo-100">Total Evaluators</p>
                    <p class="text-3xl font-bold">{{ count($evaluatorScores) }}</p>
                </div>
                <div>
                    <p class="text-indigo-100">Average Score</p>
                    <p class="text-3xl font-bold">
                        {{ number_format($calculatedAverageScore, 1) }}
                    </p>
                </div>
                <div>
                    <p class="text-indigo-100">Score Range</p>
                    <p class="text-3xl font-bold">
                        {{ number_format(collect($evaluatorScores)->min('total_score'), 1) }} - 
                        {{ number_format(collect($evaluatorScores)->max('total_score'), 1) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Presentation Decision Section -->
        @if($eventType === 'conference')
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Presentation Decision</h3>
                
                @if($paper->presentation_status === 'selected')
                <!-- Already Approved -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <h4 class="text-lg font-bold text-green-900">Approved for Presentation</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        @if($paper->presentation_queue)
                            <div>
                                <p class="text-green-700 font-semibold">Queue Number:</p>
                                <p class="text-green-900">#{{ $paper->presentation_queue }}</p>
                            </div>
                        @endif
                        @if($paper->presentation_time)
                            <div>
                                <p class="text-green-700 font-semibold">Presentation Time:</p>
                                <p class="text-green-900">{{ \Carbon\Carbon::parse($paper->presentation_time)->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                        @if($paper->presentation_location)
                            <div>
                                <p class="text-green-700 font-semibold">Location:</p>
                                <p class="text-green-900">{{ $paper->presentation_location }}</p>
                            </div>
                        @endif
                        @if($paper->presentation_link)
                            <div>
                                <p class="text-green-700 font-semibold">Meeting Link:</p>
                                <a href="{{ $paper->presentation_link }}" target="_blank" class="text-green-600 hover:text-green-800 underline">Join Meeting</a>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($paper->presentation_status === 'rejected')
                <!-- Already Rejected -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <h4 class="text-lg font-bold text-red-900">Not Selected for Presentation</h4>
                    </div>
                    @if($paper->rejection_reason)
                        <div class="text-sm">
                            <p class="text-red-700 font-semibold mb-2">Reason:</p>
                            <p class="text-red-900">{{ $paper->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            @else
                <!-- Decision Pending -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-yellow-800 font-semibold">Awaiting Decision</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Approve Button -->
                    <button onclick="showApproveModal()" class="flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve for Presentation
                    </button>

                    <!-- Reject Button -->
                    <button onclick="showRejectModal()" class="flex items-center justify-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Presentation
                    </button>
                </div>
            @endif
            </div>
        @endif
    @endif
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Approve Participant for Presentation</h3>
            <button onclick="closeModals()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('organizer.evaluation-results.approve-presentation', [$event->id, $paper->id]) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-4">
                    <strong>{{ $paper->author_name }}</strong> scored an average of 
                    <strong class="text-green-600">{{ number_format($calculatedAverageScore, 1) }}</strong> 
                    from {{ count($evaluatorScores) }} reviewers.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Queue Number (Optional)</label>
                    <input type="number" name="presentation_queue" min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                           placeholder="e.g., 1, 2, 3...">
                    <p class="text-xs text-gray-500 mt-1">Presentation order</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Presentation Time (Optional)</label>
                    <input type="datetime-local" name="presentation_time" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            @if(in_array($event->delivery_mode, ['online', 'hybrid']))
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Meeting Link (Optional)</label>
                <input type="url" name="presentation_link" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                       placeholder="https://zoom.us/j/... or Google Meet link">
                <p class="text-xs text-gray-500 mt-1">For online/hybrid events</p>
            </div>
            @endif

            @if(in_array($event->delivery_mode, ['face_to_face', 'hybrid']))
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Location (Optional)</label>
                <input type="text" name="presentation_location" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                       placeholder="e.g., Room 301, Main Hall">
                <p class="text-xs text-gray-500 mt-1">For on-site/hybrid events</p>
            </div>
            @endif

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModals()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                    ✓ Approve & Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Reject Presentation</h3>
            <button onclick="closeModals()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('organizer.evaluation-results.reject-presentation', [$event->id, $paper->id]) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-4">
                    <strong>{{ $paper->author_name }}</strong> scored an average of 
                    <strong class="text-orange-600">{{ number_format($calculatedAverageScore, 1) }}</strong> 
                    from {{ count($evaluatorScores) }} reviewers.
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason <span class="text-red-600">*</span></label>
                <textarea name="rejection_reason" rows="5" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                          placeholder="Provide constructive feedback for the participant..."></textarea>
                <p class="text-xs text-gray-500 mt-1">This will be sent to the participant via email</p>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModals()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                    ✗ Reject & Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeModals() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modal on outside click
window.onclick = function(event) {
    const approveModal = document.getElementById('approveModal');
    const rejectModal = document.getElementById('rejectModal');
    if (event.target === approveModal) {
        closeModals();
    }
    if (event.target === rejectModal) {
        closeModals();
    }
}
</script>

<!-- Award Assignment Modal (Innovation Only) -->
@if($eventType === 'innovation' && isset($awards))
<div id="awardModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $paper->author_name ?? 'Unknown' }}</h3>
                <p class="text-sm text-gray-600">
                    Score: <span class="font-semibold">{{ number_format($calculatedAverageScore, 1) }}</span> | 
                    Category: <span class="font-semibold">{{ $paper->selected_category ?? 'N/A' }}</span>
                </p>
            </div>
            <button onclick="closeAwardModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <div class="mb-6">
            <h4 class="font-semibold text-gray-700 mb-3">Select Awards (can select multiple):</h4>
            <div class="grid grid-cols-2 gap-3">
                @foreach($awards as $award)
                <label class="cursor-pointer">
                    <input type="checkbox" name="awards[]" value="{{ $award->id }}" 
                           class="award-checkbox hidden" data-award-name="{{ $award->name }}">
                    <div class="award-card border-2 rounded-lg p-4 hover:shadow-md transition-all" 
                         style="border-color: {{ $award->color }}20;">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" 
                                 style="background-color: {{ $award->color }};">
                                {{ $award->rank }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">{{ $award->name }}</div>
                                @if($award->display_name)
                                <div class="text-xs text-gray-500">{{ $award->display_name }}</div>
                                @endif
                            </div>
                            <i class="fas fa-check text-2xl award-check hidden" style="color: {{ $award->color }};"></i>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <button onclick="closeAwardModal()" 
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                Cancel
            </button>
            <button onclick="saveAwards()" 
                    class="bg-gradient-to-r from-amber-600 to-yellow-600 text-white px-6 py-2 rounded-lg hover:from-amber-700 hover:to-yellow-700 transition-all">
                <i class="fas fa-save mr-2"></i>Save Awards
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Award Management v2.0
function openAwardModal() {
    console.log('Opening award modal - v2.0');
    
    // Reset checkboxes
    document.querySelectorAll('.award-checkbox').forEach(cb => {
        cb.checked = false;
        cb.closest('label').querySelector('.award-card').style.backgroundColor = '';
        cb.closest('label').querySelector('.award-check').classList.add('hidden');
    });
    
    // Load existing awards for this participant
    fetch(`/organizer/events/{{ $event->id }}/awards/participant/{{ $paper->user_id }}`)
        .then(response => response.json())
        .then(data => {
            console.log('Loaded existing awards:', data);
            if (data.awards) {
                data.awards.forEach(awardId => {
                    const checkbox = document.querySelector(`input[value="${awardId}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.closest('label').querySelector('.award-card').style.backgroundColor = '#FEF3C7';
                        checkbox.closest('label').querySelector('.award-check').classList.remove('hidden');
                    }
                });
            }
        })
        .catch(error => console.error('Error loading awards:', error));
    
    document.getElementById('awardModal').classList.remove('hidden');
}

function closeAwardModal() {
    console.log('Closing award modal');
    document.getElementById('awardModal').classList.add('hidden');
}

// Toggle award selection
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up award checkboxes - v2.0');
    document.querySelectorAll('.award-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const card = this.closest('label').querySelector('.award-card');
            const check = this.closest('label').querySelector('.award-check');
            
            if (this.checked) {
                card.style.backgroundColor = '#FEF3C7';
                check.classList.remove('hidden');
            } else {
                card.style.backgroundColor = '';
                check.classList.add('hidden');
            }
        });
    });
});

function saveAwards() {
    console.log('Save awards clicked - v2.0');
    
    const selectedAwards = Array.from(document.querySelectorAll('.award-checkbox:checked'))
        .map(cb => cb.value);
    
    console.log('Selected awards:', selectedAwards);
    
    const url = '/organizer/events/{{ $event->id }}/awards/assign-multiple';
    console.log('Fetching URL:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            user_id: {{ $paper->user_id }},
            paper_id: {{ $paper->id }},
            award_ids: selectedAwards
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('Awards saved successfully!');
            location.reload();
        } else {
            alert('Error saving awards: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving awards: ' + error.message);
    });
}
</script>
@endpush
@endif

@endsection
