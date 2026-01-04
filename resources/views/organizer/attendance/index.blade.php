@extends('organizer.layouts.app')

@section('title', 'Innovation Attendance Management')
@section('page-title', 'Innovation Attendance Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 mt-2">Track jury attendance for innovation competition events</p>
            </div>
        </div>
    </div>

    <!-- Events List -->
    @if($events->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
            @foreach($events as $event)
                @php
                    // Calculate jury statistics
                    $allRegistrations = $event->registrations()->where('status', 'confirmed')->get();
                    $juryRegistrations = $allRegistrations->whereIn('role', ['jury', 'both']);
                    $totalJury = $juryRegistrations->count();
                    $juryCheckedIn = $juryRegistrations->where('checked_in_at', '!=', null)->count();
                    $juryNotCheckedIn = $totalJury - $juryCheckedIn;
                @endphp
                
                <a href="{{ route('organizer.attendance.event', $event) }}" 
                   class="flex items-center p-5 hover:bg-gray-50 transition-all duration-150 group">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-sm">
                            <i class="fas fa-user-check text-white text-2xl"></i>
                        </div>
                    </div>
                    
                    <!-- Event Details -->
                    <div class="flex-1 ml-5">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">
                            {{ $event->title }}
                        </h3>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="inline-flex items-center text-sm text-gray-600">
                                <i class="far fa-calendar text-gray-400 mr-1.5"></i>
                                {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                            </span>
                            <span class="inline-flex items-center text-sm text-gray-600">
                                <i class="far fa-clock text-gray-400 mr-1.5"></i>
                                {{ \Carbon\Carbon::parse($event->start_date)->format('g:i A') }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Statistics Badges -->
                    <div class="flex items-center space-x-3 ml-6">
                        <!-- Total Jury -->
                        <div class="flex items-center px-3 py-1.5 bg-purple-50 rounded-lg border border-purple-200">
                            <i class="fas fa-users text-purple-600 mr-2"></i>
                            <span class="text-sm font-semibold text-purple-700">{{ $totalJury }} Total Jury</span>
                        </div>
                        
                        <!-- Checked In -->
                        <div class="flex items-center px-3 py-1.5 bg-green-50 rounded-lg border border-green-200">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-sm font-semibold text-green-700">{{ $juryCheckedIn }} Checked In</span>
                        </div>
                        
                        <!-- Not Checked In -->
                        @if($juryNotCheckedIn > 0)
                        <div class="flex items-center px-3 py-1.5 bg-red-50 rounded-lg border border-red-200">
                            <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                            <span class="text-sm font-semibold text-red-700">{{ $juryNotCheckedIn }} Not Checked In</span>
                        </div>
                        @endif
                        
                        <!-- Arrow -->
                        <div class="pl-4">
                            <i class="fas fa-chevron-right text-gray-400 text-lg group-hover:text-purple-600 group-hover:translate-x-1 transition-all"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Events Found</h3>
                <p class="text-gray-500">You don't have any innovation competition events yet.</p>
                <a href="{{ route('organizer.events.create') }}" 
                   class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-plus mr-2"></i>
                    Create Event
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Old attendance table section (hidden for now) -->
<div style="display: none;">
    <!-- Attendance Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Attendance Records</h3>
                <div class="flex space-x-2">
                    <button onclick="exportAttendance()" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </div>
        
        @if(isset($attendanceRecords) && $attendanceRecords->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Participant
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Event
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Check In
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Check Out
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Duration
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Method
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($attendanceRecords as $registration)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $registration->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $registration->event->title }}</div>
                            <div class="text-sm text-gray-500">{{ $registration->event->start_date->format('M j, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($registration->calculated_role === 'participant')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Participant
                                </span>
                            @elseif($registration->calculated_role === 'reviewer')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Reviewer
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-indigo-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Both
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if(is_string($registration->checked_in_at))
                                {{ \Carbon\Carbon::parse($registration->checked_in_at)->format('M j, Y g:i A') }}
                            @else
                                {{ $registration->checked_in_at->format('M j, Y g:i A') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            N/A
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            N/A
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($registration->check_in_method)
                                @if($registration->check_in_method === 'qr_code' || $registration->check_in_method === 'qr')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        QR Code
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Manual
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    QR Code or Manual
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                Checked In
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('organizer.registrations.show', $registration) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">View</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $attendanceRecords->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records</h3>
            <p class="mt-1 text-sm text-gray-500">Start tracking attendance by scanning QR codes or manual check-ins.</p>
            <div class="mt-6">
                <a href="{{ route('organizer.attendance.scanner') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    Start QR Scanner
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function checkOut(attendanceId) {
    if (confirm('Check out this participant?')) {
        fetch(`/organizer/attendance/${attendanceId}/checkout`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error checking out participant');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error checking out participant');
        });
    }
}

function exportAttendance() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'csv');
    window.location.href = `{{ route('organizer.attendance.index') }}?${params.toString()}`;
}

// Auto-submit form when filters change
document.querySelectorAll('#event_id, #status, #date').forEach(element => {
    element.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endsection