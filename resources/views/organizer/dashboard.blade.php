@extends('organizer.layouts.app')

@section('title', 'Organizer Dashboard')
@section('page-title', 'Dashboard Overview')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

@section('content')
<!-- Flash Messages -->
@if (session('success'))
    <div class="mb-6 mx-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

<div class="p-6">
    <!-- Key Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Events -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-3xl opacity-80"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-blue-100 text-sm">Total Events</p>
                        <p class="text-3xl font-bold">{{ $totalEvents }}</p>
                        <p class="text-blue-200 text-xs mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>{{ $upcomingEvents }} upcoming
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Registrations -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg shadow-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-3xl opacity-80"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-green-100 text-sm">Total Registrations</p>
                        <p class="text-3xl font-bold">{{ number_format($totalRegistrations) }}</p>
                        <p class="text-green-200 text-xs mt-1">
                            <i class="fas fa-clock mr-1"></i>{{ $pendingRegistrations }} pending
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg shadow-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-3xl opacity-80"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-purple-100 text-sm">Total Revenue</p>
                        <p class="text-3xl font-bold">${{ number_format($totalRevenue, 2) }}</p>
                        <p class="text-purple-200 text-xs mt-1">
                            <i class="fas fa-hourglass-half mr-1"></i>${{ number_format($pendingRevenue, 2) }} pending
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg shadow-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-3xl opacity-80"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-orange-100 text-sm">Success Rate</p>
                        <p class="text-3xl font-bold">
                            {{ $totalRegistrations > 0 ? number_format(($confirmedRegistrations / $totalRegistrations) * 100, 1) : 0 }}%
                        </p>
                        <p class="text-orange-200 text-xs mt-1">
                            <i class="fas fa-check mr-1"></i>{{ $confirmedRegistrations }} confirmed
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Events & Registrations Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar mr-2 text-indigo-500"></i>Monthly Performance
            </h3>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-dollar-sign mr-2 text-green-500"></i>Revenue Trends
            </h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Events -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-clock mr-2 text-blue-500"></i>Recent Events
                    </h3>
                    <a href="{{ route('organizer.events.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">View all</a>
                </div>
            </div>
            <div class="p-6">
                @if($recentEvents->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentEvents as $event)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-calendar text-indigo-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $event->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $event->category->name ?? 'No Category' }}</p>
                                    <div class="flex items-center mt-1 space-x-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                            @if($event->status === 'published') bg-green-100 text-green-800
                                            @elseif($event->status === 'draft') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            <i class="fas fa-users mr-1"></i>{{ $event->registrations->count() ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No events yet</p>
                @endif
            </div>
        </div>

        <!-- Top Performing Events -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-star mr-2 text-yellow-500"></i>Top Performers
                </h3>
            </div>
            <div class="p-6">
                @if($topEvents->count() > 0)
                    <div class="space-y-4">
                        @foreach($topEvents as $index => $event)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                        @if($index === 0) bg-yellow-100 text-yellow-800
                                        @elseif($index === 1) bg-gray-100 text-gray-800
                                        @elseif($index === 2) bg-orange-100 text-orange-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $event->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-users mr-1"></i>{{ $event->registrations_count ?? $event->registrations->count() }} registrations
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No data yet</p>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-bell mr-2 text-red-500"></i>Recent Activity
                </h3>
            </div>
            <div class="p-6">
                @if($recentActivity->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentActivity->take(8) as $activity)
                            <div class="flex items-start space-x-3 text-sm">
                                <div class="flex-shrink-0">
                                    @if($activity->type === 'registration')
                                        <i class="fas fa-user-plus text-green-500 mt-0.5"></i>
                                    @else
                                        <i class="fas fa-edit text-blue-500 mt-0.5"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    @if($activity->type === 'registration')
                                        <p class="text-gray-900">
                                            <span class="font-medium">{{ $activity->user_name }}</span>
                                            registered for 
                                            <span class="font-medium">{{ $activity->event_title }}</span>
                                        </p>
                                    @else
                                        <p class="text-gray-900">
                                            <span class="font-medium">{{ $activity->event_title }}</span> 
                                            was updated
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent activity</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Category Performance & Upcoming Events -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category Performance -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-tags mr-2 text-purple-500"></i>Category Performance
                </h3>
            </div>
            <div class="p-6">
                @if($categoryStats->count() > 0)
                    <div class="space-y-4">
                        @foreach($categoryStats as $category)
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-900">{{ $category['category'] }}</span>
                                        <span class="text-xs text-gray-500">{{ $category['events_count'] }} events</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" 
                                             style="width: {{ $categoryStats->max('total_registrations') > 0 ? ($category['total_registrations'] / $categoryStats->max('total_registrations')) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="ml-4 text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $category['total_registrations'] }}</p>
                                    <p class="text-xs text-gray-500">registrations</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No categories yet</p>
                @endif
            </div>
        </div>

        <!-- Upcoming Events Calendar -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-check mr-2 text-green-500"></i>Upcoming Events
                </h3>
            </div>
            <div class="p-6">
                @if($upcomingEventsData->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingEventsData->take(6) as $event)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="text-center">
                                        <div class="text-xs font-medium text-gray-500 uppercase">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M') }}
                                        </div>
                                        <div class="text-lg font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('d') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $event->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($event->start_date)->format('h:i A') }}
                                        <span class="mx-1">•</span>
                                        <i class="fas fa-users mr-1"></i>{{ $event->registrations->count() ?? 0 }} registered
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-plus text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 mb-4">No upcoming events scheduled</p>
                        <a href="{{ route('organizer.events.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i>Create New Event
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Charts Scripts -->
<script>
    // Monthly Events & Registrations Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyStats, 'month')) !!},
            datasets: [{
                label: 'Events Created',
                data: {!! json_encode(array_column($monthlyStats, 'events')) !!},
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4
            }, {
                label: 'Registrations',
                data: {!! json_encode(array_column($monthlyStats, 'registrations')) !!},
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyRevenue, 'month')) !!},
            datasets: [{
                label: 'Revenue ($)',
                data: {!! json_encode(array_column($monthlyRevenue, 'revenue')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection