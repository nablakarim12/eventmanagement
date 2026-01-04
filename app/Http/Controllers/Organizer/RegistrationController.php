<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EventRegistrationStatusUpdate;

class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * Display all registrations for organizer's events - GROUPED BY EVENT
     */
    public function index(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get event type filter from request
        $eventType = $request->query('type'); // 'innovation' or 'conference'
        
        // Get events with registrations count (grouped view)
        $query = Event::where('organizer_id', $organizer->id);
        
        // Filter by event type
        if ($eventType === 'innovation') {
            $query->whereHas('category', function($q) {
                $q->where('name', 'Innovation Competition');
            });
        } elseif ($eventType === 'conference') {
            $query->whereHas('category', function($q) {
                $q->where('name', '!=', 'Innovation Competition');
            });
        }
        
        $eventsWithRegistrations = $query->withCount([
                'registrations as total_registrations',
                'registrations as pending_registrations' => function($q) {
                    $q->where('status', 'pending');
                },
                'registrations as confirmed_registrations' => function($q) {
                    $q->where('status', 'confirmed');
                },
                'registrations as participants_count' => function($q) {
                    $q->where('role', 'participant');
                },
                'registrations as reviewers_count' => function($q) {
                    $q->where('role', 'reviewer');
                }
            ])
            ->has('registrations')
            ->orderByDesc('pending_registrations')
            ->orderByDesc('created_at')
            ->get();

        // Get registration statistics
        $stats = [
            'total' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id);
                if ($eventType === 'innovation') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', 'Innovation Competition');
                    });
                } elseif ($eventType === 'conference') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', '!=', 'Innovation Competition');
                    });
                }
            })->count(),
            'confirmed' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id);
                if ($eventType === 'innovation') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', 'Innovation Competition');
                    });
                } elseif ($eventType === 'conference') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', '!=', 'Innovation Competition');
                    });
                }
            })->where('status', 'confirmed')->count(),
            'pending' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id);
                if ($eventType === 'innovation') {
                    $q->whereNull('delivery_mode');
                } elseif ($eventType === 'conference') {
                    $q->whereNotNull('delivery_mode');
                }
            })->where('status', 'pending')->count(),
            'cancelled' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id);
                if ($eventType === 'innovation') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', 'Innovation Competition');
                    });
                } elseif ($eventType === 'conference') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', '!=', 'Innovation Competition');
                    });
                }
            })->where('status', 'cancelled')->count(),
            // Role-based statistics
            'participants' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id);
                if ($eventType === 'innovation') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', 'Innovation Competition');
                    });
                } elseif ($eventType === 'conference') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', '!=', 'Innovation Competition');
                    });
                }
            })->where('role', 'participant')->count(),
            'reviewers' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id);
                if ($eventType === 'innovation') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', 'Innovation Competition');
                    });
                } elseif ($eventType === 'conference') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', '!=', 'Innovation Competition');
                    });
                }
            })->where('role', 'reviewer')->count(),
        ];

        return view('organizer.registrations.index', compact('eventsWithRegistrations', 'stats', 'eventType'));
    }

    /**
     * Show registrations for a specific event - SEPARATED BY ROLE
     */
    public function event($eventId)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $event = Event::where('id', $eventId)
            ->where('organizer_id', $organizer->id)
            ->firstOrFail();
        
        // Determine event type
        $eventType = $event->delivery_mode ? 'conference' : 'innovation';
        
        // For innovation events: role is "jury", for conference: role is "reviewer"
        $secondaryRole = $eventType === 'innovation' ? 'jury' : 'reviewer';
        $secondaryRoleLabel = $eventType === 'innovation' ? 'Jury' : 'Reviewers';

        // Get participants and reviewers/jury separately
        $participants = EventRegistration::where('event_id', $eventId)
            ->where('role', 'participant')
            ->with('user')
            ->latest()
            ->get();

        $reviewers = EventRegistration::where('event_id', $eventId)
            ->where('role', $secondaryRole)
            ->with('user')
            ->latest()
            ->get();

        $stats = [
            'total' => EventRegistration::where('event_id', $eventId)->count(),
            'confirmed' => EventRegistration::where('event_id', $eventId)->where('status', 'confirmed')->count(),
            'pending' => EventRegistration::where('event_id', $eventId)->where('status', 'pending')->count(),
            'cancelled' => EventRegistration::where('event_id', $eventId)->where('status', 'cancelled')->count(),
            // Role-based statistics
            'participants' => $participants->count(),
            'participants_pending' => $participants->where('status', 'pending')->count(),
            'participants_confirmed' => $participants->where('status', 'confirmed')->count(),
            'reviewers' => $reviewers->count(),
            'reviewers_pending' => $reviewers->where('status', 'pending')->count(),
            'reviewers_confirmed' => $reviewers->where('status', 'confirmed')->count(),
        ];

        return view('organizer.registrations.event', compact('event', 'participants', 'reviewers', 'stats', 'eventType', 'secondaryRoleLabel'));
    }

    /**
     * Show individual registration details
     */
    public function show($id)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $registration = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->with(['event', 'user'])->findOrFail($id);

        // Manually load event paper for this registration
        $eventPaper = \App\Models\EventPaper::where('event_id', $registration->event_id)
            ->where('user_id', $registration->user_id)
            ->first();
        
        $registration->eventPaper = $eventPaper;

        return view('organizer.registrations.show', compact('registration'));
    }

    /**
     * Update registration status
     */
    public function updateStatus(Request $request, $id)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $registration = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,attended'
        ]);

        $oldStatus = $registration->status;
        $registration->status = $request->status;
        $registration->save();

        // Send notification email if status changed
        if ($oldStatus !== $request->status) {
            try {
                Mail::to($registration->user->email)
                    ->send(new EventRegistrationStatusUpdate($registration));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                Log::error('Failed to send registration status email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Registration status updated successfully.');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $registration = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->findOrFail($id);

        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded'
        ]);

        $registration->payment_status = $request->payment_status;
        
        if ($request->payment_status === 'paid' && !$registration->amount_paid) {
            $registration->amount_paid = $registration->event->registration_fee;
        }
        
        $registration->save();

        return back()->with('success', 'Payment status updated successfully.');
    }

    /**
     * Bulk update registrations
     */
    public function bulkUpdate(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:event_registrations,id',
            'action' => 'required|in:confirm,cancel,mark_paid,mark_attended'
        ]);

        $registrations = EventRegistration::whereIn('id', $request->registration_ids)
            ->whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->get();

        $updateCount = 0;
        
        foreach ($registrations as $registration) {
            switch ($request->action) {
                case 'confirm':
                    if ($registration->status !== 'confirmed') {
                        $registration->status = 'confirmed';
                        $registration->save();
                        $updateCount++;
                    }
                    break;
                case 'cancel':
                    if ($registration->status !== 'cancelled') {
                        $registration->status = 'cancelled';
                        $registration->save();
                        $updateCount++;
                    }
                    break;
                case 'mark_paid':
                    if ($registration->payment_status !== 'paid') {
                        $registration->payment_status = 'paid';
                        if (!$registration->amount_paid) {
                                                if (!$registration->amount_paid) {
                        $registration->amount_paid = $registration->event->registration_fee;
                    }
                        }
                        $registration->save();
                        $updateCount++;
                    }
                    break;
                case 'mark_attended':
                    if ($registration->status !== 'attended') {
                        $registration->status = 'attended';
                        $registration->save();
                        $updateCount++;
                    }
                    break;
            }
        }

        return back()->with('success', "Successfully updated {$updateCount} registrations.");
    }

    /**
     * Export registrations to CSV
     */
    public function export(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $query = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->with(['event', 'user']);

        // Apply filters
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $registrations = $query->get();

        $filename = 'registrations_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($registrations) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Registration ID',
                'Event Title',
                'Participant Name',
                'Participant Email',
                'Registration Date',
                'Status',
                'Payment Status',
                'Amount Paid',
                'Phone',
                'Emergency Contact',
                'Dietary Requirements',
                'Special Notes'
            ]);

            // CSV Data
            foreach ($registrations as $registration) {
                fputcsv($file, [
                    $registration->registration_code,
                    $registration->event->title,
                    $registration->user->name,
                    $registration->user->email,
                    $registration->created_at->format('Y-m-d H:i:s'),
                    ucfirst($registration->status),
                    ucfirst($registration->payment_status),
                    $registration->amount_paid ?: 0,
                    $registration->phone,
                    $registration->emergency_contact,
                    $registration->dietary_requirements,
                    $registration->special_notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send custom message to participants
     */
    public function sendMessage(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $request->validate([
            'registration_ids' => 'required|array',
            'registration_ids.*' => 'exists:event_registrations,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $registrations = EventRegistration::whereIn('id', $request->registration_ids)
            ->whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->with(['user', 'event'])->get();

        $sentCount = 0;
        
        foreach ($registrations as $registration) {
            try {
                Mail::raw($request->message, function ($mail) use ($registration, $request) {
                    $mail->to($registration->user->email)
                         ->subject($request->subject)
                         ->from(config('mail.from.address'), config('mail.from.name'));
                });
                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Failed to send message to ' . $registration->user->email . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', "Message sent to {$sentCount} participants.");
    }

    /**
     * Check-in participant (for event attendance)
     */
    public function checkIn($id)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $registration = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->findOrFail($id);

        $registration->status = 'attended';
        $registration->checked_in_at = now();
        $registration->save();

        return back()->with('success', 'Participant checked in successfully.');
    }
}