<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\CommunicationTemplate;
use App\Models\CommunicationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendBulkEmails;

class CommunicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    /**
     * Show communication dashboard
     */
    public function index()
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get recent communications
        $recentCommunications = CommunicationLog::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->with(['event'])->latest()->take(10)->get();

        // Get communication statistics
        $stats = [
            'total_sent' => CommunicationLog::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->sum('recipients_count'),
            'this_month' => CommunicationLog::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->whereMonth('created_at', now()->month)->sum('recipients_count'),
            'templates' => CommunicationTemplate::where('organizer_id', $organizer->id)->count(),
            'events_with_participants' => Event::where('organizer_id', $organizer->id)
                ->whereHas('registrations')->count()
        ];

        return view('organizer.communications.index', compact('recentCommunications', 'stats'));
    }

    /**
     * Show compose message form
     */
    public function compose(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        // Get organizer's events with registration counts
        $events = Event::where('organizer_id', $organizer->id)
            ->withCount('registrations')
            ->having('registrations_count', '>', 0)
            ->get();

        // Get available templates
        $templates = CommunicationTemplate::where('organizer_id', $organizer->id)->get();

        // Pre-select event if provided
        $selectedEvent = null;
        if ($request->event_id) {
            $selectedEvent = Event::where('organizer_id', $organizer->id)
                ->where('id', $request->event_id)
                ->first();
        }

        return view('organizer.communications.compose', compact('events', 'templates', 'selectedEvent'));
    }

    /**
     * Send message to participants
     */
    public function send(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $request->validate([
            'event_ids' => 'required|array|min:1',
            'event_ids.*' => 'exists:events,id',
            'recipient_type' => 'required|in:all,confirmed,pending,attended',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'send_method' => 'required|in:immediate,scheduled',
            'scheduled_at' => 'required_if:send_method,scheduled|nullable|date|after:now'
        ]);

        // Get participants based on criteria
        $query = EventRegistration::whereIn('event_id', $request->event_ids)
            ->whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })
            ->with(['user', 'event']);

        // Filter by recipient type
        if ($request->recipient_type !== 'all') {
            $query->where('status', $request->recipient_type);
        }

        $registrations = $query->get();
        
        if ($registrations->isEmpty()) {
            return back()->with('error', 'No participants found matching your criteria.');
        }

        // Create communication log
        $communicationLog = CommunicationLog::create([
            'organizer_id' => $organizer->id,
            'event_id' => $request->event_ids[0], // Primary event
            'subject' => $request->subject,
            'message' => $request->message,
            'recipient_type' => $request->recipient_type,
            'recipients_count' => $registrations->count(),
            'status' => $request->send_method === 'scheduled' ? 'scheduled' : 'sending',
            'scheduled_at' => $request->send_method === 'scheduled' ? $request->scheduled_at : null
        ]);

        if ($request->send_method === 'immediate') {
            // Queue the email sending job
            Queue::push(new SendBulkEmails($registrations, $request->subject, $request->message, $communicationLog->id));
            
            return redirect()->route('organizer.communications.index')
                ->with('success', "Message queued for sending to {$registrations->count()} participants.");
        } else {
            // Schedule for later
            Queue::later(
                $request->scheduled_at,
                new SendBulkEmails($registrations, $request->subject, $request->message, $communicationLog->id)
            );
            
            return redirect()->route('organizer.communications.index')
                ->with('success', "Message scheduled for {$registrations->count()} participants on " . 
                    date('M j, Y \a\t g:i A', strtotime($request->scheduled_at)));
        }
    }

    /**
     * Get participants for selected events
     */
    public function getParticipants(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:events,id'
        ]);

        $stats = EventRegistration::whereIn('event_id', $request->event_ids)
            ->whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'total' => $stats->sum(),
            'breakdown' => $stats
        ]);
    }

    /**
     * Message templates management
     */
    public function templates()
    {
        $organizer = Auth::guard('organizer')->user();
        
        $templates = CommunicationTemplate::where('organizer_id', $organizer->id)
            ->orderBy('name')
            ->get();

        return view('organizer.communications.templates', compact('templates'));
    }

    /**
     * Create new template
     */
    public function storeTemplate(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:welcome,reminder,update,follow-up,custom'
        ]);

        CommunicationTemplate::create([
            'organizer_id' => $organizer->id,
            'name' => $request->name,
            'subject' => $request->subject,
            'message' => $request->message,
            'type' => $request->type
        ]);

        return back()->with('success', 'Template created successfully.');
    }

    /**
     * Get template content
     */
    public function getTemplate($id)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $template = CommunicationTemplate::where('organizer_id', $organizer->id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json($template);
    }

    /**
     * Send automated reminders
     */
    public function sendReminders(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'reminder_type' => 'required|in:24h,1h,custom',
            'custom_hours' => 'required_if:reminder_type,custom|integer|min:1'
        ]);

        $event = Event::where('organizer_id', $organizer->id)
            ->where('id', $request->event_id)
            ->firstOrFail();

        // Calculate reminder time
        $reminderHours = $request->reminder_type === 'custom' 
            ? $request->custom_hours 
            : ($request->reminder_type === '24h' ? 24 : 1);

        $reminderTime = now()->addHours($reminderHours);

        // Get confirmed participants
        $registrations = EventRegistration::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->with('user')
            ->get();

        if ($registrations->isEmpty()) {
            return back()->with('error', 'No confirmed participants found for this event.');
        }

        $subject = "Reminder: {$event->title} in {$reminderHours} " . ($reminderHours === 1 ? 'hour' : 'hours');
        $message = $this->generateReminderMessage($event, $reminderHours);

        // Create communication log
        $communicationLog = CommunicationLog::create([
            'organizer_id' => $organizer->id,
            'event_id' => $event->id,
            'subject' => $subject,
            'message' => $message,
            'recipient_type' => 'confirmed',
            'recipients_count' => $registrations->count(),
            'status' => 'scheduled',
            'scheduled_at' => $reminderTime,
            'type' => 'reminder'
        ]);

        // Schedule the reminder
        Queue::later(
            $reminderTime,
            new SendBulkEmails($registrations, $subject, $message, $communicationLog->id)
        );

        return back()->with('success', "Reminder scheduled for {$registrations->count()} participants in {$reminderHours} hours.");
    }

    /**
     * Generate reminder message
     */
    private function generateReminderMessage($event, $hours)
    {
        $timeText = $hours === 1 ? '1 hour' : "{$hours} hours";
        
        return "Hello [PARTICIPANT_NAME],\n\n" .
               "This is a friendly reminder that you're registered for:\n\n" .
               "📅 Event: {$event->title}\n" .
               "📍 Location: {$event->location}\n" .
               "🕒 Date & Time: " . $event->start_date->format('F j, Y \a\t g:i A') . "\n\n" .
               "Your event starts in {$timeText}. Please make sure to:\n" .
               "• Arrive 15 minutes early for check-in\n" .
               "• Bring this email or your registration code for verification\n" .
               "• Contact us if you have any questions\n\n" .
               "We're looking forward to seeing you!\n\n" .
               "Best regards,\n" .
               Auth::guard('organizer')->user()->org_name;
    }

    /**
     * Communication history
     */
    public function history()
    {
        $organizer = Auth::guard('organizer')->user();
        
        $communications = CommunicationLog::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->with('event')->latest()->paginate(20);

        return view('organizer.communications.history', compact('communications'));
    }
}