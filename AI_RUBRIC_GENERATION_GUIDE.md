# 🤖 AI-Powered Rubric Auto-Generation Guide

## 📋 Overview

This system uses Google Gemini AI to automatically generate evaluation rubrics by extracting content from various document formats. The AI can analyze uploaded files OR create rubrics from text descriptions.

---

## 🎯 Supported File Formats

| Format | Extension | Max Size | Extraction Method | Best For |
|--------|-----------|----------|-------------------|----------|
| **PDF** | `.pdf` | 10MB | Gemini Vision API + Text Extraction | ✅ Scanned documents, images |
| **Word** | `.doc`, `.docx` | 10MB | PhpWord Library | ✅ Text-based rubrics |
| **Excel** | `.xls`, `.xlsx` | 10MB | PhpSpreadsheet Library | ✅ Tabular rubrics, scoring matrices |
| **Images** | `.jpg`, `.jpeg`, `.png` | 10MB | Gemini Vision API | ✅ Photos of rubrics, handwritten notes |

---

## 🚀 How to Use

### **Method 1: Upload Document + Description (Recommended)**

1. **Navigate to Rubric Management**
   - Go to your event dashboard
   - Click "Rubric Management"
   - Click "Generate with AI" button

2. **Upload Your Document**
   - Click the upload area or drag & drop your file
   - Supported: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
   - Max size: 10MB
   - System validates file type and size automatically

3. **Add Context Description** (Required - Min 20 characters)
   ```
   Example:
   "This is a rubric for evaluating innovation projects at our tech 
   competition. The uploaded Excel file contains our existing scoring 
   criteria. Please structure it with categories for Innovation (40%), 
   Implementation (30%), and Presentation (30%). Use a 5-point scale 
   for detailed scoring."
   ```

4. **Click "Generate with AI"**
   - AI processes your document and description
   - Typical processing time: 10-30 seconds
   - You'll be redirected to preview page

5. **Review & Confirm**
   - Preview the generated rubric structure
   - Check categories, items, and score levels
   - Make manual adjustments if needed
   - Click "Confirm & Save Rubric"

---

### **Method 2: Description Only (No File Upload)**

If you don't have an existing document, just describe what you need:

**Example Descriptions:**

```text
1. Academic Paper Evaluation:
"Create a comprehensive rubric for evaluating research papers with 
sections for Abstract (10 points), Introduction (15 points), Methodology 
(20 points), Results (25 points), Discussion (20 points), and Conclusion 
(10 points). Use 3-5 score levels for each section with clear criteria."

2. Innovation Competition:
"Generate a rubric for startup pitch competition evaluating Business Model 
(25%), Innovation (25%), Market Potential (20%), Team (15%), and 
Presentation Quality (15%). Total 100 points with detailed scoring criteria."

3. Poster Presentation:
"Design rubric for academic poster evaluation covering Visual Design (20%), 
Content Quality (30%), Research Methodology (25%), and Presentation Skills 
(25%). Include specific criteria for each score level from Poor to Excellent."
```

---

## 📊 File Format Specific Tips

### **PDF Files**
- ✅ **Best for:** Scanned documents, official rubric templates
- **Processing:** Uses Gemini Vision API (can read text and images)
- **Tip:** Ensure text is clear and legible
- **Fallback:** If Vision fails, uses text extraction

### **Word Documents (DOC/DOCX)**
- ✅ **Best for:** Text-based rubrics with structured sections
- **Processing:** Extracts all text from sections
- **Tip:** Use clear headings and bullet points
- **Note:** Formatting (bold, italic) is not preserved

### **Excel Spreadsheets (XLS/XLSX)**
- ✅ **Best for:** Tabular rubrics, scoring matrices
- **Processing:** Reads all sheets, extracts cell values
- **Tip:** Organize with clear column headers
- **Limit:** Processes first 100 rows per sheet to avoid token limits

### **Images (JPG/PNG)**
- ✅ **Best for:** Photos of rubrics, handwritten notes, screenshots
- **Processing:** Uses Gemini Vision API
- **Tip:** Ensure good lighting and resolution
- **Best results:** High contrast, clear text

---

## 🎨 AI Generated Rubric Structure

The AI creates rubrics with this hierarchical structure:

```
Event Rubric
├── Category 1 (e.g., "CONTENT QUALITY")
│   ├── Description: "Evaluation of content accuracy and depth"
│   ├── Max Score: 40
│   └── Items:
│       ├── Item 1: "Abstract Clarity"
│       │   ├── Description: "How clear and comprehensive is the abstract"
│       │   ├── Max Score: 10
│       │   └── Score Levels:
│       │       ├── Level 1: Score 2 - "Poor"
│       │       ├── Level 2: Score 6 - "Good"
│       │       └── Level 3: Score 10 - "Excellent"
│       └── Item 2: "Methodology"
│           └── ...
└── Category 2 (e.g., "PRESENTATION")
    └── ...
```

**Typical AI Output:**
- **3-5 Categories** (main evaluation areas)
- **3-6 Items per Category** (specific criteria)
- **3-5 Score Levels per Item** (granular scoring)
- **Total Points:** Usually sums to 100

---

## 💡 Best Practices

### **Writing Effective Descriptions**

1. **Be Specific**
   ```
   ❌ Bad: "Create a rubric for research papers"
   ✅ Good: "Create a rubric for undergraduate research papers evaluating 
   Abstract, Literature Review, Methodology, Results, and Discussion. 
   Use 100 points total with 5-point scale for each criterion."
   ```

2. **Include Point Distribution**
   ```
   ✅ "Poster evaluation: Visual Design (30%), Content (40%), Presentation (30%)"
   ```

3. **Specify Score Levels**
   ```
   ✅ "Use 5-point scale: Poor (1), Fair (2-3), Good (4-5), Very Good (6-7), 
   Excellent (8-10)"
   ```

4. **Mention Special Requirements**
   ```
   ✅ "Include criteria for technical innovation, commercial viability, 
   and social impact for startup competition"
   ```

---

## 🔧 Technical Implementation

### **File Processing Flow**

```
1. User uploads file + description
   ↓
2. Server validates:
   - File type (mimes validation)
   - File size (max 10MB)
   - Description length (min 20 chars)
   ↓
3. File moved to temp storage
   ↓
4. GeminiService determines processing method:
   - Images/PDFs → Gemini Vision API
   - Word → PhpWord text extraction
   - Excel → PhpSpreadsheet text extraction
   ↓
5. AI generates structured rubric JSON
   ↓
6. Parse JSON and store in session
   ↓
7. User previews and confirms
   ↓
8. Save to database (rubric_categories, rubric_items, rubric_score_levels)
```

### **Libraries Used**

- **PhpSpreadsheet** - Excel file parsing (XLS, XLSX)
- **PhpWord** - Word document parsing (DOC, DOCX)
- **PDF Parser** - PDF text extraction
- **Google Gemini API** - AI content analysis and generation

### **Database Tables**

```sql
-- Main rubric structure
rubric_categories
├── id
├── event_id
├── name
├── description
├── max_score
└── order

rubric_items
├── id
├── rubric_category_id
├── name
├── description
├── max_score
└── order

rubric_score_levels
├── id
├── rubric_item_id
├── score
├── label
├── description
└── order
```

---

## 🧪 Testing Examples

### **Test Case 1: Excel Rubric**

**Create Excel file:** `innovation_rubric.xlsx`

| Category | Criterion | Max Points | Excellent (5) | Good (3) | Poor (1) |
|----------|-----------|------------|---------------|----------|----------|
| Innovation | Originality | 10 | Highly novel | Somewhat new | Not original |
| Innovation | Impact | 10 | High impact | Moderate | Low impact |
| Implementation | Technical Quality | 15 | Excellent code | Good code | Poor code |

**Description to add:**
```
"This is our innovation competition rubric with three categories. 
Please structure it properly with clear score levels."
```

---

### **Test Case 2: PDF Screenshot**

**Take screenshot of any rubric and save as:** `rubric_sample.pdf`

**Description:**
```
"This PDF contains our conference paper evaluation rubric. 
Extract all categories and criteria. Use 100 points total."
```

---

### **Test Case 3: Word Document**

**Create Word doc:** `academic_rubric.docx`

```
Paper Evaluation Rubric

Abstract (10 points)
- Clear and concise summary
- Includes key findings

Methodology (25 points)
- Appropriate research methods
- Well-documented procedures

Results (30 points)
- Clear presentation
- Statistical analysis
```

**Description:**
```
"Convert this Word document into a structured rubric with 
5-point scoring levels for each criterion."
```

---

## 🚨 Troubleshooting

### **Common Issues**

1. **"Failed to extract text from file"**
   - **Cause:** Corrupted file or unsupported format
   - **Solution:** Re-save file or convert to PDF/image

2. **"File size exceeds 10MB"**
   - **Cause:** File too large
   - **Solution:** Compress file or use lower resolution images

3. **"API key not configured"**
   - **Cause:** GEMINI_API_KEY missing in .env
   - **Solution:** Add key from https://makersuite.google.com/app/apikey

4. **"Could not parse rubric response"**
   - **Cause:** AI returned unexpected format
   - **Solution:** Add more context in description, try again

5. **Excel extraction shows "Error"**
   - **Cause:** Complex Excel formulas or protected sheets
   - **Solution:** Save as simple Excel or export to PDF

---

## 📈 API Usage & Limits

### **Gemini API Free Tier**

- **Requests:** 1,500 per day
- **Rate Limit:** 60 per minute
- **Token Limit:** 32K per request
- **Cost:** FREE (no credit card required)

### **File Size Considerations**

| File Type | Max Size | Tokens Used (Approx) |
|-----------|----------|---------------------|
| Small Image (500KB) | 10MB | 1,000-2,000 |
| PDF (5 pages) | 10MB | 2,000-5,000 |
| Excel (50 rows) | 10MB | 500-1,500 |
| Word (10 pages) | 10MB | 1,000-3,000 |

---

## 🎓 Advanced Usage

### **Custom Rubric Templates**

You can guide the AI to create specific rubric styles:

```text
"Generate an analytic rubric (not holistic) for essay evaluation with 
separate criteria for Content, Organization, Style, and Mechanics. 
Each criterion should have 4 levels: Emerging (1), Developing (2), 
Proficient (3), Exemplary (4)."
```

### **Multi-Language Support**

The AI can work with documents in multiple languages:

```text
"This document is in Bahasa Malaysia. Extract the rubric structure 
and translate criteria to English for our international jury."
```

### **Combining Multiple Sources**

Upload your primary document and reference multiple sources in description:

```text
"The uploaded Excel has our base rubric. Additionally, add criteria 
for 'Sustainability Impact' (10%) and 'Commercial Viability' (10%) 
based on our new competition requirements."
```

---

## 📝 API Reference

### **Routes**

```php
// Show AI generation form
GET /organizer/events/{event}/rubrics/generate-ai

// Process AI generation
POST /organizer/events/{event}/rubrics/process-ai
- file: multipart/form-data (optional)
- description: string (required, min:20, max:5000)

// Preview AI-generated rubric
GET /organizer/events/{event}/rubrics/preview-ai

// Confirm and save
POST /organizer/events/{event}/rubrics/confirm-ai
```

### **GeminiService Methods**

```php
// Generate from description only
public function generateRubricFromDescription(string $description): array

// Extract from file + description
public function extractRubricFromFile(
    string $filePath, 
    string $mimeType, 
    string $context = ''
): array

// Vision API for images/PDFs
private function extractWithVision(
    string $filePath, 
    string $mimeType, 
    string $context = ''
): array

// Text extraction methods
private function extractFromPdf(string $filePath): string
private function extractFromWord(string $filePath): string
private function extractFromExcel(string $filePath): string
```

---

## ✅ Success Checklist

Before deploying to production:

- [ ] GEMINI_API_KEY configured in .env
- [ ] Storage directory writable: `storage/app/public/temp_rubrics`
- [ ] PHP extensions enabled: zip, gd, xml
- [ ] Composer packages installed: phpoffice/phpspreadsheet, phpoffice/phpword
- [ ] Test with sample files of each format
- [ ] Verify error handling for large files
- [ ] Test without file upload (description only)
- [ ] Check generated rubric structure in database

---

## 🎉 Quick Start

**5-Minute Test:**

1. Make sure GEMINI_API_KEY is in your `.env`
2. Go to any event → Rubric Management
3. Click "Generate with AI"
4. Paste this description:
   ```
   Create a rubric for poster presentation with Visual Design (30 points),
   Content Quality (40 points), and Oral Presentation (30 points). 
   Use 5-point scale with clear criteria for each level.
   ```
5. Click "Generate with AI"
6. Wait 10-20 seconds
7. Review and click "Confirm & Save"

**Done!** Your AI-generated rubric is ready to use.

---

## 📞 Support

**Issues?**
- Check error logs: `storage/logs/laravel.log`
- Verify Gemini API status: https://status.cloud.google.com
- Test API key: Visit https://makersuite.google.com

**Need Help?**
- Refer to: `GEMINI_SETUP_GUIDE.md`
- Check examples in: `resources/views/organizer/rubrics/generate-ai.blade.php`

---

**Last Updated:** December 27, 2025  
**Version:** 2.0 - Enhanced Multi-Format Support
