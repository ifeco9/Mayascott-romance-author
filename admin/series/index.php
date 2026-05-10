<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  $name = $_POST['name'] ?? '';
  $slug = !empty($_POST['slug']) ? slugify($_POST['slug']) : slugify($name);
  $description = $_POST['description'] ?? '';
  $sort_order = (int)($_POST['sort_order'] ?? 0);
  if ($name) {
    try {
      $stmt = $pdo->prepare("INSERT INTO series (name, slug, description, sort_order) VALUES (?, ?, ?, ?)");
      $stmt->execute([$name, $slug, $description, $sort_order]);
      $_SESSION['flash'] = ['type' => 'success', 'message' => 'Series created.'];
    } catch (Exception $e) {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getMessage()];
    }
  }
  redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  $id = (int)($_POST['id'] ?? 0);
  $name = $_POST['name'] ?? '';
  $slug = !empty($_POST['slug']) ? slugify($_POST['slug']) : slugify($name);
  $description = $_POST['description'] ?? '';
  $sort_order = (int)($_POST['sort_order'] ?? 0);
  if ($name && $id) {
    try {
      $stmt = $pdo->prepare("UPDATE series SET name=?, slug=?, description=?, sort_order=? WHERE id=?");
      $stmt->execute([$name, $slug, $description, $sort_order, $id]);
      $_SESSION['flash'] = ['type' => 'success', 'message' => 'Series updated.'];
    } catch (Exception $e) {
      $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getMessage()];
    }
  }
  redirect('index.php');
}

if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  try {
    $stmt = $pdo->prepare("DELETE FROM series WHERE id=?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Series deleted.'];
  } catch (Exception $e) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getMessage()];
  }
  redirect('index.php');
}

$seriesList = $pdo->query("SELECT s.*, (SELECT COUNT(*) FROM products WHERE series_id=s.id) as product_count FROM series s ORDER BY s.sort_order, s.name")->fetchAll();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<div class="page-header">
  <div>
    <h1>Series</h1>
    <p>Organize products into collections</p>
  </div>
</div>

<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h2>Add Series</h2>
  </div>
  <form method="POST" style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:16px;align-items:end;">
    <input type="hidden" name="create" value="1">
    <div class="form-group" style="margin:0;">
      <label>Name</label>
      <input type="text" name="name" placeholder="e.g., The Enchanted Chronicles" required>
    </div>
    <div class="form-group" style="margin:0;">
      <label>Slug</label>
      <input type="text" name="slug" placeholder="Auto-generated if empty">
    </div>
    <div class="form-group" style="margin:0;">
      <label>Sort Order</label>
      <input type="number" name="sort_order" value="0">
    </div>
    <button type="submit" class="btn btn-primary" style="margin-bottom:20px;">Add Series</button>
  </form>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Slug</th>
          <th style="width:80px;">Products</th>
          <th style="width:60px;">Order</th>
          <th style="width:140px;"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($seriesList as $s): ?>
        <tr>
          <form method="POST" style="display:contents">
            <input type="hidden" name="update" value="1">
            <input type="hidden" name="id" value="<?= $s['id'] ?>">
            <td><input type="text" name="name" value="<?= e($s['name']) ?>" style="width:100%;padding:6px 10px;border:1px solid var(--color-border);border-radius:4px;font-size:13px;"></td>
            <td><input type="text" name="slug" value="<?= e($s['slug']) ?>" style="width:100%;padding:6px 10px;border:1px solid var(--color-border);border-radius:4px;font-size:13px;"></td>
            <td class="text-center"><?= $s['product_count'] ?></td>
            <td><input type="number" name="sort_order" value="<?= $s['sort_order'] ?>" style="width:50px;padding:6px 8px;border:1px solid var(--color-border);border-radius:4px;font-size:13px;text-align:center;"></td>
            <td class="cell-actions">
              <button type="submit" class="btn btn-sm btn-primary">Save</button>
              <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete «<?= e($s['name']) ?>»? Products in this series will be unlinked.')">Delete</a>
            </td>
          </form>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($seriesList)): ?>
        <tr><td colspan="5"><div class="empty-state"><h3>No series yet</h3><p>Create your first series above.</p></div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
