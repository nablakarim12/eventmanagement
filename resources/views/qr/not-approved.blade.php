<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Not Approved</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-100 mb-4">
                <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Pending Approval</h2>
            <p class="text-gray-600 mb-4">{{ $message }}</p>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-gray-500">Event:</span>
                        <p class="font-semibold">{{ $registration->event->event_name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Your Registration:</span>
                        <p class="font-semibold">{{ $registration->registration_code }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Status:</span>
                        <p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending Approval
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-gray-500 mb-6">Please wait for the organizer to approve your registration. You will receive an email notification once approved.</p>
            
            <a href="{{ route('dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                Go to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
