<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * Display all events with payment statistics
     */
    public function index(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get event type filter from request
        $eventType = $request->query('type'); // 'innovation' or 'conference'
        
        // Get events with payment counts (only paid events)
        $query = Event::where('organizer_id', $organizer->id)
            ->where('registration_fee', '>', 0);
        
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
        
        $eventsWithPayments = $query->withCount([
                'registrations as total_registrations',
                'registrations as pending_payment' => function($q) {
                    $q->where('payment_status', 'pending')
                      ->whereNotNull('payment_receipt_path');
                },
                'registrations as paid_count' => function($q) {
                    $q->where('payment_status', 'approved');
                },
                'registrations as rejected_payment' => function($q) {
                    $q->where('payment_status', 'rejected');
                },
            ])
            ->has('registrations')
            ->orderByDesc('pending_payment')
            ->orderByDesc('created_at')
            ->get();

        // Get payment statistics
        $stats = [
            'total' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id)
                  ->where('registration_fee', '>', 0);
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
            'pending' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id)
                  ->where('registration_fee', '>', 0);
                if ($eventType === 'innovation') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', 'Innovation Competition');
                    });
                } elseif ($eventType === 'conference') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', '!=', 'Innovation Competition');
                    });
                }
            })->where('payment_status', 'pending')
              ->whereNotNull('payment_receipt_path')
              ->count(),
            'paid' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id)
                  ->where('registration_fee', '>', 0);
                if ($eventType === 'innovation') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', 'Innovation Competition');
                    });
                } elseif ($eventType === 'conference') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', '!=', 'Innovation Competition');
                    });
                }
            })->where('payment_status', 'approved')->count(),
            'rejected' => EventRegistration::whereHas('event', function($q) use ($organizer, $eventType) {
                $q->where('organizer_id', $organizer->id)
                  ->where('registration_fee', '>', 0);
                if ($eventType === 'innovation') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', 'Innovation Competition');
                    });
                } elseif ($eventType === 'conference') {
                    $q->whereHas('category', function($c) {
                        $c->where('name', '!=', 'Innovation Competition');
                    });
                }
            })->where('payment_status', 'rejected')->count(),
        ];

        return view('organizer.payments.index', compact('eventsWithPayments', 'stats', 'eventType'));
    }

    /**
     * Show payments for a specific event
     */
    public function event($eventId)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $event = Event::where('id', $eventId)
            ->where('organizer_id', $organizer->id)
            ->firstOrFail();

        // Get registrations with payment proof (exclude jury members and rejected registrations)
        $registrations = EventRegistration::where('event_id', $event->id)
            ->whereDoesntHave('juryMappingsAsReviewer')  // Exclude jury members
            ->whereNull('rejected_at')  // Exclude rejected registrations
            ->whereNotNull('payment_receipt_path')  // Only show those who uploaded payment proof
            ->with(['user'])
            ->orderByRaw("CASE 
                WHEN payment_status = 'pending' AND payment_receipt_path IS NOT NULL THEN 1 
                WHEN payment_status = 'rejected' THEN 2 
                WHEN payment_status = 'approved' THEN 3 
                ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get();

        // Payment statistics for this event
        $stats = [
            'total' => $registrations->count(),
            'pending' => $registrations->where('payment_status', 'pending')->whereNotNull('payment_receipt_path')->count(),
            'paid' => $registrations->where('payment_status', 'approved')->count(),
            'rejected' => $registrations->where('payment_status', 'rejected')->count(),
        ];

        return view('organizer.payments.event', compact('event', 'registrations', 'stats'));
    }

    /**
     * Show payment details
     */
    public function show($registrationId)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $registration = EventRegistration::with(['user', 'event'])
            ->whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })
            ->findOrFail($registrationId);

        return view('organizer.payments.show', compact('registration'));
    }

    /**
     * Approve payment
     */
    public function approve($registrationId)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $registration = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->findOrFail($registrationId);

        $registration->payment_status = 'approved';
        $registration->payment_approved_at = now();
        $registration->payment_notes = null;
        $registration->save();

        Log::info("Payment approved for registration #{$registration->id} by organizer #{$organizer->id}");

        return back()->with('success', 'Payment approved successfully!');
    }

    /**
     * Reject payment
     */
    public function reject(Request $request, $registrationId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ], [
            'rejection_reason.required' => 'Please provide a reason for rejection',
            'rejection_reason.min' => 'Reason must be at least 10 characters',
            'rejection_reason.max' => 'Reason cannot exceed 500 characters'
        ]);

        $organizer = Auth::guard('organizer')->user();
        
        $registration = EventRegistration::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->findOrFail($registrationId);

        $registration->payment_status = 'rejected';
        $registration->payment_notes = $request->rejection_reason;
        $registration->payment_approved_at = now();
        $registration->save();

        Log::info("Payment rejected for registration #{$registration->id} by organizer #{$organizer->id}. Reason: {$request->rejection_reason}");

        return back()->with('success', 'Payment rejected. Participant will be notified.');
    }

    /**
     * Proxy payment receipt file from Cloudinary
     */
    public function viewReceipt($registrationId)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get registration with event relationship and verify ownership
        $registration = EventRegistration::whereHas('event', function ($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->findOrFail($registrationId);

        if (!$registration->payment_receipt_path) {
            abort(404, 'Payment receipt not found');
        }

        // Fetch the file from Cloudinary
        try {
            $fileContent = file_get_contents($registration->payment_receipt_path);
            
            if ($fileContent === false) {
                abort(404, 'Could not load payment receipt');
            }

            // Determine content type
            $isPdf = str_contains(strtolower($registration->payment_receipt_path), '.pdf');
            $contentType = $isPdf ? 'application/pdf' : 'image/jpeg';

            return response($fileContent)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'inline; filename="payment_receipt.'.($isPdf ? 'pdf' : 'jpg').'"');
                
        } catch (\Exception $e) {
            Log::error("Failed to load payment receipt for registration #{$registrationId}: " . $e->getMessage());
            abort(404, 'Could not load payment receipt');
        }
    }
}
