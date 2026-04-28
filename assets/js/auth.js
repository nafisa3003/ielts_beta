// ═══════════════════════════════════════════════
// IELTS Beta — Auth Page Logic
// assets/js/auth.js
// ═══════════════════════════════════════════════

function showError(msg) {
  const el = document.getElementById('auth-error');
  if (el) { el.textContent = msg; el.classList.add('show'); }
}
function clearError() {
  const el = document.getElementById('auth-error');
  if (el) el.classList.remove('show');
}
function setLoading(on) {
  const el = document.getElementById('fb-loading');
  if (el) el.style.display = on ? 'flex' : 'none';
  document.querySelectorAll('button').forEach(b => b.disabled = on);
}

/* ── PASSWORD TOGGLE ───────────────────────── */
function togglePassword(btn, id) {
  const input = document.getElementById(id);
  if (!input) return;
  const isHide = input.type === 'password';
  input.type = isHide ? 'text' : 'password';
  btn.textContent = isHide ? '👁️' : '🔒';
}

/* ── GOOGLE OAUTH (Firebase) ────────────────── */
async function firebaseGoogle() {
  clearError();
  setLoading(true);
  try {
    const result = await fbAuth.signInWithPopup(googleProvider);
    await syncFirebaseUser(result.user);
    window.location.href = 'dashboard.php';
  } catch (e) {
    showError(e.message || 'Google sign-in failed.');
    setLoading(false);
  }
}

/* ── EMAIL LOGIN ────────────────────────────── */
async function doLogin() {
  clearError();
  const email    = document.getElementById('email')?.value.trim();
  const password = document.getElementById('password')?.value;
  if (!email || !password) { showError('Please fill in all fields.'); return; }

  setLoading(true);
  try {
    // Try Firebase email auth first
    const result = await fbAuth.signInWithEmailAndPassword(email, password);
    await syncFirebaseUser(result.user);
    window.location.href = 'dashboard.php';
  } catch (fbErr) {
    // Fallback: PHP session auth
    try {
      const res = await fetch('../api/auth.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      });
      const data = await res.json();
      if (data.success) {
        AuthState.set(data.user);
        window.location.href = 'dashboard.php';
      } else {
        showError(data.error || 'Invalid credentials.');
        setLoading(false);
      }
    } catch (e) {
      showError('Network error. Please try again.');
      setLoading(false);
    }
  }
}
/* ── FORGOT PASSWORD ────────────────────────── */
async function forgotPassword() {
  clearError();
  const email = document.getElementById('email')?.value.trim();

  if (!email) {
    showError('Please enter your email address first.');
    return;
  }

  try {
    await fbAuth.sendPasswordResetEmail(email);
    alert('Password reset email sent! Please check your inbox.');
  } catch (e) {
    showError(e.message || 'Failed to send reset email.');
  }
}

/* ── EMAIL SIGNUP ───────────────────────────── */
let selectedBand = 7.0;

function selectBand(el) {
  document.querySelectorAll('.band-pill').forEach(p => p.classList.remove('selected'));
  el.classList.add('selected');
  selectedBand = parseFloat(el.dataset.band);
}

function checkPwStrength(val) {
  const bars  = [document.getElementById('pw1'), document.getElementById('pw2'), document.getElementById('pw3')];
  const label = document.getElementById('pw-label');
  bars.forEach(b => b.className = 'pw-bar');
  if (!val) { if (label) label.textContent = 'Enter a password'; return; }
  let score = 0;
  if (val.length >= 8)       score++;
  if (/[A-Z]/.test(val))    score++;
  if (/[0-9!@#$%]/.test(val)) score++;
  const levels = ['weak','medium','strong'];
  const names  = ['Too weak','Getting there','Strong ✓'];
  for (let i = 0; i < score; i++) if (bars[i]) bars[i].classList.add(levels[score-1]);
  if (label) label.textContent = names[score-1] || '';
}

async function doSignup() {
  clearError();
  const fname    = document.getElementById('fname')?.value.trim();
  const lname    = document.getElementById('lname')?.value.trim();
  const email    = document.getElementById('email')?.value.trim();
  const password = document.getElementById('password')?.value;
  const name     = `${fname} ${lname}`.trim();

  if (!fname || !email || !password) { showError('Please fill in all required fields.'); return; }
  if (password.length < 6) { showError('Password must be at least 6 characters.'); return; }

  setLoading(true);
  try {
    // Create Firebase user
    const result = await fbAuth.createUserWithEmailAndPassword(email, password);
    await result.user.updateProfile({ displayName: name });
    // Sync to PHP DB
    await syncFirebaseUser(result.user);
    // Also create via PHP for complete DB record
    await fetch('../api/auth.php?action=signup', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, email, password, target_band: selectedBand })
    });
    window.location.href = 'dashboard.php';
  } catch (e) {
    if (e.code === 'auth/email-already-in-use') {
      showError('Email already registered. Try logging in.');
    } else {
      showError(e.message || 'Signup failed. Please try again.');
    }
    setLoading(false);
  }
}

/* ── ENTER KEY SUPPORT ──────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      if (document.getElementById('fname')) doSignup();
      else doLogin();
    }
  });
});