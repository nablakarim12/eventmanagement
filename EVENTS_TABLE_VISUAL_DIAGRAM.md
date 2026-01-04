# Events Table - Visual Structure Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                            EVENTS TABLE (85 columns)                        │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────┐
│ 📦 SHARED COLUMNS (37) - Used by ALL event types                           │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  🆔 Core (6)                                                                │
│  ├─ id, organizer_id, category_id                                          │
│  ├─ delivery_mode [Innovation/Conference only]                             │
│  ├─ innovation_categories [Innovation only]                                │
│  └─ conference_categories [Conference only]                                │
│                                                                             │
│  📝 Basic Info (8)                                                          │
│  ├─ title, description, short_description                                  │
│  ├─ slug, status                                                           │
│  └─ featured_image, gallery_images                                         │
│                                                                             │
│  📍 Location (9)                                                            │
│  ├─ venue_name, venue_address                                              │
│  ├─ city, state, country                                                   │
│  └─ latitude, longitude                                                    │
│                                                                             │
│  👥 Registration (10)                                                       │
│  ├─ max_participants, current_participants                                 │
│  ├─ registration_fee, currency, is_free                                    │
│  ├─ registration_deadline [Standard events only]                           │
│  ├─ payment_deadline, requires_approval                                    │
│  ├─ is_public, allow_waitlist                                              │
│                                                                             │
│  ℹ️ Additional (9)                                                          │
│  ├─ requirements, tags                                                     │
│  ├─ contact_email, contact_phone, website_url                              │
│  ├─ views, budget                                                          │
│  ├─ min_attendance_hours, auto_generate_certificates, requires_attendance  │
│                                                                             │
│  ⏰ System (2)                                                              │
│  └─ created_at, updated_at                                                 │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘


                                    ┌──────────┐
                                    │ delivery │
                                    │   mode   │
                                    └────┬─────┘
                                         │
                    ┌────────────────────┼────────────────────┐
                    │                    │                    │
                    ▼                    ▼                    ▼
          
┌─────────────────────────┐  ┌─────────────────────────┐  ┌─────────────────────────┐
│   FACE-TO-FACE (F2F)    │  │         ONLINE          │  │    HYBRID (BOTH)        │
│      15-20 columns      │  │      15-20 columns      │  │      30-40 columns      │
└─────────────────────────┘  └─────────────────────────┘  └─────────────────────────┘
│                         │  │                         │  │                         │
│  📅 Dates & Times       │  │  📅 Dates & Times       │  │  Uses both F2F and      │
│  ├─ f2f_start_date      │  │  ├─ online_start_date   │  │  Online columns         │
│  ├─ f2f_end_date        │  │  ├─ online_end_date     │  │                         │
│  ├─ f2f_start_time      │  │  ├─ online_start_time   │  │                         │
│  └─ f2f_end_time        │  │  └─ online_end_time     │  │                         │
│                         │  │                         │  │                         │
│  🌐 Platform            │  │  🌐 Platform            │  │                         │
│  └─ (Physical venue)    │  │  └─ online_platform_url │  │                         │
│                         │  │                         │  │                         │
└─────────────────────────┘  └─────────────────────────┘  └─────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────┐
│ 🏆 INNOVATION EVENTS - Additional 30 columns                                │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ FACE-TO-FACE MODE (15 columns)                                      │   │
│  ├─────────────────────────────────────────────────────────────────────┤   │
│  │ 📝 Submission Deadlines                                             │   │
│  │ ├─ f2f_paper_deadline                                               │   │
│  │ ├─ f2f_product_deadline                                             │   │
│  │ └─ f2f_abstract_deadline                                            │   │
│  │                                                                     │   │
│  │ ✅ Acceptance & Review                                              │   │
│  │ ├─ f2f_acceptance_date                                              │   │
│  │ ├─ f2f_jury_deadline                                                │   │
│  │ └─ f2f_jury_registration_deadline                                   │   │
│  │                                                                     │   │
│  │ 💰 Payment                                                          │   │
│  │ └─ f2f_payment_deadline                                             │   │
│  │                                                                     │   │
│  │ 📊 Extensions (Max 5)                                               │   │
│  │ ├─ f2f_extension_count                                              │   │
│  │ ├─ f2f_extended_acceptance_dates (JSON)                             │   │
│  │ └─ f2f_extended_paper_deadlines (JSON)                              │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ ONLINE MODE (15 columns)                                            │   │
│  ├─────────────────────────────────────────────────────────────────────┤   │
│  │ 📝 Submission Deadlines                                             │   │
│  │ ├─ online_paper_deadline                                            │   │
│  │ ├─ online_product_deadline                                          │   │
│  │ └─ online_abstract_deadline                                         │   │
│  │                                                                     │   │
│  │ ✅ Acceptance & Review                                              │   │
│  │ ├─ online_acceptance_date                                           │   │
│  │ ├─ online_jury_deadline                                             │   │
│  │ └─ online_jury_registration_deadline                                │   │
│  │                                                                     │   │
│  │ 💰 Payment                                                          │   │
│  │ └─ online_payment_deadline                                          │   │
│  │                                                                     │   │
│  │ 📊 Extensions (Max 5)                                               │   │
│  │ ├─ online_extension_count                                           │   │
│  │ ├─ online_extended_acceptance_dates (JSON)                          │   │
│  │ └─ online_extended_paper_deadlines (JSON)                           │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────┐
│ 🎓 CONFERENCE EVENTS - Additional 26 columns                                │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ FACE-TO-FACE MODE (10 columns)                                      │   │
│  ├─────────────────────────────────────────────────────────────────────┤   │
│  │ 👥 Reviewer & Submission                                            │   │
│  │ ├─ f2f_reviewer_registration_deadline                               │   │
│  │ └─ f2f_paper_submission_deadline                                    │   │
│  │                                                                     │   │
│  │ ✅ Review Process                                                   │   │
│  │ ├─ f2f_review_deadline                                              │   │
│  │ └─ f2f_acceptance_notification_date                                 │   │
│  │                                                                     │   │
│  │ 💰 Payment                                                          │   │
│  │ └─ f2f_payment_deadline                                             │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ ONLINE MODE (10 columns)                                            │   │
│  ├─────────────────────────────────────────────────────────────────────┤   │
│  │ 👥 Reviewer & Submission                                            │   │
│  │ ├─ online_reviewer_registration_deadline                            │   │
│  │ └─ online_paper_submission_deadline                                 │   │
│  │                                                                     │   │
│  │ ✅ Review Process                                                   │   │
│  │ ├─ online_review_deadline                                           │   │
│  │ └─ online_acceptance_notification_date                              │   │
│  │                                                                     │   │
│  │ 💰 Payment                                                          │   │
│  │ └─ online_payment_deadline                                          │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ CONFERENCE SETTINGS (6 columns - Shared across modes)               │   │
│  ├─────────────────────────────────────────────────────────────────────┤   │
│  │ 📄 Paper Requirements                                               │   │
│  │ ├─ min_abstract_words                                               │   │
│  │ ├─ min_keywords                                                     │   │
│  │ ├─ max_paper_size_mb                                                │   │
│  │ └─ paper_format_guidelines                                          │   │
│  │                                                                     │   │
│  │ 👥 Review Settings                                                  │   │
│  │ ├─ allow_multiple_submissions                                       │   │
│  │ └─ min_reviewers_per_paper                                          │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────┐
│ 📊 COLUMN SUMMARY                                                           │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  Event Type         │ Total Columns │ Shared │ Specific │ Typical Usage   │
│  ───────────────────┼───────────────┼────────┼──────────┼──────────────── │
│  Standard Events    │      37       │   37   │     0    │ Workshops       │
│  Innovation F2F     │      52       │   37   │    15    │ Hackathons      │
│  Innovation Online  │      52       │   37   │    15    │ Virtual Comp    │
│  Innovation Hybrid  │      67       │   37   │    30    │ Full Innovation │
│  Conference F2F     │      53       │   37   │    16    │ Symposiums      │
│  Conference Online  │      53       │   37   │    16    │ Virtual Conf    │
│  Conference Hybrid  │      63       │   37   │    26    │ Full Conference │
│                                                                             │
│  Maximum Possible   │      85       │   37   │    48    │ (All fields)    │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────────────────┐
│ 🗂️ COLUMN ORGANIZATION LEGEND                                               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  Prefix        │ Purpose                    │ Example                      │
│  ─────────────┼────────────────────────────┼───────────────────────────── │
│  (none)       │ Shared by all events       │ title, status, venue_name    │
│  f2f_         │ Face-to-face events only   │ f2f_start_date               │
│  online_      │ Online events only         │ online_platform_url          │
│  *_categories │ Event type indicator       │ innovation_categories        │
│  min_/max_    │ Validation limits          │ min_abstract_words           │
│  is_/allow_   │ Boolean flags              │ is_free, allow_waitlist      │
│  requires_    │ Requirement flags          │ requires_approval            │
│  auto_        │ Automation flags           │ auto_generate_certificates   │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Color-Coded Field Groups (for reference)

- **🔵 Blue (Face-to-Face)**: All `f2f_*` prefixed columns
- **🟢 Green (Online)**: All `online_*` prefixed columns  
- **🟣 Purple (Shared)**: Non-prefixed columns used by all events
- **🟠 Orange (Event-Specific)**: Categories, settings unique to event type

---

## Data Flow Example

```
User Creates Event
       │
       ├─ Selects "Innovation" category
       │  └─> Sets: category_id, innovation_categories
       │
       ├─ Chooses delivery_mode = "hybrid"
       │  └─> Enables: Both F2F and Online columns
       │
       ├─ Fills F2F Section
       │  └─> Populates: f2f_start_date, f2f_paper_deadline, etc.
       │
       ├─ Fills Online Section
       │  └─> Populates: online_start_date, online_platform_url, etc.
       │
       └─ Saves Event
          └─> Database stores only populated columns
              (NULL for unused columns)
```

---

**Total Organized Columns: 85**  
**Clean Structure: ✅**  
**Well-Documented: ✅**
