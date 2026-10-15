<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($action === 'create') {
        check_auth(['student']);
        $stmt = $pdo->prepare("INSERT INTO disputes (student_id, attendance_id, reason) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $input['attendance_id'], $input['reason']]);
        echo json_encode(['success' => true, 'message' => 'Dispute raised successfully']);
    }
    elseif ($action === 'list') {
        check_auth(['admin', 'teacher']);
        $stmt = $pdo->query("
            SELECT d.*, u.full_name as student_name, a.attendance_date, c.class_code
            FROM disputes d
            JOIN users u ON d.student_id = u.id
            JOIN attendance a ON d.attendance_id = a.id
            JOIN classes c ON a.class_id = c.id
            ORDER BY d.created_at DESC
        ");
        echo json_encode(['success' => true, 'disputes' => $stmt->fetchAll()]);
    }
    elseif ($action === 'resolve') {
        check_auth(['admin', 'teacher']);
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE disputes SET status = ? WHERE id = ?");
        $stmt->execute([$input['status'], $input['id']]);

        if ($input['status'] === 'approved') {
            // Update attendance record to present
            $stmt = $pdo->prepare("UPDATE attendance SET status = 'present' WHERE id = (SELECT attendance_id FROM disputes WHERE id = ?)");
            $stmt->execute([$input['id']]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Dispute resolved']);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
