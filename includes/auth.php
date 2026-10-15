<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_auth($allowed_roles = []) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    // Check if 2FA is pending
    if (isset($_SESSION['2fa_pending']) && $_SESSION['2fa_pending'] === true) {
        $current_page = basename($_SERVER['PHP_SELF']);
        if ($current_page !== 'verify_2fa.php') {
            header("Location: ../verify_2fa.php");
            exit();
        }
    }

    // RBAC
    if (!empty($allowed_roles) && !in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: ../index.php?error=unauthorized");
        exit();
    }
}

function get_user_role() {
    return $_SESSION['role'] ?? 'guest';
}

function is_admin() {
    return get_user_role() === 'admin';
}

function is_teacher() {
    return get_user_role() === 'teacher';
}

function is_clerk() {
    return get_user_role() === 'staff' || get_user_role() === 'clerk';
}

function is_student() {
    return get_user_role() === 'student';
}
?>