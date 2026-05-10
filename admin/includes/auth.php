<?php
session_start();

function isLoggedIn(): bool {
  return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin(): void {
  if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
  }
}

function login(string $username, string $password): bool {
  require_once __DIR__ . '/../../config/database.php';
  try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['admin_logged_in'] = true;
      $_SESSION['admin_username'] = $user['username'];
      $_SESSION['admin_user_id'] = $user['id'];
      return true;
    }
  } catch (Exception $e) {}
  return false;
}

function logout(): void {
  session_destroy();
  header('Location: index.php');
  exit;
}
