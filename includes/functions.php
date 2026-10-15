<?php
function check_and_alert_low_attendance($student_id, $class_id, $pdo) {
    // ... (existing code)
}

/**
 * Log a user action to the recent_activities table
 */
function log_activity($user_id, $action, $pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO recent_activities (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, $action]);
    } catch (Exception $e) {
        // Silently fail to not block main operation
        error_log("Activity log error: " . $e->getMessage());
    }
}
?>
