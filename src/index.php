<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db/db.php';
require_once __DIR__ . '/services/GameRepository.php';
require_once __DIR__ . '/services/AnswerCheckerService.php';

try {
  $repo = new GameRepository();
  $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $method = $_SERVER['REQUEST_METHOD'];

  if ($path === '/check-answer' && $method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $questionId = $body['question_id'] ?? null;
    $userAnswer = $body['user_answer'] ?? null;

    if (!$questionId || !isset($userAnswer)) {
      http_response_code(400);
      echo json_encode(['error' => 'question_id and user_answer are required']);
      exit;
    }

    $question = $repo->getQuestionById((int) $questionId);
    if (!$question) {
      http_response_code(404);
      echo json_encode(['error' => 'Question not found']);
      exit;
    }

    $checker = new AnswerCheckerService();
    $correct = $checker->check(
      (int) $questionId,
      $userAnswer,
      $question['answer'],
      $question['question']
    );

    echo json_encode(['correct' => $correct]);

  } else {
    $date = $_GET['date'] ?? null;
    echo json_encode($date ? $repo->getByDate($date) : $repo->getLatest());
  }

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}