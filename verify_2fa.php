<?php
session_start();
if (!isset($_SESSION['2fa_pending'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - SBBU</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="login-body">
    <div class="login-container glass-card animate__animated animate__fadeIn">
        <h2 style="margin-bottom: 1rem;">Two-Factor Auth</h2>
        <p style="color: #666; margin-bottom: 2rem; font-size: 0.9rem;">
            Please enter the 6-digit code sent to your email.
        </p>
        <form id="2faForm">
            <div class="form-group">
                <input type="text" id="otp" name="otp" class="form-control" placeholder="000000" maxlength="6" required style="text-align: center; font-size: 1.5rem; letter-spacing: 5px;">
            </div>
            <button type="submit" class="btn-primary">Verify Code</button>
            <p id="error-message" style="color: #c53030; margin-top: 10px; display: none;"></p>
        </form>
        <div style="margin-top: 1.5rem;">
            <a href="api/logout.php" style="color: #777; font-size: 0.85rem;">Cancel & Logout</a>
        </div>
    </div>

    <script>
        document.getElementById('2faForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const errorMsg = document.getElementById('error-message');
            const btn = this.querySelector('button');

            btn.disabled = true;
            btn.textContent = 'Verifying...';

            try {
                const response = await fetch('api/verify_2fa.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    errorMsg.textContent = data.message;
                    errorMsg.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Verify Code';
                }
            } catch (error) {
                errorMsg.textContent = 'An error occurred. Please try again.';
                errorMsg.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Verify Code';
            }
        });
    </script>
</body>
</html>
