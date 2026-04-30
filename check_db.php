<?php
require_once 'includes/config.php';
try {
    $count = queryOne("SELECT COUNT(*) as total FROM flashcards WHERE user_id IS NULL");
    echo "<h1>Database Diagnostic</h1>";
    echo "<p>Connected to: <b>" . DB_NAME . "</b></p>";
    echo "<p>Total Global Flashcards found: <b style='color:green; font-size:24px;'>" . $count['total'] . "</b></p>";
    
    if ($count['total'] < 100) {
        echo "<p style='color:red;'>⚠️ Warning: You have fewer than 100 words. It seems <b>real_content.sql</b> was not imported correctly or was overwritten.</p>";
    } else {
        echo "<p style='color:blue;'>✅ Success! Your database has the full word list.</p>";
    }

    $sample = query("SELECT word FROM flashcards WHERE user_id IS NULL LIMIT 5");
    echo "<h3>Sample Words in DB:</h3><ul>";
    foreach($sample as $s) echo "<li>" . $s['word'] . "</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
unlink(__FILE__); // Self-delete for security
