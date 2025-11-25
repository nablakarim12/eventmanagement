@extends('organizer.layouts.app')

@section('title', 'QR Code Scanner')
@section('page-title', 'QR Code Scanner')

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qr-scanner/1.4.2/qr-scanner.umd.min.js"></script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('organizer.attendance.index') }}" class="text-gray-400 hover:text-gray-500">
                        Attendance
                    </a>
                </li>
                <li class="flex">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"/>
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">QR Scanner</span>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">QR Code Scanner</h1>
        <p class="text-gray-600 mt-2">Scan participant QR codes for quick check-in and check-out</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Scanner Section -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Camera Scanner</h3>
                        <div class="flex space-x-2">
                            <button id="startBtn" onclick="startScanner()" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2-14l-2 2m0 12l2 2m-10-2l-2 2m0-12l2-2"/>
                                </svg>
                                Start Scanner
                            </button>
                            <button id="stopBtn" onclick="stopScanner()" style="display: none;"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                                </svg>
                                Stop Scanner
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Camera Video -->
                    <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 400px;">
                        <video id="qr-video" class="w-full h-full object-cover" style="display: none;"></video>
                        
                        <!-- Scanner Placeholder -->
                        <div id="scanner-placeholder" class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-gray-500">Click "Start Scanner" to begin</p>
                                <p class="text-sm text-gray-400 mt-1">Make sure to allow camera access</p>
                            </div>
                        </div>
                        
                        <!-- Scan Overlay -->
                        <div id="scan-overlay" class="absolute inset-0 flex items-center justify-center" style="display: none;">
                            <div class="border-4 border-green-400 rounded-lg" style="width: 250px; height: 250px; border-style: dashed;">
                                <div class="w-full h-full flex items-center justify-center">
                                    <p class="text-green-600 font-medium bg-white px-3 py-1 rounded">Position QR code here</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Scanner Status -->
                    <div id="scanner-status" class="mt-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <div id="status-icon" class="w-2 h-2 rounded-full bg-gray-400 mr-2"></div>
                            <span id="status-text">Scanner inactive</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Entry & Recent Scans -->
        <div class="space-y-6">
            <!-- Manual Entry -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Manual Entry</h3>
                </div>
                <div class="p-6">
                    <form id="manualEntryForm" onsubmit="manualCheckIn(event)" class="space-y-4">
                        <div>
                            <label for="event_id" class="block text-sm font-medium text-gray-700">Event</label>
                            <select name="event_id" id="event_id" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Event</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="user_search" class="block text-sm font-medium text-gray-700">Search Participant</label>
                            <input type="text" name="user_search" id="user_search" 
                                   placeholder="Enter name or email..."
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Check In Manually
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recent Scans -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Check-ins</h3>
                </div>
                <div class="p-6">
                    <div id="recent-scans" class="space-y-3">
                        <p class="text-sm text-gray-500 text-center">No recent activity</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Today's Stats</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Total Check-ins</span>
                        <span id="total-checkins" class="text-sm font-medium text-gray-900">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">QR Scans</span>
                        <span id="qr-scans" class="text-sm font-medium text-gray-900">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Manual Entries</span>
                        <span id="manual-entries" class="text-sm font-medium text-gray-900">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="notification-container" class="fixed top-4 right-4 z-50" style="display: none;">
    <div id="notification" class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg id="notification-icon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p id="notification-title" class="text-sm font-medium"></p>
                    <p id="notification-message" class="mt-1 text-sm text-gray-500"></p>
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

<script>
let scanner = null;
let isScanning = false;
let todayStats = { total: 0, qr: 0, manual: 0 };

function startScanner() {
    const video = document.getElementById('qr-video');
    const placeholder = document.getElementById('scanner-placeholder');
    const overlay = document.getElementById('scan-overlay');
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    
    if (!scanner) {
        scanner = new QrScanner(video, result => onScanSuccess(result), {
            onDecodeError: err => {
                // Handle decode errors silently
            },
            preferredCamera: 'environment',
            highlightScanRegion: true,
            highlightCodeOutline: true,
        });
    }
    
    scanner.start().then(() => {
        isScanning = true;
        placeholder.style.display = 'none';
        video.style.display = 'block';
        overlay.style.display = 'flex';
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-flex';
        updateScannerStatus('active', 'Scanner active - Ready to scan');
    }).catch(err => {
        console.error('Camera error:', err);
        showNotification('error', 'Camera Error', 'Unable to access camera. Please check permissions.');
        updateScannerStatus('error', 'Camera access denied');
    });
}

function stopScanner() {
    if (scanner && isScanning) {
        scanner.stop();
        isScanning = false;
        
        const video = document.getElementById('qr-video');
        const placeholder = document.getElementById('scanner-placeholder');
        const overlay = document.getElementById('scan-overlay');
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        
        video.style.display = 'none';
        overlay.style.display = 'none';
        placeholder.style.display = 'flex';
        startBtn.style.display = 'inline-flex';
        stopBtn.style.display = 'none';
        updateScannerStatus('inactive', 'Scanner stopped');
    }
}

function onScanSuccess(result) {
    if (!isScanning) return;
    
    // Temporarily stop scanning to prevent multiple scans
    isScanning = false;
    updateScannerStatus('processing', 'Processing QR code...');
    
    // Process the QR code
    processQRCode(result).then(() => {
        // Resume scanning after 2 seconds
        setTimeout(() => {
            if (scanner) {
                isScanning = true;
                updateScannerStatus('active', 'Scanner active - Ready to scan');
            }
        }, 2000);
    });
}

async function processQRCode(qrData) {
    try {
        const response = await fetch('{{ route("organizer.attendance.qr-checkin") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ qr_data: qrData })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('success', 'Check-in Successful', `${data.user.name} checked in successfully`);
            addToRecentScans(data.user, data.event, 'qr_code');
            todayStats.total++;
            todayStats.qr++;
            updateStats();
        } else {
            showNotification('error', 'Check-in Failed', data.message || 'Invalid QR code');
        }
    } catch (error) {
        console.error('QR processing error:', error);
        showNotification('error', 'Processing Error', 'Failed to process QR code');
    }
}

async function manualCheckIn(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const eventId = document.getElementById('event_id').value;
    
    if (!eventId) {
        showNotification('error', 'No Event Selected', 'Please select an event first');
        return;
    }
    
    try {
        const response = await fetch(`/organizer/attendance/event/${eventId}/manual-checkin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('success', 'Manual Check-in Successful', `${data.user.name} checked in successfully`);
            addToRecentScans(data.user, data.event, 'manual');
            todayStats.total++;
            todayStats.manual++;
            updateStats();
            event.target.reset();
        } else {
            showNotification('error', 'Check-in Failed', data.message || 'Check-in failed');
        }
    } catch (error) {
        console.error('Manual check-in error:', error);
        showNotification('error', 'Processing Error', 'Failed to process manual check-in');
    }
}

function updateScannerStatus(status, message) {
    const icon = document.getElementById('status-icon');
    const text = document.getElementById('status-text');
    
    icon.className = 'w-2 h-2 rounded-full mr-2 ';
    
    switch (status) {
        case 'active':
            icon.className += 'bg-green-400';
            break;
        case 'processing':
            icon.className += 'bg-yellow-400';
            break;
        case 'error':
            icon.className += 'bg-red-400';
            break;
        default:
            icon.className += 'bg-gray-400';
    }
    
    text.textContent = message;
}

function addToRecentScans(user, event, method) {
    const container = document.getElementById('recent-scans');
    const now = new Date();
    
    const scanItem = document.createElement('div');
    scanItem.className = 'flex items-center space-x-3 p-3 bg-gray-50 rounded-md';
    scanItem.innerHTML = `
        <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900">${user.name}</p>
            <p class="text-sm text-gray-500">${event.title}</p>
        </div>
        <div class="text-xs text-gray-400">
            ${method === 'qr_code' ? 'QR' : 'Manual'}
        </div>
    `;
    
    // Remove "No recent activity" message
    const noActivity = container.querySelector('p');
    if (noActivity && noActivity.textContent === 'No recent activity') {
        container.innerHTML = '';
    }
    
    container.insertBefore(scanItem, container.firstChild);
    
    // Keep only last 5 scans
    while (container.children.length > 5) {
        container.removeChild(container.lastChild);
    }
}

function updateStats() {
    document.getElementById('total-checkins').textContent = todayStats.total;
    document.getElementById('qr-scans').textContent = todayStats.qr;
    document.getElementById('manual-entries').textContent = todayStats.manual;
}

function showNotification(type, title, message) {
    const container = document.getElementById('notification-container');
    const notification = document.getElementById('notification');
    const icon = document.getElementById('notification-icon');
    const titleEl = document.getElementById('notification-title');
    const messageEl = document.getElementById('notification-message');
    
    // Update content
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    // Update styling based on type
    if (type === 'success') {
        icon.className = 'h-6 w-6 text-green-400';
        notification.className = 'max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4 border-green-400';
    } else {
        icon.className = 'h-6 w-6 text-red-400';
        notification.className = 'max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto border-l-4 border-red-400';
    }
    
    // Show notification
    container.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(hideNotification, 5000);
}

function hideNotification() {
    document.getElementById('notification-container').style.display = 'none';
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (scanner) {
        scanner.destroy();
    }
});
</script>
@endsection