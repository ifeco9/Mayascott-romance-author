<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../config/database.php';
$pdo = getDB();

$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$seriesCount = $pdo->query("SELECT COUNT(*) FROM series")->fetchColumn();
$reviewCount = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$activeCount = $pdo->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn();
$featuredCount = $pdo->query("SELECT COUNT(*) FROM products WHERE is_featured=1")->fetchColumn();
$recentProducts = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentReviews = $pdo->query("SELECT r.*, p.title as product_title FROM reviews r JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC LIMIT 5")->fetchAll();
?>
<div class="page-header">
  <div>
    <h1>Dashboard</h1>
    <p>Overview of your store</p>
  </div>
  <a href="products/create.php" class="btn btn-primary">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Product
  </a>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon products">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
    </div>
    <div class="stat-body">
      <span class="stat-number"><?= $productCount ?></span>
      <span class="stat-label">Total Products</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
    <div class="stat-body">
      <span class="stat-number"><?= $activeCount ?></span>
      <span class="stat-label">Active</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon featured">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
    </div>
    <div class="stat-body">
      <span class="stat-number"><?= $featuredCount ?></span>
      <span class="stat-label">Featured</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon series">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
    </div>
    <div class="stat-body">
      <span class="stat-number"><?= $seriesCount ?></span>
      <span class="stat-label">Series</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon reviews">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
    </div>
    <div class="stat-body">
      <span class="stat-number"><?= $reviewCount ?></span>
      <span class="stat-label">Reviews</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2>Recent Products</h2>
    <a href="products/index.php" class="btn btn-sm btn-secondary">View All</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Image</th>
          <th>Title</th>
          <th>Price</th>
          <th>Status</th>
          <th>Created</th>
          <th class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentProducts)): ?>
          <tr><td colspan="6"><div class="empty-state"><p>No products yet.</p></div></td></tr>
        <?php else: ?>
          <?php foreach ($recentProducts as $p): ?>
          <tr>
            <td>
              <?php if ($p['image_primary']): ?>
                <img src="<?= e($p['image_primary']) ?>" alt="" class="img-preview">
              <?php endif; ?>
            </td>
            <td><a href="products/edit.php?id=<?= $p['id'] ?>"><strong><?= e($p['title']) ?></strong></a></td>
            <td>$<?= number_format($p['price'], 2) ?></td>
            <td><span class="badge badge-<?= $p['status'] ?>"><?= $p['status'] ?></span></td>
            <td><?= formatDate($p['created_at']) ?></td>
            <td class="text-right">
              <a href="products/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2>Recent Reviews</h2>
    <a href="reviews/index.php" class="btn btn-sm btn-secondary">View All</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Reviewer</th>
          <th>Rating</th>
          <th>Preview</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentReviews)): ?>
          <tr><td colspan="5"><div class="empty-state"><p>No reviews yet.</p></div></td></tr>
        <?php else: ?>
          <?php foreach ($recentReviews as $r): ?>
          <tr>
            <td><?= e($r['product_title']) ?></td>
            <td><?= e($r['author']) ?></td>
            <td style="color:var(--color-gold);"><?= str_repeat('★', $r['stars']) . str_repeat('☆', 5 - $r['stars']) ?></td>
            <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--color-text-light);">"<?= e($r['text']) ?>"</td>
            <td><?= formatDate($r['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
