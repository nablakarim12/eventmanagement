@extends('admin.layouts.app')

@section('title', 'Event Organizers')
@section('page-title', 'Event Organizers Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold">Event Organizers</h1>
        <a href="{{ route('admin.organizers.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Organizer
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Mobile Card View (Hidden on larger screens) -->
    <div class="lg:hidden space-y-4">
        @forelse($organizers as $organizer)
            <div class="bg-white shadow-md rounded-lg p-4 border">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-900">{{ $organizer->org_name }}</h3>
                        <p class="text-sm text-gray-600">{{ $organizer->org_email }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs leading-5 font-semibold rounded-full
                        @if($organizer->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($organizer->status === 'approved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($organizer->status) }}
                    </span>
                </div>
                
                <div class="text-sm text-gray-600 mb-3">
                    <p><strong>Contact:</strong> {{ $organizer->contact_person_name }}</p>
                    @if($organizer->contact_person_position)
                        <p><strong>Position:</strong> {{ $organizer->contact_person_position }}</p>
                    @endif
                    <p><strong>Created:</strong> {{ $organizer->created_at->format('M d, Y') }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.organizers.show', $organizer) }}" 
                       class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm hover:bg-blue-200">View</a>
                    <a href="{{ route('admin.organizers.edit', $organizer) }}" 
                       class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded text-sm hover:bg-indigo-200">Edit</a>
                    <form action="{{ route('admin.organizers.destroy', $organizer) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this organizer? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-100 text-red-800 px-3 py-1 rounded text-sm hover:bg-red-200">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white shadow-md rounded-lg p-8 text-center">
                <p class="text-gray-500">No event organizers found.</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View (Hidden on smaller screens) -->
    <div class="hidden lg:block bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organization</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($organizers as $organizer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($organizer->org_name, 30) }}</div>
                                    @if($organizer->website)
                                        <div class="text-sm text-gray-500">{{ Str::limit($organizer->website, 25) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($organizer->org_email, 25) }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($organizer->contact_person_name, 20) }}</div>
                                @if($organizer->contact_person_position)
                                    <div class="text-sm text-gray-500">{{ Str::limit($organizer->contact_person_position, 20) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($organizer->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($organizer->status === 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($organizer->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $organizer->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-4 text-sm font-medium">
                                <div class="flex flex-col space-y-1">
                                    <a href="{{ route('admin.organizers.show', $organizer) }}" 
                                       class="text-blue-600 hover:text-blue-900 text-xs">View</a>
                                    <a href="{{ route('admin.organizers.edit', $organizer) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-xs">Edit</a>
                                    <form action="{{ route('admin.organizers.destroy', $organizer) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this organizer? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-left text-xs">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No event organizers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($organizers->hasPages())
        <div class="mt-6">
            {{ $organizers->links() }}
        </div>
    @endif
</div>
@endsection