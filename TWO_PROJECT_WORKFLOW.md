# Two-Project Migration Strategy

## Workflow for Shared Database

### Initial Setup (One Time)

1. **You (eventmanagement project)**:
   ```bash
   # Run your existing migrations to create all tables
   php artisan migrate
   php artisan db:seed  # Optional: Add sample data
   ```

2. **Your Friend (signupgo project)**:
   ```bash
   # Copy your migration files to their project
   # Then run migrations (will detect existing tables)
   php artisan migrate
   ```

### Ongoing Development

#### When YOU create new migrations:

1. **Create migration in your project**:
   ```bash
   php artisan make:migration add_user_profiles_table
   ```

2. **Run migration**:
   ```bash
   php artisan migrate
   ```

3. **Share migration file**:
   - Copy the new migration file to your friend
   - Or commit to shared Git repository
   - Or use shared folder/drive

4. **Your friend applies**:
   ```bash
   # Copy migration file to database/migrations/
   php artisan migrate  # Only runs new migrations
   ```

#### When YOUR FRIEND creates new migrations:

1. **Friend creates migration in signupgo**:
   ```bash
   php artisan make:migration add_event_registrations_table
   ```

2. **Friend runs migration**:
   ```bash
   php artisan migrate
   ```

3. **Friend shares migration with you**:
   - You copy migration file to your project
   - You run: `php artisan migrate`

## Git-Based Sharing (Recommended)

### Option 1: Shared Repository for Migrations

Create a separate Git repo just for database:
```bash
# Create new repo: "eventsphere-database"
# Structure:
eventsphere-database/
├── migrations/
├── models/
├── seeders/
└── README.md

# Both projects add as submodule:
git submodule add https://github.com/yourname/eventsphere-database database-shared
```

### Option 2: Cross-Project Sharing

Add your friend's repo as remote:
```bash
# In your eventmanagement project
git remote add signupgo https://github.com/friend/signupgo.git

# Pull migration files when needed
git fetch signupgo
git checkout signupgo/main -- database/migrations/new_migration.php
```

## Model Sharing Strategy

### Shared Models
Both projects need same models for shared tables:

**Tables You Both Need:**
- `users` (if sharing user accounts)
- `events` 
- `event_categories`
- `event_registrations` (friend's responsibility)
- `event_organizers` (your responsibility)

### Model File Sharing:
```bash
# Copy these model files between projects:
- app/Models/Event.php
- app/Models/EventCategory.php  
- app/Models/User.php (if sharing users)
```

## Database Naming Convention

Use prefixes to avoid conflicts:

**Your Tables (eventmanagement):**
- `admin_*` - Admin-specific tables
- `organizer_*` - Organizer-specific tables
- `events` - Shared table

**Friend's Tables (signupgo):**  
- `user_*` - User-specific tables
- `registration_*` - Registration-specific tables
- `events` - Shared table (same as yours)

## Conflict Resolution

### Migration Conflicts:
1. **Coordinate timing**: Discuss before creating migrations
2. **Use descriptive names**: Include your initials in migration names
3. **Test locally first**: Always test migrations before sharing

### Model Conflicts:
1. **Same table, different methods**: OK - each project can have additional methods
2. **Same method, different logic**: Problem - coordinate the implementation
3. **Different relationships**: Plan together which relationships each project handles

## Example Workflow

### Week 1: You create events system
```bash
# Your migrations:
2025_11_01_120000_create_events_table.php
2025_11_01_120001_create_event_categories_table.php

# Share with friend:
Copy files → Friend runs php artisan migrate
```

### Week 2: Friend creates registration system  
```bash
# Friend's migrations:
2025_11_08_140000_create_event_registrations_table.php
2025_11_08_140001_create_user_profiles_table.php

# Friend shares with you:
Copy files → You run php artisan migrate
```

### Week 3: You add organizer analytics
```bash
# Your new migration:
2025_11_15_160000_add_analytics_to_events_table.php

# Share with friend → Friend migrates
```

This ensures both projects stay synchronized while allowing independent development!