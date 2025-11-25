<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Registration - EventSphere</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div class="text-center">
            <div class="flex justify-center items-center space-x-3 mb-4">
                <img src="{{ asset('assets/images/eventsphere-icon.svg') }}" alt="EventSphere" class="h-12 w-12">
                <span class="text-2xl font-bold text-gray-900">EventSphere</span>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Register as Event Organizer
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Join our platform to create and manage events
            </p>
        </div>
        
        <form class="mt-8 space-y-6 bg-white p-8 rounded-lg shadow-md" action="{{ route('organizer.register') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Organization Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Organization Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="org_name" class="block text-sm font-medium text-gray-700">Organization Name *</label>
                        <input id="org_name" name="org_name" type="text" required value="{{ old('org_name') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="org_email" class="block text-sm font-medium text-gray-700">Organization Email *</label>
                        <input id="org_email" name="org_email" type="email" required value="{{ old('org_email') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Organization Description</label>
                    <textarea id="description" name="description" rows="3" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input id="website" name="website" type="url" value="{{ old('website') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Contact Person Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Contact Person Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="contact_person_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input id="contact_person_name" name="contact_person_name" type="text" required value="{{ old('contact_person_name') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="contact_person_position" class="block text-sm font-medium text-gray-700">Position/Title</label>
                        <input id="contact_person_position" name="contact_person_position" type="text" value="{{ old('contact_person_position') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="contact_person_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                    <input id="contact_person_phone" name="contact_person_phone" type="tel" value="{{ old('contact_person_phone') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <!-- Address Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Address Information (Optional)</h3>
                
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Street Address</label>
                    <input id="address" name="address" type="text" value="{{ old('address') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State/Province</label>
                        <input id="state" name="state" type="text" value="{{ old('state') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input id="country" name="country" type="text" value="{{ old('country') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                        <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Account Security</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                        <input id="password" name="password" type="password" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Supporting Documents -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Supporting Documents</h3>
                <p class="text-sm text-gray-600">Upload relevant documents to support your organization registration (e.g., business license, tax certificate, etc.)</p>
                
                <div>
                    <label for="documents" class="block text-sm font-medium text-gray-700">Upload Documents (PDF only, max 10MB each)</label>
                    <input id="documents" name="documents[]" type="file" multiple accept=".pdf" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">You can select multiple PDF files. These documents will be reviewed by our admin team.</p>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Register Organization
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('organizer.login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>