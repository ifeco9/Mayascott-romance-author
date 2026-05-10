<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
$pdo = getDB();

$products = $pdo->query("
  SELECT p.*, s.name as series_name
  FROM products p
  LEFT JOIN series s ON p.series_id = s.id
  ORDER BY p.sort_order ASC, p.title ASC
")->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<div class="page-header">
  <div>
    <h1>Products</h1>
    <p><?= count($products) ?> products total</p>
  </div>
  <a href="create.php" class="btn btn-primary">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Product
  </a>
</div>

<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] ?>">
    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <?php if ($flash['type'] === 'success'): ?>
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
      <?php else: ?>
        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
      <?php endif; ?>
    </svg>
    <?= e($flash['message']) ?>
  </div>
<?php endif; ?>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:60px;">Image</th>
          <th>Title</th>
          <th>Series</th>
          <th>Price</th>
          <th>Status</th>
          <th style="width:60px;">Feat.</th>
          <th style="width:50px;">Order</th>
          <th style="width:130px;"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td>
            <?php if ($p['image_primary']): ?>
              <img src="<?= e($p['image_primary']) ?>" alt="" class="img-preview">
            <?php endif; ?>
          </td>
          <td>
            <strong><?= e($p['title']) ?></strong>
            <?php if ($p['subtitle']): ?><br><small style="color:var(--color-text-lighter);font-size:11px;"><?= e($p['subtitle']) ?></small><?php endif; ?>
          </td>
          <td><?= e($p['series_name'] ?? '—') ?></td>
          <td>
            <strong>$<?= number_format($p['price'], 2) ?></strong>
            <?php if ($p['compare_price']): ?><br><small style="text-decoration:line-through;color:var(--color-text-lighter);">$<?= number_format($p['compare_price'], 2) ?></small><?php endif; ?>
          </td>
          <td><span class="badge badge-<?= $p['status'] ?>"><?= $p['status'] ?></span></td>
          <td class="text-center"><?= $p['is_featured'] ? '✓' : '—' ?></td>
          <td class="text-center"><?= $p['sort_order'] ?></td>
          <td class="cell-actions">
            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
            <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete «<?= e($p['title']) ?>»? This cannot be undone.')">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
        <tr><td colspan="8">
          <div class="empty-state">
            <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div>
            <h3>No products yet</h3>
            <p>Get started by adding your first product.</p>
            <a href="create.php" class="btn btn-primary">Add Product</a>
          </div>
        </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
