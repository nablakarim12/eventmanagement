# 📊 Evaluation Results → Presentation Approval Workflow

## 🎯 Overview

After reviewers/jury members submit their scores, Event Organizers can view evaluation results and directly approve or reject participants for presentations - all within the **Evaluation Results** section.

---

## 🔄 Complete Workflow

### Step 1: Reviewers Submit Scores
- Jury members access their assigned papers via **Jury Mapping**
- They score each paper using the rubric (categories + items)
- Scores are stored in `rubric_item_scores` table

### Step 2: Organizer Views Evaluation Results
1. Login at `/organizer/login`
2. Navigate to **"Evaluation Results"** in sidebar
3. Select the conference event
4. View list of all papers with evaluation counts

### Step 3: Review Paper Details
1. Click on a paper to see detailed scores
2. View individual jury evaluations
3. See breakdown by category and rubric items
4. See **Evaluation Summary** with:
   - Total Evaluators count
   - **Average Score** (calculated from all reviewers)
   - Score Range (min - max)

### Step 4: Make Presentation Decision
At the bottom of paper details page, you'll see:

#### ⏳ If Decision Pending:
- **Approve for Presentation** (green button)
- **Reject Presentation** (red button)

#### ✅ If Already Approved:
- Shows green success card
- Displays: Queue #, Time, Location, Meeting Link

#### ❌ If Already Rejected:
- Shows red rejection card
- Displays: Rejection reason

---

## ✅ How to APPROVE for Presentation

### Click "Approve for Presentation" Button

**Modal Opens with Fields:**

1. **Queue Number** (Optional)
   - Presentation order: 1, 2, 3...
   - Example: Enter `1` for first presenter

2. **Presentation Time** (Optional)
   - Date and time of presentation
   - Example: Jan 15, 2025 10:00 AM

3. **Meeting Link** (Optional - for Online/Hybrid events)
   - Zoom, Google Meet, or other platform link
   - Example: `https://zoom.us/j/123456789`

4. **Location** (Optional - for On-site/Hybrid events)
   - Physical venue location
   - Example: `Room 301, Main Hall`

**Click "✓ Approve & Send Notification"**

### What Happens:
1. ✅ `presentation_status` set to `'selected'` in `event_registrations`
2. 📊 `average_score` calculated and saved
3. 🔢 Queue, time, link, location saved (if provided)
4. ✉️ **Email sent** to participant with all details
5. 🔔 **Dashboard notification** created for participant
6. ✅ Success message: "Participant approved for presentation! Notification sent."

---

## ❌ How to REJECT Presentation

### Click "Reject Presentation" Button

**Modal Opens with Field:**

1. **Rejection Reason** (Required)
   - Provide constructive feedback
   - Example: "Thank you for your submission. While your work shows promise, the evaluation committee felt that the research methodology needs further development. We encourage you to strengthen this area and consider submitting to future conferences."

**Click "✗ Reject & Send Notification"**

### What Happens:
1. ❌ `presentation_status` set to `'rejected'` in `event_registrations`
2. 📊 `average_score` calculated and saved
3. 💬 `rejection_reason` saved
4. ✉️ **Email sent** to participant with feedback
5. 🔔 **Dashboard notification** created for participant
6. ✅ Success message: "Participant notified of rejection."

---

## 📧 Email Notifications

### For APPROVED Participants:
**Subject:** 🎉 Congratulations! Your Presentation Has Been Selected

**Content:**
- Event name & date
- Average score from reviewers
- Presentation time (if set)
- Queue number (if set)
- Meeting link (if online/hybrid)
- Location (if on-site/hybrid)
- "View Dashboard" action button

### For REJECTED Participants:
**Subject:** Paper Review Results - [Event Name]

**Content:**
- Professional rejection message
- Average score received
- Detailed feedback/reason
- Encouragement for future submissions
- "View Dashboard" action button

---

## 🔔 Dashboard Notifications

Both approved and rejected participants receive **database notifications** that appear in their dashboard with the same information as the email.

---

## 📍 URL Structure

| Action | URL | Method |
|--------|-----|--------|
| View All Events | `/organizer/evaluation-results` | GET |
| View Event Papers | `/organizer/evaluation-results/event/{event}` | GET |
| View Paper Details | `/organizer/evaluation-results/event/{event}/paper/{paperId}` | GET |
| **Approve Presentation** | `/organizer/evaluation-results/event/{event}/paper/{paperId}/approve` | POST |
| **Reject Presentation** | `/organizer/evaluation-results/event/{event}/paper/{paperId}/reject` | POST |

---

## 💾 Database Changes

### `event_registrations` Table Updated:

| Column | Type | Description |
|--------|------|-------------|
| `presentation_status` | enum | `'selected'`, `'rejected'`, or `NULL` |
| `presentation_queue` | integer | Presentation order: 1, 2, 3... |
| `presentation_time` | datetime | When participant presents |
| `presentation_link` | varchar | Meeting link for online events |
| `presentation_location` | varchar | Physical location for on-site |
| `average_score` | decimal(5,2) | Calculated from rubric scores |
| `rejection_reason` | text | Feedback for rejected participants |
| `presentation_notified_at` | timestamp | When email was sent |

---

## 🎬 Real Example

### Event: Asia-Pacific Conference on Educational Technology (APCET 2025)

**Paper:** "AI-Driven Learning Analytics for Personalized Education"  
**Author:** Aisyah Rahman  
**Reviewers:** 2 jury members  
**Scores:**
- Reviewer 1: 90/100
- Reviewer 2: 94/100
- **Average: 92.0**

**Organizer Decision:**
1. Goes to **Evaluation Results** → APCET 2025
2. Clicks on Aisyah's paper
3. Reviews detailed scores (sees 92.0 average)
4. Clicks **"Approve for Presentation"**
5. Enters:
   - Queue: `1`
   - Time: `Jan 15, 2025 10:00 AM`
   - Link: `https://zoom.us/j/123456789`
   - Location: `Room 301` (hybrid event)
6. Clicks **"✓ Approve & Send Notification"**

**Result:**
- ✉️ Aisyah receives email: "Congratulations! You scored 92.0 and are scheduled for Queue #1 at 10:00 AM in Room 301 with Zoom link..."
- 🔔 Dashboard notification appears
- ✅ Organizer sees green success card on paper details

---

## 🆚 vs Previous Approach

| Previous | New Approach |
|----------|--------------|
| Separate "Presentation Selection" page | Integrated into Evaluation Results |
| Had to navigate to different section | All in one workflow |
| Conference Management menu | Direct from paper details |
| View scores → Go elsewhere → Approve | View scores → Approve immediately |

---

## ✨ Key Features

1. **Seamless Integration**: Approve/reject right after reviewing scores
2. **Context Awareness**: See average score when making decision
3. **Delivery Mode Smart**: Shows link for online, location for on-site, both for hybrid
4. **Dual Notifications**: Email + Dashboard automatically
5. **Immediate Feedback**: Success/error messages
6. **Status Persistence**: Can't accidentally re-approve/re-reject

---

## 🎯 Best Practices

1. **Review All Scores First**: Make sure all jury members submitted evaluations
2. **Set Clear Threshold**: Decide minimum score for approval (e.g., ≥70)
3. **Provide Constructive Feedback**: When rejecting, give helpful comments
4. **Assign Queue Early**: Help participants plan their schedule
5. **Set Presentation Times**: Avoid scheduling conflicts
6. **Include Meeting Links**: For online/hybrid, always provide link
7. **Specify Locations**: For on-site/hybrid, give clear venue details

---

## 🔧 Technical Details

### Controller Methods:

**`approvePresentation(Request $request, Event $event, $paperId)`**
- Validates input fields
- Gets paper and registration
- Calculates average score from `rubric_item_scores`
- Updates `event_registrations` table
- Sends `PresentationSelectedNotification`
- Returns with success message

**`rejectPresentation(Request $request, Event $event, $paperId)`**
- Validates rejection reason (required)
- Gets paper and registration
- Calculates average score
- Updates `event_registrations` with rejection
- Sends `PresentationRejectedNotification`
- Returns with success message

### Notification Classes:

**`PresentationSelectedNotification`**
- Implements `ShouldQueue` for async
- Channels: `['mail', 'database']`
- Email includes all presentation details
- Database stores JSON data for dashboard

**`PresentationRejectedNotification`**
- Implements `ShouldQueue` for async
- Channels: `['mail', 'database']`
- Professional rejection message
- Includes feedback and encouragement

---

## 📊 Status Badges

| Status | Color | Icon | Meaning |
|--------|-------|------|---------|
| `NULL` | Yellow | ⏳ | Awaiting Decision |
| `selected` | Green | ✓ | Approved for Presentation |
| `rejected` | Red | ✗ | Not Selected |

---

## 🚀 Quick Start

1. ✅ Make sure migrations ran: `php artisan migrate`
2. ✅ Reviewers submit scores via Jury Mapping
3. ✅ Go to **Evaluation Results** in sidebar
4. ✅ Select your conference event
5. ✅ Click on a participant's paper
6. ✅ Review the scores at the bottom
7. ✅ Click **"Approve for Presentation"** or **"Reject Presentation"**
8. ✅ Fill in the details
9. ✅ Click confirm
10. ✅ Participant receives email + dashboard notification automatically!

---

## 🎓 That's It!

Everything you need to approve/reject presentations is now integrated directly into the Evaluation Results workflow. No separate pages, no extra navigation - just review scores and make decisions instantly!
