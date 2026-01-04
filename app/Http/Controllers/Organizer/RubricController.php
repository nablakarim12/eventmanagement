<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\ReviewCriteria;
use App\Models\RubricCategory;
use App\Models\RubricItem;
use App\Models\RubricScoreLevel;
use App\Services\GeminiService;
use App\Services\OpenAIService;
use App\Services\TemplateRubricService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RubricController extends Controller
{
    /**
     * Display rubric criteria for an event
     */
    public function index(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = $event->rubricCategories()
            ->with(['items.scoreLevels'])
            ->orderBy('order')
            ->get();

        return view('organizer.rubrics.index', compact('event', 'categories'));
    }

    /**
     * Show form to create/edit rubric criteria
     */
    public function edit(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = $event->rubricCategories()
            ->with(['items.scoreLevels'])
            ->orderBy('order')
            ->get();

        return view('organizer.rubrics.edit', compact('event', 'categories'));
    }

    /**
     * Show AI rubric generation form
     */
    public function generateWithAI(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('organizer.rubrics.generate-ai', compact('event'));
    }

    /**
     * Process AI rubric generation
     */
    public function processAIGeneration(Request $request, Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB max
            'description' => 'required|string|min:20|max:5000',
        ], [
            'file.mimes' => 'File must be PDF, Word (DOC, DOCX), Excel (XLS, XLSX), or Image (JPG, PNG)',
            'file.max' => 'File size must not exceed 10MB',
            'description.required' => 'Please provide a description or context for the rubric generation',
            'description.min' => 'Description must be at least 20 characters to generate a meaningful rubric',
        ]);

        try {
            $rubricData = null;
            $provider = null;
            $errors = [];

            // Try Gemini first
            try {
                $geminiService = new GeminiService();
                
                if ($request->hasFile('file') && $request->file('file')->isValid()) {
                    $file = $request->file('file');
                    $mimeType = $file->getMimeType();
                    
                    $directory = storage_path('app/public/temp_rubrics');
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;
                    $file->move($directory, $filename);

                    $rubricData = $geminiService->extractRubricFromFile($fullPath, $mimeType, $request->description);
                    @unlink($fullPath);
                } else {
                    $rubricData = $geminiService->generateRubricFromDescription($request->description);
                }
                
                $provider = 'Gemini AI';
            } catch (\Exception $e) {
                $errors[] = 'Gemini: ' . $e->getMessage();
                Log::warning('Gemini failed, trying OpenAI', ['error' => $e->getMessage()]);
                
                // Try OpenAI as backup
                try {
                    $openAIService = new OpenAIService();
                    
                    if ($openAIService->isConfigured()) {
                        $extractedText = '';
                        
                        if ($request->hasFile('file') && $request->file('file')->isValid()) {
                            $file = $request->file('file');
                            $mimeType = $file->getMimeType();
                            
                            $directory = storage_path('app/public/temp_rubrics');
                            if (!file_exists($directory)) {
                                mkdir($directory, 0755, true);
                            }
                            
                            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;
                            $file->move($directory, $filename);
                            
                            // Extract text using Gemini's extractors (they work without API)
                            $geminiService = new GeminiService();
                            $extractedText = $geminiService->extractTextFromDocument($fullPath, $mimeType);
                            @unlink($fullPath);
                        }
                        
                        $rubricData = $openAIService->generateRubric($request->description, $extractedText);
                        $provider = 'OpenAI GPT';
                    } else {
                        throw new \Exception('OpenAI not configured');
                    }
                } catch (\Exception $e2) {
                    $errors[] = 'OpenAI: ' . $e2->getMessage();
                    Log::warning('OpenAI failed, using templates', ['error' => $e2->getMessage()]);
                    
                    // Final fallback: Use templates with document parsing
                    $templateService = new TemplateRubricService();
                    
                    if ($request->hasFile('file') && $request->file('file')->isValid()) {
                        $file = $request->file('file');
                        $mimeType = $file->getMimeType();
                        
                        $directory = storage_path('app/public/temp_rubrics');
                        if (!file_exists($directory)) {
                            mkdir($directory, 0755, true);
                        }
                        
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;
                        $file->move($directory, $filename);
                        
                        // Parse document and generate rubric
                        $rubricData = $templateService->generateFromDocument($fullPath, $mimeType, $request->description);
                        @unlink($fullPath);
                    } else {
                        // No file, use template based on description
                        $rubricData = $templateService->generateFromTemplate(
                            $request->description,
                            $event->event_type
                        );
                    }
                    
                    $provider = 'Smart Template (Parsed from your document)';
                }
            }

            if (!$rubricData) {
                throw new \Exception('All rubric generation methods failed. Errors: ' . implode('; ', $errors));
            }

            // Store in session for preview
            session([
                'ai_rubric_data' => $rubricData,
                'ai_provider' => $provider
            ]);

            return redirect()->route('organizer.events.rubrics.preview-ai', $event->id)
                ->with('success', "Rubric generated successfully using {$provider}! Please review and confirm.");

        } catch (\Exception $e) {
            // If it's an API key error, show helpful message
            if (strpos($e->getMessage(), 'API key') !== false) {
                return back()->withErrors(['error' => $e->getMessage()])->withInput();
            }
            
            // For other errors, log and show generic message
            Log::error('Gemini AI Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Failed to generate rubric: ' . $e->getMessage() . '. Please try again or use the manual creation option.'])->withInput();
        }
    }

    /**
     * Preview AI-generated rubric
     */
    public function previewAI(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $rubricData = session('ai_rubric_data');
        
        if (!$rubricData) {
            return redirect()->route('organizer.events.rubrics.generate-ai', $event->id)
                ->with('error', 'No AI-generated rubric found. Please generate one first.');
        }

        return view('organizer.rubrics.preview-ai', compact('event', 'rubricData'));
    }

    /**
     * Confirm and save AI-generated rubric
     */
    public function confirmAI(Request $request, Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $rubricData = session('ai_rubric_data');
        
        if (!$rubricData) {
            return redirect()->route('organizer.events.rubrics.generate-ai', $event->id)
                ->with('error', 'No AI-generated rubric found.');
        }

        try {
            DB::beginTransaction();

            // Delete existing rubric completely
            DB::table('rubric_score_levels')
                ->whereIn('rubric_item_id', function($query) use ($event) {
                    $query->select('id')
                        ->from('rubric_items')
                        ->whereIn('rubric_category_id', function($q) use ($event) {
                            $q->select('id')
                                ->from('rubric_categories')
                                ->where('event_id', $event->id);
                        });
                })
                ->delete();

            DB::table('rubric_items')
                ->whereIn('rubric_category_id', function($query) use ($event) {
                    $query->select('id')
                        ->from('rubric_categories')
                        ->where('event_id', $event->id);
                })
                ->delete();

            DB::table('rubric_categories')
                ->where('event_id', $event->id)
                ->delete();

            // Create new rubric from AI data
            foreach ($rubricData['categories'] as $categoryIndex => $categoryData) {
                $categoryId = DB::table('rubric_categories')->insertGetId([
                    'event_id' => $event->id,
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'] ?? null,
                    'max_score' => $categoryData['max_score'],
                    'weight' => $categoryData['weight'] ?? 1.0,
                    'order' => $categoryIndex,
                    'is_active' => 't',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($categoryData['items'] as $itemIndex => $itemData) {
                    $itemId = DB::table('rubric_items')->insertGetId([
                        'rubric_category_id' => $categoryId,
                        'name' => $itemData['name'],
                        'description' => $itemData['description'] ?? null,
                        'max_score' => $itemData['max_score'],
                        'order' => $itemIndex,
                        'is_active' => 't',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Create score levels
                    if (isset($itemData['score_levels']) && is_array($itemData['score_levels'])) {
                        foreach ($itemData['score_levels'] as $levelIndex => $levelData) {
                            DB::table('rubric_score_levels')->insert([
                                'rubric_item_id' => $itemId,
                                'level' => $levelData['score'] ?? ($levelIndex + 1),
                                'description' => $levelData['description'] ?? ($levelData['label'] ?? ''),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            // Clear session
            session()->forget(['ai_rubric_data', 'ai_provider']);

            return redirect()->route('organizer.events.rubrics.index', $event->id)
                ->with('success', '✅ AI-generated rubric has been saved successfully! You can now use it for participant evaluations.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save AI rubric', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to save rubric: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Store or update rubric criteria
     */
    public function update(Request $request, Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.description' => 'nullable|string|max:1000',
            'categories.*.max_score' => 'required|integer|min:0|max:1000',
            'categories.*.weight' => 'required|integer|min:1|max:10',
            'categories.*.items' => 'required|array|min:1',
            'categories.*.items.*.name' => 'required|string|max:255',
            'categories.*.items.*.description' => 'nullable|string|max:1000',
            'categories.*.items.*.max_score' => 'required|integer|min:1|max:100',
            'categories.*.items.*.score_levels' => 'nullable|array',
        ]);

        // Delete existing rubric for this event
        $event->rubricCategories()->delete();

        // Create new rubric structure
        foreach ($request->categories as $catIndex => $categoryData) {
            // Use DB insert to ensure proper boolean handling in PostgreSQL
            $categoryId = DB::table('rubric_categories')->insertGetId([
                'event_id' => $event->id,
                'name' => $categoryData['name'],
                'description' => $categoryData['description'] ?? null,
                'max_score' => (int) $categoryData['max_score'],
                'weight' => (int) $categoryData['weight'],
                'order' => (int) $catIndex,
                'is_active' => DB::raw('true'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create items for this category
            if (isset($categoryData['items'])) {
                foreach ($categoryData['items'] as $itemIndex => $itemData) {
                    $itemId = DB::table('rubric_items')->insertGetId([
                        'rubric_category_id' => $categoryId,
                        'name' => $itemData['name'],
                        'description' => $itemData['description'] ?? null,
                        'max_score' => (int) $itemData['max_score'],
                        'order' => (int) $itemIndex,
                        'is_active' => DB::raw('true'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Create score level descriptions
                    // The form sends score_levels as: score_levels[1] => "description", score_levels[2] => "description"
                    if (isset($itemData['score_levels']) && is_array($itemData['score_levels'])) {
                        foreach ($itemData['score_levels'] as $level => $description) {
                            // Only create if description is not empty
                            if (!empty($description)) {
                                DB::table('rubric_score_levels')->insert([
                                    'rubric_item_id' => $itemId,
                                    'level' => (int) $level,
                                    'description' => $description,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()
            ->route('organizer.events.rubrics.index', $event)
            ->with('success', 'Rubric updated successfully!');
    }

    /**
     * Set default criteria for new events
     */
    public function setDefault(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if rubric already exists
        if ($event->rubricCategories()->count() > 0) {
            return redirect()
                ->route('organizer.events.rubrics.index', $event)
                ->with('info', 'Rubric already exists for this event.');
        }

        // Create default rubric based on event type
        $defaultRubric = $this->getDefaultRubric($event);

        foreach ($defaultRubric as $catIndex => $categoryData) {
            $category = RubricCategory::create([
                'event_id' => $event->id,
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'max_score' => $categoryData['max_score'],
                'weight' => $categoryData['weight'],
                'order' => $catIndex,
                'is_active' => true,
            ]);

            foreach ($categoryData['items'] as $itemIndex => $itemData) {
                $item = RubricItem::create([
                    'rubric_category_id' => $category->id,
                    'name' => $itemData['name'],
                    'description' => $itemData['description'] ?? null,
                    'max_score' => $itemData['max_score'],
                    'order' => $itemIndex,
                    'is_active' => true,
                ]);

                // Create score levels
                foreach ($itemData['score_levels'] as $scoreLevelData) {
                    RubricScoreLevel::create([
                        'rubric_item_id' => $item->id,
                        'level' => $scoreLevelData['level'],
                        'description' => $scoreLevelData['description'],
                    ]);
                }
            }
        }

        return redirect()
            ->route('organizer.events.rubrics.edit', $event)
            ->with('success', 'Default rubric created! You can customize it now.');
    }

    /**
     * Get default rubric structure based on event category
     */
    private function getDefaultRubric(Event $event): array
    {
        // Default Innovation Competition Rubric (similar to the Excel image)
        return [
            [
                'name' => 'Poster',
                'description' => 'Visual presentation and content',
                'max_score' => 30,
                'weight' => 1,
                'items' => [
                    [
                        'name' => 'Abstract',
                        'description' => 'Quality and completeness of abstract',
                        'max_score' => 5,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'The abstract only contains one (1) of the required items.'],
                            ['level' => 2, 'description' => 'The abstract only contains two (2) of the required items.'],
                            ['level' => 3, 'description' => 'The abstract only contains three (3) of the required items.'],
                            ['level' => 4, 'description' => 'The abstract only contains four (4) of the required items.'],
                            ['level' => 5, 'description' => 'The abstract contains all five (5) of the required items.'],
                        ],
                    ],
                    [
                        'name' => 'Problem Statement & Objectives',
                        'description' => 'Clarity of problem definition',
                        'max_score' => 5,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'The Problem Statement is unclear, AND The Objective is undefined.'],
                            ['level' => 2, 'description' => 'The Problem Statement is not quite clear, AND The Objectives is not well defined.'],
                            ['level' => 3, 'description' => 'The Problem Statement is quite clear, BUT only one (1) Objective is well defined.'],
                            ['level' => 4, 'description' => 'The Problem Statement is clear, AND almost all Objectives are well defined.'],
                            ['level' => 5, 'description' => 'The Problem Statement is very clear, AND all Objectives are well defined.'],
                        ],
                    ],
                    [
                        'name' => 'Methodology',
                        'description' => 'Approach and methods used',
                        'max_score' => 5,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'The methodology is unclear.'],
                            ['level' => 2, 'description' => 'The methodology is somewhat clear.'],
                            ['level' => 3, 'description' => 'The methodology is clear with minor gaps.'],
                            ['level' => 4, 'description' => 'The methodology is clear and well-structured.'],
                            ['level' => 5, 'description' => 'The methodology is very clear and comprehensive.'],
                        ],
                    ],
                    [
                        'name' => 'Result and finding',
                        'description' => 'Quality of results presented',
                        'max_score' => 5,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'Results are unclear or missing.'],
                            ['level' => 2, 'description' => 'Results are present but poorly explained.'],
                            ['level' => 3, 'description' => 'Results are adequately presented.'],
                            ['level' => 4, 'description' => 'Results are well presented and explained.'],
                            ['level' => 5, 'description' => 'Results are excellently presented with clear insights.'],
                        ],
                    ],
                    [
                        'name' => 'Commercialization Potential',
                        'description' => 'Market viability and potential',
                        'max_score' => 5,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'No clear commercial potential.'],
                            ['level' => 2, 'description' => 'Limited commercial potential.'],
                            ['level' => 3, 'description' => 'Moderate commercial potential.'],
                            ['level' => 4, 'description' => 'Good commercial potential.'],
                            ['level' => 5, 'description' => 'Excellent commercial potential with clear market.'],
                        ],
                    ],
                    [
                        'name' => 'Visual Appearance',
                        'description' => 'Design and visual quality',
                        'max_score' => 5,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'Poor visual design.'],
                            ['level' => 2, 'description' => 'Basic visual design.'],
                            ['level' => 3, 'description' => 'Adequate visual design.'],
                            ['level' => 4, 'description' => 'Good visual design and layout.'],
                            ['level' => 5, 'description' => 'Excellent visual design, very professional.'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Presentation',
                'description' => 'Oral presentation skills',
                'max_score' => 30,
                'weight' => 1,
                'items' => [
                    [
                        'name' => 'Content Knowledge',
                        'description' => 'Understanding of the subject matter',
                        'max_score' => 10,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'Limited knowledge of content.'],
                            ['level' => 5, 'description' => 'Basic understanding of content.'],
                            ['level' => 10, 'description' => 'Expert knowledge and deep understanding.'],
                        ],
                    ],
                    [
                        'name' => 'Communication Skills',
                        'description' => 'Clarity and effectiveness of presentation',
                        'max_score' => 10,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'Poor communication skills.'],
                            ['level' => 5, 'description' => 'Adequate communication.'],
                            ['level' => 10, 'description' => 'Excellent communication, very engaging.'],
                        ],
                    ],
                    [
                        'name' => 'Q&A Handling',
                        'description' => 'Response to questions',
                        'max_score' => 10,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'Unable to answer questions effectively.'],
                            ['level' => 5, 'description' => 'Answers questions adequately.'],
                            ['level' => 10, 'description' => 'Answers all questions confidently and thoroughly.'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Product/Innovation',
                'description' => 'The actual product or innovation',
                'max_score' => 40,
                'weight' => 2,
                'items' => [
                    [
                        'name' => 'Innovation Level',
                        'description' => 'Uniqueness and creativity',
                        'max_score' => 10,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'Not innovative, copied ideas.'],
                            ['level' => 5, 'description' => 'Moderately innovative.'],
                            ['level' => 10, 'description' => 'Highly innovative and original.'],
                        ],
                    ],
                    [
                        'name' => 'Functionality',
                        'description' => 'How well does it work',
                        'max_score' => 10,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'Does not function properly.'],
                            ['level' => 5, 'description' => 'Functions with some issues.'],
                            ['level' => 10, 'description' => 'Functions perfectly as intended.'],
                        ],
                    ],
                    [
                        'name' => 'Impact Potential',
                        'description' => 'Potential social/market impact',
                        'max_score' => 10,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'Limited or no clear impact.'],
                            ['level' => 5, 'description' => 'Moderate potential impact.'],
                            ['level' => 10, 'description' => 'Significant potential impact on society/market.'],
                        ],
                    ],
                    [
                        'name' => 'Technical Quality',
                        'description' => 'Build quality and technical execution',
                        'max_score' => 10,
                        'score_levels' => [
                            ['level' => 1, 'description' => 'Poor technical quality.'],
                            ['level' => 5, 'description' => 'Adequate technical quality.'],
                            ['level' => 10, 'description' => 'Excellent technical quality and craftsmanship.'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Preview rubric as jury would see it
     */
    public function preview(Event $event)
    {
        // Verify ownership
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = $event->rubricCategories()
            ->with(['items.scoreLevels'])
            ->where('is_active', DB::raw('true'))
            ->orderBy('order')
            ->get();

        return view('organizer.rubrics.preview', compact('event', 'categories'));
    }
}

