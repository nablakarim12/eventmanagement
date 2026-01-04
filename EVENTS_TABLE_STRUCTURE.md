# Events Table - Organized Column Structure

## Overview
The events table supports **3 event types**:
1. **Standard Events** - Traditional events (category-based)
2. **Innovation Events** - Competitions with jury evaluation
3. **Conference Events** - Academic conferences with paper submissions

---

## Column Organization

### 1. CORE IDENTIFICATION & RELATIONSHIPS
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `id` | bigint | Primary key | All |
| `organizer_id` | bigint | Foreign key to event_organizers | All |
| `category_id` | bigint | Foreign key to event_categories | All |
| `delivery_mode` | enum | face_to_face, online, hybrid | Innovation, Conference |
| `innovation_categories` | json | Array of innovation categories | Innovation only |
| `conference_categories` | json | Array of conference categories | Conference only |

---

### 2. BASIC EVENT INFORMATION
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `title` | varchar | Event name | All |
| `description` | text | Full description | All |
| `short_description` | text | Brief description | All |
| `slug` | varchar | URL-friendly identifier | All |
| `status` | enum | draft, published, cancelled, completed | All |
| `featured_image` | varchar | Main event image | All |
| `gallery_images` | json | Additional images | All |

---

### 3. LOCATION INFORMATION
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `venue_name` | varchar | Event venue name | All |
| `venue_address` | text | Full address | All |
| `city` | varchar | City name | All |
| `state` | varchar | State/Province | All |
| `country` | varchar | Country name | All |
| `latitude` | decimal | GPS coordinate | All |
| `longitude` | decimal | GPS coordinate | All |

---

### 4. REGISTRATION & PARTICIPANTS
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `max_participants` | int | Maximum capacity | All |
| `current_participants` | int | Current count | All |
| `registration_fee` | decimal | Fee amount | All |
| `currency` | varchar(3) | Currency code (MYR, USD, etc.) | All |
| `is_free` | boolean | Free event flag | All |
| `registration_deadline` | datetime | Registration cutoff | **Standard only** |
| `requires_approval` | boolean | Manual approval needed | All |
| `is_public` | boolean | Public visibility | All |
| `allow_waitlist` | boolean | Waitlist enabled | All |

---

### 5. FACE-TO-FACE (F2F) EVENT DETAILS

#### 5A. F2F Dates & Times
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `f2f_start_date` | datetime | F2F event start | Innovation, Conference |
| `f2f_end_date` | datetime | F2F event end | Innovation, Conference |
| `f2f_start_time` | time | Start time | Innovation, Conference |
| `f2f_end_time` | time | End time | Innovation, Conference |

#### 5B. F2F Innovation Deadlines
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `f2f_paper_deadline` | date | Paper submission | Innovation |
| `f2f_product_deadline` | date | Product submission | Innovation |
| `f2f_abstract_deadline` | date | Abstract submission | Innovation |
| `f2f_acceptance_date` | date | Acceptance notification | Innovation |
| `f2f_jury_deadline` | datetime | Jury evaluation | Innovation |
| `f2f_jury_registration_deadline` | datetime | Jury registration cutoff | Innovation |
| `f2f_payment_deadline` | datetime | Payment cutoff | Innovation |
| `f2f_extension_count` | int | Number of extensions | Innovation |
| `f2f_extended_acceptance_dates` | json | Extended acceptance dates | Innovation |
| `f2f_extended_paper_deadlines` | json | Extended paper deadlines | Innovation |

#### 5C. F2F Conference Deadlines
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `f2f_reviewer_registration_deadline` | datetime | Reviewer registration | Conference |
| `f2f_paper_submission_deadline` | datetime | Paper submission | Conference |
| `f2f_review_deadline` | datetime | Review completion | Conference |
| `f2f_acceptance_notification_date` | datetime | Acceptance notification | Conference |
| `f2f_payment_deadline` | datetime | Payment cutoff | Conference |

---

### 6. ONLINE EVENT DETAILS

#### 6A. Online Dates & Platform
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `online_start_date` | datetime | Online event start | Innovation, Conference |
| `online_end_date` | datetime | Online event end | Innovation, Conference |
| `online_start_time` | time | Start time | Innovation, Conference |
| `online_end_time` | time | End time | Innovation, Conference |
| `online_platform_url` | varchar | Meeting link (Zoom, Teams, etc.) | Innovation, Conference |

#### 6B. Online Innovation Deadlines
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `online_paper_deadline` | date | Paper submission | Innovation |
| `online_product_deadline` | date | Product submission | Innovation |
| `online_abstract_deadline` | date | Abstract submission | Innovation |
| `online_acceptance_date` | date | Acceptance notification | Innovation |
| `online_jury_deadline` | datetime | Jury evaluation | Innovation |
| `online_jury_registration_deadline` | datetime | Jury registration cutoff | Innovation |
| `online_payment_deadline` | datetime | Payment cutoff | Innovation |
| `online_extension_count` | int | Number of extensions | Innovation |
| `online_extended_acceptance_dates` | json | Extended acceptance dates | Innovation |
| `online_extended_paper_deadlines` | json | Extended paper deadlines | Innovation |

#### 6C. Online Conference Deadlines
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `online_reviewer_registration_deadline` | datetime | Reviewer registration | Conference |
| `online_paper_submission_deadline` | datetime | Paper submission | Conference |
| `online_review_deadline` | datetime | Review completion | Conference |
| `online_acceptance_notification_date` | datetime | Acceptance notification | Conference |
| `online_payment_deadline` | datetime | Payment cutoff | Conference |

---

### 7. CONFERENCE-SPECIFIC SETTINGS
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `min_abstract_words` | int | Minimum abstract length | Conference |
| `min_keywords` | int | Minimum keywords required | Conference |
| `max_paper_size_mb` | int | Max file size | Conference |
| `paper_format_guidelines` | text | Formatting instructions | Conference |
| `allow_multiple_submissions` | boolean | Allow multiple papers | Conference |
| `min_reviewers_per_paper` | int | Required reviewers | Conference |

---

### 8. ATTENDANCE & CERTIFICATES
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `min_attendance_hours` | decimal | Required attendance | All |
| `auto_generate_certificates` | boolean | Auto certificate generation | All |
| `requires_attendance` | boolean | Attendance tracking required | All |

---

### 9. ADDITIONAL INFORMATION
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `requirements` | json | Event requirements | All |
| `tags` | json | Search tags | All |
| `contact_email` | varchar | Contact email | All |
| `contact_phone` | varchar | Contact phone | All |
| `website_url` | varchar | Event website | All |
| `views` | int | Page views counter | All |
| `budget` | decimal | Event budget | All |

---

### 10. SYSTEM FIELDS
| Column | Type | Description | Used By |
|--------|------|-------------|---------|
| `created_at` | timestamp | Record creation time | All |
| `updated_at` | timestamp | Last update time | All |

---

## Column Count Summary

| Event Type | Columns Used | Shared | Specific |
|------------|--------------|--------|----------|
| **Standard Events** | ~45 | 45 | 0 |
| **Innovation Events** | ~75 | 45 | 30 |
| **Conference Events** | ~70 | 45 | 25 |

**Total Columns in Table:** 91

---

## Removed Columns (Cleaned Up)

The following legacy columns were removed as they were empty and replaced by delivery-mode specific versions:

### ❌ Dropped Empty Columns
- `reviewer_registration_deadline` → Replaced by `f2f_reviewer_registration_deadline` and `online_reviewer_registration_deadline`
- `paper_submission_deadline` → Replaced by `f2f_paper_submission_deadline` and `online_paper_submission_deadline`
- `review_deadline` → Replaced by `f2f_review_deadline` and `online_review_deadline`
- `acceptance_notification_date` → Replaced by `f2f_acceptance_notification_date` and `online_acceptance_notification_date`

### ❌ Migrated & Dropped
- `start_date` → Migrated to `f2f_start_date`
- `end_date` → Migrated to `f2f_end_date`
- `start_time` → Migrated to `f2f_start_time`
- `end_time` → Migrated to `f2f_end_time`

---

## Event Type Detection Logic

### Standard Event
```php
$event->delivery_mode === null && 
$event->category_id != null && 
$event->category->name !== 'Innovation'
```

### Innovation Event
```php
$event->category->name === 'Innovation' OR
$event->innovation_categories !== null
```

### Conference Event
```php
$event->delivery_mode !== null && 
in_array($event->delivery_mode, ['face_to_face', 'online', 'hybrid']) &&
$event->conference_categories !== null
```

---

## Usage Guidelines

### Creating Standard Event
Use columns:
- Core fields (title, description, category_id, etc.)
- Location fields
- `registration_deadline`
- No delivery_mode

### Creating Innovation Event
Use columns:
- Core fields + `delivery_mode` + `innovation_categories`
- F2F fields if mode is `face_to_face` or `hybrid`
- Online fields if mode is `online` or `hybrid`
- Innovation-specific deadlines

### Creating Conference Event
Use columns:
- Core fields + `delivery_mode` + `conference_categories`
- F2F fields if mode is `face_to_face` or `hybrid`
- Online fields if mode is `online` or `hybrid`
- Conference-specific deadlines and settings

---

## Notes

1. **Hybrid Events**: Use BOTH f2f_* and online_* columns
2. **Nullable Fields**: Most deadline fields are nullable to support flexible event configurations
3. **JSON Fields**: Categories, extensions, and arrays stored as JSON for flexibility
4. **Currency**: Default is 'MYR', can be changed per event
5. **Payment Deadlines**: Available for both Innovation and Conference events in all delivery modes
