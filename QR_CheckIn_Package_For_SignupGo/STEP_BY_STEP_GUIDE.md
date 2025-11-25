# Step-by-Step Installation Guide
## For Non-Technical Users

### 📋 What You'll Need
- Access to SignupGo project folder
- Basic knowledge of copy/paste files
- 15 minutes of time

---

## Step 1: Copy the Package Folder
⏱️ Time: 2 minutes

1. **Find this folder on your computer:**
   ```
   C:\laragon\www\eventmanagement\QR_CheckIn_Package_For_SignupGo
   ```

2. **Copy the ENTIRE folder** to your desktop (for easy access)

3. You should now have this on your desktop:
   ```
   Desktop\QR_CheckIn_Package_For_SignupGo
   ```

✅ **Success Check:** You can see the folder on your desktop

---

## Step 2: Copy Controller File
⏱️ Time: 2 minutes

1. **Open File Explorer** and go to:
   ```
   Desktop\QR_CheckIn_Package_For_SignupGo\app\Http\Controllers
   ```

2. **Copy this file:**
   ```
   QrCheckInController.php
   ```

3. **Navigate to SignupGo project:**
   ```
   C:\laragon\www\signupgo\app\Http\Controllers
   ```

4. **Paste the file** there

✅ **Success Check:** File `QrCheckInController.php` is now in `signupgo/app/Http/Controllers/`

---

## Step 3: Copy QR Views
⏱️ Time: 3 minutes

1. **Go to package folder:**
   ```
   Desktop\QR_CheckIn_Package_For_SignupGo\resources\views
   ```

2. **Copy the ENTIRE `qr` folder**

3. **Navigate to SignupGo views:**
   ```
   C:\laragon\www\signupgo\resources\views
   ```

4. **Paste the `qr` folder** there

✅ **Success Check:** You should now have `signupgo/resources/views/qr/` with 4 files inside:
   - check-in.blade.php
   - invalid.blade.php
   - not-approved.blade.php
   - too-early.blade.php

---

## Step 4: Copy Dashboard Views
⏱️ Time: 3 minutes

1. **Go to package folder:**
   ```
   Desktop\QR_CheckIn_Package_For_SignupGo\resources\views\dashboard
   ```

2. **Copy the ENTIRE `registrations` folder**

3. **Navigate to SignupGo dashboard views:**
   ```
   C:\laragon\www\signupgo\resources\views\dashboard
   ```
   
   **Note:** If `dashboard` folder doesn't exist, create it first!

4. **Paste the `registrations` folder** there

✅ **Success Check:** You should now have `signupgo/resources/views/dashboard/registrations/` with 2 files:
   - index.blade.php
   - show.blade.php

---

## Step 5: Add Routes
⏱️ Time: 3 minutes

1. **Open file in text editor:**
   ```
   C:\laragon\www\signupgo\routes\web.php
   ```

2. **Scroll to the bottom** of the file (but before the last `?>` if there is one)

3. **Open this file for reference:**
   ```
   Desktop\QR_CheckIn_Package_For_SignupGo\routes_to_add.txt
   ```

4. **Copy ALL the code** from `routes_to_add.txt`

5. **Paste it** at the bottom of `web.php`

6. **Save the file** (Ctrl + S)

✅ **Success Check:** The file `web.php` now has the new routes at the bottom

---

## Step 6: Update EventRegistration Model
⏱️ Time: 2 minutes

1. **Open file:**
   ```
   C:\laragon\www\signupgo\app\Models\EventRegistration.php
   ```

2. **Find the `$fillable` array** (looks like this:)
   ```php
   protected $fillable = [
       'event_id',
       'user_id',
       // ... more fields ...
   ];
   ```

3. **Add these 3 lines** inside the array:
   ```php
   'qr_code',
   'qr_image_path',
   'checked_in_at',
   ```

4. **Save the file**

✅ **Success Check:** The 3 new fields are in the `$fillable` array

---

## Step 7: Clear Caches
⏱️ Time: 1 minute

1. **Open PowerShell** (or Command Prompt)

2. **Navigate to SignupGo:**
   ```powershell
   cd C:\laragon\www\signupgo
   ```

3. **Run these commands ONE BY ONE:**
   ```powershell
   php artisan route:clear
   php artisan view:clear
   php artisan config:clear
   php artisan cache:clear
   ```

4. You should see "success" messages for each

✅ **Success Check:** All commands ran without errors

---

## Step 8: Test the Installation
⏱️ Time: 3 minutes

### Test 1: Check Routes

In PowerShell, run:
```powershell
php artisan route:list | findstr "check-in"
```

✅ **You should see:**
```
GET     check-in/{qrCode}
POST    check-in/{qrCode}
```

### Test 2: Access Registrations Page

1. **Open browser**
2. **Go to:** `http://signupgo.test/dashboard/registrations`
3. **Login if needed** (use a test user account)

✅ **You should see:** List of registrations (or "No registrations" message)

### Test 3: View QR Code

1. **Click on any approved registration** from the list
2. **Scroll down** to see the QR code

✅ **You should see:** 
   - Registration details
   - QR code image (if approved)
   - "Download QR" link
   - Check-in status

### Test 4: Test Check-In

1. **Click "Test QR Code"** button (or copy the check-in URL)
2. **New page opens** showing event details
3. **Click "Confirm Check-In"** button
4. **Wait for success message**

✅ **You should see:** "Successfully checked in!" message

---

## 🎉 Installation Complete!

Your users can now:
- ✅ View their event registrations
- ✅ See QR codes for approved events  
- ✅ Download QR codes
- ✅ Check in at events
- ✅ Track check-in status

---

## ❌ Troubleshooting

### Problem: "Page not found (404)"

**Solution:**
```powershell
cd C:\laragon\www\signupgo
php artisan route:clear
```

### Problem: "QR code image not showing"

**Solution:**
```powershell
cd C:\laragon\www\signupgo
php artisan storage:link
```

### Problem: "Controller not found"

**Solution:**
- Check if `QrCheckInController.php` is in `app/Http/Controllers/` folder
- Make sure you copied it correctly
- Run: `php artisan clear-compiled`

### Problem: "View not found"

**Solution:**
- Check if views are in `resources/views/qr/` folder
- Check if views are in `resources/views/dashboard/registrations/` folder
- Run: `php artisan view:clear`

---

## 📞 Need More Help?

Check the full guide: `INSTALLATION_GUIDE.md`

---

**Created by:** EventManagement Team  
**Date:** November 23, 2025  
**Version:** 1.0
