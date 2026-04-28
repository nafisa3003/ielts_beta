<?php
$page_title='Create Account';$body_class='auth-body';
$extra_css=['auth.css'];$topbar_links='auth';
require_once '../includes/header.php';
?>
<div class="auth-bg">
  <div class="auth-orb auth-orb1"></div><div class="auth-orb auth-orb2"></div>
  <div class="auth-card">
    <div class="auth-logo"><img src="/ielts_beta_v3/assets/img/logo-transparent.png" alt="IELTS Beta" onerror="this.style.display='none'"></div>
    <h2 class="auth-title">Create your account 🚀</h2>
    <p class="auth-sub">Start free — no credit card needed.</p>
    <div class="auth-error" id="auth-error"></div>

    <button class="soc-btn" id="google-btn" onclick="firebaseGoogle()">
      <img src="/ielts_beta_v3/assets/img/google.svg" alt="Google"> Sign up with Google
    </button>
    <div class="divider">or with email</div>

    <div class="grid2" style="gap:12px;">
      <div class="fg"><label class="fl">First name</label><input class="fi" id="fname" placeholder="Aisha"></div>
      <div class="fg"><label class="fl">Last name</label><input class="fi" id="lname" placeholder="Rahman"></div>
    </div>
    <div class="fg"><label class="fl">Email address</label><input class="fi" type="email" id="email" placeholder="you@email.com"></div>
    <div class="fg">
      <label class="fl">Password</label>
      <div class="pw-wrap">
    <input class="fi" type="password" id="password" placeholder="Create a strong password" oninput="checkPwStrength(this.value)">
    <button class="pw-toggle" onclick="togglePassword(this, 'password')">🔒</button>
</div>
      <div class="pw-strength"><div class="pw-bar" id="pw1"></div><div class="pw-bar" id="pw2"></div><div class="pw-bar" id="pw3"></div></div>
      <div class="pw-label" id="pw-label">Enter a password</div>
    </div>
    <div class="fg">
      <label class="fl">Target band score</label>
      <div class="band-pills">
        <div class="band-pill" data-band="5.5" onclick="selectBand(this)">Band 5.5</div>
        <div class="band-pill" data-band="6.0" onclick="selectBand(this)">Band 6.0</div>
        <div class="band-pill selected" data-band="7.0" onclick="selectBand(this)">Band 7.0</div>
        <div class="band-pill" data-band="7.5" onclick="selectBand(this)">Band 7.5</div>
        <div class="band-pill" data-band="8.0" onclick="selectBand(this)">Band 8.0+</div>
      </div>
    </div>
    <button class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;" onclick="doSignup()">Create account →</button>
    <div class="auth-footer">Have an account? <a class="alink" href="login.php">Log in</a></div>

    <div class="firebase-loading" id="fb-loading"><div class="spinner"></div></div>
  </div>
</div>

<?php
$extra_js = ['auth.js'];
require_once '../includes/footer.php';
?>
