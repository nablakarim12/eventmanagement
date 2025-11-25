# EventSphere Database Schema

## Database Structure Overview

This document describes the current database structure for the EventSphere application.

## Tables Created

### 1. **users** (Default Laravel Users)
- Standard Laravel user authentication table
- Used for general user accounts

### 2. **admins**
- Admin user accounts
- Fields: id, name, email, password, created_at, updated_at

### 3. **event_organizers**
- Event organizer accounts and profiles
- Fields: id, org_name, org_type, contact_person_name, contact_person_email, contact_person_phone, org_email, org_phone, org_address, org_city, org_country, org_website, org_description, status (pending/approved/rejected), password, created_at, updated_at

### 4. **event_categories**
- Event categorization
- Fields: id, name, description, created_at, updated_at

### 5. **events**
- Main events table with comprehensive event information
- Fields: id, organizer_id (FK), category_id (FK), title, description, short_description, start_date, end_date, start_time, end_time, venue_name, venue_address, city, state, country, latitude, longitude, max_participants, current_participants, registration_fee, is_free (boolean), registration_deadline, status (enum: draft/published/cancelled/completed), requires_approval (boolean), is_public (boolean), allow_waitlist (boolean), requirements (JSON), tags (JSON), contact_email, contact_phone, website_url, slug (unique), featured_image, gallery_images (JSON), created_at, updated_at

## Key Relationships

- **event_organizers** → **events** (One-to-Many)
- **event_categories** → **events** (One-to-Many)

## Authentication Guards

- `admin` - For admin users
- `event-organizer` - For event organizers  
- `web` - For regular users (default)

## Database Setup Instructions

1. Copy all migration files from `database/migrations/`
2. Copy all model files from `app/Models/`
3. Run migrations: `php artisan migrate`
4. Run seeders (if needed): `php artisan db:seed`

## Important Notes

- PostgreSQL boolean fields require proper handling (see Event model mutators)
- Event slugs must be unique
- JSON fields used for flexible data storage (requirements, tags, gallery_images)
- File uploads stored in Laravel storage system

## Sample Data

Run the seeders to get sample categories and admin users for testing.