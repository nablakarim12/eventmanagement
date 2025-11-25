# DATABASE SCHEMA COORDINATION
## For Shared Database Between Projects

### TABLES NEEDED FOR REGISTRATION SYSTEM (Friend's Project + Your Project)

#### 1. Enhanced `event_registrations` table
```sql
-- Add these columns to existing event_registrations table
ALTER TABLE event_registrations ADD COLUMN role_type VARCHAR(20); -- 'participant', 'jury', 'both'
ALTER TABLE event_registrations ADD COLUMN approval_status VARCHAR(20) DEFAULT 'pending'; -- 'pending', 'approved', 'rejected'
ALTER TABLE event_registrations ADD COLUMN approved_by INTEGER REFERENCES users(id); -- organizer who approved
ALTER TABLE event_registrations ADD COLUMN approved_at TIMESTAMP;
ALTER TABLE event_registrations ADD COLUMN rejection_reason TEXT;
ALTER TABLE event_registrations ADD COLUMN materials_submitted BOOLEAN DEFAULT FALSE;
ALTER TABLE event_registrations ADD COLUMN documents_submitted BOOLEAN DEFAULT FALSE;
```

#### 2. New `registration_files` table  
```sql
CREATE TABLE registration_files (
    id SERIAL PRIMARY KEY,
    registration_id INTEGER REFERENCES event_registrations(id) ON DELETE CASCADE,
    file_type VARCHAR(50), -- 'material', 'document', 'poster', 'cv', etc.
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    file_size INTEGER,
    mime_type VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_required BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 3. New `registration_form_data` table
```sql
CREATE TABLE registration_form_data (
    id SERIAL PRIMARY KEY,
    registration_id INTEGER REFERENCES event_registrations(id) ON DELETE CASCADE,
    field_name VARCHAR(100),
    field_value TEXT,
    field_type VARCHAR(50), -- 'text', 'email', 'file', 'select', etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### INTEGRATION POINTS FOR QR GENERATION

#### Status Check Function (Your QR System Will Use):
```php
// Function to check if user is eligible for QR code
function isEligibleForQR($userId, $eventId) {
    $registration = EventRegistration::where('user_id', $userId)
                                   ->where('event_id', $eventId)
                                   ->first();
    
    if (!$registration) return false;
    
    // Check deadline
    $event = Event::find($eventId);
    if (now() > $event->registration_deadline) return false;
    
    // Check approval status based on role
    if ($registration->role_type === 'participant') {
        return $registration->materials_submitted;
    } elseif ($registration->role_type === 'jury') {
        return $registration->approval_status === 'approved';
    } elseif ($registration->role_type === 'both') {
        return $registration->materials_submitted && 
               $registration->approval_status === 'approved';
    }
    
    return false;
}
```

### WORKFLOW INTEGRATION

#### Friend's Project Endpoints (What you need from them):
1. `POST /register/event/{id}` - User registration with role selection
2. `POST /register/{id}/upload` - File upload for materials/documents
3. `GET /user/registrations` - User's registration status
4. `GET /events/available` - Events available for registration

#### Your Project Endpoints (What you'll build):
1. `GET /organizer/registrations/{event}` - View all registrations for approval
2. `POST /organizer/registrations/{id}/approve` - Approve jury application
3. `POST /organizer/registrations/{id}/reject` - Reject jury application
4. `GET /organizer/events/{id}/qr-eligible` - Users eligible for QR codes

### SHARED DATABASE CONFIGURATION
Make sure both projects use same database connection with proper table prefixes if needed.