@extends('organizer.layouts.app')

@section('title', 'Jury Mapping - ' . ucfirst($eventType ?? 'All') . ' Events')
@section('page-title', 'Jury Mapping - ' . ucfirst($eventType ?? 'All') . ' Events')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-gray-600 mt-2">Assign reviewers to participants with category constraints</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600">
                    {{ $events->count() }} Events
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a 1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Info Alert -->
    <div class="rounded-md bg-blue-50 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Jury Mapping Constraints</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Users with both roles (participant + reviewer) <strong>cannot review their own paper</strong></li>
                        <li>Reviewers <strong>cannot evaluate participants from the same category</strong></li>
                        @if($eventType === 'innovation')
                        <li><strong>Jury cannot evaluate participants from the same theme</strong> (Innovation only)</li>
                        @endif
                        <li>Example: EdTech reviewer ❌ → EdTech participant (Invalid)</li>
                        <li>Example: Learning Analytics reviewer ✅ → Digital Pedagogy participant (Valid)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if($events->isEmpty())
        <!-- No Events -->
        <div class="bg-white shadow rounded-lg">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No {{ ucfirst($eventType ?? 'Conference') }} Events with Registrations</h3>
                <p class="mt-1 text-sm text-gray-500">Create a {{ $eventType ?? 'conference' }} event and approve participants/reviewers to start mapping.</p>
                <div class="mt-6">
                    <a href="{{ route('organizer.events.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Event
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Events List -->
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-gavel mr-2 text-indigo-600"></i>
                    Events with Jury Mapping
                </h2>
                <p class="text-sm text-gray-600 mt-1">Click on an event to manage jury-participant assignments</p>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($events as $event)
                    <a href="{{ route('organizer.jury-mapping.show', $event) }}" 
                       class="flex items-center p-5 hover:bg-gray-50 transition-all duration-150 group">
                        <!-- Event Icon -->
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm relative">
                                <i class="fas fa-gavel text-white text-2xl"></i>
                                <!-- Progress Badge -->
                                <div class="absolute -top-2 -right-2 w-10 h-10 rounded-full {{ $event->mapping_percentage >= 100 ? 'bg-green-500' : ($event->mapping_percentage >= 50 ? 'bg-yellow-500' : 'bg-red-500') }} flex items-center justify-center text-white text-xs font-bold shadow-lg">
                                    {{ number_format($event->mapping_percentage, 0) }}%
                                </div>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div class="flex-1 ml-5">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                {{ $event->title }}
                            </h3>
                            <div class="flex items-center mt-2 space-x-4">
                                <span class="inline-flex items-center text-sm text-gray-600">
                                    <i class="far fa-calendar text-gray-400 mr-1.5"></i>
                                    {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'Date TBD' }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ ucfirst(str_replace('_', ' ', $event->delivery_mode ?? 'Conference')) }}
                                </span>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="flex items-center space-x-3 ml-6">
                            <!-- Participants -->
                            <div class="flex items-center px-3 py-1.5 bg-indigo-50 rounded-lg border border-indigo-200">
                                <i class="fas fa-user text-indigo-600 mr-2"></i>
                                <span class="text-sm font-semibold text-indigo-700">{{ $event->participants_count }} Participants</span>
                            </div>

                            <!-- Reviewers -->
                            <div class="flex items-center px-3 py-1.5 bg-green-50 rounded-lg border border-green-200">
                                <i class="fas fa-user-tie text-green-600 mr-2"></i>
                                <span class="text-sm font-semibold text-green-700">{{ $event->reviewers_count }} Reviewers</span>
                            </div>

                            <!-- Assignments -->
                            <div class="flex items-center px-3 py-1.5 bg-blue-50 rounded-lg border border-blue-200">
                                <i class="fas fa-link text-blue-600 mr-2"></i>
                                <span class="text-sm font-semibold text-blue-700">{{ $event->total_mappings }} Assigned</span>
                            </div>

                            <!-- Not Mapped Warning -->
                            @if($event->participants_without_reviewer > 0)
                                <div class="flex items-center px-3 py-1.5 bg-red-50 rounded-lg border border-red-200">
                                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                    <span class="text-sm font-semibold text-red-700">{{ $event->participants_without_reviewer }} Not Mapped</span>
                                </div>
                            @endif

                            <!-- Arrow Icon -->
                            <div class="pl-4">
                                <i class="fas fa-chevron-right text-gray-400 text-lg group-hover:text-indigo-600 group-hover:translate-x-1 transition-all"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
