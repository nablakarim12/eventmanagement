# Role Terminology & Database Schema

## Database Field: `role`

The `event_registrations.role` field stores the user's role for a specific event. The **same field** is used for both event types, but with different values:

### Innovation Competition Events
```sql
role ENUM values:
- 'participant'  -- Presents ideas at event
- 'jury'         -- Judges presentations during event
- 'both'         -- Presents AND judges
```

### Academic Conference Events
```sql
role ENUM values:
- 'participant'  -- Submits & presents papers
- 'reviewer'     -- Reviews submitted papers
- 'both'         -- Submits papers AND reviews others' papers
```

## Current Database Schema

```sql
CREATE TABLE event_registrations (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT REFERENCES events(id),
    user_id BIGINT REFERENCES users(id),
    
    -- Role field (context-dependent on event type)
    role VARCHAR(50),
    -- For Innovation: 'participant', 'jury', 'both'
    -- For Conference: 'participant', 'reviewer', 'both'
    
    approval_status ENUM('pending', 'approved', 'rejected'),
    checked_in_at TIMESTAMP NULL,
    
    -- Qualification fields (for jury/reviewer roles)
    -- Field names say "jury" but apply to reviewers too
    jury_qualification_summary TEXT,
    jury_institution VARCHAR(255),
    jury_position VARCHAR(255),
    jury_years_experience INTEGER,
    jury_expertise_areas TEXT,
    jury_experience TEXT,
    jury_qualification_documents TEXT, -- JSON array of file paths
    
    -- Other fields...
    registration_code VARCHAR(50) UNIQUE,
    qr_code VARCHAR(100) UNIQUE,
    qr_image_path VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Why "jury_*" Field Names for Both?

The qualification fields are named `jury_qualification_summary`, `jury_institution`, etc., but they're used for **both** jury (innovation) and reviewer (conference) roles because:

1. **Shared database** - Both you and your friend use same tables
2. **Same purpose** - Both roles need expertise verification
3. **Same fields** - Institution, position, experience are needed for both
4. **Historical naming** - Fields were created with "jury" prefix

**Could be renamed to `evaluator_*` for clarity, but not necessary** since the functionality is identical.

---

## Form Display Logic

### Innovation Competition - Registration Form

```html
<div class="role-selection">
    <label>Select Your Role *</label>
    <select name="role" id="role" required>
        <option value="">-- Choose Your Role --</option>
        <option value="participant">
            Participant (Present Ideas at Event)
        </option>
        <option value="jury">
            Jury (Judge Presentations During Event)
        </option>
        <option value="both">
            Both (Present Ideas & Judge Others)
        </option>
    </select>
</div>

<!-- Jury Qualifications (shown if jury or both) -->
<div id="jury-qualifications" style="display: none;">
    <h3>Jury Qualifications</h3>
    <p class="info">
        As a jury member, you will evaluate presentations during the event.
        Please provide your qualifications to help us ensure fair judging.
    </p>
    
    <label>Qualification Summary *</label>
    <textarea name="jury_qualification_summary" required></textarea>
    
    <label>Institution *</label>
    <input type="text" name="jury_institution" required>
    
    <label>Position *</label>
    <input type="text" name="jury_position" required>
    
    <!-- ... other fields ... -->
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const qualificationsDiv = document.getElementById('jury-qualifications');
    if (this.value === 'jury' || this.value === 'both') {
        qualificationsDiv.style.display = 'block';
    } else {
        qualificationsDiv.style.display = 'none';
    }
});
</script>
```

### Academic Conference - Registration Form

```html
<div class="role-selection">
    <label>Select Your Role *</label>
    <select name="role" id="role" required>
        <option value="">-- Choose Your Role --</option>
        <option value="participant">
            Participant (Submit & Present Research Paper)
        </option>
        <option value="reviewer">
            Reviewer (Review Submitted Papers Before Conference)
        </option>
        <option value="both">
            Both (Submit Paper & Review Others' Papers)
        </option>
    </select>
</div>

<!-- Reviewer Qualifications (shown if reviewer or both) -->
<!-- SAME HTML STRUCTURE, just different heading/description -->
<div id="jury-qualifications" style="display: none;">
    <h3>Reviewer Qualifications</h3>
    <p class="info">
        As a reviewer, you will evaluate submitted papers before the conference.
        Please provide your qualifications to help us assign appropriate papers.
    </p>
    
    <label>Qualification Summary *</label>
    <textarea name="jury_qualification_summary" required></textarea>
    
    <label>Institution *</label>
    <input type="text" name="jury_institution" required>
    
    <label>Position *</label>
    <input type="text" name="jury_position" required>
    
    <!-- ... other fields ... -->
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const qualificationsDiv = document.getElementById('jury-qualifications');
    // Note: checking for 'reviewer' instead of 'jury'
    if (this.value === 'reviewer' || this.value === 'both') {
        qualificationsDiv.style.display = 'block';
    } else {
        qualificationsDiv.style.display = 'none';
    }
});
</script>
```

---

## Database Queries

### Check if User is Jury/Reviewer

```php
// For Innovation Competition
$isJury = in_array($registration->role, ['jury', 'both']);

// For Academic Conference
$isReviewer = in_array($registration->role, ['reviewer', 'both']);

// Generic (works for both)
$canEvaluate = in_array($registration->role, ['jury', 'reviewer', 'both']);
```

### Check if User Can Submit Papers

```php
// Innovation Competition - NO ONE can submit papers
if ($event->category->name === 'Innovation Competition') {
    $canSubmit = false;
}

// Academic Conference - Participants and Both can submit
if ($event->category->name === 'Academic Conference') {
    $canSubmit = in_array($registration->role, ['participant', 'both']);
}
```

### Get Available Jury/Reviewers (After Check-In)

```php
// For Innovation Competition (get jury members)
$availableJury = EventRegistration::where('event_id', $eventId)
    ->whereIn('role', ['jury', 'both'])
    ->where('approval_status', 'approved')
    ->whereNotNull('checked_in_at') // Must be checked in
    ->get();

// For Academic Conference (get reviewers)
$availableReviewers = EventRegistration::where('event_id', $eventId)
    ->whereIn('role', ['reviewer', 'both'])
    ->where('approval_status', 'approved')
    ->whereNotNull('checked_in_at') // Must be checked in
    ->get();
```

---

## Validation Rules

### Registration Form Validation

```php
// Base validation (same for all)
$rules = [
    'role' => 'required|in:participant,jury,reviewer,both',
];

// Innovation Competition - add jury validation if needed
if ($event->category->name === 'Innovation Competition') {
    if (in_array($request->role, ['jury', 'both'])) {
        $rules = array_merge($rules, [
            'jury_qualification_summary' => 'required|min:100|max:1000',
            'jury_institution' => 'required|min:3|max:255',
            'jury_position' => 'required|min:3|max:255',
            'jury_years_experience' => 'required|integer|min:1',
            'jury_expertise_areas' => 'required|min:20|max:500',
        ]);
    }
}

// Academic Conference - add reviewer validation if needed
if ($event->category->name === 'Academic Conference') {
    if (in_array($request->role, ['reviewer', 'both'])) {
        // SAME validation rules as jury
        $rules = array_merge($rules, [
            'jury_qualification_summary' => 'required|min:100|max:1000',
            'jury_institution' => 'required|min:3|max:255',
            'jury_position' => 'required|min:3|max:255',
            'jury_years_experience' => 'required|integer|min:1',
            'jury_expertise_areas' => 'required|min:20|max:500',
        ]);
    }
}
```

---

## Migration for Role Field

If you need to enforce specific role values per event type, you could add validation in the application layer:

```php
// In EventRegistration model
public static function boot()
{
    parent::boot();
    
    static::creating(function ($registration) {
        $event = $registration->event;
        
        // Validate role based on event type
        if ($event->category->name === 'Innovation Competition') {
            if (!in_array($registration->role, ['participant', 'jury', 'both'])) {
                throw new \Exception('Invalid role for Innovation Competition. Use: participant, jury, or both');
            }
        }
        
        if ($event->category->name === 'Academic Conference') {
            if (!in_array($registration->role, ['participant', 'reviewer', 'both'])) {
                throw new \Exception('Invalid role for Academic Conference. Use: participant, reviewer, or both');
            }
        }
    });
}
```

---

## Summary Table

| Aspect | Innovation Competition | Academic Conference | Database Field |
|--------|----------------------|---------------------|----------------|
| **Presents/Submits** | participant | participant | `role = 'participant'` |
| **Evaluates** | jury | reviewer | `role = 'jury'` or `role = 'reviewer'` |
| **Both** | both | both | `role = 'both'` |
| **Qualification Fields** | jury_* | jury_* (same) | Same fields used |
| **Paper Submission** | ❌ No | ✅ Yes | N/A |
| **When Evaluation Happens** | During event | Before event | N/A |

---

## Recommended: Event Category Setup

Make sure your `event_categories` table has clear categories:

```sql
INSERT INTO event_categories (name, description) VALUES
('Innovation Competition', 'Idea pitches, startup competitions, hackathons'),
('Academic Conference', 'Research paper presentations, symposiums'),
('Workshop', 'Hands-on learning sessions'),
('Seminar', 'Educational talks and presentations');
```

Then in your code:

```php
// Check event type easily
$isInnovation = $event->category->name === 'Innovation Competition';
$isAcademic = $event->category->name === 'Academic Conference';

if ($isInnovation) {
    // Use jury terminology, no paper submission
}

if ($isAcademic) {
    // Use reviewer terminology, with paper submission
}
```

---

This clarifies the role field usage and ensures your friend understands:
1. **Same database field** (`role`) for both event types
2. **Different values** depending on event type
3. **Same qualification fields** for both jury and reviewer
4. **Paper submission only for conferences**, not innovation competitions
