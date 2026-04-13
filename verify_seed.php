<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'xdpmweb_test', 3307);

if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

// Test subject 1
$result = $mysqli->query('SELECT * FROM subjects WHERE id = 1');
$subject = $result->fetch_assoc();
echo "Subject 1: " . ($subject ? $subject['name'] : 'NOT FOUND') . "\n";

// Count questions for subject 1
$result = $mysqli->query('SELECT COUNT(*) as count FROM questions WHERE subject_id = 1');
$row = $result->fetch_assoc();
echo "Questions for Subject 1: " . $row['count'] . "\n";

// Show a sample question
$result = $mysqli->query('SELECT id, question FROM questions WHERE subject_id = 1 LIMIT 1');
$q = $result->fetch_assoc();
if ($q) {
    echo "Sample question ID: " . $q['id'] . " - " . substr($q['question'], 0, 50) . "...\n";
    
    // Count options for this question
    $result = $mysqli->query('SELECT COUNT(*) as count FROM options WHERE question_id = ' . $q['id']);
    $opts = $result->fetch_assoc();
    echo "Options for this question: " . $opts['count'] . "\n";
}

echo "\n✅ All systems ready for testing!\n";
$mysqli->close();

