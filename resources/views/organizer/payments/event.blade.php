@extends('organizer.layouts.app')

@section('title', 'Payment Verification - ' . $event->title)

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('organizer.payment-verification.index') }}" class="text-blue-600 hover:text-blue-800 mb-3 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Back to All Events
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->title }}</h1>
            <p class="text-gray-600">Registration Fee: RM {{ number_format($event->registration_fee, 2) }}</p>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-users text-blue-600 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm text-blue-700 font-medium">Total</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-600 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm text-yellow-700 font-medium">Pending</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm text-green-700 font-medium">Paid</p>
                        <p class="text-2xl font-bold text-green-900">{{ $stats['paid'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm text-red-700 font-medium">Rejected</p>
                        <p class="text-2xl font-bold text-red-900">{{ $stats['rejected'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registrations List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Payment Submissions</h2>
            </div>

            @if($registrations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Participant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($registrations as $registration)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $registration->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($registration->role === 'participant')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Participant</span>
                                        @elseif($registration->role === 'jury')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Jury</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Both</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $registration->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($registration->payment_status === 'approved')
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Paid
                                            </span>
                                        @elseif($registration->payment_status === 'pending' && $registration->payment_receipt_path)
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>Pending Verification
                                            </span>
                                        @elseif($registration->payment_status === 'rejected')
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>Rejected
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-hourglass mr-1"></i>No Proof
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($registration->payment_receipt_path)
                                            <a href="{{ route('organizer.payment-verification.show', $registration->id) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                <i class="fas fa-eye mr-2"></i>View Details
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-xs">No proof uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-receipt text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No registrations yet</h3>
                    <p class="text-gray-500">Payment submissions will appear here</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
