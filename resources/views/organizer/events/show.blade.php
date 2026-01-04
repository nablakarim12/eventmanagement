@extends('organizer.layouts.app')

@section('title', 'Event Details')
@section('page-title', 'Event Details')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            @if($event->featured_image)
            <div class="h-64 bg-cover bg-center" style="background-image: url('{{ str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . $event->featured_image) }}');"></div>
            @else
            <div class="h-64 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                <div class="text-center text-white">
                    <i class="fas fa-calendar-alt text-6xl mb-4"></i>
                    <h2 class="text-2xl font-bold">{{ $event->title }}</h2>
                </div>
            </div>
            @endif
            
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                        <p class="text-gray-600 mt-1">{{ $event->category->name ?? 'No Category' }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($event->status === 'published') bg-green-100 text-green-800
                            @elseif($event->status === 'draft') bg-yellow-100 text-yellow-800
                            @elseif($event->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Date & Time -->
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-calendar mr-3 text-blue-500"></i>
                        <div>
                            <p class="font-semibold">Date & Time</p>
                            @if($event->delivery_mode === 'hybrid')
                                <p class="text-xs font-semibold text-blue-600 mb-1">On-Site</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_start_date)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ $event->f2f_start_time }} - {{ $event->f2f_end_time }}</p>
                                <p class="text-xs font-semibold text-green-600 mt-2 mb-1">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_start_date)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ $event->online_start_time }} - {{ $event->online_end_time }}</p>
                            @elseif($event->delivery_mode === 'face_to_face')
                                <p class="text-xs font-semibold text-blue-600">On-Site Event</p>
                                <p>{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->start_date)->format('h:i A') }} - 
                                   {{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('h:i A') : 'TBD' }}</p>
                            @elseif($event->delivery_mode === 'online')
                                <p class="text-xs font-semibold text-green-600">Online Event</p>
                                <p>{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->start_date)->format('h:i A') }} - 
                                   {{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('h:i A') : 'TBD' }}</p>
                            @else
                                <p>{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->start_date)->format('h:i A') }} - 
                                   {{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('h:i A') : 'TBD' }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Registration Deadline (only for non-innovation events) -->
                    @if(!$event->delivery_mode)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-clock mr-3 text-orange-500"></i>
                        <div>
                            <p class="font-semibold">Registration Deadline</p>
                            @if($event->registration_deadline)
                                <p>{{ \Carbon\Carbon::parse($event->registration_deadline)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->registration_deadline)->format('h:i A') }}</p>
                            @else
                                <p class="text-sm text-gray-500">Not set</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Participant Registration Deadline (for innovation events) -->
                    @if($event->delivery_mode && ($event->f2f_registration_deadline || $event->online_registration_deadline))
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-users mr-3 text-blue-500"></i>
                        <div>
                            <p class="font-semibold">Participant Registration Deadline</p>
                            @if($event->delivery_mode === 'hybrid')
                                @if($event->f2f_registration_deadline)
                                    <p class="text-xs font-semibold text-blue-600">On-Site</p>
                                    <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_registration_deadline)->format('M d, Y') }}</p>
                                    <p class="text-xs mb-2">{{ \Carbon\Carbon::parse($event->f2f_registration_deadline)->format('h:i A') }}</p>
                                @endif
                                @if($event->online_registration_deadline)
                                    <p class="text-xs font-semibold text-green-600">Online</p>
                                    <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_registration_deadline)->format('M d, Y') }}</p>
                                    <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_registration_deadline)->format('h:i A') }}</p>
                                @endif
                            @elseif($event->delivery_mode === 'face_to_face' && $event->f2f_registration_deadline)
                                <p class="text-xs font-semibold text-blue-600">On-Site</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_registration_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->f2f_registration_deadline)->format('h:i A') }}</p>
                            @elseif($event->delivery_mode === 'online' && $event->online_registration_deadline)
                                <p class="text-xs font-semibold text-green-600">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_registration_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_registration_deadline)->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Jury Registration Deadline (for innovation events) -->
                    @if($event->delivery_mode === 'hybrid' && ($event->f2f_jury_registration_deadline || $event->online_jury_registration_deadline))
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-user-tie mr-3 text-orange-500"></i>
                        <div>
                            <p class="font-semibold">Jury Registration Deadline</p>
                            @if($event->f2f_jury_registration_deadline)
                                <p class="text-xs font-semibold text-blue-600">On-Site</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_jury_registration_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs mb-2">{{ \Carbon\Carbon::parse($event->f2f_jury_registration_deadline)->format('h:i A') }}</p>
                            @endif
                            @if($event->online_jury_registration_deadline)
                                <p class="text-xs font-semibold text-green-600">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_jury_registration_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_jury_registration_deadline)->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                        @php
                            $juryDeadline = null;
                            $juryLabel = '';
                            if ($event->delivery_mode === 'face_to_face') {
                                $juryDeadline = $event->f2f_jury_registration_deadline;
                                $juryLabel = 'On-Site';
                            } elseif ($event->delivery_mode === 'online') {
                                $juryDeadline = $event->online_jury_registration_deadline;
                                $juryLabel = 'Online';
                            }
                        @endphp
                        @if($juryDeadline)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-user-tie mr-3 text-orange-500"></i>
                            <div>
                                <p class="font-semibold">Jury Registration Deadline</p>
                                <p class="text-xs font-semibold {{ $event->delivery_mode === 'face_to_face' ? 'text-blue-600' : 'text-green-600' }}">{{ $juryLabel }}</p>
                                <p>{{ \Carbon\Carbon::parse($juryDeadline)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($juryDeadline)->format('h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- Price -->
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-tag mr-3 text-green-500"></i>
                        <div>
                            <p class="font-semibold">Price</p>
                            <p class="text-lg font-bold">{{ $event->registration_fee > 0 ? 'RM ' . number_format($event->registration_fee, 2) : 'Free' }}</p>
                        </div>
                    </div>
                    
                    <!-- Submission Deadline (for innovation events) -->
                    @if($event->delivery_mode === 'hybrid' && ($event->f2f_submission_deadline || $event->online_submission_deadline))
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-file-upload mr-3 text-purple-500"></i>
                        <div>
                            <p class="font-semibold">Submission Deadline</p>
                            @if($event->f2f_submission_deadline)
                                <p class="text-xs font-semibold text-blue-600">On-Site</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_submission_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs mb-2">{{ \Carbon\Carbon::parse($event->f2f_submission_deadline)->format('h:i A') }}</p>
                            @endif
                            @if($event->online_submission_deadline)
                                <p class="text-xs font-semibold text-green-600">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_submission_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_submission_deadline)->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                        @php
                            $submissionDeadline = null;
                            $submissionLabel = '';
                            if ($event->delivery_mode === 'face_to_face') {
                                $submissionDeadline = $event->f2f_submission_deadline;
                                $submissionLabel = 'On-Site';
                            } elseif ($event->delivery_mode === 'online') {
                                $submissionDeadline = $event->online_submission_deadline;
                                $submissionLabel = 'Online';
                            }
                        @endphp
                        @if($submissionDeadline)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-file-upload mr-3 text-purple-500"></i>
                            <div>
                                <p class="font-semibold">Submission Deadline</p>
                                <p class="text-xs font-semibold {{ $event->delivery_mode === 'face_to_face' ? 'text-blue-600' : 'text-green-600' }}">{{ $submissionLabel }}</p>
                                <p>{{ \Carbon\Carbon::parse($submissionDeadline)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($submissionDeadline)->format('h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- Acceptance Notification Date (for innovation events) -->
                    @if($event->delivery_mode === 'hybrid' && ($event->f2f_acceptance_notification_date || $event->online_acceptance_notification_date_new))
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-check-circle mr-3 text-teal-500"></i>
                        <div>
                            <p class="font-semibold">Acceptance Notification</p>
                            @if($event->f2f_acceptance_notification_date)
                                <p class="text-xs font-semibold text-blue-600">On-Site</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_acceptance_notification_date)->format('M d, Y') }}</p>
                                <p class="text-xs mb-2">{{ \Carbon\Carbon::parse($event->f2f_acceptance_notification_date)->format('h:i A') }}</p>
                            @endif
                            @if($event->online_acceptance_notification_date_new)
                                <p class="text-xs font-semibold text-green-600">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_acceptance_notification_date_new)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_acceptance_notification_date_new)->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                        @php
                            $acceptanceDate = null;
                            $acceptanceLabel = '';
                            if ($event->delivery_mode === 'face_to_face') {
                                $acceptanceDate = $event->f2f_acceptance_notification_date;
                                $acceptanceLabel = 'On-Site';
                            } elseif ($event->delivery_mode === 'online') {
                                $acceptanceDate = $event->online_acceptance_notification_date_new;
                                $acceptanceLabel = 'Online';
                            }
                        @endphp
                        @if($acceptanceDate)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle mr-3 text-teal-500"></i>
                            <div>
                                <p class="font-semibold">Acceptance Notification</p>
                                <p class="text-xs font-semibold {{ $event->delivery_mode === 'face_to_face' ? 'text-blue-600' : 'text-green-600' }}">{{ $acceptanceLabel }}</p>
                                <p>{{ \Carbon\Carbon::parse($acceptanceDate)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($acceptanceDate)->format('h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- Payment Deadline -->
                    @if($event->delivery_mode === 'hybrid' && ($event->f2f_payment_deadline_new || $event->online_payment_deadline_new))
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-money-bill-wave mr-3 text-emerald-500"></i>
                        <div>
                            <p class="font-semibold">Payment Deadline</p>
                            @if($event->f2f_payment_deadline_new)
                                <p class="text-xs font-semibold text-blue-600">On-Site</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_payment_deadline_new)->format('M d, Y') }}</p>
                                <p class="text-xs mb-2">{{ \Carbon\Carbon::parse($event->f2f_payment_deadline_new)->format('h:i A') }}</p>
                            @endif
                            @if($event->online_payment_deadline_new)
                                <p class="text-xs font-semibold text-green-600">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_payment_deadline_new)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_payment_deadline_new)->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                        @php
                            $paymentDeadline = null;
                            $paymentLabel = '';
                            if ($event->delivery_mode === 'face_to_face') {
                                $paymentDeadline = $event->f2f_payment_deadline_new ?? $event->f2f_payment_deadline;
                                $paymentLabel = 'On-Site';
                            } elseif ($event->delivery_mode === 'online') {
                                $paymentDeadline = $event->online_payment_deadline_new ?? $event->online_payment_deadline;
                                $paymentLabel = 'Online';
                            } elseif ($event->payment_deadline && $event->registration_fee > 0) {
                                $paymentDeadline = $event->payment_deadline;
                                $paymentLabel = '';
                            }
                        @endphp
                        @if($paymentDeadline)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-money-bill-wave mr-3 text-emerald-500"></i>
                            <div>
                                <p class="font-semibold">Payment Deadline</p>
                                @if($paymentLabel)
                                    <p class="text-xs font-semibold {{ $event->delivery_mode === 'face_to_face' ? 'text-blue-600' : 'text-green-600' }}">{{ $paymentLabel }}</p>
                                @endif
                                <p class="{{ $paymentLabel ? 'text-sm' : '' }}">{{ \Carbon\Carbon::parse($paymentDeadline)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($paymentDeadline)->format('h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- Reviewer Registration Deadline (for conference events) -->
                    @if($event->delivery_mode === 'hybrid' && ($event->f2f_reviewer_registration_deadline || $event->online_reviewer_registration_deadline))
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-user-graduate mr-3 text-indigo-500"></i>
                        <div>
                            <p class="font-semibold">Reviewer Registration</p>
                            @if($event->f2f_reviewer_registration_deadline)
                                <p class="text-xs font-semibold text-blue-600">Face to Face</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_reviewer_registration_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs mb-2">{{ \Carbon\Carbon::parse($event->f2f_reviewer_registration_deadline)->format('h:i A') }}</p>
                            @endif
                            @if($event->online_reviewer_registration_deadline)
                                <p class="text-xs font-semibold text-green-600">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_reviewer_registration_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_reviewer_registration_deadline)->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                        @php
                            $reviewerDeadline = null;
                            $reviewerLabel = '';
                            if ($event->delivery_mode === 'face_to_face') {
                                $reviewerDeadline = $event->f2f_reviewer_registration_deadline;
                                $reviewerLabel = 'Face to Face';
                            } elseif ($event->delivery_mode === 'online') {
                                $reviewerDeadline = $event->online_reviewer_registration_deadline;
                                $reviewerLabel = 'Online';
                            }
                        @endphp
                        @if($reviewerDeadline)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-user-graduate mr-3 text-indigo-500"></i>
                            <div>
                                <p class="font-semibold">Reviewer Registration</p>
                                <p class="text-xs font-semibold {{ $event->delivery_mode === 'face_to_face' ? 'text-blue-600' : 'text-green-600' }}">{{ $reviewerLabel }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($reviewerDeadline)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($reviewerDeadline)->format('h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- Paper Submission Deadline (for conference events) -->
                    @if($event->delivery_mode === 'hybrid' && ($event->f2f_paper_submission_deadline || $event->online_paper_submission_deadline))
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-file-upload mr-3 text-purple-500"></i>
                        <div>
                            <p class="font-semibold">Paper Submission Deadline</p>
                            @if($event->f2f_paper_submission_deadline)
                                <p class="text-xs font-semibold text-blue-600">Face to Face</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs mb-2">{{ \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format('h:i A') }}</p>
                            @endif
                            @if($event->online_paper_submission_deadline)
                                <p class="text-xs font-semibold text-green-600">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                        @php
                            $paperSubmissionDeadline = null;
                            $paperSubmissionLabel = '';
                            if ($event->delivery_mode === 'face_to_face') {
                                $paperSubmissionDeadline = $event->f2f_paper_submission_deadline;
                                $paperSubmissionLabel = 'Face to Face';
                            } elseif ($event->delivery_mode === 'online') {
                                $paperSubmissionDeadline = $event->online_paper_submission_deadline;
                                $paperSubmissionLabel = 'Online';
                            }
                        @endphp
                        @if($paperSubmissionDeadline)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-file-upload mr-3 text-purple-500"></i>
                            <div>
                                <p class="font-semibold">Paper Submission Deadline</p>
                                <p class="text-xs font-semibold {{ $event->delivery_mode === 'face_to_face' ? 'text-blue-600' : 'text-green-600' }}">{{ $paperSubmissionLabel }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($paperSubmissionDeadline)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($paperSubmissionDeadline)->format('h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- Review Deadline (for conference events) -->
                    @if($event->delivery_mode === 'hybrid' && ($event->f2f_review_deadline || $event->online_review_deadline))
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-file-signature mr-3 text-pink-500"></i>
                        <div>
                            <p class="font-semibold">Review Deadline</p>
                            @if($event->f2f_review_deadline)
                                <p class="text-xs font-semibold text-blue-600">Face to Face</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_review_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs mb-2">{{ \Carbon\Carbon::parse($event->f2f_review_deadline)->format('h:i A') }}</p>
                            @endif
                            @if($event->online_review_deadline)
                                <p class="text-xs font-semibold text-green-600">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_review_deadline)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_review_deadline)->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                        @php
                            $reviewDeadline = null;
                            $reviewLabel = '';
                            if ($event->delivery_mode === 'face_to_face') {
                                $reviewDeadline = $event->f2f_review_deadline;
                                $reviewLabel = 'Face to Face';
                            } elseif ($event->delivery_mode === 'online') {
                                $reviewDeadline = $event->online_review_deadline;
                                $reviewLabel = 'Online';
                            }
                        @endphp
                        @if($reviewDeadline)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-file-signature mr-3 text-pink-500"></i>
                            <div>
                                <p class="font-semibold">Review Deadline</p>
                                <p class="text-xs font-semibold {{ $event->delivery_mode === 'face_to_face' ? 'text-blue-600' : 'text-green-600' }}">{{ $reviewLabel }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($reviewDeadline)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($reviewDeadline)->format('h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- Acceptance Notification Date (for conference events) -->
                    @if($event->delivery_mode === 'hybrid' && ($event->f2f_acceptance_notification_date || $event->online_acceptance_notification_date))
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-check-circle mr-3 text-teal-500"></i>
                        <div>
                            <p class="font-semibold">Acceptance Notification Date</p>
                            @if($event->f2f_acceptance_notification_date)
                                <p class="text-xs font-semibold text-blue-600">Face to Face</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->f2f_acceptance_notification_date)->format('M d, Y') }}</p>
                                <p class="text-xs mb-2">{{ \Carbon\Carbon::parse($event->f2f_acceptance_notification_date)->format('h:i A') }}</p>
                            @endif
                            @if($event->online_acceptance_notification_date)
                                <p class="text-xs font-semibold text-green-600">Online</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($event->online_acceptance_notification_date)->format('M d, Y') }}</p>
                                <p class="text-xs">{{ \Carbon\Carbon::parse($event->online_acceptance_notification_date)->format('h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                        @php
                            $acceptanceNotificationDate = null;
                            $acceptanceNotificationLabel = '';
                            if ($event->delivery_mode === 'face_to_face') {
                                $acceptanceNotificationDate = $event->f2f_acceptance_notification_date;
                                $acceptanceNotificationLabel = 'Face to Face';
                            } elseif ($event->delivery_mode === 'online') {
                                $acceptanceNotificationDate = $event->online_acceptance_notification_date;
                                $acceptanceNotificationLabel = 'Online';
                            }
                        @endphp
                        @if($acceptanceNotificationDate)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle mr-3 text-teal-500"></i>
                            <div>
                                <p class="font-semibold">Acceptance Notification Date</p>
                                <p class="text-xs font-semibold {{ $event->delivery_mode === 'face_to_face' ? 'text-blue-600' : 'text-green-600' }}">{{ $acceptanceNotificationLabel }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($acceptanceNotificationDate)->format('M d, Y') }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($acceptanceNotificationDate)->format('h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    @endif

                    <!-- Innovation Categories (for innovation events) -->
                    @if($event->innovation_categories && count($event->innovation_categories) > 0)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-lightbulb mr-3 text-yellow-500"></i>
                        <div>
                            <p class="font-semibold">Innovation Categories</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($event->innovation_categories as $category)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $category }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Innovation Themes (for innovation events) -->
                    @if($event->innovation_theme && count($event->innovation_theme) > 0)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-project-diagram mr-3 text-indigo-500"></i>
                        <div>
                            <p class="font-semibold">Innovation Themes</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($event->innovation_theme as $theme)
                                    <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">{{ $theme }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Conference Categories (for conference events) -->
                    @if($event->conference_categories && count($event->conference_categories) > 0)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-tags mr-3 text-purple-500"></i>
                        <div>
                            <p class="font-semibold">Conference Categories</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($event->conference_categories as $category)
                                    <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">{{ $category }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Location -->
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-map-marker-alt mr-3 text-red-500"></i>
                        <div>
                            <p class="font-semibold">Location</p>
                            <p>{{ $event->venue_name }}</p>
                            @if($event->venue_address)
                                <p class="text-sm text-gray-500">{{ $event->venue_address }}</p>
                            @endif
                            @if($event->city || $event->country)
                                <p class="text-sm text-gray-500">{{ $event->city }}@if($event->city && $event->country), @endif{{ $event->country }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Innovation Event Form Fields -->
                    @if(!$event->delivery_mode)
                    
                    <!-- Jury Registration Deadline -->
                    @if($event->jury_registration_deadline)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-user-tie mr-3 text-amber-500"></i>
                        <div>
                            <p class="font-semibold">Jury Registration Deadline</p>
                            <p>{{ \Carbon\Carbon::parse($event->jury_registration_deadline)->format('M d, Y') }}</p>
                            <p class="text-sm">{{ \Carbon\Carbon::parse($event->jury_registration_deadline)->format('h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Submission Deadline -->
                    @if($event->submission_deadline)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-upload mr-3 text-purple-500"></i>
                        <div>
                            <p class="font-semibold">Submission Deadline</p>
                            <p>{{ \Carbon\Carbon::parse($event->submission_deadline)->format('M d, Y') }}</p>
                            <p class="text-sm">{{ \Carbon\Carbon::parse($event->submission_deadline)->format('h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Acceptance Notification Date -->
                    @if($event->acceptance_notification_date)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-bell mr-3 text-teal-500"></i>
                        <div>
                            <p class="font-semibold">Acceptance Notification</p>
                            <p>{{ \Carbon\Carbon::parse($event->acceptance_notification_date)->format('M d, Y') }}</p>
                            <p class="text-sm">{{ \Carbon\Carbon::parse($event->acceptance_notification_date)->format('h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Extended Registration Deadline -->
                    @if($event->extended_registration_deadline)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-clock mr-3 text-blue-500"></i>
                        <div>
                            <p class="font-semibold">Extended Registration Deadline</p>
                            <p>{{ \Carbon\Carbon::parse($event->extended_registration_deadline)->format('M d, Y') }}</p>
                            <p class="text-sm">{{ \Carbon\Carbon::parse($event->extended_registration_deadline)->format('h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Extended Jury Deadline -->
                    @if($event->extended_jury_deadline)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-user-clock mr-3 text-amber-500"></i>
                        <div>
                            <p class="font-semibold">Extended Jury Deadline</p>
                            <p>{{ \Carbon\Carbon::parse($event->extended_jury_deadline)->format('M d, Y') }}</p>
                            <p class="text-sm">{{ \Carbon\Carbon::parse($event->extended_jury_deadline)->format('h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Extended Submission Deadline -->
                    @if($event->extended_submission_deadline)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-file-upload mr-3 text-purple-500"></i>
                        <div>
                            <p class="font-semibold">Extended Submission Deadline</p>
                            <p>{{ \Carbon\Carbon::parse($event->extended_submission_deadline)->format('M d, Y') }}</p>
                            <p class="text-sm">{{ \Carbon\Carbon::parse($event->extended_submission_deadline)->format('h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Extended Notification Date -->
                    @if($event->extended_notification_date)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-bell-slash mr-3 text-teal-500"></i>
                        <div>
                            <p class="font-semibold">Extended Notification Date</p>
                            <p>{{ \Carbon\Carbon::parse($event->extended_notification_date)->format('M d, Y') }}</p>
                            <p class="text-sm">{{ \Carbon\Carbon::parse($event->extended_notification_date)->format('h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Venue Details -->
                    @if($event->state)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-map-pin mr-3 text-green-500"></i>
                        <div>
                            <p class="font-semibold">State</p>
                            <p>{{ $event->state }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Contact Information -->
                    @if($event->contact_email)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-envelope mr-3 text-blue-500"></i>
                        <div>
                            <p class="font-semibold">Contact Email</p>
                            <p class="text-sm">{{ $event->contact_email }}</p>
                        </div>
                    </div>
                    @endif

                    @if($event->contact_phone)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone mr-3 text-green-500"></i>
                        <div>
                            <p class="font-semibold">Contact Phone</p>
                            <p class="text-sm">{{ $event->contact_phone }}</p>
                        </div>
                    </div>
                    @endif

                    @if($event->website_url)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-globe mr-3 text-indigo-500"></i>
                        <div>
                            <p class="font-semibold">Website</p>
                            <a href="{{ $event->website_url }}" target="_blank" class="text-sm text-blue-600 hover:underline">{{ $event->website_url }}</a>
                        </div>
                    </div>
                    @endif

                    <!-- Event Settings -->
                    @if($event->max_participants)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-users mr-3 text-orange-500"></i>
                        <div>
                            <p class="font-semibold">Max Participants</p>
                            <p>{{ $event->max_participants }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-check-circle mr-3 text-green-500"></i>
                        <div>
                            <p class="font-semibold">Event Settings</p>
                            <p class="text-sm">
                                @if($event->is_public)
                                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1">Public</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded mr-1">Private</span>
                                @endif
                                @if($event->requires_approval)
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded mr-1">Approval Required</span>
                                @endif
                                @if($event->allow_waitlist)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">Waitlist Enabled</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @endif
                </div>
            </div>
        </div>

        <!-- Event Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>Event Description
                    </h3>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $event->description }}</p>
                    </div>
                </div>

                <!-- Extended Deadlines (if any) -->
                @if(($event->delivery_mode === 'face_to_face' || $event->delivery_mode === 'hybrid') && 
                    ($event->f2f_extended_registration_deadline || $event->f2f_extended_jury_deadline || 
                     $event->f2f_extended_submission_deadline || $event->f2f_extended_notification_date))
                <div class="bg-yellow-50 rounded-lg shadow border border-yellow-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-clock mr-2 text-yellow-600"></i>Extended Deadlines (On-Site)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($event->f2f_extended_registration_deadline)
                        <div>
                            <p class="text-sm font-medium text-gray-700">Extended Registration</p>
                            <p class="text-gray-600">{{ \Carbon\Carbon::parse($event->f2f_extended_registration_deadline)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        @if($event->f2f_extended_jury_deadline)
                        <div>
                            <p class="text-sm font-medium text-gray-700">Extended Jury Registration</p>
                            <p class="text-gray-600">{{ \Carbon\Carbon::parse($event->f2f_extended_jury_deadline)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        @if($event->f2f_extended_submission_deadline)
                        <div>
                            <p class="text-sm font-medium text-gray-700">Extended Submission</p>
                            <p class="text-gray-600">{{ \Carbon\Carbon::parse($event->f2f_extended_submission_deadline)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        @if($event->f2f_extended_notification_date)
                        <div>
                            <p class="text-sm font-medium text-gray-700">Extended Notification</p>
                            <p class="text-gray-600">{{ \Carbon\Carbon::parse($event->f2f_extended_notification_date)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if(($event->delivery_mode === 'online' || $event->delivery_mode === 'hybrid') && 
                    ($event->online_extended_registration_deadline || $event->online_extended_jury_deadline || 
                     $event->online_extended_submission_deadline || $event->online_extended_notification_date))
                <div class="bg-green-50 rounded-lg shadow border border-green-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-clock mr-2 text-green-600"></i>Extended Deadlines (Online)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($event->online_extended_registration_deadline)
                        <div>
                            <p class="text-sm font-medium text-gray-700">Extended Registration</p>
                            <p class="text-gray-600">{{ \Carbon\Carbon::parse($event->online_extended_registration_deadline)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        @if($event->online_extended_jury_deadline)
                        <div>
                            <p class="text-sm font-medium text-gray-700">Extended Jury Registration</p>
                            <p class="text-gray-600">{{ \Carbon\Carbon::parse($event->online_extended_jury_deadline)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        @if($event->online_extended_submission_deadline)
                        <div>
                            <p class="text-sm font-medium text-gray-700">Extended Submission</p>
                            <p class="text-gray-600">{{ \Carbon\Carbon::parse($event->online_extended_submission_deadline)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        @if($event->online_extended_notification_date)
                        <div>
                            <p class="text-sm font-medium text-gray-700">Extended Notification</p>
                            <p class="text-gray-600">{{ \Carbon\Carbon::parse($event->online_extended_notification_date)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Registration Statistics -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-users mr-2 text-green-500"></i>Registration Statistics
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">{{ $event->registrations->count() }}</p>
                            <p class="text-sm text-gray-500">Total Registered</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-green-600">{{ $event->registrations->where('status', 'confirmed')->count() }}</p>
                            <p class="text-sm text-gray-500">Confirmed</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-yellow-600">{{ $event->registrations->where('status', 'pending')->count() }}</p>
                            <p class="text-sm text-gray-500">Pending</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-gray-600">{{ $event->max_participants ? max(0, $event->max_participants - $event->registrations->count()) : '∞' }}</p>
                            <p class="text-sm text-gray-500">Remaining</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-bolt mr-2 text-purple-500"></i>Quick Actions
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('organizer.registrations.index', ['event' => $event->id]) }}" 
                           class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors flex items-center">
                            <i class="fas fa-users mr-2"></i>View Registrations
                        </a>
                        <a href="{{ route('organizer.attendance.event', $event) }}" 
                           class="bg-green-100 text-green-700 px-4 py-2 rounded-lg hover:bg-green-200 transition-colors flex items-center">
                            <i class="fas fa-clipboard-check mr-2"></i>Attendance
                        </a>
                        
                        <a href="{{ route('organizer.events.jury-assignments.index', $event) }}" 
                           class="bg-purple-100 text-purple-700 px-4 py-2 rounded-lg hover:bg-purple-200 transition-colors flex items-center">
                            <i class="fas fa-user-tie mr-2"></i>Jury Assignments
                        </a>
                        
                        <a href="{{ route('organizer.events.rubrics.index', $event) }}" 
                           class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg hover:bg-indigo-200 transition-colors flex items-center">
                            <i class="fas fa-clipboard-list mr-2"></i>Evaluation Rubric
                        </a>
                        
                        <!-- Award Setup (Innovation Events Only) -->
                        @if($event->category && $event->category->name === 'Innovation Competition')
                        <a href="{{ route('organizer.awards.setup', $event) }}" 
                           class="bg-amber-100 text-amber-700 px-4 py-2 rounded-lg hover:bg-amber-200 transition-colors flex items-center">
                            <i class="fas fa-trophy mr-2"></i>Award Setup
                        </a>
                        @endif
                        
                        <!-- Payment Setup (Paid Events Only) -->
                        @if($event->registration_fee && $event->registration_fee > 0)
                        <a href="{{ route('organizer.payment.setup', $event) }}" 
                           class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-lg hover:bg-emerald-200 transition-colors flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>Payment Setup
                        </a>
                        @endif
                        
                        @if($event->registrations->count() > 0)
                        <a href="{{ route('organizer.certificates.event', $event) }}" 
                           class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg hover:bg-yellow-200 transition-colors flex items-center">
                            <i class="fas fa-certificate mr-2"></i>Certificates
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Event Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Information</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Maximum Participants</p>
                            <p class="text-lg">{{ $event->max_participants ?? 'Unlimited' }}</p>
                        </div>
                        
                        @if($event->registration_start)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Registration Opens</p>
                            <p class="text-lg">{{ \Carbon\Carbon::parse($event->registration_start)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        
                        @if($event->registration_end)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Registration Closes</p>
                            <p class="text-lg">{{ \Carbon\Carbon::parse($event->registration_end)->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Created</p>
                            <p class="text-lg">{{ $event->created_at->format('M d, Y') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Last Updated</p>
                            <p class="text-lg">{{ $event->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('organizer.events.edit', $event) }}" 
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>Edit Event
                        </a>
                        
                        <a href="{{ route('organizer.events.duplicate', $event) }}" 
                           class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-copy mr-2"></i>Duplicate Event
                        </a>
                        
                        <form action="{{ route('organizer.events.destroy', $event) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>Delete Event
                            </button>
                        </form>
                        
                        <a href="{{ route('organizer.events.index') }}" 
                           class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection