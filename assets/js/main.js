document.addEventListener('DOMContentLoaded', function() {
    initLayout();

    // Check if we are on the dashboard page by looking for a unique element
    if (document.querySelector('.dashboard-container')) {
        initDashboard();
    }
});

function initLayout() {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // Live Date Time
    updateDateTime();
    setInterval(updateDateTime, 1000);
}

function updateDateTime() {
    const display = document.getElementById('current-datetime');
    if (display && window.moment) {
        display.innerHTML = `<i class="far fa-clock"></i> ` + moment().format('MMMM Do YYYY, h:mm:ss a');
    }
}

function syncHolidays() {
    if (!confirm('Sync holidays from Global API?')) return;

    const btn = document.querySelector('a[onclick="syncHolidays()"]');
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';

    fetch('api/manage_holidays.php?action=sync')
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = original;
            alert(data.message);
        })
        .catch(() => {
            btn.innerHTML = original;
            alert('Failed to sync holidays.');
        });
}

function initDashboard() {
    fetchDashboardData();
}

function fetchDashboardData() {
    fetch('api/get_dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStats(data.stats);
                renderChart(data.attendance_chart);
                renderLists(data.activities, data.announcements, data.upcoming_classes);
            }
        })
        .catch(error => console.error('Error fetching dashboard data:', error));
}

function updateStats(stats) {
    if (document.getElementById('total-students')) {
        animateCounter('total-students', stats.total_students || 0);
        animateCounter('active-classes', stats.active_classes || 0);
        animateCounter('present-today', stats.present_today || 0);

        const percentEl = document.getElementById('present-percentage');
        const barEl = document.getElementById('present-bar');
        if (percentEl) percentEl.textContent = `${stats.present_percentage || 0}% Attendance`;
        if (barEl) barEl.style.width = `${stats.present_percentage || 0}%`;
    } else if (document.getElementById('teacher-classes')) {
        animateCounter('teacher-classes', stats.my_classes || 0);
        animateCounter('students-marked', stats.students_marked || 0);
    } else if (document.getElementById('student-attendance')) {
        animateCounter('student-attendance', stats.attendance_percent || 0);
        animateCounter('total-sessions', stats.total_sessions || 0);
    }
}

function animateCounter(id, target) {
    const element = document.getElementById(id);
    if (!element) return;

    const start = 0;
    const duration = 2000;
    const startTime = performance.now();

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        // Ease out quart
        const ease = 1 - Math.pow(1 - progress, 4);

        const current = Math.floor(start + (target - start) * ease);
        element.textContent = current;

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            element.textContent = target; // Ensure final value is exact
        }
    }

    requestAnimationFrame(update);
}

function renderChart(chartData) {
    const ctx = document.getElementById('attendanceChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Present Students',
                data: chartData.data,
                borderColor: '#8BC9D4',
                backgroundColor: 'rgba(139, 201, 212, 0.2)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6aafbc',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5] }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

function renderLists(activities, announcements, classes) {
    // Recent Activities
    const activityList = document.getElementById('recent-activities-list');
    if (activityList) {
        if (activities.length === 0) {
            activityList.innerHTML = '<div class="list-item"><span class="item-subtitle">No recent activity</span></div>';
        } else {
            activityList.innerHTML = activities.map(act => `
                <div class="list-item">
                    <div class="item-icon"><i class="fas fa-history"></i></div>
                    <div class="item-content">
                        <span class="item-title">${act.action}</span>
                        <span class="item-subtitle">${act.user} - ${moment(act.timestamp).fromNow()}</span>
                    </div>
                </div>
            `).join('');
        }
    }

    // Upcoming Classes
    const classList = document.getElementById('upcoming-classes-list');
    if (classList) {
        if (classes.length === 0) {
            classList.innerHTML = '<div class="list-item"><span class="item-subtitle">No active classes</span></div>';
        } else {
            classList.innerHTML = classes.map(cls => `
                <div class="list-item">
                    <div class="item-icon"><i class="fas fa-book-open"></i></div>
                    <div class="item-content">
                        <span class="item-title">${cls.class_name}</span>
                        <span class="item-subtitle">${cls.schedule}</span>
                    </div>
                </div>
            `).join('');
        }
    }

    // Announcements
    const noticeList = document.getElementById('announcements-list');
    if (noticeList) {
        if (announcements.length === 0) {
            noticeList.innerHTML = '<div class="notice-item"><span class="notice-meta">No new announcements</span></div>';
        } else {
            noticeList.innerHTML = announcements.map(note => `
                <div class="notice-item">
                    <span class="notice-title">${note.title}</span>
                    <p style="font-size: 0.85rem; color: #555; margin-bottom: 5px;">${note.content}</p>
                    <div class="notice-meta">
                        <span>${note.posted_by}</span>
                        <span>${moment(note.posted_date).format('MMM D')}</span>
                    </div>
                </div>
            `).join('');
        }
    }
}
