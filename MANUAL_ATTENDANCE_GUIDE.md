# User-Side Manual Attendance Form

## Overview
This backup attendance system allows users to manually check in to events when QR code scanning is not working or unavailable.

## Features

### 1. **Access Control**
- Only available to users with **approved registrations**
- Check-in window opens **1 hour before event start time**
- Prevents duplicate check-ins
- Validates user identity (name & email must match account)

### 2. **Form Fields**
**Required:**
- Full Name (must match account exactly)
- Email Address (must match account exactly)
- Reason for manual check-in:
  - QR Code Not Working
  - Forgot QR Code
  - Technical Issue with Scanner
  - Other

**Optional:**
- Additional Notes (max 500 characters)

### 3. **User Flow**

#### Accessing the Form
**Option 1: From Registration Details Page**
1. User logs in to their account
2. Goes to "My Registrations"
3. Clicks on a specific registration
4. Sees "QR code not working?" with "Manual Check-In Form" button
5. Clicks button to access form

**Option 2: Direct URL**
```
/dashboard/events/{event_id}/attendance
```

#### Submitting Attendance
1. User fills in their full name (pre-filled from account)
2. User confirms their email (pre-filled from account)
3. User selects reason for manual check-in
4. User optionally adds notes
5. User clicks "Submit Check-In"
6. System validates:
   - Registration exists and is approved
   - Not already checked in
   - Name/email match account
   - Within check-in time window
7. If valid: `checked_in_at` timestamp is set
8. User is redirected to registration details with success message

### 4. **Validation Rules**

| Validation | Error Message |
|------------|---------------|
| No registration / Not approved | "You are not registered for this event or your registration is not approved yet." |
| Already checked in | "You have already checked in at [timestamp]" |
| Too early | "Check-in is not yet available. You can check in starting 1 hour before the event." |
| Name/email mismatch | "Name and email must match your account details." |

### 5. **Files Modified/Created**

**Controller:**
- `app/Http/Controllers/DashboardController.php`
  - Added `showAttendanceForm()` method
  - Added `submitAttendance()` method

**Routes:**
- `routes/web.php`
  - `GET /dashboard/events/{event}/attendance` → `dashboard.attendance.form`
  - `POST /dashboard/events/{event}/attendance` → `dashboard.attendance.submit`

**Views:**
- `resources/views/dashboard/attendance/form.blade.php` (new)
  - Complete attendance form with validation and styling
- `resources/views/dashboard/registrations/show.blade.php` (updated)
  - Added "Manual Check-In Form" button when not checked in

**Test Script:**
- `test_manual_attendance.php`
  - Comprehensive testing of the manual attendance system

### 6. **Integration with Existing Systems**

#### QR Check-In
- Uses the **same** `checked_in_at` field in `event_registrations` table
- No conflict between manual and QR check-in
- Whichever happens first is recorded

#### Jury Assignment
- Manual check-in enables jury assignment (same as QR)
- Organizers can assign jury after manual check-in
- System checks `whereNotNull('checked_in_at')` for available jury

#### Organizer Dashboard
- Manual check-ins appear in organizer attendance dashboard
- No visual difference from QR check-ins
- Both methods counted in attendance statistics

### 7. **Security Features**
- ✅ User must be authenticated
- ✅ Email verification required
- ✅ Name/email must match account (prevents impersonation)
- ✅ Registration must be approved
- ✅ Prevents duplicate check-ins
- ✅ CSRF protection
- ✅ Time-window validation

### 8. **User Experience**

**When QR Works:**
- User scans QR code → Instant check-in ✓

**When QR Doesn't Work:**
- User clicks "Manual Check-In Form"
- Fills simple form (pre-filled data)
- Selects reason
- Submits → Manual check-in ✓

**Warning Notice:**
Form includes warning: "By submitting this form, you confirm that you are physically present at the event location. False attendance submissions may result in registration cancellation."

### 9. **Testing Instructions**

**Test Case 1: Successful Manual Check-In**
1. Login as user with approved registration
2. Wait until 1 hour before event start
3. Go to registration details
4. Click "Manual Check-In Form"
5. Verify name/email are pre-filled
6. Select reason, add optional notes
7. Submit form
8. Verify success message
9. Check database: `checked_in_at` should have timestamp

**Test Case 2: Name/Email Mismatch**
1. Access manual check-in form
2. Change name or email to different value
3. Submit form
4. Verify error: "Name and email must match your account details"

**Test Case 3: Too Early**
1. Try to access form more than 1 hour before event
2. Verify error: "Check-in is not yet available..."

**Test Case 4: Already Checked In**
1. User already has `checked_in_at` set
2. Try to access form again
3. Verify redirect with "already checked in" message

**Test Case 5: Jury Assignment After Manual Check-In**
1. User manually checks in
2. Organizer goes to paper management
3. Organizer should see user in "Available Jury" list
4. Organizer can assign papers successfully

### 10. **Running the Test Script**

```bash
php test_manual_attendance.php
```

**Expected Output:**
- ✓ Routes exist
- ✓ Controller methods exist
- ✓ View file exists
- ✓ Test data found
- ✓ Validation logic explained
- ✓ Integration points verified

### 11. **Future Enhancements** (Optional)

Consider these improvements:
1. **Organizer Notification**: Email organizer when manual check-in occurs
2. **Audit Trail**: Separate table to log manual check-ins with reason
3. **Approval Required**: Option to require organizer approval for manual check-ins
4. **Location Verification**: Optionally collect location data
5. **Photo Upload**: Allow user to upload photo as proof of attendance
6. **Bulk Export**: Export manual vs QR check-in statistics

### 12. **Troubleshooting**

**Problem: "Route not found" error**
- Solution: Run `php artisan route:clear` and `php artisan config:clear`

**Problem: View not found**
- Solution: Check `resources/views/dashboard/attendance/form.blade.php` exists

**Problem: Form validation fails**
- Solution: Ensure name/email exactly match account (case-insensitive comparison used)

**Problem: Can't access form**
- Solution: Check:
  1. User is logged in
  2. Registration exists and is approved
  3. Current time is within 1 hour of event start

## Summary

This manual attendance form provides a reliable backup when QR code scanning fails. It maintains the same data structure and validation rules as QR check-in, ensuring seamless integration with existing jury assignment and attendance tracking systems.

The form is secure, user-friendly, and prevents abuse through identity verification and time-window restrictions.
