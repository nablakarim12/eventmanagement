@extends('organizer.layouts.app')

@section('title', 'Payment Setup - ' . $event->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">💳 Payment Setup</h1>
                <p class="text-gray-600 mt-2">Configure payment settings for {{ $event->title }}</p>
            </div>
            <a href="{{ route('organizer.events.show', $event) }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Event
            </a>
        </div>
    </div>

    <!-- Event Fee Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
            <div>
                <h3 class="font-semibold text-blue-900">Registration Fee</h3>
                <p class="text-blue-800">RM {{ number_format($event->registration_fee, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Payment Setup Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <form action="{{ route('organizer.payment.save', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="p-6 space-y-6">
                <!-- QR Code Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-qrcode mr-2"></i>Payment QR Code
                    </label>
                    
                    @if($paymentSetting && $paymentSetting->qr_code_url)
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Current QR Code:</p>
                            <img src="{{ $paymentSetting->qr_code_url }}" alt="Payment QR Code" class="w-48 h-48 object-contain border border-gray-300 rounded">
                        </div>
                    @endif
                    
                    <input type="file" 
                           name="qr_code" 
                           id="qr_code"
                           accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-2 text-sm text-gray-500">Upload your bank's QR code for payment. Maximum size: 5MB</p>
                    @error('qr_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-university mr-2"></i>Bank Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="bank_name" 
                           id="bank_name"
                           value="{{ old('bank_name', $paymentSetting->bank_name ?? '') }}"
                           required
                           placeholder="e.g., Maybank, CIMB, Public Bank"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('bank_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div>
                    <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hashtag mr-2"></i>Account Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="account_number" 
                           id="account_number"
                           value="{{ old('account_number', $paymentSetting->account_number ?? '') }}"
                           required
                           placeholder="e.g., 1234567890"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('account_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Holder Name -->
                <div>
                    <label for="account_holder_name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Account Holder Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="account_holder_name" 
                           id="account_holder_name"
                           value="{{ old('account_holder_name', $paymentSetting->account_holder_name ?? '') }}"
                           required
                           placeholder="e.g., John Doe"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('account_holder_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('organizer.events.show', $event) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Save Payment Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h3 class="font-semibold text-yellow-900 mb-2">
            <i class="fas fa-lightbulb mr-2"></i>Tips
        </h3>
        <ul class="list-disc list-inside text-sm text-yellow-800 space-y-1">
            <li>Upload a clear QR code image for easy scanning by participants</li>
            <li>Double-check your account details before saving</li>
            <li>Participants will use this QR code to make payments after registration approval</li>
            <li>You can update these settings anytime</li>
        </ul>
    </div>
</div>
@endsection
