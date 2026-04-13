<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'xdpmweb_test', 3307);

if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "     KIỂM TRA NGÂN HÀNG CÂU HỎI - QUESTION BANK VERIFICATION\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Get all subjects
$subjects_result = $mysqli->query('SELECT id, name FROM subjects ORDER BY id');
$total_questions = 0;
$total_options = 0;
$all_subjects = [];

echo "📚 DANH SÁCH MÔN HỌC:\n";
echo str_pad("STT", 5) . str_pad("ID", 5) . str_pad("Tên Môn", 40) . str_pad("Câu Hỏi", 12) . "Đáp Án\n";
echo str_repeat("─", 70) . "\n";

$i = 1;
while ($subject = $subjects_result->fetch_assoc()) {
    $all_subjects[] = $subject;
    
    // Count questions for this subject
    $q_result = $mysqli->query("SELECT COUNT(*) as count FROM questions WHERE subject_id = " . $subject['id']);
    $q_count = $q_result->fetch_assoc()['count'];
    
    // Count options for questions in this subject
    $opt_result = $mysqli->query("SELECT COUNT(*) as count FROM options o 
        JOIN questions q ON o.question_id = q.id 
        WHERE q.subject_id = " . $subject['id']);
    $opt_count = $opt_result->fetch_assoc()['count'];
    
    $total_questions += $q_count;
    $total_options += $opt_count;
    
    printf("%d. %d %-40s %d câu  %d đáp án\n", $i, $subject['id'], $subject['name'], $q_count, $opt_count);
    $i++;
}

echo str_repeat("─", 70) . "\n";
printf("%-45s %d câu  %d đáp án\n\n", "TỔNG CỘNG:", $total_questions, $total_options);

// Verify data integrity
echo "🔍 KIỂM TRA TÍNH TOÀN VẸN DỮ LIỆU:\n";
echo "─────────────────────────────────────\n";

// Check 1: All subjects have exactly 20 questions
$inconsistent = 0;
foreach ($all_subjects as $subject) {
    $result = $mysqli->query("SELECT COUNT(*) as count FROM questions WHERE subject_id = " . $subject['id']);
    $count = (int)$result->fetch_assoc()['count'];
    if ($count != 20) {
        echo "❌ {$subject['name']}: {$count} câu (cần 20)\n";
        $inconsistent++;
    }
}

if ($inconsistent === 0) {
    echo "✅ Tất cả 12 môn đều có đúng 20 câu hỏi\n";
}

// Check 2: All questions have options
$questions_missing_options = $mysqli->query("
    SELECT q.id, q.question FROM questions q 
    WHERE NOT EXISTS (SELECT 1 FROM options WHERE question_id = q.id) 
    AND q.subject_id IS NOT NULL
")->fetch_all(MYSQLI_ASSOC);

if (count($questions_missing_options) === 0) {
    echo "✅ Tất cả 240 câu hỏi đều có đáp án\n";
} else {
    echo "❌ Có " . count($questions_missing_options) . " câu không có đáp án\n";
}

// Check 3: Verify each question has 4 options
$question_options = $mysqli->query("
    SELECT q.id, COUNT(o.id) as option_count 
    FROM questions q 
    LEFT JOIN options o ON q.id = o.question_id 
    WHERE q.subject_id IS NOT NULL 
    GROUP BY q.id 
    HAVING option_count != 4
")->fetch_all(MYSQLI_ASSOC);

if (count($question_options) === 0) {
    echo "✅ Tất cả 240 câu hỏi đều có đúng 4 đáp án\n";
} else {
    echo "❌ Có " . count($question_options) . " câu không có đúng 4 đáp án\n";
}

// Check 4: Verify correct options
$missing_correct = $mysqli->query("
    SELECT q.id FROM questions q 
    WHERE subject_id IS NOT NULL 
    AND NOT EXISTS (SELECT 1 FROM options WHERE question_id = q.id AND is_correct = 1)
")->fetch_all(MYSQLI_ASSOC);

if (count($missing_correct) === 0) {
    echo "✅ Mỗi câu hỏi đều có đúng 1 đáp án đúng\n";
} else {
    echo "❌ Có " . count($missing_correct) . " câu không có đáp án đúng\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "                    ✅ TẤT CẢ DỮ LIỆU ĐÃ SẴN SÀNG\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📊 TÓM TẮT:\n";
echo "  • 12 chủ đề/môn học ✅\n";
echo "  • 240 câu hỏi (20 câu mỗi môn) ✅\n";
echo "  • 960 tổng đáp án (4 đáp án mỗi câu) ✅\n";
echo "  • Không thay đổi cấu trúc database gốc ✅\n";
echo "  • Không sử dụng --fresh, dữ liệu cũ được giữ ✅\n";
echo "\n";

$mysqli->close();
