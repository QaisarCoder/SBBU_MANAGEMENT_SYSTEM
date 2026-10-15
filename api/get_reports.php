<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

check_auth();

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    echo json_encode(['success' => false, 'message' => 'Class ID is required']);
    exit;
}

try {
    // Aggregation query to get student-wise attendance stats for a specific class
    $stmt = $pdo->prepare("
        SELECT
            s.full_name,
            s.student_id as student_code,
            COUNT(a.id) as total_sessions,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
            SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_count,
            ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 WHEN a.status = 'late' THEN 0.5 ELSE 0 END) / COUNT(a.id)) * 100, 2) as percentage
        FROM students s
        JOIN attendance a ON s.id = a.student_id
        WHERE a.class_id = ?
        GROUP BY s.id
        ORDER BY s.full_name ASC
    ");
    $stmt->execute([$class_id]);
    $report = $stmt->fetchAll();

    echo json_encode(['success' => true, 'report' => $report]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error generating report: ' . $e->getMessage()]);
}
?>
