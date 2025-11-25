# 📂 FILE PLACEMENT GUIDE FOR SIGNUPGO PROJECT

## 🎯 Where to Put Each File

### STEP 1: Copy Migration Files

**From your received package:**
```
SignupGo_Database_Package/migrations/
├── 2014_10_12_000000_create_users_table.php
├── 2014_10_12_100000_create_password_reset_tokens_table.php  
├── 2019_08_19_000000_create_failed_jobs_table.php
├── 2019_12_14_000001_create_personal_access_tokens_table.php
├── 2025_10_03_151812_create_admins_table.php
├── 2025_10_03_151857_create_event_organizers_table.php
├── 2025_10_03_151858_create_event_organizer_documents_table.php
├── 2025_10_07_145406_create_event_categories_table.php
├── 2025_10_07_145951_create_event_types_table.php
├── 2025_10_07_153123_create_event_organizer_password_resets_table.php
├── 2025_10_13_153257_create_organizer_documents_table.php
├── 2025_10_13_164619_create_jobs_table.php
└── 2025_10_27_161100_create_events_table.php
```

**Copy ALL these files to:**
```
signupgo/database/migrations/
```

### STEP 2: Copy Model Files

**From your received package:**
```
SignupGo_Database_Package/models/
├── Admin.php
├── Event.php
├── EventCategory.php
├── EventOrganizer.php
├── EventOrganizerDocument.php
├── EventType.php
├── OrganizerDocument.php
└── User.php
```

**Copy ALL these files to:**
```
signupgo/app/Models/
```

### STEP 3: Update Database Configuration

**Add these lines to your `signupgo/.env` file:**

```env
# EventSphere Shared Database
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.vlnpraogcdbtefvdhzrf
DB_PASSWORD=EventM@nagement123
```

## 🖥️ Command Line Instructions

### For Windows:
```cmd
cd C:\path\to\signupgo

# Copy migration files
xcopy "C:\path\to\SignupGo_Database_Package\migrations\*" "database\migrations\" /Y

# Copy model files  
xcopy "C:\path\to\SignupGo_Database_Package\models\*" "app\Models\" /Y
```

### For Mac/Linux:
```bash
cd /path/to/signupgo

# Copy migration files
cp /path/to/SignupGo_Database_Package/migrations/* database/migrations/

# Copy model files
cp /path/to/SignupGo_Database_Package/models/* app/Models/
```

## 🔧 Manual Copy Instructions

### If using File Explorer (Windows) or Finder (Mac):

1. **Open two windows:**
   - Window 1: `SignupGo_Database_Package` folder
   - Window 2: Your `signupgo` Laravel project folder

2. **Copy migrations:**
   - Select ALL files in `SignupGo_Database_Package/migrations/`
   - Copy and paste into `signupgo/database/migrations/`

3. **Copy models:**
   - Select ALL files in `SignupGo_Database_Package/models/`
   - Copy and paste into `signupgo/app/Models/`

4. **Edit .env:**
   - Open `signupgo/.env` in text editor
   - Add the database configuration lines from the package

## ✅ Verification Steps

After copying files, run these commands in your signupgo project:

```bash
# 1. Check if Laravel recognizes the migrations
php artisan migrate:status

# 2. Test database connection
php artisan tinker
>>> DB::connection()->getPdo()
>>> App\Models\Event::count()
>>> exit
```

## 🎯 Expected Results

### Migration Status:
You should see something like:
```
Migration name ...................................... Batch / Status
2014_10_12_000000_create_users_table ........................... Ran
2025_10_03_151812_create_admins_table .......................... Ran
2025_10_27_161100_create_events_table .......................... Ran
... (all showing "Ran")
```

### Database Test:
```bash
>>> App\Models\Event::count()
=> 5  # (or whatever number of events exist)

>>> App\Models\EventCategory::all()
=> Illuminate\Database\Eloquent\Collection {#4436
     all: [
       App\Models\EventCategory {#4437
         id: 1,
         name: "Academic Conference",
         ...
       },
       ...
     ],
   }
```

## 🚨 Common Issues & Solutions

### Issue: "Class not found" errors
**Solution:** Make sure all model files are copied to `app/Models/`

### Issue: "Table doesn't exist" errors  
**Solution:** Verify database credentials in `.env` are exactly as provided

### Issue: "Migration not found" errors
**Solution:** Ensure all migration files are copied to `database/migrations/`

### Issue: Connection refused
**Solution:** Check internet connection and database credentials

## 🎉 Success!

If all tests pass, you're ready to start building:
- User registration system
- Event browsing interface  
- Event signup functionality
- User dashboard features

You now have access to all the events and data from the eventmanagement system!