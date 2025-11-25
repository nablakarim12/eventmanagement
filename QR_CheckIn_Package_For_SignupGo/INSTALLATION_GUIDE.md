# QR Code Check-In System - Installation Guide for SignupGo

## 📦 Package Contents

This package contains all the files needed to enable QR code check-in functionality in the SignupGo user portal.

```
QR_CheckIn_Package_For_SignupGo/
├── app/
│   └── Http/
│       └── Controllers/
│           └── QrCheckInController.php          # Handles QR scanning and check-in
├── resources/
│   └── views/
│       ├── qr/
│       │   ├── check-in.blade.php              # Check-in confirmation page
│       │   ├── invalid.blade.php               # Invalid QR code error
│       │   ├── not-approved.blade.php          # Pending approval page
│       │   └── too-early.blade.php             # Event hasn't started page
│       └── dashboard/
│           └── registrations/
│               ├── index.blade.php             # List of user's registrations
│               └── show.blade.php              # Individual registration details (shows QR)
├── routes_to_add.txt                           # Routes to add to web.php
└── INSTALLATION_GUIDE.md                       # This file
```

---

## 🎯 What This System Does

### For Users:
1. **View QR Code** - After registration is approved, users see their unique QR code
2. **Download QR** - Users can download QR code as PNG
3. **Check In** - Users scan QR at event to check in automatically
4. **Track Status** - Users see check-in status in their dashboard

### Flow:
```
User Registers → Organizer Approves → QR Auto-Generated (in database) →
User Views QR in Dashboard → User Scans QR at Event → Check-In Confirmed ✅
```

---

## ⚙️ Prerequisites

Before installation, ensure your SignupGo project has:

- ✅ Laravel 10.x
- ✅ Shared database with EventManagement project (Supabase)
- ✅ `event_registrations` table with `qr_code` and `qr_image_path` columns
- ✅ User authentication system
- ✅ EventRegistration model
- ✅ Storage symlink created (`php artisan storage:link`)

---

## 📋 Installation Steps

### Step 1: Copy Files to SignupGo Project

Copy all files from this package to your SignupGo project:

```powershell
# Navigate to your SignupGo project
cd C:\laragon\www\signupgo

# Copy controller
Copy-Item "C:\laragon\www\eventmanagement\QR_CheckIn_Package_For_SignupGo\app\Http\Controllers\QrCheckInController.php" -Destination "app\Http\Controllers\"

# Copy QR views
New-Item -ItemType Directory -Path "resources\views\qr" -Force
Copy-Item "C:\laragon\www\eventmanagement\QR_CheckIn_Package_For_SignupGo\resources\views\qr\*" -Destination "resources\views\qr\" -Recurse

# Copy dashboard views
New-Item -ItemType Directory -Path "resources\views\dashboard\registrations" -Force
Copy-Item "C:\laragon\www\eventmanagement\QR_CheckIn_Package_For_SignupGo\resources\views\dashboard\registrations\*" -Destination "resources\views\dashboard\registrations\" -Recurse
```

**OR** manually copy using File Explorer:
- Copy `app/Http/Controllers/QrCheckInController.php` → `signupgo/app/Http/Controllers/`
- Copy `resources/views/qr/` folder → `signupgo/resources/views/`
- Copy `resources/views/dashboard/registrations/` folder → `signupgo/resources/views/`

---

### Step 2: Add Routes

Open `signupgo/routes/web.php` and add these routes:

**Location:** Add AFTER your existing user authentication routes, but BEFORE the closing of the file.

```php
// QR Code Check-In Routes (Public - no auth required for scanning)
Route::prefix('check-in')->name('qr.scan.')->group(function () {
    Route::get('/{qrCode}', [App\Http\Controllers\QrCheckInController::class, 'scan'])->name('registration');
    Route::post('/{qrCode}', [App\Http\Controllers\QrCheckInController::class, 'checkIn'])->name('process');
});

// User Dashboard Routes (Protected - auth required)
Route::middleware(['auth'])->group(function () {
    // Registration Management
    Route::get('/dashboard/registrations', [App\Http\Controllers\EventRegistrationController::class, 'index'])->name('dashboard.registrations');
    Route::get('/dashboard/registrations/{registration}', [App\Http\Controllers\EventRegistrationController::class, 'show'])->name('dashboard.registrations.show');
});
```

**Note:** If you already have a `dashboard.registrations` route, just add the `show` route.

---

### Step 3: Update EventRegistration Model

Open `signupgo/app/Models/EventRegistration.php` and ensure these columns are in `$fillable`:

```php
protected $fillable = [
    // ... your existing fields ...
    'qr_code',
    'qr_image_path',
    'checked_in_at',
    // ... rest of your fields ...
];
```

**Optional:** Add a helper method to check if user has checked in:

```php
/**
 * Check if user has checked in
 */
public function isCheckedIn(): bool
{
    return !is_null($this->checked_in_at);
}
```

---

### Step 4: Verify EventRegistrationController Exists

Check if `signupgo/app/Http/Controllers/EventRegistrationController.php` exists.

**If it EXISTS:**
- Make sure it has `index()` and `show()` methods (the views expect these)

**If it DOESN'T EXIST:**
Create it with this command:

```powershell
php artisan make:controller EventRegistrationController
```

Then add these methods:

```php
<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventRegistrationController extends Controller
{
    /**
     * Show user's registrations
     */
    public function index()
    {
        $registrations = Auth::user()->eventRegistrations()
            ->with(['event', 'event.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.registrations.index', compact('registrations'));
    }

    /**
     * Show specific registration details
     */
    public function show(EventRegistration $registration)
    {
        // Ensure user can only view their own registrations
        if ($registration->user_id !== Auth::id()) {
            abort(403);
        }

        $registration->load(['event', 'event.category']);

        return view('dashboard.registrations.show', compact('registration'));
    }
}
```

---

### Step 5: Create Storage Symlink (If Not Already Done)

```powershell
cd C:\laragon\www\signupgo
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` so QR code images are accessible.

---

### Step 6: Update User Model (If Needed)

Ensure your `User` model has the relationship to `EventRegistration`:

Open `signupgo/app/Models/User.php`:

```php
/**
 * Get user's event registrations
 */
public function eventRegistrations()
{
    return $this->hasMany(EventRegistration::class);
}
```

---

### Step 7: Check Event Model Relationship

Ensure your `Event` model has the correct relationship:

Open `signupgo/app/Models/Event.php`:

```php
/**
 * Get event registrations
 */
public function registrations()
{
    return $this->hasMany(EventRegistration::class);
}
```

---

### Step 8: Update Navigation (Optional but Recommended)

Add a link to "My Registrations" in your user dashboard navigation.

**Example (if using Blade layouts):**

```blade
<a href="{{ route('dashboard.registrations') }}" class="nav-link">
    My Registrations
</a>
```

---

### Step 9: Clear Caches

```powershell
cd C:\laragon\www\signupgo
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

---

### Step 10: Test the Installation

#### Test 1: View Registrations List

1. Log in as a user (who has registered for events)
2. Go to: `http://signupgo.test/dashboard/registrations`
3. You should see a list of your registrations

#### Test 2: View QR Code

1. Click on any **approved** registration
2. You should see:
   - Registration details
   - QR code image (if approved)
   - Download QR button
   - Check-in status

#### Test 3: Test Check-In

**Option A: Click QR Link**
1. On the registration details page, click "Test QR Code" button
2. Should open check-in confirmation page
3. Click "Confirm Check-In"
4. Should show success and mark as checked in

**Option B: Manual URL**
1. Copy the QR code (e.g., `REG-ABC123DEF456`)
2. Go to: `http://signupgo.test/check-in/REG-ABC123DEF456`
3. Should show check-in page

---

## 🔧 Troubleshooting

### Issue 1: Routes not found (404 error)

**Solution:**
```powershell
php artisan route:clear
php artisan route:list | findstr "check-in"
```

Verify these routes exist:
- `GET check-in/{qrCode}`
- `POST check-in/{qrCode}`

### Issue 2: QR code image not showing

**Solution:**
```powershell
# Check if storage symlink exists
ls public\storage

# If not, create it
php artisan storage:link

# Check if QR images exist
ls storage\app\public\qr-codes
```

### Issue 3: "Class QrCheckInController not found"

**Solution:**
- Make sure you copied `QrCheckInController.php` to the correct location
- Check namespace in controller matches: `namespace App\Http\Controllers;`
- Clear caches: `php artisan clear-compiled`

### Issue 4: "View [dashboard.registrations.index] not found"

**Solution:**
- Make sure you copied the views to `resources/views/dashboard/registrations/`
- Check file names are exactly:
  - `index.blade.php`
  - `show.blade.php`

### Issue 5: CSRF token mismatch

**Solution:**
```powershell
php artisan config:clear
php artisan cache:clear
```

### Issue 6: "Relationship [eventRegistrations] not found"

**Solution:**
Add to `User` model:
```php
public function eventRegistrations()
{
    return $this->hasMany(EventRegistration::class);
}
```

---

## 📱 Testing with Phone (Using Ngrok)

To test QR scanning with your phone camera:

### 1. Install Ngrok

```powershell
choco install ngrok
# OR download from https://ngrok.com/download
```

### 2. Start Ngrok Tunnel

```powershell
ngrok http signupgo.test:80 --host-header="signupgo.test"
```

### 3. Update .env

Copy the ngrok URL (e.g., `https://abc123.ngrok-free.app`)

```env
APP_URL=https://abc123.ngrok-free.app
SESSION_DOMAIN=.ngrok-free.app
```

### 4. Clear Config

```powershell
php artisan config:clear
```

### 5. Test

1. Open ngrok URL on your computer: `https://abc123.ngrok-free.app`
2. Login as user → View registration → See QR code
3. Open phone camera → Scan QR from computer screen
4. Tap link → Check in!

---

## 🔒 Security Considerations

### 1. QR Code URLs are Public
- Anyone with the QR code URL can check in
- This is intentional for easy scanning
- QR codes are unique and hard to guess (12 random characters)

### 2. Validation Checks
The system validates:
- ✅ QR code exists in database
- ✅ Registration is approved
- ✅ Event date is today or past
- ✅ Not already checked in

### 3. Authorization
- Users can only view their own registrations
- Check-in URLs are public (no auth required for scanning)

---

## 📊 Database Impact

This system uses **existing** database columns (already added by organizer):

```sql
-- event_registrations table
qr_code VARCHAR           -- e.g., "REG-ABC123DEF456"
qr_image_path VARCHAR     -- e.g., "qr-codes/REG-ABC123DEF456.png"
checked_in_at TIMESTAMP   -- e.g., "2025-11-23 15:30:00"
```

**No migrations needed** - these columns were added when the organizer approved registrations.

---

## 🎨 Customization

### Change QR Code Size

Edit `resources/views/dashboard/registrations/show.blade.php`:

```blade
<!-- Find this line -->
<img src="{{ asset('storage/' . $registration->qr_image_path) }}" 
     alt="Registration QR Code" 
     class="w-64 h-64">  <!-- Change w-64 h-64 to desired size -->
```

### Change Check-In Page Colors

Edit files in `resources/views/qr/`:
- `check-in.blade.php` - Main check-in page (blue theme)
- `invalid.blade.php` - Error page (red theme)
- `not-approved.blade.php` - Pending page (yellow theme)

Colors use Tailwind CSS classes (e.g., `bg-blue-600`, `text-red-800`)

### Add Your Logo

Edit `resources/views/qr/check-in.blade.php` and add:

```blade
<div class="text-center mb-4">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 mx-auto">
</div>
```

---

## 📞 Support & Questions

If you encounter issues:

1. **Check Laravel Logs:**
   ```powershell
   Get-Content storage\logs\laravel.log -Tail 50
   ```

2. **Verify Database Connection:**
   ```powershell
   php artisan tinker
   >>> \App\Models\EventRegistration::count()
   ```

3. **Check Routes:**
   ```powershell
   php artisan route:list
   ```

4. **Clear Everything:**
   ```powershell
   php artisan optimize:clear
   ```

---

## ✅ Post-Installation Checklist

After installation, verify:

- [ ] Files copied to correct locations
- [ ] Routes added to `web.php`
- [ ] `EventRegistration` model has `qr_code`, `qr_image_path` in `$fillable`
- [ ] `EventRegistrationController` exists with `index()` and `show()` methods
- [ ] Storage symlink created
- [ ] User model has `eventRegistrations()` relationship
- [ ] Caches cleared
- [ ] Can access: `http://signupgo.test/dashboard/registrations`
- [ ] Can see QR code on approved registration
- [ ] Can click QR code and reach check-in page
- [ ] Check-in button works and marks attendance

---

## 🚀 You're Done!

Your users can now:
- ✅ View their registrations
- ✅ See QR codes for approved events
- ✅ Download QR codes
- ✅ Check in at events by scanning QR
- ✅ Track their check-in status

**Happy Event Managing! 🎉**
