<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

check_auth();

try {
    $role = $_SESSION['role'];
    $user_id = $_SESSION['user_id'];

    $query = "
        SELECT
            c.*,
            p.program_name,
            u.full_name as teacher_name,
            u.role as teacher_role,
            s.subject_name,
            b.batch_name
        FROM classes c
        LEFT JOIN programs p ON c.program_id = p.id
        LEFT JOIN users u ON c.teacher_id = u.id
        LEFT JOIN subjects s ON c.subject_id = s.id
        LEFT JOIN batches b ON c.batch_id = b.id
    ";

    // Filtering by role
    if ($role === 'teacher') {
        // For Teacher role, we now show ALL active classes so they can mark attendance for anyone
        // but we can still order them to show 'their' classes first if we wanted.
        // For now, simple alphabetical order to ensure they see everything.
        $query .= " WHERE c.status = 'active'";
        $stmt = $pdo->query($query . " ORDER BY c.class_name ASC");
    } else {
        $query .= " ORDER BY c.class_name ASC";
        $stmt = $pdo->query($query);
    }

    $classes = $stmt->fetchAll();
    echo json_encode(['success' => true, 'classes' => $classes]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching classes: ' . $e->getMessage()]);
}
?>