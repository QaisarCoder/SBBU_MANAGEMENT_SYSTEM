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
    // Get unique dates for which attendance was marked for this class
    $stmt = $pdo->prepare("
        SELECT
            attendance_date,
            COUNT(*) as total_students,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
            SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count
        FROM attendance
        WHERE class_id = ?
        GROUP BY attendance_date
        ORDER BY attendance_date DESC
    ");
    $stmt->execute([$class_id]);
    $sessions = $stmt->fetchAll();

    echo json_encode(['success' => true, 'sessions' => $sessions]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
