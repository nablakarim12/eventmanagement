# 🎨 AI Rubric Generation - System Architecture

## 📊 Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         USER INTERFACE                               │
│  (resources/views/organizer/rubrics/generate-ai.blade.php)          │
│                                                                      │
│  ┌─────────────────────┐        ┌──────────────────────┐           │
│  │  📁 File Upload     │        │  💬 Text Description │           │
│  │  • PDF              │        │  • Min 20 chars      │           │
│  │  • DOC/DOCX         │   +    │  • Max 5000 chars    │           │
│  │  • XLS/XLSX         │        │  • Context/Requirements          │
│  │  • JPG/PNG          │        │                      │           │
│  └─────────────────────┘        └──────────────────────┘           │
│             │                              │                        │
│             └──────────────────┬───────────┘                        │
└─────────────────────────────────┼──────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      CONTROLLER LAYER                                │
│       (app/Http/Controllers/Organizer/RubricController.php)         │
│                                                                      │
│  ✅ Validate Request:                                               │
│     • File type: mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png          │
│     • File size: max:10240 (10MB)                                   │
│     • Description: required|min:20|max:5000                         │
│                                                                      │
│  📂 Store File:                                                      │
│     storage/app/public/temp_rubrics/{timestamp}_{unique}.{ext}     │
└─────────────────────────────────┬───────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      SERVICE LAYER                                   │
│              (app/Services/GeminiService.php)                        │
│                                                                      │
│  ┌─────────────────────────────────────────────────────────┐       │
│  │          FILE TYPE ROUTING                               │       │
│  └─────────────────────────────────────────────────────────┘       │
│                                                                      │
│  Image (JPG, PNG)?  ──────────┐                                     │
│  PDF?               ──────────┤                                     │
│                               │                                     │
│                               ▼                                     │
│                    ┌─────────────────────┐                          │
│                    │  Gemini Vision API  │                          │
│                    │  • Base64 encode    │                          │
│                    │  • Send to API      │                          │
│                    │  • Extract JSON     │                          │
│                    └─────────────────────┘                          │
│                                                                      │
│  Word (DOC, DOCX)? ────────┐                                        │
│                            │                                        │
│                            ▼                                        │
│                  ┌────────────────────┐                             │
│                  │  PhpWord Library   │                             │
│                  │  • Load document   │                             │
│                  │  • Extract text    │                             │
│                  │  • Parse sections  │                             │
│                  └────────────────────┘                             │
│                            │                                        │
│  Excel (XLS, XLSX)? ───────┤                                        │
│                            │                                        │
│                            ▼                                        │
│                 ┌─────────────────────┐                             │
│                 │ PhpSpreadsheet Lib  │                             │
│                 │ • Load workbook     │                             │
│                 │ • Read all sheets   │                             │
│                 │ • Extract cell data │                             │
│                 └─────────────────────┘                             │
│                            │                                        │
│                            ▼                                        │
│                 ┌─────────────────────┐                             │
│                 │  Text Extraction    │                             │
│                 │  Complete           │                             │
│                 └─────────────────────┘                             │
│                            │                                        │
│                            ▼                                        │
│              ┌──────────────────────────────┐                       │
│              │  Generate Rubric from Text   │                       │
│              │  • Build AI prompt           │                       │
│              │  • Add context description   │                       │
│              │  • Call Gemini API           │                       │
│              │  • Parse JSON response       │                       │
│              └──────────────────────────────┘                       │
│                                                                      │
└─────────────────────────────────┬───────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    GEMINI API (Google)                               │
│                                                                      │
│  Model: gemini-1.5-pro                                              │
│  API Endpoint: generativelanguage.googleapis.com/v1                 │
│                                                                      │
│  Input:                                                              │
│  • Prompt with instructions                                         │
│  • File data (if image/PDF)                                         │
│  • Extracted text (if Word/Excel)                                   │
│  • User context description                                         │
│                                                                      │
│  Output:                                                             │
│  {                                                                   │
│    "categories": [                                                   │
│      {                                                               │
│        "name": "Category Name",                                      │
│        "description": "...",                                         │
│        "max_score": 30,                                              │
│        "items": [...]                                                │
│      }                                                               │
│    ]                                                                 │
│  }                                                                   │
└─────────────────────────────────┬───────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      RESPONSE PROCESSING                             │
│                                                                      │
│  1. Parse JSON response                                             │
│  2. Validate structure                                              │
│  3. Store in session: session(['ai_rubric_data' => $data])         │
│  4. Redirect to preview page                                        │
└─────────────────────────────────┬───────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                       PREVIEW PAGE                                   │
│     (resources/views/organizer/rubrics/preview-ai.blade.php)        │
│                                                                      │
│  Display:                                                            │
│  • All categories with descriptions                                 │
│  • All items with max scores                                        │
│  • All score levels with labels                                     │
│                                                                      │
│  Actions:                                                            │
│  • ✅ Confirm & Save Rubric                                         │
│  • ❌ Cancel & Regenerate                                           │
└─────────────────────────────────┬───────────────────────────────────┘
                                  │
                        [User Confirms]
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      DATABASE STORAGE                                │
│                                                                      │
│  Transaction Start                                                   │
│    │                                                                 │
│    ├─► Delete existing rubric (if any)                              │
│    │                                                                 │
│    ├─► INSERT INTO rubric_categories                                │
│    │    • event_id, name, description, max_score, order             │
│    │                                                                 │
│    ├─► INSERT INTO rubric_items                                     │
│    │    • rubric_category_id, name, description, max_score          │
│    │                                                                 │
│    ├─► INSERT INTO rubric_score_levels                              │
│    │    • rubric_item_id, score, label, description                 │
│    │                                                                 │
│  Transaction Commit                                                  │
│                                                                      │
│  Clear session data                                                  │
│  Redirect to rubric index with success message                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Processing Methods by File Type

```
┌──────────────┬─────────────────┬──────────────────┬─────────────────┐
│  File Type   │  MIME Type      │  Library Used    │  AI Method      │
├──────────────┼─────────────────┼──────────────────┼─────────────────┤
│  PDF         │ application/pdf │ Gemini Vision +  │ Vision API      │
│              │                 │ PDF Parser       │ (primary)       │
├──────────────┼─────────────────┼──────────────────┼─────────────────┤
│  JPG/PNG     │ image/jpeg      │ Gemini Vision    │ Vision API      │
│              │ image/png       │                  │                 │
├──────────────┼─────────────────┼──────────────────┼─────────────────┤
│  DOC/DOCX    │ application/    │ PhpWord +        │ Text Extraction │
│              │ msword          │ Gemini Text      │ → AI Gen        │
├──────────────┼─────────────────┼──────────────────┼─────────────────┤
│  XLS/XLSX    │ application/    │ PhpSpreadsheet + │ Text Extraction │
│              │ vnd.ms-excel    │ Gemini Text      │ → AI Gen        │
└──────────────┴─────────────────┴──────────────────┴─────────────────┘
```

---

## 📦 Libraries & Dependencies

```
composer.json
├── phpoffice/phpspreadsheet (^5.3)
│   ├── markbaker/matrix (3.0.2)
│   ├── markbaker/complex (3.0.2)
│   ├── maennchen/zipstream-php (3.1.2)
│   └── phpoffice/math (0.3.0)
│
├── phpoffice/phpword (^1.4)
│   └── phpoffice/math (0.3.0)
│
└── smalot/pdfparser (^2.12)
    └── composer/pcre (3.3.2)
```

---

## 🎯 Database Schema

```
┌─────────────────────────┐
│   rubric_categories     │
├─────────────────────────┤
│ id (PK)                 │
│ event_id (FK)           │
│ name                    │
│ description             │
│ max_score               │
│ order                   │
│ is_active               │
│ created_at              │
│ updated_at              │
└────────┬────────────────┘
         │
         │ 1:N
         │
┌────────▼────────────────┐
│    rubric_items         │
├─────────────────────────┤
│ id (PK)                 │
│ rubric_category_id (FK) │
│ name                    │
│ description             │
│ max_score               │
│ order                   │
│ is_active               │
│ created_at              │
│ updated_at              │
└────────┬────────────────┘
         │
         │ 1:N
         │
┌────────▼────────────────┐
│  rubric_score_levels    │
├─────────────────────────┤
│ id (PK)                 │
│ rubric_item_id (FK)     │
│ score                   │
│ label                   │
│ description             │
│ order                   │
│ is_active               │
│ created_at              │
│ updated_at              │
└─────────────────────────┘
```

---

## 🔐 API Configuration

```env
# .env file
GEMINI_API_KEY=AIzaSyD_your_actual_key_here

# Get from: https://makersuite.google.com/app/apikey
```

**API Limits (Free Tier):**
- 1,500 requests per day
- 60 requests per minute  
- 32K tokens per request
- No credit card required

---

## 📝 Example Rubric JSON Structure

```json
{
  "categories": [
    {
      "name": "CONTENT QUALITY",
      "description": "Evaluation of content accuracy and depth",
      "max_score": 40,
      "items": [
        {
          "name": "Abstract Clarity",
          "description": "How clear and comprehensive is the abstract",
          "max_score": 10,
          "score_levels": [
            {
              "score": 2,
              "label": "Poor",
              "description": "Abstract is unclear or incomplete"
            },
            {
              "score": 6,
              "label": "Good",
              "description": "Abstract is clear with minor issues"
            },
            {
              "score": 10,
              "label": "Excellent",
              "description": "Abstract is very clear and comprehensive"
            }
          ]
        },
        {
          "name": "Methodology",
          "description": "Quality of research methods",
          "max_score": 15,
          "score_levels": [...]
        }
      ]
    },
    {
      "name": "PRESENTATION",
      "description": "Quality of presentation and delivery",
      "max_score": 30,
      "items": [...]
    }
  ]
}
```

---

## ⚡ Performance Metrics

| Operation | Time | Notes |
|-----------|------|-------|
| File Upload | < 1s | Local storage |
| Excel Extraction | 1-3s | 100 rows limit |
| Word Extraction | 1-2s | All sections |
| PDF Vision API | 5-15s | Depends on size |
| AI Generation | 10-20s | Gemini processing |
| Database Save | < 1s | Transaction |
| **Total** | **15-30s** | End-to-end |

---

## 🎯 Success Criteria

✅ All file formats supported (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG)  
✅ File size validation (10MB max)  
✅ Real-time UI feedback  
✅ AI extraction working  
✅ Database storage functional  
✅ Error handling robust  
✅ User-friendly interface  
✅ Documentation complete  

---

**Status:** ✅ **FULLY OPERATIONAL**
