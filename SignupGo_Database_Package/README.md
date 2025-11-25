# 📦 EventSphere Database Package for SignupGo

## 🚀 Quick Start (5 minutes setup!)

### What's in this package:
- ✅ **13 migration files** - All database tables
- ✅ **8 model files** - All Laravel models
- ✅ **Database credentials** - Ready-to-use Supabase connection
- ✅ **Setup documentation** - Step-by-step guide

---

## 🎯 STEP-BY-STEP SETUP:

### 1️⃣ Copy Migration Files
```bash
# Copy ALL files from migrations/ folder to:
signupgo/database/migrations/
```

### 2️⃣ Copy Model Files  
```bash
# Copy ALL files from models/ folder to:
signupgo/app/Models/
```

### 3️⃣ Database Configuration
Add these lines to your `signupgo/.env` file:

```env
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.vlnpraogcdbtefvdhzrf
DB_PASSWORD=EventM@nagement123
```

### 4️⃣ Test Connection
```bash
cd signupgo
php artisan migrate:status
```
✅ **Success**: You should see tables marked as "Ran"

```bash
php artisan tinker
>>> App\Models\Event::count()
>>> App\Models\EventCategory::all()
>>> exit
```
✅ **Success**: You should see actual events and categories!

---

## 🏗️ NOW YOU CAN BUILD:

### User Features You Can Create:
- 👤 **User Registration/Login** (create `users` table migration)
- 📝 **Event Registration** (create `event_registrations` table)  
- ❤️ **Favorite Events** (create `user_favorites` table)
- ⭐ **Event Reviews** (create `event_reviews` table)
- 🔔 **Notifications** (create `notifications` table)
- 💳 **Payments** (create `payments` table if needed)

### Tables Already Available:
- `events` - All events (read-only, don't modify structure)
- `event_categories` - Event categories (read-only)
- `event_organizers` - Organizer information (read-only)
- `admins` - Admin accounts (read-only)

---

## 🤝 COLLABORATION WORKFLOW:

### When YOU create new migrations:
1. Create: `php artisan make:migration create_event_registrations`
2. Run: `php artisan migrate`
3. **Share the new migration file** with your eventmanagement friend
4. They copy and run: `php artisan migrate`

### When YOUR FRIEND creates new migrations:
1. They share the migration file with you  
2. You copy it to your `database/migrations/` folder
3. You run: `php artisan migrate`

### 🚨 IMPORTANT RULES:
- ⚠️ **Same database** - changes are instant for both projects
- ⚠️ **Don't modify existing tables** - only add new ones
- ⚠️ **Coordinate before schema changes** - discuss first
- ⚠️ **Test carefully** - affects both projects

---

## ✅ SUCCESS CHECKLIST:

- [ ] Migration files copied to `database/migrations/`
- [ ] Model files copied to `app/Models/`  
- [ ] Database credentials added to `.env`
- [ ] `php artisan migrate:status` shows existing tables
- [ ] `App\Models\Event::count()` returns actual count
- [ ] Ready to build user registration system!

---

## 📞 SUPPORT:

If something doesn't work:
1. Check `.env` database credentials are exact
2. Verify all migration files copied
3. Run `php artisan config:clear`
4. Check Laravel logs: `storage/logs/laravel.log`

**You're sharing a live production database - all your changes are real and instant!**