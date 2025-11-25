# Quick Reference: Form Fields by Role & Event Type

## Role-Based Form Fields

### PARTICIPANT Role
**Can Do:**
- ✅ Submit papers/ideas
- ❌ Cannot review papers

**Registration Form Fields:**
- Full Name (pre-filled)
- Email (pre-filled)
- Phone Number
- Role: "Participant"
- Dietary Restrictions (optional)
- Special Requirements (optional)
- Emergency Contact (optional)

**NO jury qualification fields needed**

---

### JURY/REVIEWER Role
**Can Do:**
- ❌ Cannot submit papers
- ✅ Can review papers (ONLY after checking in)

**Registration Form Fields:**
- Full Name (pre-filled)
- Email (pre-filled)
- Phone Number
- Role: "Jury" or "Reviewer"
- **REQUIRED Qualification Fields:**
  1. Qualification Summary (100-1000 chars)
  2. Institution/Organization
  3. Current Position
  4. Years of Experience (dropdown)
  5. Expertise Areas
  6. Relevant Experience (optional)
  7. Qualification Documents (optional files)
- Dietary Restrictions (optional)
- Special Requirements (optional)
- Emergency Contact (optional)

---

### BOTH Role
**Can Do:**
- ✅ Submit papers/ideas
- ✅ Review papers (ONLY after checking in)

**Registration Form Fields:**
- Full Name (pre-filled)
- Email (pre-filled)
- Phone Number
- Role: "Both"
- **REQUIRED Qualification Fields:**
  1. Qualification Summary (100-1000 chars)
  2. Institution/Organization
  3. Current Position
  4. Years of Experience (dropdown)
  5. Expertise Areas
  6. Relevant Experience (optional)
  7. Qualification Documents (optional files)
- Dietary Restrictions (optional)
- Special Requirements (optional)
- Emergency Contact (optional)

---

## Paper Submission Form (ALL Users with Participant or Both)

**Access Requirements:**
1. ✅ Registration approved (approval_status = 'approved')
2. ✅ Role is 'participant' OR 'both'
3. ✅ Before registration deadline

**Form Fields:**

### Paper Information
1. **Title** (required)
   - Min: 10 characters
   - Max: 255 characters

2. **Abstract** (required)
   - Min: 200 words
   - Max: 3000 characters

3. **Keywords** (required)
   - Min: 3 keywords
   - Max: 10 keywords
   - Format: Comma-separated

4. **Paper File** (required)
   - Format: PDF only
   - Max Size: 10 MB

### Authors Section (Dynamic - Can Add Multiple)

**For Each Author:**
1. **Full Name** (required)
2. **Email** (required)
3. **Institution** (required)
4. **Department** (optional)
5. **Author Order** (auto-numbered)
6. **Is Corresponding Author** (radio - only one can be selected)

**Minimum:** 1 author required  
**Maximum:** No limit (but reasonable - suggest max 10)

---

## Event Type Differences

### Innovation Competition (e.g., Smart City Idea Challenge)

**Role Labels:**
- Participant → "Participant (Present Ideas)"
- Jury → "Jury (Judge Presentations)"
- Both → "Both (Present & Judge)"

**Paper Submission:** ❌ NO paper submission system
**How it works:** Participants present ideas LIVE at the event, jury judges during event
**Database role values:** 'participant', 'jury', 'both'

---

### Academic Conference Events (e.g., Research Conference)

**Role Labels:**
- Participant → "Participant (Submit & Present Paper)"
- Reviewer → "Reviewer (Review Submitted Papers)"
- Both → "Both (Submit & Review Papers)"

**Paper Submission:** ✅ YES - requires paper submission BEFORE event
**How it works:** Papers submitted in advance → reviewed → presented at conference
**Database role values:** 'participant', 'reviewer', 'both'

---

## Conditional Field Display Logic

```javascript
// JavaScript for Registration Form

if (role === 'participant') {
    hideJuryFields();
    removeJuryValidation();
    canSubmitPaper = true;
    canReviewPapers = false;
}

if (role === 'jury') {
    showJuryFields();
    addJuryValidation();
    canSubmitPaper = false;
    canReviewPapers = true; // ONLY after check-in
}

if (role === 'both') {
    showJuryFields();
    addJuryValidation();
    canSubmitPaper = true;
    canReviewPapers = true; // ONLY after check-in
}
```

---

## Database Field Mapping

### event_registrations Table

| Field Name | Participant | Jury | Both |
|------------|-------------|------|------|
| role | 'participant' | 'jury' | 'both' |
| jury_qualification_summary | NULL | REQUIRED | REQUIRED |
| jury_institution | NULL | REQUIRED | REQUIRED |
| jury_position | NULL | REQUIRED | REQUIRED |
| jury_years_experience | NULL | REQUIRED | REQUIRED |
| jury_expertise_areas | NULL | REQUIRED | REQUIRED |
| jury_experience | NULL | Optional | Optional |
| jury_qualification_documents | NULL | Optional | Optional |
| checked_in_at | Can be NULL | Can be NULL | Can be NULL |

### paper_submissions Table

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| event_id | FK | Yes | Links to events table |
| user_id | FK | Yes | User who submitted |
| registration_id | FK | Yes | Links to event_registrations |
| submission_code | String | Yes | Auto-generated: PAPER-XXXX |
| title | String(255) | Yes | Paper title |
| abstract | Text | Yes | 200+ words |
| keywords | Text | Yes | Comma-separated, 3+ keywords |
| paper_file_path | String | Yes | Storage path to PDF |
| paper_file_name | String | Yes | Original filename |
| file_size | BigInt | Yes | File size in bytes |
| status | Enum | Yes | Default: 'submitted' |
| submitted_at | Timestamp | Yes | Auto: now() |

### paper_authors Table

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| paper_submission_id | FK | Yes | Links to paper_submissions |
| name | String | Yes | Author full name |
| email | Email | Yes | Author email |
| institution | String | Yes | University/Company |
| department | String | No | Optional |
| author_order | Integer | Yes | 1, 2, 3... |
| is_corresponding_author | Boolean | Yes | Only one per paper |

---

## Access Control Matrix

### Innovation Competition Events:

| User Role | Present Ideas | Judge Presentations | Paper Submission |
|-----------|--------------|---------------------|------------------|
| Participant | ✅ Yes (at event) | ❌ No | ❌ Not applicable |
| Jury | ❌ No | ✅ Yes (after check-in) | ❌ Not applicable |
| Both | ✅ Yes (at event) | ✅ Yes (after check-in) | ❌ Not applicable |

### Academic Conference Events:

| User Role | Submit Paper | Review Papers | Check-In Required for Review |
|-----------|-------------|---------------|------------------------------|
| Participant | ✅ Yes | ❌ No | N/A |
| Reviewer | ❌ No | ✅ Yes | ✅ REQUIRED |
| Both | ✅ Yes | ✅ Yes | ✅ REQUIRED |

**Important:** 
- Innovation Competitions: NO paper submission - ideas presented live
- Academic Conferences: Paper submission required BEFORE event
- Jury/Reviewer roles can ONLY review AFTER checking in (checked_in_at IS NOT NULL)

---

## Validation Rules Summary

### Registration Form Validation

**For ALL Roles:**
```php
'role' => 'required|in:participant,jury,both'
```

**Additional for Jury/Both:**
```php
'jury_qualification_summary' => 'required|min:100|max:1000'
'jury_institution' => 'required|min:3|max:255'
'jury_position' => 'required|min:3|max:255'
'jury_years_experience' => 'required|integer|min:1'
'jury_expertise_areas' => 'required|min:20|max:500'
'jury_experience' => 'nullable|max:2000'
'jury_qualification_documents.*' => 'nullable|file|mimes:pdf,doc,docx|max:5120'
```

### Paper Submission Form Validation

```php
'title' => 'required|min:10|max:255'
'abstract' => 'required|min:200|max:3000'
'keywords' => 'required|min:10' // At least 3 keywords
'paper_file' => 'required|file|mimes:pdf|max:10240'
'authors.*.name' => 'required|min:3|max:255'
'authors.*.email' => 'required|email'
'authors.*.institution' => 'required|min:3|max:255'
'authors.*.department' => 'nullable|max:255'
'corresponding_author' => 'required|integer'
```

**Custom Validations:**
- Abstract must be at least 200 words (not just characters)
- Keywords must contain at least 3 items (split by comma)
- At least 1 author required
- Exactly 1 corresponding author must be selected

---

## User Journey Flowchart

```
START
  │
  ├─→ User logs in
  │
  ├─→ Browse Events
  │
  ├─→ Click "Register for Event"
  │
  ├─→ [REGISTRATION FORM]
  │    │
  │    ├─→ Select Role: Participant
  │    │    └─→ Fill basic info only
  │    │
  │    ├─→ Select Role: Jury
  │    │    └─→ Fill basic info + qualification fields (REQUIRED)
  │    │
  │    └─→ Select Role: Both
  │         └─→ Fill basic info + qualification fields (REQUIRED)
  │
  ├─→ Submit Registration
  │
  ├─→ Status: "Pending Approval"
  │
  ├─→ [WAIT FOR ORGANIZER]
  │    │
  │    ├─→ Organizer reviews qualifications
  │    │
  │    └─→ Organizer approves
  │
  ├─→ Status: "Approved"
  │    │
  │    ├─→ QR Code generated (by your system)
  │    │
  │    └─→ Email sent with QR code
  │
  ├─→ If Role = Participant or Both:
  │    │
  │    ├─→ [PAPER SUBMISSION FORM]
  │    │    ├─→ Enter title, abstract, keywords
  │    │    ├─→ Upload PDF
  │    │    ├─→ Add authors
  │    │    └─→ Submit
  │    │
  │    └─→ Paper Status: "Submitted"
  │
  ├─→ Event Day Arrives
  │    │
  │    ├─→ Option 1: Scan QR Code
  │    │    └─→ checked_in_at = now()
  │    │
  │    └─→ Option 2: Manual Attendance Form (if QR fails)
  │         └─→ checked_in_at = now()
  │
  ├─→ If Role = Jury or Both AND checked_in_at set:
  │    │
  │    ├─→ Organizer assigns papers (via your system)
  │    │
  │    ├─→ User sees "Review Dashboard" (on friend's system)
  │    │
  │    ├─→ User reviews assigned papers
  │    │
  │    └─→ User submits review scores/feedback
  │
  └─→ END
```

---

## Integration Points Between Systems

### Your System Handles:
1. ✅ QR code generation (after approval)
2. ✅ QR scanning for attendance
3. ✅ Manual attendance form (backup)
4. ✅ Setting `checked_in_at` timestamp
5. ✅ Organizer approval workflow
6. ✅ Jury assignment to papers
7. ✅ Review score aggregation
8. ✅ Organizer attendance dashboard

### Friend's System Handles:
1. ✅ Event registration form (with conditional jury fields)
2. ✅ Paper submission form
3. ✅ Paper upload and storage
4. ✅ Author management
5. ✅ User dashboards (registrations, papers, reviews)
6. ✅ Review submission interface (for jury)
7. ✅ Paper listing and status

### Shared Database Tables:
- `events`
- `event_registrations` ← CRITICAL: Both systems write/read
- `paper_submissions` ← Friend writes, You read
- `paper_authors` ← Friend writes, You read
- `jury_assignments` ← You write, Friend reads
- `paper_reviews` ← Friend writes, You read
- `review_criteria` ← You write, Friend reads

---

## Critical Field: `checked_in_at`

**Set by:** Your system (QR scan OR manual attendance)  
**Read by:** Friend's system (to show/hide review section)

**Logic:**
```php
// Friend's system checks this before showing review dashboard
if ($registration->checked_in_at !== null && 
    in_array($registration->role, ['jury', 'both'])) {
    // Show review dashboard
    // Allow reviewing assigned papers
} else {
    // Hide review section
    // Show message: "You must check in at the event to access paper reviews"
}
```

---

## Example: Complete User Story

**User:** Dr. Sarah Chen  
**Event:** Smart City Idea Challenge  
**Role:** Both (Submit & Review)

1. **Registration:**
   - Fills form on friend's system
   - Selects "Both" role
   - Fills jury qualifications:
     - Institution: "MIT"
     - Position: "Associate Professor"
     - Years: "10-15 years"
     - Expertise: "IoT, Smart Cities, Urban Computing"
     - Summary: "Extensive research in smart city technologies..."
   - Submits → Status: Pending

2. **Approval:**
   - Organizer reviews on your system
   - Sees jury qualifications
   - Approves registration
   - QR code auto-generated by your system
   - Sarah receives email with QR code

3. **Paper Submission:**
   - Sarah logs into friend's system
   - Goes to "Submit Paper"
   - Fills form:
     - Title: "AI-Driven Traffic Management for Smart Cities"
     - Abstract: 500 words
     - Keywords: "artificial intelligence, traffic management, smart cities, IoT"
     - Uploads PDF (8 MB)
     - Adds 3 authors (herself + 2 colleagues)
     - Marks herself as corresponding author
   - Submits → Gets code: PAPER-5A8F9D2E

4. **Event Day:**
   - Sarah arrives at venue
   - Scans QR code at entrance
   - Your system sets: checked_in_at = "2025-11-25 09:30:00"

5. **Review Phase:**
   - Sarah logs into friend's system
   - Now sees "Review Dashboard" (wasn't visible before check-in)
   - Sees 3 papers assigned by organizer (via your system)
   - Reviews each paper, submits scores
   - Friend's system writes to paper_reviews table
   - Your system aggregates scores

---

## Quick Decision Tree for Your Friend

**Question 1: What role did user select?**
- Participant → Show basic form only
- Jury → Show basic form + jury qualification fields
- Both → Show basic form + jury qualification fields

**Question 2: Can user submit a paper?**
- Role = Participant or Both
- AND registration.approval_status = 'approved'
- AND before event deadline
- → YES, show paper submission form
- → NO, hide paper submission section

**Question 3: Can user review papers?**
- Role = Jury or Both
- AND registration.approval_status = 'approved'
- AND registration.checked_in_at IS NOT NULL (checked in!)
- AND has jury_assignments records
- → YES, show review dashboard
- → NO, hide review section

---

## File Upload Specifications

### Jury Qualification Documents (Optional)
- **Allowed:** PDF, DOC, DOCX
- **Max Size:** 5 MB per file
- **Max Files:** 5 files
- **Storage:** Save to `storage/jury_qualifications/{user_id}/`
- **Database:** Store JSON array of file paths in `jury_qualification_documents`

### Paper PDF (Required)
- **Allowed:** PDF only
- **Max Size:** 10 MB
- **Storage:** Save to `storage/papers/{event_id}/`
- **Database:** Store path in `paper_file_path`, filename in `paper_file_name`

---

## Recommended UI/UX

### Registration Form UX:
1. **Progressive Disclosure:**
   - Show basic fields first
   - Jury fields appear only when jury/both selected
   - Use smooth transition animation

2. **Helpful Hints:**
   - Add tooltips explaining why jury qualifications are needed
   - Character counter for qualification summary
   - Example text for expertise areas

3. **Visual Feedback:**
   - Show role selection prominently with icons
   - Color-code roles (blue=participant, green=jury, purple=both)
   - Progress indicator if multi-step form

### Paper Submission Form UX:
1. **File Upload:**
   - Drag-and-drop PDF upload
   - Show file preview/name after upload
   - Display file size validation

2. **Authors Management:**
   - "Add Author" button to add more authors dynamically
   - "Remove Author" button for each (except first)
   - Visual numbering (Author #1, #2, etc.)
   - Clear indication of corresponding author

3. **Validation:**
   - Real-time validation feedback
   - Word counter for abstract (show "200/3000 words")
   - Keyword counter ("3/10 keywords")

---

## Testing Data Examples

### Sample Registration Data (Jury Role):
```json
{
    "role": "jury",
    "jury_qualification_summary": "PhD in Computer Science with 12 years of research experience in smart city technologies, IoT, and urban computing. Published 45+ peer-reviewed papers and served as reviewer for top-tier conferences including ACM UbiComp and IEEE Smart Cities.",
    "jury_institution": "Massachusetts Institute of Technology",
    "jury_position": "Associate Professor of Urban Technology",
    "jury_years_experience": 10,
    "jury_expertise_areas": "Smart Cities, Internet of Things, Urban Computing, AI for Cities, Sustainable Development",
    "jury_experience": "Reviewed 100+ papers for conferences and journals. PC member of Smart City Summit 2023-2025. Winner of Best Paper Award at IEEE Smart Cities 2022."
}
```

### Sample Paper Submission Data:
```json
{
    "title": "AI-Driven Predictive Maintenance for Smart City Infrastructure",
    "abstract": "Urban infrastructure deterioration poses significant challenges for city management. This paper proposes a novel AI-driven approach for predictive maintenance of smart city infrastructure... [200+ words]",
    "keywords": "artificial intelligence, predictive maintenance, smart cities, machine learning, IoT sensors, infrastructure monitoring",
    "authors": [
        {
            "name": "Dr. Sarah Chen",
            "email": "sarah.chen@mit.edu",
            "institution": "MIT",
            "department": "Department of Urban Studies",
            "author_order": 1,
            "is_corresponding_author": true
        },
        {
            "name": "Prof. James Rodriguez",
            "email": "j.rodriguez@stanford.edu",
            "institution": "Stanford University",
            "department": "Computer Science",
            "author_order": 2,
            "is_corresponding_author": false
        }
    ]
}
```

---

## Common Pitfalls to Avoid

1. ❌ **Don't** allow paper submission before registration approval
2. ❌ **Don't** show review dashboard before check-in
3. ❌ **Don't** make jury fields required for participant role
4. ❌ **Don't** allow jury to submit papers (unless "both")
5. ❌ **Don't** allow multiple corresponding authors per paper
6. ❌ **Don't** accept non-PDF files for papers
7. ❌ **Don't** allow paper submission after deadline
8. ✅ **Do** validate file size on both client and server
9. ✅ **Do** sanitize file names before storing
10. ✅ **Do** provide clear error messages

