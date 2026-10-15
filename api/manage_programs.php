<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";

check_auth(['admin']);

$action = $_GET["action"] ?? $_POST["action"] ?? "";
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

try {
    if ($action === "add") {
        $code = $input["program_code"] ?? "";
        $name = $input["program_name"] ?? "";
        $teacher_id = !empty($input["assigned_teacher_id"]) ? $input["assigned_teacher_id"] : null;

        $stmt = $pdo->prepare("INSERT INTO programs (program_code, program_name, assigned_teacher_id) VALUES (?, ?, ?)");
        $stmt->execute([$code, $name, $teacher_id]);
        echo json_encode(["success" => true, "message" => "Program created"]);
    }
    elseif ($action === "update") {
        $id = $input["id"] ?? "";
        $code = $input["program_code"] ?? "";
        $name = $input["program_name"] ?? "";
        $teacher_id = !empty($input["assigned_teacher_id"]) ? $input["assigned_teacher_id"] : null;

        if (empty($id)) {
            echo json_encode(["success" => false, "message" => "Missing program ID"]);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE programs SET program_code = ?, program_name = ?, assigned_teacher_id = ? WHERE id = ?");
        $stmt->execute([$code, $name, $teacher_id, $id]);
        echo json_encode(["success" => true, "message" => "Program updated successfully"]);
    }
    elseif ($action === "delete") {
        $id = $_POST["id"] ?? "";
        $stmt = $pdo->prepare("DELETE FROM programs WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["success" => true, "message" => "Program deleted"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>