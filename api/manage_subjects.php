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
        $stmt = $pdo->query("SELECT s.*, p.program_name FROM subjects s LEFT JOIN programs p ON s.program_id = p.id ORDER BY s.subject_code ASC");
        echo json_encode(['success' => true, 'subjects' => $stmt->fetchAll()]);
    }
    elseif ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_code, subject_name, program_id, semester) VALUES (?, ?, ?, ?)");
        $stmt->execute([$input['subject_code'], $input['subject_name'], $input['program_id'], $input['semester']]);
        echo json_encode(['success' => true, 'message' => 'Subject added successfully']);
    }
    elseif ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE subjects SET subject_code = ?, subject_name = ?, program_id = ?, semester = ? WHERE id = ?");
        $stmt->execute([$input['subject_code'], $input['subject_name'], $input['program_id'], $input['semester'], $input['id']]);
        echo json_encode(['success' => true, 'message' => 'Subject updated successfully']);
    }
    elseif ($action === 'delete') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Subject deleted successfully']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
