# Testing Cloudinary Integration

## ✅ What's Been Updated

### Controllers Updated:
1. ✅ **EventController** - Event posters/featured images
2. ✅ **PaperSubmissionController** - PDF paper uploads  
3. ✅ **EventRegistrationObserver** - QR code generation

All file uploads now go to Cloudinary instead of local storage!

---

## 🧪 How to Test

### 1. Test Event Image Upload

1. Log in as an organizer
2. Go to **Create Event** or **Edit Event**
3. Upload a featured image
4. After saving, check the database:
   ```sql
   SELECT id, title, featured_image, featured_image_public_id 
   FROM events 
   ORDER BY id DESC 
   LIMIT 1;
   ```
5. The `featured_image` should be a Cloudinary URL like:
   `https://res.cloudinary.com/your-cloud-name/image/upload/v123456/events/posters/event_123_poster.jpg`

### 2. Test Paper Submission (PDF)

1. Log in as a regular user
2. Register for a conference event
3. Get approved (you may need to approve yourself from organizer panel)
4. Submit a paper with PDF
5. Check database:
   ```sql
   SELECT id, title, paper_file_path, file_public_id 
   FROM paper_submissions 
   ORDER BY id DESC 
   LIMIT 1;
   ```
6. The `paper_file_path` should be a Cloudinary URL

### 3. Test QR Code Generation

1. As organizer, approve a registration
2. Check the `event_registrations` table:
   ```sql
   SELECT id, qr_code, qr_image_path, qr_code_public_id 
   FROM event_registrations 
   WHERE qr_code IS NOT NULL 
   ORDER BY id DESC 
   LIMIT 1;
   ```
3. The `qr_image_path` should be a Cloudinary URL
4. Copy the URL and open it in browser - you should see the QR code image

### 4. Test Image Display

Visit event pages and verify images are loading correctly from Cloudinary URLs.

---

## 🔍 Verify in Cloudinary Dashboard

1. Go to https://cloudinary.com
2. Log in to your account
3. Go to **Media Library**
4. You should see folders:
   - `events/posters/` - Event images
   - `papers/` - PDF submissions
   - `qr-codes/registrations/` - QR code images

---

## ⚠️ Important Notes

### Database Changes
The migration added these columns:
- `events.featured_image_public_id`
- `event_registrations.qr_code_public_id`
- `paper_submissions.file_public_id`

These store Cloudinary's `public_id` for easier file management.

### Old Local Files
Your existing local files in `storage/app/public/` are NOT automatically migrated. They will still work for old records. New uploads will go to Cloudinary.

### Sharing with Your Friend
Once both of you use the same Cloudinary credentials:
1. Both can upload files
2. Both can view files
3. Database has URLs - no need to sync files!

---

## 🐛 Troubleshooting

### Images not uploading?
Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

### Error: "Invalid signature"?
- Verify `.env` has correct CLOUDINARY_URL
- No spaces in the URL
- Format: `cloudinary://api_key:api_secret@cloud_name`

### QR codes not generating?
- Check `storage/app/temp/` directory exists and is writable
- Check Laravel logs for errors

---

## 📊 Monitor Usage

Keep track of your Cloudinary usage:
- **Dashboard** > **Dashboard** shows storage, bandwidth, transformations
- Free tier: 25GB storage, 25GB bandwidth/month
- Set up email alerts in Cloudinary for 80% usage

---

## 🔄 Next Steps

1. ✅ Test all three upload types
2. Share Cloudinary credentials with your friend
3. Both test uploading and viewing files
4. Update any remaining controllers if needed:
   - MaterialController (event materials)
   - Jury qualification documents
   - User avatars (if you have them)

---

Need help? Check [CLOUDINARY_SETUP_GUIDE.md](CLOUDINARY_SETUP_GUIDE.md) for more details!
