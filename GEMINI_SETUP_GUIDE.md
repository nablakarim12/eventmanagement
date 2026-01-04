# Google Gemini API Setup Guide

## 🚀 Quick Setup (5 minutes)

### Step 1: Get Your Free API Key

1. Go to **Google AI Studio**: https://makersuite.google.com/app/apikey
2. Click **"Get API Key"** (or **"Create API Key"**)
3. Choose **"Create API key in new project"** or select existing project
4. Copy your API key (starts with `AIza...`)

**Important**: Keep this key secret! Don't commit it to Git.

### Step 2: Add API Key to Your .env File

Open `.env` file and update:

```env
GEMINI_API_KEY=AIzaSyD_your_actual_key_here
```

Replace `your_api_key_here` with your actual key from Step 1.

### Step 3: Test the Integration

1. Navigate to any event in your organizer dashboard
2. Go to **Rubric Management**
3. Click **"Generate with AI"** button
4. Try either:
   - **Upload**: Upload an image of your rubric
   - **Description**: Type something like:
     ```
     Create a rubric for innovation competition with poster evaluation (30%), 
     presentation quality (30%), and product demo (40%). Include criteria 
     for technical merit, creativity, and commercial potential.
     ```
5. Click **"Generate with AI"**
6. Review the generated rubric
7. Click **"Confirm & Save Rubric"**

## ✅ What Works with Free Tier

**Generous Limits**:
- ✅ 1,500 requests per day
- ✅ 60 requests per minute
- ✅ No credit card required
- ✅ Vision API included (analyze images/PDFs)
- ✅ Up to 32K tokens per request

**Supported Features**:
- ✅ Text-based rubric generation
- ✅ Image analysis (upload JPG/PNG rubric images)
- ✅ PDF document analysis
- ✅ Structured JSON output
- ✅ Multi-turn conversations

## 📝 Example Prompts

### For Academic Events:
```
Create a comprehensive rubric for academic paper evaluation with sections for 
Abstract (10%), Literature Review (15%), Methodology (25%), Results (25%), 
Discussion (15%), and Conclusion (10%). Use 5-point scale for each criterion.
```

### For Innovation Competitions:
```
Generate rubric for startup pitch competition evaluating Business Model (30%), 
Market Opportunity (20%), Team Capability (20%), Product Demo (20%), and 
Financial Projections (10%). Total 100 points.
```

### For Design Contests:
```
Design evaluation rubric for UI/UX competition covering Creativity (25%), 
Usability (25%), Visual Design (25%), and Technical Implementation (25%). 
Include detailed scoring criteria.
```

## 🔧 Troubleshooting

### Error: "API key not valid"
- Make sure you copied the full key (starts with `AIza`)
- Check for extra spaces in `.env` file
- Ensure you're using the correct key from Google AI Studio

### Error: "Quota exceeded"
- Free tier allows 1,500 requests/day
- Wait 24 hours for quota to reset
- Or upgrade to paid plan if needed

### Error: "Failed to generate rubric"
- Check your internet connection
- Verify API key is set correctly
- Try a simpler prompt first
- Check Laravel logs: `storage/logs/laravel.log`

## 🎨 Tips for Best Results

1. **Be Specific**: The more details you provide, the better the output
   - ❌ "Create a rubric"
   - ✅ "Create a rubric for science fair with 3 categories, 5 points each"

2. **Mention Structure**: Specify categories, point distributions
   - Include total points
   - Mention category weights
   - Specify scoring ranges

3. **Upload Clear Images**: For image upload
   - Use high-quality scans
   - Ensure text is readable
   - Table format works best

4. **Review & Edit**: AI is smart but not perfect
   - Always review generated rubrics
   - You can edit manually after generation
   - Adjust scores and descriptions as needed

## 📊 API Cost (if you upgrade later)

**Free Tier**: Perfect for most users
- 1,500 requests/day is plenty for event management

**Paid Tier** (if needed):
- Gemini Pro: $0.00025 per 1K characters
- Gemini Pro Vision: $0.0025 per image
- Very affordable compared to alternatives

## 🔒 Security Best Practices

1. **Never commit API key to Git**:
   ```bash
   # .env file should be in .gitignore
   echo ".env" >> .gitignore
   ```

2. **Rotate keys periodically**:
   - Generate new key every 3-6 months
   - Delete old keys from Google Console

3. **Monitor usage**:
   - Check Google AI Studio dashboard
   - Set up usage alerts

## 🆘 Need Help?

- **Google AI Documentation**: https://ai.google.dev/docs
- **API Reference**: https://ai.google.dev/api/rest
- **Community Forum**: https://discuss.ai.google.dev/

## 🎉 You're All Set!

Your AI rubric generation is now powered by Google Gemini! 🚀

Test it out and enjoy automated rubric creation! ✨
