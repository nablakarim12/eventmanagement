# Cloudinary Integration Guide

## 🎯 Overview
This guide explains how to use Cloudinary for storing images, PDFs, and QR codes in your Laravel event management system. Both you and your friend can share the same files through Cloudinary's cloud storage.

---

## 📋 Setup Instructions

### Step 1: Get Cloudinary Credentials

1. Go to [https://cloudinary.com](https://cloudinary.com)
2. Sign up for a free account
3. After logging in, go to **Dashboard**
4. Copy your credentials:
   - **Cloud Name**
   - **API Key**
   - **API Secret**

### Step 2: Configure Environment Variables

Open your `.env` file and update the Cloudinary URL:

```env
CLOUDINARY_URL=cloudinary://YOUR_API_KEY:YOUR_API_SECRET@YOUR_CLOUD_NAME
```

**Example:**
```env
CLOUDINARY_URL=cloudinary://123456789012345:abcdefghijklmnopqrstuvwxyz123456@your-cloud-name
```

### Step 3: Run Database Migration

Add the public_id columns to your database:

```bash
php artisan migrate
```

This will add columns to store Cloudinary's public IDs for easier file management.

---

## 🚀 Usage Examples

### 1. Upload Event Poster/Image

In your `EventController.php`:

```php
use App\Services\CloudinaryService;

class EventController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function store(Request $request)
    {
        // ... your existing validation ...

        $event = Event::create([...]);

        // Upload image to Cloudinary instead of local storage
        if ($request->hasFile('featured_image')) {
            $uploadResult = $this->cloudinaryService->uploadImage(
                $request->file('featured_image'),
                'events/posters',
                [
                    'public_id' => 'event_' . $event->id,
                    'transformation' => [
                        'width' => 1920,
                        'height' => 1080,
                        'crop' => 'limit'
                    ]
                ]
            );

            $event->update([
                'featured_image' => $uploadResult['secure_url'],
                'featured_image_public_id' => $uploadResult['public_id']
            ]);
        }

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event created successfully!');
    }
}
```

### 2. Upload PDF (Paper Submission)

In your `PaperSubmissionController.php`:

```php
use App\Services\CloudinaryService;

public function store(Request $request, CloudinaryService $cloudinaryService)
{
    $request->validate([
        'paper_file' => 'required|file|mimes:pdf|max:10240'
    ]);

    $uploadResult = $cloudinaryService->uploadPdf(
        $request->file('paper_file'),
        'papers/submissions'
    );

    PaperSubmission::create([
        'event_id' => $request->event_id,
        'user_id' => auth()->id(),
        'file_path' => $uploadResult['secure_url'],
        'file_public_id' => $uploadResult['public_id'],
        // ... other fields
    ]);
}
```

### 3. Upload QR Code

When generating QR codes for registrations:

```php
use App\Services\CloudinaryService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

public function generateQrCode($registration, CloudinaryService $cloudinaryService)
{
    // Generate QR code
    $qrCode = QrCode::create($registration->qr_code);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    // Save temporarily
    $tempPath = storage_path('app/temp/qr_' . $registration->id . '.png');
    $result->saveToFile($tempPath);
    
    // Upload to Cloudinary
    $uploadResult = $cloudinaryService->uploadQrCode(
        $tempPath,
        'qr_registration_' . $registration->id . '.png',
        'qr-codes/registrations'
    );
    
    // Update registration
    $registration->update([
        'qr_code_url' => $uploadResult['secure_url'],
        'qr_code_public_id' => $uploadResult['public_id']
    ]);
    
    // Delete temp file
    unlink($tempPath);
    
    return $uploadResult['secure_url'];
}
```

### 4. Display Images in Blade Views

Simply use the URL stored in your database:

```blade
{{-- Event poster --}}
@if($event->featured_image)
    <img src="{{ $event->featured_image }}" alt="{{ $event->title }}" class="img-fluid">
@endif

{{-- Thumbnail (Cloudinary generates on-the-fly) --}}
<img src="{{ $event->featured_image }}" 
     alt="{{ $event->title }}"
     class="img-thumbnail"
     style="width: 300px; height: 300px; object-fit: cover;">
```

### 5. Delete Files

When deleting an event or file:

```php
public function destroy(Event $event, CloudinaryService $cloudinaryService)
{
    // Delete image from Cloudinary
    if ($event->featured_image_public_id) {
        $cloudinaryService->delete($event->featured_image_public_id, 'image');
    }
    
    $event->delete();
    
    return redirect()->route('organizer.events.index')
        ->with('success', 'Event deleted successfully!');
}
```

---

## 🔧 CloudinaryService Methods

The `CloudinaryService` class provides these methods:

| Method | Description |
|--------|-------------|
| `uploadImage($file, $folder, $options)` | Upload image files |
| `uploadPdf($file, $folder, $options)` | Upload PDF files |
| `uploadQrCode($path, $filename, $folder)` | Upload QR code images |
| `uploadMultiple($files, $folder, $type)` | Upload multiple files |
| `delete($publicId, $resourceType)` | Delete a file from Cloudinary |
| `getImageUrl($publicId, $transformations)` | Get URL with transformations |
| `getThumbnail($publicId, $width, $height)` | Get thumbnail URL |

---

## 🎨 Image Transformations

Cloudinary can automatically optimize and transform images:

```php
// Resize and crop
$uploadResult = $cloudinaryService->uploadImage(
    $file,
    'events/posters',
    [
        'transformation' => [
            'width' => 800,
            'height' => 600,
            'crop' => 'fill',
            'gravity' => 'auto', // Smart cropping
            'quality' => 'auto:good', // Auto quality
            'format' => 'auto' // Auto format (WebP for supported browsers)
        ]
    ]
);

// Create circular avatar
$avatarUrl = $cloudinaryService->getImageUrl($publicId, [
    'width' => 200,
    'height' => 200,
    'crop' => 'thumb',
    'gravity' => 'face',
    'radius' => 'max'
]);
```

---

## 📂 Folder Organization

Recommended folder structure in Cloudinary:

```
your-cloud-name/
├── events/
│   ├── posters/
│   └── materials/
├── papers/
│   ├── submissions/
│   └── reviews/
├── qr-codes/
│   ├── registrations/
│   └── check-ins/
├── jury/
│   └── qualifications/
└── users/
    └── avatars/
```

---

## 🔐 Security Best Practices

1. **Never commit `.env` file** - Add to `.gitignore`
2. **Use signed URLs** for private files:
   ```php
   $signedUrl = Cloudinary::getUrl($publicId, [
       'sign_url' => true,
       'type' => 'authenticated'
   ]);
   ```
3. **Set upload presets** in Cloudinary dashboard for consistent transformations
4. **Enable auto-moderation** in Cloudinary for user-uploaded content

---

## 💰 Free Tier Limits

Cloudinary's free tier includes:
- **25 GB** storage
- **25 GB** bandwidth/month
- **25,000** transformations/month
- Unlimited image/video delivery

This should be more than enough for your project!

---

## 🐛 Troubleshooting

### Error: "Invalid signature"
- Check your CLOUDINARY_URL in `.env`
- Ensure no spaces or special characters

### Error: "Upload failed"
- Check file size limits
- Verify file MIME type is allowed
- Check internet connection

### Images not displaying
- Verify the URL is accessible in browser
- Check if public_id was saved correctly
- Ensure image was uploaded successfully

---

## 📱 Sharing with Your Friend

Both you and your friend should:
1. Use the **same Cloudinary account credentials** in `.env`
2. Use the **same database** (which you already do)
3. Files will be automatically accessible to both projects!

**Important:** Coordinate folder naming to avoid conflicts.

---

## 🔄 Migration from Local Storage to Cloudinary

If you have existing local files to migrate:

```bash
php artisan make:command MigrateToCloudinary
```

Create a migration command to upload existing files to Cloudinary and update database records.

---

## 📚 Additional Resources

- [Cloudinary Laravel Documentation](https://cloudinary.com/documentation/laravel_integration)
- [Cloudinary PHP SDK](https://cloudinary.com/documentation/php_integration)
- [Image Transformations](https://cloudinary.com/documentation/image_transformations)

---

## ✅ Next Steps

1. ✅ Package installed
2. ✅ Service class created
3. ✅ Migration created
4. ⏳ Update `.env` with your Cloudinary credentials
5. ⏳ Run migrations
6. ⏳ Update your controllers to use CloudinaryService
7. ⏳ Test uploads
8. ⏳ Share credentials with your friend

---

Need help implementing Cloudinary in a specific controller? Let me know!
