<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../../config/database.php';
$pdo = getDB();

$series = $pdo->query("SELECT * FROM series ORDER BY sort_order, name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $slug = !empty($_POST['slug']) ? slugify($_POST['slug']) : slugify($_POST['title']);
  $title = $_POST['title'];
  $subtitle = $_POST['subtitle'] ?? '';
  $series_id = !empty($_POST['series_id']) ? (int)$_POST['series_id'] : null;
  $author = $_POST['author'] ?? 'Maya Scott';
  $description = $_POST['description'] ?? '';
  $price = (float)($_POST['price'] ?? 0);
  $compare_price = !empty($_POST['compare_price']) ? (float)$_POST['compare_price'] : null;
  $image_primary = $_POST['image_primary'] ?? '';
  $image_secondary = $_POST['image_secondary'] ?? '';
  $badge = $_POST['badge'] ?? '';
  $format = $_POST['format'] ?? '';
  $pages = $_POST['pages'] ?? '';
  $isbn = $_POST['isbn'] ?? '';
  $published = $_POST['published'] ?? '';
  $tropes = $_POST['tropes'] ?? '';
  $is_featured = isset($_POST['is_featured']) ? 1 : 0;
  $is_preorder = isset($_POST['is_preorder']) ? 1 : 0;
  $status = $_POST['status'] ?? 'active';
  $sort_order = (int)($_POST['sort_order'] ?? 0);
  $error = null;

  try {
    $stmt = $pdo->prepare("INSERT INTO products (slug, title, subtitle, series_id, author, description, price, compare_price, image_primary, image_secondary, badge, format, pages, isbn, published, tropes, is_featured, is_preorder, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$slug, $title, $subtitle, $series_id, $author, $description, $price, $compare_price, $image_primary, $image_secondary, $badge, $format, $pages, $isbn, $published, $tropes, $is_featured, $is_preorder, $status, $sort_order]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Product created successfully.'];
    redirect('index.php');
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<div class="page-header">
  <div>
    <h1>Add Product</h1>
    <p>Create a new book or product</p>
  </div>
  <a href="index.php" class="btn btn-secondary">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Back
  </a>
</div>

<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
  <div class="alert alert-danger">
    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    <?= e($error) ?>
  </div>
<?php endif; ?>

<div class="card">
  <form method="POST">
    <div class="card-header"><h2>Basic Information</h2></div>
    <div class="form-row">
      <div class="form-group">
        <label for="title">Title <span style="color:var(--color-danger)">*</span></label>
        <input type="text" id="title" name="title" value="<?= e(old('title')) ?>" required>
      </div>
      <div class="form-group">
        <label for="slug">Slug</label>
        <input type="text" id="slug" name="slug" value="<?= e(old('slug')) ?>" placeholder="Leave blank to auto-generate">
        <span class="hint">URL-friendly identifier (e.g., "my-book-title")</span>
      </div>
    </div>
    <div class="form-group">
      <label for="subtitle">Subtitle</label>
      <input type="text" id="subtitle" name="subtitle" value="<?= e(old('subtitle')) ?>" placeholder="e.g., Book One of The Enchanted Chronicles">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="series_id">Series</label>
        <select id="series_id" name="series_id">
          <option value="">— No Series —</option>
          <?php foreach ($series as $s): ?>
            <option value="<?= $s['id'] ?>" <?= old('series_id') == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="author">Author</label>
        <input type="text" id="author" name="author" value="<?= e(old('author', 'Maya Scott')) ?>">
      </div>
    </div>
    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description" placeholder="Full product description..."><?= e(old('description')) ?></textarea>
    </div>

    <div class="card-header" style="margin-top:24px;"><h2>Pricing</h2></div>
    <div class="form-row">
      <div class="form-group">
        <label for="price">Price <span style="color:var(--color-danger)">*</span></label>
        <input type="number" step="0.01" id="price" name="price" value="<?= e(old('price', '0')) ?>" required>
      </div>
      <div class="form-group">
        <label for="compare_price">Compare-at Price</label>
        <input type="number" step="0.01" id="compare_price" name="compare_price" value="<?= e(old('compare_price')) ?>" placeholder="Original price before discount">
        <span class="hint">Shows a strikethrough comparison price</span>
      </div>
    </div>

    <div class="card-header" style="margin-top:24px;"><h2>Media</h2></div>
    <div class="form-row">
      <div class="form-group">
        <label for="image_primary">Primary Image URL</label>
        <input type="text" id="image_primary" name="image_primary" value="<?= e(old('image_primary')) ?>" placeholder="https://...">
      </div>
      <div class="form-group">
        <label for="image_secondary">Secondary Image URL (hover)</label>
        <input type="text" id="image_secondary" name="image_secondary" value="<?= e(old('image_secondary')) ?>" placeholder="https://...">
      </div>
    </div>

    <div class="card-header" style="margin-top:24px;"><h2>Details</h2></div>
    <div class="form-row-3">
      <div class="form-group">
        <label for="badge">Badge</label>
        <input type="text" id="badge" name="badge" value="<?= e(old('badge')) ?>" placeholder="e.g., Bestseller, Signed">
      </div>
      <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          <option value="coming_soon" <?= old('status') === 'coming_soon' ? 'selected' : '' ?>>Coming Soon</option>
        </select>
      </div>
      <div class="form-group">
        <label for="sort_order">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="<?= e(old('sort_order', '0')) ?>">
      </div>
    </div>
    <div class="form-row-3">
      <div class="form-group">
        <label for="format">Format</label>
        <input type="text" id="format" name="format" value="<?= e(old('format')) ?>" placeholder="e.g., Paperback, Ebook">
      </div>
      <div class="form-group">
        <label for="pages">Pages</label>
        <input type="text" id="pages" name="pages" value="<?= e(old('pages')) ?>" placeholder="e.g., 342">
      </div>
      <div class="form-group">
        <label for="isbn">ISBN</label>
        <input type="text" id="isbn" name="isbn" value="<?= e(old('isbn')) ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="published">Published</label>
        <input type="text" id="published" name="published" value="<?= e(old('published')) ?>" placeholder="e.g., January 2026">
      </div>
      <div class="form-group">
        <label for="tropes">Tropes</label>
        <input type="text" id="tropes" name="tropes" value="<?= e(old('tropes')) ?>" placeholder="e.g., Fake Relationship, Slow Burn">
        <span class="hint">Comma-separated list</span>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group" style="display:flex;gap:24px;padding-top:8px;">
        <label class="form-checkbox">
          <input type="checkbox" name="is_featured" value="1" <?= old('is_featured') ? 'checked' : '' ?>>
          Featured product
        </label>
        <label class="form-checkbox">
          <input type="checkbox" name="is_preorder" value="1" <?= old('is_preorder') ? 'checked' : '' ?>>
          Preorder item
        </label>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary btn-lg">Create Product</button>
      <a href="index.php" class="btn btn-secondary btn-lg">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
