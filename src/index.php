<?php
header('Content-Type: application/json');

$socketPath = '/cloudsql/quizgame-491018:us-west1:quizgame';
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');
$dbHost = getenv('DB_HOST');

$dsn = $dbHost
  ? "mysql:host=$dbHost;dbname=$dbName"
  : "mysql:unix_socket=$socketPath;dbname=$dbName";

try {
  $pdo = new PDO($dsn, $dbUser, $dbPassword);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $games = $pdo->query('SELECT * FROM games ORDER BY date DESC LIMIT 1')->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($games);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}