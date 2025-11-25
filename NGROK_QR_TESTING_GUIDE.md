# QR Code Check-In System - Testing with Phone (Ngrok Setup)

## Overview
This guide will help you test the QR code check-in system using your phone camera. Since your Laravel app runs on `localhost` (not accessible from your phone), we'll use **ngrok** to create a temporary public URL.

---

## What You've Just Built

### ✅ Features Implemented:
1. **QR Code Generation**: Auto-generated when registration is approved
2. **User Dashboard**: Shows QR code for approved registrations
3. **QR Scanning**: Phone-friendly check-in page
4. **Check-In Tracking**: Marks `checked_in_at` timestamp automatically
5. **Smart Validation**: Checks approval status, event date, duplicate check-ins

### Database Updates:
- Added `qr_code` column (unique identifier)
- Added `qr_image_path` column (stores PNG file path)

---

## Step 1: Install Ngrok

### Option A: Using Chocolatey (Recommended for Windows)
```powershell
choco install ngrok
```

### Option B: Manual Download
1. Visit https://ngrok.com/download
2. Download the Windows version
3. Extract `ngrok.exe` to `C:\ngrok\` (or any folder)
4. Add `C:\ngrok` to your PATH environment variable

### Option C: Using Scoop
```powershell
scoop install ngrok
```

---

## Step 2: Create Ngrok Account (Free)

1. Go to https://dashboard.ngrok.com/signup
2. Sign up for a free account
3. Copy your **authtoken** from https://dashboard.ngrok.com/get-started/your-authtoken
4. Run this command to authenticate:
   ```powershell
   ngrok config add-authtoken YOUR_TOKEN_HERE
   ```

---

## Step 3: Start Ngrok Tunnel

Since your Laravel app runs on Laragon (default port 80), run:

```powershell
ngrok http 80
```

**You should see output like this:**
```
ngrok                                                                    

Session Status                online
Account                       your-email@example.com
Version                       3.x.x
Region                        United States (us)
Latency                       -
Web Interface                 http://127.0.0.1:4040
Forwarding                    https://abc123def456.ngrok-free.app -> http://localhost:80

Connections                   ttl     opn     rt1     rt5     p50     p90
                              0       0       0.00    0.00    0.00    0.00
```

**Copy the HTTPS URL** (e.g., `https://abc123def456.ngrok-free.app`) - this is your public URL!

---

## Step 4: Update Laravel Configuration

### 4.1 Update `.env` File
Open `c:\laragon\www\eventmanagement\.env` and update:

```env
APP_URL=https://abc123def456.ngrok-free.app
SESSION_DOMAIN=.ngrok-free.app
```

**Important:** Replace `abc123def456.ngrok-free.app` with YOUR actual ngrok URL!

### 4.2 Clear Laravel Cache
```powershell
cd C:\laragon\www\eventmanagement
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Step 5: Testing the QR Code System

### 5.1 Test an Existing Registration
1. **Login as Event Organizer**:
   - Go to: `https://your-ngrok-url.ngrok-free.app/organizer/login`
   - Approve a pending registration (this will generate QR code automatically)

2. **Login as User** (who registered):
   - Go to: `https://your-ngrok-url.ngrok-free.app/login`
   - Navigate to Dashboard → My Registrations
   - Click on the approved registration
   - You should see the QR code image!

3. **Scan QR Code with Phone**:
   - Open your phone camera app
   - Point at the QR code on your computer screen
   - Tap the notification/link that appears
   - You'll land on the check-in confirmation page
   - Click "Confirm Check-In"
   - Success! ✓ You're checked in

### 5.2 Test New Registration Flow
1. **Register for an Event as Participant**:
   ```
   https://your-ngrok-url.ngrok-free.app/events
   ```
   - Pick an event → Click "Register"
   - Fill out the form (select "Participant" role)
   - Submit registration

2. **Approve as Organizer**:
   - Login as organizer: `https://your-ngrok-url.ngrok-free.app/organizer/login`
   - Go to: Registrations → Pending
   - Approve the registration
   - **QR code is auto-generated!**

3. **View QR Code as User**:
   - Login as user
   - Dashboard → My Registrations → View Details
   - Download or scan the QR code

4. **Test Check-In on Phone**:
   - Scan QR → Confirm → See success message

---

## Step 6: Troubleshooting

### Issue: Ngrok shows "Visit Site" button (free plan warning)
**Solution**: Just click "Visit Site" on the warning page - it's normal for free ngrok accounts.

### Issue: QR code image not showing
**Solutions**:
1. Check if QR was generated:
   ```powershell
   ls C:\laragon\www\eventmanagement\storage\app\public\qr-codes\
   ```
2. Verify storage symlink:
   ```powershell
   php artisan storage:link
   ```
3. Check file permissions on `storage/app/public/qr-codes/`

### Issue: 404 error when scanning QR
**Solutions**:
1. Verify route exists:
   ```powershell
   php artisan route:list | findstr "check-in"
   ```
   Should show:
   ```
   GET|HEAD  check-in/{qrCode} ... QrCheckInController@scan
   POST      check-in/{qrCode} ... QrCheckInController@checkIn
   ```

2. Check if `APP_URL` in `.env` matches ngrok URL exactly

### Issue: "Registration not approved" error
**Solution**: Make sure you approved the registration in the organizer panel first!

### Issue: CSRF token mismatch
**Solutions**:
1. Clear Laravel cache:
   ```powershell
   php artisan config:clear; php artisan cache:clear
   ```
2. Make sure `SESSION_DOMAIN` in `.env` includes the `.ngrok-free.app` domain

---

## Step 7: When You're Done Testing

### 7.1 Stop Ngrok
- Press `Ctrl+C` in the ngrok terminal window

### 7.2 Restore Local Configuration
Update `.env` back to:
```env
APP_URL=http://localhost
SESSION_DOMAIN=localhost
```

Clear cache:
```powershell
php artisan config:clear
```

---

## How the QR System Works

### Flow Diagram:
```
1. User registers for event (participant/jury/both)
   ↓
2. Organizer approves registration
   ↓
3. Observer auto-generates QR code
   - Creates unique code: REG-XXXXXXXXXXXX
   - Generates PNG image: qr-codes/REG-XXXXXXXXXXXX.png
   - Saves to registration: qr_code & qr_image_path
   ↓
4. User views registration details
   - Dashboard shows QR code image
   - Can download QR as PNG
   ↓
5. User scans QR at event (with phone camera)
   - Lands on: /check-in/{qrCode}
   - Sees confirmation page with event details
   ↓
6. User clicks "Confirm Check-In"
   - POST request to /check-in/{qrCode}
   - Backend sets checked_in_at = now()
   - Returns success JSON
   ↓
7. Page shows "Already Checked In" status
   - User cannot check in twice
   - Timestamp is displayed
```

### Database Schema:
```sql
event_registrations table:
- qr_code VARCHAR (unique) - e.g., "REG-ABC123DEF456"
- qr_image_path VARCHAR - e.g., "qr-codes/REG-ABC123DEF456.png"
- checked_in_at TIMESTAMP - e.g., "2025-11-23 15:30:00"
```

---

## QR Code Routes Reference

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `/check-in/{qrCode}` | GET | `QrCheckInController@scan` | Show check-in page |
| `/check-in/{qrCode}` | POST | `QrCheckInController@checkIn` | Process check-in |
| `/dashboard/registrations/{id}` | GET | `EventRegistrationController@show` | View QR code |

---

## Files Created/Modified

### New Files:
- `app/Observers/EventRegistrationObserver.php` - Auto-generates QR on approval
- `app/Http/Controllers/QrCheckInController.php` - Handles check-in logic
- `resources/views/qr/check-in.blade.php` - Check-in confirmation page
- `resources/views/qr/invalid.blade.php` - Invalid QR error page
- `resources/views/qr/not-approved.blade.php` - Pending approval page
- `resources/views/qr/too-early.blade.php` - Event hasn't started page
- `resources/views/dashboard/registrations/show.blade.php` - User QR view
- `database/migrations/2025_11_23_145737_add_qr_code_to_event_registrations_table.php`

### Modified Files:
- `app/Providers/EventServiceProvider.php` - Registered observer
- `app/Models/EventRegistration.php` - Added qr_code, qr_image_path to $fillable
- `routes/web.php` - Added check-in routes

---

## Next Steps (Optional Enhancements)

1. **Certificate Generation**:
   - Generate PDF certificates after event ends
   - Only for checked-in users
   - Different certificates for participant vs jury

2. **Email QR Code**:
   - Send QR code via email after approval
   - Update `JuryApproved.php` mailable

3. **Check-In Analytics**:
   - Organizer dashboard showing real-time check-ins
   - Export attendance reports

4. **Offline QR Scanning**:
   - Mobile app for organizers
   - Scan QRs without internet (store locally, sync later)

---

## Quick Command Reference

```powershell
# Start Laravel server (if not using Laragon)
php artisan serve

# Start ngrok tunnel
ngrok http 80

# Clear Laravel caches
php artisan config:clear; php artisan route:clear; php artisan cache:clear

# Verify storage symlink
php artisan storage:link

# List routes
php artisan route:list

# Check QR codes directory
ls storage/app/public/qr-codes/

# Test database connection
php artisan tinker
>>> \App\Models\EventRegistration::where('approved_at', '!=', null)->first()
```

---

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check ngrok web interface: http://127.0.0.1:4040 (shows all requests)
3. Inspect network tab in browser DevTools (F12)
4. Verify database changes: `php artisan tinker` → query EventRegistration model

---

**Happy Testing! 🎉**
