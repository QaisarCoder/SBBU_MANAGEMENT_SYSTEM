<?php
header("Content-Type: application/json");
require_once "../includes/config.php";
require_once "../includes/db.php";

$otpInput = $_POST["otp"] ?? "";

if (empty($otpInput)) {
    echo json_encode(["success" => false, "message" => "Please enter the OTP."]);
    exit;
}

try {
    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["success" => false, "message" => "Session expired."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION["user_id"]]);
    $user = $stmt->fetch();

    if ($user && $user["otp_code"] === $otpInput && strtotime($user["otp_expiry"]) > time()) {

        // 2FA Success
        unset($_SESSION["2fa_pending"]);

        // Clear OTP
        $update = $pdo->prepare("UPDATE users SET otp_code = NULL, otp_expiry = NULL WHERE id = ?");
        $update->execute([$user["id"]]);

        // Track Session
        $token = bin2hex(random_bytes(32));
        $ip = get_client_ip();
        $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user["id"], $token, $ip, $_SERVER['HTTP_USER_AGENT']]);
        $_SESSION["session_token"] = $token;

        echo json_encode(["success" => true, "redirect" => "index.php"]);
        exit;
    }

    echo json_encode(["success" => false, "message" => "Invalid or expired OTP."]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
