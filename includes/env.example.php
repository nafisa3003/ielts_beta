<?php
// ============================================================
// IELTS Beta — Environment / Secrets
// includes/env.example.php
//
// SETUP:
//   cp includes/env.example.php includes/env.php
//   Then fill in your real values below.
//
// env.php is listed in .gitignore and will NEVER be pushed
// to GitHub. This example file IS committed (with no real keys)
// so teammates know what variables are needed.
// ============================================================

// ── DATABASE ──────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // your MySQL username
define('DB_PASS', '');              // your MySQL password
define('DB_NAME', 'ielts_beta');

// ── FIREBASE (client-side config — safe to expose publicly) ───
// These go into assets/js/firebase-init.js, NOT kept secret.
// Firebase security is enforced by Firebase Rules, not by hiding these.
// See: https://firebase.google.com/docs/projects/api-keys
define('FIREBASE_API_KEY',     'YOUR_FIREBASE_API_KEY');
define('FIREBASE_PROJECT_ID',  'YOUR_PROJECT_ID');
define('FIREBASE_APP_ID',      'YOUR_APP_ID');
define('FIREBASE_AUTH_DOMAIN', 'YOUR_PROJECT.firebaseapp.com');

// ── CHATBOT (server-side — KEEP SECRET) ───────────────────────
// These are used by the Python FastAPI server in chatbot/.env
// They are listed here just as a reminder of what's needed.
// DO NOT put them here — put them in chatbot/.env instead.
//
// GROQ_API_KEY=gsk_...
// TAVILY_API_KEY=tvly-...