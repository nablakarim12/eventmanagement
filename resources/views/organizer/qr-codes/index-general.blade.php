@extends('organizer.layouts.app')

@section('title', 'QR Code Management')
@section('page-title', 'QR Code Management')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-qrcode text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total QR Codes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $qrCodes->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active QR Codes</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $qrCodes->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-eye text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Scans</p>
                                                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($qrCodes->sum('scan_count')) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Your Events</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $events->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search QR Codes</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by code, type, or event..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Event</label>
                    <select name="event_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="attendance" {{ request('type') === 'attendance' ? 'selected' : '' }}>Attendance</option>
                        <option value="registration" {{ request('type') === 'registration' ? 'selected' : '' }}>Registration</option>
                        <option value="access" {{ request('type') === 'access' ? 'selected' : '' }}>Access</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="is_active" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
            
            @if(request()->hasAny(['search', 'event_id', 'type', 'is_active']))
                <div class="flex justify-end">
                    <a href="{{ route('organizer.qr-codes.index') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </a>
                </div>
            @endif
        </form>
    </div>
    
    <!-- QR Codes List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($qrCodes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scans</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($qrCodes as $qrCode)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center mr-3">
                                            <i class="fas fa-qrcode text-gray-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 font-mono">{{ Str::limit($qrCode->qr_code, 20) }}</div>
                                            @if($qrCode->description)
                                                <div class="text-sm text-gray-500">{{ Str::limit($qrCode->description, 40) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $qrCode->event->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $qrCode->event->start_date->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($qrCode->type === 'attendance') bg-blue-100 text-blue-800
                                        @elseif($qrCode->type === 'registration') bg-green-100 text-green-800
                                        @elseif($qrCode->type === 'access') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($qrCode->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($qrCode->scan_count) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $qrCode->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $qrCode->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $qrCode->created_at->format('M d, Y') }}<br>
                                    <span class="text-xs text-gray-400">{{ $qrCode->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('organizer.qr-codes.show', $qrCode) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('organizer.qr-codes.download', $qrCode) }}" 
                                           class="text-green-600 hover:text-green-900" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="{{ route('organizer.events.qr-codes.index', $qrCode->event) }}" 
                                           class="text-purple-600 hover:text-purple-900" title="Manage Event QR Codes">
                                            <i class="fas fa-cogs"></i>
                                        </a>
                                        <form action="{{ route('organizer.qr-codes.destroy', $qrCode) }}" 
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this QR code?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
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
            
            @if($qrCodes->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $qrCodes->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-qrcode text-4xl mb-4"></i>
                    <p class="text-lg font-medium mb-2">No QR codes found</p>
                    @if(request()->hasAny(['search', 'event_id', 'type', 'is_active']))
                        <p class="text-sm mt-2 mb-4">Try adjusting your filters</p>
                        <a href="{{ route('organizer.qr-codes.index') }}" class="text-blue-600 hover:text-blue-800">
                            Clear filters and view all QR codes
                        </a>
                    @else
                        <p class="text-sm mb-4">You haven't created any QR codes yet</p>
                        <a href="{{ route('organizer.events.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Create QR codes from Events
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection