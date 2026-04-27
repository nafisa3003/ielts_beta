// ═══════════════════════════════════════════════
// IELTS Beta — Global Helpers
// assets/js/globals.js
// ═══════════════════════════════════════════════

// --- Dark Mode Toggle ---
const Theme = {
  toggle() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    const btn = document.getElementById('dm-toggle');
    if (btn) btn.textContent = newTheme === 'dark' ? '☀️' : '🌙';
  },
  init() {
    const saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    const btn = document.getElementById('dm-toggle');
    if (btn) btn.textContent = saved === 'dark' ? '☀️' : '🌙';
  }
};

// --- Toast Notifications ---
const Toast = {
  show(msg, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.textContent = msg;
    container.appendChild(t);
    setTimeout(() => t.remove(), 3500);
  }
};

// --- Global Logout ---
window.logout = async function() {
  try {
    // 1. Sign out from Firebase if available
    if (typeof fbAuth !== 'undefined') {
      await fbAuth.signOut();
    }
    // 2. Clear PHP Session
    await fetch('/ielts_beta_v3/api/auth.php?action=logout');
    // 3. Redirect home
    window.location.href = '/ielts_beta_v3/pages/home.php';
  } catch (e) {
    console.error('Logout failed', e);
    window.location.href = '/ielts_beta_v3/pages/home.php';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  Theme.init();
  const dmBtn = document.getElementById('dm-toggle');
  if (dmBtn) dmBtn.onclick = Theme.toggle;
});
