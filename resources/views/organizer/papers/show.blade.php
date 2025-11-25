@extends('organizer.layouts.app')

@section('title', 'Paper Details - ' . $paper->submission_code)
@section('page-title', 'Paper Details & Jury Assignment')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $paper->title }}</h1>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($paper->status === 'submitted') bg-blue-100 text-blue-800
                        @elseif($paper->status === 'under_review') bg-orange-100 text-orange-800
                        @elseif($paper->status === 'reviewed') bg-green-100 text-green-800
                        @elseif($paper->status === 'accepted') bg-green-600 text-white
                        @elseif($paper->status === 'rejected') bg-red-600 text-white
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                    </span>
                </div>
                <p class="text-gray-600 mt-1">
                    <code class="bg-gray-100 px-2 py-1 rounded">{{ $paper->submission_code }}</code>
                    • Submitted: {{ $paper->submitted_at->format('M d, Y h:i A') }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('organizer.events.papers.download', [$event, $paper]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <a href="{{ route('organizer.events.papers.index', $event) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Paper Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Abstract -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Abstract</h3>
                <p class="text-gray-700 whitespace-pre-line">{{ $paper->abstract }}</p>
            </div>

            <!-- Keywords -->
            @if($paper->keywords)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Keywords</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $paper->keywords) as $keyword)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ trim($keyword) }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Authors -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Authors</h3>
                <div class="space-y-3">
                    @foreach($paper->authors as $author)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="bg-blue-100 rounded-full p-2">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <p class="font-semibold text-gray-900">{{ $author->name }}</p>
                                @if($author->is_corresponding)
                                    <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold">Corresponding</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">{{ $author->email }}</p>
                            @if($author->affiliation)
                                <p class="text-sm text-gray-500"><i class="fas fa-building mr-1"></i>{{ $author->affiliation }}</p>
                            @endif
                            @if($author->country)
                                <p class="text-sm text-gray-500"><i class="fas fa-globe mr-1"></i>{{ $author->country }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Assigned Jury & Reviews -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Assigned Jury & Reviews</h3>
                    <span class="text-sm text-gray-600">
                        {{ $paper->reviews->where('status', 'submitted')->count() }} / {{ $paper->juryAssignments->count() }} reviews completed
                    </span>
                </div>

                @if($paper->juryAssignments->count() > 0)
                    <div class="space-y-4">
                        @foreach($paper->juryAssignments as $assignment)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <p class="font-semibold text-gray-900">{{ $assignment->juryRegistration->user->name }}</p>
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold
                                            @if($assignment->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($assignment->status === 'accepted') bg-blue-100 text-blue-800
                                            @elseif($assignment->status === 'declined') bg-red-100 text-red-800
                                            @elseif($assignment->status === 'completed') bg-green-100 text-green-800
                                            @endif">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $assignment->juryRegistration->user->email }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Assigned: {{ $assignment->assigned_at->format('M d, Y h:i A') }}</p>
                                    
                                    @if($assignment->review && $assignment->review->status === 'submitted')
                                        <div class="mt-3 bg-green-50 p-3 rounded border border-green-200">
                                            <p class="text-sm font-semibold text-green-800 mb-2">
                                                <i class="fas fa-check-circle mr-1"></i>Review Submitted
                                            </p>
                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                <div><span class="font-semibold">Originality:</span> {{ $assignment->review->originality_score }}/10</div>
                                                <div><span class="font-semibold">Methodology:</span> {{ $assignment->review->methodology_score }}/10</div>
                                                <div><span class="font-semibold">Clarity:</span> {{ $assignment->review->clarity_score }}/10</div>
                                                <div><span class="font-semibold">Contribution:</span> {{ $assignment->review->contribution_score }}/10</div>
                                            </div>
                                            <div class="mt-2 pt-2 border-t border-green-300">
                                                <span class="font-semibold">Overall Score:</span> {{ number_format($assignment->review->overall_score, 2) }}/10
                                                <span class="ml-3 font-semibold">Recommendation:</span> 
                                                <span class="px-2 py-0.5 rounded text-xs font-semibold
                                                    @if($assignment->review->recommendation === 'accept') bg-green-200 text-green-900
                                                    @elseif($assignment->review->recommendation === 'reject') bg-red-200 text-red-900
                                                    @else bg-yellow-200 text-yellow-900 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $assignment->review->recommendation)) }}
                                                </span>
                                            </div>
                                            @if($assignment->review->comments)
                                                <div class="mt-2 pt-2 border-t border-green-300">
                                                    <p class="font-semibold text-xs mb-1">Comments:</p>
                                                    <p class="text-xs text-gray-700">{{ $assignment->review->comments }}</p>
                                                </div>
                                            @endif
                                            @if($assignment->review->confidential_comments)
                                                <div class="mt-2 pt-2 border-t border-green-300 bg-yellow-50 p-2 rounded">
                                                    <p class="font-semibold text-xs mb-1 text-yellow-800"><i class="fas fa-lock mr-1"></i>Confidential Comments (Organizer Only):</p>
                                                    <p class="text-xs text-gray-700">{{ $assignment->review->confidential_comments }}</p>
                                                </div>
                                            @endif>
                                        </div>
                                    @endif
                                </div>
                                
                                @if(!$assignment->review || $assignment->review->status === 'draft')
                                    <form action="{{ route('organizer.events.papers.remove-jury', [$event, $paper, $assignment]) }}" method="POST" onsubmit="return confirm('Remove this jury assignment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No jury assigned yet.</p>
                @endif
            </div>

            <!-- Final Decision (if all reviews submitted) -->
            @if($paper->juryAssignments->count() > 0 && $paper->reviews->where('status', 'submitted')->count() === $paper->juryAssignments->count())
                @if(!in_array($paper->status, ['accepted', 'rejected']))
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Final Decision</h3>
                    <p class="text-gray-600 mb-4">All jury members have submitted their reviews. You can now make a final decision on this paper.</p>
                    
                    <form action="{{ route('organizer.events.papers.update-status', [$event, $paper]) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Decision</label>
                            <select name="status" required class="w-full border-gray-300 rounded-lg">
                                <option value="">Select decision...</option>
                                <option value="accepted">Accept</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason (required if rejecting)</label>
                            <textarea name="rejection_reason" rows="3" class="w-full border-gray-300 rounded-lg" placeholder="Provide reason for your decision..."></textarea>
                        </div>
                        
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                            Submit Final Decision
                        </button>
                    </form>
                </div>
                @endif
            @endif
        </div>

        <!-- Right Column: Assign Jury -->
        <div class="space-y-6">
            <!-- Submission Info -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Submission Info</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600">Submitted by</p>
                        <p class="font-semibold">{{ $paper->user->name }}</p>
                        <p class="text-gray-500 text-xs">{{ $paper->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">File Size</p>
                        <p class="font-semibold">{{ number_format($paper->file_size / 1024, 2) }} KB</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Submitted At</p>
                        <p class="font-semibold">{{ $paper->submitted_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $paper->submitted_at->format('h:i A') }}</p>
                    </div>
                    @if($paper->average_score)
                    <div>
                        <p class="text-gray-600">Average Score</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($paper->average_score, 2) }}/10</p>
                        <p class="text-xs text-gray-500">{{ $paper->review_count }} reviews</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Assign Jury -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Assign Jury</h3>
                
                @if($availableJury->count() > 0)
                    <form action="{{ route('organizer.events.papers.assign-jury', [$event, $paper]) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Select Jury Members (Checked-in only)
                            </label>
                            <div class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                @foreach($availableJury as $jury)
                                    @php
                                        $alreadyAssigned = $paper->juryAssignments->pluck('jury_registration_id')->contains($jury->id);
                                    @endphp
                                    <label class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded {{ $alreadyAssigned ? 'opacity-50' : '' }}">
                                        <input type="checkbox" 
                                               name="jury_registration_ids[]" 
                                               value="{{ $jury->id }}"
                                               {{ $alreadyAssigned ? 'disabled' : '' }}
                                               class="mt-1">
                                        <div class="flex-1">
                                            <p class="font-semibold text-sm">{{ $jury->user->name }}</p>
                                            <p class="text-xs text-gray-600">{{ $jury->user->email }}</p>
                                            @if($jury->jury_institution)
                                                <p class="text-xs text-gray-500">{{ $jury->jury_institution }}</p>
                                            @endif
                                            @if($jury->jury_expertise_areas)
                                                <p class="text-xs text-gray-500"><i class="fas fa-brain mr-1"></i>{{ $jury->jury_expertise_areas }}</p>
                                            @endif
                                            @if($alreadyAssigned)
                                                <span class="text-xs text-green-600 font-semibold">✓ Already assigned</span>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">
                                                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                                Checked in: {{ $jury->checked_in_at->format('M d, h:i A') }}
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-user-plus mr-2"></i>Assign Selected Jury
                        </button>
                    </form>
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-user-times text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm">No jury members have checked in yet.</p>
                        <p class="text-gray-400 text-xs mt-1">Jury must scan QR code before they can be assigned.</p>
                        <a href="{{ route('organizer.attendance.event', $event) }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                            View Attendance →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
