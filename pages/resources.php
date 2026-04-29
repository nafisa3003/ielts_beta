<?php
require_once '../includes/db_connection.php';
$user = auth_required();
$content = new IELTSContent();
$resources = $content->getResources();

$page_title = 'Cambridge Resources';
$active_page = 'cambridge';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="app-layout">
<main class="app-main">
  <div class="pg-header">
    <div>
      <div class="pg-title">📖 Cambridge Resources</div>
      <div class="pg-sub">Authentic materials for your IELTS preparation.</div>
    </div>
    <div class="tabs" id="res-tabs" style="margin:0;">
      <div class="tab active" onclick="filterResources('all', this)">All</div>
      <div class="tab" onclick="filterResources('free', this)">Free</div>
      <div class="tab" onclick="filterResources('premium', this)">Premium 🔒</div>
    </div>
  </div>

  <style>
    /* Pinterest-style Masonry Grid using standard CSS variables */
    .res-masonry {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      grid-gap: 24px;
      margin-top: 24px;
    }
    .res-card-v2 {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 24px;
      display: flex;
      flex-direction: column;
      transition: all 0.2s ease;
      position: relative;
    }
    .res-card-v2:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
      border-color: var(--teal);
    }
    .res-badge {
      font-size: 10px;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 6px;
      display: inline-block;
      margin-bottom: 12px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .badge-pdf   { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .badge-audio { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .badge-book  { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    
    .res-v2-title {
      font-weight: 700;
      font-size: 18px;
      color: var(--text);
      line-height: 1.4;
      margin-bottom: 8px;
    }
    .res-v2-desc {
      font-size: 14px;
      color: var(--text2);
      line-height: 1.6;
      margin-bottom: 20px;
    }
    .res-v2-footer {
      margin-top: auto;
      padding-top: 16px;
      border-top: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .lock-icon { color: #f59e0b; }
  </style>

  <div class="res-masonry" id="resource-grid">
    <?php foreach ($resources as $r): 
      $isPremium = $r['is_premium'];
      $type = strtolower($r['type']);
    ?>
    <div class="res-card-v2" data-premium="<?= $isPremium ? 'true' : 'false' ?>">
      <div style="display:flex; justify-content:space-between; align-items:start;">
        <span class="res-badge badge-<?= $type ?>"><?= $type ?></span>
        <?php if($isPremium): ?>
          <span class="lock-icon">🔒</span>
        <?php endif; ?>
      </div>
      
      <div class="res-v2-title"><?= htmlspecialchars($r['title']) ?></div>
      <div class="res-v2-desc"><?= htmlspecialchars($r['description']) ?></div>
      
      <div class="res-v2-footer">
        <?php if($isPremium): ?>
          <button class="btn btn-ghost btn-sm" style="width:100%; justify-content:center; color:var(--text3);" onclick="Toast.show('Upgrade to Premium to access!')">Locked Content</button>
        <?php else: ?>
          <a href="<?= htmlspecialchars($r['file_url'] ?? '#') ?>" target="_blank" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">Download Now</a>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</main>
</div>

<script>
function filterResources(type, el) {
  document.querySelectorAll('#res-tabs .tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');

  const cards = document.querySelectorAll('.res-card-v2');
  cards.forEach(card => {
    const isPremium = card.getAttribute('data-premium') === 'true';
    if (type === 'all') card.style.display = 'flex';
    else if (type === 'free') card.style.display = !isPremium ? 'flex' : 'none';
    else if (type === 'premium') card.style.display = isPremium ? 'flex' : 'none';
  });
}
</script>

<?php require_once '../includes/footer.php'; ?>