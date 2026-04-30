<?php
require_once '../includes/config.php';
$user = auth_required();

$attempt_id = intval($_GET['attempt_id'] ?? 0);
if (!$attempt_id) {
    header('Location: mock-tests.php');
    exit;
}

$attempt = queryOne('SELECT a.*, t.title FROM test_attempts a JOIN mock_tests t ON a.test_id = t.id WHERE a.id = ? AND a.user_id = ?', [$attempt_id, $user['id']]);

if (!$attempt) {
    die('Attempt not found.');
}

$page_title = 'Test Results';
$active_page = 'mock-tests';
$extra_css = ['dashboard.css'];
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="app-layout">
<main class="app-main">
  <div class="pg-header">
    <div>
      <div class="pg-title">🎉 Test Completed!</div>
      <div class="pg-sub">Here is how you performed in <?= htmlspecialchars($attempt['title']) ?></div>
    </div>
    <a href="mock-tests.php" class="btn btn-ghost btn-sm">← Back to Tests</a>
  </div>

  <div class="result-card" style="background:#fff; border-radius:16px; padding:40px; text-align:center; box-shadow:0 4px 20px rgba(0,0,0,0.05); border:1px solid #e5e7eb; margin-top:24px;">
    <div style="font-size:14px; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280; font-weight:700;">Overall Band Score</div>
    <div style="font-size:96px; font-weight:800; color:var(--teal); line-height:1; margin:20px 0;"><?= number_format($attempt['overall'], 1) ?></div>
    
    <?php
    $feedback = "";
    if ($attempt['overall'] >= 8.0) $feedback = "Outstanding! You are performing at an expert level.";
    elseif ($attempt['overall'] >= 7.0) $feedback = "Great job! You have reached a very good level of English.";
    elseif ($attempt['overall'] >= 6.0) $feedback = "Well done! You have a competent grasp of the language.";
    else $feedback = "Keep practicing! You are making progress toward your goal.";
    ?>
    <p style="font-size:18px; color:#374151; max-width:500px; margin:0 auto 30px;"><?= $feedback ?></p>

    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-top:40px;">
      <div style="background:#f9fafb; padding:20px; border-radius:12px;">
        <div style="font-size:11px; color:#9ca3af; text-transform:uppercase; font-weight:700;">Listening</div>
        <div style="font-size:24px; font-weight:800; color:#1f2937;"><?= number_format($attempt['score_l'], 1) ?></div>
      </div>
      <div style="background:#f9fafb; padding:20px; border-radius:12px;">
        <div style="font-size:11px; color:#9ca3af; text-transform:uppercase; font-weight:700;">Reading</div>
        <div style="font-size:24px; font-weight:800; color:#1f2937;"><?= number_format($attempt['score_r'], 1) ?></div>
      </div>
      <div style="background:#f9fafb; padding:20px; border-radius:12px;">
        <div style="font-size:11px; color:#9ca3af; text-transform:uppercase; font-weight:700;">Writing</div>
        <div style="font-size:24px; font-weight:800; color:#1f2937;"><?= number_format($attempt['score_w'], 1) ?></div>
      </div>
      <div style="background:#f9fafb; padding:20px; border-radius:12px;">
        <div style="font-size:11px; color:#9ca3af; text-transform:uppercase; font-weight:700;">Speaking</div>
        <div style="font-size:24px; font-weight:800; color:#1f2937;"><?= number_format($attempt['score_s'], 1) ?></div>
      </div>
    </div>

    <div style="margin-top:40px; padding-top:40px; border-top:1px solid #e5e7eb;">
      <button class="btn btn-primary btn-lg" onclick="location.href='dashboard.php'">Go to Dashboard</button>
      <button class="btn btn-ghost btn-lg" style="margin-left:12px;" onclick="location.href='flashcards.php'">Study Vocabulary</button>
    </div>
  </div>
</main>
</div>

<?php require_once '../includes/footer.php'; ?>
