<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/database.php';

try {
  $pdo = getDB();

  $products = $pdo->query("
    SELECT p.*, s.name as series_name, s.slug as series_slug
    FROM products p
    LEFT JOIN series s ON p.series_id = s.id
    WHERE p.status = 'active' OR p.status = 'coming_soon'
    ORDER BY p.sort_order ASC, p.title ASC
  ")->fetchAll();

  $reviews = $pdo->query("
    SELECT r.*, p.slug as product_slug
    FROM reviews r
    JOIN products p ON r.product_id = p.id
    ORDER BY r.created_at DESC
  ")->fetchAll();

  $reviewsByProduct = [];
  foreach ($reviews as $r) {
    $reviewsByProduct[$r['product_slug']][] = [
      'stars' => (int)$r['stars'],
      'text' => $r['text'],
      'author' => $r['author'],
    ];
  }

  $result = [];
  foreach ($products as $p) {
    $slug = $p['slug'];
    $result[$slug] = [
      'id' => $slug,
      'title' => $p['title'],
      'subtitle' => $p['subtitle'] ?: ($p['series_name'] ? ($p['series_name'] . ' - ' . $p['format']) : ''),
      'price' => $p['price'],
      'comparePrice' => $p['compare_price'],
      'image' => $p['image_primary'],
      'imageSecondary' => $p['image_secondary'],
      'badge' => $p['badge'] ?: '',
      'description' => $p['description'],
      'format' => $p['format'],
      'pages' => $p['pages'],
      'isbn' => $p['isbn'],
      'published' => $p['published'],
      'series' => $p['series_name'],
      'tropes' => $p['tropes'],
      'is_preorder' => (bool)$p['is_preorder'],
      'status' => $p['status'],
    ];

    if (isset($reviewsByProduct[$slug])) {
      $result[$slug]['reviews'] = $reviewsByProduct[$slug];
    }
  }

  echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Failed to load products']);
}
