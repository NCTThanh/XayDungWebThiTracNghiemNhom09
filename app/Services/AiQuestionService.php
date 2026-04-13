<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiQuestionService {
    
    private $apiKey;
    private $provider;

    public function __construct() {
        $this->provider = config('quiz.ai.provider', 'gemini');
        $this->apiKey = config('quiz.ai.gemini_api_key');
    }

    // ===================================
    // GENERATE QUESTIONS
    // ===================================

    public function generateMultipleChoice($topic, $quantity = 5, $difficulty = 'medium') {
        if ($this->provider === 'gemini') {
            return $this->generateWithGemini($topic, $quantity, $difficulty, 'multiple_choice');
        }
        
        return [];
    }

    public function generateTrueFalse($topic, $quantity = 5, $difficulty = 'medium') {
        if ($this->provider === 'gemini') {
            return $this->generateWithGemini($topic, $quantity, $difficulty, 'true_false');
        }
        
        return [];
    }

    public function generateEssay($topic, $quantity = 3, $difficulty = 'hard') {
        if ($this->provider === 'gemini') {
            return $this->generateWithGemini($topic, $quantity, $difficulty, 'essay');
        }
        
        return [];
    }

    // ===================================
    // GEMINI API INTEGRATION
    // ===================================

    private function generateWithGemini($topic, $quantity, $difficulty, $type) {
        if (!$this->apiKey) {
            Log::error('Gemini API key not configured');
            return [];
        }

        $prompt = $this->buildPrompt($topic, $quantity, $difficulty, $type);

        try {
            $response = Http::timeout(60)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $this->apiKey,
                [
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
                    ]
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['contents'][0]['parts'][0]['text'] ?? '';
                return $this->parseGeminiResponse($content, $type);
            } else {
                Log::error('Gemini API error: ' . $response->body());
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Gemini API exception: ' . $e->getMessage());
            return [];
        }
    }

    private function buildPrompt($topic, $quantity, $difficulty, $type) {
        $difficultyDesc = match($difficulty) {
            'easy' => 'easy (suitable for beginners)',
            'medium' => 'medium (intermediate level)',
            'hard' => 'hard (advanced level)',
            default => 'medium'
        };

        $typeDesc = match($type) {
            'multiple_choice' => 'multiple choice questions with 4 options (A, B, C, D)',
            'true_false' => 'true/false questions',
            'essay' => 'essay questions',
            default => 'multiple choice questions'
        };

        return <<<PROMPT
Generate exactly $quantity $difficulty $typeDesc about the topic: "$topic"

For each question, provide in JSON format:
{
    "questions": [
        {
            "question": "the question text",
            "type": "$type",
            "difficulty": "$difficulty",
            "options": ["option1", "option2", "option3", "option4"],
            "correct_answer": "option text or option index",
            "explanation": "why this is correct"
        }
    ]
}

Requirements:
- Each question must be clear and educational
- For multiple choice, always provide exactly 4 options
- For true/false, use true/false options
- Correct answer should be the exact option text
- Include detailed explanation for each answer
- Make questions practical and relevant
- Ensure proper grammar and spelling

Return ONLY valid JSON, no other text.
PROMPT;
    }

    private function parseGeminiResponse($content, $type) {
        try {
            // Extract JSON from response
            preg_match('/\{[\s\S]*\}/', $content, $matches);
            if (empty($matches)) {
                return [];
            }

            $json = json_decode($matches[0], true);
            $questions = $json['questions'] ?? [];

            // Normalize questions
            return array_map(function ($q) {
                return [
                    'question' => $q['question'] ?? '',
                    'type' => $q['type'] ?? 'multiple_choice',
                    'difficulty' => $q['difficulty'] ?? 'medium',
                    'options' => $q['options'] ?? [],
                    'correct_answer' => $q['correct_answer'] ?? (isset($q['options'][0]) ? $q['options'][0] : ''),
                    'explanation' => $q['explanation'] ?? '',
                ];
            }, $questions);
        } catch (\Exception $e) {
            Log::error('Error parsing Gemini response: ' . $e->getMessage());
            return [];
        }
    }

    // ===================================
    // QUESTION IMPROVEMENT
    // ===================================

    public function improveQuestion($questionText, $options, $correctAnswer) {
        if (!$this->apiKey) {
            return null;
        }

        $prompt = "Improve this quiz question for clarity and educational value:\n\nQuestion: $questionText\nOptions:\n";
        foreach ($options as $i => $opt) {
            $prompt .= chr(65 + $i) . ") $opt\n";
        }
        $prompt .= "\nCorrect answer: $correctAnswer\n\nProvide improved version as JSON with 'improved_question', 'improved_options', 'explanation'.";

        try {
            $response = Http::timeout(30)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $this->apiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['contents'][0]['parts'][0]['text'] ?? '';
                
                preg_match('/\{[\s\S]*\}/', $content, $matches);
                if (!empty($matches)) {
                    return json_decode($matches[0], true);
                }
            }
        } catch (\Exception $e) {
            Log::error('Question improvement error: ' . $e->getMessage());
        }

        return null;
    }

    // ===================================
    // ANSWER EVALUATION
    // ===================================

    public function evaluateEssayAnswer($question, $studentAnswer) {
        if (!$this->apiKey) {
            return null;
        }

        $prompt = <<<PROMPT
Evaluate this essay answer to the question:

Question: $question

Student's Answer: $studentAnswer

Provide evaluation in JSON format:
{
    "score": (0-100),
    "feedback": "constructive feedback",
    "strengths": ["point1", "point2"],
    "improvements": ["area1", "area2"],
    "overall_comment": "summary comment"
}

Be fair and encouraging while providing constructive criticism.
PROMPT;

        try {
            $response = Http::timeout(30)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $this->apiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['contents'][0]['parts'][0]['text'] ?? '';
                
                preg_match('/\{[\s\S]*\}/', $content, $matches);
                if (!empty($matches)) {
                    return json_decode($matches[0], true);
                }
            }
        } catch (\Exception $e) {
            Log::error('Essay evaluation error: ' . $e->getMessage());
        }

        return null;
    }
}
