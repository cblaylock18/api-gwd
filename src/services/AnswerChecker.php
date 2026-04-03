<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../db/db.php';

use Gemini\Data\Content;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;

class AnswerCheckerService {
  private PDO $pdo;
  private $geminiClient;

  public function __construct() {
    $this->pdo = DB::getInstance();
    $apiKey = $_ENV['GEMINI_API_KEY'];
    $this->geminiClient = Gemini::client($apiKey);
  }

  public function check(int $questionId, string $userAnswer, string $correctAnswer, string $question): bool {
    $normalized = $this->normalize($userAnswer);
    $normalizedCorrect = $this->normalize($correctAnswer);

    // 1. Exact match
    if ($normalized === $normalizedCorrect) {
      return true;
    }

    // 2. Check accepted answers cache
    $stmt = $this->pdo->prepare('
      SELECT 1 FROM accepted_answers
      WHERE question_id = ? AND normalized_answer = ?
    ');
    $stmt->execute([$questionId, $normalized]);
    if ($stmt->fetchColumn()) {
      return true;
    }

    // 3. Check rejected answers cache
    $stmt = $this->pdo->prepare('
      SELECT 1 FROM rejected_answers
      WHERE question_id = ? AND normalized_answer = ?
    ');
    $stmt->execute([$questionId, $normalized]);
    if ($stmt->fetchColumn()) {
      return false;
    }

    // 4. Ask Gemini
    $correct = $this->askGemini($question, $correctAnswer, $userAnswer);

    // 5. Persist result
    $table = $correct ? 'accepted_answers' : 'rejected_answers';
    $stmt = $this->pdo->prepare("
      INSERT IGNORE INTO {$table} (question_id, normalized_answer)
      VALUES (?, ?)
    ");
    $stmt->execute([$questionId, $normalized]);

    return $correct;
  }

  private function normalize(string $s): string {
    $s = mb_strtolower($s);
    $s = preg_replace('/[^\p{L}\p{N}\s]/u', '', $s); // strip punctuation, keep letters/numbers/spaces
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
  }

  private function askGemini(string $question, string $correctAnswer, string $userAnswer): bool {
    $response = $this->geminiClient
      ->generativeModel(model: 'gemini-2.5-flash')
      ->withSystemInstruction(
        Content::parse('You are a JSON API. Always respond with valid JSON objects in the format {"correct": true} or {"correct": false}. You are given a question, correct answer, and a user answer, and you must determine if the user answer is correct. Be forgiving of minor spelling errors and typos — if the intended answer is clear, mark it correct. Only respond with the JSON object, do not include any additional text.')
      )
      ->withGenerationConfig(
        new GenerationConfig(responseMimeType: ResponseMimeType::APPLICATION_JSON)
      )
      ->generateContent(
        "Question: {$question} Correct Answer: {$correctAnswer}. User Answer: {$userAnswer}."
      );

    $result = $response->json();
    return (bool) ($result['correct'] ?? false);
  }
}