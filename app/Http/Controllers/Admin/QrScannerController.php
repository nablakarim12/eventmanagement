<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventQrCode;
use App\Models\Registration;
use App\Models\Attendance;
use Illuminate\Http\Request;

class QrScannerController extends Controller
{
    public function index()
    {
        return view('admin.qr-scanner.index');
    }
    
    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);
        
        try {
            // Find the QR code
            $qrCode = EventQrCode::where('qr_code', $request->qr_code)->first();
            
            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code'
                ]);
            }
            
            if (!$qrCode->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code is inactive'
                ]);
            }
            
            // Update QR code scan statistics
            $qrCode->increment('scan_count');
            $qrCode->update(['last_scanned_at' => now()]);
            
            $response = [
                'success' => true,
                'qr_code' => $qrCode,
                'event' => $qrCode->event,
                'message' => 'QR code scanned successfully'
            ];
            
            // If it's an attendance QR code, try to check in the user
            if ($qrCode->type === 'attendance') {
                $registration = Registration::where('event_id', $qrCode->event_id)
                    ->where('qr_code', $request->qr_code)
                    ->first();
                    
                if ($registration) {
                    $existingAttendance = Attendance::where('registration_id', $registration->id)->first();
                    
                    if (!$existingAttendance) {
                        // Create new attendance record
                        Attendance::create([
                            'registration_id' => $registration->id,
                            'checked_in_at' => now(),
                            'check_in_method' => 'qr_scan'
                        ]);
                        
                        $response['attendance_action'] = 'checked_in';
                        $response['user'] = $registration->user;
                        $response['message'] = 'User checked in successfully';
                    } else if (!$existingAttendance->checked_out_at) {
                        // Check out the user
                        $existingAttendance->update([
                            'checked_out_at' => now()
                        ]);
                        
                        $response['attendance_action'] = 'checked_out';
                        $response['user'] = $registration->user;
                        $response['message'] = 'User checked out successfully';
                    } else {
                        $response['attendance_action'] = 'already_completed';
                        $response['user'] = $registration->user;
                        $response['message'] = 'User has already completed attendance';
                    }
                }
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code: ' . $e->getMessage()
            ]);
        }
    }
}