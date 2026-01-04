<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    private $models = [
        'gemini-2.0-flash-exp',
        'gemini-1.5-pro-latest',
        'gemini-1.5-flash-latest',
    ];

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        
        if (!$this->apiKey || $this->apiKey === 'your_api_key_here') {
            throw new \Exception('Gemini API key is not configured. Please add your API key to the .env file. Get a free key at: https://makersuite.google.com/app/apikey');
        }
    }
    
    /**
     * Try multiple models with fallback
     */
    private function callGeminiAPI(array $payload)
    {
        $lastError = null;
        
        foreach ($this->models as $model) {
            try {
                $response = Http::timeout(60)->post(
                    "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}",
                    $payload
                );
                
                if ($response->successful()) {
                    return $response;
                }
                
                $lastError = $response->json();
                Log::warning("Gemini model {$model} failed", ['error' => $lastError]);
                
            } catch (\Exception $e) {
                $lastError = ['error' => ['message' => $e->getMessage()]];
                Log::warning("Gemini model {$model} exception", ['error' => $e->getMessage()]);
                continue;
            }
        }
        
        throw new \Exception('All Gemini models failed. Last error: ' . json_encode($lastError));
    }

    /**
     * Extract rubric structure from text description
     */
    public function generateRubricFromDescription(string $description): array
    {
        $prompt = $this->buildRubricPrompt($description);

        try {
            $response = $this->callGeminiAPI([
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 8192,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Extract text from Gemini response format
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Extract JSON from response
                return $this->parseRubricResponse($text);
            }

            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            throw new \Exception('Failed to generate rubric from Gemini API. Error: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Gemini Service Error', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Extract rubric from uploaded file
     */
    public function extractRubricFromFile(string $filePath, string $mimeType, string $context = ''): array
    {
        // For images, use Gemini Vision directly
        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg'])) {
            return $this->extractWithVision($filePath, $mimeType, $context);
        }

        // For PDFs, try Vision first (for scanned/image PDFs), fallback to text extraction
        if ($mimeType === 'application/pdf') {
            try {
                // Try Vision API first for PDFs (works better for scanned/image PDFs)
                return $this->extractWithVision($filePath, $mimeType, $context);
            } catch (\Exception $e) {
                Log::info('PDF Vision failed, trying text extraction', ['error' => $e->getMessage()]);
                // Fallback to text extraction
                $extractedText = $this->extractFromPdf($filePath);
                return $this->generateRubricFromDescription(
                    "Extract the rubric structure from this PDF document:\n\n" . 
                    $extractedText . 
                    "\n\nAdditional context: " . $context
                );
            }
        }

        // For Excel/Word files, extract text first then analyze
        $extractedText = $this->extractTextFromDocument($filePath, $mimeType);
        
        if (empty($extractedText) || str_contains($extractedText, 'Error extracting')) {
            throw new \Exception('Could not extract text from the uploaded file. Please try uploading an image (JPG, PNG) or PDF version instead.');
        }
        
        return $this->generateRubricFromDescription(
            "Extract and analyze the rubric structure from this document:\n\n" . 
            $extractedText . 
            "\n\nAdditional context from organizer: " . $context .
            "\n\nPlease create a structured rubric based on this content."
        );
    }

    /**
     * Use Gemini Vision to analyze images/PDFs
     */
    private function extractWithVision(string $filePath, string $mimeType, string $context = ''): array
    {
        try {
            // Check file size (Gemini has limits)
            $fileSize = filesize($filePath);
            if ($fileSize > 10 * 1024 * 1024) { // 10MB limit
                throw new \Exception('File size exceeds 10MB limit. Please upload a smaller file.');
            }

            $imageData = base64_encode(file_get_contents($filePath));

            $promptText = 'You are an expert in analyzing evaluation rubrics and extracting structured assessment criteria. Carefully analyze this image/document which contains a rubric or evaluation criteria.';
            
            if ($context) {
                $promptText .= "\n\n📋 ORGANIZER CONTEXT:\n{$context}\n\nUse this context to better understand the rubric requirements, event type, and evaluation focus.";
            }
            
            $promptText .= "\n\n🎯 YOUR TASK:\nExtract and structure the rubric information EXACTLY as shown in this document. You MUST:\n\n✅ REQUIRED - Extract exact text from the document:\n- Use the EXACT category names as written in the document\n- Use the EXACT criterion names as written in the document  \n- Use the EXACT descriptions word-for-word from the document\n- Use the EXACT score labels as shown in the document\n- Use the EXACT point values from the document\n- Preserve ALL original wording and terminology\n\n❌ DO NOT:\n- Create or invent ANY new descriptions\n- Paraphrase or rewrite ANY text from the document\n- Add generic or made-up criteria\n- Change the original wording in any way\n\nReturn a JSON object with this exact format (no markdown, no backticks):\n{\n  \"categories\": [\n    {\n      \"name\": \"EXACT_CATEGORY_NAME_FROM_DOCUMENT\",\n      \"description\": \"EXACT description text from the document\",\n      \"max_score\": 30,\n      \"items\": [\n        {\n          \"name\": \"EXACT criterion name from document\",\n          \"description\": \"EXACT description from document\",\n          \"max_score\": 10,\n          \"score_levels\": [\n            {\"score\": 1, \"label\": \"EXACT label from document\", \"description\": \"EXACT description from document\"},\n            {\"score\": 5, \"label\": \"EXACT label from document\", \"description\": \"EXACT description from document\"},\n            {\"score\": 10, \"label\": \"EXACT label from document\", \"description\": \"EXACT description from document\"}\n          ]\n        }\n      ]\n    }\n  ]\n}\n\n⚠️ CRITICAL: Return ONLY the actual text found in the document. If you cannot read certain text clearly, use your best interpretation but stay as close as possible to the visible text. Return ONLY valid JSON, nothing else.";

            $response = $this->callGeminiAPI([
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $promptText
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'topK' => 20,
                    'topP' => 0.8,
                    'maxOutputTokens' => 8192,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                return $this->parseRubricResponse($text);
            }

            Log::error('Gemini Vision API Error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            throw new \Exception('Failed to analyze document with Gemini Vision. Error: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Gemini Vision Error', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Build prompt for rubric generation
     */
    private function buildRubricPrompt(string $description): string
    {
        return <<<PROMPT
You are an expert in analyzing evaluation rubrics and extracting structured assessment criteria. Based on the provided content, extract a detailed, well-structured evaluation rubric.

Input Content: {$description}

🎯 YOUR TASK: Extract the rubric structure EXACTLY from the content provided above.

✅ REQUIRED - Use exact text from the input:
- Extract the EXACT category names as written
- Extract the EXACT criterion/item names as written
- Copy the EXACT descriptions word-for-word from the content
- Use the EXACT score labels as shown
- Use the EXACT point values from the content
- Preserve ALL original wording and phrasing

❌ DO NOT:
- Create or invent new descriptions
- Paraphrase or rewrite any text
- Add generic criteria not in the content
- Change original terminology

Return ONLY a valid JSON object (no markdown, no backticks, no explanations) with this exact structure:
{
  "categories": [
    {
      "name": "EXACT_CATEGORY_NAME_FROM_CONTENT",
      "description": "EXACT description from the content",
      "max_score": 30,
      "items": [
        {
          "name": "EXACT criterion name from content",
          "description": "EXACT description from content",
          "max_score": 10,
          "score_levels": [
            {
              "score": 1,
              "label": "EXACT label from content",
              "description": "EXACT criteria description from content"
            },
            {
              "score": 3,
              "label": "EXACT label from content",
              "description": "EXACT criteria description from content"
            },
            {
              "score": 5,
              "label": "EXACT label from content",
              "description": "EXACT criteria description from content"
            },
            {
              "score": 7,
              "label": "EXACT label from content",
              "description": "EXACT criteria description from content"
            },
            {
              "score": 10,
              "label": "EXACT label from content",
              "description": "EXACT criteria description from content"
            }
          ]
        }
      ]
    }
  ]
}

⚠️ CRITICAL: Return ONLY the text that appears in the provided content. Extract, don't generate. Copy exact wording from the content above.

Return ONLY the JSON object, nothing else.Return ONLY the JSON object, nothing else.
PROMPT;
    }

    /**
     * Parse Gemini response and extract JSON
     */
    private function parseRubricResponse(string $text): array
    {
        // Remove markdown code blocks if present
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        // Try to find JSON in the response
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $json = $matches[0];
            $data = json_decode($json, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($data['categories'])) {
                return $data;
            }
        }

        // If parsing fails, return a default structure
        Log::warning('Failed to parse Gemini response', ['text' => $text]);
        return $this->getDefaultStructure();
    }

    /**
     * Extract text from document files
     */
    private function extractTextFromDocument(string $filePath, string $mimeType): string
    {
        // For Excel files
        if (str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'excel')) {
            return $this->extractFromExcel($filePath);
        }

        // For Word documents
        if (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
            return $this->extractFromWord($filePath);
        }

        // For PDF files
        if (str_contains($mimeType, 'pdf')) {
            return $this->extractFromPdf($filePath);
        }

        return '';
    }

    /**
     * Extract text from Excel files (XLS, XLSX)
     */
    private function extractFromExcel(string $filePath): string
    {
        try {
            // Check if zip extension is available (required for Excel files)
            if (!extension_loaded('zip')) {
                Log::warning('ZIP extension not loaded, cannot extract Excel file');
                throw new \Exception('ZIP extension is not enabled. Excel files cannot be processed. Please use PDF or Image format instead, or enable zip extension in php.ini');
            }
            
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $extractedText = '';
            
            foreach ($spreadsheet->getAllSheets() as $sheetIndex => $sheet) {
                $sheetName = $sheet->getTitle();
                $extractedText .= "Sheet: {$sheetName}\n";
                $extractedText .= str_repeat('=', 50) . "\n";
                
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Limit to reasonable size to avoid token limits
                $maxRows = min($highestRow, 100);
                
                for ($row = 1; $row <= $maxRows; $row++) {
                    $rowData = [];
                    for ($col = 'A'; $col <= $highestColumn; $col++) {
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        if (!empty($cellValue)) {
                            $rowData[] = $cellValue;
                        }
                    }
                    if (!empty($rowData)) {
                        $extractedText .= implode(' | ', $rowData) . "\n";
                    }
                }
                $extractedText .= "\n";
            }
            
            return $extractedText;
            
        } catch (\Exception $e) {
            Log::error('Excel extraction error', ['message' => $e->getMessage()]);
            return "Error extracting Excel file: " . $e->getMessage();
        }
    }

    /**
     * Extract text from Word documents (DOC, DOCX)
     */
    private function extractFromWord(string $filePath): string
    {
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $extractedText = '';
            
            foreach ($phpWord->getSections() as $sectionIndex => $section) {
                $extractedText .= "Section " . ($sectionIndex + 1) . ":\n";
                $extractedText .= str_repeat('-', 50) . "\n";
                
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $extractedText .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        // Handle nested elements (like tables, textboxes)
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $extractedText .= $childElement->getText() . " ";
                            }
                        }
                        $extractedText .= "\n";
                    }
                }
                $extractedText .= "\n";
            }
            
            return $extractedText;
            
        } catch (\Exception $e) {
            Log::error('Word extraction error', ['message' => $e->getMessage()]);
            return "Error extracting Word file: " . $e->getMessage();
        }
    }

    /**
     * Extract text from PDF files
     */
    private function extractFromPdf(string $filePath): string
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            
            $extractedText = $pdf->getText();
            
            // Limit text size to avoid token limits (first 5000 characters)
            if (strlen($extractedText) > 5000) {
                $extractedText = substr($extractedText, 0, 5000) . "\n\n[Text truncated for analysis...]";
            }
            
            return $extractedText;
            
        } catch (\Exception $e) {
            Log::error('PDF extraction error', ['message' => $e->getMessage()]);
            return "Error extracting PDF file: " . $e->getMessage();
        }
    }

    /**
     * Get default rubric structure as fallback
     */
    private function getDefaultStructure(): array
    {
        return [
            'categories' => [
                [
                    'name' => 'CONTENT',
                    'description' => 'Content quality and completeness',
                    'max_score' => 40,
                    'items' => [
                        [
                            'name' => 'Abstract',
                            'description' => 'Clarity and completeness of abstract',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 2, 'label' => 'Poor', 'description' => 'Unclear or incomplete'],
                                ['score' => 6, 'label' => 'Good', 'description' => 'Clear and mostly complete'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Very clear and comprehensive'],
                            ]
                        ],
                        [
                            'name' => 'Methodology',
                            'description' => 'Research methods and approach',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 5, 'label' => 'Poor', 'description' => 'Unclear methodology'],
                                ['score' => 10, 'label' => 'Good', 'description' => 'Clear methodology'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'Outstanding methodology'],
                            ]
                        ],
                        [
                            'name' => 'Results',
                            'description' => 'Quality of results and analysis',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 5, 'label' => 'Poor', 'description' => 'Limited results'],
                                ['score' => 10, 'label' => 'Good', 'description' => 'Good results'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'Excellent results'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'PRESENTATION',
                    'description' => 'Presentation quality and delivery',
                    'max_score' => 30,
                    'items' => [
                        [
                            'name' => 'Visual Design',
                            'description' => 'Overall visual appeal',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Poor', 'description' => 'Poor design'],
                                ['score' => 7, 'label' => 'Good', 'description' => 'Good design'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Excellent design'],
                            ]
                        ],
                        [
                            'name' => 'Clarity',
                            'description' => 'Clarity of presentation',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Poor', 'description' => 'Unclear'],
                                ['score' => 7, 'label' => 'Good', 'description' => 'Clear'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Very clear'],
                            ]
                        ],
                        [
                            'name' => 'Organization',
                            'description' => 'Logical organization',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Poor', 'description' => 'Poorly organized'],
                                ['score' => 7, 'label' => 'Good', 'description' => 'Well organized'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Excellently organized'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'INNOVATION',
                    'description' => 'Innovation and originality',
                    'max_score' => 30,
                    'items' => [
                        [
                            'name' => 'Originality',
                            'description' => 'Level of originality',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 5, 'label' => 'Poor', 'description' => 'Not original'],
                                ['score' => 10, 'label' => 'Good', 'description' => 'Somewhat original'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'Highly original'],
                            ]
                        ],
                        [
                            'name' => 'Impact',
                            'description' => 'Potential impact',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 5, 'label' => 'Poor', 'description' => 'Low impact'],
                                ['score' => 10, 'label' => 'Good', 'description' => 'Moderate impact'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'High impact'],
                            ]
                        ],
                    ]
                ],
            ]
        ];
    }

    /**
     * Generate certificate text overlay instructions using Gemini AI
     * 
     * @param array $data Certificate data (name, project, event, date)
     * @param string $userPrompt User's placement instructions
     * @return string Gemini's response with text placement suggestions
     */
    public function generateCertificateTextPlacement(array $data, string $userPrompt): string
    {
        $prompt = "You are a professional certificate designer. Based on the following certificate data and user instructions, suggest how to beautifully place the text on the certificate.\n\n";
        
        $prompt .= "Certificate Data:\n";
        $prompt .= "- Participant Name: {$data['participant_name']}\n";
        
        if (!empty($data['project_name'])) {
            $prompt .= "- Project/Paper Title: {$data['project_name']}\n";
        }
        
        $prompt .= "- Event Name: {$data['event_name']}\n";
        $prompt .= "- Event Date: {$data['event_date']}\n";
        $prompt .= "- Role: {$data['role']}\n";
        
        if (!empty($data['awards'])) {
            $prompt .= "- Awards: {$data['awards']}\n";
        }
        
        $prompt .= "\nUser's Placement Instructions:\n{$userPrompt}\n\n";
        $prompt .= "Please provide a brief summary of how the text should be arranged on the certificate to look professional and aligned with the user's instructions.";

        try {
            $response = $this->callGeminiAPI([
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'topK' => 20,
                    'topP' => 0.8,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'AI processing completed';
            }

            return 'Could not generate AI placement instructions';

        } catch (\Exception $e) {
            Log::error('Gemini certificate generation failed: ' . $e->getMessage());
            return 'AI processing unavailable';
        }
    }
}

