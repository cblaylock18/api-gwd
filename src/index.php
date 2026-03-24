<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db/db.php';
require_once __DIR__ . '/services/GameRepository.php';

try {
  $repo = new GameRepository();
  $path = $_SERVER['REQUEST_URI'];
  $date = $_GET['date'] ?? null;

  if ($date) {
    echo json_encode($repo->getByDate($date));
  } else if (str_contains($path, '/dates')) {
    echo json_encode($repo->getAllDates());
  } else {
    echo json_encode($repo->getLatest());
  }
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}