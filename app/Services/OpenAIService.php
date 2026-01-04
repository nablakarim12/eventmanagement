<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private $apiKey;
    private $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your_api_key_here';
    }

    /**
     * Generate rubric using OpenAI GPT
     */
    public function generateRubric(string $description, string $extractedText = ''): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('OpenAI API key is not configured');
        }

        $prompt = $this->buildPrompt($description, $extractedText);

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert in creating evaluation rubrics. Return only valid JSON, no markdown formatting.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['choices'][0]['message']['content'] ?? '';
                return $this->parseResponse($text);
            }

            throw new \Exception('OpenAI API request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('OpenAI error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function buildPrompt(string $description, string $extractedText): string
    {
        $prompt = "Create an evaluation rubric based on the following requirements:\n\n";
        $prompt .= $description . "\n\n";
        
        if ($extractedText) {
            $prompt .= "Reference document content:\n" . substr($extractedText, 0, 3000) . "\n\n";
        }

        $prompt .= 'Return a JSON object with this exact format (no markdown, no backticks):
{
  "categories": [
    {
      "name": "Category Name",
      "description": "What this evaluates",
      "max_score": 30,
      "items": [
        {
          "name": "Criterion Name",
          "description": "What to evaluate",
          "max_score": 10,
          "score_levels": [
            {"score": 1, "label": "Poor", "description": "Criteria"},
            {"score": 5, "label": "Good", "description": "Criteria"},
            {"score": 10, "label": "Excellent", "description": "Criteria"}
          ]
        }
      ]
    }
  ]
}';

        return $prompt;
    }

    private function parseResponse(string $text): array
    {
        // Remove markdown code blocks if present
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*$/i', '', $text);
        $text = trim($text);

        $data = json_decode($text, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['categories'])) {
            throw new \Exception('Invalid JSON response from OpenAI');
        }

        return $data;
    }
}
