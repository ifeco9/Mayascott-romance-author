<?php
header('Content-Type: application/json');
echo json_encode([
  'endpoints' => [
    'products' => '/api/products.php',
  ]
]);
