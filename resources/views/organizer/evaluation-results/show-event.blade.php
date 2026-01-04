@extends('organizer.layouts.app')

@section('title', ($eventType ?? null ? ucfirst($eventType) . ' ' : '') . 'Evaluation Results - ' . $event->title)
@section('page-title', ($eventType ?? null ? ucfirst($eventType) . ' ' : '') . 'Evaluation Results')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('organizer.evaluation-results.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-2 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Events
            </a>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                {{ $event->title }} - Results
            </h1>
            <p class="text-gray-600 mt-2">Rubric evaluation scores from jury members</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-3xl font-bold text-indigo-600">{{ $papers->count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Submissions</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-3xl font-bold text-purple-600">{{ $papers->where('evaluators')->count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Evaluated</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-3xl font-bold text-green-600">{{ $categories->count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Rubric Categories</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-3xl font-bold text-orange-600">
                    {{ number_format($papers->where('total_score', '>', 0)->avg('total_score'), 1) }}
                </div>
                <div class="text-sm text-gray-600 mt-1">Average Score</div>
            </div>
        </div>

        <!-- Papers Results Table -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Participant Scores</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paper Title
                            </th>
                            @foreach($categories as $category)
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $category->name }}
                                    <br>
                                    <span class="text-xs text-gray-400">({{ $category->max_score }} pts)</span>
                                </th>
                            @endforeach
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Score
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Evaluators
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($papers as $paper)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $paper->title ?? 'Untitled' }}</div>
                                    <div class="text-sm text-gray-600">{{ $paper->author_name ?? 'Unknown Author' }}</div>
                                    <div class="text-xs text-gray-400">ID: {{ $paper->id }}</div>
                                </td>
                                @foreach($categories as $category)
                                    @php
                                        $categoryScores = $paper->scores->get($category->id);
                                        $categoryTotal = $categoryScores ? $categoryScores->sum('avg_score') : 0;
                                        $percentage = ($categoryTotal / $category->max_score) * 100;
                                    @endphp
                                    <td class="px-6 py-4 text-center">
                                        @if($categoryScores)
                                            <div class="text-lg font-bold text-gray-900">
                                                {{ number_format($categoryTotal, 1) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ number_format($percentage, 0) }}%
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-6 py-4 text-center">
                                    @if($paper->total_score > 0)
                                        <div class="inline-flex items-center px-4 py-2 rounded-full text-lg font-bold
                                            @if(($paper->total_score / $paper->total_max_score) >= 0.8) bg-green-100 text-green-800
                                            @elseif(($paper->total_score / $paper->total_max_score) >= 0.6) bg-blue-100 text-blue-800
                                            @elseif(($paper->total_score / $paper->total_max_score) >= 0.4) bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ number_format($paper->total_score, 1) }} / {{ $paper->total_max_score }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Not evaluated</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($paper->evaluators && count($paper->evaluators) > 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                            {{ count($paper->evaluators) }} jury
                                        </span>
                                    @else
                                        <span class="text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($eventType === 'innovation')
                                        @php
                                            $hasAward = isset($participantAwards) && $participantAwards->has($paper->user_id) && $participantAwards[$paper->user_id]->count() > 0;
                                        @endphp
                                        @if($hasAward)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                <i class="fas fa-trophy mr-1"></i>
                                                Awarded
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                <i class="fas fa-clock mr-1"></i>
                                                Not Awarded
                                            </span>
                                        @endif
                                    @else
                                        @if($paper->presentation_status === 'selected')
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100" title="Approved for Presentation">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </span>
                                        @elseif($paper->presentation_status === 'rejected')
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100" title="Rejected">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100" title="Pending Decision">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('organizer.evaluation-results.paper-details', ['event' => $event->id, 'paperId' => $paper->id]) }}"
                                       class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $categories->count() + 6 }}" class="px-6 py-12 text-center text-gray-500">
                                    No submissions found for this event
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
