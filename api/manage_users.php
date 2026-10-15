<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";

// Teachers can see list for assignment, but only admins can perform mutations
$action = $_GET["action"] ?? "list";

try {
    if ($action === "list") {
        check_auth(['admin', 'teacher']);
        $role_filter = $_GET['role'] ?? '';
        $query = "SELECT id, user_code, username, email, full_name, role FROM users";
        $params = [];
        if (!empty($role_filter)) {
            $query .= " WHERE role = ?";
            $params[] = $role_filter;
        }
        $query .= " ORDER BY id DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        echo json_encode(["success" => true, "users" => $stmt->fetchAll()]);
    }
    elseif ($action === "add" || $action === "update" || $action === "delete") {
        check_auth(['admin']);

        if ($action === "add") {
            $username = $_POST["username"];
            $email = $_POST["email"];
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $full_name = $_POST["full_name"];
            $role = $_POST["role"];
            $user_code = $_POST["user_code"] ?? "";

            if (empty($user_code)) {
                // Generate User Code
                $prefix = strtoupper(substr($role, 0, 3));
                if ($role === 'staff' || $role === 'clerk') $prefix = 'CLK';

                $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = ? OR (role IN ('staff', 'clerk') AND 'CLK' = ?)");
                $count_stmt->execute([$role, $prefix]);
                $count = $count_stmt->fetchColumn() + 1;
                $user_code = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }

            $stmt = $pdo->prepare("INSERT INTO users (user_code, username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_code, $username, $email, $password, $full_name, $role]);
            echo json_encode(["success" => true, "message" => "User $user_code created successfully"]);
        }
        elseif ($action === "update") {
            $id = $_POST["id"];
            if ($id == $_SESSION['user_id']) throw new Exception("You cannot update your own account.");

            $username = $_POST["username"];
            $email = $_POST["email"];
            $full_name = $_POST["full_name"];
            $role = $_POST["role"];
            $user_code = $_POST["user_code"] ?? "";

            if (!empty($_POST["password"])) {
                $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, password=?, full_name=?, role=?, user_code=? WHERE id=?");
                $stmt->execute([$username, $email, $password, $full_name, $role, $user_code, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, full_name=?, role=?, user_code=? WHERE id=?");
                $stmt->execute([$username, $email, $full_name, $role, $user_code, $id]);
            }
            echo json_encode(["success" => true, "message" => "User updated successfully"]);
        }
        elseif ($action === "delete") {
            $id = $_POST["id"];
            if ($id == $_SESSION['user_id']) throw new Exception("You cannot delete your own account.");

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["success" => true, "message" => "User deleted"]);
        }
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>