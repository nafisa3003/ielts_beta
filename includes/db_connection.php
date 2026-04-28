<?php
// includes/db_connection.php
require_once __DIR__ . '/config.php';

class IELTSContent {
    private $db;

    public function __construct() {
        $this->db = db(); // Reusing the singleton PDO from config.php
    }

    /**
     * Fetch global academic word list with optional difficulty filtering
     */
    public function getGlobalFlashcards($difficulty = 'all') {
        if ($difficulty !== 'all') {
            return query("SELECT * FROM flashcards WHERE user_id IS NULL AND difficulty = ? ORDER BY word ASC", [$difficulty]);
        }
        return query("SELECT * FROM flashcards WHERE user_id IS NULL ORDER BY word ASC");
    }

    /**
     * Fetch user's personal flashcards
     */
    public function getUserFlashcards($user_id) {
        return query("SELECT * FROM flashcards WHERE user_id = ? ORDER BY id DESC", [$user_id]);
    }

    /**
     * Fetch all vocabulary flashcards (legacy support)
     */
    public function getFlashcards($limit = 100) {
        $stmt = $this->db->prepare("SELECT * FROM flashcards ORDER BY word ASC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Fetch Cambridge Resources with optional tier filtering
     */
    public function getResources($tier = 'all') {
        if ($tier === 'premium') {
            return query("SELECT * FROM cambridge_resources WHERE is_premium = 1 ORDER BY id DESC");
        } elseif ($tier === 'free') {
            return query("SELECT * FROM cambridge_resources WHERE is_premium = 0 ORDER BY id DESC");
        }
        return query("SELECT * FROM cambridge_resources ORDER BY is_premium ASC, id DESC");
    }

    /**
     * Fetch chat history for a specific user
     */
    public function getChatMessages($user_id) {
        return query("SELECT * FROM chat_messages WHERE user_id = ? ORDER BY created_at ASC", [$user_id]);
    }
}
?>
