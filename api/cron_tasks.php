<?php
require_once "../includes/config.php";
require_once "../includes/db.php";

// This script should be run via CRON job once a day or week

try {
    echo "Running background tasks...\n";

    // 1. Award 'Perfect Month' Badges
    $last_month = date('m', strtotime('first day of last month'));
    $year = date('Y', strtotime('first day of last month'));

    $stmt = $pdo->prepare("
        SELECT student_id
        FROM attendance
        WHERE MONTH(attendance_date) = ? AND YEAR(attendance_date) = ?
        GROUP BY student_id
        HAVING COUNT(*) = SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END)
    ");
    $stmt->execute([$last_month, $year]);
    $perfect_students = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($perfect_students as $s_id) {
        // Link to user account
        $user_stmt = $pdo->prepare("SELECT u.id FROM users u JOIN students s ON u.email = s.email WHERE s.id = ?");
        $user_stmt->execute([$s_id]);
        $u_id = $user_stmt->fetchColumn();

        if ($u_id) {
            $badge_stmt = $pdo->prepare("INSERT IGNORE INTO badges (student_id, badge_type) VALUES (?, 'Perfect Month')");
            $badge_stmt->execute([$u_id]);
        }
    }

    // 2. Send Weekly Summaries (if Monday)
    if (date('D') === 'Mon') {
        echo "Sending weekly summaries...\n";
        $students = $pdo->query("SELECT * FROM students")->fetchAll();
        foreach ($students as $s) {
            // Aggregate last week's data and send mock email
            // @mail($s['email'], "Weekly Summary", "Your attendance last week was...");
        }
    }

    echo "Tasks completed successfully.";

} catch (Exception $e) {
    echo "Error in cron: " . $e->getMessage();
}
?>
