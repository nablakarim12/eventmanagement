<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventOrganizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('organizer.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find organizer by organization email
        $organizer = EventOrganizer::where('org_email', $request->email)
            ->first();

        if (!$organizer) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        // Check if organizer is approved
        if ($organizer->status !== 'approved') {
            throw ValidationException::withMessages([
                'email' => ['Your account is not yet approved. Please wait for admin approval.'],
            ]);
        }

        // Verify password
        if (!Hash::check($request->password, $organizer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        // Log in the organizer
        Auth::guard('organizer')->login($organizer, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('organizer.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('organizer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('organizer.login');
    }

    public function showRegistrationForm()
    {
        return view('organizer.auth.register');
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'org_name' => 'required|string|max:255',
                'org_email' => 'required|email|unique:event_organizers,org_email',
                'phone' => 'required|string|max:20',
                'password' => [
                    'required',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%!&*])[^\s]{8,}$/'
                ],
                'documents.*' => 'nullable|file|mimes:pdf|max:10240', // 10MB max per file
            ], [
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, one special character (@#$%!&*), and no spaces.',
                'documents.*.mimes' => 'Only PDF files are allowed for documents.',
                'documents.*.max' => 'Each document must not exceed 10MB.',
            ]);

            // Check for common weak passwords
            $weakPasswords = ['password', '123456', '12345678', 'qwerty', 'abc123', 'password123'];
            if (in_array(strtolower($request->password), $weakPasswords)) {
                return back()->withErrors(['password' => 'This password is too common and easily guessed. Please choose a stronger password.'])->withInput();
            }

            $organizer = EventOrganizer::create([
                'org_name' => $request->org_name,
                'org_email' => $request->org_email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 'pending',
                // Optional fields that can be updated later
                'description' => '',
                'website' => null,
                'contact_person_name' => $request->org_name ?? 'Contact Person', // Default to org name
                'contact_person_position' => null,
                'address' => null,
                'city' => null,
                'state' => null,
                'country' => null,
                'postal_code' => null,
            ]);

            // Handle document uploads to Cloudinary
            if ($request->hasFile('documents')) {
                try {
                    foreach ($request->file('documents') as $index => $document) {
                        if ($document && $document->isValid()) {
                            // Get the real path of the uploaded file
                            $filePath = $document->getRealPath();
                            
                            // Verify file exists and is readable
                            if (empty($filePath) || !file_exists($filePath)) {
                                \Log::error('Invalid file path for document: ' . $document->getClientOriginalName());
                                continue;
                            }
                            
                            // Upload to Cloudinary in organizer-documents folder
                            $publicId = 'org_' . $organizer->id . '_' . time() . '_' . $index . '_' . pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                            
                            $uploadedFile = Cloudinary::upload($filePath, [
                                'folder' => 'organizer-documents',
                                'resource_type' => 'raw', // For PDF files
                                'public_id' => $publicId,
                            ]);
                            
                            // Get the secure URL from Cloudinary response
                            $fileUrl = $uploadedFile->getSecurePath();
                            
                            if (!empty($fileUrl)) {
                                \App\Models\EventOrganizerDocument::create([
                                    'event_organizer_id' => $organizer->id,
                                    'document_type' => 'registration',
                                    'file_path' => $fileUrl,
                                    'original_name' => $document->getClientOriginalName(),
                                ]);
                                \Log::info('Document uploaded successfully: ' . $document->getClientOriginalName() . ' -> ' . $fileUrl);
                            } else {
                                \Log::warning('Cloudinary upload returned empty URL for: ' . $document->getClientOriginalName());
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Document upload error: ' . $e->getMessage());
                    \Log::error('Stack trace: ' . $e->getTraceAsString());
                    // Continue with registration even if document upload fails
                }
            }

            return redirect()->route('organizer.login')->with('success', 
                'Registration successful! Please wait for admin approval before you can log in. You can complete your profile details after approval.');

        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function showChangePasswordForm()
    {
        return view('organizer.auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $organizer = Auth::guard('organizer')->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $organizer->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update password
        $organizer->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully!');
    }

    // Google OAuth redirect
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Google OAuth callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if organizer exists with this Google ID
            $organizer = EventOrganizer::where('google_id', $googleUser->id)->first();
            
            if (!$organizer) {
                // Check if email already exists
                $organizer = EventOrganizer::where('org_email', $googleUser->email)->first();
                
                if ($organizer) {
                    // Link existing account to Google
                    $organizer->update(['google_id' => $googleUser->id]);
                } else {
                    // Create new organizer account
                    $organizer = EventOrganizer::create([
                        'org_name' => $googleUser->name,
                        'org_email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'password' => null, // No password for OAuth users
                        'contact_person_name' => $googleUser->name,
                        'phone' => '', // Will need to update later
                        'status' => 'pending', // Still requires admin approval
                    ]);
                    
                    return redirect()->route('organizer.login')->with('success', 
                        'Account created successfully! Please wait for admin approval before you can log in. You may want to upload supporting documents for faster approval.');
                }
            }
            
            // Check if organizer is approved
            if ($organizer->status !== 'approved') {
                return redirect()->route('organizer.login')->with('error', 
                    'Your account is not yet approved. Please wait for admin approval.');
            }
            
            // Log in the organizer
            Auth::guard('organizer')->login($organizer, true);
            
            return redirect()->intended(route('organizer.dashboard'));
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('organizer.login')->with('error', 
                'Unable to login with Google. Please try again or use email/password.');
        }
    }
}
