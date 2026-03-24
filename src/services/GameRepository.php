<?php
require_once __DIR__ . '/../db/db.php';

class GameRepository {
  private PDO $pdo;

  public function __construct() {
    $this->pdo = DB::getInstance();
  }

  public function getLatest(): array {
    $stmt = $this->pdo->query('
      SELECT g.id, g.date, a.category, a.answer, a.sort_order,
             q.question, q.difficulty
      FROM games g
      JOIN answers a ON a.game_id = g.id
      JOIN questions q ON q.answer_id = a.id
      ORDER BY g.date DESC, a.sort_order ASC, q.difficulty ASC
      LIMIT 15
    ');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getByDate(string $date): array {
    $stmt = $this->pdo->prepare('
      SELECT g.id, g.date, a.category, a.answer, a.sort_order,
             q.question, q.difficulty
      FROM games g
      JOIN answers a ON a.game_id = g.id
      JOIN questions q ON q.answer_id = a.id
      WHERE g.date = ?
      ORDER BY a.sort_order ASC, q.difficulty ASC
    ');
    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllDates(): array {
    $stmt = $this->pdo->query('SELECT id, date FROM games ORDER BY date DESC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}