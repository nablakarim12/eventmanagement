@extends('organizer.layouts.app')

@section('title', 'Registration Approvals')

@section('content')
<div class="flex-1 p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Registration Approvals</h1>
            <nav class="text-sm text-gray-500 mt-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Registration Approvals</span>
            </nav>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="filter" class="block text-sm font-medium text-gray-700 mb-2">Status Filter</label>
                    <select name="filter" id="filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All Events</option>
                        <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Events with Pending Approvals</option>
                    </select>
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role Filter</label>
                    <select name="role" id="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                        <option value="participant" {{ request('role') == 'participant' ? 'selected' : '' }}>Participants</option>
                        <option value="jury" {{ request('role') == 'jury' ? 'selected' : '' }}>Jury</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
                <div>
                    <a href="{{ route('organizer.approvals.index') }}" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center">
                        <i class="fas fa-refresh mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Events Grid -->
        @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    @php
                        $pendingCount = $event->registrations->where('approval_status', 'pending')->count();
                        $totalRegistrations = $event->registrations->count();
                        $approvedCount = $event->registrations->where('approval_status', 'approved')->count();
                        $rejectedCount = $event->registrations->where('approval_status', 'rejected')->count();
                    @endphp
                    
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                        @if($event->poster)
                            <div class="h-48 bg-gray-200">
                                <img src="{{ asset('storage/events/posters/' . basename($event->poster)) }}" 
                                     alt="Event Poster" 
                                     class="w-full h-full object-cover">
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 line-clamp-2" title="{{ $event->title }}">
                                    {{ Str::limit($event->title, 40) }}
                                </h3>
                                @if($pendingCount > 0)
                                    <span class="bg-yellow-100 text-yellow-800 text-sm px-2 py-1 rounded-full">{{ $pendingCount }} Pending</span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-sm px-2 py-1 rounded-full">All Reviewed</span>
                                @endif
                            </div>

                            <!-- Statistics -->
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ $totalRegistrations }}</div>
                                    <div class="text-sm text-gray-500">Total</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $approvedCount }}</div>
                                    <div class="text-sm text-gray-500">Approved</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">{{ $rejectedCount }}</div>
                                    <div class="text-sm text-gray-500">Rejected</div>
                                </div>
                            </div>

                            <!-- Role breakdown -->
                            @if($totalRegistrations > 0)
                                @php
                                    $participantCount = $event->registrations->filter(function($reg) {
                                        return in_array($reg->role, ['participant', 'both']);
                                    })->count();
                                    $juryCount = $event->registrations->filter(function($reg) {
                                        return in_array($reg->role, ['jury', 'both']);
                                    })->count();
                                @endphp
                                <div class="flex justify-between items-center mb-4">
                                    <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">{{ $participantCount }} Participants</span>
                                    <span class="bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">{{ $juryCount }} Jury</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $event->start_date?->format('M j, Y') ?? 'Date TBA' }}
                                </div>
                                <a href="{{ route('organizer.approvals.event-registrations', $event) }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm">
                                    <i class="fas fa-eye mr-1"></i>Review
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="mb-6">
                    <i class="fas fa-file-alt text-6xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Events Found</h3>
                <p class="text-gray-600 mb-6">
                    @if(request('filter') === 'pending')
                        No events have pending registrations to review.
                    @else
                        You haven't created any events yet.
                    @endif
                </p>
                <a href="{{ route('organizer.events.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Create New Event
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filter changes
    document.getElementById('filter').addEventListener('change', function() {
        this.closest('form').submit();
    });
    
    document.getElementById('role').addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>
@endpush
@endsection