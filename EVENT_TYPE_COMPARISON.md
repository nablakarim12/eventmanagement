# Event Type Comparison: Innovation vs Conference

## Critical Difference: Paper Submission

### ❌ Innovation Competition (NO Paper Submission)
- Smart City Idea Challenge
- Startup Pitch Competition
- Hackathons
- Design Challenges

**How it works:**
1. Participants register for the event
2. NO paper submission required
3. Participants prepare their ideas/pitches
4. At the event: Participants present ideas LIVE
5. Jury members attend event and judge presentations in real-time
6. Winners announced at event

**Role: JURY** (not reviewer)

---

### ✅ Academic Conference (WITH Paper Submission)
- Research Conferences
- Academic Symposiums
- Scientific Seminars
- Journal Presentations

**How it works:**
1. Participants register for the event
2. Participants MUST submit research papers (PDF) BEFORE event
3. Papers are assigned to reviewers
4. Reviewers review papers and provide scores/feedback
5. At the event: Accepted papers are presented
6. Discussion and Q&A during conference

**Role: REVIEWER** (not jury)

---

## Database Role Values

### Innovation Competition
```
role = 'participant'  → Presents ideas at event (NO paper submission)
role = 'jury'         → Judges presentations at event
role = 'both'         → Presents AND judges
```

### Academic Conference
```
role = 'participant'  → Submits paper + presents at conference
role = 'reviewer'     → Reviews submitted papers (before event)
role = 'both'         → Submits paper AND reviews others' papers
```

**Note:** Both use the SAME database field (`role`), just different values!

---

## Registration Form Differences

### Innovation Competition Registration

```html
<h2>Select Your Role</h2>
<select name="role" required>
    <option value="">-- Choose Role --</option>
    <option value="participant">Participant (Present Ideas at Event)</option>
    <option value="jury">Jury (Judge Presentations)</option>
    <option value="both">Both (Present & Judge)</option>
</select>

<!-- Jury Qualification Fields shown if jury/both selected -->
@if(role === 'jury' || role === 'both')
    <h3>Jury Qualifications</h3>
    <p>As a jury member, you will evaluate presentations at the event.</p>
    <!-- qualification fields here -->
@endif
```

### Academic Conference Registration

```html
<h2>Select Your Role</h2>
<select name="role" required>
    <option value="">-- Choose Role --</option>
    <option value="participant">Participant (Submit & Present Paper)</option>
    <option value="reviewer">Reviewer (Review Submitted Papers)</option>
    <option value="both">Both (Submit & Review Papers)</option>
</select>

<!-- Reviewer Qualification Fields shown if reviewer/both selected -->
@if(role === 'reviewer' || role === 'both')
    <h3>Reviewer Qualifications</h3>
    <p>As a reviewer, you will evaluate submitted papers before the conference.</p>
    <!-- qualification fields here -->
@endif
```

---

## Paper Submission Form Logic

### Show Paper Submission Form?

```php
// Innovation Competition - NEVER show paper submission
if ($event->category->name === 'Innovation Competition') {
    // NO paper submission form at all
    // Hide "Submit Paper" button completely
    return false;
}

// Academic Conference - Show if conditions met
if ($event->category->name === 'Academic Conference') {
    // Check if user can submit
    if ($registration->approval_status === 'approved' && 
        in_array($registration->role, ['participant', 'both'])) {
        // Show paper submission form
        return true;
    }
}

return false;
```

---

## Dashboard Differences

### Innovation Competition Dashboard

**Participant View:**
```
My Registration
├─ Registration Code: REG-ABC123
├─ Role: Participant
├─ Status: ✓ Approved
└─ Check-In Status: [QR Code or Manual Form]

Event Information
├─ Event Date & Time
├─ Venue Location
└─ Presentation Guidelines

No "Submit Paper" section!
```

**Jury View:**
```
My Registration
├─ Registration Code: REG-ABC123
├─ Role: Jury
├─ Status: ✓ Approved
└─ Check-In Status: [QR Code or Manual Form]

Review Dashboard (shown AFTER check-in)
├─ Assigned Presentations: 0
└─ Note: Presentations will be assigned at the event

No papers to review before event!
Judging happens during live presentations!
```

---

### Academic Conference Dashboard

**Participant View:**
```
My Registration
├─ Registration Code: REG-ABC123
├─ Role: Participant
├─ Status: ✓ Approved
└─ Check-In Status: [QR Code or Manual Form]

My Submitted Papers ← THIS SECTION EXISTS
├─ Paper #1: PAPER-XYZ789
│   ├─ Title: "AI in Healthcare"
│   ├─ Status: Under Review
│   ├─ Reviews: 2/3 completed
│   └─ Average Score: 8.5/10
└─ [Submit New Paper] button

Event Information
└─ Conference Schedule
```

**Reviewer View:**
```
My Registration
├─ Registration Code: REG-ABC123
├─ Role: Reviewer
├─ Status: ✓ Approved
└─ Check-In Status: [QR Code or Manual Form]

Review Dashboard (shown AFTER check-in) ← THIS SECTION EXISTS
├─ Assigned Papers: 3
│   ├─ Paper #1: PAPER-ABC123 (Pending Review)
│   ├─ Paper #2: PAPER-DEF456 (Reviewed ✓)
│   └─ Paper #3: PAPER-GHI789 (Pending Review)
└─ Review Deadline: Nov 30, 2025

Papers assigned BEFORE conference!
Reviews submitted BEFORE conference!
```

---

## Timeline Comparison

### Innovation Competition Timeline

```
Registration Opens
    ↓
User Registers (Participant/Jury/Both)
    ↓
Organizer Approves Registration
    ↓
QR Code Generated
    ↓
[NO PAPER SUBMISSION PHASE]
    ↓
Event Day Arrives
    ↓
Check-In via QR or Manual Form
    ↓
Participants Present Ideas LIVE
    ↓
Jury Judges Presentations DURING EVENT
    ↓
Winners Announced
```

### Academic Conference Timeline

```
Registration Opens
    ↓
User Registers (Participant/Reviewer/Both)
    ↓
Organizer Approves Registration
    ↓
QR Code Generated
    ↓
PAPER SUBMISSION PHASE ← IMPORTANT!
    ↓
Participants Submit Papers (PDFs)
    ↓
Organizer Assigns Papers to Reviewers
    ↓
[REVIEW PHASE - Before Conference]
    ↓
Reviewers Check-In (sets checked_in_at)
    ↓
Reviewers Review Papers & Submit Scores
    ↓
Papers Accepted/Rejected
    ↓
Conference Day Arrives
    ↓
Check-In via QR or Manual Form
    ↓
Accepted Papers Presented
    ↓
Discussion & Networking
```

---

## Database Usage Differences

### Innovation Competition

**Tables Used:**
- ✅ `events`
- ✅ `event_registrations` (with role = 'participant', 'jury', or 'both')
- ❌ `paper_submissions` (NOT USED)
- ❌ `paper_authors` (NOT USED)
- ✅ `jury_assignments` (assigned during event, not before)
- ✅ `paper_reviews` (may use for scoring presentations live)

**Note:** For innovation competitions, you might use `jury_assignments` and `paper_reviews` tables differently - to score live presentations rather than pre-submitted papers.

---

### Academic Conference

**Tables Used:**
- ✅ `events`
- ✅ `event_registrations` (with role = 'participant', 'reviewer', or 'both')
- ✅ `paper_submissions` (REQUIRED - participants submit PDFs)
- ✅ `paper_authors` (REQUIRED - paper author details)
- ✅ `jury_assignments` (renamed: should be `reviewer_assignments`)
- ✅ `paper_reviews` (reviewers submit scores BEFORE conference)

---

## Qualification Fields

### SAME for Both Event Types

Whether it's **Jury** (innovation) or **Reviewer** (conference), the qualification fields are identical:

```
jury_qualification_summary
jury_institution
jury_position
jury_years_experience
jury_expertise_areas
jury_experience
jury_qualification_documents
```

**Why same fields?**
- Database structure is shared
- Both roles require expertise verification
- Field name uses "jury" but applies to reviewers too
- Could be renamed to "evaluator_*" for clarity, but not necessary

---

## Your Friend's Implementation Checklist

### ✅ For ALL Event Types:
- [ ] Registration form with role selection
- [ ] Conditional jury/reviewer qualification fields
- [ ] User registration dashboard
- [ ] QR code display (generated by your system)
- [ ] Check-in status display

### ✅ ONLY for Academic Conferences:
- [ ] Paper submission form (with PDF upload)
- [ ] Author management (add multiple authors)
- [ ] "My Papers" dashboard
- [ ] Paper status tracking
- [ ] Review dashboard (for reviewers, after check-in)
- [ ] Review submission form

### ❌ NOT for Innovation Competitions:
- [ ] ~~Paper submission form~~
- [ ] ~~Author management~~
- [ ] ~~"My Papers" dashboard~~
- [ ] Review happens live at event, not in advance

---

## Code Example: Event Type Detection

```php
// Helper function your friend can use
function hassPaperSubmission($event) {
    // Check if event category supports paper submission
    $categoriesWithPapers = [
        'Academic Conference',
        'Research Symposium',
        'Scientific Seminar',
        // Add more as needed
    ];
    
    return in_array($event->category->name, $categoriesWithPapers);
}

// Usage in views
@if(hasPaperSubmission($event))
    <!-- Show paper submission section -->
    <a href="{{ route('papers.create', $event) }}">Submit Paper</a>
@else
    <!-- Show event participation info -->
    <p>Present your ideas at the event!</p>
@endif

// Usage in controllers
if (hasPaperSubmission($event)) {
    // Process paper submission
} else {
    return redirect()->back()->with('error', 
        'This event does not accept paper submissions');
}
```

---

## Summary

| Feature | Innovation Competition | Academic Conference |
|---------|----------------------|---------------------|
| **Paper Submission** | ❌ No | ✅ Yes (Required) |
| **Evaluation Role** | Jury | Reviewer |
| **When Judging Happens** | During event (live) | Before event (pre-review) |
| **Check-in Required for Judging** | ✅ Yes | ✅ Yes |
| **Participant Action** | Present live | Submit paper + present |
| **Forms Needed** | Registration only | Registration + Paper submission |
| **Database Tables** | Fewer (no papers) | More (with papers) |

---

## Quick Decision Tree for Your Friend

```
Question: What type of event is it?

├─ Innovation Competition
│   ├─ Show registration form with "Jury" role
│   ├─ NO paper submission form
│   ├─ Jury evaluates DURING event
│   └─ Use jury_assignments for live judging
│
└─ Academic Conference
    ├─ Show registration form with "Reviewer" role
    ├─ YES - show paper submission form
    ├─ Reviewers evaluate BEFORE event (after check-in)
    └─ Use jury_assignments + paper_reviews for pre-conference review
```

---

This clarifies the critical difference! Your friend should:

1. **Check event category** before showing paper submission
2. **Use "Jury" terminology** for Innovation Competitions
3. **Use "Reviewer" terminology** for Academic Conferences
4. **Only build paper submission system** for Academic Conferences
5. **Both types** still use same qualification fields
6. **Both types** require check-in before judging/reviewing
