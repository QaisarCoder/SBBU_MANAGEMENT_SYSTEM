<?php
require_once "includes/config.php";
require_once "includes/db.php";
require_once "includes/auth.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$page = $_GET["page"] ?? "dashboard";
$allowed_pages = ["dashboard", "attendance", "students", "teachers", "classes", "reports", "programs", "subjects", "batches", "sessions", "disputes", "student_portal", "users"];
if (!in_array($page, $allowed_pages)) { $page = "dashboard"; }

// Role-based page access restriction
$role_restrictions = [
    'attendance' => ['admin', 'teacher'],
    'students' => ['admin', 'staff', 'clerk', 'teacher'],
    'teachers' => ['admin'],
    'classes' => ['admin', 'staff', 'clerk'],
    'programs' => ['admin', 'staff', 'clerk'],
    'subjects' => ['admin', 'staff', 'clerk'],
    'batches' => ['admin', 'staff', 'clerk'],
    'sessions' => ['admin'],
    'users' => ['admin'],
    'reports' => ['admin', 'teacher', 'staff', 'clerk'],
    'student_portal' => ['student'],
    'disputes' => ['admin']
];

if (isset($role_restrictions[$page])) {
    $current_role = $_SESSION['role'] === 'staff' ? 'staff' : $_SESSION['role'];
    if (!in_array($current_role, $role_restrictions[$page])) {
        $page = 'dashboard';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($page); ?> - SBBU Attendance System</title>

    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-logo" style="text-align: left; display: flex; align-items: center; gap: 1rem; padding: 1.5rem;">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRLkc2wqsTveLTVUCFhvk_kKja7MfUQmqoBMQ&s" alt="SBBU Logo" style="width: 85px; height: 85px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2); object-fit: cover; flex-shrink: 0;">
                <h3 style="font-size: 0.95rem; color: white; margin: 0; line-height: 1.3;"><?php echo APP_NAME; ?></h3>
            </div>
            <nav>
                <a href="index.php?page=dashboard" class="nav-link <?php echo $page === "dashboard" ? "active" : ""; ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>

                <?php if (is_admin() || is_teacher()): ?>
                <a href="index.php?page=attendance" class="nav-link <?php echo $page === "attendance" ? "active" : ""; ?>">
                    <i class="fas fa-user-check"></i> Mark Attendance
                </a>
                <?php endif; ?>

                <?php if (is_admin() || is_clerk() || is_teacher()): ?>
                <?php if (is_admin()): ?>
                <a href="index.php?page=users" class="nav-link <?php echo $page === "users" ? "active" : ""; ?>">
                    <i class="fas fa-user-shield"></i> Manage Users
                </a>
                <a href="index.php?page=teachers" class="nav-link <?php echo $page === "teachers" ? "active" : ""; ?>">
                    <i class="fas fa-chalkboard-teacher"></i> Manage Teachers
                </a>
                <?php endif; ?>

                <?php if (is_admin() || is_clerk()): ?>
                <a href="index.php?page=classes" class="nav-link <?php echo $page === "classes" ? "active" : ""; ?>">
                    <i class="fas fa-chalkboard-teacher"></i> Manage Classes
                </a>
                <?php endif; ?>

                <a href="index.php?page=students" class="nav-link <?php echo $page === "students" ? "active" : ""; ?>">
                    <i class="fas fa-user-graduate"></i> Manage Students
                </a>

                <?php if (is_admin() || is_clerk()): ?>
                <a href="index.php?page=programs" class="nav-link <?php echo $page === "programs" ? "active" : ""; ?>">
                    <i class="fas fa-book"></i> Manage Programs
                </a>
                <a href="index.php?page=subjects" class="nav-link <?php echo $page === "subjects" ? "active" : ""; ?>">
                    <i class="fas fa-book-open"></i> Manage Subjects
                </a>
                <a href="index.php?page=batches" class="nav-link <?php echo $page === "batches" ? "active" : ""; ?>">
                    <i class="fas fa-users-class"></i> Manage Batches
                </a>
                <?php if (is_admin()): ?>
                <a href="index.php?page=sessions" class="nav-link <?php echo $page === "sessions" ? "active" : ""; ?>">
                    <i class="fas fa-key"></i> Active Sessions
                </a>
                <a href="index.php?page=disputes" class="nav-link <?php echo $page === "disputes" ? "active" : ""; ?>">
                    <i class="fas fa-exclamation-triangle"></i> Attendance Disputes
                </a>
                <?php endif; ?>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (is_clerk() || is_admin()): ?>
                <a href="index.php?page=reports" class="nav-link <?php echo $page === "reports" ? "active" : ""; ?>">
                    <i class="fas fa-file-alt"></i> Reports & Exports
                </a>
                <?php endif; ?>

                <?php if (is_student()): ?>
                <a href="index.php?page=student_portal" class="nav-link <?php echo $page === "student_portal" ? "active" : ""; ?>">
                    <i class="fas fa-user-circle"></i> My Attendance
                </a>
                <?php endif; ?>

                <a href="api/logout.php" class="nav-link" style="color: #ff6b6b; margin-top: 2rem;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button id="sidebar-toggle" class="icon-btn"><i class="fas fa-bars"></i></button>
                    <div style="margin-left: 15px;">
                        <h2 style="margin:0; font-size: 1.4rem;"><?php echo ucfirst(str_replace('_', ' ', $page)); ?></h2>
                        <span style="font-size: 0.75rem; color: #666; font-weight: 600; text-transform: uppercase;"><?php echo $_SESSION['user_code'] ?? 'ID-PENDING'; ?></span>
                    </div>
                </div>

                <div class="header-right">
                    <!-- Face AI Status Indicator -->
                    <div class="ai-status-wrapper" style="display: flex; align-items: center; gap: 8px; background: #f8f9fa; padding: 6px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                        <span>Face AI:</span>
                        <div id="face-ai-status" class="status-dot" style="width: 10px; height: 10px; border-radius: 50%; background-color: #f1c40f;" title="Initializing..."></div>
                    </div>

                    <div class="datetime-display" id="current-datetime">
                        <i class="far fa-clock"></i> <span>Loading...</span>
                    </div>

                    <div class="user-profile-dropdown">
                        <div class="profile-trigger">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION["full_name"]); ?>&background=random" alt="User" class="avatar">
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($_SESSION["full_name"]); ?></span>
                                <span class="user-role"><?php echo ucfirst($_SESSION['role']); ?></span>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="dropdown-menu">
                            <a href="#"><i class="fas fa-user-cog"></i> Profile Settings</a>
                            <a href="#"><i class="fas fa-shield-alt"></i> Security</a>
                            <div class="divider"></div>
                            <a href="api/logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <?php
                $file = "pages/$page.php";
                if (file_exists($file)) { include $file; }
                else { echo "<div class='error-page'><h2>404</h2><p>Page not found.</p></div>"; }
                ?>
            </div>
        </main>
    </div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/face-logic.js"></script>
</body>
</html>
