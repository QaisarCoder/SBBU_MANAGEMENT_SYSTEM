<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";

check_auth(['admin']);

$action = $_GET['action'] ?? '';

try {
    if ($action === 'list') {
        $stmt = $pdo->query("SELECT s.*, u.full_name, u.username, u.role FROM user_sessions s JOIN users u ON s.user_id = u.id ORDER BY s.last_activity DESC");
        echo json_encode(['success' => true, 'sessions' => $stmt->fetchAll()]);
    }
    elseif ($action === 'terminate') {
        $id = $_GET['id'] ?? '';
        $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Session terminated']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
