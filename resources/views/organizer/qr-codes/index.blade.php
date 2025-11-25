@extends('organizer.layouts.app')

@section('title', 'QR Code Management')
@section('page-title', 'QR Code Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-gray-600 mt-2">Generate and manage QR codes for event attendance tracking</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('organizer.attendance.scanner') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-camera mr-2"></i>
                    Open Scanner
                </a>
                <a href="{{ route('organizer.events.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i>
                    Create Event (Auto QR)
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-qrcode text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total QR Codes</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_qr_codes'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active QR Codes</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['active_qr_codes'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-scan text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Scans</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_scans'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar text-orange-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Events with QR</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['events_with_qr'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Auto-Generation Info Box -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl mt-1"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-blue-900 mb-2">🎉 Automatic QR Generation</h3>
                <div class="text-blue-800">
                    <p class="mb-2">QR codes are <strong>automatically generated</strong> when you create new events! Each event gets:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div class="flex items-center">
                            <i class="fas fa-sign-in-alt text-green-600 mr-3"></i>
                            <span><strong>Check-in QR Code</strong> - For participant arrival</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-sign-out-alt text-red-600 mr-3"></i>
                            <span><strong>Check-out QR Code</strong> - For participant departure</span>
                        </div>
                    </div>
                    <p class="mt-3 text-sm">💡 No manual setup required - just create your event and start scanning!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('organizer.qr-codes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Event Filter -->
                <div>
                    <label for="event_id" class="block text-sm font-medium text-gray-700">Event</label>
                    <select name="event_id" id="event_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Events</option>
                        @if(isset($events))
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <label for="qr_type" class="block text-sm font-medium text-gray-700">QR Type</label>
                    <select name="qr_type" id="qr_type" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Types</option>
                        <option value="check_in" {{ request('qr_type') == 'check_in' ? 'selected' : '' }}>Check-in</option>
                        <option value="check_out" {{ request('qr_type') == 'check_out' ? 'selected' : '' }}>Check-out</option>
                        <option value="general" {{ request('qr_type') == 'general' ? 'selected' : '' }}>General</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="is_active" id="is_active" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Search QR codes..."
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-10">
                        <div class="absolute inset-y-0 right-0 flex items-center">
                            <button type="submit" class="h-full px-3 py-2 text-gray-400 hover:text-gray-500">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="mt-4 flex justify-end">
                <button type="button" onclick="document.querySelector('form').reset(); window.location.href='{{ route('organizer.qr-codes.index') }}'" 
                        class="text-sm text-gray-500 hover:text-gray-700">
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- QR Codes Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">QR Codes</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Manage all QR codes for your events. Download, view analytics, or regenerate as needed.
            </p>
        </div>
        
        @if(isset($qrCodes) && $qrCodes->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Event & QR Info
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type & Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usage Stats
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Validity
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($qrCodes as $qrCode)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $qrCode->event->title ?? 'Unknown Event' }}</div>
                                <div class="text-sm text-gray-500">{{ $qrCode->description ?? 'QR Code' }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    ID: {{ substr($qrCode->qr_code, 0, 8) }}...
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col space-y-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($qrCode->type === 'check_in') bg-green-100 text-green-800
                                    @elseif($qrCode->type === 'check_out') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    <i class="fas 
                                        @if($qrCode->type === 'check_in') fa-sign-in-alt
                                        @elseif($qrCode->type === 'check_out') fa-sign-out-alt  
                                        @else fa-qrcode
                                        @endif mr-1"></i>
                                    {{ ucfirst(str_replace('_', '-', $qrCode->type)) }}
                                </span>
                                
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $qrCode->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $qrCode->is_active ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    {{ $qrCode->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div><strong>{{ $qrCode->scan_count }}</strong> scans</div>
                                @if($qrCode->last_scanned_at)
                                    <div class="text-xs text-gray-500">
                                        Last: {{ $qrCode->last_scanned_at->format('M j, g:i A') }}
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400">Never scanned</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                @if($qrCode->valid_from && $qrCode->valid_until)
                                    <div class="text-gray-900">{{ $qrCode->valid_from->format('M j') }} - {{ $qrCode->valid_until->format('M j, Y') }}</div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1
                                        {{ $qrCode->is_valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $qrCode->is_valid ? 'Valid' : 'Expired' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No validity set</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('organizer.qr-codes.show', $qrCode) }}" 
                                   class="text-indigo-600 hover:text-indigo-800" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('organizer.qr-codes.download', $qrCode) }}" 
                                   class="text-green-600 hover:text-green-800" title="Download QR">
                                    <i class="fas fa-download"></i>
                                </a>
                                
                                <button onclick="printQR('{{ $qrCode->id }}')" 
                                        class="text-purple-600 hover:text-purple-800" title="Print QR">
                                    <i class="fas fa-print"></i>
                                </button>
                                
                                <form action="{{ route('organizer.qr-codes.destroy', $qrCode) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this QR code?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($qrCodes, 'links'))
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $qrCodes->links() }}
            </div>
        @endif
        @else
        <div class="text-center py-12">
            <i class="fas fa-qrcode text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No QR Codes Found</h3>
            <p class="text-gray-600 mb-6">Create your first event to automatically generate QR codes!</p>
            <a href="{{ route('organizer.events.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>
                Create Event
            </a>
        </div>
        @endif
    </div>
</div>

<script>
function printQR(qrCodeId) {
    // Open QR code in a new window for printing
    const printWindow = window.open(`/organizer/qr-codes/${qrCodeId}/print`, '_blank', 'width=600,height=600');
    if (printWindow) {
        printWindow.focus();
    }
}
</script>
@endsection