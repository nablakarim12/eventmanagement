<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Event Check-In</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            @if($alreadyCheckedIn)
                <!-- Already Checked In -->
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-4">
                        <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Already Checked In</h2>
                    <p class="text-gray-600 mb-6">You checked in at {{ $registration->checked_in_at->format('h:i A, F j, Y') }}</p>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-500">Name:</span>
                                <p class="font-semibold">{{ $registration->user->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Event:</span>
                                <p class="font-semibold">{{ $registration->event->event_name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Role:</span>
                                <p class="font-semibold capitalize">
                                    @if($registration->role === 'participant')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Participant
                                        </span>
                                    @elseif($registration->role === 'jury')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Jury
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Both (Participant & Jury)
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-500">Enjoy the event!</p>
                </div>
            @else
                <!-- Check-In Confirmation -->
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 mb-4">
                        <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Confirm Check-In</h2>
                    <p class="text-gray-600 mb-6">Please confirm your attendance</p>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-500">Name:</span>
                                <p class="font-semibold">{{ $registration->user->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Email:</span>
                                <p class="font-semibold">{{ $registration->user->email }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Event:</span>
                                <p class="font-semibold">{{ $registration->event->event_name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Date:</span>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($registration->event->event_date)->format('F j, Y') }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Role:</span>
                                <p class="font-semibold capitalize">
                                    @if($registration->role === 'participant')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Participant
                                        </span>
                                    @elseif($registration->role === 'jury')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Jury
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Both (Participant & Jury)
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <button id="checkInBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                        Confirm Check-In
                    </button>
                    
                    <div id="successMessage" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-800 font-semibold">✓ Successfully checked in!</p>
                    </div>
                    
                    <div id="errorMessage" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800 font-semibold">Error checking in. Please try again.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('checkInBtn')?.addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Processing...';
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('successMessage').classList.remove('hidden');
                    btn.classList.add('bg-green-600');
                    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    btn.textContent = 'Checked In Successfully!';
                    
                    // Reload page after 2 seconds to show the "already checked in" view
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Check-in failed');
                }
            })
            .catch(error => {
                document.getElementById('errorMessage').classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = 'Try Again';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
