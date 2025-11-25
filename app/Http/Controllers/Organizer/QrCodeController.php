<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organizer');
    }

    public function index(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $qrCodes = EventQrCode::where('event_id', $event->id)
            ->latest()
            ->get();

        return view('organizer.qr-codes.index', compact('event', 'qrCodes'));
    }

    public function create(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        return view('organizer.qr-codes.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:check_in,check_out,general',
            'description' => 'nullable|string|max:500',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
        ]);

        $qrCode = EventQrCode::generateForEvent($event, $request->type);
        
        $qrCode->update([
            'description' => $request->description,
            'valid_from' => $request->valid_from ?: $event->event_date,
            'valid_until' => $request->valid_until ?: ($event->event_end_date ?: $event->event_date->addDay()),
        ]);

        // Generate the actual QR code image
        $this->generateQrCodeImage($qrCode);

        return redirect()->route('organizer.events.qr-codes.index', $event)
            ->with('success', 'QR Code created successfully!');
    }

    public function show(Event $event, EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $qrCode->event_id !== $event->id) {
            abort(403);
        }

        return view('organizer.qr-codes.show', compact('event', 'qrCode'));
    }

    public function edit(Event $event, EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $qrCode->event_id !== $event->id) {
            abort(403);
        }

        return view('organizer.qr-codes.edit', compact('event', 'qrCode'));
    }

    public function update(Request $request, Event $event, EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $qrCode->event_id !== $event->id) {
            abort(403);
        }

        $request->validate([
            'description' => 'nullable|string|max:500',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $qrCode->update([
            'description' => $request->description,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('organizer.events.qr-codes.show', [$event, $qrCode])
            ->with('success', 'QR Code updated successfully!');
    }

    public function destroy(Event $event, EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $qrCode->event_id !== $event->id) {
            abort(403);
        }

        // Delete QR code image file
        if ($qrCode->qr_image_path) {
            Storage::disk('public')->delete($qrCode->qr_image_path);
        }

        $qrCode->delete();

        return redirect()->route('organizer.events.qr-codes.index', $event)
            ->with('success', 'QR Code deleted successfully!');
    }

    public function download(Event $event, EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $qrCode->event_id !== $event->id) {
            abort(403);
        }

        if (!$qrCode->qr_image_path || !Storage::disk('public')->exists($qrCode->qr_image_path)) {
            // Generate QR code if it doesn't exist
            $this->generateQrCodeImage($qrCode);
        }

        $fileName = "qr_code_{$qrCode->type}_{$event->slug}.png";
        
        return Storage::disk('public')->download($qrCode->qr_image_path, $fileName);
    }

    public function regenerate(Event $event, EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $qrCode->event_id !== $event->id) {
            abort(403);
        }

        // Generate new QR code
        $newQrCode = Str::uuid();
        
        $qrCode->update([
            'qr_code' => $newQrCode,
            'scan_count' => 0,
            'last_scanned_at' => null,
        ]);

        // Regenerate QR image
        $this->generateQrCodeImage($qrCode);

        return redirect()->route('organizer.events.qr-codes.show', [$event, $qrCode])
            ->with('success', 'QR Code regenerated successfully!');
    }

    public function analytics(Event $event, EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id || $qrCode->event_id !== $event->id) {
            abort(403);
        }

        // Here you can add detailed analytics for QR code usage
        $analytics = [
            'total_scans' => $qrCode->scan_count,
            'last_scan' => $qrCode->last_scanned_at,
            'is_active' => $qrCode->is_active,
            'validity' => $qrCode->is_valid,
        ];

        return view('organizer.qr-codes.analytics', compact('event', 'qrCode', 'analytics'));
    }

    protected function generateQrCodeImage(EventQrCode $qrCode)
    {
        try {
            // Create the QR code data URL that will be scanned by users
            $scanUrl = route('qr.scan', ['qrCode' => $qrCode->qr_code]);
            
            // Create QR code using Builder pattern for v6
            $builder = new Builder();
            $builder->data($scanUrl);
            $builder->writer(new PngWriter());
            $builder->encoding(new Encoding('UTF-8'));
            $builder->errorCorrectionLevel(ErrorCorrectionLevel::High);
            $builder->size(400);
            $builder->margin(10);
            
            $result = $builder->build();

            // Create directory if it doesn't exist
            $directory = 'qr_codes';
            Storage::disk('public')->makeDirectory($directory);

            // Generate filename
            $fileName = "qr_code_{$qrCode->id}_{$qrCode->type}.png";
            $filePath = "{$directory}/{$fileName}";

            // Save the QR code image
            Storage::disk('public')->put($filePath, $result->getString());

            // Update the QR code with the image path
            $qrCode->update(['qr_image_path' => $filePath]);
            
            return $filePath;
        } catch (\Exception $e) {
            // Fallback: Create a simple text indicator if QR generation fails
            \Log::error('QR Code generation failed: ' . $e->getMessage());
            
            $directory = 'qr_codes';
            Storage::disk('public')->makeDirectory($directory);
            
            $fileName = "qr_placeholder_{$qrCode->id}.txt";
            $filePath = "{$directory}/{$fileName}";
            
            $content = "QR Code: {$qrCode->qr_code}\nType: {$qrCode->type}\nEvent: {$qrCode->event->title}\nScan URL: " . route('qr.scan', ['qrCode' => $qrCode->qr_code]);
            Storage::disk('public')->put($filePath, $content);
            
            $qrCode->update(['qr_image_path' => $filePath]);
            return $filePath;
        }
    }

    public function bulkGenerate(Event $event)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($event->organizer_id !== $organizer->id) {
            abort(403);
        }

        // Generate default QR codes for the event
        $types = ['check_in', 'check_out'];
        $generated = 0;

        foreach ($types as $type) {
            // Only generate if doesn't exist
            $exists = EventQrCode::where('event_id', $event->id)
                ->where('type', $type)
                ->where('is_active', true)
                ->exists();

            if (!$exists) {
                $qrCode = EventQrCode::generateForEvent($event, $type);
                $this->generateQrCodeImage($qrCode);
                $generated++;
            }
        }

        $message = $generated > 0 
            ? "Generated {$generated} QR codes successfully!" 
            : "QR codes already exist for this event.";

        return redirect()->route('organizer.events.qr-codes.index', $event)
            ->with('success', $message);
    }
    
    // General QR Code management methods (not event-specific)
    public function indexGeneral(Request $request)
    {
        $organizer = Auth::guard('organizer')->user();
        
        $query = EventQrCode::whereHas('event', function($q) use ($organizer) {
            $q->where('organizer_id', $organizer->id);
        })->with(['event']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('qr_code', 'ILIKE', "%{$search}%")
                  ->orWhere('type', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%")
                  ->orWhereHas('event', function($eventQuery) use ($search) {
                      $eventQuery->where('title', 'ILIKE', "%{$search}%");
                  });
            });
        }
        
        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }
        
        // Filter by type
        if ($request->has('qr_type') && $request->qr_type) {
            $query->where('type', $request->qr_type);
        }
        
        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $isActiveValue = $request->is_active;
            // Convert string values to proper boolean for PostgreSQL
            if (in_array($isActiveValue, ['1', 'true'], true)) {
                $query->whereRaw('is_active = true');
            } elseif (in_array($isActiveValue, ['0', 'false'], true)) {
                $query->whereRaw('is_active = false');
            }
        }
        
        $qrCodes = $query->orderBy('created_at', 'desc')->paginate(15);
        $events = Event::where('organizer_id', $organizer->id)->orderBy('title')->get();
        
        // Statistics
        $stats = [
            'total_qr_codes' => EventQrCode::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->count(),
            'active_qr_codes' => EventQrCode::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->whereRaw('is_active = true')->count(),
            'total_scans' => EventQrCode::whereHas('event', function($q) use ($organizer) {
                $q->where('organizer_id', $organizer->id);
            })->sum('scan_count'),
            'events_with_qr' => Event::where('organizer_id', $organizer->id)
                ->whereHas('qrCodes')->count()
        ];
        
        return view('organizer.qr-codes.index', compact('qrCodes', 'events', 'stats'));
    }
    
    public function showGeneral(EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($qrCode->event->organizer_id !== $organizer->id) {
            abort(403);
        }
        
        $qrCode->load(['event']);
        
        // Get scan statistics
        $scanStats = [
            'total_scans' => $qrCode->scan_count,
            'last_scan' => $qrCode->last_scanned_at
        ];
        
        return view('organizer.qr-codes.show-general', compact('qrCode', 'scanStats'));
    }
    
    public function downloadGeneral(EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($qrCode->event->organizer_id !== $organizer->id) {
            abort(403);
        }
        
        return response()->download(storage_path('app/' . $qrCode->qr_image_path));
    }
    
    public function destroyGeneral(EventQrCode $qrCode)
    {
        $organizer = Auth::guard('organizer')->user();
        
        if ($qrCode->event->organizer_id !== $organizer->id) {
            abort(403);
        }
        
        // Delete the QR code image file if it exists
        if (\Storage::exists($qrCode->qr_image_path)) {
            \Storage::delete($qrCode->qr_image_path);
        }
        
        $qrCode->delete();
        
        return redirect()->route('organizer.qr-codes.index')
            ->with('success', 'QR Code deleted successfully.');
    }
}
