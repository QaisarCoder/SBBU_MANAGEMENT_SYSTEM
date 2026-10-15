<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";

check_auth();

try {
    $stmt = $pdo->query("
        SELECT p.*, u.full_name as teacher_name
        FROM programs p
        LEFT JOIN users u ON p.assigned_teacher_id = u.id
        ORDER BY p.program_name ASC
    ");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch subjects for all programs
    $stmt_sub = $pdo->query("SELECT id, subject_name, program_id FROM subjects");
    $subjects = $stmt_sub->fetchAll(PDO::FETCH_ASSOC);

    // Group subjects by program_id
    $subjects_by_program = [];
    foreach ($subjects as $s) {
        $subjects_by_program[$s['program_id']][] = $s['subject_name'];
    }

    // Attach subjects to programs
    foreach ($programs as &$p) {
        $p['subjects'] = $subjects_by_program[$p['id']] ?? [];
    }

    echo json_encode(["success" => true, "programs" => $programs]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>