<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";
require_once "../includes/functions.php";

check_auth(['admin']);

$action = $_POST["action"] ?? "";

try {
    if ($action === "add") {
        $student_id = $_POST["student_id"];
        $full_name = $_POST["full_name"];
        $email = $_POST["email"];
        $program_id = $_POST["program_id"];
        $batch_id = $_POST["batch_id"];
        $semester = $_POST["semester"];

        $pdo->beginTransaction();

        // 1. Add to students table
        $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, email, program_id, batch_id, semester) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $full_name, $email, $program_id, $batch_id, $semester]);
        
        // 2. Automatically create login account in users table
        // Use student_id as username and email as email. Generate cryptographically secure random password if not supplied.
        $provided_password = $_POST["password"] ?? "";
        $raw_password = !empty($provided_password) ? $provided_password : bin2hex(random_bytes(6));
        $pass_hash = password_hash($raw_password, PASSWORD_DEFAULT);
        
        // Securely store temporary credential in admin session only (never exposed in public messages or logs)
        if (empty($provided_password) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $_SESSION['last_temp_password'] = $raw_password;
            $_SESSION['last_temp_student_id'] = $student_id;
        }

        $stmt_user = $pdo->prepare("INSERT INTO users (user_code, username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?, 'student')");
        $stmt_user->execute([$student_id, $student_id, $email, $pass_hash, $full_name]);

        $pdo->commit();

        log_activity($_SESSION['user_id'], "Added new student & created login: $full_name ($student_id)", $pdo);
        
        echo json_encode(["success" => true, "message" => "Student registered and login created successfully."]);
    } 
    elseif ($action === "update") {
        $id = $_POST["id"];
        $student_id = $_POST["student_id"];
        $full_name = $_POST["full_name"];
        $email = $_POST["email"];
        $program_id = $_POST["program_id"];
        $batch_id = $_POST["batch_id"];
        $semester = $_POST["semester"];
        $password = $_POST["password"] ?? "";

        // Get old email to find user record
        $stmt_old = $pdo->prepare("SELECT email FROM students WHERE id = ?");
        $stmt_old->execute([$id]);
        $old_email = $stmt_old->fetchColumn();

        $pdo->beginTransaction();
        
        // 1. Update Student Table
        $stmt = $pdo->prepare("UPDATE students SET student_id=?, full_name=?, email=?, program_id=?, batch_id=?, semester=? WHERE id=?");
        $stmt->execute([$student_id, $full_name, $email, $program_id, $batch_id, $semester, $id]);

        // 2. Update Linked User Record (if any)
        if ($old_email) {
            // Find user by email
            $user_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $user_stmt->execute([$old_email]);
            $user_id_row = $user_stmt->fetch();
            
            if ($user_id_row) {
                $uid = $user_id_row['id'];
                if (!empty($password)) {
                    $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                    // Update username and user_code to match student_id for login consistency
                    $upd_user = $pdo->prepare("UPDATE users SET user_code=?, username=?, email=?, full_name=?, password=? WHERE id=?");
                    $upd_user->execute([$student_id, $student_id, $email, $full_name, $pass_hash, $uid]);
                } else {
                    $upd_user = $pdo->prepare("UPDATE users SET user_code=?, username=?, email=?, full_name=? WHERE id=?");
                    $upd_user->execute([$student_id, $student_id, $email, $full_name, $uid]);
                }
            } else {
                // If student exists but user account doesn't, create it now
                $raw_password = !empty($password) ? $password : bin2hex(random_bytes(6));
                $pass_hash = password_hash($raw_password, PASSWORD_DEFAULT);
                
                if (empty($password) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                    $_SESSION['last_temp_password'] = $raw_password;
                    $_SESSION['last_temp_student_id'] = $student_id;
                }

                $stmt_user = $pdo->prepare("INSERT INTO users (user_code, username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?, 'student')");
                $stmt_user->execute([$student_id, $student_id, $email, $pass_hash, $full_name]);
            }
        }

        $pdo->commit();
        log_activity($_SESSION['user_id'], "Updated student & login: $full_name ($student_id)", $pdo);
        echo json_encode(["success" => true, "message" => "Student details and login updated successfully"]);
    }
    elseif ($action === "delete") {
        $id = $_POST["id"] ?? "";
        
        // Get student info before deleting
        $stmt_get = $pdo->prepare("SELECT full_name, student_id, email FROM students WHERE id = ?");
        $stmt_get->execute([$id]);
        $student = $stmt_get->fetch();
        
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($student) {
            // Also delete their user account
            $del_user = $pdo->prepare("DELETE FROM users WHERE email = ? AND role = 'student'");
            $del_user->execute([$student['email']]);
            
            log_activity($_SESSION['user_id'], "Deleted student & account: " . $student['full_name'] . " (" . $student['student_id'] . ")", $pdo);
        }
        
        $pdo->commit();
        echo json_encode(["success" => true, "message" => "Student and account deleted successfully"]);
    }
    else {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>