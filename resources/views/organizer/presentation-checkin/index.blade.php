@extends('organizer.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                        Presentation Check-In
                    </h1>
                    <p class="text-gray-600 mt-2">{{ $event->title }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('organizer.template-certificates.index', $event) }}" 
                       class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Certificates
                    </a>
                    <a href="{{ route('organizer.presentation-checkin.scanner', $event) }}" 
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        QR Scanner
                    </a>
                    <form action="{{ route('organizer.presentation-checkin.generate-all-qr', $event) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Generate All QR Codes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-3xl font-bold text-purple-600">{{ $stats['total_approved'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Approved</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-3xl font-bold text-green-600">{{ $stats['checked_in'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Checked In</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-3xl font-bold text-orange-600">{{ $stats['pending'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Pending</div>
            </div>
        </div>

        <!-- Participants List -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Approved Participants</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Queue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($participants as $participant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    @if($participant->presentation_queue)
                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-100 text-purple-700 font-bold">
                                            #{{ $participant->presentation_queue }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $participant->user->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $participant->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-600">
                                    {{ $participant->presentation_time ? \Carbon\Carbon::parse($participant->presentation_time)->format('M d, h:i A') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($participant->average_score)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                                            @if($participant->average_score >= 80) bg-green-100 text-green-800
                                            @elseif($participant->average_score >= 60) bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ number_format($participant->average_score, 1) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($participant->qr_code_url)
                                        <button onclick="showQr('{{ $participant->qr_code_url }}', '{{ $participant->user->name }}')" 
                                                class="text-purple-600 hover:text-purple-800">
                                            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-gray-400">Not generated</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($participant->attendance)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            ✓ Checked In<br>
                                            <span class="text-xs">{{ \Carbon\Carbon::parse($participant->attendance->check_in_time)->format('h:i A') }}</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($participant->attendance)
                                        <form action="{{ route('organizer.presentation-checkin.undo-checkin', [$event, $participant]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Undo check-in for this participant?')" 
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                Undo
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('organizer.presentation-checkin.manual-checkin', [$event, $participant]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                                Manual Check-In
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No approved participants yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 id="qrModalTitle" class="text-lg font-bold text-gray-900">QR Code</h3>
            <button onclick="closeQrModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="text-center">
            <img id="qrImage" src="" alt="QR Code" class="mx-auto border-4 border-gray-200 rounded-lg">
            <button onclick="downloadQr()" class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Download QR Code
            </button>
        </div>
    </div>
</div>

<script>
let currentQrUrl = '';

function showQr(url, name) {
    currentQrUrl = url;
    document.getElementById('qrImage').src = url;
    document.getElementById('qrModalTitle').textContent = 'QR Code - ' + name;
    document.getElementById('qrModal').classList.remove('hidden');
}

function closeQrModal() {
    document.getElementById('qrModal').classList.add('hidden');
}

function downloadQr() {
    window.open(currentQrUrl, '_blank');
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('qrModal');
    if (event.target === modal) {
        closeQrModal();
    }
}
</script>
@endsection
