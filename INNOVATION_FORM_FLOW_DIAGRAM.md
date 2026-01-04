# Innovation Competition Form - Visual Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                    EVENT CREATION FLOW                              │
└─────────────────────────────────────────────────────────────────────┘

                              START
                                │
                                ▼
                    ┌───────────────────────┐
                    │  Click "Create Event" │
                    │  (Organizer Dashboard)│
                    └───────────┬───────────┘
                                │
                                ▼
                    ┌───────────────────────┐
                    │  Event Type Selection │
                    │       Page            │
                    └───────────┬───────────┘
                                │
                ┌───────────────┼───────────────┐
                │                               │
                ▼                               ▼
    ┌─────────────────────┐         ┌─────────────────────┐
    │  Innovation         │         │  Academic           │
    │  Competition        │         │  Conference         │
    │  (IMPLEMENTED)      │         │  (COMING SOON)      │
    └──────────┬──────────┘         └─────────────────────┘
               │
               ▼
    ┌─────────────────────────────────────────────────┐
    │  INNOVATION COMPETITION FORM                    │
    ├─────────────────────────────────────────────────┤
    │                                                 │
    │  📝 SECTION 1: Basic Information                │
    │     • Event Title                               │
    │     • Category (Auto: Innovation Competition)   │
    │     • Registration Deadline                     │
    │     • Description                               │
    │                                                 │
    │  📍 SECTION 2: Location & Pricing               │
    │     • Venue Name & Address                      │
    │     • City & Country                            │
    │     • Max Participants                          │
    │     • Registration Fee                          │
    │     • Event Poster Upload                       │
    │                                                 │
    │  🎯 SECTION 3: Delivery Mode Selection ⭐       │
    │                                                 │
    │     ┌──────────┐ ┌──────────┐ ┌──────────┐    │
    │     │ Face to  │ │  Online  │ │  Hybrid  │    │
    │     │   Face   │ │   Only   │ │  (Both)  │    │
    │     └────┬─────┘ └────┬─────┘ └────┬─────┘    │
    │          │            │            │           │
    └──────────┼────────────┼────────────┼───────────┘
               │            │            │
       ┌───────┘            │            └────────┐
       │                    │                     │
       ▼                    ▼                     ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────────┐
│   F2F ONLY   │    │ ONLINE ONLY  │    │  HYBRID (BOTH)   │
├──────────────┤    ├──────────────┤    ├──────────────────┤
│              │    │              │    │                  │
│ Show:        │    │ Show:        │    │ Show:            │
│ • F2F Section│    │ • Online Sec │    │ • F2F Section    │
│              │    │              │    │ • Online Section │
│ Hide:        │    │ Hide:        │    │                  │
│ • Online Sec │    │ • F2F Section│    │ (Both visible)   │
│              │    │              │    │                  │
└──────┬───────┘    └──────┬───────┘    └────────┬─────────┘
       │                   │                     │
       │                   │                     │
       └───────────────────┼─────────────────────┘
                           │
                           ▼
            ┌─────────────────────────────────┐
            │  CONDITIONAL SECTIONS           │
            └─────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│  🏛️ SECTION 4: Face-to-Face Details                             │
│  (Visible when F2F or Hybrid selected)                          │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Event Timing:                                                   │
│  ├─ F2F Start Date ────────► [March 10, 2025]                   │
│  ├─ F2F End Date ──────────► [March 12, 2025]                   │
│  ├─ F2F Start Time ────────► [09:00]                            │
│  └─ F2F End Time ──────────► [17:00]                            │
│                                                                  │
│  Submission Deadlines:                                           │
│  ├─ Paper Deadline ────────► [February 1, 2025]                 │
│  ├─ Product Deadline ──────► [February 15, 2025]                │
│  ├─ Abstract Deadline ─────► [January 20, 2025]                 │
│  └─ Acceptance Date ───────► [February 20, 2025]                │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│  💻 SECTION 5: Online Details                                    │
│  (Visible when Online or Hybrid selected)                       │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Event Timing:                                                   │
│  ├─ Online Start Date ─────► [March 5, 2025]                    │
│  ├─ Online End Date ───────► [March 14, 2025]                   │
│  ├─ Online Start Time ─────► [09:00]                            │
│  └─ Online End Time ───────► [17:00]                            │
│                                                                  │
│  Platform:                                                       │
│  └─ Platform URL ──────────► [https://zoom.us/j/123456789]      │
│                                                                  │
│  Submission Deadlines:                                           │
│  ├─ Paper Deadline ────────► [February 10, 2025]                │
│  ├─ Product Deadline ──────► [February 25, 2025]                │
│  ├─ Abstract Deadline ─────► [January 25, 2025]                 │
│  └─ Acceptance Date ───────► [February 28, 2025]                │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘

                           │
                           ▼
                ┌──────────────────────┐
                │   Submit Form        │
                │ "Create Innovation   │
                │       Event"         │
                └──────────┬───────────┘
                           │
                           ▼
            ┌──────────────────────────────┐
            │  CONTROLLER PROCESSING       │
            ├──────────────────────────────┤
            │  1. Validate delivery mode   │
            │  2. Validate required fields │
            │     based on mode            │
            │  3. Store event data         │
            │  4. Store mode-specific data │
            │  5. Upload poster image      │
            └──────────┬───────────────────┘
                       │
                       ▼
            ┌──────────────────────────────┐
            │    DATABASE STORAGE          │
            ├──────────────────────────────┤
            │  events table:               │
            │  ├─ Basic fields             │
            │  ├─ delivery_mode            │
            │  ├─ f2f_* fields (if F2F)    │
            │  ├─ online_* fields (Online) │
            │  └─ featured_image           │
            └──────────┬───────────────────┘
                       │
                       ▼
            ┌──────────────────────────────┐
            │    SUCCESS!                  │
            │  Redirect to Events List     │
            │  Show success message        │
            └──────────────────────────────┘
```

## 🎯 Key Decision Points

### Decision 1: Delivery Mode Selection
```
IF user selects "Face-to-Face":
   → Show F2F section only
   → Validate F2F fields as required
   → Hide Online section

IF user selects "Online":
   → Show Online section only
   → Validate Online fields as required
   → Hide F2F section

IF user selects "Hybrid":
   → Show BOTH sections
   → Validate ALL fields as required
   → User fills both F2F and Online details
```

### Decision 2: Primary Event Dates
```
IF delivery_mode = 'face_to_face':
   event.start_date = f2f_start_date
   event.end_date = f2f_end_date

IF delivery_mode = 'online':
   event.start_date = online_start_date
   event.end_date = online_end_date

IF delivery_mode = 'hybrid':
   event.start_date = MIN(f2f_start_date, online_start_date)
   event.end_date = MAX(f2f_end_date, online_end_date)
```

## 📊 Data Flow Example

### Example: Hybrid Event

**Input:**
```
delivery_mode: hybrid

F2F Data:
- f2f_start_date: 2025-03-10 09:00:00
- f2f_end_date: 2025-03-12 17:00:00
- f2f_paper_deadline: 2025-02-01
- f2f_product_deadline: 2025-02-15

Online Data:
- online_start_date: 2025-03-05 09:00:00
- online_end_date: 2025-03-14 17:00:00
- online_paper_deadline: 2025-02-10
- online_product_deadline: 2025-02-25
- online_platform_url: https://zoom.us/j/123
```

**Stored in Database:**
```sql
INSERT INTO events (
  delivery_mode,
  start_date,              -- 2025-03-05 (earliest)
  end_date,                -- 2025-03-14 (latest)
  f2f_start_date,
  f2f_end_date,
  f2f_paper_deadline,
  f2f_product_deadline,
  online_start_date,
  online_end_date,
  online_paper_deadline,
  online_product_deadline,
  online_platform_url,
  ...
) VALUES (
  'hybrid',
  '2025-03-05 09:00:00',
  '2025-03-14 17:00:00',
  '2025-03-10 09:00:00',
  '2025-03-12 17:00:00',
  '2025-02-01',
  '2025-02-15',
  '2025-03-05 09:00:00',
  '2025-03-14 17:00:00',
  '2025-02-10',
  '2025-02-25',
  'https://zoom.us/j/123',
  ...
);
```

## 🎨 UI State Management

### JavaScript Logic:
```javascript
// When delivery mode changes
deliveryModeInputs.addEventListener('change', function() {
    const mode = this.value;
    
    switch(mode) {
        case 'face_to_face':
            f2fSection.show();
            onlineSection.hide();
            setRequired('f2f', true);
            setRequired('online', false);
            break;
            
        case 'online':
            f2fSection.hide();
            onlineSection.show();
            setRequired('f2f', false);
            setRequired('online', true);
            break;
            
        case 'hybrid':
            f2fSection.show();
            onlineSection.show();
            setRequired('f2f', true);
            setRequired('online', true);
            break;
    }
});
```

## ✅ Validation Flow

```
Form Submit
    │
    ▼
Is delivery_mode selected?
    │
    ├─ No → Error: "Please select delivery mode"
    │
    └─ Yes → Continue
         │
         ▼
    Is mode = 'face_to_face' OR 'hybrid'?
         │
         ├─ Yes → Validate F2F fields
         │         ├─ f2f_start_date required
         │         ├─ f2f_end_date required
         │         └─ f2f times required
         │
         ▼
    Is mode = 'online' OR 'hybrid'?
         │
         ├─ Yes → Validate Online fields
         │         ├─ online_start_date required
         │         ├─ online_end_date required
         │         ├─ online times required
         │         └─ platform_url optional
         │
         ▼
    All validations passed?
         │
         ├─ Yes → Save to database → Success!
         │
         └─ No → Show errors → User fixes → Resubmit
```

---

**This visual guide shows the complete flow from event creation to database storage!** 🎉
