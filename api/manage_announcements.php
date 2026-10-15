<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";
require_once "../includes/functions.php";

check_auth(); // Generic auth check, specific roles below

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

try {
    if ($action === "list") {
        $stmt = $pdo->query("
            SELECT a.*, u.full_name as author
            FROM announcements a
            LEFT JOIN users u ON a.posted_by = u.id
            ORDER BY a.posted_date DESC
            LIMIT 10
        ");
        echo json_encode(["success" => true, "announcements" => $stmt->fetchAll()]);
    }
    elseif ($action === "add") {
        if (!is_admin() && !is_teacher()) throw new Exception("Unauthorized.");
        $title = $input["title"] ?? "";
        $content = $input["content"] ?? "";
        $posted_by = $_SESSION['user_id'];

        if (empty($title) || empty($content)) {
            throw new Exception("Title and content are required.");
        }

        $stmt = $pdo->prepare("INSERT INTO announcements (title, content, posted_by) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $posted_by]);

        log_activity($posted_by, "Posted a new announcement: $title", $pdo);

        echo json_encode(["success" => true, "message" => "Announcement posted successfully"]);
    }
    elseif ($action === "delete") {
        $id = $input["id"] ?? "";
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["success" => true, "message" => "Announcement deleted"]);
    }
    else {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>