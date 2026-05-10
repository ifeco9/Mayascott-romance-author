<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  $product_id = (int)($_POST['product_id'] ?? 0);
  $stars = (int)($_POST['stars'] ?? 5);
  $text = $_POST['text'] ?? '';
  $author = $_POST['author'] ?? '';
  if ($product_id && $text) {
    try {
      $stmt = $pdo->prepare("INSERT INTO reviews (product_id, stars, text, author) VALUES (?, ?, ?, ?)");
      $stmt->execute([$product_id, $stars, $text, $author]);
      $_SESSION['flash'] = ['type' => 'success', 'message' => 'Review created.'];
    } catch (Exception $e) {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getMessage()];
    }
  }
  redirect('index.php');
}

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  try {
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id=?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Review deleted.'];
  } catch (Exception $e) {}
  redirect('index.php');
}

$reviews = $pdo->query("
  SELECT r.*, p.title as product_title, p.slug as product_slug
  FROM reviews r
  JOIN products p ON r.product_id = p.id
  ORDER BY r.created_at DESC
")->fetchAll();

$products = $pdo->query("SELECT id, title FROM products WHERE status='active' ORDER BY title")->fetchAll();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<div class="page-header">
  <div>
    <h1>Reviews</h1>
    <p><?= count($reviews) ?> reviews across all products</p>
  </div>
</div>

<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h2>Add Review</h2>
  </div>
  <form method="POST">
    <input type="hidden" name="create" value="1">
    <div class="form-row">
      <div class="form-group">
        <label>Product</label>
        <select name="product_id" required>
          <option value="">— Select product —</option>
          <?php foreach ($products as $p): ?>
            <option value="<?= $p['id'] ?>"><?= e($p['title']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Stars</label>
        <select name="stars">
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?= $i ?>" <?= $i === 5 ? 'selected' : '' ?>><?= $i ?> star<?= $i !== 1 ? 's' : '' ?></option>
          <?php endfor; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label>Reviewer Name</label>
      <input type="text" name="author" placeholder="e.g., Sarah M.">
    </div>
    <div class="form-group">
      <label>Review Text</label>
      <textarea name="text" placeholder="Write the review content..." required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Add Review</button>
  </form>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Reviewer</th>
          <th>Rating</th>
          <th>Review</th>
          <th>Date</th>
          <th style="width:80px;"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reviews as $r): ?>
        <tr>
          <td><strong><?= e($r['product_title']) ?></strong></td>
          <td><?= e($r['author']) ?></td>
          <td style="color:var(--color-gold);font-size:14px;"><?= str_repeat('★', $r['stars']) . str_repeat('☆', 5 - $r['stars']) ?></td>
          <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--color-text-light);font-style:italic;">"<?= e($r['text']) ?>"</td>
          <td style="font-size:12px;color:var(--color-text-lighter);"><?= formatDate($r['created_at']) ?></td>
          <td class="cell-actions">
            <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this review by «<?= e($r['author']) ?>»?')">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($reviews)): ?>
        <tr><td colspan="6"><div class="empty-state"><h3>No reviews yet</h3><p>Add your first review above.</p></div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
