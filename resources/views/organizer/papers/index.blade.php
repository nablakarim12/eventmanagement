@extends('organizer.layouts.app')

@section('title', 'Paper Management - ' . $event->title)
@section('page-title', 'Paper Management')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                <p class="text-gray-600 mt-1">Paper Submissions & Jury Assignment</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('organizer.attendance.event', $event) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-users mr-2"></i>View Attendance
                </a>
                <a href="{{ route('organizer.events.show', $event) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Event
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Papers -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Papers</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $papers->count() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-file-alt text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Under Review -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Under Review</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $papers->where('status', 'under_review')->count() }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-hourglass-half text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <!-- Reviewed -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Reviewed</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $papers->where('status', 'reviewed')->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Checked-In Jury -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Available Jury</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">
                        {{ \App\Models\EventRegistration::where('event_id', $event->id)
                            ->where('approval_status', 'approved')
                            ->whereIn('role', ['jury', 'both'])
                            ->whereNotNull('checked_in_at')
                            ->count() }}
                    </p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-user-tie text-2xl text-purple-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">Checked-in jury only</p>
        </div>
    </div>

    <!-- Papers List -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Submitted Papers</h3>
        </div>

        @if($papers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paper Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Authors</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned Jury</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviews</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($papers as $paper)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">{{ $paper->submission_code }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 max-w-xs">{{ $paper->title }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ \Str::limit($paper->abstract, 100) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">
                                    @if($paper->authors->count() > 0)
                                        {{ $paper->authors->first()->name }}
                                        @if($paper->authors->count() > 1)
                                            <span class="text-gray-400">+{{ $paper->authors->count() - 1 }} more</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No authors listed</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $paper->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $paper->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($paper->status === 'submitted') bg-blue-100 text-blue-800
                                    @elseif($paper->status === 'under_review') bg-orange-100 text-orange-800
                                    @elseif($paper->status === 'reviewed') bg-green-100 text-green-800
                                    @elseif($paper->status === 'accepted') bg-green-100 text-green-800
                                    @elseif($paper->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ $paper->juryAssignments->count() }} jury
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ $paper->reviews->where('status', 'submitted')->count() }}/{{ $paper->juryAssignments->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($paper->average_score)
                                    <div class="text-sm font-bold text-gray-900">{{ number_format($paper->average_score, 2) }}/10</div>
                                @else
                                    <span class="text-gray-400 text-sm">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('organizer.events.papers.show', [$event, $paper]) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No papers have been submitted yet.</p>
                <p class="text-gray-400 text-sm mt-2">Papers will appear here once participants submit them through the submission system.</p>
            </div>
        @endif
    </div>
</div>
@endsection
