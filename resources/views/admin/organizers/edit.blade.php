@extends('admin.layouts.app')

@section('title', 'Edit Organizer')
@section('page-title', 'Edit Event Organizer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.organizers.index') }}" 
               class="text-blue-600 hover:text-blue-800 mr-4">
                ← Back to Organizers
            </a>
            <h1 class="text-2xl font-bold">Edit Event Organizer</h1>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('admin.organizers.update', $organizer) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Organization Information -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Organization Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="org_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Organization Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="org_name" 
                                   name="org_name" 
                                   value="{{ old('org_name', $organizer->org_name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('org_name') border-red-500 @enderror"
                                   required>
                            @error('org_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="org_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Organization Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="org_email" 
                                   name="org_email" 
                                   value="{{ old('org_email', $organizer->org_email) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('org_email') border-red-500 @enderror"
                                   required>
                            @error('org_email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="org_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Organization Description
                        </label>
                        <textarea id="org_description" 
                                  name="org_description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('org_description') border-red-500 @enderror"
                                  placeholder="Brief description of the organization...">{{ old('org_description', $organizer->org_description) }}</textarea>
                        @error('org_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                Website URL
                            </label>
                            <input type="url" 
                                   id="website" 
                                   name="website" 
                                   value="{{ old('website', $organizer->website) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('website') border-red-500 @enderror"
                                   placeholder="https://example.com">
                            @error('website')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $organizer->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Person Information -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Contact Person Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_person_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Person Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="contact_person_name" 
                                   name="contact_person_name" 
                                   value="{{ old('contact_person_name', $organizer->contact_person_name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contact_person_name') border-red-500 @enderror"
                                   required>
                            @error('contact_person_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_person_position" class="block text-sm font-medium text-gray-700 mb-2">
                                Position/Title
                            </label>
                            <input type="text" 
                                   id="contact_person_position" 
                                   name="contact_person_position" 
                                   value="{{ old('contact_person_position', $organizer->contact_person_position) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contact_person_position') border-red-500 @enderror">
                            @error('contact_person_position')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="contact_person_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="contact_person_email" 
                                   name="contact_person_email" 
                                   value="{{ old('contact_person_email', $organizer->contact_person_email) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contact_person_email') border-red-500 @enderror"
                                   required>
                            @error('contact_person_email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_person_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Phone
                            </label>
                            <input type="tel" 
                                   id="contact_person_phone" 
                                   name="contact_person_phone" 
                                   value="{{ old('contact_person_phone', $organizer->contact_person_phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contact_person_phone') border-red-500 @enderror">
                            @error('contact_person_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Address Information</h2>
                    
                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Street Address
                        </label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="{{ old('address', $organizer->address) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror">
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $organizer->city) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('city') border-red-500 @enderror">
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                State/Province
                            </label>
                            <input type="text" 
                                   id="state" 
                                   name="state" 
                                   value="{{ old('state', $organizer->state) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('state') border-red-500 @enderror">
                            @error('state')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                Country
                            </label>
                            <input type="text" 
                                   id="country" 
                                   name="country" 
                                   value="{{ old('country', $organizer->country) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('country') border-red-500 @enderror">
                            @error('country')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Account Settings</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password <span class="text-gray-500">(Leave blank to keep current)</span>
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                                required>
                            <option value="">Select Status</option>
                            <option value="pending" {{ old('status', $organizer->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status', $organizer->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status', $organizer->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.organizers.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Update Organizer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection