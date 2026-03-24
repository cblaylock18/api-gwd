<?php
class DB {
  private static $instance = null;

  public static function getInstance(): PDO {
    if (self::$instance === null) {
      $socketPath = '/cloudsql/quizgame-491018:us-west1:quizgame';
      $dbName = getenv('DB_NAME');
      $dbUser = getenv('DB_USER');
      $dbPassword = getenv('DB_PASSWORD');
      $dbHost = getenv('DB_HOST');

      $dsn = $dbHost
        ? "mysql:host=$dbHost;dbname=$dbName"
        : "mysql:unix_socket=$socketPath;dbname=$dbName";

      self::$instance = new PDO($dsn, $dbUser, $dbPassword);
      self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return self::$instance;
  }
}