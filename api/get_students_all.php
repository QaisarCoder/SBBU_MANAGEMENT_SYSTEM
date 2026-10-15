<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";
check_auth();
try {
    $stmt = $pdo->query("SELECT s.*, b.batch_name FROM students s LEFT JOIN batches b ON s.batch_id = b.id WHERE s.status = 'active' ORDER BY s.id DESC");
    $students = $stmt->fetchAll();
    echo json_encode(["success" => true, "students" => $students]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
