@extends('organizer.layouts.app')

@section('title', ($eventType ?? null) ? ucfirst($eventType) . ' Evaluation Results' : 'Evaluation Results')
@section('page-title', ($eventType ?? null) ? ucfirst($eventType) . ' Evaluation Results' : 'Evaluation Results')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                {{ $eventType ? ucfirst($eventType) . ' ' : '' }}Evaluation Results
            </h1>
            <p class="text-gray-600 mt-2">View rubric scores and jury evaluations for your events</p>
        </div>

        <!-- Events List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>
                    Events with Evaluations
                </h2>
                <p class="text-sm text-gray-600 mt-1">Click on an event to view rubric scores and jury evaluations</p>
            </div>

            @forelse($events as $event)
                <div class="divide-y divide-gray-200">
                    <a href="{{ route('organizer.evaluation-results.show-event', $event) }}" 
                       class="flex items-center p-5 hover:bg-gray-50 transition-all duration-150 group {{ $event->evaluation_count == 0 ? 'pointer-events-none opacity-50' : '' }}">
                        <!-- Event Image -->
                        <div class="flex-shrink-0">
                            @if($event->poster_image)
                                <img src="{{ asset('storage/' . $event->poster_image) }}" 
                                     alt="{{ $event->title }}" 
                                     class="w-20 h-20 rounded-lg object-cover shadow-sm">
                            @else
                                <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-chart-bar text-white text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Event Details -->
                        <div class="flex-1 ml-5">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                {{ $event->title }}
                            </h3>
                            <div class="flex items-center mt-2">
                                <span class="inline-flex items-center text-sm text-gray-600">
                                    <i class="far fa-calendar text-gray-400 mr-1.5"></i>
                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="flex items-center space-x-3 ml-6">
                            <!-- Submissions -->
                            <div class="flex items-center px-3 py-1.5 bg-indigo-50 rounded-lg border border-indigo-200">
                                <i class="fas fa-file-alt text-indigo-600 mr-2"></i>
                                <span class="text-sm font-semibold text-indigo-700">{{ $event->paper_submissions_count ?? 0 }} Submissions</span>
                            </div>

                            <!-- Evaluations -->
                            @if($event->evaluation_count > 0)
                                <div class="flex items-center px-3 py-1.5 bg-purple-50 rounded-lg border border-purple-200">
                                    <i class="fas fa-check-circle text-purple-600 mr-2"></i>
                                    <span class="text-sm font-semibold text-purple-700">{{ $event->evaluation_count }} Evaluated</span>
                                </div>
                            @else
                                <div class="flex items-center px-3 py-1.5 bg-gray-50 rounded-lg border border-gray-200">
                                    <i class="fas fa-exclamation-circle text-gray-400 mr-2"></i>
                                    <span class="text-sm font-semibold text-gray-500">No Evaluations</span>
                                </div>
                            @endif

                            <!-- Arrow Icon -->
                            @if($event->evaluation_count > 0)
                                <div class="pl-4">
                                    <i class="fas fa-chevron-right text-gray-400 text-lg group-hover:text-indigo-600 group-hover:translate-x-1 transition-all"></i>
                                </div>
                            @endif
                        </div>
                    </a>
                </div>
            @empty
                <!-- Empty State remains unchanged for readability -->
                <div class="col-span-full bg-white rounded-xl shadow-lg p-12 text-center">
                    <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Events Found</h3>
                    <p class="text-gray-500 mb-6">You haven't created any events yet.</p>
                    <a href="{{ route('organizer.events.create') }}" 
                       class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-300">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Create Your First Event
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
