<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../../config/database.php';
$pdo = getDB();

$id = (int)($_GET['id'] ?? 0);

try {
  $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
  $stmt->execute([$id]);
  $_SESSION['flash'] = ['type' => 'success', 'message' => 'Product deleted.'];
} catch (Exception $e) {
  $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Error deleting product.'];
}

redirect('../products/index.php');
