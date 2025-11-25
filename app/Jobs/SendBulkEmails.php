<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\CommunicationLog;

class SendBulkEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $registrations;
    protected $subject;
    protected $message;
    protected $communicationLogId;

    /**
     * Create a new job instance.
     */
    public function __construct($registrations, $subject, $message, $communicationLogId)
    {
        $this->registrations = $registrations;
        $this->subject = $subject;
        $this->message = $message;
        $this->communicationLogId = $communicationLogId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sentCount = 0;
        $failedCount = 0;

        // Update status to sending
        $log = CommunicationLog::find($this->communicationLogId);
        if ($log) {
            $log->update(['status' => 'sending']);
        }

        foreach ($this->registrations as $registration) {
            try {
                // Replace placeholders in message
                $personalizedMessage = $this->personalizeMessage($this->message, $registration);
                
                Mail::raw($personalizedMessage, function ($mail) use ($registration) {
                    $mail->to($registration->user->email, $registration->user->name)
                         ->subject($this->subject)
                         ->from(config('mail.from.address'), config('mail.from.name'));
                });

                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Failed to send bulk email to ' . $registration->user->email . ': ' . $e->getMessage());
            }

            // Add small delay to avoid overwhelming mail server
            usleep(100000); // 0.1 second delay
        }

        // Update communication log
        if ($log) {
            $log->update([
                'status' => 'completed',
                'sent_at' => now(),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount
            ]);
        }
    }

    /**
     * Personalize message with participant data
     */
    private function personalizeMessage($message, $registration)
    {
        $replacements = [
            '[PARTICIPANT_NAME]' => $registration->user->name,
            '[EVENT_TITLE]' => $registration->event->title,
            '[EVENT_DATE]' => $registration->event->start_date->format('F j, Y'),
            '[EVENT_TIME]' => $registration->event->start_date->format('g:i A'),
            '[EVENT_LOCATION]' => $registration->event->location,
            '[REGISTRATION_CODE]' => $registration->registration_code,
            '[ORGANIZER_NAME]' => $registration->event->organizer->org_name ?? 'Event Organizer'
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        $log = CommunicationLog::find($this->communicationLogId);
        if ($log) {
            $log->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage()
            ]);
        }
    }
}