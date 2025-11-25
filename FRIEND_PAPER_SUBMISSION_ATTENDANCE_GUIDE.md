# Paper Submission & Attendance Form Guide (For Friend's System)

## Overview
This guide is for **your friend** who handles the paper submission UI. It covers what forms need to be created for different event types and user roles.

---

## Event Types & User Roles

### 1. Innovation Competition Events (e.g., Smart City Idea Challenge)
**User Roles Available:**
- **Participant** - Presents ideas/pitches at event (NO paper submission)
- **Jury** - Judges presentations and ideas at event
- **Both** - Presents ideas AND judges others' presentations

**Note:** Innovation competitions do NOT have paper submissions. Participants present their ideas live at the event, and jury members evaluate them during the event.

### 2. Academic Conference Events (e.g., Research Conferences)
**User Roles Available:**
- **Participant** - Submits and presents research papers
- **Reviewer** - Reviews submitted papers before conference
- **Both** - Submits papers AND reviews others' papers

**Note:** Academic conferences REQUIRE paper submissions. Papers are submitted in advance, reviewed, and then presented at the conference.

---

## Database Structure (Already Shared)

### `event_registrations` Table
```sql
- id
- event_id
- user_id
- registration_code (unique, e.g., REG-ABC123)
- role (enum: 'participant', 'jury', 'both')
- approval_status (enum: 'pending', 'approved', 'rejected')
- checked_in_at (timestamp, set when QR scanned OR manual check-in)
- qr_code (unique code for QR)
- qr_image_path (path to QR image)

-- Jury-specific fields (only for jury/both roles)
- jury_qualification_summary (text)
- jury_qualification_documents (JSON/text)
- jury_experience (text)
- jury_expertise_areas (text)
- jury_institution (varchar)
- jury_position (varchar)
- jury_years_experience (integer)
```

### `paper_submissions` Table
```sql
- id
- event_id
- user_id
- registration_id (links to event_registrations)
- submission_code (unique, e.g., PAPER-ABC123)
- title
- abstract
- keywords
- paper_file_path
- paper_file_name
- file_size
- status (draft/submitted/under_review/reviewed/accepted/rejected)
- average_score (calculated from reviews)
- review_count
- submitted_at
- reviewed_at
```

### `paper_authors` Table
```sql
- id
- paper_submission_id
- name
- email
- institution
- department
- is_corresponding_author (boolean)
- author_order (integer)
```

---

## Forms Your Friend Needs to Create

### FORM 1: Event Registration Form
**Purpose:** Initial registration for an event  
**When:** User first signs up for an event  
**Access:** Available to all logged-in users

#### Form Fields:

**Basic Information (Pre-filled from user account):**
- Full Name (from users.name)
- Email (from users.email)
- Phone Number (from users.phone if exists)

**Role Selection (REQUIRED):**
```html
<select name="role" required>
    <option value="">Select Your Role</option>
    
    @if($event->category->name === 'Innovation Competition')
        <!-- For Innovation Competitions (NO paper submission) -->
        <option value="participant">Participant (Present Ideas)</option>
        <option value="jury">Jury (Judge Presentations)</option>
        <option value="both">Both (Present & Judge)</option>
    @elseif($event->category->name === 'Academic Conference')
        <!-- For Academic Conferences (WITH paper submission) -->
        <option value="participant">Participant (Submit & Present Paper)</option>
        <option value="reviewer">Reviewer (Review Submitted Papers)</option>
        <option value="both">Both (Submit & Review Papers)</option>
    @endif
</select>
```

**Important Notes:**
- Innovation Competitions use `jury` role (judges live presentations at event)
- Academic Conferences use `reviewer` role (reviews submitted papers before event)
- Both event types use the same database field (`role`), just different terminology

**Conditional Fields (Show ONLY if role = 'jury' OR 'both'):**

1. **Qualification Summary** (Required)
```html
<textarea name="jury_qualification_summary" required>
Placeholder: "Please summarize your qualifications and expertise relevant to reviewing papers in this field..."
</textarea>
Validation: Min 100 characters, Max 1000 characters
```

2. **Institution** (Required)
```html
<input type="text" name="jury_institution" required>
Placeholder: "University name, Company name, Research institute..."
Validation: Min 3 characters
```

3. **Current Position** (Required)
```html
<input type="text" name="jury_position" required>
Placeholder: "e.g., Professor, Senior Researcher, Industry Expert..."
Validation: Min 3 characters
```

4. **Years of Experience** (Required)
```html
<select name="jury_years_experience" required>
    <option value="">Select Experience Level</option>
    <option value="1">Less than 2 years</option>
    <option value="2">2-5 years</option>
    <option value="5">5-10 years</option>
    <option value="10">10-15 years</option>
    <option value="15">15+ years</option>
</select>
```

5. **Expertise Areas** (Required)
```html
<textarea name="jury_expertise_areas" required>
Placeholder: "List your areas of expertise (comma-separated): 
e.g., Machine Learning, IoT, Smart Cities, Sustainable Development..."
</textarea>
Validation: Min 20 characters
```

6. **Experience Description** (Optional)
```html
<textarea name="jury_experience">
Placeholder: "Describe your relevant experience (papers reviewed, conferences attended, awards, publications, etc.)"
</textarea>
Validation: Max 2000 characters
```

7. **Qualification Documents** (Optional)
```html
<input type="file" name="jury_qualification_documents[]" multiple accept=".pdf,.doc,.docx">
<p>Upload CV, certificates, or proof of expertise (optional, max 5 files, 5MB each)</p>
```

**For ALL Roles (Optional):**
- Dietary Restrictions
- Special Requirements
- Emergency Contact Name
- Emergency Contact Phone

#### Form Submission Logic:
```javascript
// When form submitted
if (role === 'jury' || role === 'both') {
    // Validate all jury fields are filled
    if (!jury_qualification_summary || jury_qualification_summary.length < 100) {
        return error('Please provide detailed qualification summary (min 100 characters)');
    }
    if (!jury_institution || !jury_position || !jury_years_experience || !jury_expertise_areas) {
        return error('All jury qualification fields are required');
    }
}

// Submit to database
INSERT INTO event_registrations (
    event_id, user_id, role, approval_status,
    jury_qualification_summary, jury_institution, 
    jury_position, jury_years_experience, 
    jury_expertise_areas, jury_experience,
    ...other fields
) VALUES (
    $event_id, $user_id, $role, 'pending',
    $jury_qualification_summary, $jury_institution,
    $jury_position, $jury_years_experience,
    $jury_expertise_areas, $jury_experience,
    ...
);

// Auto-generate registration_code: REG-{8_random_chars}
// Set approval_status = 'pending' (organizer will approve)
```

---

### FORM 2: Paper Submission Form
**Purpose:** Submit research paper for academic conference  
**When:** After user registration is approved (approval_status = 'approved')  
**Access:** Only users with role = 'participant' OR 'both'  
**Condition:** Must be registered and approved for the event  
**Event Type:** ONLY for Academic Conference events (NOT for Innovation Competitions)

**IMPORTANT:** This form should ONLY be shown for Academic Conference events. Innovation Competition events do NOT have paper submissions - participants present their ideas live at the event instead.

#### Pre-submission Check:
```php
// Before showing form, verify:
// 1. Check if event is Academic Conference (has paper submissions)
$event = Event::find($event_id);
if ($event->category->name !== 'Academic Conference') {
    return redirect()->back()->with('error', 
        'Paper submission is only available for Academic Conference events');
}

// 2. Check user registration
$registration = EventRegistration::where('event_id', $event_id)
    ->where('user_id', $user_id)
    ->where('approval_status', 'approved')
    ->whereIn('role', ['participant', 'both'])
    ->first();

if (!$registration) {
    return redirect()->back()->with('error', 
        'You must be registered and approved as a participant to submit papers');
}
```

#### Form Fields:

**Paper Information:**

1. **Paper Title** (Required)
```html
<input type="text" name="title" required maxlength="255">
Placeholder: "Enter your paper/idea title"
Validation: Min 10 characters, Max 255 characters
```

2. **Abstract** (Required)
```html
<textarea name="abstract" required rows="10">
Placeholder: "Provide a concise summary of your paper (200-500 words)..."
</textarea>
Validation: Min 200 words, Max 3000 characters
```

3. **Keywords** (Required)
```html
<input type="text" name="keywords" required>
Placeholder: "Enter keywords separated by commas (min 3, max 10)"
Example: "smart city, IoT, sustainability, urban planning"
Validation: Min 3 keywords
```

4. **Paper File Upload** (Required)
```html
<input type="file" name="paper_file" accept=".pdf" required>
<p class="help-text">
    - Format: PDF only
    - Maximum size: 10 MB
    - Recommended: Include title page, abstract, main content, references
</p>
Validation: 
- Must be PDF
- Max 10MB
- File should be readable
```

**Authors Information (Dynamic - Add Multiple Authors):**

```html
<div id="authors-section">
    <h3>Authors</h3>
    <p>Add all authors of this paper (including yourself)</p>
    
    <div class="author-entry" data-author="1">
        <h4>Author #1</h4>
        
        <input type="text" name="authors[0][name]" required 
               placeholder="Full Name">
        
        <input type="email" name="authors[0][email]" required 
               placeholder="Email Address">
        
        <input type="text" name="authors[0][institution]" required 
               placeholder="Institution/University">
        
        <input type="text" name="authors[0][department]" 
               placeholder="Department (optional)">
        
        <input type="hidden" name="authors[0][author_order]" value="1">
        
        <label>
            <input type="radio" name="corresponding_author" value="0" required>
            Corresponding Author
        </label>
    </div>
    
    <button type="button" id="add-author">+ Add Another Author</button>
</div>

<script>
let authorCount = 1;
document.getElementById('add-author').addEventListener('click', function() {
    authorCount++;
    // Clone author entry and update indices
    // ... (implementation for adding authors dynamically)
});
</script>
```

**Author Fields (for each author):**
- Full Name (required)
- Email (required)
- Institution (required)
- Department (optional)
- Author Order (auto-numbered)
- Is Corresponding Author (radio button, only one can be selected)

#### Form Validation:
```javascript
// Client-side validation
if (!title || title.length < 10) {
    return error('Title must be at least 10 characters');
}

if (!abstract || abstract.split(' ').length < 200) {
    return error('Abstract must be at least 200 words');
}

let keywordArray = keywords.split(',').map(k => k.trim()).filter(k => k);
if (keywordArray.length < 3) {
    return error('Please provide at least 3 keywords');
}

if (!paperFile || paperFile.type !== 'application/pdf') {
    return error('Only PDF files are accepted');
}

if (paperFile.size > 10 * 1024 * 1024) { // 10MB
    return error('File size must not exceed 10 MB');
}

// At least one author required
if (authors.length < 1) {
    return error('Please add at least one author');
}

// Must have one corresponding author
let correspondingCount = authors.filter(a => a.is_corresponding_author).length;
if (correspondingCount !== 1) {
    return error('Please select exactly one corresponding author');
}
```

#### Form Submission Logic:
```php
// Server-side processing
DB::transaction(function() use ($request, $event_id, $user_id, $registration) {
    // 1. Upload PDF file
    $paperPath = $request->file('paper_file')->store('papers', 'public');
    
    // 2. Generate submission code
    $submissionCode = 'PAPER-' . strtoupper(Str::random(12));
    
    // 3. Create paper submission
    $paper = PaperSubmission::create([
        'event_id' => $event_id,
        'user_id' => $user_id,
        'registration_id' => $registration->id,
        'submission_code' => $submissionCode,
        'title' => $request->title,
        'abstract' => $request->abstract,
        'keywords' => $request->keywords,
        'paper_file_path' => $paperPath,
        'paper_file_name' => $request->file('paper_file')->getClientOriginalName(),
        'file_size' => $request->file('paper_file')->getSize(),
        'status' => 'submitted',
        'submitted_at' => now(),
    ]);
    
    // 4. Create authors
    foreach ($request->authors as $index => $authorData) {
        PaperAuthor::create([
            'paper_submission_id' => $paper->id,
            'name' => $authorData['name'],
            'email' => $authorData['email'],
            'institution' => $authorData['institution'],
            'department' => $authorData['department'] ?? null,
            'author_order' => $index + 1,
            'is_corresponding_author' => $request->corresponding_author == $index,
        ]);
    }
});

return redirect()->route('dashboard.papers.show', $paper)
    ->with('success', 'Paper submitted successfully! Submission Code: ' . $submissionCode);
```

---

### FORM 3: Attendance Check-In Form (Optional - If QR Fails)

**Note:** Your system already handles this through the manual attendance form. Your friend doesn't need to create this - it's on the user dashboard side only.

**However**, if your friend wants to add a "Check-In Status" display on their paper submission dashboard:

#### Display Check-In Status on Paper Dashboard:
```html
<div class="registration-status">
    <h3>Event Registration Status</h3>
    
    <div class="status-item">
        <label>Registration Code:</label>
        <span class="code">{{ $registration->registration_code }}</span>
    </div>
    
    <div class="status-item">
        <label>Role:</label>
        <span class="badge badge-{{ $registration->role }}">
            {{ ucfirst($registration->role) }}
        </span>
    </div>
    
    <div class="status-item">
        <label>Approval Status:</label>
        @if($registration->approval_status === 'approved')
            <span class="badge badge-success">✓ Approved</span>
        @elseif($registration->approval_status === 'rejected')
            <span class="badge badge-danger">✗ Rejected</span>
        @else
            <span class="badge badge-warning">⏱ Pending Approval</span>
        @endif
    </div>
    
    <div class="status-item">
        <label>Check-In Status:</label>
        @if($registration->checked_in_at)
            <span class="badge badge-success">
                ✓ Checked In ({{ $registration->checked_in_at->format('M d, Y h:i A') }})
            </span>
        @else
            <span class="badge badge-secondary">
                Not Checked In Yet
            </span>
            <p class="help-text">
                You can check in using QR code or manual form on event day
            </p>
        @endif
    </div>
    
    <!-- If role includes 'jury' and checked in -->
    @if(in_array($registration->role, ['jury', 'both']) && $registration->checked_in_at)
    <div class="status-item">
        <label>Review Status:</label>
        <span class="badge badge-info">
            ✓ Available for Paper Assignment
        </span>
        <p class="help-text">
            Organizers can now assign papers to you for review
        </p>
    </div>
    @endif
</div>
```

---

## Form Flow Diagrams

### For Innovation Competitions (e.g., Smart City Idea Challenge)

```
User Actions:
1. Browse Events → Select "Smart City Idea Challenge"
2. Click "Register" → Fill Registration Form
   ├─ Select Role: Participant/Jury/Both
   ├─ If Jury/Both: Fill qualification fields
   └─ Submit → Status: "Pending Approval"

3. [Wait for Organizer Approval]
   ├─ Organizer reviews qualifications
   └─ Approves → Status: "Approved"

4. If Role = Participant or Both:
   ├─ Go to "Submit Paper" section
   ├─ Fill Paper Submission Form
   │   ├─ Title, Abstract, Keywords
   │   ├─ Upload PDF
   │   └─ Add Authors
   └─ Submit → Paper Status: "Submitted"

5. Event Day:
   ├─ Scan QR Code → checked_in_at set
   └─ OR use Manual Attendance Form → checked_in_at set

6. If Role = Jury or Both AND Checked In:
   ├─ Organizer assigns papers to review
   ├─ Jury reviews papers (on your friend's system)
   └─ Submits scores/feedback
```

### For Conferences (e.g., Academic Conference)

```
User Actions:
1. Browse Events → Select Conference
2. Click "Register" → Fill Registration Form
   ├─ Select Role: Participant/Reviewer/Both
   ├─ If Reviewer/Both: Fill qualification fields
   └─ Submit → Status: "Pending Approval"

3. [Wait for Organizer Approval]
   └─ Approves → Status: "Approved"

4. If Role = Participant or Both:
   ├─ Submit Paper for Conference
   └─ Paper Status: "Submitted"

5. Conference Day:
   ├─ Check in via QR or Manual Form
   └─ checked_in_at timestamp set

6. If Role = Reviewer or Both AND Checked In:
   └─ Access assigned papers for review
```

---

## Conditional Logic Summary

### When to Show Paper Submission Form:
```php
// Show ONLY if ALL conditions met:
1. Event category = 'Academic Conference' (NOT Innovation Competition)
2. User has EventRegistration for this event
3. registration.approval_status = 'approved'
4. registration.role IN ('participant', 'both')
5. Current date <= event.registration_deadline (if set)

// Example:
if ($event->category->name === 'Academic Conference' && 
    $registration && 
    $registration->approval_status === 'approved' && 
    in_array($registration->role, ['participant', 'both']) && 
    (!$event->registration_deadline || now()->lte($event->registration_deadline))) {
    // Show paper submission form
}
```

### When to Show Jury Qualification Fields:
```javascript
// Show ONLY if:
role === 'jury' OR role === 'both'

// Make required if shown
```

### When User Can Review Papers:
```php
// User can access paper review section ONLY if ALL met:
1. registration.role IN ('jury', 'both')
2. registration.approval_status = 'approved'
3. registration.checked_in_at IS NOT NULL (must check in first!)
4. Has JuryAssignment records (organizer assigned papers)
```

---

## Recommended Form Layout (HTML Structure)

### Registration Form Layout:
```html
<form action="/events/{event}/register" method="POST" enctype="multipart/form-data">
    @csrf
    
    <!-- Section 1: Basic Info -->
    <section class="form-section">
        <h2>Basic Information</h2>
        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" value="{{ auth()->user()->name }}" readonly>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="{{ auth()->user()->email }}" readonly>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" name="phone" value="{{ auth()->user()->phone }}">
        </div>
    </section>
    
    <!-- Section 2: Role Selection -->
    <section class="form-section">
        <h2>Registration Role *</h2>
        <div class="form-group">
            <select name="role" id="role-select" required>
                <option value="">-- Select Your Role --</option>
                <option value="participant">Participant (Submit Paper/Idea)</option>
                <option value="jury">{{ $event->category->name == 'Conference' ? 'Reviewer' : 'Jury' }} (Review Papers)</option>
                <option value="both">Both (Submit & Review)</option>
            </select>
            <p class="help-text">
                Choose carefully - this determines your permissions for this event
            </p>
        </div>
    </section>
    
    <!-- Section 3: Jury Qualifications (Show/Hide based on role) -->
    <section class="form-section" id="jury-section" style="display: none;">
        <h2>Qualification Details (Required for Jury/Reviewer)</h2>
        
        <div class="alert alert-info">
            <strong>Why do we need this?</strong><br>
            To ensure fair and quality reviews, we need to verify your expertise.
            Your qualifications will be reviewed by organizers before approval.
        </div>
        
        <div class="form-group">
            <label>Qualification Summary *</label>
            <textarea name="jury_qualification_summary" rows="5" 
                      placeholder="Describe your qualifications, expertise, and why you're suitable to review papers for this event (min 100 characters)"></textarea>
            <span class="char-count">0 / 1000</span>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Institution/Organization *</label>
                <input type="text" name="jury_institution" 
                       placeholder="e.g., Stanford University, Google Inc.">
            </div>
            <div class="form-group col-md-6">
                <label>Current Position *</label>
                <input type="text" name="jury_position" 
                       placeholder="e.g., Associate Professor, Senior Engineer">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Years of Experience *</label>
                <select name="jury_years_experience">
                    <option value="">Select...</option>
                    <option value="1">Less than 2 years</option>
                    <option value="2">2-5 years</option>
                    <option value="5">5-10 years</option>
                    <option value="10">10-15 years</option>
                    <option value="15">15+ years</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>Expertise Areas *</label>
                <input type="text" name="jury_expertise_areas" 
                       placeholder="e.g., AI, IoT, Sustainability, Urban Planning">
            </div>
        </div>
        
        <div class="form-group">
            <label>Relevant Experience (Optional)</label>
            <textarea name="jury_experience" rows="4" 
                      placeholder="Papers reviewed, conferences attended, publications, awards, etc."></textarea>
        </div>
        
        <div class="form-group">
            <label>Supporting Documents (Optional)</label>
            <input type="file" name="jury_qualification_documents[]" multiple 
                   accept=".pdf,.doc,.docx">
            <p class="help-text">Upload CV, certificates, or proof of expertise (Max 5 files, 5MB each)</p>
        </div>
    </section>
    
    <!-- Section 4: Additional Information -->
    <section class="form-section">
        <h2>Additional Information (Optional)</h2>
        <div class="form-group">
            <label>Dietary Restrictions</label>
            <input type="text" name="dietary_restrictions">
        </div>
        <div class="form-group">
            <label>Special Requirements</label>
            <textarea name="special_requirements" rows="2"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Emergency Contact Name</label>
                <input type="text" name="emergency_contact_name">
            </div>
            <div class="form-group col-md-6">
                <label>Emergency Contact Phone</label>
                <input type="tel" name="emergency_contact_phone">
            </div>
        </div>
    </section>
    
    <!-- Submit Button -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg">
            Complete Registration
        </button>
        <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">
            Cancel
        </a>
    </div>
</form>

<script>
// Show/hide jury section based on role selection
document.getElementById('role-select').addEventListener('change', function() {
    const jurySection = document.getElementById('jury-section');
    const juryFields = jurySection.querySelectorAll('input, textarea, select');
    
    if (this.value === 'jury' || this.value === 'both') {
        jurySection.style.display = 'block';
        // Make fields required
        juryFields.forEach(field => {
            if (field.name !== 'jury_experience' && field.name !== 'jury_qualification_documents[]') {
                field.setAttribute('required', 'required');
            }
        });
    } else {
        jurySection.style.display = 'none';
        // Remove required attribute
        juryFields.forEach(field => {
            field.removeAttribute('required');
        });
    }
});

// Character counter for qualification summary
const summaryField = document.querySelector('[name="jury_qualification_summary"]');
const charCount = document.querySelector('.char-count');
summaryField.addEventListener('input', function() {
    charCount.textContent = this.value.length + ' / 1000';
});
</script>
```

---

## Error Handling & Validation Messages

### Registration Form Errors:
```php
// Server-side validation
$rules = [
    'role' => 'required|in:participant,jury,both',
];

if (in_array($request->role, ['jury', 'both'])) {
    $rules = array_merge($rules, [
        'jury_qualification_summary' => 'required|min:100|max:1000',
        'jury_institution' => 'required|min:3|max:255',
        'jury_position' => 'required|min:3|max:255',
        'jury_years_experience' => 'required|integer|min:1',
        'jury_expertise_areas' => 'required|min:20|max:500',
        'jury_experience' => 'nullable|max:2000',
        'jury_qualification_documents.*' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
    ]);
}

$validator = Validator::make($request->all(), $rules);

if ($validator->fails()) {
    return back()->withErrors($validator)->withInput();
}
```

### Paper Submission Form Errors:
```php
$rules = [
    'title' => 'required|min:10|max:255',
    'abstract' => 'required|min:200|max:3000',
    'keywords' => 'required|min:10',
    'paper_file' => 'required|file|mimes:pdf|max:10240', // 10MB
    'authors.*.name' => 'required|min:3|max:255',
    'authors.*.email' => 'required|email',
    'authors.*.institution' => 'required|min:3|max:255',
    'authors.*.department' => 'nullable|max:255',
    'corresponding_author' => 'required|integer',
];

// Custom validation
$validator = Validator::make($request->all(), $rules);

$validator->after(function ($validator) use ($request) {
    // Check keyword count
    $keywords = array_filter(explode(',', $request->keywords));
    if (count($keywords) < 3) {
        $validator->errors()->add('keywords', 'Please provide at least 3 keywords');
    }
    
    // Check at least one author
    if (!$request->authors || count($request->authors) < 1) {
        $validator->errors()->add('authors', 'Please add at least one author');
    }
    
    // Verify word count in abstract
    $wordCount = str_word_count($request->abstract);
    if ($wordCount < 200) {
        $validator->errors()->add('abstract', "Abstract must be at least 200 words (current: {$wordCount})");
    }
});
```

---

## User Notifications

### After Registration Submission:
```php
// Email to user
Mail::to($user->email)->send(new RegistrationPendingMail([
    'event' => $event,
    'registration_code' => $registration->registration_code,
    'role' => $registration->role,
    'message' => 'Your registration is pending approval. You will receive an email once the organizer reviews your application.'
]));

// If role includes jury
if (in_array($registration->role, ['jury', 'both'])) {
    // Additional message
    $message .= ' Your qualification details are being reviewed to ensure quality paper assessment.';
}
```

### After Organizer Approval:
```php
// Email to user
Mail::to($user->email)->send(new RegistrationApprovedMail([
    'event' => $event,
    'registration' => $registration,
    'qr_code_path' => $registration->qr_image_path,
    'next_steps' => in_array($registration->role, ['participant', 'both']) 
        ? 'You can now submit your paper for this event.'
        : 'Papers will be assigned to you for review after event check-in.',
]));
```

### After Paper Submission:
```php
Mail::to($user->email)->send(new PaperSubmittedMail([
    'paper' => $paper,
    'submission_code' => $paper->submission_code,
    'message' => 'Your paper has been successfully submitted. You will be notified once the review process begins.'
]));
```

---

## Dashboard Views Your Friend Should Create

### 1. Registration Dashboard
**URL:** `/dashboard/registrations` or `/my-registrations`

**Show:**
- List of all user's event registrations
- For each registration:
  - Event name
  - Registration code
  - Role badge
  - Approval status badge
  - Check-in status
  - Actions: View Details, Cancel (if not checked in)

### 2. Paper Submissions Dashboard
**URL:** `/dashboard/papers` or `/my-papers`

**Show:**
- List of all submitted papers
- For each paper:
  - Submission code
  - Title
  - Event name
  - Status badge
  - Submission date
  - Review status (if under review)
  - Actions: View, Edit (if draft), Download PDF

### 3. Review Dashboard (For Jury/Reviewers)
**URL:** `/dashboard/reviews` or `/my-reviews`

**Show ONLY if:**
- User has role = 'jury' or 'both'
- User is checked in (checked_in_at IS NOT NULL)

**Show:**
- List of assigned papers
- For each assignment:
  - Paper title
  - Submission code
  - Assigned date
  - Review deadline
  - Review status
  - Actions: View Paper, Submit Review

---

## API Endpoints Your Friend Might Need

If your friend is building a separate frontend (React, Vue, etc.):

### GET `/api/events/{event}/registration-form`
**Returns:** Registration form configuration based on event type

### POST `/api/events/{event}/register`
**Accepts:** Registration data with conditional jury fields

### GET `/api/my-registrations`
**Returns:** User's event registrations with check-in status

### POST `/api/events/{event}/submit-paper`
**Accepts:** Paper data + file upload + authors array

### GET `/api/my-papers`
**Returns:** User's paper submissions

### GET `/api/my-reviews`
**Returns:** Papers assigned for review (only if checked in)

---

## Testing Checklist for Your Friend

### Registration Form Testing:
- [ ] Participant-only registration works
- [ ] Jury-only registration requires qualification fields
- [ ] Both role registration requires qualification fields
- [ ] Qualification fields are hidden when participant selected
- [ ] File upload for jury documents works (optional field)
- [ ] Character counter works for qualification summary
- [ ] Validation prevents submission with missing jury fields
- [ ] Successful submission shows pending status

### Paper Submission Testing:
- [ ] Form only accessible to approved participants/both
- [ ] PDF upload works and validates file type
- [ ] File size validation works (max 10MB)
- [ ] Can add multiple authors dynamically
- [ ] Must select exactly one corresponding author
- [ ] Keyword validation requires minimum 3 keywords
- [ ] Abstract word count validation works
- [ ] Submission generates unique PAPER-XXXX code
- [ ] Paper appears in "My Papers" dashboard

### Role-Based Access Testing:
- [ ] Participant can submit papers, cannot review
- [ ] Jury cannot submit papers, can review (after check-in)
- [ ] Both can submit AND review (after check-in)
- [ ] Review section hidden before check-in
- [ ] Review section appears after check-in

---

## Summary for Your Friend

**What your friend needs to build:**

1. **Event Registration Form** with:
   - Role selection (participant/jury/both)
   - Conditional jury qualification fields
   - Validation logic

2. **Paper Submission Form** with:
   - Title, abstract, keywords
   - PDF upload
   - Dynamic author management
   - Access control (only approved participants)

3. **Three Dashboard Views**:
   - Registrations list
   - Papers list
   - Reviews list (conditional)

4. **Database Inserts** to shared tables:
   - `event_registrations` (with jury fields if applicable)
   - `paper_submissions`
   - `paper_authors`

**What your system handles:**
- QR code generation (after approval)
- QR scanning for check-in
- Manual attendance form (backup)
- Jury assignment to papers
- Review score aggregation
- Organizer approval workflow

**Shared Data:**
- Same database tables
- `checked_in_at` determines if jury can review
- `approval_status` determines if user can submit papers
- `role` field determines permissions

