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
     * Display all registrations for organizer's events
     */
    public function index(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $query = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->with(['event', 'user']);

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by participant name or email
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $registrations = $query->latest()->paginate(20);
        
        // Get organizer's events for filter dropdown
        $events = Event::where('organizer_id', $organizer->id)
            ->select('id', 'title')
            ->get();

        // Get registration statistics
        $stats = [
            'total' => EventRegistration::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->count(),
            'confirmed' => EventRegistration::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->where('status', 'confirmed')->count(),
            'pending' => EventRegistration::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->where('status', 'pending')->count(),
            'cancelled' => EventRegistration::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->where('status', 'cancelled')->count(),
            // Role-based statistics
            'participants' => EventRegistration::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->whereIn('role', ['participant', 'both'])->count(),
            'jury' => EventRegistration::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->whereIn('role', ['jury', 'both'])->count(),
        ];

        return view('organizer.registrations.index', compact('registrations', 'events', 'stats'));
    }

    /**
     * Show registrations for a specific event
     */
    public function event($eventId)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $event = Event::where('id', $eventId)
            ->where('organizer_id', $organizer->id)
            ->firstOrFail();

        $registrations = EventRegistration::where('event_id', $eventId)
            ->with('user')
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => $registrations->total(),
            'confirmed' => EventRegistration::where('event_id', $eventId)->where('status', 'confirmed')->count(),
            'pending' => EventRegistration::where('event_id', $eventId)->where('status', 'pending')->count(),
            'cancelled' => EventRegistration::where('event_id', $eventId)->where('status', 'cancelled')->count(),
            'attended' => EventRegistration::where('event_id', $eventId)->where('status', 'attended')->count(),
            // Role-based statistics
            'participants' => EventRegistration::where('event_id', $eventId)->whereIn('role', ['participant', 'both'])->count(),
            'jury' => EventRegistration::where('event_id', $eventId)->whereIn('role', ['jury', 'both'])->count(),
        ];

        return view('organizer.registrations.event', compact('event', 'registrations', 'stats'));
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