@extends('organizer.layouts.app')

@section('title', ($eventType ?? null ? ucfirst($eventType) . ' ' : '') . 'Jury Mapping - ' . $event->title)
@section('page-title', ($eventType ?? null ? ucfirst($eventType) . ' ' : '') . 'Jury Mapping - ' . $event->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('organizer.jury-mapping.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    All Events
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $event->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header with Stats -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        <svg class="inline-block h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('F d, Y') : 'Date TBD' }}
                        • 
                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $event->delivery_mode)) }}</span>
                    </p>
                </div>
                <div>
                    @if($stats['total_participants'] > 0 && $stats['total_reviewers'] > 0)
                        <form action="{{ route('organizer.jury-mapping.auto-assign', $event) }}" method="POST" class="inline" onsubmit="return confirm('Auto-assign {{ $eventType === 'innovation' ? 'jury' : 'reviewers' }} to all participants? This will assign at least 2 {{ $eventType === 'innovation' ? 'jury members' : 'reviewers' }} per participant based on category{{ $eventType === 'innovation' ? ' and theme' : '' }} constraints.');">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Auto-Assign
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['total_participants'] }}</div>
                    <div class="text-sm text-indigo-900 mt-1">Total Participants</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['total_reviewers'] }}</div>
                    <div class="text-sm text-green-900 mt-1">Total {{ $eventType === 'innovation' ? 'Jury' : 'Reviewers' }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_mappings'] }}</div>
                    <div class="text-sm text-blue-900 mt-1">Total Assignments</div>
                </div>
                <div class="bg-{{ $stats['total_participants'] - $stats['participants_with_reviewer'] > 0 ? 'red' : 'green' }}-50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-{{ $stats['total_participants'] - $stats['participants_with_reviewer'] > 0 ? 'red' : 'green' }}-600">
                        {{ $stats['participants_with_reviewer'] }}/{{ $stats['total_participants'] }}
                    </div>
                    <div class="text-sm text-{{ $stats['total_participants'] - $stats['participants_with_reviewer'] > 0 ? 'red' : 'green' }}-900 mt-1">Participants Mapped</div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Participants List -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Participants</h3>
                    <p class="text-sm text-gray-500 mt-1">Assign {{ $eventType === 'innovation' ? 'jury members' : 'reviewers' }} to each participant</p>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($participants as $participant)
                        <div class="px-6 py-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                                            {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $participant->user->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $participant->user->email }}</p>
                                        </div>
                                    </div>
                                    @if($participant->selected_category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            📚 {{ $participant->selected_category }}
                                        </span>
                                    @endif
                                    
                                    <!-- Assigned Reviewers -->
                                    <div class="mt-3">
                                        @if($participant->juryMappingsAsParticipant->isNotEmpty())
                                            <div class="text-xs text-gray-500 mb-2">Assigned {{ $eventType === 'innovation' ? 'Jury' : 'Reviewers' }} ({{ $participant->juryMappingsAsParticipant->count() }}):</div>
                                            <div class="space-y-2">
                                                @foreach($participant->juryMappingsAsParticipant as $mapping)
                                                    <div class="flex items-center justify-between bg-green-50 rounded-lg px-3 py-2">
                                                        <div class="flex items-center flex-1">
                                                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-semibold text-xs">
                                                                {{ strtoupper(substr($mapping->reviewerRegistration->user->name, 0, 1)) }}
                                                            </div>
                                                            <div class="ml-2">
                                                                <div class="text-sm font-medium text-gray-900">{{ $mapping->reviewerRegistration->user->name }}</div>
                                                                @if($mapping->reviewerRegistration->selected_category)
                                                                    <span class="text-xs text-gray-500">{{ $mapping->reviewerRegistration->selected_category }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <form action="{{ route('organizer.jury-mapping.remove', $mapping) }}" method="POST" class="inline" onsubmit="return confirm('Remove this {{ $eventType === 'innovation' ? 'jury' : 'reviewer' }} assignment?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-xs text-red-600 bg-red-50 rounded-lg px-3 py-2">
                                                <svg class="inline-block h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                No {{ $eventType === 'innovation' ? 'jury' : 'reviewers' }} assigned
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button 
                                        onclick="showAssignModal({{ $participant->id }}, '{{ $participant->user->name }}', '{{ $participant->selected_category }}')" 
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Assign
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Participants</h3>
                            <p class="mt-1 text-sm text-gray-500">No confirmed participants for this event yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Reviewer Workload -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">{{ $eventType === 'innovation' ? 'Jury' : 'Reviewer' }} Workload</h3>
                    <p class="text-sm text-gray-500 mt-1">Current assignment distribution</p>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($reviewerWorkload as $reviewer)
                        <div class="px-6 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-semibold text-xs">
                                        {{ strtoupper(substr($reviewer->user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $reviewer->user->name }}</div>
                                        @if($reviewer->selected_category)
                                            <div class="text-xs text-gray-500">{{ $reviewer->selected_category }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold {{ $reviewer->participants_assigned == 0 ? 'text-gray-400' : ($reviewer->participants_assigned <= 3 ? 'text-green-600' : ($reviewer->participants_assigned <= 6 ? 'text-yellow-600' : 'text-red-600')) }}">
                                        {{ $reviewer->participants_assigned }}
                                    </div>
                                    <div class="text-xs text-gray-500">assigned</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No {{ $eventType === 'innovation' ? 'Jury' : 'Reviewers' }}</h3>
                            <p class="mt-1 text-sm text-gray-500">No confirmed {{ $eventType === 'innovation' ? 'jury members' : 'reviewers' }} for this event yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Reviewer Modal -->
<div id="assignModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assign {{ $eventType === 'innovation' ? 'Jury' : 'Reviewer' }}</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Participant: <strong id="participantName"></strong>
                                <span id="participantCategory" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 ml-2"></span>
                            </p>
                            <div id="eligibleReviewersList" class="space-y-2 max-h-96 overflow-y-auto">
                                <!-- Dynamically loaded reviewers -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button onclick="closeAssignModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentParticipantId = null;

function showAssignModal(participantId, participantName, participantCategory) {
    currentParticipantId = participantId;
    document.getElementById('participantName').textContent = participantName;
    document.getElementById('participantCategory').textContent = participantCategory || 'No category';
    document.getElementById('assignModal').classList.remove('hidden');
    
    // Load eligible reviewers
    const baseUrl = '{{ route("organizer.jury-mapping.show", $event) }}';
    fetch(`${baseUrl}/eligible-reviewers/${participantId}`)
        .then(response => response.json())
        .then(reviewers => {
            const list = document.getElementById('eligibleReviewersList');
            if (reviewers.length === 0) {
                list.innerHTML = '<p class="text-sm text-red-600 bg-red-50 rounded-lg px-4 py-3">No eligible {{ $eventType === 'innovation' ? 'jury members' : 'reviewers' }} found. All {{ $eventType === 'innovation' ? 'jury members' : 'reviewers' }} are either already assigned, from the same category{{ $eventType === 'innovation' ? ', from the same theme,' : '' }} or the same user.</p>';
            } else {
                list.innerHTML = reviewers.map(reviewer => `
                    <div class="flex items-center justify-between border border-gray-200 rounded-lg px-4 py-3 hover:bg-gray-50">
                        <div class="flex items-center flex-1">
                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-semibold">
                                ${reviewer.user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">${reviewer.user.name}</div>
                                <div class="text-xs text-gray-500">
                                    ${reviewer.selected_category || 'No category'} • ${reviewer.current_workload || 0} assigned
                                </div>
                            </div>
                        </div>
                        <button 
                            onclick="assignReviewer(${reviewer.id})"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Assign
                        </button>
                    </div>
                `).join('');
            }
        });
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}

function assignReviewer(reviewerId) {
    fetch(`{{ route('organizer.jury-mapping.assign', $event) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            participant_registration_id: currentParticipantId,
            reviewer_registration_id: reviewerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Failed to assign {{ $eventType === 'innovation' ? 'jury member' : 'reviewer' }}');
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
        console.error(error);
    });
}
</script>
@endsection
