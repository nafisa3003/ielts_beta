<?php
// includes/header.php
require_once __DIR__ . '/config.php';

$page_title   = $page_title   ?? 'IELTS Beta';
$body_class   = $body_class   ?? '';
$extra_css    = $extra_css    ?? [];
$show_topbar  = $show_topbar  ?? true;

// Auto-detect navigation state
$is_logged_in = logged_in();
$user = $is_logged_in ? queryOne('SELECT * FROM users WHERE id = ?', [$_SESSION['user_id']]) : null;
$topbar_links = $is_logged_in ? 'dashboard' : ($topbar_links ?? 'landing');

// Get initials for profile icon
$initials = '';
if ($user) {
    $parts = explode(' ', trim($user['name']));
    $initials = strtoupper(substr($parts[0], 0, 1)) . (isset($parts[1]) ? strtoupper(substr($parts[1], 0, 1)) : '');
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?> — IELTS Beta</title>
  
  <link rel="icon" type="image/png" href="/ielts_beta_v3/assets/img/logo-transparent.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
  
  <!-- Firebase (Global) -->
  <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js"></script>
  <script src="/ielts_beta_v3/assets/js/firebase-init.js"></script>
  <script src="/ielts_beta_v3/assets/js/globals.js?v=1.1"></script>

  <link rel="stylesheet" href="/ielts_beta_v3/assets/css/globals.css?v=1.4">
  <?php foreach ($extra_css as $css): ?>
  <link rel="stylesheet" href="/ielts_beta_v3/assets/css/<?= htmlspecialchars($css) ?>?v=1.4">
  <?php endforeach; ?>

  <script>
  // Firebase Config from PHP (env.php)
  window.firebaseConfig = {
    apiKey: "<?= defined('FIREBASE_API_KEY') ? FIREBASE_API_KEY : '' ?>",
    authDomain: "<?= defined('FIREBASE_AUTH_DOMAIN') ? FIREBASE_AUTH_DOMAIN : '' ?>",
    projectId: "<?= defined('FIREBASE_PROJECT_ID') ? FIREBASE_PROJECT_ID : '' ?>",
    appId: "<?= defined('FIREBASE_APP_ID') ? FIREBASE_APP_ID : '' ?>",
    // Optional fields if you have them in env.php
    storageBucket: "<?= defined('FIREBASE_STORAGE_BUCKET') ? FIREBASE_STORAGE_BUCKET : '' ?>",
    messagingSenderId: "<?= defined('FIREBASE_MESSAGING_SENDER_ID') ? FIREBASE_MESSAGING_SENDER_ID : '' ?>",
    measurementId: "<?= defined('FIREBASE_MEASUREMENT_ID') ? FIREBASE_MEASUREMENT_ID : '' ?>"
  };
</script>
</head>
<body class="<?= htmlspecialchars($body_class) ?>">
<div id="toast-container"></div>

<?php if ($show_topbar): ?>
<nav class="topbar">
  <button class="menu-toggle" id="menu-toggle" style="display:none; background:none; border:none; color:#fff; font-size:24px; cursor:pointer;
         margin-right:15px;">☰</button>
  <a href="/ielts_beta_v3/pages/home.php">
    <img src="/ielts_beta_v3/assets/img/logo-dark.png" class="logo-img" alt="IELTS Beta"
         onerror="this.outerHTML='<span style=&quot;font-family:var(--font);font-weight:800;font-size:18px;color:#5ddcc9;&quot;>IELTS Beta</span>'">
  </a>
  
  <!-- Center: Main Navigation -->
  <div class="top-nav">
    <a class="tnav" href="/ielts_beta_v3/pages/home.php">Home</a>
    <?php if ($topbar_links === 'landing'): ?>
      <a class="tnav" href="/ielts_beta_v3/pages/home.php#features">Features</a>
      <a class="tnav" href="/ielts_beta_v3/pages/cambridge.php">Resources</a>
    <?php elseif ($topbar_links === 'dashboard'): ?>
      <a class="tnav" href="/ielts_beta_v3/pages/dashboard.php">Dashboard</a>
      <a class="tnav" href="/ielts_beta_v3/pages/skills.php">Skills</a>
      <a class="tnav" href="/ielts_beta_v3/pages/flashcards.php">Flashcards</a>
      <a class="tnav" href="/ielts_beta_v3/pages/chatbot.php">AI Tutor</a>
      <a class="tnav" href="/ielts_beta_v3/pages/cambridge.php">Cambridge</a>
    <?php endif; ?>
  </div>

  <!-- Right Side: Auth & Settings -->
  <div class="topbar-right">
    <button class="dark-btn" id="dm-toggle" style="margin-right: 8px;">🌙</button>

    <?php if ($is_logged_in): ?>
      <a class="tnav outline" href="/ielts_beta_v3/pages/profile.php" style="margin-right: 8px;">Profile</a>
      <a class="tnav" href="#" onclick="logout(); return false;" style="color: #ff6b6b;">Sign out</a>
    <?php else: ?>
      <a class="tnav outline" href="/ielts_beta_v3/pages/login.php" style="margin-right: 8px;">Log in</a>
      <a class="tnav cta" href="/ielts_beta_v3/pages/signup.php">Get started free</a>
    <?php endif; ?>
  </div>
</nav>

<!-- Close dropdown when clicking outside -->
<script>
window.onclick = function(event) {
  if (!event.target.matches('.av')) {
    var dropdowns = document.getElementsByClassName("dropdown-menu");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
</script>
<?php endif; ?>