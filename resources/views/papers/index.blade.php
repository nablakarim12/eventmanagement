@extends('layouts.app')

@section('title', 'My Paper Submissions')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-900">My Paper Submissions</h1>
            <p class="text-gray-600 mt-2">View and manage all your submitted papers</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @if($papers->count() > 0)
        <div class="grid grid-cols-1 gap-6">
            @foreach($papers as $paper)
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $paper->title }}</h2>
                            <p class="text-gray-600 text-sm mb-2">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                {{ $paper->event->title }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold flex-shrink-0
                            @if($paper->status === 'submitted') bg-blue-100 text-blue-800
                            @elseif($paper->status === 'under_review') bg-yellow-100 text-yellow-800
                            @elseif($paper->status === 'accepted') bg-green-100 text-green-800
                            @elseif($paper->status === 'rejected') bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $paper->status)) }}
                        </span>
                    </div>

                    <p class="text-gray-700 text-sm mb-4 line-clamp-2">{{ $paper->abstract }}</p>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($paper->authors as $author)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                            <i class="fas fa-user mr-1"></i>{{ $author->name }}
                        </span>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 pb-4 border-b text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Submission Code</p>
                            <p class="font-semibold text-gray-900 font-mono">{{ $paper->submission_code }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Submitted On</p>
                            <p class="font-semibold text-gray-900">{{ $paper->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Reviews</p>
                            <p class="font-semibold text-gray-900">{{ $paper->reviews->count() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">File Size</p>
                            <p class="font-semibold text-gray-900">{{ number_format($paper->file_size / 1024 / 1024, 2) }} MB</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('papers.show', $paper) }}" class="flex-1 px-4 py-2 bg-purple-600 text-white text-center rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>
                        <a href="{{ route('papers.download', $paper) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Papers Submitted Yet</h3>
            <p class="text-gray-600 mb-6">You haven't submitted any papers for conference events.</p>
            <a href="{{ route('dashboard.registrations') }}" class="inline-block px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-list mr-2"></i>View My Event Registrations
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
