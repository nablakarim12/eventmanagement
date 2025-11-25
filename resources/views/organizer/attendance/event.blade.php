@extends('organizer.layouts.app')

@section('title', 'Event Attendance - ' . $event->title)
@section('page-title', 'Event Attendance')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                <p class="text-gray-600 mt-1">
                    <i class="fas fa-calendar mr-2"></i>
                    {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y - h:i A') }}
                </p>
            </div>
            <div class="flex space-x-2">
                <button onclick="openManualCheckInModal()" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-hand-pointer mr-2"></i>Manual Check-In
                </button>
                <a href="{{ route('organizer.events.show', $event) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Event
                </a>
            </div>
        </div>
    </div>

    <!-- Attendance Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Registered -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Registered</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalRegistrations }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Checked In -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Checked In</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalCheckedIn }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                {{ $totalRegistrations > 0 ? round(($totalCheckedIn / $totalRegistrations) * 100, 1) : 0 }}% attendance
            </p>
        </div>

        <!-- Jury Checked In -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Jury Checked In</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $juryCheckedIn }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-user-tie text-2xl text-purple-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                Out of {{ $totalJury }} approved jury
            </p>
        </div>

        <!-- Participants Checked In -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Participants Checked In</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $participantsCheckedIn }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-user-check text-2xl text-orange-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                Out of {{ $totalParticipants }} approved participants
            </p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow-lg mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="showTab('all')" id="tab-all" class="tab-button active px-6 py-4 text-sm font-medium border-b-2">
                    All Attendees ({{ $totalCheckedIn }})
                </button>
                <button onclick="showTab('jury')" id="tab-jury" class="tab-button px-6 py-4 text-sm font-medium border-b-2">
                    Jury ({{ $juryCheckedIn }})
                </button>
                <button onclick="showTab('participants')" id="tab-participants" class="tab-button px-6 py-4 text-sm font-medium border-b-2">
                    Participants ({{ $participantsCheckedIn }})
                </button>
                <button onclick="showTab('not-checked-in')" id="tab-not-checked-in" class="tab-button px-6 py-4 text-sm font-medium border-b-2">
                    Not Checked In ({{ $totalRegistrations - $totalCheckedIn }})
                </button>
            </nav>
        </div>
    </div>

    <!-- Attendee Lists -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- All Attendees -->
        <div id="content-all" class="tab-content">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">All Checked-In Attendees</h3>
                @if($allCheckedIn->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-in Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">QR Code</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allCheckedIn as $registration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $registration->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($registration->role === 'jury') bg-purple-100 text-purple-800
                                            @elseif($registration->role === 'both') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($registration->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $registration->checked_in_at ? $registration->checked_in_at->format('M d, Y h:i A') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <code class="bg-gray-100 px-2 py-1 rounded">{{ $registration->qr_code }}</code>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No one has checked in yet.</p>
                @endif
            </div>
        </div>

        <!-- Jury Only -->
        <div id="content-jury" class="tab-content hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Checked-In Jury Members</h3>
                    <a href="{{ route('organizer.events.papers.index', $event) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-file-alt mr-2"></i>Manage Papers & Assignments
                    </a>
                </div>
                @if($juryOnly->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expertise</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-in Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned Papers</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($juryOnly as $registration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $registration->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $registration->jury_institution ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">{{ $registration->jury_expertise_areas ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $registration->checked_in_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                            {{ $registration->juryAssignments->count() }} papers
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No jury members have checked in yet.</p>
                @endif
            </div>
        </div>

        <!-- Participants Only -->
        <div id="content-participants" class="tab-content hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Checked-In Participants</h3>
                @if($participantsOnly->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-in Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">QR Code</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($participantsOnly as $registration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $registration->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $registration->checked_in_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <code class="bg-gray-100 px-2 py-1 rounded">{{ $registration->qr_code }}</code>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No participants have checked in yet.</p>
                @endif
            </div>
        </div>

        <!-- Not Checked In -->
        <div id="content-not-checked-in" class="tab-content hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Not Yet Checked In</h3>
                @if($notCheckedIn->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration Code</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($notCheckedIn as $registration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ $registration->user->email }}</div>
        <!-- Not Checked In -->
        <div id="content-not-checked-in" class="tab-content hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Not Yet Checked In</h3>
                    @if($notCheckedIn->count() > 0)
                        <button onclick="bulkCheckInAll()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-check-double mr-2"></i>Check In All
                        </button>
                    @endif
                </div>
                @if($notCheckedIn->count() > 0)
                    <form id="bulk-checkin-form" action="{{ route('organizer.attendance.bulk-registration-checkin', $event) }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left">
                                            <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)" class="rounded">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($notCheckedIn as $registration)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="registration_ids[]" value="{{ $registration->id }}" class="rounded registration-checkbox">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">{{ $registration->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($registration->role === 'jury') bg-purple-100 text-purple-800
                                                @elseif($registration->role === 'both') bg-blue-100 text-blue-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst($registration->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <code class="bg-gray-100 px-2 py-1 rounded">{{ $registration->registration_code }}</code>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button type="button" onclick="quickCheckIn({{ $registration->id }}, '{{ $registration->user->name }}')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                                <i class="fas fa-check mr-1"></i>Check In
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                <i class="fas fa-check-circle mr-2"></i>Check In Selected
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-gray-500 text-center py-8">Everyone has checked in!</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Manual Check-In Modal -->
<div id="manual-checkin-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Manual Check-In</h3>
                <button onclick="closeManualCheckInModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mt-2">Use this if QR code scanning is not working. Select attendees to mark them as checked in.</p>
        </div>
        
        <div class="p-6">
            <div class="mb-4">
                <input type="text" id="search-attendee" placeholder="Search by name or email..." 
                       onkeyup="filterAttendees()" 
                       class="w-full border-gray-300 rounded-lg">
            </div>
            
            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="attendee-list">
                        @foreach($notCheckedIn as $registration)
                        <tr class="attendee-row hover:bg-gray-50" data-name="{{ strtolower($registration->user->name) }}" data-email="{{ strtolower($registration->user->email) }}">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $registration->user->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($registration->role === 'jury') bg-purple-100 text-purple-800
                                    @elseif($registration->role === 'both') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($registration->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <form action="{{ route('organizer.attendance.manual-registration-checkin', $event) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="registration_id" value="{{ $registration->id }}">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-check mr-1"></i>Check In
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.tab-button {
    color: #6B7280;
    border-bottom-color: transparent;
    transition: all 0.2s;
}
.tab-button:hover {
    color: #374151;
    border-bottom-color: #D1D5DB;
}
.tab-button.active {
    color: #3B82F6;
    border-bottom-color: #3B82F6;
}
</style>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab
    document.getElementById('tab-' + tabName).classList.add('active');
}

function openManualCheckInModal() {
    document.getElementById('manual-checkin-modal').classList.remove('hidden');
    document.getElementById('manual-checkin-modal').classList.add('flex');
}

function closeManualCheckInModal() {
    document.getElementById('manual-checkin-modal').classList.add('hidden');
    document.getElementById('manual-checkin-modal').classList.remove('flex');
}

function filterAttendees() {
    const searchInput = document.getElementById('search-attendee').value.toLowerCase();
    const rows = document.querySelectorAll('.attendee-row');
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const email = row.getAttribute('data-email');
        
        if (name.includes(searchInput) || email.includes(searchInput)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function quickCheckIn(registrationId, name) {
    if (confirm(`Check in ${name}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('organizer.attendance.manual-registration-checkin', $event) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const regId = document.createElement('input');
        regId.type = 'hidden';
        regId.name = 'registration_id';
        regId.value = registrationId;
        
        form.appendChild(csrfToken);
        form.appendChild(regId);
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.registration-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

function bulkCheckInAll() {
    if (confirm('Check in all attendees who have not checked in yet?')) {
        document.getElementById('select-all').checked = true;
        toggleSelectAll(document.getElementById('select-all'));
        document.getElementById('bulk-checkin-form').submit();
    }
}
</script>
@endsection
    document.getElementById('tab-' + tabName).classList.add('active');
}
</script>
@endsection
