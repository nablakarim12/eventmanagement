@extends('admin.layouts.app')

@section('title', 'QR Code Scanner')
@section('page-title', 'QR Code Scanner')

@section('styles')
<style>
    #reader {
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
    }
    .scan-result {
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Scanner Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl mt-0.5"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">QR Code Scanner Instructions</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Allow camera access when prompted</li>
                        <li>Position QR code within the scanning area</li>
                        <li>The scanner will automatically detect and process QR codes</li>
                        <li>Results will be displayed below the scanner</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scanner Controls -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">QR Code Scanner</h3>
            <div class="flex space-x-2">
                <button id="startBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-play mr-2"></i>Start Scanner
                </button>
                <button id="stopBtn" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors" style="display: none;">
                    <i class="fas fa-stop mr-2"></i>Stop Scanner
                </button>
            </div>
        </div>
        
        <!-- Camera Feed -->
        <div class="relative">
            <div id="reader" style="min-height: 400px; background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                <div class="text-gray-500 text-center">
                    <i class="fas fa-camera text-4xl mb-4"></i>
                    <p>Click "Start Scanner" to begin</p>
                </div>
            </div>
        </div>
        
        <!-- Manual Input Option -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Manual QR Code Entry</h4>
            <div class="flex space-x-2">
                <input type="text" id="manualInput" placeholder="Enter QR code manually..."
                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button id="processManual" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Process
                </button>
            </div>
        </div>
    </div>
    
    <!-- Scan Results -->
    <div id="scanResults" class="space-y-4" style="display: none;">
        <!-- Results will be dynamically inserted here -->
    </div>
    
    <!-- Recent Scans -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Scans</h3>
        <div id="recentScans" class="space-y-2">
            <p class="text-gray-500 text-center py-8">No recent scans</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let html5QrcodeScanner;
    let isScanning = false;
    let recentScans = [];
    
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const manualInput = document.getElementById('manualInput');
    const processManualBtn = document.getElementById('processManual');
    const scanResults = document.getElementById('scanResults');
    const recentScansContainer = document.getElementById('recentScans');
    
    // Start scanner
    startBtn.addEventListener('click', function() {
        if (!isScanning) {
            startScanner();
        }
    });
    
    // Stop scanner
    stopBtn.addEventListener('click', function() {
        if (isScanning) {
            stopScanner();
        }
    });
    
    // Manual processing
    processManualBtn.addEventListener('click', function() {
        const qrCode = manualInput.value.trim();
        if (qrCode) {
            processQrCode(qrCode, 'manual');
            manualInput.value = '';
        }
    });
    
    // Manual input on Enter key
    manualInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            processManualBtn.click();
        }
    });
    
    function startScanner() {
        html5QrcodeScanner = new Html5Qrcode("reader");
        
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            processQrCode(decodedText, 'scanner');
        };
        
        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };
        
        html5QrcodeScanner.start(
            { facingMode: "environment" }, 
            config, 
            qrCodeSuccessCallback
        ).then(() => {
            isScanning = true;
            startBtn.style.display = 'none';
            stopBtn.style.display = 'inline-flex';
        }).catch(err => {
            console.error('Error starting scanner:', err);
            alert('Failed to start camera. Please check camera permissions.');
        });
    }
    
    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                isScanning = false;
                startBtn.style.display = 'inline-flex';
                stopBtn.style.display = 'none';
                
                // Reset reader content
                document.getElementById('reader').innerHTML = `
                    <div class="text-gray-500 text-center">
                        <i class="fas fa-camera text-4xl mb-4"></i>
                        <p>Click "Start Scanner" to begin</p>
                    </div>
                `;
            }).catch(err => {
                console.error('Error stopping scanner:', err);
            });
        }
    }
    
    function processQrCode(qrCode, method) {
        // Show loading state
        showScanResult({
            success: null,
            message: 'Processing QR code...',
            qr_code: qrCode,
            method: method
        }, true);
        
        fetch('{{ route("admin.qr.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qr_code: qrCode
            })
        })
        .then(response => response.json())
        .then(data => {
            showScanResult({
                ...data,
                qr_code: qrCode,
                method: method
            });
            
            // Add to recent scans
            addToRecentScans({
                qr_code: qrCode,
                result: data,
                timestamp: new Date().toLocaleString(),
                method: method
            });
        })
        .catch(error => {
            console.error('Error:', error);
            showScanResult({
                success: false,
                message: 'Network error occurred',
                qr_code: qrCode,
                method: method
            });
        });
    }
    
    function showScanResult(result, isLoading = false) {
        const resultHtml = `
            <div class="scan-result bg-white rounded-lg shadow p-6 border-l-4 ${
                isLoading ? 'border-yellow-500' : 
                result.success ? 'border-green-500' : 'border-red-500'
            }">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        ${isLoading ? 
                            '<i class="fas fa-spinner fa-spin text-yellow-500 text-xl"></i>' :
                            result.success ? 
                                '<i class="fas fa-check-circle text-green-500 text-xl"></i>' :
                                '<i class="fas fa-times-circle text-red-500 text-xl"></i>'
                        }
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-lg font-medium ${
                            isLoading ? 'text-yellow-800' :
                            result.success ? 'text-green-800' : 'text-red-800'
                        }">
                            ${isLoading ? 'Processing...' : result.message}
                        </h3>
                        
                        <div class="mt-2 text-sm text-gray-600">
                            <p><strong>QR Code:</strong> <span class="font-mono">${result.qr_code}</span></p>
                            <p><strong>Method:</strong> ${result.method === 'scanner' ? 'Camera Scanner' : 'Manual Entry'}</p>
                            <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
                        </div>
                        
                        ${result.event && !isLoading ? `
                            <div class="mt-4 p-3 bg-gray-50 rounded">
                                <h4 class="font-medium text-gray-800">Event Information</h4>
                                <p class="text-sm text-gray-600"><strong>Event:</strong> ${result.event.title}</p>
                                <p class="text-sm text-gray-600"><strong>Date:</strong> ${new Date(result.event.start_date).toLocaleDateString()}</p>
                            </div>
                        ` : ''}
                        
                        ${result.user && !isLoading ? `
                            <div class="mt-3 p-3 bg-blue-50 rounded">
                                <h4 class="font-medium text-blue-800">Participant Information</h4>
                                <p class="text-sm text-blue-600"><strong>Name:</strong> ${result.user.name}</p>
                                <p class="text-sm text-blue-600"><strong>Email:</strong> ${result.user.email}</p>
                                ${result.attendance_action ? `
                                    <p class="text-sm text-blue-600"><strong>Action:</strong> ${result.attendance_action.replace('_', ' ').toUpperCase()}</p>
                                ` : ''}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        
        scanResults.innerHTML = resultHtml;
        scanResults.style.display = 'block';
        
        // Auto-hide loading results
        if (isLoading) {
            setTimeout(() => {
                // This will be replaced by the actual result
            }, 5000);
        }
    }
    
    function addToRecentScans(scanData) {
        recentScans.unshift(scanData);
        
        // Keep only the last 10 scans
        if (recentScans.length > 10) {
            recentScans = recentScans.slice(0, 10);
        }
        
        updateRecentScansDisplay();
    }
    
    function updateRecentScansDisplay() {
        if (recentScans.length === 0) {
            recentScansContainer.innerHTML = '<p class="text-gray-500 text-center py-8">No recent scans</p>';
            return;
        }
        
        const recentHtml = recentScans.map(scan => `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded border">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        ${scan.result.success ? '✅' : '❌'} 
                        <span class="font-mono">${scan.qr_code.substring(0, 20)}${scan.qr_code.length > 20 ? '...' : ''}</span>
                    </p>
                    <p class="text-xs text-gray-500">${scan.timestamp} • ${scan.method}</p>
                </div>
                <div class="text-sm text-gray-600">
                    ${scan.result.success ? scan.result.message : 'Failed'}
                </div>
            </div>
        `).join('');
        
        recentScansContainer.innerHTML = recentHtml;
    }
});
</script>
@endsection