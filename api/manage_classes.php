<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";
require_once "../includes/functions.php";

check_auth(['admin', 'teacher']);

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

try {
    if ($action === "add") {
        $class_code = $input['class_code'] ?? '';
        $subject_id = $input['subject_id'] ?? null;
        $teacher_id = $input['teacher_id'] ?? null;
        $program_id = $input['program_id'] ?? null;
        $batch_id = $input['batch_id'] ?? null;
        $semester = $input['semester'] ?? null;
        $schedule = $input['schedule'] ?? '';
        $start_time = $input['start_time'] ?? null;
        $end_time = $input['end_time'] ?? null;

        // Fetch subject name for class_name
        $class_name = '';
        if ($subject_id) {
            $stmt_sub = $pdo->prepare("SELECT subject_name FROM subjects WHERE id = ?");
            $stmt_sub->execute([$subject_id]);
            $class_name = $stmt_sub->fetchColumn() ?: '';
        }
        if (empty($class_name)) $class_name = $class_code;

        $stmt = $pdo->prepare("INSERT INTO classes (class_code, class_name, subject_id, teacher_id, program_id, batch_id, semester, schedule, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$class_code, $class_name, $subject_id, $teacher_id, $program_id, $batch_id, $semester, $schedule, $start_time, $end_time]);

        log_activity($_SESSION['user_id'], "Registered new class: $class_code", $pdo);

        echo json_encode(["success" => true, "message" => "Class registered successfully"]);
    }
    elseif ($action === "update") {
        $id = $input['id'] ?? '';
        $class_code = $input['class_code'] ?? '';
        $subject_id = $input['subject_id'] ?? null;
        $teacher_id = $input['teacher_id'] ?? null;
        $program_id = $input['program_id'] ?? null;
        $batch_id = $input['batch_id'] ?? null;
        $semester = $input['semester'] ?? null;
        $schedule = $input['schedule'] ?? '';
        $start_time = $input['start_time'] ?? null;
        $end_time = $input['end_time'] ?? null;

        if (empty($id)) {
            echo json_encode(["success" => false, "message" => "Missing class ID"]);
            exit;
        }

        // Fetch subject name for class_name
        $class_name = '';
        if ($subject_id) {
            $stmt_sub = $pdo->prepare("SELECT subject_name FROM subjects WHERE id = ?");
            $stmt_sub->execute([$subject_id]);
            $class_name = $stmt_sub->fetchColumn() ?: '';
        }
        if (empty($class_name)) $class_name = $class_code;

        $stmt = $pdo->prepare("UPDATE classes SET class_code = ?, class_name = ?, subject_id = ?, teacher_id = ?, program_id = ?, batch_id = ?, semester = ?, schedule = ?, start_time = ?, end_time = ? WHERE id = ?");
        $stmt->execute([$class_code, $class_name, $subject_id, $teacher_id, $program_id, $batch_id, $semester, $schedule, $start_time, $end_time, $id]);

        log_activity($_SESSION['user_id'], "Updated class details: $class_code", $pdo);

        echo json_encode(["success" => true, "message" => "Class updated successfully"]);
    }
    elseif ($action === "delete") {
        $id = $input["id"] ?? "";

        if (empty($id)) {
            echo json_encode(["success" => false, "message" => "Missing class ID"]);
            exit;
        }

        // Get class code before deleting for logging
        $stmt_get = $pdo->prepare("SELECT class_code FROM classes WHERE id = ?");
        $stmt_get->execute([$id]);
        $class_code = $stmt_get->fetchColumn();

        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        $stmt->execute([$id]);

        log_activity($_SESSION['user_id'], "Deleted class: $class_code", $pdo);

        echo json_encode(["success" => true, "message" => "Class deleted"]);
    }
    else {
        echo json_encode(["success" => false, "message" => "Invalid action: " . $action]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>