<?php
$page_title='Log In';$body_class='auth-body';
$extra_css=['auth.css'];$topbar_links='auth';
require_once '../includes/header.php';
require_once '../includes/config.php';
?>
<div class="auth-bg">
  <div class="auth-orb auth-orb1"></div><div class="auth-orb auth-orb2"></div>
  <div class="auth-card">
    <div class="auth-logo"><img src="/ielts_beta_v3/assets/img/logo-transparent.png" alt="IELTS Beta" onerror="this.style.display='none'"></div>
    <h2 class="auth-title">Welcome back! 👋</h2>
    <p class="auth-sub">Log in to continue your IELTS journey.</p>
    <div class="auth-error" id="auth-error"></div>

    <!-- Google / Firebase login -->
    <button class="soc-btn" id="google-btn" onclick="firebaseGoogle()">
      <img src="/ielts_beta_v3/assets/img/google.svg" alt="Google"> Continue with Google
    </button>
    <div class="divider">or with email</div>

    <div class="fg"><label class="fl">Email address</label><input class="fi" type="email" id="email" placeholder="you@email.com" autocomplete="email"></div>
    <div class="fg">
    <label class="fl">Password</label>
    <div class="pw-wrap">
        <input class="fi" type="password" id="password" placeholder="••••••••" autocomplete="current-password">
        <button class="pw-toggle" onclick="togglePassword(this, 'password')">🔒</button>
    </div>
  </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <label style="font-size:13px;color:var(--text2);display:flex;align-items:center;gap:6px;cursor:pointer;"><input type="checkbox" id="remember" style="accent-color:var(--teal);"> Remember me</label>
      <a class="alink" style="font-size:13px;cursor:pointer;" onclick="forgotPassword()">Forgot password?</a>
    </div>
    <button class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;" onclick="doLogin()">Log in →</button>
    <div class="auth-footer">No account? <a class="alink" href="signup.php">Sign up free</a></div>

    <div class="firebase-loading" id="fb-loading"><div class="spinner"></div></div>
  </div>
</div>

<?php
$extra_js = ['auth.js'];
require_once '../includes/footer.php';
?>
