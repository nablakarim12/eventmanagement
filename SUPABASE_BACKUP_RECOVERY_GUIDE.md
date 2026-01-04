# How to Recover Data from Supabase Backups

## Important: Check Supabase Backups FIRST!

Your database is hosted on Supabase, which may have automatic backups that can restore ALL your deleted data.

---

## Step 1: Login to Supabase

1. Go to https://supabase.com
2. Login with your account credentials
3. Select your EventSphere project

---

## Step 2: Check for Backups

### For Free Tier Users:
- Navigate to **Database** → **Backups**
- Free tier has daily backups for the last 7 days
- You may be able to restore to yesterday's backup

### For Pro/Team Users:
- Navigate to **Database** → **Backups**
- Pro tier has point-in-time recovery for up to 7 days
- Team tier has point-in-time recovery for up to 30 days
- You can restore to ANY point before the deletion

---

## Step 3: Restore from Backup

### Option A: Point-in-Time Recovery (Pro/Team only)

1. In Supabase Dashboard, go to **Database** → **Backups**
2. Click on **Point-in-time Recovery**
3. Select a date/time BEFORE your friend deleted the data
   - Example: If deletion happened today at 2:00 PM
   - Select today at 1:00 PM or earlier
4. Click **Restore**
5. Wait for the restoration process (may take several minutes)
6. Your data will be restored!

### Option B: Daily Backup Restoration (Free/Pro/Team)

1. In Supabase Dashboard, go to **Database** → **Backups**
2. Look at the list of daily backups
3. Select the most recent backup BEFORE the deletion
4. Click **Restore** next to that backup
5. Confirm the restoration
6. Wait for completion

---

## Step 4: Verify Data Restoration

After restoration, run this command to verify:

```bash
php check_database_status.php
```

You should see all your data restored:
- Events
- Registrations
- Organizers
- Papers
- Reviews
- etc.

---

## Alternative: Export and Download Backup

If you want to download a backup file:

1. In Supabase Dashboard, go to **Database**
2. Click on **Backups**
3. Find the backup you want
4. Click **Download** or **Export**
5. Save the `.sql` file to your computer
6. You can restore it manually later if needed

### To Restore from Downloaded SQL File:

```bash
# Using psql command line
psql -h aws-1-ap-southeast-1.pooler.supabase.com -p 6543 -U postgres.vlnpraogcdbtefvdhzrf -d postgres -f backup.sql
```

---

## What If There Are No Backups?

If Supabase doesn't have backups available:

1. **Check your email** - Supabase sends backup notifications
2. **Contact Supabase Support**:
   - Go to https://supabase.com/support
   - Explain the situation
   - They may have additional backup options
   - They're usually very helpful!

3. **Check your local machine**:
   - Look for any `.sql` files you may have exported before
   - Check your Downloads folder
   - Check for any database export scripts

4. **Start fresh** (if no backups exist):
   - You've already recovered the admin account ✅
   - Event categories and types are restored ✅
   - You'll need to recreate events and registrations manually

---

## Supabase Support Contacts

- **Documentation**: https://supabase.com/docs/guides/platform/backups
- **Support**: https://supabase.com/support
- **Community**: https://github.com/supabase/supabase/discussions

---

## Prevention for the Future

Once you recover your data (or start fresh):

### 1. Enable Automatic Backups
- Already enabled by default in Supabase
- But check the retention period

### 2. Create Manual Backups Weekly
Create a script to export database weekly:

```bash
# Create a file: weekly_backup.bat
pg_dump -h aws-1-ap-southeast-1.pooler.supabase.com -p 6543 -U postgres.vlnpraogcdbtefvdhzrf -d postgres > backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%.sql
```

### 3. Store Backups in Multiple Places
- Keep local copies
- Upload to Google Drive/Dropbox
- Store on external hard drive

### 4. Restrict Database Access
- Don't share database credentials
- Use environment variables
- Create separate accounts for different team members

---

## Quick Action Checklist

- [ ] Login to Supabase
- [ ] Navigate to Database → Backups
- [ ] Check available backups
- [ ] Identify backup from before deletion
- [ ] Restore from backup
- [ ] Verify data using `php check_database_status.php`
- [ ] Change all passwords
- [ ] Set up future backup procedures

---

**DO THIS NOW:**
1. Open https://supabase.com in your browser
2. Login and check for backups
3. Restore if available
4. Come back here if you need help

Good luck! 🍀
