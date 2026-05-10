<?php require_once __DIR__ . '/auth.php'; require_once __DIR__ . '/functions.php'; requireLogin();

$depth = substr_count($_SERVER['SCRIPT_NAME'], '/') - 3;
$base = str_repeat('../', $depth);
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Maya Scott</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $base ?>assets/css/admin.css">
</head>
<body>
  <aside class="admin-sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo-icon">M</div>
      <div class="sidebar-logo-text">
        <span class="logo-line1">Maya Scott</span>
        <span class="logo-line2">Admin Panel</span>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="sidebar-nav-label">Main</div>
      <a href="<?= $base ?>dashboard.php" class="<?= basename($_SERVER['SCRIPT_NAME']) === 'dashboard.php' ? 'active' : '' ?>">
        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg></span>
        <span>Dashboard</span>
      </a>
      <a href="<?= $base ?>products/index.php" class="<?= str_contains($_SERVER['SCRIPT_NAME'], '/products/') ? 'active' : '' ?>">
        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
        <span>Products</span>
      </a>
      <a href="<?= $base ?>series/index.php" class="<?= str_contains($_SERVER['SCRIPT_NAME'], '/series/') ? 'active' : '' ?>">
        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
        <span>Series</span>
      </a>
      <a href="<?= $base ?>reviews/index.php" class="<?= str_contains($_SERVER['SCRIPT_NAME'], '/reviews/') ? 'active' : '' ?>">
        <span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
        <span>Reviews</span>
      </a>
    </nav>
    <div class="sidebar-footer">
      <div class="admin-user">
        <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)) ?></div>
        <span><?= e($_SESSION['admin_username'] ?? '') ?></span>
      </div>
      <a href="<?= $base ?>logout.php" class="logout-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <span>Sign Out</span>
      </a>
    </div>
  </aside>

  <div class="admin-main">
    <header class="top-bar">
      <div class="top-bar-left">
        <span class="top-bar-title">
          <?php
          $page = basename($_SERVER['SCRIPT_NAME'], '.php');
          $titles = ['dashboard' => 'Dashboard', 'index' => 'Overview', 'create' => 'Create', 'edit' => 'Edit'];
          echo $titles[$page] ?? ucfirst($page);
          ?>
        </span>
      </div>
      <div class="top-bar-right"></div>
    </header>
    <div class="page-content">
