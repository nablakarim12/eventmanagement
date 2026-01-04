<?php

namespace App\Services;

class TemplateRubricService
{
    /**
     * Generate a rubric from uploaded document or description
     */
    public function generateFromDocument(string $filePath, string $mimeType, string $description = ''): array
    {
        // Extract text from document
        $extractedText = $this->extractTextFromDocument($filePath, $mimeType);
        
        // Parse the extracted text to detect rubric structure
        if (!empty($extractedText)) {
            $parsedRubric = $this->parseRubricFromText($extractedText, $description);
            if ($parsedRubric) {
                return $parsedRubric;
            }
        }
        
        // Fallback to template generation
        return $this->generateFromTemplate($description . "\n\n" . substr($extractedText, 0, 500));
    }
    
    /**
     * Extract text from various file formats
     */
    private function extractTextFromDocument(string $filePath, string $mimeType): string
    {
        try {
            // Excel files
            if (str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'excel')) {
                return $this->extractFromExcel($filePath);
            }
            
            // Word documents
            if (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
                return $this->extractFromWord($filePath);
            }
            
            // PDF files
            if (str_contains($mimeType, 'pdf')) {
                return $this->extractFromPdf($filePath);
            }
            
            return '';
        } catch (\Exception $e) {
            \Log::warning('Document extraction error: ' . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Extract text from Excel files
     */
    private function extractFromExcel(string $filePath): string
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $text = '';
            
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $text .= "Sheet: " . $sheet->getTitle() . "\n\n";
                
                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    
                    $rowData = [];
                    foreach ($cellIterator as $cell) {
                        $value = $cell->getValue();
                        if (!empty($value)) {
                            $rowData[] = $value;
                        }
                    }
                    
                    if (!empty($rowData)) {
                        $text .= implode(' | ', $rowData) . "\n";
                    }
                }
                $text .= "\n";
            }
            
            return $text;
        } catch (\Exception $e) {
            return "Error extracting from Excel: " . $e->getMessage();
        }
    }
    
    /**
     * Extract text from Word documents
     */
    private function extractFromWord(string $filePath): string
    {
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText() . " ";
                            }
                        }
                        $text .= "\n";
                    }
                }
            }
            
            return $text;
        } catch (\Exception $e) {
            return "Error extracting from Word: " . $e->getMessage();
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
            $text = $pdf->getText();
            
            return substr($text, 0, 5000);
        } catch (\Exception $e) {
            return "Error extracting from PDF: " . $e->getMessage();
        }
    }
    
    /**
     * Parse rubric structure from extracted text
     */
    private function parseRubricFromText(string $text, string $context = ''): ?array
    {
        $text = strtolower($text);
        $lines = explode("\n", $text);
        
        $categories = [];
        $currentCategory = null;
        $scores = [];
        
        // Detect scoring patterns
        preg_match_all('/(\d+)\s*(?:points?|pts?|marks?)/i', $text, $scoreMatches);
        if (!empty($scoreMatches[1])) {
            $scores = array_map('intval', $scoreMatches[1]);
        }
        
        // Look for category/section headers
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Detect potential category names (headers, bold text, numbered items)
            if (preg_match('/^[\d\.\)]+\s*(.+)$/i', $line, $match) ||
                preg_match('/^([a-z\s]+):$/i', $line, $match) ||
                strlen($line) < 50 && !str_contains($line, '.')) {
                
                $categoryName = trim($match[1] ?? $line);
                
                // Skip common non-category words
                if (strlen($categoryName) > 3 && 
                    !in_array($categoryName, ['the', 'and', 'for', 'with'])) {
                    
                    if ($currentCategory) {
                        $categories[] = $currentCategory;
                    }
                    
                    $currentCategory = [
                        'name' => ucwords($categoryName),
                        'potential_items' => []
                    ];
                }
            } elseif ($currentCategory && strlen($line) > 10) {
                $currentCategory['potential_items'][] = $line;
            }
        }
        
        if ($currentCategory) {
            $categories[] = $currentCategory;
        }
        
        // If we found structured content, build rubric
        if (count($categories) >= 2) {
            return $this->buildRubricFromParsedData($categories, $scores, $context);
        }
        
        return null;
    }
    
    /**
     * Build rubric from parsed data
     */
    private function buildRubricFromParsedData(array $categories, array $scores, string $context): array
    {
        $maxScore = !empty($scores) ? max($scores) : 100;
        $categoryCount = count($categories);
        $scorePerCategory = (int)($maxScore / $categoryCount);
        
        $rubricCategories = [];
        
        foreach ($categories as $index => $cat) {
            $categoryScore = $scorePerCategory;
            if ($index === $categoryCount - 1) {
                // Last category gets remaining points
                $categoryScore = $maxScore - (array_sum(array_column($rubricCategories, 'max_score')));
            }
            
            $items = [];
            $itemCount = max(1, min(3, count($cat['potential_items'])));
            $scorePerItem = (int)($categoryScore / $itemCount);
            
            for ($i = 0; $i < $itemCount; $i++) {
                $itemName = $cat['potential_items'][$i] ?? "Criterion " . ($i + 1);
                $itemScore = $scorePerItem;
                
                if ($i === $itemCount - 1) {
                    $itemScore = $categoryScore - array_sum(array_column($items, 'max_score'));
                }
                
                $items[] = [
                    'name' => ucfirst(substr($itemName, 0, 50)),
                    'description' => 'Evaluate based on the criteria described in the document',
                    'max_score' => $itemScore,
                    'score_levels' => $this->generateScoreLevels($itemScore)
                ];
            }
            
            $rubricCategories[] = [
                'name' => $cat['name'],
                'description' => 'Evaluation criteria for ' . strtolower($cat['name']),
                'max_score' => $categoryScore,
                'items' => $items
            ];
        }
        
        return ['categories' => $rubricCategories];
    }
    
    /**
     * Generate score levels based on max score
     */
    private function generateScoreLevels(int $maxScore): array
    {
        $levels = [];
        $levelCount = min(4, max(3, (int)($maxScore / 5)));
        
        for ($i = 0; $i < $levelCount; $i++) {
            $score = (int)(($i + 1) * $maxScore / $levelCount);
            $label = match($i) {
                0 => 'Needs Improvement',
                1 => 'Satisfactory',
                2 => 'Good',
                default => 'Excellent'
            };
            
            $levels[] = [
                'score' => $score,
                'label' => $label,
                'description' => "Demonstrates {$label} level of achievement"
            ];
        }
        
        return $levels;
    }

    /**
     * Generate a rubric from templates without using AI
     */
    public function generateFromTemplate(string $description, ?string $eventType = null): array
    {
        // Use event type if provided, otherwise detect from description
        if (empty($eventType)) {
            $eventType = 'innovation';
        }
        
        // Detect rubric type from description
        $type = $this->detectRubricType($description);
        
        return match($type) {
            'innovation' => $this->getInnovationTemplate(),
            'conference' => $this->getConferenceTemplate(),
            'research' => $this->getResearchTemplate(),
            'presentation' => $this->getPresentationTemplate(),
            default => $this->getGenericTemplate(),
        };
    }

    private function detectRubricType(string $description): string
    {
        $description = strtolower($description);
        
        if (str_contains($description, 'innovation') || str_contains($description, 'invention')) {
            return 'innovation';
        }
        if (str_contains($description, 'conference') || str_contains($description, 'paper')) {
            return 'conference';
        }
        if (str_contains($description, 'research') || str_contains($description, 'thesis')) {
            return 'research';
        }
        if (str_contains($description, 'presentation') || str_contains($description, 'pitch')) {
            return 'presentation';
        }
        
        return 'generic';
    }

    private function getInnovationTemplate(): array
    {
        return [
            'categories' => [
                [
                    'name' => 'Innovation & Creativity',
                    'description' => 'Originality and creative approach to problem-solving',
                    'max_score' => 25,
                    'items' => [
                        [
                            'name' => 'Novelty',
                            'description' => 'How unique and original is the solution?',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Basic', 'description' => 'Common or existing solution with minor modifications'],
                                ['score' => 8, 'label' => 'Good', 'description' => 'Notable improvements to existing approaches'],
                                ['score' => 12, 'label' => 'Very Good', 'description' => 'Significant innovation with clear differentiation'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'Groundbreaking and highly original solution'],
                            ]
                        ],
                        [
                            'name' => 'Creative Approach',
                            'description' => 'Application of creative thinking in design and execution',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 2, 'label' => 'Basic', 'description' => 'Conventional approach with limited creativity'],
                                ['score' => 5, 'label' => 'Good', 'description' => 'Some creative elements demonstrated'],
                                ['score' => 8, 'label' => 'Very Good', 'description' => 'Strong creative thinking throughout'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Exceptional creativity and imagination'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Technical Implementation',
                    'description' => 'Quality of technical execution and functionality',
                    'max_score' => 30,
                    'items' => [
                        [
                            'name' => 'Functionality',
                            'description' => 'Does the solution work as intended?',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Poor', 'description' => 'Multiple issues, limited functionality'],
                                ['score' => 8, 'label' => 'Fair', 'description' => 'Basic functionality with some issues'],
                                ['score' => 12, 'label' => 'Good', 'description' => 'Works well with minor issues'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'Flawless execution and performance'],
                            ]
                        ],
                        [
                            'name' => 'Technical Quality',
                            'description' => 'Quality of construction, coding, or implementation',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Poor', 'description' => 'Low quality, numerous technical issues'],
                                ['score' => 8, 'label' => 'Fair', 'description' => 'Acceptable quality with room for improvement'],
                                ['score' => 12, 'label' => 'Good', 'description' => 'High quality with best practices applied'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'Outstanding technical excellence'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Impact & Feasibility',
                    'description' => 'Potential impact and practical implementation',
                    'max_score' => 25,
                    'items' => [
                        [
                            'name' => 'Problem Solving',
                            'description' => 'How effectively does it address the problem?',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Limited', 'description' => 'Minimal impact on the problem'],
                                ['score' => 8, 'label' => 'Moderate', 'description' => 'Addresses some aspects of the problem'],
                                ['score' => 12, 'label' => 'Strong', 'description' => 'Effectively solves the main problem'],
                                ['score' => 15, 'label' => 'Outstanding', 'description' => 'Comprehensive solution with wide impact'],
                            ]
                        ],
                        [
                            'name' => 'Market Potential',
                            'description' => 'Commercial viability and scalability',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 2, 'label' => 'Low', 'description' => 'Limited market potential'],
                                ['score' => 5, 'label' => 'Moderate', 'description' => 'Some commercial opportunity'],
                                ['score' => 8, 'label' => 'High', 'description' => 'Strong market potential'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Outstanding market opportunity'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Presentation',
                    'description' => 'Quality of demonstration and communication',
                    'max_score' => 20,
                    'items' => [
                        [
                            'name' => 'Clarity',
                            'description' => 'How clearly is the innovation explained?',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 2, 'label' => 'Unclear', 'description' => 'Difficult to understand'],
                                ['score' => 5, 'label' => 'Fair', 'description' => 'Basic understanding achieved'],
                                ['score' => 8, 'label' => 'Clear', 'description' => 'Well explained and easy to follow'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Exceptionally clear and compelling'],
                            ]
                        ],
                        [
                            'name' => 'Visual Presentation',
                            'description' => 'Quality of visuals, demo, and supporting materials',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 2, 'label' => 'Basic', 'description' => 'Minimal or poor quality materials'],
                                ['score' => 5, 'label' => 'Adequate', 'description' => 'Acceptable presentation materials'],
                                ['score' => 8, 'label' => 'Professional', 'description' => 'High-quality, well-designed materials'],
                                ['score' => 10, 'label' => 'Outstanding', 'description' => 'Exceptional presentation quality'],
                            ]
                        ],
                    ]
                ],
            ]
        ];
    }

    private function getConferenceTemplate(): array
    {
        return [
            'categories' => [
                [
                    'name' => 'Research Quality',
                    'description' => 'Rigor and quality of research methodology',
                    'max_score' => 30,
                    'items' => [
                        [
                            'name' => 'Methodology',
                            'description' => 'Appropriateness and rigor of research methods',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Weak', 'description' => 'Methodology unclear or inappropriate'],
                                ['score' => 8, 'label' => 'Adequate', 'description' => 'Acceptable methodology with limitations'],
                                ['score' => 12, 'label' => 'Strong', 'description' => 'Well-designed and rigorous approach'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'Exemplary methodology and execution'],
                            ]
                        ],
                        [
                            'name' => 'Data Analysis',
                            'description' => 'Quality of data collection and analysis',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Weak', 'description' => 'Limited or flawed analysis'],
                                ['score' => 8, 'label' => 'Adequate', 'description' => 'Reasonable analysis with some gaps'],
                                ['score' => 12, 'label' => 'Strong', 'description' => 'Thorough and appropriate analysis'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'Sophisticated and comprehensive analysis'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Contribution',
                    'description' => 'Originality and significance of contribution',
                    'max_score' => 25,
                    'items' => [
                        [
                            'name' => 'Originality',
                            'description' => 'Novelty of research and findings',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Limited', 'description' => 'Incremental or derivative work'],
                                ['score' => 8, 'label' => 'Moderate', 'description' => 'Some original insights'],
                                ['score' => 12, 'label' => 'Significant', 'description' => 'Notable original contribution'],
                                ['score' => 15, 'label' => 'Outstanding', 'description' => 'Groundbreaking original work'],
                            ]
                        ],
                        [
                            'name' => 'Significance',
                            'description' => 'Impact and importance to the field',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 2, 'label' => 'Limited', 'description' => 'Minor contribution'],
                                ['score' => 5, 'label' => 'Moderate', 'description' => 'Useful contribution to field'],
                                ['score' => 8, 'label' => 'High', 'description' => 'Important contribution'],
                                ['score' => 10, 'label' => 'Exceptional', 'description' => 'Major impact on field'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Writing Quality',
                    'description' => 'Clarity and quality of written presentation',
                    'max_score' => 25,
                    'items' => [
                        [
                            'name' => 'Organization',
                            'description' => 'Structure and flow of the paper',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 2, 'label' => 'Poor', 'description' => 'Disorganized or unclear structure'],
                                ['score' => 5, 'label' => 'Adequate', 'description' => 'Basic organization maintained'],
                                ['score' => 8, 'label' => 'Good', 'description' => 'Well-organized and logical flow'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Exceptionally clear structure'],
                            ]
                        ],
                        [
                            'name' => 'Clarity',
                            'description' => 'Writing clarity and communication effectiveness',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Unclear', 'description' => 'Difficult to understand'],
                                ['score' => 8, 'label' => 'Clear', 'description' => 'Generally well-written'],
                                ['score' => 12, 'label' => 'Very Clear', 'description' => 'Highly readable and clear'],
                                ['score' => 15, 'label' => 'Outstanding', 'description' => 'Exemplary writing quality'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Presentation',
                    'description' => 'Quality of oral presentation and Q&A',
                    'max_score' => 20,
                    'items' => [
                        [
                            'name' => 'Delivery',
                            'description' => 'Presentation skills and engagement',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 2, 'label' => 'Weak', 'description' => 'Poor delivery or engagement'],
                                ['score' => 5, 'label' => 'Adequate', 'description' => 'Acceptable presentation'],
                                ['score' => 8, 'label' => 'Strong', 'description' => 'Confident and engaging'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Outstanding presentation skills'],
                            ]
                        ],
                        [
                            'name' => 'Q&A Response',
                            'description' => 'Handling of questions and discussion',
                            'max_score' => 10,
                            'score_levels' => [
                                ['score' => 2, 'label' => 'Weak', 'description' => 'Struggled with questions'],
                                ['score' => 5, 'label' => 'Adequate', 'description' => 'Answered basic questions'],
                                ['score' => 8, 'label' => 'Strong', 'description' => 'Thorough and thoughtful responses'],
                                ['score' => 10, 'label' => 'Excellent', 'description' => 'Exceptional Q&A handling'],
                            ]
                        ],
                    ]
                ],
            ]
        ];
    }

    private function getResearchTemplate(): array
    {
        return $this->getConferenceTemplate(); // Similar structure
    }

    private function getPresentationTemplate(): array
    {
        return [
            'categories' => [
                [
                    'name' => 'Content',
                    'description' => 'Quality and relevance of presentation content',
                    'max_score' => 40,
                    'items' => [
                        [
                            'name' => 'Understanding',
                            'description' => 'Depth of knowledge demonstrated',
                            'max_score' => 20,
                            'score_levels' => [
                                ['score' => 5, 'label' => 'Basic', 'description' => 'Surface-level understanding'],
                                ['score' => 10, 'label' => 'Good', 'description' => 'Solid understanding'],
                                ['score' => 15, 'label' => 'Very Good', 'description' => 'Deep understanding'],
                                ['score' => 20, 'label' => 'Expert', 'description' => 'Expert-level mastery'],
                            ]
                        ],
                        [
                            'name' => 'Relevance',
                            'description' => 'Alignment with topic and objectives',
                            'max_score' => 20,
                            'score_levels' => [
                                ['score' => 5, 'label' => 'Off-topic', 'description' => 'Poorly aligned'],
                                ['score' => 10, 'label' => 'Relevant', 'description' => 'Generally on-topic'],
                                ['score' => 15, 'label' => 'Focused', 'description' => 'Well-aligned content'],
                                ['score' => 20, 'label' => 'Perfect', 'description' => 'Perfectly targeted'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Delivery',
                    'description' => 'Presentation skills and audience engagement',
                    'max_score' => 30,
                    'items' => [
                        [
                            'name' => 'Communication',
                            'description' => 'Clarity and effectiveness of delivery',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Unclear', 'description' => 'Hard to follow'],
                                ['score' => 8, 'label' => 'Clear', 'description' => 'Easy to understand'],
                                ['score' => 12, 'label' => 'Engaging', 'description' => 'Captivating delivery'],
                                ['score' => 15, 'label' => 'Outstanding', 'description' => 'Exceptional communication'],
                            ]
                        ],
                        [
                            'name' => 'Engagement',
                            'description' => 'Audience connection and interaction',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Low', 'description' => 'Minimal engagement'],
                                ['score' => 8, 'label' => 'Moderate', 'description' => 'Some audience connection'],
                                ['score' => 12, 'label' => 'High', 'description' => 'Strong audience engagement'],
                                ['score' => 15, 'label' => 'Excellent', 'description' => 'Outstanding engagement'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Visual Aids',
                    'description' => 'Quality and effectiveness of supporting materials',
                    'max_score' => 30,
                    'items' => [
                        [
                            'name' => 'Design',
                            'description' => 'Visual design and aesthetics',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Poor', 'description' => 'Unprofessional or cluttered'],
                                ['score' => 8, 'label' => 'Adequate', 'description' => 'Acceptable design'],
                                ['score' => 12, 'label' => 'Professional', 'description' => 'Well-designed visuals'],
                                ['score' => 15, 'label' => 'Outstanding', 'description' => 'Exceptional design quality'],
                            ]
                        ],
                        [
                            'name' => 'Effectiveness',
                            'description' => 'How well visuals support the message',
                            'max_score' => 15,
                            'score_levels' => [
                                ['score' => 3, 'label' => 'Unhelpful', 'description' => 'Distracting or irrelevant'],
                                ['score' => 8, 'label' => 'Helpful', 'description' => 'Support main points'],
                                ['score' => 12, 'label' => 'Very Effective', 'description' => 'Enhance understanding'],
                                ['score' => 15, 'label' => 'Perfect', 'description' => 'Perfectly complement content'],
                            ]
                        ],
                    ]
                ],
            ]
        ];
    }

    private function getGenericTemplate(): array
    {
        return [
            'categories' => [
                [
                    'name' => 'Quality',
                    'description' => 'Overall quality of work',
                    'max_score' => 40,
                    'items' => [
                        [
                            'name' => 'Excellence',
                            'description' => 'Level of quality demonstrated',
                            'max_score' => 40,
                            'score_levels' => [
                                ['score' => 10, 'label' => 'Below Standard', 'description' => 'Does not meet expectations'],
                                ['score' => 20, 'label' => 'Meets Standard', 'description' => 'Acceptable quality'],
                                ['score' => 30, 'label' => 'Exceeds Standard', 'description' => 'High quality work'],
                                ['score' => 40, 'label' => 'Outstanding', 'description' => 'Exceptional quality'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Completeness',
                    'description' => 'Thoroughness and attention to requirements',
                    'max_score' => 30,
                    'items' => [
                        [
                            'name' => 'Coverage',
                            'description' => 'How completely requirements are addressed',
                            'max_score' => 30,
                            'score_levels' => [
                                ['score' => 8, 'label' => 'Incomplete', 'description' => 'Major gaps in coverage'],
                                ['score' => 15, 'label' => 'Adequate', 'description' => 'Most requirements met'],
                                ['score' => 23, 'label' => 'Complete', 'description' => 'All requirements addressed'],
                                ['score' => 30, 'label' => 'Comprehensive', 'description' => 'Exceeds all requirements'],
                            ]
                        ],
                    ]
                ],
                [
                    'name' => 'Presentation',
                    'description' => 'Quality of delivery and communication',
                    'max_score' => 30,
                    'items' => [
                        [
                            'name' => 'Communication',
                            'description' => 'Clarity and effectiveness of presentation',
                            'max_score' => 30,
                            'score_levels' => [
                                ['score' => 8, 'label' => 'Unclear', 'description' => 'Difficult to understand'],
                                ['score' => 15, 'label' => 'Clear', 'description' => 'Well communicated'],
                                ['score' => 23, 'label' => 'Professional', 'description' => 'High quality presentation'],
                                ['score' => 30, 'label' => 'Outstanding', 'description' => 'Exceptional presentation'],
                            ]
                        ],
                    ]
                ],
            ]
        ];
    }
}
