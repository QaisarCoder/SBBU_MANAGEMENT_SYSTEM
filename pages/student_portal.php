<?php
// Student Portal - Self Service
$user_id = $_SESSION['user_id'];
?>
<style>
    .student-hero {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        padding: 2.5rem;
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }
    .student-hero::after {
        content: '';
        position: absolute;
        right: -50px;
        top: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: var(--radius-full);
    }
    .student-info-main h1 { color: #ffffff; margin: 0; font-size: 2rem; }
    .student-info-main p { color: #ffffff; opacity: 0.95; margin: 5px 0 0; font-size: 1.1rem; }

    .attendance-circle-container {
        position: relative;
        width: 120px;
        height: 120px;
    }
    .attendance-circle-container svg {
        transform: rotate(-90deg);
        width: 120px;
        height: 120px;
    }
    .attendance-circle-bg {
        fill: none;
        stroke: rgba(255,255,255,0.3);
        stroke-width: 8;
    }
    .attendance-circle-progress {
        fill: none;
        stroke: white;
        stroke-width: 8;
        stroke-linecap: round;
        stroke-dasharray: 314;
        stroke-dashoffset: 314;
        transition: stroke-dashoffset 1s ease-out;
    }
    .attendance-percent-text {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.5rem;
        color: #ffffff;
    }

    .stat-cards-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .mini-stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.3s;
        border-left: 4px solid var(--primary-color);
    }
    .mini-stat-card:hover { transform: translateY(-5px); }
    .mini-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .schedule-card {
        background: white;
        border-radius: var(--radius-md);
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid var(--primary-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s;
    }
    .schedule-card:hover { box-shadow: var(--shadow-md); transform: scale(1.02); }
    .schedule-time { color: #4a5568; font-size: 0.85rem; font-weight: 600; }

    .badge-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 1.0rem;
    }
    .badge-item {
        text-align: center;
        transition: all 0.3s;
    }
    .badge-item:hover { transform: translateY(-5px); }
    .badge-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-full);
        background: #fff8e1;
        border: 2px solid #ffca28;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 1.5rem;
        color: #ffa000;
        box-shadow: 0 4px 10px rgba(255, 202, 40, 0.2);
    }
    .portal-card-panel {
        background: white;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        border-left: 4px solid var(--primary-color);
        margin-bottom: 1.5rem;
    }
</style>

<div class="animate__animated animate__fadeIn">
    <!-- Hero Section -->
    <div class="student-hero">
        <div class="student-info-main">
            <span class="admin-badge" style="background: rgba(255,255,255,0.25); color: #ffffff; border: none; font-weight: 700;">Student Profile</span>
            <h1>Welcome back, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>!</h1>
            <p><i class="fas fa-university"></i> Shaheed Benazir Bhutto University</p>
            <div style="margin-top: 15px; display: flex; gap: 15px;">
                <span style="font-size: 0.9rem; background: rgba(0,0,0,0.2); color: #ffffff; padding: 4px 12px; border-radius: var(--radius-xl); font-weight: 600;"><i class="fas fa-id-card"></i> <?php echo $_SESSION['user_code']; ?></span>
                <span id="student-program-badge" style="font-size: 0.9rem; background: rgba(0,0,0,0.2); color: #ffffff; padding: 4px 12px; border-radius: var(--radius-xl); font-weight: 600;"><i class="fas fa-graduation-cap"></i> Loading...</span>
            </div>
        </div>
        <div class="attendance-circle-container">
            <svg>
                <circle class="attendance-circle-bg" cx="60" cy="60" r="50"></circle>
                <circle id="attendance-progress-bar" class="attendance-circle-progress" cx="60" cy="60" r="50"></circle>
            </svg>
            <div class="attendance-percent-text"><span id="student-overall-percent" style="color: #ffffff;">0%</span></div>
        </div>
    </div>

    <!-- Mini Stats -->
    <div class="stat-cards-row">
        <div class="mini-stat-card">
            <div class="mini-stat-icon" style="background: #e1f5fe; color: #03a9f4;"><i class="fas fa-calendar-check"></i></div>
            <div>
                <div style="font-size: 0.8rem; color: #2d3748; font-weight: 700;">PRESENT DAYS</div>
                <div id="stat-present-count" style="font-size: 1.4rem; font-weight: 700; color: #2d3748;">0</div>
            </div>
        </div>
        <div class="mini-stat-card">
            <div class="mini-stat-icon" style="background: #fff3e0; color: #ff9800;"><i class="fas fa-clock"></i></div>
            <div>
                <div style="font-size: 0.8rem; color: #2d3748; font-weight: 700;">LATE ARRIVALS</div>
                <div id="stat-late-count" style="font-size: 1.4rem; font-weight: 700; color: #2d3748;">0</div>
            </div>
        </div>
        <div class="mini-stat-card">
            <div class="mini-stat-icon" style="background: #ffebee; color: #f44336;"><i class="fas fa-user-times"></i></div>
            <div>
                <div style="font-size: 0.8rem; color: #2d3748; font-weight: 700;">ABSENT DAYS</div>
                <div id="stat-absent-count" style="font-size: 1.4rem; font-weight: 700; color: #2d3748;">0</div>
            </div>
        </div>
    </div>

    <!-- Subject Wise Stats -->
    <div class="portal-card-panel animate__animated animate__fadeInUp" style="border-left-color: #2ecc71;">
        <div class="card-header">
            <h2><i class="fas fa-chart-bar"></i> Subject Wise Performance</h2>
        </div>
        <div class="card-body">
            <div id="subject-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <!-- Loaded via JS -->
            </div>
        </div>
    </div>

    <div class="content-split-layout">
        <!-- Left: Attendance History -->
        <div class="left-column">
            <div class="portal-card-panel">
                <div class="card-header">
                    <h3><i class="fas fa-list-alt"></i> Attendance History</h3>
                    <button class="action-btn" onclick="showDisputeModal()" style="font-size: 0.8rem; padding: 6px 16px; border-radius: var(--radius-sm);">
                        <i class="fas fa-exclamation-triangle"></i> Report Issue
                    </button>
                </div>
                <div class="table-container" style="box-shadow: none;">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding: 12px 16px;">Date</th>
                                <th style="padding: 12px 16px;">Subject</th>
                                <th style="text-align: center; padding: 12px 16px;">Status</th>
                                <th style="text-align: right; padding: 12px 16px;">Options</th>
                            </tr>
                        </thead>
                        <tbody id="student-attendance-body">
                            <!-- Loaded via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Schedule & Badges -->
        <div class="right-column">
            <!-- Today's Schedule -->
            <div class="portal-card-panel">
                <div class="card-header">
                    <h3><i class="far fa-clock"></i> Today's Classes</h3>
                </div>
                <div class="card-body" id="student-schedule-list">
                    <div class="skeleton-loader"></div>
                </div>
            </div>

            <!-- University Notice Board (NEW) -->
            <div class="portal-card-panel" style="border-left-color: #3498db;">
                <div class="card-header">
                    <h3><i class="fas fa-bullhorn"></i> Notice Board</h3>
                </div>
                <div class="card-body" id="announcements-feed" tabindex="0" role="region" aria-label="Notice Board" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                    <div class="skeleton-loader"></div>
                </div>
            </div>

            <!-- Achievement Badges -->
            <div class="portal-card-panel" style="border-left-color: #f1c40f;">
                <div class="card-header">
                    <h3><i class="fas fa-medal"></i> Achievements</h3>
                </div>
                <div class="card-body">
                    <div id="badges-container" class="badge-grid">
                        <!-- Loaded via JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dispute Modal -->
<div id="dispute-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="card-panel animate__animated animate__zoomIn" style="width: 95%; max-width: 500px; padding: 2rem; border-radius: var(--radius-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0;"><i class="fas fa-flag" style="color: var(--accent-color);"></i> Raise Dispute</h3>
            <button onclick="closeDisputeModal()" style="background: none; border: none; cursor: pointer; color: #666; font-size: 1.2rem;">&times;</button>
        </div>
        <form id="dispute-form">
            <div class="form-group">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Select Record</label>
                <select name="attendance_id" id="absent-records-select" class="form-control" required style="border-radius: var(--radius-sm);">
                    <option value="">Choose absent record...</option>
                </select>
            </div>
            <div class="form-group">
                <label style="font-weight: 600; display: block; margin-bottom: 8px;">Reason</label>
                <textarea name="reason" class="form-control" rows="4" placeholder="Briefly explain why this record is incorrect..." required style="border-radius: var(--radius-sm);"></textarea>
            </div>
            <div style="margin-top: 1.5rem; text-align: right;">
                <button type="submit" class="btn-primary" style="border-radius: var(--radius-xl); padding: 12px 30px;">Submit for Review</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadStudentPortalData();
    loadAnnouncements();

    document.getElementById('dispute-form').onsubmit = function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this).entries());
        fetch('api/manage_disputes.php?action=create', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        }).then(r => r.json()).then(res => {
            if (res.success) {
                alert('Dispute submitted successfully.');
                closeDisputeModal();
            } else alert('Error: ' + res.message);
        });
    };
});

function loadStudentPortalData() {
    // Stats & Profile info
    fetch('api/get_student_portal_data.php?action=stats')
        .then(r => r.json()).then(data => {
            if (data.success) {
                const percent = data.stats.percentage || 0;
                document.getElementById('student-overall-percent').textContent = percent + '%';

                // Animate progress circle
                const circle = document.getElementById('attendance-progress-bar');
                const offset = 314 - (314 * percent / 100);
                circle.style.strokeDashoffset = offset;

                document.getElementById('stat-present-count').textContent = data.stats.present || 0;
                document.getElementById('stat-late-count').textContent = data.stats.late || 0;
                document.getElementById('stat-absent-count').textContent = data.stats.absent || 0;

                if (data.stats.program_name) {
                    document.getElementById('student-program-badge').innerHTML = `<i class="fas fa-graduation-cap"></i> ${data.stats.program_name}`;
                }
            }
        });

    // Subject Wise Stats
    fetch('api/get_student_portal_data.php?action=subject_stats')
        .then(r => r.json()).then(data => {
            if (data.success) {
                renderSubjectStats(data.subjects);
            }
        });

    // Attendance History
    fetch('api/get_student_portal_data.php?action=attendance')
        .then(r => r.json()).then(data => {
            const tbody = document.getElementById('student-attendance-body');
            const select = document.getElementById('absent-records-select');
            tbody.innerHTML = '';
            select.innerHTML = '<option value="">Choose record...</option>';

            if (data.attendance.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 2rem; color: #4a5568;">No records found.</td></tr>';
            }

            data.attendance.forEach(rec => {
                const tr = document.createElement('tr');
                let statusClass = 'status-' + rec.status;
                const subjectDisplay = rec.subject_name || rec.class_code || 'Unknown';

                tr.innerHTML = `
                    <td style="font-weight: 500; padding: 12px 16px;">${moment(rec.attendance_date).format('MMM D, YYYY')}</td>
                    <td style="padding: 12px 16px;"><strong>${subjectDisplay}</strong></td>
                    <td style="text-align: center; padding: 12px 16px;"><span class="status-badge ${statusClass}">${rec.status.toUpperCase()}</span></td>
                    <td style="text-align: right; padding: 12px 16px;">
                        <button onclick="openDisputeFor(${rec.id})" class="action-btn" title="Report Issue" aria-label="Report Issue for ${subjectDisplay}" style="display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: var(--radius-sm); color: #2d3748; font-size: 0.8rem; font-weight: 600; cursor: pointer;"><i class="fas fa-flag" style="color: var(--accent-color);"></i> Report</button>
                    </td>
                `;
                tbody.appendChild(tr);

                // Add ALL records to the dispute dropdown so the student sees them
                const opt = document.createElement('option');
                opt.value = rec.id;
                opt.textContent = `${moment(rec.attendance_date).format('MMM D')} - ${subjectDisplay} (${rec.status.toUpperCase()})`;
                select.appendChild(opt);
            });
        });

    // Badges
    fetch('api/get_student_portal_data.php?action=badges')
        .then(r => r.json()).then(data => {
            const container = document.getElementById('badges-container');
            container.innerHTML = '';
            if (data.badges.length === 0) {
                container.innerHTML = '<p style="color: #4a5568; grid-column: 1/-1; text-align: center; font-style: italic;">No badges yet.</p>';
                return;
            }
            data.badges.forEach(b => {
                const badge = document.createElement('div');
                badge.className = 'badge-item animate__animated animate__bounceIn';
                badge.innerHTML = `
                    <div class="badge-icon-wrapper">
                        <i class="${getBadgeIcon(b.badge_type)}"></i>
                    </div>
                    <span style="font-weight: 600; font-size: 0.65rem; color: #2d3748;">${b.badge_type}</span>
                `;
                container.appendChild(badge);
            });
        });

    // Schedule
    fetch('api/get_student_portal_data.php?action=schedule')
        .then(r => r.json()).then(data => {
            const list = document.getElementById('student-schedule-list');
            list.innerHTML = '';
            if (data.schedule.length === 0) {
                list.innerHTML = '<div style="text-align: center; color: #4a5568; padding: 1rem;">No classes today.</div>';
                return;
            }
            data.schedule.forEach(s => {
                list.innerHTML += `
                    <div class="schedule-card">
                        <div>
                            <strong style="display: block; font-size: 0.95rem;">${s.subject_name}</strong>
                            <small style="color: #4a5568;">${s.class_code}</small>
                        </div>
                        <div class="schedule-time">
                            <i class="far fa-clock"></i> ${s.start_time.substring(0,5)}
                        </div>
                    </div>
                `;
            });
        });
}

function renderSubjectStats(subjects) {
    const grid = document.getElementById('subject-stats-grid');
    grid.innerHTML = '';

    if (subjects.length === 0) {
        grid.innerHTML = '<p style="color: #4a5568; text-align: center; grid-column: 1/-1;">No subject data available.</p>';
        return;
    }

    subjects.forEach(s => {
        const card = document.createElement('div');
        card.style = "background: #f8fafc; border-radius: var(--radius-md); padding: 1.2rem; border: 1px solid #e2e8f0; transition: transform 0.3s;";
        card.onmouseover = () => card.style.transform = 'translateY(-5px)';
        card.onmouseout = () => card.style.transform = 'translateY(0)';

        const color = s.percentage >= 75 ? '#27ae60' : (s.percentage >= 60 ? '#d4ac0d' : '#c0392b');

        card.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1rem; color: #2d3748; flex: 1; padding-right: 10px;">${s.name}</h3>
                <span style="background: ${color}20; color: ${color}; padding: 4px 10px; border-radius: var(--radius-sm); font-weight: 700; font-size: 0.85rem;">${s.percentage}%</span>
            </div>
            <div style="font-size: 0.8rem; color: #4a5568; margin-bottom: 8px; display: flex; justify-content: space-between;">
                <span>Attended: <strong>${s.present + s.late}</strong> / ${s.total}</span>
            </div>
            <div style="width: 100%; height: 8px; background: #eee; border-radius: 4px; overflow: hidden;">
                <div style="width: ${s.percentage}%; height: 100%; background: ${color}; border-radius: 4px;"></div>
            </div>
        `;
        grid.appendChild(card);
    });
}

function getBadgeIcon(type) {
    if (type === 'Perfect Month') return 'fas fa-calendar-check';
    if (type === 'Early Bird') return 'fas fa-dove';
    return 'fas fa-star';
}

function showDisputeModal() { document.getElementById('dispute-modal').style.display = 'flex'; }
function closeDisputeModal() { document.getElementById('dispute-modal').style.display = 'none'; }
function openDisputeFor(id) {
    showDisputeModal();
    document.getElementById('absent-records-select').value = id;
}

function loadAnnouncements() {
    fetch('api/manage_announcements.php?action=list')
        .then(r => r.json())
        .then(data => {
            const feed = document.getElementById('announcements-feed');
            feed.innerHTML = '';

            if (data.success && data.announcements.length > 0) {
                data.announcements.forEach(a => {
                    const div = document.createElement('div');
                    div.style = "border-bottom: 1px solid #edf2f7; padding: 1rem 0; margin-bottom: 0.5rem;";
                    div.innerHTML = `
                        <h4 style="margin: 0 0 5px; color: #2d3748; font-size: 1rem; font-weight: 700;">${a.title}</h4>
                        <p style="margin: 0 0 10px; color: #4a5568; font-size: 0.85rem; line-height: 1.6;">${a.content}</p>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #4a5568; font-weight: 600;">
                            <span><i class="fas fa-user-edit"></i> ${a.author || 'University Admin'}</span>
                            <span><i class="far fa-clock"></i> ${moment(a.posted_date).fromNow()}</span>
                        </div>
                    `;
                    feed.appendChild(div);
                });
            } else {
                feed.innerHTML = '<p style="text-align: center; color: #4a5568; padding: 2rem;">No recent announcements.</p>';
            }
        });
}
</script>
