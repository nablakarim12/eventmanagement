@extends('organizer.layouts.app')

@section('title', 'QR Code Details')
@section('page-title', 'QR Code Details')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('organizer.qr-codes.index') }}" class="text-gray-400 hover:text-gray-500">
                        QR Codes
                    </a>
                </li>
                <li class="flex">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">{{ $qrCode->label ?: 'QR Code' }}</span>
                </li>
            </ol>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $qrCode->label ?: 'QR Code Details' }}</h1>
                <p class="text-gray-600 mt-2">{{ $qrCode->event->title }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button onclick="toggleQRCode()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span id="toggle-text">{{ $qrCode->is_active ? 'Deactivate' : 'Activate' }}</span>
                </button>
                <a href="{{ route('organizer.qr-codes.download', $qrCode) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download
                </a>
                <a href="{{ route('organizer.qr-codes.edit', $qrCode) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- QR Code Display -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">QR Code</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div class="inline-block p-8 bg-gray-50 rounded-lg">
                            <div class="w-64 h-64 mx-auto">
                                {!! $qrCode->getQRCodeSVG() !!}
                            </div>
                        </div>
                        <div class="mt-6">
                            <p class="text-sm text-gray-500 mb-4">Scan this QR code with any QR code scanner</p>
                            <div class="flex justify-center space-x-4">
                                <button onclick="downloadQR('png')" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    PNG
                                </button>
                                <button onclick="downloadQR('svg')" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    SVG
                                </button>
                                <button onclick="downloadQR('pdf')" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Analytics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Usage Analytics</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-indigo-600">{{ $qrCode->scan_count }}</div>
                            <div class="text-sm text-gray-500">Total Scans</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $qrCode->unique_scans_count }}</div>
                            <div class="text-sm text-gray-500">Unique Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $qrCode->scans_today }}</div>
                            <div class="text-sm text-gray-500">Scans Today</div>
                        </div>
                    </div>

                    <!-- Usage Chart (Placeholder for Chart.js) -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Scan Activity (Last 7 Days)</h4>
                        <div class="h-32 flex items-center justify-center">
                            <canvas id="scanChart" width="400" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Scans -->
            @if($recentScans->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Scans</h3>
                </div>
                <div class="overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentScans as $scan)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <svg class="h-4 w-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $scan->user ? $scan->user->name : 'Guest User' }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $scan->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $scan->created_at->format('M j, Y g:i A') }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- QR Code Info -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">QR Code Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Event</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ route('organizer.events.show', $qrCode->event) }}" class="text-indigo-600 hover:text-indigo-500">
                                {{ $qrCode->event->title }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1">
                            @if($qrCode->qr_type === 'check_in')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Check-in
                                </span>
                            @elseif($qrCode->qr_type === 'check_out')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Check-out
                                </span>
                            @elseif($qrCode->qr_type === 'both')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Check-in/out
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Registration
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span id="status-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $qrCode->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 {{ $qrCode->is_active ? 'text-green-400' : 'text-red-400' }}" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                {{ $qrCode->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->created_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @if($qrCode->expires_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Expires</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->expires_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    @endif
                    @if($qrCode->usage_limit)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Usage Limit</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->usage_limit }} scans</dd>
                    </div>
                    @endif
                    @if($qrCode->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $qrCode->description }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="copyQRData()" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Copy QR Data
                    </button>
                    <a href="{{ route('organizer.attendance.scanner') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        Open Scanner
                    </a>
                    <a href="{{ route('organizer.events.show', $qrCode->event) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6a2 2 0 012 2v10a2 2 0 01-2 2H8a2 2 0 01-2-2V9a2 2 0 012-2z" />
                        </svg>
                        View Event
                    </a>
                </div>
            </div>

            <!-- QR Code URL -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">QR Code URL</h3>
                </div>
                <div class="p-6">
                    <div class="flex">
                        <input type="text" id="qr-url" value="{{ $qrCode->getPublicUrl() }}" readonly
                               class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50">
                        <button onclick="copyUrl()" 
                                class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="notification-container" class="fixed top-4 right-4 z-50" style="display: none;">
    <div id="notification" class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg id="notification-icon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p id="notification-message" class="text-sm font-medium"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="hideNotification()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize scan activity chart
const ctx = document.getElementById('scanChart').getContext('2d');
const scanChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartData['labels']),
        datasets: [{
            label: 'Scans',
            data: @json($chartData['data']),
            borderColor: 'rgb(99, 102, 241)',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
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

function toggleQRCode() {
    const isActive = {{ $qrCode->is_active ? 'true' : 'false' }};
    const action = isActive ? 'deactivate' : 'activate';
    
    if (confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} this QR code?`)) {
        fetch(`{{ route('organizer.qr-codes.toggle', $qrCode) }}`, {
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
                showNotification('error', 'Failed to update QR code status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'An error occurred');
        });
    }
}

function downloadQR(format) {
    window.open(`{{ route('organizer.qr-codes.download', $qrCode) }}?format=${format}`, '_blank');
}

function copyQRData() {
    const qrData = '{{ $qrCode->qr_data }}';
    navigator.clipboard.writeText(qrData).then(() => {
        showNotification('success', 'QR code data copied to clipboard');
    }).catch(() => {
        showNotification('error', 'Failed to copy QR code data');
    });
}

function copyUrl() {
    const url = document.getElementById('qr-url').value;
    navigator.clipboard.writeText(url).then(() => {
        showNotification('success', 'URL copied to clipboard');
    }).catch(() => {
        showNotification('error', 'Failed to copy URL');
    });
}

function showNotification(type, message) {
    const container = document.getElementById('notification-container');
    const notification = document.getElementById('notification');
    const icon = document.getElementById('notification-icon');
    const messageEl = document.getElementById('notification-message');
    
    messageEl.textContent = message;
    
    if (type === 'success') {
        icon.className = 'h-6 w-6 text-green-400';
        notification.className = 'max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4 border-green-400';
    } else {
        icon.className = 'h-6 w-6 text-red-400';
        notification.className = 'max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4 border-red-400';
    }
    
    container.style.display = 'block';
    setTimeout(hideNotification, 3000);
}

function hideNotification() {
    document.getElementById('notification-container').style.display = 'none';
}
</script>
@endsection