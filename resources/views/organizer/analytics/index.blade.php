@extends('organizer.layouts.app')

@section('title', 'Analytics Dashboard')
@section('page-title', 'Analytics Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <p class="text-gray-600 mt-1">Comprehensive insights into your event performance</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('organizer.analytics.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-download mr-2"></i>Export Report
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow mb-6">
        <form method="GET" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-alt text-3xl opacity-80"></i>
                </div>
                <div class="ml-4">
                    <p class="text-blue-100 text-sm">Total Events</p>
                    <p class="text-3xl font-bold">{{ $eventPerformance->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-3xl opacity-80"></i>
                </div>
                <div class="ml-4">
                    <p class="text-green-100 text-sm">Total Registrations</p>
                    <p class="text-3xl font-bold">{{ $eventPerformance->sum('registrations_count') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-dollar-sign text-3xl opacity-80"></i>
                </div>
                <div class="ml-4">
                    <p class="text-purple-100 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold">${{ number_format($eventPerformance->sum('revenue'), 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-percentage text-3xl opacity-80"></i>
                </div>
                <div class="ml-4">
                    <p class="text-orange-100 text-sm">Attendance Rate</p>
                    <p class="text-3xl font-bold">{{ $attendanceRate['attendance_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Registration Trends -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line mr-2 text-blue-500"></i>Registration Trends
            </h3>
            <div class="h-64">
                <canvas id="registrationTrendsChart"></canvas>
            </div>
        </div>

        <!-- Revenue by Month -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-dollar-sign mr-2 text-green-500"></i>Monthly Revenue
            </h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Status Distribution & Category Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Registration Status Distribution -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Registration Status Distribution
            </h3>
            <div class="h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-tags mr-2 text-indigo-500"></i>Category Performance
            </h3>
            <div class="space-y-4">
                @foreach($categoryPerformance->take(5) as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-900">{{ $category['category'] }}</span>
                                <span class="text-xs text-gray-500">${{ number_format($category['total_revenue'], 0) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" 
                                     style="width: {{ $categoryPerformance->max('total_revenue') > 0 ? ($category['total_revenue'] / $categoryPerformance->max('total_revenue')) * 100 : 0 }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>{{ $category['events_count'] }} events</span>
                                <span>{{ $category['total_registrations'] }} registrations</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Events & Demographics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Events by Revenue -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-trophy mr-2 text-yellow-500"></i>Top Events by Revenue
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($topEventsByRevenue->take(8) as $index => $eventData)
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
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $eventData['event']->title }}</p>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-dollar-sign mr-1"></i>${{ number_format($eventData['revenue'], 2) }}
                                    <span class="mx-1">•</span>
                                    <i class="fas fa-users mr-1"></i>{{ $eventData['paid_registrations'] }} paid
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Participant Demographics -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-users-cog mr-2 text-green-500"></i>Participant Insights
                </h3>
            </div>
            <div class="p-6">
                <!-- Demographics Summary -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($demographics['total_participants']) }}</p>
                        <p class="text-sm text-gray-600">Total Participants</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($demographics['unique_participants']) }}</p>
                        <p class="text-sm text-gray-600">Unique Participants</p>
                    </div>
                </div>

                <!-- Top Email Domains -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Top Email Domains</h4>
                    <div class="space-y-2">
                        @foreach($demographics['email_domains']->take(5) as $domain => $count)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ $domain }}</span>
                                <span class="font-medium text-gray-900">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Attendance Statistics -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Attendance Statistics</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Attendance Rate</p>
                            <p class="font-semibold text-green-600">{{ $attendanceRate['attendance_rate'] }}%</p>
                        </div>
                        <div>
                            <p class="text-gray-600">No-Show Rate</p>
                            <p class="font-semibold text-red-600">{{ $attendanceRate['no_show_rate'] }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Performance Table -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-table mr-2 text-gray-500"></i>Event Performance Details
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrations</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confirmed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attended</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($eventPerformance->take(10) as $performance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $performance['event']->title }}</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($performance['event']->start_date)->format('M j, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($performance['registrations_count']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($performance['confirmed_count']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($performance['attended_count']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($performance['revenue'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $rate = $performance['confirmed_count'] > 0 ? ($performance['attended_count'] / $performance['confirmed_count']) * 100 : 0;
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($rate >= 80) bg-green-100 text-green-800
                                    @elseif($rate >= 60) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ number_format($rate, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Registration Trends Chart
const trendsCtx = document.getElementById('registrationTrendsChart').getContext('2d');
new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($registrationTrends, 'date')) !!},
        datasets: [{
            label: 'Daily Registrations',
            data: {!! json_encode(array_column($registrationTrends, 'count')) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
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
                display: false
            }
        }
    }
});

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($revenueByMonth->pluck('month')) !!},
        datasets: [{
            label: 'Monthly Revenue',
            data: {!! json_encode($revenueByMonth->pluck('revenue')) !!},
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

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($statusDistribution->keys()) !!},
        datasets: [{
            data: {!! json_encode($statusDistribution->values()) !!},
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(251, 191, 36, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(156, 163, 175, 0.8)'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection