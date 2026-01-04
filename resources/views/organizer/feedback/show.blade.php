@extends('organizer.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('organizer.feedback.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium mb-2 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Events
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $event->title }}</h1>
                <p class="mt-1 text-sm text-gray-600">Event Feedback Overview</p>
            </div>
            <a href="{{ route('organizer.feedback.export', $event) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-150">
                <i class="fas fa-file-download mr-2"></i>
                Export to CSV
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Participant Statistics -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-sm p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Participant Feedback</h2>
                <div class="flex items-center justify-center w-12 h-12 bg-blue-600 rounded-full">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
            
            <div class="mb-6">
                <div class="flex items-baseline">
                    <span class="text-4xl font-bold text-blue-600">{{ $participantAverages['count'] }}</span>
                    <span class="ml-2 text-gray-600">{{ Str::plural('response', $participantAverages['count']) }}</span>
                </div>
            </div>

            <div class="space-y-3">
                <!-- Overall Rating -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Overall Rating</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($participantAverages['overall_rating'], 1) }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($participantAverages['overall_rating']/5)*100 }}%"></div>
                    </div>
                </div>

                <!-- Content Rating -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Content Quality</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($participantAverages['content_rating'], 1) }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($participantAverages['content_rating']/5)*100 }}%"></div>
                    </div>
                </div>

                <!-- Organization Rating -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Organization</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($participantAverages['organization_rating'], 1) }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($participantAverages['organization_rating']/5)*100 }}%"></div>
                    </div>
                </div>

                <!-- Venue Rating -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Venue</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($participantAverages['venue_rating'], 1) }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($participantAverages['venue_rating']/5)*100 }}%"></div>
                    </div>
                </div>

                <!-- Recommendation Rate -->
                <div class="pt-3 border-t border-blue-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Would Recommend</span>
                        <span class="text-lg font-bold text-green-600">{{ $participantAverages['would_recommend_percentage'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jury Statistics -->
        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-lg shadow-sm p-6 border border-orange-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Jury Feedback</h2>
                <div class="flex items-center justify-center w-12 h-12 bg-orange-600 rounded-full">
                    <i class="fas fa-user-tie text-white text-xl"></i>
                </div>
            </div>
            
            <div class="mb-6">
                <div class="flex items-baseline">
                    <span class="text-4xl font-bold text-orange-600">{{ $juryAverages['count'] }}</span>
                    <span class="ml-2 text-gray-600">{{ Str::plural('response', $juryAverages['count']) }}</span>
                </div>
            </div>

            @if($juryAverages['count'] > 0)
            <div class="space-y-3">
                <!-- Overall Rating -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Overall Rating</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($juryAverages['overall_rating'], 1) }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ ($juryAverages['overall_rating']/5)*100 }}%"></div>
                    </div>
                </div>

                <!-- Content Rating -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Content Quality</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($juryAverages['content_rating'], 1) }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ ($juryAverages['content_rating']/5)*100 }}%"></div>
                    </div>
                </div>

                <!-- Organization Rating -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Organization</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($juryAverages['organization_rating'], 1) }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ ($juryAverages['organization_rating']/5)*100 }}%"></div>
                    </div>
                </div>

                <!-- Platform Rating -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Platform</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($juryAverages['platform_rating'], 1) }}/5</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ ($juryAverages['platform_rating']/5)*100 }}%"></div>
                    </div>
                </div>

                <!-- Recommendation Rate -->
                <div class="pt-3 border-t border-orange-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Would Recommend</span>
                        <span class="text-lg font-bold text-green-600">{{ $juryAverages['would_recommend_percentage'] }}%</span>
                    </div>
                </div>
            </div>
            @else
            <div class="flex items-center justify-center h-48">
                <p class="text-gray-500 text-center">No jury feedback yet</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Participant Feedback Details -->
    @if($participantFeedback->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-users text-blue-600 mr-3"></i>
            Participant Feedback ({{ $participantFeedback->count() }})
        </h2>
        
        <div class="space-y-4">
            @foreach($participantFeedback as $feedback)
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:border-blue-300 transition-colors duration-150">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $feedback->eventRegistration->user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $feedback->eventRegistration->user->email }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                Submitted {{ $feedback->submitted_at ? $feedback->submitted_at->format('M d, Y h:i A') : 'N/A' }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-star text-yellow-400"></i>
                            <span class="text-2xl font-bold text-gray-900">{{ $feedback->overall_rating }}</span>
                            <span class="text-gray-500">/5</span>
                        </div>
                    </div>

                    <!-- Rating Breakdown -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 pb-4 border-b border-gray-100">
                        <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">Content</div>
                            <div class="text-lg font-bold text-blue-600">{{ $feedback->content_rating ?? 'N/A' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">Organization</div>
                            <div class="text-lg font-bold text-blue-600">{{ $feedback->organization_rating ?? 'N/A' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">Platform</div>
                            <div class="text-lg font-bold text-blue-600">{{ $feedback->platform_rating ?? 'N/A' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">Venue</div>
                            <div class="text-lg font-bold text-blue-600">{{ $feedback->venue_rating ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <!-- Comments and Suggestions -->
                    <div class="space-y-3">
                        @if($feedback->comments)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-comment text-blue-500 mr-2"></i>
                                Comments
                            </h4>
                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $feedback->comments }}</p>
                        </div>
                        @endif

                        @if($feedback->suggestions)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                Suggestions
                            </h4>
                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $feedback->suggestions }}</p>
                        </div>
                        @endif

                        @if($feedback->system_feedback)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-cog text-purple-500 mr-2"></i>
                                System Feedback
                            </h4>
                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $feedback->system_feedback }}</p>
                        </div>
                        @endif

                        <div class="flex items-center pt-2">
                            @if($feedback->would_recommend)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-thumbs-up mr-1"></i>
                                    Would Recommend
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-thumbs-down mr-1"></i>
                                    Would Not Recommend
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Jury Feedback Details -->
    @if($juryFeedback->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-user-tie text-orange-600 mr-3"></i>
            Jury Feedback ({{ $juryFeedback->count() }})
        </h2>
        
        <div class="space-y-4">
            @foreach($juryFeedback as $feedback)
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:border-orange-300 transition-colors duration-150">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                {{ $feedback->eventRegistration->user->name }}
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-gavel mr-1"></i>
                                    Jury
                                </span>
                            </h3>
                            <p class="text-sm text-gray-500">{{ $feedback->eventRegistration->user->email }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                Submitted {{ $feedback->submitted_at ? $feedback->submitted_at->format('M d, Y h:i A') : 'N/A' }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-star text-yellow-400"></i>
                            <span class="text-2xl font-bold text-gray-900">{{ $feedback->overall_rating }}</span>
                            <span class="text-gray-500">/5</span>
                        </div>
                    </div>

                    <!-- Rating Breakdown -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 pb-4 border-b border-gray-100">
                        <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">Content</div>
                            <div class="text-lg font-bold text-orange-600">{{ $feedback->content_rating ?? 'N/A' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">Organization</div>
                            <div class="text-lg font-bold text-orange-600">{{ $feedback->organization_rating ?? 'N/A' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">Platform</div>
                            <div class="text-lg font-bold text-orange-600">{{ $feedback->platform_rating ?? 'N/A' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">Venue</div>
                            <div class="text-lg font-bold text-orange-600">{{ $feedback->venue_rating ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <!-- Comments and Suggestions -->
                    <div class="space-y-3">
                        @if($feedback->comments)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-comment text-orange-500 mr-2"></i>
                                Comments
                            </h4>
                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $feedback->comments }}</p>
                        </div>
                        @endif

                        @if($feedback->suggestions)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                Suggestions
                            </h4>
                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $feedback->suggestions }}</p>
                        </div>
                        @endif

                        @if($feedback->system_feedback)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-1 flex items-center">
                                <i class="fas fa-cog text-purple-500 mr-2"></i>
                                System Feedback
                            </h4>
                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $feedback->system_feedback }}</p>
                        </div>
                        @endif

                        <div class="flex items-center pt-2">
                            @if($feedback->would_recommend)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-thumbs-up mr-1"></i>
                                    Would Recommend
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-thumbs-down mr-1"></i>
                                    Would Not Recommend
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($participantFeedback->isEmpty() && $juryFeedback->isEmpty())
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Feedback Submitted Yet</h3>
        <p class="text-gray-500">Feedback will appear here once participants or jury members submit their responses.</p>
    </div>
    @endif
</div>
@endsection
