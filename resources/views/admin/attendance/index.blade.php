@extends('admin.layouts.app')

@section('title', 'Attendance Management')
@section('page-title', 'Attendance Management')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Attendance</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_attendance']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Today's Attendance</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['today_attendance']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg. Duration</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['avg_duration'] ?? 0, 0) }} min</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Attendees</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name, email, or event..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event</label>
                    <select name="event_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    
                    @if(request()->hasAny(['search', 'event_id', 'date_from', 'date_to']))
                        <a href="{{ route('admin.attendance.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>
                    @endif
                </div>
                
                <div>
                    <a href="{{ route('admin.attendance.export', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Attendance List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($attendance->registration->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->registration->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $attendance->registration->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $attendance->registration->event->title }}</div>
                                <div class="text-sm text-gray-500">{{ $attendance->registration->event->start_date->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $attendance->checked_in_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $attendance->checked_in_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->checked_out_at)
                                    <div class="text-sm text-gray-900">{{ $attendance->checked_out_at->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $attendance->checked_out_at->format('h:i A') }}</div>
                                @else
                                    <span class="text-sm text-gray-500">Still attending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->checked_out_at)
                                    @php
                                        $duration = $attendance->checked_out_at->diffInMinutes($attendance->checked_in_at);
                                    @endphp
                                    <span class="text-sm text-gray-900">{{ $duration }} min</span>
                                @else
                                    @php
                                        $duration = now()->diffInMinutes($attendance->checked_in_at);
                                    @endphp
                                    <span class="text-sm text-blue-600">{{ $duration }} min (ongoing)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($attendance->check_in_method === 'qr_scan') bg-blue-100 text-blue-800
                                    @elseif($attendance->check_in_method === 'manual') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    @if($attendance->check_in_method === 'qr_scan')
                                        QR Scan
                                    @elseif($attendance->check_in_method === 'manual')
                                        Manual
                                    @else
                                        {{ ucfirst($attendance->check_in_method) }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-clipboard-check text-4xl mb-4"></i>
                                    <p>No attendance records found</p>
                                    @if(request()->hasAny(['search', 'event_id', 'date_from', 'date_to']))
                                        <p class="text-sm mt-2">Try adjusting your filters</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attendances->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection