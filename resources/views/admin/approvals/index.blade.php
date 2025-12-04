@extends('admin.layouts.app')

@section('title', 'Registration Approvals')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Registration Approvals</h1>
        <p class="text-gray-600 mt-2">Review and approve event registrations</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.approvals.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="event_id" class="block text-sm font-medium text-gray-700 mb-2">Filter by Event</label>
                    <select name="event_id" id="event_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Filter by Role</label>
                    <select name="role" id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Roles</option>
                        <option value="participant" {{ request('role') == 'participant' ? 'selected' : '' }}>Participant</option>
                        <option value="jury" {{ request('role') == 'jury' ? 'selected' : '' }}>Jury</option>
                        <option value="both" {{ request('role') == 'both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Registrations Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($registrations->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending registrations</h3>
                <p class="mt-1 text-sm text-gray-500">All registrations have been processed.</p>
            </div>
        @else
            <form id="bulkActionForm" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="selectAll" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="selectAll" class="ml-2 text-sm text-gray-700">Select All</label>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" onclick="bulkAction('approve')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Bulk Approve
                            </button>
                            <button type="button" onclick="bulkAction('reject')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Bulk Reject
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($registrations as $registration)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="registration_ids[]" value="{{ $registration->id }}" class="registration-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $registration->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $registration->event->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $registration->event->start_date->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($registration->role === 'both')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Both</span>
                                        @elseif($registration->role === 'participant')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Participant</span>
                                        @elseif($registration->role === 'jury')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Jury</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $registration->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.approvals.show', $registration) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <form action="{{ route('admin.approvals.approve', $registration) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Approve this registration?')">Approve</button>
                                            </form>
                                            <button type="button" onclick="openRejectModal({{ $registration->id }})" class="text-red-600 hover:text-red-900">Reject</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Registration</h3>
                    <div>
                        <label for="rejected_reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                        <textarea id="rejected_reason" name="rejected_reason" rows="4" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Reject
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(registrationId) {
    document.getElementById('rejectForm').action = `/admin/approvals/${registrationId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejected_reason').value = '';
}

function bulkAction(action) {
    const checkboxes = document.querySelectorAll('.registration-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one registration');
        return;
    }

    if (action === 'approve') {
        if (confirm(`Approve ${checkboxes.length} registration(s)?`)) {
            document.getElementById('bulkActionForm').action = '{{ route("admin.approvals.bulk-approve") }}';
            document.getElementById('bulkActionForm').submit();
        }
    } else if (action === 'reject') {
        const reason = prompt('Enter rejection reason:');
        if (reason) {
            const form = document.getElementById('bulkActionForm');
            form.action = '{{ route("admin.approvals.bulk-reject") }}';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'rejected_reason';
            input.value = reason;
            form.appendChild(input);
            form.submit();
        }
    }
}

// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.registration-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>
@endsection
