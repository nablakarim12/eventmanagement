# Innovation Event Date Fields Update

## Required Date Fields (In Order):

### 1. Registration Deadline (Participant)
- **Field**: `registration_deadline`
- **Validation**: Must be >= today
- **Description**: Last date for participants to register

### 2. Jury Registration Deadline
- **Field**: `jury_registration_deadline`  
- **Validation**: Must be >= today
- **Description**: Last date for jury members to register

### 3. Submission Deadline (Participant)
- **Field**: `submission_deadline`
- **Validation**: Must be > Registration Deadline
- **Description**: Last date for participants to submit/edit posters and product details

### 4. Acceptance Notification Deadline
- **Field**: `acceptance_notification_date`
- **Validation**: Must be > Submission Deadline
- **Description**: Date when EO sends acceptance notifications to participants

### 5. Extended Deadlines (Optional)
- **Fields**: 
  - `extended_registration_deadline`
  - `extended_jury_deadline`
  - `extended_submission_deadline`
  - `extended_notification_date`
- **Validation**: Each must be > their respective original deadline
- **Description**: Optional extension if registrations haven't reached target

### 6. Payment Deadline (Participant Only)
- **Field**: `payment_deadline`
- **Validation**: Must be > (original 3 deadlines OR extended deadlines if set)
- **Description**: Last date for participants to complete payment

### 7. Event Start Date & Time
- **Field**: `start_date`, `start_time` (or combined `start_datetime`)
- **Validation**: Must be > Payment Deadline
- **Description**: When the event begins

### 8. Event End Date & Time
- **Field**: `end_date`, `end_time` (or combined `end_datetime`)
- **Validation**: Must be >= Start Date/Time + 2 hours
- **Description**: When the event ends

## Validation Chain:

```
Today <= Registration Deadline
Today <= Jury Registration Deadline
Registration Deadline < Submission Deadline
Submission Deadline < Acceptance Notification
Original Deadlines < Extended Deadlines (if set)
(Original OR Extended) < Payment Deadline
Payment Deadline < Event Start
Event Start + 2 hours <= Event End
```

## Implementation Notes:

1. These fields apply to BOTH f2f and online modes separately
2. For hybrid mode, each mode has its own complete set
3. JavaScript validation should prevent form submission if rules violated
4. Backend validation should also enforce these rules
5. Helpful error messages should guide organizers

## Migration Needs:

- Add `registration_deadline` (datetime)
- Add `submission_deadline` (datetime)  
- Add `acceptance_notification_date` (datetime)
- Add `extended_registration_deadline` (datetime, nullable)
- Add `extended_jury_deadline` (datetime, nullable)
- Add `extended_submission_deadline` (datetime, nullable)
- Add `extended_notification_date` (datetime, nullable)

Similar fields with `f2f_` and `online_` prefixes for hybrid events.
