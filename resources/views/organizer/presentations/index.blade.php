@extends('organizer.layouts.app')

@section('title', 'Presentation Selection - ' . $event->title)
@section('page-title', 'Presentation Selection')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('organizer.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Dashboard
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
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">📊 Presentation Selection</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
                    <p class="text-sm text-gray-500">
                        {{ ucfirst(str_replace('_', ' ', $event->delivery_mode)) }} • 
                        {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <button onclick="document.getElementById('autoSelectModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Auto-Select by Score
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_participants'] }}</div>
                    <div class="text-sm text-blue-900 mt-1">Total Participants</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['reviewed_participants'] }}</div>
                    <div class="text-sm text-green-900 mt-1">Reviewed</div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_review'] }}</div>
                    <div class="text-sm text-yellow-900 mt-1">Pending Review</div>
                </div>
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['selected'] }}</div>
                    <div class="text-sm text-indigo-900 mt-1">Selected</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
                    <div class="text-sm text-red-900 mt-1">Rejected</div>
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

    <!-- Participants List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Participants with Review Scores</h3>
            <p class="text-sm text-gray-500 mt-1">Select participants to present based on their review scores</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Reviews</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Score</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Queue</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($participants as $index => $participant)
                        <tr class="hover:bg-gray-50 {{ $participant->presentation_status === 'selected' ? 'bg-green-50' : ($participant->presentation_status === 'rejected' ? 'bg-red-50' : '') }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                                        {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $participant->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $participant->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($participant->selected_category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $participant->selected_category }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <span class="text-gray-900 font-medium">{{ $participant->completed_reviews }}</span>
                                <span class="text-gray-400">/{{ $participant->review_count }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($participant->calculated_average !== null)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold 
                                        @if($participant->calculated_average >= 80) bg-green-100 text-green-800
                                        @elseif($participant->calculated_average >= 60) bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ number_format($participant->calculated_average, 1) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">No reviews</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($participant->presentation_status === 'selected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✓ Selected
                                    </span>
                                @elseif($participant->presentation_status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ✗ Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        ⏳ Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                @if($participant->presentation_queue)
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-800 font-bold">
                                        #{{ $participant->presentation_queue }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="showActionModal({{ $participant->id }}, '{{ $participant->user->name }}', {{ $participant->calculated_average ?? 0 }}, '{{ $participant->presentation_status }}', {{ $participant->presentation_queue ?? 'null' }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <svg class="h-5 w-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @if($participant->presentation_status === 'selected')
                                    <button onclick="showDetailsModal({{ $participant->id }}, '{{ $participant->user->name }}', '{{ $event->delivery_mode }}', {{ $participant->presentation_queue ?? 'null' }}, '{{ $participant->presentation_time ? $participant->presentation_time : '' }}', '{{ $participant->presentation_link ?? '' }}', '{{ $participant->presentation_location ?? '' }}')" class="text-blue-600 hover:text-blue-900">
                                        <svg class="h-5 w-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No Participants</h3>
                                <p class="mt-1 text-sm text-gray-500">No confirmed participants for this event yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Action Modal (Select/Reject) -->
<div id="actionModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="actionForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="actionModalTitle"></h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Participant</label>
                        <p id="actionParticipantName" class="text-sm text-gray-900 font-medium"></p>
                        <p class="text-xs text-gray-500">Average Score: <span id="actionScore" class="font-bold"></span></p>
                    </div>

                    <div id="selectFields" class="space-y-4">
                        <div>
                            <label for="presentation_queue" class="block text-sm font-medium text-gray-700">Queue Number (Optional)</label>
                            <input type="number" name="presentation_queue" id="presentation_queue" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div id="rejectFields" class="hidden space-y-4">
                        <div>
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason (Required)</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Please provide feedback to the participant..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" onclick="submitAction('select')" id="btnSelect" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:w-auto sm:text-sm">
                        ✓ Select for Presentation
                    </button>
                    <button type="button" onclick="submitAction('reject')" id="btnReject" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:w-auto sm:text-sm">
                        ✗ Reject
                    </button>
                    <button type="button" onclick="closeActionModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Details Modal (For selected participants) -->
<div id="detailsModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="detailsForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Update Presentation Details</h3>
                    
                    <div class="mb-4">
                        <p id="detailsParticipantName" class="text-sm text-gray-900 font-medium"></p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="details_queue" class="block text-sm font-medium text-gray-700">Queue Number</label>
                            <input type="number" name="presentation_queue" id="details_queue" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="details_time" class="block text-sm font-medium text-gray-700">Presentation Time</label>
                            <input type="datetime-local" name="presentation_time" id="details_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div id="linkField">
                            <label for="details_link" class="block text-sm font-medium text-gray-700">Online Meeting Link</label>
                            <input type="url" name="presentation_link" id="details_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://meet.google.com/...">
                        </div>
                        <div id="locationField">
                            <label for="details_location" class="block text-sm font-medium text-gray-700">Physical Location</label>
                            <input type="text" name="presentation_location" id="details_location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Room 301, Building A">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">
                        Save Details
                    </button>
                    <button type="button" onclick="closeDetailsModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Auto-Select Modal -->
<div id="autoSelectModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('organizer.presentations.auto-select', $event) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Auto-Select by Minimum Score</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="min_score" class="block text-sm font-medium text-gray-700">Minimum Score Threshold</label>
                            <input type="number" name="min_score" id="min_score" min="0" max="100" step="0.1" value="70" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Only participants with average score ≥ this value will be selected</p>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="auto_assign_queue" id="auto_assign_queue" value="1" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="auto_assign_queue" class="ml-2 block text-sm text-gray-900">
                                Automatically assign queue numbers
                            </label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:w-auto sm:text-sm">
                        Auto-Select
                    </button>
                    <button type="button" onclick="document.getElementById('autoSelectModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentParticipantId = null;
let currentDeliveryMode = '{{ $event->delivery_mode }}';

function showActionModal(participantId, name, score, status, queue) {
    currentParticipantId = participantId;
    document.getElementById('actionModalTitle').textContent = status === 'selected' ? 'Update Selection' : 'Select or Reject Participant';
    document.getElementById('actionParticipantName').textContent = name;
    document.getElementById('actionScore').textContent = score > 0 ? score.toFixed(1) : 'Not reviewed';
    document.getElementById('presentation_queue').value = queue || '';
    
    // Show/hide buttons based on status
    if (status === 'selected') {
        document.getElementById('btnSelect').classList.add('hidden');
        document.getElementById('btnReject').classList.remove('hidden');
    } else if (status === 'rejected') {
        document.getElementById('btnSelect').classList.remove('hidden');
        document.getElementById('btnReject').classList.add('hidden');
    } else {
        document.getElementById('btnSelect').classList.remove('hidden');
        document.getElementById('btnReject').classList.remove('hidden');
    }
    
    document.getElementById('actionModal').classList.remove('hidden');
}

function closeActionModal() {
    document.getElementById('actionModal').classList.add('hidden');
    document.getElementById('selectFields').classList.remove('hidden');
    document.getElementById('rejectFields').classList.add('hidden');
}

function submitAction(action) {
    const form = document.getElementById('actionForm');
    
    if (action === 'select') {
        form.action = `/organizer/presentations/{{ $event->id }}/participant/${currentParticipantId}/select`;
        document.getElementById('selectFields').classList.remove('hidden');
        document.getElementById('rejectFields').classList.add('hidden');
    } else {
        form.action = `/organizer/presentations/{{ $event->id }}/participant/${currentParticipantId}/reject`;
        document.getElementById('selectFields').classList.add('hidden');
        document.getElementById('rejectFields').classList.remove('hidden');
        
        const reason = document.getElementById('rejection_reason').value;
        if (!reason.trim()) {
            alert('Please provide a rejection reason');
            return;
        }
    }
    
    form.submit();
}

function showDetailsModal(participantId, name, deliveryMode, queue, time, link, location) {
    currentParticipantId = participantId;
    document.getElementById('detailsParticipantName').textContent = name;
    document.getElementById('details_queue').value = queue || '';
    document.getElementById('details_time').value = time || '';
    document.getElementById('details_link').value = link || '';
    document.getElementById('details_location').value = location || '';
    
    // Show/hide fields based on delivery mode
    if (deliveryMode === 'online') {
        document.getElementById('linkField').classList.remove('hidden');
        document.getElementById('locationField').classList.add('hidden');
    } else if (deliveryMode === 'face_to_face') {
        document.getElementById('linkField').classList.add('hidden');
        document.getElementById('locationField').classList.remove('hidden');
    } else {
        document.getElementById('linkField').classList.remove('hidden');
        document.getElementById('locationField').classList.remove('hidden');
    }
    
    document.getElementById('detailsForm').action = `/organizer/presentations/{{ $event->id }}/participant/${participantId}/update-details`;
    document.getElementById('detailsModal').classList.remove('hidden');
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}
</script>
@endsection
