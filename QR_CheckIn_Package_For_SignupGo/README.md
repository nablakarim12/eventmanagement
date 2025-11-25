# QR Code Check-In Package - Quick Start

## 📦 What's Inside

All files needed for your friend to enable QR code check-in on SignupGo user portal.

## 🚀 Quick Installation (For Your Friend)

### Method 1: Automated Copy (PowerShell)

Run these commands in PowerShell (from SignupGo project folder):

```powershell
# Navigate to SignupGo
cd C:\laragon\www\signupgo

# Copy controller
Copy-Item "C:\laragon\www\eventmanagement\QR_CheckIn_Package_For_SignupGo\app\Http\Controllers\QrCheckInController.php" -Destination "app\Http\Controllers\" -Force

# Copy QR views
New-Item -ItemType Directory -Path "resources\views\qr" -Force
Copy-Item "C:\laragon\www\eventmanagement\QR_CheckIn_Package_For_SignupGo\resources\views\qr\*" -Destination "resources\views\qr\" -Recurse -Force

# Copy dashboard views
New-Item -ItemType Directory -Path "resources\views\dashboard\registrations" -Force
Copy-Item "C:\laragon\www\eventmanagement\QR_CheckIn_Package_For_SignupGo\resources\views\dashboard\registrations\*" -Destination "resources\views\dashboard\registrations\" -Recurse -Force

# Done!
echo "✅ Files copied successfully!"
```

### Method 2: Manual Copy (File Explorer)

1. Copy entire `QR_CheckIn_Package_For_SignupGo` folder to your friend's computer
2. Manually copy files from package to SignupGo project:
   - `app/Http/Controllers/QrCheckInController.php` → `signupgo/app/Http/Controllers/`
   - `resources/views/qr/*` → `signupgo/resources/views/qr/`
   - `resources/views/dashboard/registrations/*` → `signupgo/resources/views/dashboard/registrations/`

### After Copying Files:

1. **Add routes** (see `routes_to_add.txt`)
2. **Clear caches:**
   ```powershell
   php artisan route:clear
   php artisan view:clear
   php artisan config:clear
   ```

3. **Test:**
   - Visit: `http://signupgo.test/dashboard/registrations`
   - Click on approved registration
   - See QR code!

## 📖 Full Instructions

See `INSTALLATION_GUIDE.md` for:
- Detailed step-by-step installation
- Troubleshooting guide
- Testing instructions
- Phone testing with ngrok
- Customization options

## ⚡ Quick Test URLs

After installation, test these URLs:

- **Registrations List:** `http://signupgo.test/dashboard/registrations`
- **QR Check-In:** `http://signupgo.test/check-in/REG-BYFVRQK0AR0D` (Smart City - Mantul)
- **QR Check-In:** `http://signupgo.test/check-in/REG-JCWRB6SIJUJY` (Smart City - Ahmad)
- **QR Check-In:** `http://signupgo.test/check-in/REG-XEHQFYOOQLI1` (Smart City - Qixi)

## 📁 Package Structure

```
QR_CheckIn_Package_For_SignupGo/
├── app/Http/Controllers/
│   └── QrCheckInController.php           # Main controller
├── resources/views/
│   ├── qr/                                # Check-in pages
│   │   ├── check-in.blade.php
│   │   ├── invalid.blade.php
│   │   ├── not-approved.blade.php
│   │   └── too-early.blade.php
│   └── dashboard/registrations/           # User dashboard
│       ├── index.blade.php               # List registrations
│       └── show.blade.php                # Show QR code
├── INSTALLATION_GUIDE.md                  # Full guide
├── routes_to_add.txt                      # Routes to add
└── README.md                              # This file
```

## ✅ Requirements

- Laravel 10.x
- Shared database with EventManagement (Supabase)
- User authentication system
- QR codes already generated (when organizer approves)

## 🎯 What Users Will See

1. **My Registrations** - List of all events they registered for
2. **QR Code View** - See unique QR code for approved events
3. **Download QR** - Download QR as PNG image
4. **Check-In Page** - Scan QR to check in at event
5. **Check-In Status** - See if already checked in with timestamp

## 💡 Tips

- Make sure storage symlink exists: `php artisan storage:link`
- QR codes are auto-generated when you (organizer) approve registrations
- No database changes needed - columns already exist
- Works with same database you share with EventManagement

---

**Need help?** Check `INSTALLATION_GUIDE.md` for troubleshooting and detailed instructions.
