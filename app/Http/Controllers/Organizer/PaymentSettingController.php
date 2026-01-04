<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PaymentSetting;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    protected $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    /**
     * Show payment setup form
     */
    public function showSetupForm(Event $event)
    {
        // Check if event has registration fee
        if (!$event->registration_fee || $event->registration_fee == 0) {
            return redirect()->route('organizer.events.show', $event)
                ->with('error', 'This event is free. Payment setup is not required.');
        }

        $paymentSetting = PaymentSetting::where('event_id', $event->id)->first();

        return view('organizer.payment.setup', compact('event', 'paymentSetting'));
    }

    /**
     * Save payment settings
     */
    public function saveSettings(Request $request, Event $event)
    {
        $validated = $request->validate([
            'qr_code' => 'nullable|image|max:5120', // 5MB max
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
        ]);

        $paymentSetting = PaymentSetting::firstOrNew(['event_id' => $event->id]);

        // Upload QR code to Cloudinary if provided
        if ($request->hasFile('qr_code')) {
            // Delete old QR if exists
            if ($paymentSetting->qr_cloudinary_id) {
                $this->cloudinary->deleteFile($paymentSetting->qr_cloudinary_id);
            }

            $uploadResult = $this->cloudinary->uploadFile(
                $request->file('qr_code'),
                'payment-qr-codes'
            );

            $paymentSetting->qr_code_url = $uploadResult['secure_url'];
            $paymentSetting->qr_cloudinary_id = $uploadResult['public_id'];
        }

        $paymentSetting->bank_name = $validated['bank_name'];
        $paymentSetting->account_number = $validated['account_number'];
        $paymentSetting->account_holder_name = $validated['account_holder_name'];
        $paymentSetting->is_active = \DB::raw('true');

        $paymentSetting->save();

        return redirect()->route('organizer.events.show', $event)
            ->with('success', 'Payment settings saved successfully!');
    }
}
