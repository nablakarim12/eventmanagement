<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventOrganizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
                'password' => 'required|min:8|confirmed',
                'contact_person_name' => 'required|string|max:255',
            ]);

            $organizer = EventOrganizer::create([
                'org_name' => $request->org_name,
                'org_email' => $request->org_email,
                'description' => $request->description ?? '',
                'website' => $request->website,
                'phone' => $request->phone,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_position' => $request->contact_person_position,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'password' => Hash::make($request->password),
                'status' => 'pending',
            ]);

            return redirect()->route('organizer.login')->with('success', 
                'Registration successful! Please wait for admin approval before you can log in.');

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
}
