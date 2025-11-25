@extends('organizer.layouts.app')

@section('title', 'Registration Management')
@section('page-title', 'Registration Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-600 mt-1">Manage all event registrations and participants</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('organizer.registrations.export', request()->query()) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Registrations</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Confirmed</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['confirmed']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['pending']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Cancelled</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['cancelled']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <i class="fas fa-user text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Participants</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['participants']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-gavel text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Jury Members</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['jury']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow mb-6">
        <form method="GET" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name or email..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Event</label>
                    <select name="event_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Roles</option>
                        <option value="participant" {{ request('role') === 'participant' ? 'selected' : '' }}>Participant Only</option>
                        <option value="jury" {{ request('role') === 'jury' ? 'selected' : '' }}>Jury Only</option>
                        <option value="both" {{ request('role') === 'both' ? 'selected' : '' }}>Participant & Jury</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="attended" {{ request('status') === 'attended' ? 'selected' : '' }}>Attended</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                    <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Payment Status</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('organizer.registrations.index') }}" 
                   class="text-sm text-gray-600 hover:text-gray-900">Clear Filters</a>
            </div>
        </form>
    </div>

    <!-- Registrations Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($registrations->count() > 0)
            <form id="bulkForm" method="POST" action="{{ route('organizer.registrations.bulk-update') }}">
                @csrf
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <input type="checkbox" id="selectAll" class="rounded">
                            <label for="selectAll" class="text-sm font-medium text-gray-700">Select All</label>
                        </div>
                        <div class="flex space-x-2" id="bulkActions" style="display: none;">
                            <select name="action" class="px-3 py-1 border border-gray-300 rounded text-sm">
                                <option value="">Bulk Actions</option>
                                <option value="confirm">Confirm Selected</option>
                                <option value="cancel">Cancel Selected</option>
                                <option value="mark_paid">Mark as Paid</option>
                                <option value="mark_attended">Mark as Attended</option>
                            </select>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Select
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Participant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Registration Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($registrations as $registration)
                                @php
                                    $roles = $registration->role === 'both' ? ['participant', 'jury'] : [$registration->role];
                                @endphp
                                @foreach($roles as $displayRole)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="registration_ids[]" value="{{ $registration->id }}" 
                                               class="registration-checkbox rounded">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-indigo-800">
                                                        {{ strtoupper(substr($registration->user->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $registration->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $registration->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $registration->event->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $registration->registration_code }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($displayRole === 'participant')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-user mr-1"></i>Participant
                                            </span>
                                        @elseif($displayRole === 'jury')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-gavel mr-1"></i>Jury
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-question mr-1"></i>{{ ucfirst($displayRole ?? 'Unknown') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $registration->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($registration->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($registration->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($registration->status === 'cancelled') bg-red-100 text-red-800
                                            @elseif($registration->status === 'attended') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($registration->payment_status === 'paid') bg-green-100 text-green-800
                                            @elseif($registration->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($registration->payment_status === 'failed') bg-red-100 text-red-800
                                            @elseif($registration->payment_status === 'refunded') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($registration->payment_status) }}
                                        </span>
                                        @if($registration->amount_paid)
                                            <div class="text-xs text-gray-500 mt-1">
                                                ${{ number_format($registration->amount_paid, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('organizer.registrations.show', $registration->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($registration->status !== 'attended')
                                                <form method="POST" action="{{ route('organizer.registrations.check-in', $registration->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Check In">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $registrations->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No registrations found</h3>
                <p class="text-gray-500">No registrations match your current filters.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const registrationCheckboxes = document.querySelectorAll('.registration-checkbox');
    const bulkActions = document.getElementById('bulkActions');

    selectAllCheckbox.addEventListener('change', function() {
        registrationCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActions();
    });

    registrationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleBulkActions);
    });

    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.registration-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkActions.style.display = 'flex';
        } else {
            bulkActions.style.display = 'none';
        }
    }
});
</script>
@endsection