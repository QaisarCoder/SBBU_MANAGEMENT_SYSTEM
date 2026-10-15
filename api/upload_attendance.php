<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

check_auth();
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['attendance_file']) || $_FILES['attendance_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['attendance_file'];

// Validate file type
$allowedTypes = ['text/csv', 'text/plain', 'application/csv', 'text/comma-separated-values'];
if (!in_array($file['type'], $allowedTypes) && pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload a CSV file.']);
    exit;
}

// Read CSV file
if (($handle = fopen($file['tmp_name'], "r")) === FALSE) {
    echo json_encode(['success' => false, 'message' => 'Error opening file']);
    exit;
}

$headers = fgetcsv($handle);
if ($headers === FALSE) {
    fclose($handle);
    echo json_encode(['success' => false, 'message' => 'Empty CSV file']);
    exit;
}

// Expected columns
$expectedHeaders = ['class_code', 'student_id', 'attendance_date', 'status', 'session_type'];

// Validate headers
$headerDiff = array_diff($expectedHeaders, $headers);
if (!empty($headerDiff)) {
    fclose($handle);
    echo json_encode(['success' => false, 'message' => 'Invalid CSV format. Missing columns: ' . implode(', ', $headerDiff)]);
    exit;
}

// Map column indices
$colMap = array_flip($headers);

// Initialize variables for processing
$attendanceRecords = [];
$errors = [];
$rowNumber = 1; // Start at 1 for header row

// Process each row
while (($data = fgetcsv($handle)) !== FALSE) {
    $rowNumber++;

    // Skip empty rows
    if (empty($data) || count($data) < count($expectedHeaders)) {
        continue;
    }

    // Extract values using column map
    $classCode = trim($data[$colMap['class_code']] ?? '');
    $studentIdStr = trim($data[$colMap['student_id']] ?? '');
    $attendanceDate = trim($data[$colMap['attendance_date']] ?? '');
    $status = trim(strtolower($data[$colMap['status']] ?? ''));
    $sessionType = trim($data[$colMap['session_type']] ?? '');

    // Validate required fields
    if (empty($classCode)) {
        $errors[] = "Row $rowNumber: Class code is required";
        continue;
    }

    if (empty($studentIdStr)) {
        $errors[] = "Row $rowNumber: Student ID is required";
        continue;
    }

    if (empty($attendanceDate)) {
        $errors[] = "Row $rowNumber: Attendance date is required";
        continue;
    }

    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $attendanceDate)) {
        $errors[] = "Row $rowNumber: Invalid date format. Use YYYY-MM-DD";
        continue;
    }

    // Validate status
    $validStatuses = ['present', 'absent', 'late'];
    if (!in_array($status, $validStatuses)) {
        $errors[] = "Row $rowNumber: Invalid status. Must be one of: present, absent, late";
        continue;
    }

    // Validate session type (optional but recommended)
    if (empty($sessionType)) {
        $sessionType = 'regular'; // Default session type
    }

    // Look up class by class_code
    $classStmt = $pdo->prepare("SELECT id FROM classes WHERE class_code = ?");
    $classStmt->execute([$classCode]);
    $class = $classStmt->fetch();

    if (!$class) {
        $errors[] = "Row $rowNumber: Class not found with code '$classCode'";
        continue;
    }

    // Look up student by student_id string
    $studentStmt = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
    $studentStmt->execute([$studentIdStr]);
    $student = $studentStmt->fetch();

    if (!$student) {
        $errors[] = "Row $rowNumber: Student not found with ID '$studentIdStr'";
        continue;
    }

    // Add valid record to process
    $attendanceRecords[] = [
        'student_id' => $student['id'],
        'class_id' => $class['id'],
        'attendance_date' => $attendanceDate,
        'status' => $status,
        'session_type' => $sessionType,
        'marked_by' => $_SESSION['user_id']
    ];
}

fclose($handle);

// If there are validation errors, return them
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => 'CSV validation failed',
        'errors' => $errors
    ]);
    exit;
}

// If no valid records, return error
if (empty($attendanceRecords)) {
    echo json_encode(['success' => false, 'message' => 'No valid records found in CSV']);
    exit;
}

// Insert attendance records in a transaction
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO attendance (student_id, class_id, attendance_date, status, session_type, marked_by)
                           VALUES (:student_id, :class_id, :attendance_date, :status, :session_type, :marked_by)
                           ON DUPLICATE KEY UPDATE
                               status = VALUES(status),
                               session_type = VALUES(session_type),
                               marked_by = VALUES(marked_by)");

    foreach ($attendanceRecords as $record) {
        $stmt->execute($record);
        // 3. Check for low attendance and send alerts
        check_and_alert_low_attendance($record['student_id'], $record['class_id'], $pdo);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Attendance uploaded successfully. Processed ' . count($attendanceRecords) . ' records.',
        'processed_count' => count($attendanceRecords)
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error saving attendance: ' . $e->getMessage()]);
}
?>