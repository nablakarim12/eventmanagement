@extends('organizer.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-indigo-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('organizer.presentation-checkin.index', $event) }}" class="text-purple-600 hover:text-purple-800 mb-2 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Check-In List
            </a>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                QR Code Scanner
            </h1>
            <p class="text-gray-600 mt-2">{{ $event->title }}</p>
        </div>

        <!-- Scanner Card -->
        <div class="bg-white rounded-xl shadow-xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-100 mb-4">
                    <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Scan Participant QR Code</h2>
                <p class="text-gray-600 mt-2">Position the QR code within the frame to check-in</p>
            </div>

            <!-- Video Scanner -->
            <div id="reader" class="rounded-lg overflow-hidden border-4 border-purple-200 mb-6"></div>

            <!-- Manual Input -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Or Enter QR Code Manually</h3>
                <div class="flex gap-3">
                    <input type="text" id="manualQrInput" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Paste QR code data here...">
                    <button onclick="processManualInput()" 
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Submit
                    </button>
                </div>
            </div>

            <!-- Status Messages -->
            <div id="statusMessage" class="mt-6 hidden">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>

        <!-- Recent Check-Ins -->
        <div class="bg-white rounded-xl shadow-xl p-6 mt-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Recent Check-Ins</h3>
            <div id="recentCheckIns" class="space-y-3">
                <p class="text-gray-500 text-center py-4">No check-ins yet</p>
            </div>
        </div>
    </div>
</div>

<!-- Include Html5-QRCode library -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
const eventId = {{ $event->id }};
const scanUrl = "{{ route('organizer.presentation-checkin.process-scan', $event) }}";
const csrfToken = "{{ csrf_token() }}";
let recentCheckIns = [];

// Initialize QR Scanner
function onScanSuccess(decodedText, decodedResult) {
    processQrCode(decodedText);
}

function onScanError(errorMessage) {
    // Ignore errors during scanning
}

const html5QrCode = new Html5Qrcode("reader");
const qrCodeSuccessCallback = (decodedText, decodedResult) => {
    onScanSuccess(decodedText, decodedResult);
};

html5QrCode.start(
    { facingMode: "environment" },
    {
        fps: 10,
        qrbox: { width: 250, height: 250 }
    },
    qrCodeSuccessCallback
).catch(err => {
    console.error("Unable to start scanning", err);
    showStatus('error', 'Camera access denied. Please use manual input.');
});

// Process QR Code
function processQrCode(qrData) {
    fetch(scanUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ qr_data: qrData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatus('success', data.message);
            addRecentCheckIn(data.participant);
            playSuccessSound();
        } else {
            showStatus('error', data.message);
            playErrorSound();
        }
    })
    .catch(error => {
        showStatus('error', 'Error processing QR code: ' + error.message);
        playErrorSound();
    });
}

// Manual Input
function processManualInput() {
    const input = document.getElementById('manualQrInput');
    const qrData = input.value.trim();
    
    if (!qrData) {
        showStatus('error', 'Please enter QR code data');
        return;
    }
    
    processQrCode(qrData);
    input.value = '';
}

// Show Status
function showStatus(type, message) {
    const statusDiv = document.getElementById('statusMessage');
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const icon = type === 'success' 
        ? '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
        : '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
    
    statusDiv.innerHTML = `
        <div class="flex items-center p-4 border rounded-lg ${bgColor}">
            ${icon}
            <span class="ml-3 font-medium">${message}</span>
        </div>
    `;
    statusDiv.classList.remove('hidden');
    
    setTimeout(() => {
        statusDiv.classList.add('hidden');
    }, 5000);
}

// Add Recent Check-In
function addRecentCheckIn(participant) {
    recentCheckIns.unshift(participant);
    if (recentCheckIns.length > 5) {
        recentCheckIns.pop();
    }
    
    const container = document.getElementById('recentCheckIns');
    container.innerHTML = recentCheckIns.map(p => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div>
                <div class="font-semibold text-gray-900">${p.name}</div>
                <div class="text-sm text-gray-600">${p.email}</div>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-green-600">${p.time}</div>
                ${p.queue ? '<div class="text-xs text-gray-500">Queue #' + p.queue + '</div>' : ''}
            </div>
        </div>
    `).join('');
}

// Sound Effects
function playSuccessSound() {
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuByvLTfzMGHm7A7+OZUA4NVKXh8bVlHwU='); audio.play();
}

function playErrorSound() {
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACAgICA/v7++fn5/f39/Pz8+/v7+vr6+fn5/f3+/Pz8+/v7+vr6+fn5/Pz9/f3+/v7++/v7+vr6+fn5/f39/Pz8+/v7+vr6+Pj4+/v8/f3+/v7++/v7+vr6+fn5/Pz9/f3+'); audio.play();
}

// Allow Enter key for manual input
document.getElementById('manualQrInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        processManualInput();
    }
});
</script>
@endsection
