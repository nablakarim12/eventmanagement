@extends('layouts.app')

@section('title', 'Paper Details - ' . $paper->title)

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('papers.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to My Papers
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $paper->title }}</h1>
            <p class="text-gray-600 mt-2">{{ $paper->event->title }}</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Paper Info Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-purple-500">
                        <i class="fas fa-file-alt mr-2"></i>Paper Information
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 uppercase mb-1">Abstract</h3>
                            <p class="text-gray-800 leading-relaxed">{{ $paper->abstract }}</p>
                        </div>

                        @if($paper->keywords)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Keywords</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $paper->keywords) as $keyword)
                                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                        {{ trim($keyword) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 uppercase mb-1">Submission Code</h3>
                                <p class="text-gray-800 font-mono">{{ $paper->submission_code }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 uppercase mb-1">Submitted On</h3>
                                <p class="text-gray-800">{{ $paper->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 uppercase mb-1">Status</h3>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold
                                    @if($paper->status === 'submitted') bg-blue-100 text-blue-800
                                    @elseif($paper->status === 'under_review') bg-yellow-100 text-yellow-800
                                    @elseif($paper->status === 'accepted') bg-green-100 text-green-800
                                    @elseif($paper->status === 'rejected') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 uppercase mb-1">File Size</h3>
                                <p class="text-gray-800">{{ number_format($paper->file_size / 1024 / 1024, 2) }} MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Authors Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-purple-500">
                        <i class="fas fa-users mr-2"></i>Authors
                    </h2>

                    <div class="space-y-4">
                        @foreach($paper->authors as $author)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">
                                    {{ $author->name }}
                                    @if($author->is_corresponding)
                                        <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Corresponding</span>
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-600">{{ $author->email }}</p>
                                @if($author->affiliation)
                                    <p class="text-sm text-gray-600">{{ $author->affiliation }}</p>
                                @endif
                                @if($author->country)
                                    <p class="text-sm text-gray-500">{{ $author->country }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reviews Card (if any) -->
                @if($paper->reviews->count() > 0)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-purple-500">
                        <i class="fas fa-comment-dots mr-2"></i>Reviews
                    </h2>

                    <div class="space-y-4">
                        @foreach($paper->reviews as $review)
                        <div class="p-4 bg-gray-50 rounded-lg border-l-4 
                            @if($review->recommendation === 'accept') border-green-500
                            @elseif($review->recommendation === 'minor_revision') border-yellow-500
                            @elseif($review->recommendation === 'major_revision') border-orange-500
                            @else border-red-500
                            @endif">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-gray-900">
                                    Reviewer: {{ $review->juryRegistration->user->name }}
                                </h3>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($review->recommendation === 'accept') bg-green-100 text-green-800
                                    @elseif($review->recommendation === 'minor_revision') bg-yellow-100 text-yellow-800
                                    @elseif($review->recommendation === 'major_revision') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $review->recommendation)) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">{{ $review->created_at->format('M d, Y') }}</p>
                            
                            @if($review->comments)
                            <div class="mt-3 pt-3 border-t">
                                <h4 class="text-sm font-semibold text-gray-700 mb-1">Comments:</h4>
                                <p class="text-gray-700 text-sm">{{ $review->comments }}</p>
                            </div>
                            @endif

                            @if($review->scores)
                            <div class="mt-3 pt-3 border-t">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Scores:</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach(json_decode($review->scores, true) as $criterion => $score)
                                    <div class="text-sm">
                                        <span class="text-gray-600">{{ ucfirst($criterion) }}:</span>
                                        <span class="font-semibold text-gray-900">{{ $score }}/10</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Download Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-download mr-2"></i>Download Paper
                    </h3>
                    <a href="{{ route('papers.download', $paper) }}" class="block w-full px-4 py-3 bg-purple-600 text-white text-center rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>Download PDF
                    </a>
                    <p class="text-xs text-gray-500 mt-2">{{ $paper->paper_file_name }}</p>
                </div>

                <!-- Event Details Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-calendar-alt mr-2"></i>Event Details
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600 font-semibold">Event</p>
                            <p class="text-gray-900">{{ $paper->event->title }}</p>
                        </div>
                        @if($paper->event->f2f_start_date)
                        <div>
                            <p class="text-gray-600 font-semibold">Event Date</p>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($paper->event->f2f_start_date)->format('M d, Y') }}</p>
                        </div>
                        @endif
                        <div>
                            <a href="{{ route('events.show', $paper->event->slug) }}" class="text-purple-600 hover:text-purple-800">
                                View Event Details →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Review Status Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-chart-line mr-2"></i>Review Status
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Reviews Received</span>
                            <span class="font-bold text-gray-900">{{ $paper->reviews->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Submission Status</span>
                            <span class="font-semibold
                                @if($paper->status === 'accepted') text-green-600
                                @elseif($paper->status === 'rejected') text-red-600
                                @else text-yellow-600
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
