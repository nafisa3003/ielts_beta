<?php
// ============================================================
// IELTS Beta — Environment / Secrets
// includes/env.example.php
//
// SETUP:
//   1. Copy this file: cp includes/env.example.php includes/env.php
//   2. Fill in your real values in includes/env.php
//
// NOTE: env.php is gitignored to keep your credentials safe.
// ============================================================

// ── DATABASE ──────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ielts_beta');

// ── FIREBASE (Client-side Config) ─────────────────────────────
// You can find these in your Firebase Project Settings
define('FIREBASE_API_KEY',             'YOUR_FIREBASE_API_KEY');
define('FIREBASE_AUTH_DOMAIN',         'your-project.firebaseapp.com');
define('FIREBASE_PROJECT_ID',          'your-project-id');
define('FIREBASE_STORAGE_BUCKET',      'your-project.firebasestorage.app');
define('FIREBASE_MESSAGING_SENDER_ID', '1234567890');
define('FIREBASE_APP_ID',              '1:1234567890:web:abcdef12345');
define('FIREBASE_MEASUREMENT_ID',      'G-XXXXXXXXXX');

// ── CHATBOT (Python API) ──────────────────────────────────────
// The URL where your FastAPI server is running
define('CHATBOT_API_URL', 'http://127.0.0.1:8000');

// ── APP SETTINGS ──────────────────────────────────────────────
define('DEV_MODE', true); // Set to false in production