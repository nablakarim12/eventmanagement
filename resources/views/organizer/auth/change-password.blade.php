@extends('organizer.layouts.app')

@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-key text-2xl text-orange-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Change Password</h2>
                <p class="text-gray-600 mt-2">Update your account password</p>
            </div>

            <form action="{{ route('organizer.change-password.update') }}" method="POST">
                @csrf
                
                <!-- Current Password -->
                <div class="mb-6">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Current Password
                    </label>
                    <input type="password" 
                           name="current_password" 
                           id="current_password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('current_password') border-red-500 @enderror" 
                           required>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>New Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('password') border-red-500 @enderror" 
                           required
                           minlength="8">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Password must be at least 8 characters long.</p>
                </div>

                <!-- Confirm New Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>Confirm New Password
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                           required
                           minlength="8">
                </div>

                <!-- Submit Button -->
                <div class="mb-4">
                    <button type="submit" 
                            class="w-full bg-orange-600 text-white py-3 px-6 rounded-lg hover:bg-orange-700 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>Update Password
                    </button>
                </div>

                <!-- Back to Dashboard -->
                <div class="text-center">
                    <a href="{{ route('organizer.dashboard') }}" 
                       class="inline-flex items-center text-gray-600 hover:text-gray-800 text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Password confirmation validation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    
    document.getElementById('password').addEventListener('input', function() {
        const confirmPassword = document.getElementById('password_confirmation');
        confirmPassword.dispatchEvent(new Event('input'));
    });
</script>
@endsection