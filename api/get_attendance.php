<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

check_auth();
header('Content-Type: application/json');

$class_id = $_GET['class_id'] ?? null;
$date = $_GET['date'] ?? date('Y-m-d');

if (!$class_id) {
    echo json_encode(['success' => false, 'message' => 'Class ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT student_id, status FROM attendance WHERE class_id = ? AND attendance_date = ?");
    $stmt->execute([$class_id, $date]);
    $attendance = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // returns [student_id => status]

    echo json_encode(['success' => true, 'attendance' => $attendance]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching attendance']);
}
?>