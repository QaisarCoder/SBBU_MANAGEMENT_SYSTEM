<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";

check_auth(['student']);

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User account not found.']);
        exit;
    }

    // Find student record using user_code (which is our Student ID)
    $student_id_code = $user['user_code'];
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$student_id_code]);
    $student = $stmt->fetch();

    if (!$student) {
        // Fallback: Try email if user_code didn't work (for older accounts)
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$user['email']]);
        $student = $stmt->fetch();
    }

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student profile not linked. Please contact admin to set your Student ID correctly.']);
        exit;
    }

    if ($action === 'stats') {
        $stmt = $pdo->prepare("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = 'present' THEN 1 WHEN status = 'late' THEN 0.5 ELSE 0 END) as weighted_attended
            FROM attendance
            WHERE student_id = ?
        ");
        $stmt->execute([$student['id']]);
        $stats = $stmt->fetch();
        $percent = $stats['total'] > 0 ? round(($stats['weighted_attended'] / $stats['total']) * 100, 1) : 0;

        // Fetch program name
        $stmt = $pdo->prepare("SELECT program_name FROM programs WHERE id = ?");
        $stmt->execute([$student['program_id']]);
        $program_name = $stmt->fetchColumn();

        echo json_encode(['success' => true, 'stats' => [
            'percentage' => $percent,
            'present' => $stats['present'],
            'late' => $stats['late'],
            'absent' => $stats['absent'],
            'program_name' => $program_name
        ]]);
    }
    elseif ($action === 'subject_stats') {
        $stmt = $pdo->prepare("
            SELECT
                COALESCE(s.subject_name, c.class_name) as name,
                COUNT(*) as total,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.status = 'present' THEN 1 WHEN a.status = 'late' THEN 0.5 ELSE 0 END) as weighted_attended
            FROM attendance a
            JOIN classes c ON a.class_id = c.id
            LEFT JOIN subjects s ON c.subject_id = s.id
            WHERE a.student_id = ?
            GROUP BY name
        ");
        $stmt->execute([$student['id']]);
        $rows = $stmt->fetchAll();

        $subject_stats = array_map(function($row) {
            $row['percentage'] = $row['total'] > 0 ? round(($row['weighted_attended'] / $row['total']) * 100, 1) : 0;
            return $row;
        }, $rows);

        echo json_encode(['success' => true, 'subjects' => $subject_stats]);
    }
    elseif ($action === 'attendance') {
        $stmt = $pdo->prepare("
            SELECT a.*, c.class_code, s.subject_name
            FROM attendance a
            JOIN classes c ON a.class_id = c.id
            LEFT JOIN subjects s ON c.subject_id = s.id
            WHERE a.student_id = ?
            ORDER BY a.attendance_date DESC LIMIT 20
        ");
        $stmt->execute([$student['id']]);
        echo json_encode(['success' => true, 'attendance' => $stmt->fetchAll()]);
    }
    elseif ($action === 'badges') {
        $stmt = $pdo->prepare("SELECT * FROM badges WHERE student_id = ?");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true, 'badges' => $stmt->fetchAll()]);
    }
    elseif ($action === 'schedule') {
        // Simple mock schedule based on student's program and semester
        $stmt = $pdo->prepare("
            SELECT c.*, s.subject_name
            FROM classes c
            LEFT JOIN subjects s ON c.subject_id = s.id
            WHERE c.program_id = ? AND c.semester = ?
        ");
        $stmt->execute([$student['program_id'], $student['semester']]);
        echo json_encode(['success' => true, 'schedule' => $stmt->fetchAll()]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
