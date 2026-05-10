<?php
function slugify(string $text): string {
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  $text = preg_replace('~[^-\w]+~', '', $text);
  $text = trim($text, '-');
  $text = preg_replace('~-+~', '-', $text);
  $text = strtolower($text);
  return $text ?: 'n-a';
}

function redirect(string $path): void {
  header("Location: $path");
  exit;
}

function old(string $key, string $default = '') {
  return $_POST[$key] ?? $default;
}

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatDate(string $date): string {
  return date('M j, Y g:i A', strtotime($date));
}
