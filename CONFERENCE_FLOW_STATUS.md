# Conference Event Flow - Implementation Status

## ✅ What's Working

### 1. Event Organizer - Conference Creation
- ✅ Conference creation form exists (`create_conference.blade.php`)
- ✅ Conference edit form exists (`edit_conference.blade.php`)
- ✅ All deadlines properly stored (F2F and Online):
  - Reviewer Registration Deadline
  - Paper Submission Deadline
  - Review/Evaluation Deadline
  - Acceptance Notification Date
  - Payment Deadline
  - Event Start/End Dates
- ✅ Conference categories/topics support
- ✅ Delivery modes: face_to_face, online, hybrid
- ✅ QR code generation works for conference events

### 2. User Registration (Your Friend's Side)
- ✅ Event registration system exists
- ✅ Role selection: participant, reviewer, both
- ✅ Certificate upload for reviewers (`certificate_path`, `certificate_filename`)
- ✅ Jury qualification fields available:
  - `jury_qualification_documents`
  - `jury_expertise_areas`
  - `jury_institution`
  - `jury_position`
  - `jury_years_experience`

### 3. Paper Submission (Participants)
- ✅ Paper submission routes added to `web.php`
- ✅ Paper submission form created (`papers/create.blade.php`)
- ✅ Paper submission controller exists (`PaperSubmissionController.php`)
- ✅ Support for multiple authors
- ✅ PDF upload (max 10MB)
- ✅ Abstract, keywords, title fields
- ✅ Paper viewing page created (`papers/show.blade.php`)
- ✅ My papers list created (`papers/index.blade.php`)

### 4. Event Organizer - Approval System
- ✅ Registration approval system exists
- ✅ Organizer can approve/reject registrations
- ✅ Approval status tracking (`approval_status`: pending, approved, rejected)

### 5. Event Organizer - Jury Mapping
- ✅ Jury assignment system exists (`JuryAssignmentController.php`)
- ✅ Self-evaluation prevention (users with both roles cannot review own paper)
- ✅ Manual assignment feature
- ✅ Auto-assign feature (distributes evenly)
- ⚠️ **MISSING**: Category-based assignment logic for conference events

### 6. Evaluation System
- ✅ Rubric system exists (`RubricController.php`)
- ✅ Rubric categories and items support
- ✅ Score levels configuration
- ✅ Weighted scoring
- ✅ Paper review controller exists (`PaperReviewController.php`)
- ✅ Review submission form exists
- ✅ Review scoring system

### 7. Paper Management (Organizer)
- ✅ View all paper submissions (`PaperManagementController.php`)
- ✅ Download papers
- ✅ Assign reviewers to papers
- ✅ View review scores
- ✅ Update paper status (submitted, under_review, accepted, rejected)

### 8. Certificate Generation
- ✅ Certificate generation system exists
- ✅ Auto-generate after event
- ✅ Email certificates to participants/reviewers

---

## ⚠️ What Needs to Be Added/Fixed

### 1. Category-Based Reviewer Assignment (IMPORTANT)
**Issue**: Current jury assignment doesn't check conference categories to avoid bias.

**Requirements**:
- Reviewers should NOT review papers in the same category they registered for
- When user registers as reviewer, they select expertise categories
- When participant submits paper, they select paper category
- Mapping should ensure: `reviewer_category ≠ paper_category`

**Action Needed**:
- Add `selected_categories` column to `event_registrations` table (JSON array)
- Add `selected_category` to `paper_submissions` table
- Update registration form to capture reviewer expertise categories
- Update paper submission form to capture paper category
- Modify `JuryAssignmentController.php` auto-assign logic to check categories

### 2. Reviewer Registration Form Enhancement
**Current**: Basic registration form
**Needed**: 
- Category/topic selection for reviewers (from `event.conference_categories`)
- Better certificate upload UI
- Qualification form fields

### 3. Paper Submission Category Selection
**Current**: Paper submission form doesn't have category selection
**Needed**:
- Add category dropdown (from `event.conference_categories`)
- Store in `paper_submissions.selected_category`

### 4. Notification System After Review
**Current**: Manual notifications
**Needed**:
- Auto-send email to accepted participants
- Include payment deadline reminder
- Template for acceptance/rejection emails

### 5. Payment Tracking After Acceptance
**Current**: General payment tracking exists
**Needed**:
- Link payment specifically to acceptance
- Track payment deadline compliance
- Only generate certificate if paid

---

## 📋 Conference Event Flow (Current Implementation)

### Step 1: Event Organizer Creates Conference ✅
```
Organizer → Create Conference Event → Set all deadlines → Publish
```
- Form: `create_conference.blade.php`
- Controller: `EventController@store`

### Step 2: User Registration ✅ (Your Friend's Side)
```
User → Register for Event → Select Role (participant/reviewer/both) → Upload Certificate (if reviewer)
```
- Controller: `EventRegistrationController`
- Table: `event_registrations` (with role, certificate_path)

### Step 3: Paper Submission ✅ (Participants)
```
Participant → Submit Paper → Upload PDF → Add Authors → Submit before deadline
```
- Routes: `papers.create`, `papers.store`
- Views: `papers/create.blade.php`
- Controller: `PaperSubmissionController`

### Step 4: Organizer Approval ✅
```
Organizer → View Registrations → Approve/Reject Each User
```
- Controller: `ApprovalController`
- Table: `event_registrations.approval_status`

### Step 5: Reviewer Mapping ⚠️ (Needs Category Logic)
```
Organizer → Assign Reviewers to Papers → Prevent self-review → Prevent same-category review
```
- Controller: `JuryAssignmentController`
- **TODO**: Add category-based filtering

### Step 6: Reviewer Evaluation ✅
```
Reviewer → View Assigned Papers → Use Rubric → Submit Scores before deadline
```
- Controller: `PaperReviewController`
- Rubric: `RubricController`

### Step 7: Acceptance Notifications ⚠️ (Manual)
```
Organizer → View Scores → Mark Accepted/Rejected → Send Notifications → Request Payment
```
- **TODO**: Auto-notification system

### Step 8: Certificate Generation ✅
```
System → After Event → Auto-generate Certificates → Email to Eligible Users
```
- Controller: `CertificateController`

---

## 🚀 Immediate Next Steps

1. **Add category selection to registration form** (for reviewers)
2. **Add category selection to paper submission form**
3. **Modify JuryAssignmentController auto-assign** to check categories
4. **Test complete flow** from creation to certificate generation

---

## Database Schema Updates Needed

```sql
-- Add selected categories to registrations
ALTER TABLE event_registrations 
ADD COLUMN selected_categories JSON;

-- Add category to paper submissions  
ALTER TABLE paper_submissions 
ADD COLUMN selected_category VARCHAR(255);
```

---

## Testing Checklist

- [ ] Create conference event with categories
- [ ] Register as reviewer with category selection
- [ ] Register as participant
- [ ] Submit paper with category
- [ ] Organizer approves registrations
- [ ] Organizer maps reviewers (auto-assign with category check)
- [ ] Reviewer submits evaluation
- [ ] Organizer marks papers accepted
- [ ] Payment tracking
- [ ] Certificate generation

---

**Summary**: Most of the conference flow is implemented! The main missing piece is the **category-based reviewer assignment logic** to prevent bias. Everything else works.
