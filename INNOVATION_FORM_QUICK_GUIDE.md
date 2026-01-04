# Quick Reference: Innovation Competition Event Form

## 🎯 What's New?

You can now create Innovation Competition events with **3 delivery modes**:
1. **Face-to-Face Only** - Physical event
2. **Online Only** - Virtual event  
3. **Hybrid** - Both F2F and Online with **different dates and deadlines**

## 📋 How to Create an Innovation Event

### Step 1: Navigate to Create Event
```
Organizer Dashboard → Events → Create New Event
```

### Step 2: Select Event Type
You'll see two cards:
- **Innovation Competition** ← Click this one
- Academic Conference (coming soon)

### Step 3: Fill Basic Information
- Event Title
- Description
- Venue details
- Price & Max Participants
- Upload Poster (drag & drop)

### Step 4: Select Delivery Mode
Choose one of three options:

#### Option A: Face-to-Face Only
✅ Physical event at your venue  
📝 Fill F2F dates and deadlines  
❌ Online section hidden

#### Option B: Online Only  
✅ Virtual event (Zoom, Teams, etc.)  
📝 Fill online dates, platform URL, and deadlines  
❌ F2F section hidden

#### Option C: Hybrid (Both)
✅ **Both sections appear!**  
📝 Fill F2F dates and deadlines  
📝 Fill Online dates and deadlines  
🎯 **Each can have different schedules!**

### Step 5: Set Submission Deadlines

For each mode, you can set:
- **Paper Submission Deadline** - When papers/proposals are due
- **Product Submission Deadline** - When prototypes/products are due
- **Abstract Submission Deadline** - When abstracts are due
- **Acceptance Notification Date** - When participants get notified

## 💡 Example Use Case: Hybrid Event

**Event**: Global Innovation Challenge 2025  
**Delivery Mode**: Hybrid

### Face-to-Face Details:
- Event Dates: March 10-12, 2025 (3 days)
- Paper Deadline: February 1, 2025
- Product Deadline: February 15, 2025
- Abstract Deadline: January 20, 2025
- Acceptance Date: February 20, 2025

### Online Details:
- Event Dates: March 5-14, 2025 (10 days - longer!)
- Paper Deadline: February 10, 2025 (later deadline)
- Product Deadline: February 25, 2025 (later deadline)
- Abstract Deadline: January 25, 2025 (later deadline)
- Acceptance Date: February 28, 2025
- Platform URL: https://zoom.us/j/123456789

**Why?**
- Online participants need more time
- Different acceptance notifications
- Same event, different participation windows

## 🔄 What Happens When You Save?

The system automatically:
1. ✅ Validates required fields based on delivery mode
2. ✅ Sets primary event dates (earliest start, latest end)
3. ✅ Saves mode-specific deadlines separately
4. ✅ Stores platform URL for online events
5. ✅ Uploads and stores event poster

## 📊 Database Fields Added

New fields in `events` table:
```
delivery_mode: 'face_to_face' | 'online' | 'hybrid'

F2F Fields:
- f2f_start_date, f2f_end_date
- f2f_start_time, f2f_end_time  
- f2f_paper_deadline
- f2f_product_deadline
- f2f_abstract_deadline
- f2f_acceptance_date

Online Fields:
- online_start_date, online_end_date
- online_start_time, online_end_time
- online_platform_url
- online_paper_deadline
- online_product_deadline  
- online_abstract_deadline
- online_acceptance_date
```

## 🎨 Form Features

### Visual Feedback
- Selected delivery mode gets blue border
- Sections smoothly show/hide
- Image preview on upload

### Smart Validation
- Only validates fields for selected mode
- Date validation (end after start)
- Required fields marked with *

### User-Friendly
- Clear section headings with icons
- Helper text for each field
- Drag & drop image upload

## 🚀 Next: Academic Conference Form

Later you'll create:
```
resources/views/organizer/events/create_academic.blade.php
```

This will have different fields specific to academic conferences (paper review system, conference proceedings, etc.)

## 📁 Files Modified

**Created:**
- `create_innovation.blade.php` - New innovation form
- `select_type.blade.php` - Event type selector
- Migration file - Database changes

**Updated:**
- `EventController.php` - Handles new form logic
- `Event.php` model - Added new fields

## ✅ Testing Checklist

Test these scenarios:
- [ ] Create F2F only event → Verify dates saved
- [ ] Create Online only event → Verify URL saved
- [ ] Create Hybrid event → Verify both dates saved
- [ ] Upload poster → Verify image appears
- [ ] Submit without required fields → See validation errors
- [ ] Check different deadlines for F2F vs Online

---

**All Done!** 🎉  

Your Innovation Competition form is ready with full support for Face-to-Face, Online, and Hybrid delivery modes with separate submission deadlines!

When you're ready, just tell me and I'll help you create the Academic Conference form too! 😊
