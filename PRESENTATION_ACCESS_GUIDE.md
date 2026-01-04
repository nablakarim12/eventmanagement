# 🎯 Quick Access Guide - Presentation Selection System

## 📍 How to Access All Functions

### Method 1: Direct URL
Simply visit:
```
http://eventmanagement.test/organizer/presentations/{event_id}
```

**Example:**
```
http://eventmanagement.test/organizer/presentations/41
```

Replace `{event_id}` with your conference event ID.

---

### Method 2: Navigation Menu (Recommended)

After logging in as Event Organizer:

1. **Login**: Go to `/organizer/login`
2. Look at the **left sidebar menu**
3. Find section: **"Conference Management"**
4. Click on your conference event name
5. ✅ You're now in Presentation Selection!

**Menu Structure:**
```
📊 Dashboard
📅 Events
👥 Registrations
📜 Certificates
🎭 Jury Mapping
📊 Evaluation Results

┌─ 🎓 Conference Management ─────┐
│  → Asia-Pacific Conference...  │ ← Click here!
│  → International Conference... │
│  → Global Innovation Summit... │
└────────────────────────────────┘
```

---

### Method 3: From Jury Mapping Page

1. Go to **Jury Mapping** (`/organizer/jury-mapping`)
2. Select your conference event
3. After reviewing jury mappings, manually navigate to:
   `/organizer/presentations/{event_id}`

---

## 🔗 Complete URL Structure

### Main Functions:

| Function | URL | Method |
|----------|-----|--------|
| **View All Participants with Scores** | `/organizer/presentations/{event}` | GET |
| **Select Participant** | `/organizer/presentations/{event}/participant/{participant}/select` | POST |
| **Reject Participant** | `/organizer/presentations/{event}/participant/{participant}/reject` | POST |
| **Auto-Select by Score** | `/organizer/presentations/{event}/auto-select` | POST |
| **Bulk Select Multiple** | `/organizer/presentations/{event}/bulk-select` | POST |
| **Update Details** | `/organizer/presentations/{event}/participant/{participant}/update-details` | POST |
| **Resend Notification** | `/organizer/presentations/{event}/participant/{participant}/resend-notification` | POST |

---

## 📋 Step-by-Step Workflow

### Step 1: Login
```
URL: http://eventmanagement.test/organizer/login
Credentials: Your organizer account
```

### Step 2: Navigate to Event
**Option A - Sidebar Menu:**
- Look for "Conference Management" section
- Click your event name

**Option B - Direct URL:**
```
http://eventmanagement.test/organizer/presentations/41
```

### Step 3: Review Participants
You'll see a table with:
- ✅ Participant names
- 📊 Average scores (from reviewers)
- 📈 Review status (X/Y reviews completed)
- 🎯 Current status (Pending/Selected/Rejected)
- 🔢 Queue numbers

### Step 4: Take Action

**🟢 To SELECT a Participant:**
1. Click the **edit icon** (pencil) on participant row
2. Modal opens
3. Enter **queue number** (optional, e.g., 1, 2, 3...)
4. Click **"✓ Select for Presentation"**
5. ✉️ Email sent automatically!

**🔴 To REJECT a Participant:**
1. Click the **edit icon** on participant row
2. Click **"✗ Reject"** button
3. Enter **rejection reason** (required)
4. Click confirm
5. ✉️ Email sent automatically!

**⚡ To AUTO-SELECT:**
1. Click **"Auto-Select by Score"** button at top
2. Enter minimum score (e.g., 70)
3. Check "auto-assign queue numbers"
4. Click **"Auto-Select"**
5. All participants with score ≥ 70 are selected!
6. ✉️ Emails sent to all!

**⚙️ To ADD DETAILS (for selected participants):**
1. Click the **+ icon** on selected participant
2. Enter:
   - **Queue number**: #1, #2, #3...
   - **Presentation time**: Date & time
   - **Meeting link**: (for online/hybrid events)
   - **Location**: (for on-site/hybrid events)
3. Click **"Save Details"**
4. Participant receives updated info

---

## 🎬 Real Example

**Scenario:** You have "Asia-Pacific Conference on Educational Technology (APCET 2025)" with 13 participants.

### Access:
```
1. Login: http://eventmanagement.test/organizer/login
2. Navigate: Click "Asia-Pacific Conference..." in sidebar
   OR
   Go to: http://eventmanagement.test/organizer/presentations/41
```

### You'll See:
```
┌─────────────────────────────────────────────────────────────┐
│ 📊 Presentation Selection                                   │
│ Asia-Pacific Conference on Educational Technology           │
│                                                              │
│ [13 Total] [8 Reviewed] [5 Pending] [0 Selected] [0 Reject] │
│                                                              │
│ [🚀 Auto-Select by Score]                                   │
├─────────────────────────────────────────────────────────────┤
│ # │ Participant    │ Category  │ Reviews │ Score │ Status  │
├───┼────────────────┼───────────┼─────────┼───────┼─────────┤
│ 1 │ Aisyah         │ Digital   │ 2/2     │ 92.0  │ Pending │
│ 2 │ Luqmanul       │ EdTech    │ 2/2     │ 85.0  │ Pending │
│ 3 │ Alvin Wei      │ E-Learning│ 2/2     │ 78.0  │ Pending │
│ 4 │ Kumar          │ Analytics │ 2/2     │ 55.0  │ Pending │
└─────────────────────────────────────────────────────────────┘
```

### Actions:
1. **Click "Auto-Select by Score"**
2. Enter minimum: `70`
3. Check "auto-assign queue"
4. Result: Aisyah (#1), Luqmanul (#2), Alvin (#3) selected!

### What Happens:
- ✉️ Aisyah receives email: "Congrats! Queue #1, Time: Jan 15 10:00 AM"
- ✉️ Luqmanul receives email: "Congrats! Queue #2, Time: Jan 15 10:30 AM"
- ✉️ Alvin receives email: "Congrats! Queue #3, Time: Jan 15 11:00 AM"
- ✉️ Kumar receives email: "Thank you for submission... not selected"
- 🔔 All receive dashboard notifications

---

## 🎯 Quick Actions Cheatsheet

| I want to... | Click... |
|--------------|----------|
| Select 1 participant | Edit icon → "✓ Select for Presentation" |
| Reject 1 participant | Edit icon → "✗ Reject" + reason |
| Select all with score ≥ 70 | "Auto-Select by Score" → Enter 70 |
| Add queue number | Edit icon (for selected) → Enter queue |
| Add meeting link | + icon → Enter link |
| Add location | + icon → Enter location |
| Set presentation time | + icon → Pick date/time |
| Resend email | (Future feature) |

---

## 📧 What Participants Receive

### Selected Participants Get:
1. **Email** with:
   - Congratulations message
   - Event name & date
   - Their average score
   - Queue number (#1, #2, #3...)
   - Presentation time
   - Meeting link (if online/hybrid)
   - Location (if on-site/hybrid)
   - "View Dashboard" button

2. **Dashboard Notification** with same info

### Rejected Participants Get:
1. **Email** with:
   - Professional rejection message
   - Their average score
   - Reviewer feedback/reason
   - Encouragement for future submissions
   - "View Dashboard" button

2. **Dashboard Notification** with same info

---

## 🔍 Finding Your Event ID

Not sure of your event ID?

### Method 1: Check URL
When viewing events at `/organizer/events`, hover over event name:
```
/organizer/events/41/edit  ← Event ID is 41
```

### Method 2: Database
```sql
SELECT id, title FROM events WHERE delivery_mode IS NOT NULL;
```

### Method 3: Use Sidebar
Just click the event name in "Conference Management" section - it goes to the right URL automatically!

---

## ✅ System Requirements Checklist

Before using Presentation Selection, ensure:
- [x] Conference event created
- [x] Participants registered
- [x] Participants approved (status = confirmed)
- [x] Reviewers assigned (jury mapping done)
- [x] Reviews submitted (with scores)
- [x] Notifications table created (`php artisan migrate`)

---

## 🆘 Troubleshooting

**Problem**: "No participants showing"
- **Solution**: Make sure participants are approved (status = confirmed)

**Problem**: "All scores show 'No reviews'"
- **Solution**: Reviewers haven't submitted scores yet. Check `/organizer/jury-mapping`

**Problem**: "Email not sent"
- **Solution**: Check mail configuration in `.env` file

**Problem**: "Navigation menu not showing"
- **Solution**: Make sure you have conference events (delivery_mode IS NOT NULL)

**Problem**: "Can't access /organizer/presentations/41"
- **Solution**: Replace `41` with your actual event ID

---

## 🎓 Pro Tips

1. **Use Auto-Select** for large conferences (saves time!)
2. **Set minimum score** based on quality threshold (70-80 recommended)
3. **Assign queue numbers** to organize presentation order
4. **Add meeting links early** so participants can prepare
5. **Check "reviewed" count** before selecting (ensure all reviews done)
6. **Sort by score** to see best submissions first

---

## 📞 Need Help?

If you're stuck:
1. Check this guide
2. Look at the URL examples
3. Try the sidebar navigation
4. Check that migrations ran: `php artisan migrate`
5. Verify event is conference type (has delivery_mode)

**Quick Test URL:**
```
http://eventmanagement.test/organizer/presentations/41
```
(Replace 41 with your event ID)
