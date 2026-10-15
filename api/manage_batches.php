<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";

check_auth(['admin']);

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($action === 'list') {
        $stmt = $pdo->query("SELECT b.*, p.program_name FROM batches b LEFT JOIN programs p ON b.program_id = p.id ORDER BY b.start_year DESC, b.batch_name ASC");
        echo json_encode(['success' => true, 'batches' => $stmt->fetchAll()]);
    }
    elseif ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO batches (batch_name, program_id, start_year, end_year) VALUES (?, ?, ?, ?)");
        $stmt->execute([$input['batch_name'], $input['program_id'], $input['start_year'], $input['end_year']]);
        echo json_encode(['success' => true, 'message' => 'Batch created successfully']);
    }
    elseif ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE batches SET batch_name = ?, program_id = ?, start_year = ?, end_year = ? WHERE id = ?");
        $stmt->execute([$input['batch_name'], $input['program_id'], $input['start_year'], $input['end_year'], $input['id']]);
        echo json_encode(['success' => true, 'message' => 'Batch updated successfully']);
    }
    elseif ($action === 'delete') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("DELETE FROM batches WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Batch deleted successfully']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
