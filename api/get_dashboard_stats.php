<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

check_auth();
header('Content-Type: application/json');

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

try {
    $response = [
        'success' => true,
        'role' => $role,
        'stats' => [],
        'activities' => [],
        'announcements' => [],
        'attendance_chart' => ['labels' => [], 'data' => []],
        'upcoming_classes' => []
    ];

    if ($role === 'admin' || $role === 'staff' || $role === 'clerk') {
        // Stats for Admin/Clerk
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM students WHERE status = 'active'");
        $response['stats']['total_students'] = $stmt->fetch()['count'];

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM classes WHERE status = 'active'");
        $response['stats']['active_classes'] = $stmt->fetch()['count'];

        $today = date('Y-m-d');
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM attendance WHERE attendance_date = ? AND status IN ('present', 'late')");
        $stmt->execute([$today]);
        $response['stats']['present_today'] = $stmt->fetch()['count'];

        $response['stats']['present_percentage'] = ($response['stats']['total_students'] > 0)
            ? round(($response['stats']['present_today'] / $response['stats']['total_students']) * 100) : 0;

        // Activities (All for admin)
        $stmt = $pdo->query("SELECT ra.action, ra.timestamp, u.full_name as user FROM recent_activities ra LEFT JOIN users u ON ra.user_id = u.id ORDER BY ra.timestamp DESC LIMIT 5");
        $response['activities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Active Classes list for Admin/Clerk
        $stmt = $pdo->query("SELECT class_name, schedule FROM classes WHERE status = 'active' LIMIT 5");
        $response['upcoming_classes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($role === 'teacher') {
        // Teacher Specific Stats
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE teacher_id = ? AND status = 'active'");
        $stmt->execute([$user_id]);
        $response['stats']['my_classes'] = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE marked_by = ? AND attendance_date = CURDATE()");
        $stmt->execute([$user_id]);
        $response['stats']['students_marked'] = $stmt->fetchColumn();

        // My Upcoming Classes
        $stmt = $pdo->prepare("
            SELECT c.class_name, c.schedule
            FROM classes c
            LEFT JOIN programs p ON c.program_id = p.id
            WHERE (c.teacher_id = ? OR c.substitute_teacher_id = ? OR p.assigned_teacher_id = ?)
            AND c.status = 'active'
            LIMIT 5
        ");
        $stmt->execute([$user_id, $user_id, $user_id]);
        $response['upcoming_classes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // My Recent Activity
        $stmt = $pdo->prepare("SELECT action, timestamp, 'Me' as user FROM recent_activities WHERE user_id = ? ORDER BY timestamp DESC LIMIT 5");
        $stmt->execute([$user_id]);
        $response['activities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($role === 'student') {
        // Find student profile to link
        $stmt = $pdo->prepare("SELECT id FROM students WHERE student_id = (SELECT user_code FROM users WHERE id = ?) OR email = (SELECT email FROM users WHERE id = ?)");
        $stmt->execute([$user_id, $user_id]);
        $student_db_id = $stmt->fetchColumn();

        if ($student_db_id) {
            $stmt = $pdo->prepare("SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status='present' THEN 1 WHEN status='late' THEN 0.5 ELSE 0 END) as attended
                FROM attendance WHERE student_id = ?");
            $stmt->execute([$student_db_id]);
            $att = $stmt->fetch();

            $response['stats']['attendance_percent'] = ($att['total'] > 0) ? round(($att['attended'] / $att['total']) * 100) : 0;
            $response['stats']['total_sessions'] = $att['total'];
        }
    }

    // Fetch Announcements for everyone
    $stmt = $pdo->query("
        SELECT a.title, a.content, a.posted_date, u.full_name as posted_by
        FROM announcements a
        LEFT JOIN users u ON a.posted_by = u.id
        ORDER BY a.posted_date DESC
        LIMIT 5
    ");
    $response['announcements'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mock Chart Data if empty
    if (empty($response['attendance_chart']['labels'])) {
        $response['attendance_chart'] = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            'data' => [65, 72, 58, 85, 77]
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
