<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SBBU Attendance System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="login-body">
    <div class="login-container glass-card">
        <img src="https://encrypted-tbn0.gstatic.com/imagesq=tbn:ANd9GcRLkc2wqsTveLTVUCFhvk_kKja7MfUQmqoBMQ&s"
            alt="SBBU Logo" class="login-logo">
        <h2 style="margin-bottom: 2rem;">Staff Login</h2>
        <form id="loginForm">
            <div class="form-group">
                <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password"
                    required>
            </div>
            <button type="submit" class="btn-primary">Sign In</button>
            <p id="error-message" style="color: #c53030; margin-top: 10px; display: none;"></p>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const errorMsg = document.getElementById('error-message');

            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    errorMsg.textContent = data.message;
                    errorMsg.style.display = 'block';
                }
            } catch (error) {
                errorMsg.textContent = 'An error occurred. Please try again.';
                errorMsg.style.display = 'block';
            }
        });
    </script>
</body>

</html>