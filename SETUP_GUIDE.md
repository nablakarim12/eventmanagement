# ConVex Project Setup Guide

## For Collaborative Development

This guide will help your teammate set up the ConVex database and continue development on the user-facing features.

## 🗄️ Database Sharing Options

### Option 1: Migration-Based Sharing (Recommended)

**What you need to share:**
```
📁 database/
  📁 migrations/ (all migration files)
  📁 seeders/ (optional - for sample data)
📁 app/Models/ (all model files)
📄 .env.example (database configuration template)
📄 DATABASE_SCHEMA.md (this documentation)
```

**Setup steps for your teammate:**

1. **Copy Files**
   ```bash
   # Copy these folders to their project:
   - database/migrations/* → database/migrations/
   - app/Models/* → app/Models/
   - database/seeders/* → database/seeders/ (optional)
   ```

2. **Database Configuration**
   ```bash
   # Copy .env.example to .env
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   
   # Configure database settings in .env
   ```

3. **Run Migrations**
   ```bash
   # Create database tables
   php artisan migrate
   
   # Optional: Run seeders for sample data
   php artisan db:seed
   ```

### Option 2: Database Dump Sharing

**Create database dump:**
```bash
# For PostgreSQL
pg_dump --host=localhost --username=your_username --dbname=eventmanagement --file=convex_dump.sql

# For MySQL
mysqldump -u username -p eventmanagement > convex_dump.sql
```

**Your teammate imports:**
```bash
# PostgreSQL
psql --host=localhost --username=username --dbname=eventmanagement < convex_dump.sql

# MySQL
mysql -u username -p eventmanagement < convex_dump.sql
```

### Option 3: Shared Development Database

**Use a shared cloud database:**
- Both developers connect to the same PostgreSQL/MySQL instance
- Use services like:
  - Supabase (PostgreSQL)
  - PlanetScale (MySQL)
  - AWS RDS
  - Railway
  - Heroku Postgres

## 🔧 Development Workflow

### Git Collaboration for Database

1. **Always commit migrations:**
   ```bash
   git add database/migrations/*
   git commit -m "Add event management migrations"
   ```

2. **Share new models:**
   ```bash
   git add app/Models/*
   git commit -m "Add Event and EventOrganizer models"
   ```

3. **Your teammate pulls and migrates:**
   ```bash
   git pull origin main
   php artisan migrate
   ```

## 📋 Current Database Tables

### Completed Tables:
- ✅ `admins` - Admin user accounts
- ✅ `event_organizers` - Event organizer profiles  
- ✅ `event_categories` - Event categorization
- ✅ `events` - Complete event information

### Tables Your Friend May Need:
- 🔲 `users` - Regular user accounts (Laravel default)
- 🔲 `event_registrations` - User event registrations
- 🔲 `event_reviews` - User event reviews/ratings
- 🔲 `event_favorites` - User favorite events
- 🔲 `notifications` - User notifications
- 🔲 `payments` - Payment records (if needed)

## 🚀 Getting Started Checklist

### For Your Teammate:

1. **Environment Setup**
   - [ ] Copy .env.example to .env
   - [ ] Configure database credentials
   - [ ] Run `php artisan key:generate`

2. **Database Setup**
   - [ ] Copy migration files
   - [ ] Copy model files  
   - [ ] Run `php artisan migrate`
   - [ ] Run `php artisan db:seed` (optional)

3. **Test Database Access**
   - [ ] Check admin login works
   - [ ] Verify event organizer registration
   - [ ] Confirm events can be created

4. **Start User Development**
   - [ ] Create user authentication (if not using Laravel Breeze/Jetstream)
   - [ ] Build public event listings
   - [ ] Implement event registration system

## 🛠️ Helpful Commands

```bash
# Check current migrations status
php artisan migrate:status

# Reset and re-run all migrations (WARNING: destroys data)
php artisan migrate:fresh

# Reset and re-run with seeders
php artisan migrate:fresh --seed

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

## 🔗 API Endpoints Available

Your friend can use these for user features:

- `GET /api/events` - List all published events
- `GET /api/events/{slug}` - Get event details
- `GET /api/categories` - List event categories
- Authentication routes for organizers already implemented

## 📞 Support

If your teammate encounters issues:
1. Check DATABASE_SCHEMA.md for table structure
2. Verify .env database configuration
3. Run `php artisan migrate:status` to check migrations
4. Check Laravel logs in `storage/logs/laravel.log`

---
**Note**: Make sure to coordinate when making database schema changes to avoid conflicts!