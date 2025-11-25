<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use App\Models\EventOrganizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('organizer.auth.register');
    }

    /**
     * Handle the registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'org_name' => 'required|string|max:255',
            'org_email' => 'required|string|email|max:255|unique:event_organizers',
            'password' => 'required|string|min:8|confirmed',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_position' => 'nullable|string|max:255',
            'documents' => 'nullable|array',
            'documents.*' => 'required|file|mimes:pdf|max:10240', // 10MB max for PDF files
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'pending';

        $organizer = EventOrganizer::create($validated);

        // Handle document uploads if any
        try {
            if ($request->hasFile('documents')) {
                Log::info('Documents are present in request');
                foreach ($request->file('documents') as $document) {
                    if (!$document->isValid()) {
                        Log::error('Invalid file upload: ' . $document->getErrorMessage());
                        return back()->withErrors(['documents' => 'File upload failed: ' . $document->getErrorMessage()]);
                    }

                    Log::info('Processing document: ' . $document->getClientOriginalName());
                    Log::info('File size: ' . $document->getSize() . ' bytes');
                    Log::info('File type: ' . $document->getMimeType());

                    try {
                        $extension = $document->getClientOriginalExtension();
                        $filename = time() . '_' . uniqid() . '.' . $extension;
                        
                        // Get the storage path
                        $storagePath = storage_path('app/public/organizer-documents');
                        
                        // Create directory if it doesn't exist
                        if (!file_exists($storagePath)) {
                            mkdir($storagePath, 0755, true);
                        }
                        
                        // Move the file
                        $document->move($storagePath, $filename);
                        
                        // Store the relative path
                        $relativePath = 'organizer-documents/' . $filename;
                        
                        Log::info('File stored at: ' . $storagePath . '/' . $filename);
                        
                        $organizer->documents()->create([
                            'file_path' => $relativePath,
                            'original_name' => $document->getClientOriginalName(),
                            'document_type' => $document->getMimeType(),
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error storing file: ' . $e->getMessage());
                        Log::error('Stack trace: ' . $e->getTraceAsString());
                        return back()->withErrors(['documents' => 'Error storing file: ' . $e->getMessage()]);
                    }
                }
            } else {
                Log::info('No documents in request');
                if ($request->has('documents')) {
                    Log::info('Documents field exists but is not a file');
                    Log::info('Documents field value: ' . print_r($request->input('documents'), true));
                }
            }
        } catch (\Exception $e) {
            Log::error('Unexpected error in file upload: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['documents' => 'An unexpected error occurred during file upload']);
        }

        Auth::guard('event-organizer')->login($organizer);

        return redirect()->route('organizer.dashboard')
            ->with('success', 'Registration successful! Your account is pending approval.');
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('organizer.auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'org_email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('event-organizer')->attempt($credentials)) {
            $request->session()->regenerate();

            $organizer = Auth::guard('event-organizer')->user();
            if ($organizer->status === 'rejected') {
                Auth::guard('event-organizer')->logout();
                return back()->withErrors([
                    'email' => 'Your account has been rejected. Reason: ' . $organizer->rejection_reason,
                ]);
            }

            if ($organizer->status === 'pending') {
                return redirect()->route('organizer.dashboard')
                    ->with('warning', 'Your account is pending approval.');
            }

            return redirect()->intended(route('organizer.dashboard'));
        }

        return back()->withErrors([
            'org_email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        Auth::guard('event-organizer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('organizer.login');
    }
}
