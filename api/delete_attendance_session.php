<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

check_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$class_id = $data['class_id'] ?? null;
$date = $data['date'] ?? null;

if (!$class_id || !$date) {
    echo json_encode(['success' => false, 'message' => 'Class ID and Date required']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE class_id = ? AND attendance_date = ?");
    $stmt->execute([$class_id, $date]);

    echo json_encode(['success' => true, 'message' => 'Attendance session deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
