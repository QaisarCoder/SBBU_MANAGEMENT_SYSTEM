<div class="dashboard-container">
    <!-- Hero Section -->
    <div class="dashboard-hero animate__animated animate__fadeIn">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="university-branding">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRLkc2wqsTveLTVUCFhvk_kKja7MfUQmqoBMQ&s" alt="SBBU Logo" class="hero-logo">
                <div>
                    <span class="admin-badge"><?php echo strtoupper($_SESSION['role']); ?> PORTAL</span>
                    <h1>Welcome, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>!</h1>
                    <p class="location"><i class="fas fa-id-card"></i> <?php echo $_SESSION['user_code'] ?? 'ID-PENDING'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (is_admin() || is_clerk()): ?>
    <!-- Admin/Clerk Stats Grid -->
    <div class="stats-grid-main">
        <div class="stat-card primary animate__animated animate__fadeInUp">
            <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-info">
                <h4>Total Students</h4>
                <h2 class="counter" id="total-students">0</h2>
                <p class="trend positive">Active & Enrolled</p>
            </div>
        </div>
        <div class="stat-card info animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="stat-icon"><i class="fas fa-chalkboard"></i></div>
            <div class="stat-info">
                <h4>Active Classes</h4>
                <h2 class="counter" id="active-classes">0</h2>
                <p class="trend neutral">Currently Running</p>
            </div>
        </div>
        <div class="stat-card success animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h4>Present Today</h4>
                <h2 class="counter" id="present-today">0</h2>
                <p class="trend positive" id="present-percentage">0% Attendance</p>
            </div>
            <div class="progress-bar-mini"><div class="fill" id="present-bar" style="width: 0%"></div></div>
        </div>
    </div>
    <?php elseif (is_teacher()): ?>
    <!-- Teacher Stats Grid -->
    <div class="stats-grid-main">
        <div class="stat-card primary animate__animated animate__fadeInUp">
            <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-info">
                <h4>My Classes</h4>
                <h2 class="counter" id="teacher-classes">0</h2>
                <p class="trend neutral">Assigned Subjects</p>
            </div>
        </div>
        <div class="stat-card success animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="stat-icon"><i class="fas fa-user-check"></i></div>
            <div class="stat-info">
                <h4>Students Marked</h4>
                <h2 class="counter" id="students-marked">0</h2>
                <p class="trend positive">Today's Progress</p>
            </div>
        </div>
    </div>
    <?php elseif (is_student()): ?>
    <!-- Student Stats Grid -->
    <div class="stats-grid-main">
        <div class="stat-card primary animate__animated animate__fadeInUp">
            <div class="stat-icon"><i class="fas fa-percentage"></i></div>
            <div class="stat-info">
                <h4>Attendance</h4>
                <h2 class="counter" id="student-attendance">0</h2>
                <p class="trend positive">% Overall Attendance</p>
            </div>
        </div>
        <div class="stat-card info animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-info">
                <h4>Total Sessions</h4>
                <h2 class="counter" id="total-sessions">0</h2>
                <p class="trend neutral">Classes Conducted</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Content Split -->
    <div class="content-split-layout">
        <div class="left-column">
            <!-- Chart for everyone -->
            <div class="chart-container card-panel animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> Attendance Trends</h3>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="right-column">
             <?php if (!is_student()): ?>
             <!-- Upcoming Classes for Staff/Teachers -->
             <div class="card-panel widget animate__animated animate__fadeInRight" style="animation-delay: 0.5s;">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> <?php echo is_teacher() ? "My Schedule" : "Active Classes"; ?></h3>
                </div>
                <div class="card-body list-view" id="upcoming-classes-list">
                    <div class="skeleton-loader"></div>
                </div>
             </div>
             <?php endif; ?>

             <!-- Notice Board -->
             <div class="card-panel widget notice-board animate__animated animate__fadeInRight" style="animation-delay: 0.6s;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3><i class="fas fa-bullhorn"></i> Notice Board</h3>
                    <?php if (is_admin() || is_teacher()): ?>
                    <button onclick="showAnnouncementModal()" class="action-btn" style="width: auto; padding: 8px 16px; font-size: 0.85rem; border-radius: 20px;">
                        <i class="fas fa-plus"></i> New
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body" id="announcements-list">
                    <div class="skeleton-loader"></div>
                </div>
             </div>

             <!-- Recent Activity (Filtered by role in API) -->
             <div class="card-panel widget animate__animated animate__fadeInRight" style="animation-delay: 0.7s;">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Recent Activity</h3>
                </div>
                <div class="card-body feed-view" id="recent-activities-list">
                     <div class="skeleton-loader"></div>
                </div>
             </div>
        </div>
    </div>
</div>

<!-- Modal logic included in dashboard.php as before -->
<?php if (is_admin() || is_teacher()): ?>
<div id="announcement-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card-panel animate__animated animate__zoomIn" style="width: 95%; max-width: 500px; border-radius: 20px; padding: 2rem; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; color: #2c3e50;"><i class="fas fa-bullhorn" style="color: #8BC9D4;"></i> Post Announcement</h3>
            <button onclick="closeAnnouncementModal()" style="background: none; border: none; cursor: pointer; color: #64748b;"><i class="fas fa-times"></i></button>
        </div>
        <form id="announcement-form">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Title</label>
                <input type="text" name="title" class="form-control" required placeholder="Announcement Title" style="height: 45px; border-radius: 10px;">
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Content</label>
                <textarea name="content" class="form-control" required rows="4" placeholder="Announcement details..." style="border-radius: 10px; padding: 10px;"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeAnnouncementModal()" class="btn-secondary" style="width: auto; padding: 10px 25px; border-radius: 30px;">Cancel</button>
                <button type="submit" class="btn-primary" style="width: auto; padding: 10px 30px; border-radius: 30px;">Post Now</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showAnnouncementModal() {
        document.getElementById('announcement-modal').style.display = 'flex';
    }
    function closeAnnouncementModal() {
        document.getElementById('announcement-modal').style.display = 'none';
    }
    document.getElementById('announcement-form').onsubmit = function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        fetch('api/manage_announcements.php?action=add', { method: 'POST', body: new FormData(this) })
            .then(r => r.json()).then(data => {
                if (data.success) {
                    alert(data.message);
                    closeAnnouncementModal();
                    this.reset();
                    initDashboard();
                } else alert(data.message);
            });
    }
</script>
<?php endif; ?>
