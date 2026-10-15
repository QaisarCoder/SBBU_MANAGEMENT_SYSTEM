<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

check_auth();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$class_id = $data['class_id'] ?? null;
$date = $data['date'] ?? date('Y-m-d');
$attendance_data = $data['attendance'] ?? []; // Array of {student_id: 1, status: 'present'}
$session_type = $data['session_type'] ?? 'normal';
$user_lat = $data['lat'] ?? null;
$user_lng = $data['lng'] ?? null;

if (!$class_id || empty($attendance_data)) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

// 0. Holiday check
$holiday_stmt = $pdo->prepare("SELECT description FROM holidays WHERE holiday_date = ?");
$holiday_stmt->execute([$date]);
$holiday = $holiday_stmt->fetchColumn();
if ($holiday) {
    echo json_encode(['success' => false, 'message' => "Today is a holiday: $holiday. Attendance marking is disabled."]);
    exit;
}

// 1. Geo-location restriction
$settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$campus_lat = $settings['campus_lat'] ?? null;
$campus_lng = $settings['campus_lng'] ?? null;
$campus_radius = $settings['campus_radius'] ?? 500; // meters

if ($campus_lat && $campus_lng && $user_lat && $user_lng) {
    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    $distance = calculateDistance($user_lat, $user_lng, $campus_lat, $campus_lng);
    if ($distance > $campus_radius) {
        echo json_encode(['success' => false, 'type' => 'GEOFENCE_ERROR', 'message' => 'ATTENTION: You are outside of the campus boundary. Attendance marking is only permitted within SBBU premises.']);
        exit;
    }
}

// 2. Time-based rules (Prepare class info)
$class_stmt = $pdo->prepare("SELECT start_time FROM classes WHERE id = ?");
$class_stmt->execute([$class_id]);
$class_info = $class_stmt->fetch();
$start_time = $class_info['start_time'] ?? null;

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO attendance (student_id, class_id, attendance_date, status, marked_by, session_type, latitude, longitude)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                           ON DUPLICATE KEY UPDATE status = VALUES(status), marked_by = VALUES(marked_by), session_type = VALUES(session_type), latitude = VALUES(latitude), longitude = VALUES(longitude)");

    foreach ($attendance_data as $record) {
        $status = $record['status'];
        $sid = $record['student_id'];

        // Robust ID Resolution: If student_id is not numeric, try to find the primary key ID
        if (!is_numeric($sid)) {
            $stmt_resolve = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
            $stmt_resolve->execute([$sid]);
            $resolved_id = $stmt_resolve->fetchColumn();
            if ($resolved_id) $sid = $resolved_id;
        }

        // Auto-status logic if it was marked as present but it's late
        if ($status === 'present' && $start_time && $date === date('Y-m-d')) {
            $current_time = date('H:i:s');
            $diff = (strtotime($current_time) - strtotime($start_time)) / 60; // difference in minutes

            if ($diff > 10) {
                $status = 'late';
            }
        }

        $stmt->execute([
            $sid,
            $class_id,
            $date,
            $status,
            $_SESSION['user_id'],
            $session_type,
            $user_lat,
            $user_lng
        ]);

        // 3. Check for low attendance and send alerts
        check_and_alert_low_attendance($sid, $class_id, $pdo);
    }

    $pdo->commit();

    // Log activity
    $class_stmt_name = $pdo->prepare("SELECT class_name FROM classes WHERE id = ?");
    $class_stmt_name->execute([$class_id]);
    $class_name = $class_stmt_name->fetchColumn();
    log_activity($_SESSION['user_id'], "Marked attendance for class: $class_name on $date", $pdo);

    echo json_encode(['success' => true, 'message' => 'Attendance saved successfully']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>