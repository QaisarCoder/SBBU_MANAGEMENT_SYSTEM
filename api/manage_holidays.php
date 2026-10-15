<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";
require_once "../includes/auth.php";

check_auth(['admin']);

$action = $_GET['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $pdo->query("SELECT * FROM holidays ORDER BY holiday_date ASC");
        echo json_encode(['success' => true, 'holidays' => $stmt->fetchAll()]);
    }
    elseif ($action === 'sync') {
        $year = date('Y');
        $country = 'PK';
        $url = "https://date.nager.at/api/v3/PublicHolidays/$year/$country";

        // Use CURL for better compatibility and to bypass SSL issues on local dev
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL check for local dev
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);

        $holidays = json_decode($response, true);

        // Fallback for common holidays if API fails
        if (!$holidays) {
            $holidays = [
                ['date' => "$year-02-05", 'name' => 'Kashmir Day'],
                ['date' => "$year-03-23", 'name' => 'Pakistan Day'],
                ['date' => "$year-05-01", 'name' => 'Labour Day'],
                ['date' => "$year-08-14", 'name' => 'Independence Day'],
                ['date' => "$year-11-09", 'name' => 'Iqbal Day'],
                ['date' => "$year-12-25", 'name' => 'Quaid-e-Azam Day']
            ];
            $message = 'Holidays loaded from Secure Backup.';
        } else {
            $message = 'Holidays successfully synced from Global API.';
        }

        if ($holidays) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO holidays (holiday_date, description) VALUES (?, ?)");
            foreach ($holidays as $h) {
                $stmt->execute([$h['date'], $h['name']]);
            }
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not connect to Holiday API']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
