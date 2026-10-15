<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";

check_auth(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$students = json_decode($_POST['students'] ?? '[]', true);
if (empty($students)) {
    echo json_encode(['success' => false, 'message' => 'No student data provided']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Cache programs and batches for lookup
    $programs_lookup = $pdo->query("SELECT id, program_code, program_name FROM programs")->fetchAll(PDO::FETCH_ASSOC);
    $batches_lookup = $pdo->query("SELECT id, batch_name FROM batches")->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, email, program_id, batch_id, semester, enrollment_date, status) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), 'active') ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), program_id = VALUES(program_id), batch_id = VALUES(batch_id), semester = VALUES(semester), status = 'active'");

    $successCount = 0;
    foreach ($students as $s) {
        $student_id = trim($s['student_id'] ?? '');
        $full_name = trim($s['full_name'] ?? '');
        $email = trim($s['email'] ?? '');
        $semester = (!empty($s['semester']) && is_numeric($s['semester'])) ? (int)$s['semester'] : 1;
        $target_class_id = $s['target_class_id'] ?? null;

        // Resolve Program and Batch
        $program_id = null;
        $batch_id = null;

        if ($target_class_id) {
            // Override everything with class info
            $stmt_class = $pdo->prepare("SELECT program_id, batch_id, semester FROM classes WHERE id = ?");
            $stmt_class->execute([$target_class_id]);
            $class_info = $stmt_class->fetch();
            if ($class_info) {
                $program_id = $class_info['program_id'];
                $batch_id = $class_info['batch_id'];
                $semester = $class_info['semester'];
            }
        } else {
            // Resolve Program ID by input
            $prog_input = trim($s['program_id'] ?? '');
            if (is_numeric($prog_input)) {
                $program_id = (int)$prog_input;
            } elseif (!empty($prog_input)) {
                foreach ($programs_lookup as $p) {
                    if (strcasecmp($p['program_code'], $prog_input) === 0 || strcasecmp($p['program_name'], $prog_input) === 0) {
                        $program_id = $p['id'];
                        break;
                    }
                }
            }

            // Resolve Batch ID by input
            $batch_input = trim($s['batch_id'] ?? '');
            if (is_numeric($batch_input)) {
                $batch_id = (int)$batch_input;
            } elseif (!empty($batch_input)) {
                foreach ($batches_lookup as $b) {
                    if (strcasecmp($b['batch_name'], $batch_input) === 0) {
                        $batch_id = $b['id'];
                        break;
                    }
                }
            }
        }

        if ($student_id && $full_name) {
            $stmt->execute([$student_id, $full_name, $email, $program_id, $batch_id, $semester]);
            $successCount++;
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => "Successfully processed $successCount students."]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
