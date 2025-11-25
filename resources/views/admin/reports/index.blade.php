@extends('admin.layouts.app')

@section('title', 'Reports')
@section('page-title', 'Generate Reports')

@section('content')
<div class="space-y-6">
    <!-- Report Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Organizers -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="mb-2 text-sm font-medium text-gray-600">Total Organizers</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $organizerStats['total'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="mb-2 text-sm font-medium text-gray-600">Pending Approval</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $organizerStats['pending'] }}</p>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="mb-2 text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $organizerStats['approved'] }}</p>
                </div>
            </div>
        </div>

        <!-- Rejected -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="mb-2 text-sm font-medium text-gray-600">Rejected</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $organizerStats['rejected'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Reports Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Export Reports</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Organizers Report -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Event Organizers</h4>
                        <p class="text-sm text-gray-500">Complete list of all organizers</p>
                    </div>
                </div>
                <a href="{{ route('admin.reports.export.organizers') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download CSV
                </a>
            </div>

            <!-- Events Report (placeholder for future) -->
            <div class="border border-gray-200 rounded-lg p-4 opacity-50">
                <div class="flex items-center mb-3">
                    <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Events Report</h4>
                        <p class="text-sm text-gray-500">Coming soon</p>
                    </div>
                </div>
                <button disabled class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest cursor-not-allowed">
                    Download CSV
                </button>
            </div>

            <!-- Participants Report (placeholder for future) -->
            <div class="border border-gray-200 rounded-lg p-4 opacity-50">
                <div class="flex items-center mb-3">
                    <svg class="w-8 h-8 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <div>
                        <h4 class="font-medium text-gray-900">Participants Report</h4>
                        <p class="text-sm text-gray-500">Coming soon</p>
                    </div>
                </div>
                <button disabled class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest cursor-not-allowed">
                    Download CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Registration Trends Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Organizer Registration Trends</h3>
        <canvas id="trendsChart" height="100"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Registration Trends Chart
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
const trendsData = @json($registrationTrends);

new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: trendsData.map(item => new Date(item.month).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })),
        datasets: [{
            label: 'New Organizers',
            data: trendsData.map(item => item.count),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endsection