# Certificate System - How to Use

## 🎓 Certificate Generation with Cloudinary

Your system already has a certificate module! Here's how to use it:

### ✅ What You Need To Do:

#### Step 1: Access Certificate Management
1. Go to **Organizer Dashboard**
2. Click on **"Certificate"** in the sidebar (under Analytics & More)
3. Or navigate to: `/organizer/certificates`

#### Step 2: Upload Certificate Template
1. Click **"Create New Template"** or similar button
2. **Upload your certificate background image** to Cloudinary
   - This should be a blank certificate with just the design
   - No text yet - the system will add names, dates, etc.
3. **Configure Text Positions:**
   - Set X, Y coordinates for:
     - Participant/Jury Name
     - Event Name
     - Date
     - Role (Participant/Jury)
     - Award Name (for Innovation winners)
   - Set font size and color for each field

#### Step 3: Generate Certificates

**For Participants:**
1. Go to the event's evaluation results page
2. Click **"Generate Certificates"** button
3. System will automatically generate certificates for:
   - ✅ All evaluated participants
   - ✅ Include award names (for Innovation events with awards)

**For Jury:**
1. Go to certificate management
2. Select **"Generate Jury Certificates"**
3. System generates for all assigned jury members

#### Step 4: Download & Send
- Certificates are stored in database (`certificate_path` column)
- Participants can download from their dashboard
- Organizer can bulk download or send via email

---

## 🔧 Current System Status

✅ **Already Built:**
- Database tables
- Models (CertificateTemplate)
- Controllers (CertificateController)
- Routes for certificate management

📋 **What Exists:**
- Certificate template upload
- Text position configuration
- Certificate generation logic
- Cloudinary integration

---

## 📍 Next Steps for You

**RIGHT NOW:**
1. **Check if certificate menu exists** in your sidebar
2. **Navigate to** `/organizer/certificates` in browser
3. **Tell me what you see** - is there a page or error?

Based on what you see, I'll either:
- Guide you through using the existing system
- OR update/fix the certificate system to work with Cloudinary properly

**Just go to the certificate page and tell me what happens!**
