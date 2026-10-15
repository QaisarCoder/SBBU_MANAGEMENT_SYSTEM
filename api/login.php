<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";

$usernameInput = $_POST["username"] ?? "";
$passwordInput = $_POST["password"] ?? "";

if (empty($usernameInput) || empty($passwordInput)) {
    echo json_encode(["success" => false, "message" => "Please enter username and password."]);
    exit;
}

try {
    // 1. IP Whitelisting check for Admin
    $ip = get_client_ip();
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'allowed_ip_range'");
    $allowed_range = $stmt->fetchColumn();

    // We only enforce IP check if a range is configured
    if (!empty($allowed_range) && !ip_in_range($ip, $allowed_range)) {
        echo json_encode(["success" => false, "message" => "Access denied from this IP address ($ip)."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$usernameInput, $usernameInput]);
    $user = $stmt->fetch();

    if ($user && password_verify($passwordInput, $user["password"])) {

        // 2. Setup Base Session
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["user_code"] = $user["user_code"];

        // 3. Handle 2FA for Admin (DISABLED for local dev)
        /*
        if ($user["role"] === 'admin') {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $update = $pdo->prepare("UPDATE users SET otp_code = ?, otp_expiry = ?, last_ip = ? WHERE id = ?");
            $update->execute([$otp, $expiry, $ip, $user["id"]]);

            $_SESSION["2fa_pending"] = true;

            echo json_encode(["success" => true, "redirect" => "verify_2fa.php", "message" => "OTP sent to your email."]);
            exit;
        }
        */

        // 4. Track Session for all users
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user["id"], $token, $ip, $_SERVER['HTTP_USER_AGENT']]);
        $_SESSION["session_token"] = $token;

        echo json_encode(["success" => true, "redirect" => "index.php"]);
        exit;
    }

    echo json_encode(["success" => false, "message" => "Invalid credentials."]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
