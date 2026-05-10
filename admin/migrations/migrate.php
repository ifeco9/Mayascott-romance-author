<?php
require_once __DIR__ . '/../../config/database.php';

echo "Running migrations...\n";

try {
  $pdo = getDB();

  $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  $stmt = $pdo->query("SELECT migration FROM migrations");
  $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

  $migrationFiles = glob(__DIR__ . '/*.php');
  sort($migrationFiles);

  $count = 0;
  foreach ($migrationFiles as $file) {
    $basename = basename($file);
    if ($basename === 'migrate.php') continue;
    if (in_array($basename, $executed)) continue;

    echo "  Running: $basename... ";
    try {
      $sql = require $file;
      if (is_string($sql) && trim($sql)) {
        $pdo->exec($sql);
      }
      $stmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
      $stmt->execute([$basename]);
      echo "OK\n";
      $count++;
    } catch (Exception $e) {
      echo "FAILED: " . $e->getMessage() . "\n";
    }
  }

  if ($count === 0) {
    echo "  All migrations already executed.\n";
  } else {
    echo "  $count migration(s) executed successfully.\n";
  }

} catch (Exception $e) {
  echo "Error: " . $e->getMessage() . "\n";
  exit(1);
}
