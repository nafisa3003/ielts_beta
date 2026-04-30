<?php
$page_title='Page Not Found'; $topbar_links='auth';
require_once '../includes/header.php';
http_response_code(404);
?>
<div style="min-height:calc(100vh - 68px);display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:60px 24px;">
  <div style="font-family:var(--font);font-size:140px;font-weight:900;color:var(--teal);line-height:1;opacity:.15;">404</div>
  <div style="font-family:var(--font);font-size:28px;font-weight:800;margin-bottom:10px;margin-top:-20px;">Page wandered off to study somewhere else.</div>
  <div style="font-size:16px;color:var(--text2);margin-bottom:28px;">Let's get you back on track.</div>
  <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
    <a href="home.php" class="btn btn-primary">🏠 Back to home</a>
    <a href="dashboard.php" class="btn btn-outline">📚 Dashboard</a>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
