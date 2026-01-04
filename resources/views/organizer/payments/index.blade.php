@extends('organizer.layouts.app')

@section('title', 'Payment Verification')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-money-check-alt mr-3"></i>Payment Verification
            </h1>
            <p class="text-gray-600">Review and verify participant payment proofs</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Registrations</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Verification</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Paid</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['paid'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events List -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-alt mr-2"></i>Events with Payments
                </h2>
                <p class="text-sm text-gray-600 mt-1">Click on an event to view and manage payment verifications</p>
            </div>

            @if($eventsWithPayments->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($eventsWithPayments as $event)
                        <a href="{{ route('organizer.payment-verification.event', $event->id) }}" 
                           class="flex items-center p-5 hover:bg-gray-50 transition-all duration-150 group">
                            <!-- Event Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-money-check-alt text-white text-2xl"></i>
                                </div>
                            </div>

                            <!-- Event Details -->
                            <div class="flex-1 ml-5">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-600 transition-colors">
                                    {{ $event->title }}
                                </h3>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="inline-flex items-center text-sm text-gray-600">
                                        <i class="fas fa-tag text-gray-400 mr-1.5"></i>
                                        {{ $event->category->name }}
                                    </span>
                                    <span class="inline-flex items-center text-sm text-gray-600">
                                        <i class="fas fa-dollar-sign text-gray-400 mr-1.5"></i>
                                        RM {{ number_format($event->registration_fee, 2) }}
                                    </span>
                                    <span class="inline-flex items-center text-sm text-gray-600">
                                        <i class="far fa-calendar text-gray-400 mr-1.5"></i>
                                        {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'TBA' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Statistics -->
                            <div class="flex items-center space-x-3 ml-6">
                                <!-- Paid Badge -->
                                @if($event->paid_count > 0)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-green-50 text-green-700 border border-green-200">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        {{ $event->paid_count }} Paid
                                    </span>
                                @endif

                                <!-- Pending Badge -->
                                @if($event->pending_payment > 0)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                        <i class="fas fa-clock mr-1.5"></i>
                                        {{ $event->pending_payment }} Pending
                                    </span>
                                @endif

                                <!-- Rejected Badge -->
                                @if($event->rejected_payment > 0)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-red-50 text-red-700 border border-red-200">
                                        <i class="fas fa-times-circle mr-1.5"></i>
                                        {{ $event->rejected_payment }} Rejected
                                    </span>
                                @endif

                                <!-- Arrow Icon -->
                                <div class="pl-4">
                                    <i class="fas fa-chevron-right text-gray-400 text-lg group-hover:text-green-600 group-hover:translate-x-1 transition-all"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-money-bill-wave text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No payment events yet</h3>
                    <p class="text-gray-500">Events with registration fees will appear here</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
