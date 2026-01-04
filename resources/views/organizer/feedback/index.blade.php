@extends('organizer.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Event Feedback</h1>
        <p class="mt-2 text-sm text-gray-600">
            View feedback from participants and jury for your {{ $type === 'conference' ? 'conference' : 'innovation' }} events
        </p>
    </div>

    @if($events->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                <i class="fas fa-comment-slash text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Feedback Yet</h3>
                <p class="text-gray-500">
                    You don't have any {{ $type === 'conference' ? 'conference' : 'innovation' }} events with feedback submissions yet. 
                    Feedback will appear here once participants or jury members submit their responses.
                </p>
            </div>
        </div>
    @else
        <!-- Events List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>
                    Events with Feedback
                </h2>
                <p class="text-sm text-gray-600 mt-1">Click on an event to view detailed feedback from participants and jury</p>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($events as $event)
                    <a href="{{ route('organizer.feedback.show', $event) }}" 
                       class="flex items-center p-5 hover:bg-gray-50 transition-all duration-150 group">
                        <!-- Event Image -->
                        <div class="flex-shrink-0">
                            @if($event->featured_image)
                                <img src="{{ $event->featured_image }}" alt="{{ $event->title }}" class="w-20 h-20 rounded-lg object-cover shadow-sm">
                            @else
                                <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-lightbulb text-white text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Event Details -->
                        <div class="flex-1 ml-5">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                {{ $event->title }}
                            </h3>
                            <div class="flex items-center mt-2 space-x-4">
                                <span class="inline-flex items-center text-sm text-gray-600">
                                    <i class="far fa-calendar text-gray-400 mr-1.5"></i>
                                    {{ $event->start_date ? $event->start_date->format('M d, Y') : 'Date TBD' }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                    <i class="fas fa-flask mr-1"></i>
                                    Innovation
                                </span>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="flex items-center space-x-3 ml-6">
                            <!-- Feedback Count Badge -->
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                <i class="fas fa-comment-dots mr-1.5"></i>
                                {{ $event->feedback_count }} {{ Str::plural('Feedback', $event->feedback_count) }}
                            </span>

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
