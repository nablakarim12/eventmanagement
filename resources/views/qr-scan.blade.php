<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <i class="fas fa-qrcode text-6xl text-indigo-600 mb-4"></i>
                <h2 class="text-3xl font-extrabold text-gray-900">Event Check-in</h2>
                <p class="mt-2 text-sm text-gray-600">Please provide your information to register your attendance</p>
            </div>

            <!-- Event Information Card -->
            <div id="event-info" class="bg-white shadow-lg rounded-lg p-6 hidden">
                <div class="border-l-4 border-indigo-400 pl-4 mb-4">
                    <h3 id="event-title" class="text-lg font-medium text-gray-900"></h3>
                    <p id="event-description" class="text-sm text-gray-600"></p>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Date:</span>
                        <span id="event-date" class="text-gray-600"></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Time:</span>
                        <span id="event-time" class="text-gray-600"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="font-medium text-gray-700">Location:</span>
                        <span id="event-location" class="text-gray-600"></span>
                    </div>
                </div>
                <div class="mt-4">
                    <span id="qr-type-badge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"></span>
                </div>
            </div>

            <!-- Attendance Form -->
            <form id="attendance-form" class="mt-8 space-y-6 hidden">
                <div class="space-y-4">
                    <div>
                        <label for="user_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input id="user_name" name="user_name" type="text" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="user_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input id="user_email" name="user_email" type="email" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="participant_type" class="block text-sm font-medium text-gray-700">Attending as</label>
                        <select id="participant_type" name="participant_type" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="participant">Participant</option>
                            <option value="jury">Jury Member</option>
                        </select>
                    </div>
                </div>

                <div>
                    <button type="submit" id="submit-btn"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-user-check mr-2"></i>
                        <span id="submit-text">Register Attendance</span>
                    </button>
                </div>
            </form>

            <!-- Success/Error Messages -->
            <div id="message-area" class="hidden">
                <div id="success-message" class="bg-green-50 border border-green-200 rounded-md p-4 hidden">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                        <div>
                            <h3 class="text-sm font-medium text-green-800">Success!</h3>
                            <p id="success-text" class="mt-1 text-sm text-green-700"></p>
                            <div id="attendance-details" class="mt-2 text-sm text-green-600"></div>
                        </div>
                    </div>
                </div>
                
                <div id="error-message" class="bg-red-50 border border-red-200 rounded-md p-4 hidden">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-0.5"></i>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <p id="error-text" class="mt-1 text-sm text-red-700"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loading" class="text-center hidden">
                <i class="fas fa-spinner fa-spin text-2xl text-indigo-600 mb-2"></i>
                <p class="text-gray-600">Processing...</p>
            </div>
        </div>
    </div>

    <script>
        // Get QR code from URL
        const qrCode = window.location.pathname.split('/').pop();
        let qrData = null;

        // Load event information
        async function loadEventInfo() {
            try {
                const response = await fetch(`/scan/${qrCode}`);
                const data = await response.json();
                
                if (data.success) {
                    qrData = data.qr_data;
                    displayEventInfo(data.event);
                    document.getElementById('attendance-form').classList.remove('hidden');
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Failed to load event information');
            }
        }

        function displayEventInfo(event) {
            document.getElementById('event-title').textContent = event.title;
            document.getElementById('event-description').textContent = event.description;
            document.getElementById('event-date').textContent = event.date;
            document.getElementById('event-time').textContent = event.time;
            document.getElementById('event-location').textContent = event.location;
            
            const badge = document.getElementById('qr-type-badge');
            if (event.qr_type === 'check_in') {
                badge.textContent = 'Check-in QR Code';
                badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                document.getElementById('submit-text').textContent = 'Check In';
            } else if (event.qr_type === 'check_out') {
                badge.textContent = 'Check-out QR Code';
                badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
                document.getElementById('submit-text').textContent = 'Check Out';
            }
            
            document.getElementById('event-info').classList.remove('hidden');
        }

        function showError(message) {
            document.getElementById('error-text').textContent = message;
            document.getElementById('error-message').classList.remove('hidden');
            document.getElementById('message-area').classList.remove('hidden');
        }

        function showSuccess(message, details) {
            document.getElementById('success-text').textContent = message;
            if (details) {
                const detailsDiv = document.getElementById('attendance-details');
                detailsDiv.innerHTML = `
                    ${details.check_in_time ? `<div>Check-in: ${details.check_in_time}</div>` : ''}
                    ${details.check_out_time ? `<div>Check-out: ${details.check_out_time}</div>` : ''}
                    ${details.duration_hours ? `<div>Duration: ${details.duration_hours} hours</div>` : ''}
                    <div>Role: ${details.participant_type}</div>
                `;
            }
            document.getElementById('success-message').classList.remove('hidden');
            document.getElementById('message-area').classList.remove('hidden');
            document.getElementById('attendance-form').classList.add('hidden');
        }

        // Handle form submission
        document.getElementById('attendance-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            document.getElementById('loading').classList.remove('hidden');
            
            // Hide previous messages
            document.getElementById('success-message').classList.add('hidden');
            document.getElementById('error-message').classList.add('hidden');
            
            const formData = new FormData(e.target);
            formData.append('qr_id', qrData.qr_id);
            
            try {
                const response = await fetch('/scan/attendance', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess(data.message, data.attendance);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Failed to process attendance');
            } finally {
                submitBtn.disabled = false;
                document.getElementById('loading').classList.add('hidden');
            }
        });

        // Load event info on page load
        loadEventInfo();
    </script>
</body>
</html>