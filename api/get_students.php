<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

check_auth();
header('Content-Type: application/json');

$class_id = $_GET['class_id'] ?? null;

if (!$class_id) {
    echo json_encode(['success' => false, 'message' => 'Class ID required']);
    exit;
}

try {
    // First get program_id, batch_id, and semester from class
    $stmt = $pdo->prepare("SELECT program_id, batch_id, semester FROM classes WHERE id = ?");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch();

    if (!$class) {
        echo json_encode(['success' => false, 'message' => 'Class not found']);
        exit;
    }

    // Get students matching program
    // We remove the strict semester/batch filter to ensure all students of the program are visible
    // as sometimes students from different semesters or batches attend the same class.
    $sql = "SELECT * FROM students WHERE program_id = ? AND status = 'active'";
    $params = [$class['program_id']];

    /*
    // Optional: Re-add these if strict filtering is required later
    if ($class['semester']) {
        $sql .= " AND semester = ?";
        $params[] = $class['semester'];
    }
    if ($class['batch_id']) {
        $sql .= " AND batch_id = ?";
        $params[] = $class['batch_id'];
    }
    */

    $sql .= " ORDER BY full_name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();

    echo json_encode(['success' => true, 'students' => $students]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching students: ' . $e->getMessage()]);
}
?>