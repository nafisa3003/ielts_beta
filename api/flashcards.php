<?php
// api/flashcards.php
require_once '../includes/config.php';
// Force clean JSON output
error_reporting(0);
ini_set('display_errors', 0);

$user = auth_required();
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    $word = trim($d['word'] ?? ''); 
    $def = trim($d['definition'] ?? '');
    
    if (!$word || !$def) {
        json_response(['error' => 'Word and definition are required'], 422);
    }
    
    try {
        $id = execute('INSERT INTO flashcards (word, definition, category, user_id) VALUES (?, ?, ?, ?)', 
                      [$word, $def, 'Custom', $user['id']]);
        json_response(['success' => true, 'id' => $id]);
    } catch (Exception $e) {
        json_response(['error' => 'Database error: ' . $e->getMessage()], 500);
    }
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    $card_id = $d['card_id']; 
    $word = trim($d['word'] ?? ''); 
    $def = trim($d['definition'] ?? '');
    
    // Auth Check
    $check = queryOne('SELECT id FROM flashcards WHERE id=? AND user_id=?', [$card_id, $user['id']]);
    if (!$check) json_response(['error' => 'You can only edit your own custom cards'], 403);
    
    execute('UPDATE flashcards SET word=?, definition=? WHERE id=?', [$word, $def, $card_id]);
    json_response(['success' => true]);
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    $card_id = $d['card_id'];
    
    // Auth Check
    $check = queryOne('SELECT id FROM flashcards WHERE id=? AND user_id=?', [$card_id, $user['id']]);
    if (!$check) json_response(['error' => 'You can only delete your own custom cards'], 403);
    
    execute('DELETE FROM flashcards WHERE id=?', [$card_id]);
    execute('DELETE FROM flashcard_reviews WHERE card_id=?', [$card_id]);
    json_response(['success' => true]);
}

if ($action === 'review' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    $card_id = $d['card_id']; 
    $result = $d['result'];
    
    $interval = ($result === 'again') ? 0 : (($result === 'hard') ? 1 : 3);
    $next_review = date('Y-m-d', strtotime("+$interval days"));
    
    execute('INSERT INTO flashcard_reviews (user_id, card_id, result, next_review) VALUES (?,?,?,?)',
            [$user['id'], $card_id, $result, $next_review]);
    json_response(['success' => true]);
}

json_response(['error' => 'Invalid action'], 400);
