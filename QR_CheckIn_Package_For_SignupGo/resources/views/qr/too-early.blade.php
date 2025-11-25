<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In Not Available</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 mb-4">
                <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Too Early</h2>
            <p class="text-gray-600 mb-4">{{ $message }}</p>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-gray-500">Event:</span>
                        <p class="font-semibold">{{ $registration->event->event_name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Event Date:</span>
                        <p class="font-semibold">{{ $eventDate->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Event Time:</span>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($registration->event->start_time)->format('h:i A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Your Registration:</span>
                        <p class="font-semibold">{{ $registration->registration_code }}</p>
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-gray-500 mb-6">Check-in will be available on the event day. Save this QR code and scan it when you arrive at the event!</p>
            
            <a href="{{ route('dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                Go to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
