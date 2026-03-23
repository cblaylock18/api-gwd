<?php
header('Content-Type: application/json');

$pdo = new PDO(
  "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
  $_ENV['DB_USER'],
  $_ENV['DB_PASSWORD']
);

$games = $pdo->query('SELECT * FROM games')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($games);