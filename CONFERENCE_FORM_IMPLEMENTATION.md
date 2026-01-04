# Conference Form Implementation Guide

## Overview
The event creation form now supports **Academic Conference** events with paper submission functionality. When an organizer selects "Academic Conference" as the event category, additional fields appear for configuring paper submission requirements.

---

## Features Implemented

### 1. **Dynamic Form Fields**
Conference-specific fields automatically show/hide based on category selection:

- ✅ **Paper Submission Deadline** (Required)
- ✅ **Minimum Abstract Word Count** (Default: 200)
- ✅ **Minimum Keywords Required** (Default: 3)
- ✅ **Maximum Paper File Size** (Default: 10MB)
- ✅ **Paper Format Guidelines** (Optional text)
- ✅ **Allow Multiple Submissions** (Checkbox)
- ✅ **Minimum Reviewers per Paper** (Default: 2)
- ✅ **Review Deadline** (Optional)

---

## How It Works

### Frontend (Blade Template)

**File:** `resources/views/organizer/events/create.blade.php`

#### Conference Fields Section
```blade
<!-- Conference-Specific Fields (Show only for Academic Conference) -->
<div id="conference-fields" class="mt-8 border-t pt-6" style="display: none;">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-file-alt mr-2 text-blue-600"></i>
        Paper Submission Settings (Academic Conference)
    </h3>
    <!-- Fields here -->
</div>
```

#### JavaScript Logic
```javascript
const categorySelect = document.querySelector('select[name="category_id"]');
const conferenceFields = document.getElementById('conference-fields');

function toggleConferenceFields() {
    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
    const categoryName = selectedOption.text;
    
    if (categoryName.includes('Conference') || categoryName.includes('Academic')) {
        conferenceFields.style.display = 'block';
        paperDeadlineInput.setAttribute('required', 'required');
    } else {
        conferenceFields.style.display = 'none';
        paperDeadlineInput.removeAttribute('required');
    }
}
```

---

### Backend (Controller)

**File:** `app/Http/Controllers/Organizer/EventController.php`

#### Validation Rules
```php
// Check if this is an academic conference
$category = EventCategory::find($request->category_id);
$isConference = $category && (
    $category->name === 'Academic Conference' || 
    strpos($category->name, 'Conference') !== false
);

// Add conference-specific validation
if ($isConference && !$isInnovationForm) {
    $validationRules['paper_submission_deadline'] = 'required|date|before:start_date';
    $validationRules['min_abstract_words'] = 'nullable|integer|min:50|max:1000';
    $validationRules['min_keywords'] = 'nullable|integer|min:1|max:10';
    $validationRules['max_paper_size_mb'] = 'nullable|integer|min:1|max:50';
    $validationRules['paper_format_guidelines'] = 'nullable|string|max:2000';
    $validationRules['allow_multiple_submissions'] = 'nullable|boolean';
    $validationRules['min_reviewers_per_paper'] = 'nullable|integer|min:1|max:10';
    $validationRules['review_deadline'] = 'nullable|date|after:paper_submission_deadline|before:start_date';
}
```

#### Saving Conference Data
```php
// Add conference-specific data
if ($isConference && !$isInnovationForm) {
    $eventData['paper_submission_deadline'] = $request->paper_submission_deadline;
    $eventData['min_abstract_words'] = $request->min_abstract_words ?? 200;
    $eventData['min_keywords'] = $request->min_keywords ?? 3;
    $eventData['max_paper_size_mb'] = $request->max_paper_size_mb ?? 10;
    $eventData['paper_format_guidelines'] = $request->paper_format_guidelines;
    $eventData['allow_multiple_submissions'] = $request->has('allow_multiple_submissions');
    $eventData['min_reviewers_per_paper'] = $request->min_reviewers_per_paper ?? 2;
    $eventData['review_deadline'] = $request->review_deadline;
}
```

---

### Database Migration

**File:** `database/migrations/2025_12_09_173733_add_conference_fields_to_events_table.php`

#### New Columns Added to `events` Table
```php
$table->datetime('paper_submission_deadline')->nullable();
$table->integer('min_abstract_words')->default(200);
$table->integer('min_keywords')->default(3);
$table->integer('max_paper_size_mb')->default(10);
$table->text('paper_format_guidelines')->nullable();
$table->boolean('allow_multiple_submissions')->default(false);
$table->integer('min_reviewers_per_paper')->default(2);
$table->datetime('review_deadline')->nullable();
```

---

### Model Updates

**File:** `app/Models/Event.php`

#### Fillable Fields
```php
protected $fillable = [
    // ... existing fields
    'paper_submission_deadline',
    'min_abstract_words',
    'min_keywords',
    'max_paper_size_mb',
    'paper_format_guidelines',
    'allow_multiple_submissions',
    'min_reviewers_per_paper',
    'review_deadline',
    // ... other fields
];
```

#### Casts
```php
protected $casts = [
    // ... existing casts
    'paper_submission_deadline' => 'datetime',
    'review_deadline' => 'datetime',
    // ... other casts
];
```

---

## User Flow

### For Organizers Creating Academic Conference:

1. **Navigate to Create Event** → Click "Create Event"
2. **Fill Basic Details** → Title, Description, Dates, Venue
3. **Select Category** → Choose "Academic Conference"
4. **Conference Fields Appear** → Additional paper submission settings show
5. **Configure Paper Submission:**
   - Set paper submission deadline (before event start)
   - Set minimum abstract word count (default: 200)
   - Set minimum keywords (default: 3)
   - Set max paper file size (default: 10MB)
   - Add format guidelines (optional)
   - Choose if multiple submissions allowed
   - Set minimum reviewers per paper (default: 2)
   - Set review deadline (optional, between paper deadline and event start)
6. **Submit** → Event created with conference settings

---

## Field Constraints

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| Paper Submission Deadline | DateTime | Yes (for conferences) | Must be before event start date |
| Min Abstract Words | Integer | No | 50-1000 (default: 200) |
| Min Keywords | Integer | No | 1-10 (default: 3) |
| Max Paper Size (MB) | Integer | No | 1-50 (default: 10) |
| Paper Format Guidelines | Text | No | Max 2000 characters |
| Allow Multiple Submissions | Boolean | No | Default: false |
| Min Reviewers per Paper | Integer | No | 1-10 (default: 2) |
| Review Deadline | DateTime | No | After paper deadline, before event start |

---

## Validation Rules

### Date Logic:
```
Today → Registration Deadline → Paper Submission Deadline → Review Deadline → Event Start Date
```

### JavaScript Validation:
- Paper submission deadline max = event start date
- Review deadline min = paper submission deadline
- Review deadline max = event start date

---

## Testing Checklist

- [ ] Form shows basic fields for all event types
- [ ] Conference fields hidden by default
- [ ] Conference fields show when "Academic Conference" selected
- [ ] Conference fields hide when switching to other categories
- [ ] Paper submission deadline is required for conferences
- [ ] Date constraints are enforced (deadlines before event start)
- [ ] Default values populate correctly (200, 3, 10, 2)
- [ ] Multiple submissions checkbox works
- [ ] Format guidelines textarea accepts text
- [ ] Form submits successfully with conference data
- [ ] Database stores conference fields correctly
- [ ] Validation errors display properly

---

## Next Steps

### For Edit Form:
- [ ] Add conference fields to `resources/views/organizer/events/edit.blade.php`
- [ ] Update `EventController@update` method to handle conference fields
- [ ] Add JavaScript to toggle conference fields in edit form

### For Participant Experience:
- [ ] Use these settings in paper submission form validation
- [ ] Display format guidelines to participants
- [ ] Enforce file size limits
- [ ] Enforce abstract word count
- [ ] Enforce keyword requirements
- [ ] Respect multiple submissions setting

### For Review System:
- [ ] Use `min_reviewers_per_paper` for assignment logic
- [ ] Use `review_deadline` for reviewer notifications
- [ ] Display review deadline to reviewers

---

## Related Files

### Views:
- `resources/views/organizer/events/create.blade.php` - Event creation form with conference fields

### Controllers:
- `app/Http/Controllers/Organizer/EventController.php` - Handles event creation/update with validation

### Models:
- `app/Models/Event.php` - Event model with fillable fields and casts

### Migrations:
- `database/migrations/2025_12_09_173733_add_conference_fields_to_events_table.php` - Adds conference columns

---

## Comparison: Innovation vs Conference

| Feature | Innovation Competition | Academic Conference |
|---------|----------------------|---------------------|
| **Delivery Mode** | Face-to-face, Online, Hybrid | Standard (Face-to-face) |
| **Paper Submission** | ❌ No (Ideas presented live) | ✅ Yes (Papers submitted in advance) |
| **Role Options** | Participant, Jury, Both | Participant, Reviewer, Both |
| **Jury/Reviewer** | Judges at event | Reviews before event |
| **Deadlines** | Multiple (by delivery mode) | Paper → Review → Event |
| **Categories** | Innovation categories list | Standard category |

---

## Status

✅ **Conference form implementation is COMPLETE** for event creation.

The form now properly supports Academic Conference events with full paper submission configuration. Organizers can set all necessary parameters for paper submissions, reviews, and conference requirements.

---

**Last Updated:** December 10, 2025
**Implementation:** Event Creation Form (Conference Support)
**Developer Notes:** Conference edit form needs similar updates.
