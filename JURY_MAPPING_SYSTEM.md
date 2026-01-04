# Jury Mapping System - Conference Events

## Overview
The jury mapping system allows event organizers to assign reviewers to participants for conference events, with intelligent constraints to ensure fair and unbiased reviews.

## Business Constraints

### 1. Self-Evaluation Prevention
- **Rule**: Users with both roles (participant + reviewer) cannot review their own paper
- **Implementation**: System checks `user_id` match between reviewer and participant
- **Example**: If Ahmad registers as both participant and reviewer, he cannot be assigned to review his own submission

### 2. Same-Category Prevention
- **Rule**: Reviewers cannot evaluate participants from the same category
- **Implementation**: System checks `selected_category` match
- **Example Valid**: Learning Analytics reviewer ✅ → Digital Pedagogy participant
- **Example Invalid**: EdTech reviewer ❌ → EdTech participant

## Database Structure

### Jury Mappings Table
```sql
jury_mappings
├── id (bigint, primary key)
├── event_id (bigint, foreign key → events)
├── reviewer_registration_id (bigint, foreign key → event_registrations)
├── participant_registration_id (bigint, foreign key → event_registrations)
├── assigned_by (bigint, foreign key → event_organizers)
├── status (varchar: pending, completed, skipped)
├── review_notes (text, nullable)
├── score (decimal, nullable)
├── reviewed_at (timestamp, nullable)
├── created_at (timestamp)
└── updated_at (timestamp)

Unique Constraint: (reviewer_registration_id, participant_registration_id)
```

## Features

### 1. Event List View (`/organizer/jury-mapping`)
- Shows all conference events with registration data
- Displays mapping statistics per event:
  - Total participants
  - Total reviewers
  - Total assignments
  - Participants without reviewers
  - Mapping coverage percentage
- Visual progress indicators (circular charts + progress bars)
- Color-coded status:
  - 🟢 Green: 100% coverage
  - 🟡 Yellow: 50-99% coverage
  - 🔴 Red: 0-49% coverage

### 2. Event Detail View (`/organizer/jury-mapping/{event}`)
- **Left Panel**: Participants list
  - Shows each participant with their selected category
  - Displays currently assigned reviewers
  - "Assign" button to add more reviewers
  - Remove button for each assigned reviewer
  
- **Right Panel**: Reviewer workload
  - Shows all reviewers with their categories
  - Displays current assignment count
  - Color-coded workload status:
    - Light: 0 assignments
    - Good: 1-3 assignments
    - Moderate: 4-6 assignments
    - Heavy: 7+ assignments

### 3. Auto-Assignment Feature
- **Button**: "Auto-Assign" at top of event detail page
- **Algorithm**:
  1. Assigns minimum 2 reviewers per participant (configurable)
  2. Filters eligible reviewers based on constraints:
     - Different user_id
     - Different selected_category
     - Not already assigned
  3. Distributes load evenly (assigns to least-loaded reviewers first)
  4. Skips participants without enough eligible reviewers

### 4. Manual Assignment
- **Modal Dialog**: Click "Assign" button on any participant
- Shows list of eligible reviewers with:
  - Name and avatar
  - Selected category
  - Current workload count
- Reviewers filtered by constraints automatically
- One-click assignment
- Real-time validation (same user, same category checks)

### 5. Remove Assignment
- **Button**: "X" icon next to each assigned reviewer
- Confirmation dialog before removal
- Updates workload statistics immediately

## Routes

```php
GET  /organizer/jury-mapping                                  // Event list
GET  /organizer/jury-mapping/{event}                          // Event detail
GET  /organizer/jury-mapping/{event}/eligible-reviewers/{participant}  // AJAX: Get eligible reviewers
POST /organizer/jury-mapping/{event}/assign                   // Assign reviewer to participant
POST /organizer/jury-mapping/{event}/auto-assign              // Auto-assign all
DELETE /organizer/jury-mapping/mappings/{mapping}             // Remove assignment
```

## Models & Relationships

### JuryMapping Model
```php
Relationships:
- event() → Event
- reviewerRegistration() → EventRegistration
- participantRegistration() → EventRegistration
- assignedBy() → Organizer (event_organizers table)
```

### EventRegistration Model (Updated)
```php
New Relationships:
- juryMappingsAsReviewer() → JuryMapping[]  // When this registration is the reviewer
- juryMappingsAsParticipant() → JuryMapping[]  // When this registration is the participant
```

## Controller Methods

### JuryMappingController
1. **index()** - List all conference events with mapping stats
2. **show($event)** - Show detailed mapping for specific event
3. **getEligibleReviewers($event, $participant)** - AJAX endpoint for filtered reviewers
4. **assignReviewer(Request $request, $event)** - Manually assign reviewer
5. **removeReviewer(JuryMapping $mapping)** - Remove assignment
6. **autoAssign($event)** - Automatically assign reviewers to all participants

## Validation Logic

### assignReviewer() Method
```php
// Check 1: Same user
if ($participant->user_id === $reviewer->user_id) {
    return error('User cannot review their own paper!');
}

// Check 2: Same category
if ($participant->selected_category === $reviewer->selected_category) {
    return error('Reviewer cannot evaluate participant from same category!');
}

// Check 3: Already assigned
if (mapping exists) {
    return error('Already assigned');
}
```

### getEligibleReviewers() Query
```sql
SELECT * FROM event_registrations
WHERE event_id = ?
  AND status = 'confirmed'
  AND role = 'reviewer'
  AND user_id != ?  -- Not same user
  AND (selected_category != ? OR selected_category IS NULL)  -- Not same category
  AND id NOT IN (
    SELECT reviewer_registration_id 
    FROM jury_mappings 
    WHERE participant_registration_id = ?
  )  -- Not already assigned
ORDER BY current_workload ASC  -- Least loaded first
```

## Usage Workflow

### For Event Organizers:

1. **Approve Registrations** (`/organizer/registrations`)
   - Approve participants with their selected categories
   - Approve reviewers with their selected categories

2. **Navigate to Jury Mapping** (`/organizer/jury-mapping`)
   - See list of all conference events
   - View mapping coverage percentages

3. **Choose Event** (Click "Manage Mapping")
   - See participants on left
   - See reviewer workload on right

4. **Option A: Auto-Assign**
   - Click "Auto-Assign" button
   - System assigns 2 reviewers per participant automatically
   - Constraints applied automatically
   - Success message shows number of assignments created

5. **Option B: Manual Assign**
   - Click "Assign" button on specific participant
   - Modal shows eligible reviewers (filtered by constraints)
   - Click "Assign" next to desired reviewer
   - Assignment created immediately

6. **Remove Assignments** (if needed)
   - Click "X" icon next to assigned reviewer
   - Confirm removal
   - Assignment deleted

## Example Scenario

**Event**: International EdTech Conference 2025
**Categories**: EdTech, E-Learning, Digital Pedagogy, Learning Analytics

**Registrations**:
- Luqmanul (Participant, EdTech)
- Alvin Wei (Participant, E-Learning)
- Aisyah (Participant, Digital Pedagogy)
- Subramaniam (Reviewer, Learning Analytics)
- Lim Wei (Reviewer, Marketing)
- Ahmad (Both Participant & Reviewer, EdTech)

**Valid Assignments**:
- Subramaniam (Learning Analytics) → Luqmanul (EdTech) ✅
- Lim Wei (Marketing) → Alvin Wei (E-Learning) ✅
- Subramaniam (Learning Analytics) → Ahmad (EdTech) ✅

**Invalid Assignments**:
- Ahmad → Ahmad's own paper ❌ (Same user)
- Any EdTech reviewer → Luqmanul (EdTech) ❌ (Same category)
- Any EdTech reviewer → Ahmad (EdTech) ❌ (Same category)

## Files Changed/Created

### Controllers
- `app/Http/Controllers/Organizer/JuryMappingController.php` (Updated)

### Models
- `app/Models/JuryMapping.php` (Created)
- `app/Models/EventRegistration.php` (Updated - added relationships)

### Migrations
- `database/migrations/2025_12_13_162526_create_jury_mappings_table.php` (Created)
- `database/migrations/2025_12_13_160718_add_selected_category_to_event_registrations_table.php` (Already exists)

### Views
- `resources/views/organizer/jury-mapping/index.blade.php` (Updated)
- `resources/views/organizer/jury-mapping/show.blade.php` (Updated)

### Routes
- `routes/web.php` (Updated - added 4 new jury-mapping routes)

## Testing Checklist

- [ ] Navigate to `/organizer/jury-mapping`
- [ ] See list of conference events with statistics
- [ ] Click on event to see detailed mapping
- [ ] Click "Assign" on a participant
- [ ] Verify only eligible reviewers shown (not same user, not same category)
- [ ] Assign a reviewer manually
- [ ] Verify assignment appears in participant's list
- [ ] Verify reviewer workload count increases
- [ ] Click "X" to remove assignment
- [ ] Verify assignment removed
- [ ] Click "Auto-Assign" button
- [ ] Verify participants get reviewers assigned
- [ ] Check that constraints are respected (no same-user, no same-category)

## Next Steps (Optional Enhancements)

1. **Email Notifications**: Notify reviewers when assigned
2. **Review Submission**: Allow reviewers to submit scores and notes
3. **Dashboard Widget**: Show pending reviews on reviewer dashboard
4. **Export**: Download mapping assignments as Excel/PDF
5. **Bulk Operations**: Assign same reviewer to multiple participants
6. **Minimum Reviewers**: Configure minimum reviewers per participant per event
7. **Reviewer Availability**: Let reviewers set availability/capacity
8. **Conflict of Interest**: Allow manual marking of conflicts beyond category
