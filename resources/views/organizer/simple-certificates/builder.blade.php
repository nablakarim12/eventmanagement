@extends('organizer.layouts.app')

@section('title', ($eventType ?? null ? ucfirst($eventType) . ' ' : '') . 'Certificate Management - ' . $event->title)
@section('page-title', ($eventType ?? null ? ucfirst($eventType) . ' ' : '') . 'Certificate Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Certificate Management</h1>
                <p class="text-gray-600 mt-2">{{ $event->title }}</p>
                <p class="text-sm text-gray-500 mt-1">Upload certificates for each participant and jury member individually</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">
                @if($eventType === 'innovation')
                    Total Eligible
                @else
                    Total Checked In
                @endif
            </div>
            <div class="text-3xl font-bold text-blue-600 mt-2">
                @php
                    if ($eventType === 'innovation') {
                        // Innovation: All confirmed registrations
                        $totalEligible = App\Models\EventRegistration::where('event_id', $event->id)
                            ->where('status', 'confirmed')
                            ->count();
                    } else {
                        // Conference: Checked-in attendees
                        $totalEligible = App\Models\EventRegistration::where('event_id', $event->id)
                            ->whereNotNull('checked_in_at')
                            ->count();
                    }
                @endphp
                {{ $totalEligible }}
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Certificates Uploaded</div>
            <div class="text-3xl font-bold text-green-600 mt-2">
                @php
                    if ($eventType === 'innovation') {
                        $uploaded = App\Models\EventRegistration::where('event_id', $event->id)
                            ->where('status', 'confirmed')
                            ->whereNotNull('certificate_path')
                            ->count();
                    } else {
                        $uploaded = App\Models\EventRegistration::where('event_id', $event->id)
                            ->whereNotNull('checked_in_at')
                            ->whereNotNull('certificate_path')
                            ->count();
                    }
                @endphp
                {{ $uploaded }}
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">Participants</div>
            <div class="text-3xl font-bold text-purple-600 mt-2">
                @php
                    if ($eventType === 'innovation') {
                        $participants = App\Models\EventRegistration::where('event_id', $event->id)
                            ->where('status', 'confirmed')
                            ->whereDoesntHave('juryMappingsAsReviewer')
                            ->count();
                    } else {
                        $participants = App\Models\EventRegistration::where('event_id', $event->id)
                            ->whereNotNull('checked_in_at')
                            ->where('presentation_status', 'selected')
                            ->count();
                    }
                @endphp
                {{ $participants }}
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600">
                @if($eventType === 'innovation')
                    Jury
                @else
                    Reviewers
                @endif
            </div>
            <div class="text-3xl font-bold text-orange-600 mt-2">
                @php
                    if ($eventType === 'innovation') {
                        $jury = App\Models\EventRegistration::where('event_id', $event->id)
                            ->where('status', 'confirmed')
                            ->whereHas('juryMappingsAsReviewer')
                            ->count();
                    } else {
                        $jury = App\Models\EventRegistration::where('event_id', $event->id)
                            ->whereNotNull('checked_in_at')
                            ->whereHas('juryMappingsAsReviewer')
                            ->count();
                    }
                @endphp
                {{ $jury }}
            </div>
        </div>
    </div>

    <!-- Send All Certificates Button -->
    @php
        if ($eventType === 'innovation') {
            $allRegistrations = App\Models\EventRegistration::where('event_id', $event->id)
                ->where('status', 'confirmed')
                ->get();
        } else {
            $allRegistrations = App\Models\EventRegistration::where('event_id', $event->id)
                ->whereNotNull('checked_in_at')
                ->get();
        }
        $allHaveCertificates = $allRegistrations->count() > 0 && $allRegistrations->every(function($reg) {
            return !empty($reg->certificate_path);
        });
    @endphp

    @if($allRegistrations->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Send Certificates</h2>
                    <p class="text-gray-600 mt-1">
                        @if($allHaveCertificates)
                            ✅ All certificates uploaded! Ready to send.
                        @else
                            ⚠️ Upload certificates for all participants/jury before sending.
                        @endif
                    </p>
                </div>
                <form action="{{ route('organizer.simple-certificates.send-all', $event) }}" method="POST" 
                      onsubmit="return confirm('Send certificates to all {{ $allRegistrations->count() }} participants/jury?');">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-3 rounded-lg font-semibold transition {{ $allHaveCertificates ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}"
                            {{ !$allHaveCertificates ? 'disabled' : '' }}>
                        📧 Send All Certificates
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Participants/Jury Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-900">
                @if($eventType === 'innovation')
                    Participants & Jury
                @else
                    Checked-In Attendees
                @endif
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($allRegistrations as $registration)
                        @php
                            $isReviewer = $registration->juryMappingsAsReviewer->count() > 0;
                            $isPresenter = $registration->presentation_status === 'selected';
                            
                            if ($eventType === 'innovation') {
                                if ($isReviewer) {
                                    $role = 'Jury';
                                    $roleColor = 'bg-orange-100 text-orange-800';
                                } else {
                                    $role = 'Participant';
                                    $roleColor = 'bg-blue-100 text-blue-800';
                                }
                            } else {
                                if ($isReviewer && $isPresenter) {
                                    $role = 'Participant & Reviewer';
                                    $roleColor = 'bg-purple-100 text-purple-800';
                                } elseif ($isReviewer) {
                                    $role = 'Reviewer';
                                    $roleColor = 'bg-orange-100 text-orange-800';
                                } else {
                                    $role = 'Participant';
                                    $roleColor = 'bg-blue-100 text-blue-800';
                                }
                            }

                            $hasCertificate = !empty($registration->certificate_path);
                        @endphp
                        <tr class="{{ $hasCertificate ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleColor }}">
                                    {{ $role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($hasCertificate)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ Uploaded
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⏳ Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($hasCertificate)
                                    <div class="flex gap-2">
                                        <a href="{{ asset('storage/' . $registration->certificate_path) }}" target="_blank" 
                                           class="text-blue-600 hover:text-blue-800 font-medium">
                                            📄 View
                                        </a>
                                        <button onclick="showUploadModal({{ $registration->id }}, '{{ $registration->user->name }}')" 
                                                class="text-orange-600 hover:text-orange-800 font-medium">
                                            🔄 Replace
                                        </button>
                                    </div>
                                @else
                                    <button onclick="showUploadModal({{ $registration->id }}, '{{ $registration->user->name }}')" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
                                        📤 Upload Certificate
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                @if($eventType === 'innovation')
                                    No confirmed registrations found.
                                @else
                                    No checked-in attendees found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Certificate</h3>
            <p class="text-sm text-gray-600 mb-4">For: <span id="recipientName" class="font-semibold"></span></p>
            
            <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Certificate File (PDF or Image)</label>
                    <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, JPG, PNG (Max: 5MB)</p>
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeUploadModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showUploadModal(registrationId, name) {
    document.getElementById('recipientName').textContent = name;
    document.getElementById('uploadForm').action = `/organizer/simple-certificates/upload/${registrationId}`;
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadForm').reset();
}

// Close modal when clicking outside
document.getElementById('uploadModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUploadModal();
    }
});
</script>
@endsection
