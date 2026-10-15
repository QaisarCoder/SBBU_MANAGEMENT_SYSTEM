<?php
// Fetch campus settings for JS
$settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('campus_lat', 'campus_lng', 'campus_radius')");
$campus_settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$campus_lat = $campus_settings['campus_lat'] ?? 24.8607;
$campus_lng = $campus_settings['campus_lng'] ?? 67.0011;
$campus_radius = $campus_settings['campus_radius'] ?? 1000;
?>

<!--
    SBBU Attendance Management System - Advanced Integrated attendance
-->
<script>
    const CAMPUS_LAT = <?php echo $campus_lat; ?>;
    const CAMPUS_LNG = <?php echo $campus_lng; ?>;
    const CAMPUS_RADIUS = <?php echo $campus_radius; ?>;

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // meters
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
    }
</script>

<div class="animate__animated animate__fadeIn">
    <!-- Page Heading -->
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #2c3e50; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-user-check"></i> Attendance Management
    </h1>

    <!-- TOP GUI PANELS -->
    <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

        <!-- GUI: Geo-location Status -->
        <div class="card-panel" style="border-top: 5px solid #cbd5e1;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div id="geo-gui-icon" style="background: #f1f5f9; color: #3498db; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <div style="flex: 1;">
                    <h3 style="margin: 0; font-size: 0.85rem; color: #7f8c8d; text-transform: uppercase; font-family: 'Inter', sans-serif;">Campus Boundary</h3>
                    <span id="gui-geo-status" style="font-weight: 700; font-size: 1.2rem; color: #2c3e50;">Locating...</span>
                </div>
                <div id="geo-badge" style="padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; display: none;">INSIDE</div>
            </div>
            <div style="margin-top: 1rem; font-size: 0.8rem; background: #f8f9fa; padding: 10px; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Coordinates:</span> <span id="gui-coords" style="font-family: monospace;">---</span>
                </div>
                <div style="width: 100%; height: 6px; background: #eee; border-radius: 3px; overflow: hidden;">
                    <div id="geo-accuracy-bar" style="width: 0%; height: 100%; background: #3498db; transition: width 0.5s;"></div>
                </div>
            </div>
        </div>

        <!-- GUI: Time & Status Rules -->
        <div class="card-panel" style="border-top: 5px solid #cbd5e1;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #f1f5f9; color: #f1c40f; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-clock"></i>
                </div>
                <div style="flex: 1;">
                    <h3 style="margin: 0; font-size: 0.85rem; color: #7f8c8d; text-transform: uppercase; font-family: 'Inter', sans-serif;">Time-Based Status</h3>
                    <span id="gui-time-status" style="font-weight: 700; font-size: 1.2rem; color: #2c3e50;">No Class Selected</span>
                </div>
            </div>
            <div style="margin-top: 1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div style="background: #f8f9fa; padding: 8px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 0.65rem; color: #999;">GRACE PERIOD</div>
                    <div style="font-weight: 700; color: #f1c40f;">10 Mins</div>
                </div>
                <div style="background: #f8f9fa; padding: 8px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 0.65rem; color: #999;">ABSENT CUT-OFF</div>
                    <div style="font-weight: 700; color: #e74c3c;">30 Mins</div>
                </div>
            </div>
        </div>

        <!-- GUI: Recent Alerts Log -->
        <div class="card-panel" style="border-top: 5px solid #cbd5e1;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <div style="background: #f1f5f9; color: #2ecc71; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 0.85rem; color: #7f8c8d; text-transform: uppercase; font-family: 'Inter', sans-serif;">Recent Alerts</h3>
                    <span style="font-weight: 700; font-size: 1rem; color: #2ecc71;">Auto-System Active</span>
                </div>
            </div>
            <div id="gui-alert-log" style="max-height: 80px; overflow-y: auto; font-size: 0.75rem; color: #666;">
                <div style="padding: 4px; border-bottom: 1px solid #eee;">• System initialized...</div>
            </div>
        </div>
    </div>

    <!-- MAIN INTERACTIVE SECTION -->
    <div class="card-panel" style="margin-bottom: 2rem;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.1rem; color: #2c3e50;"><i class="fas fa-tasks"></i> Attendance Management Console</h3>
            <div style="display: flex; background: #edf2f7; padding: 4px; border-radius: 12px; gap: 4px;">
                <button onclick="switchTab('manual')" id="tab-btn-manual" class="action-btn active-tab-btn" style="border: none; background: white; color: #2c3e50; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-weight: 700; padding: 8px 16px; border-radius: 8px; cursor: pointer; transition: all 0.2s;">Mark Attendance</button>
                <button onclick="switchTab('history')" id="tab-btn-history" class="action-btn" style="border: none; background: transparent; color: #718096; font-weight: 600; padding: 8px 16px; border-radius: 8px; cursor: pointer; transition: all 0.2s;">View History</button>
                <button onclick="switchTab('bulk')" id="tab-btn-bulk" class="action-btn" style="border: none; background: transparent; color: #718096; font-weight: 600; padding: 8px 16px; border-radius: 8px; cursor: pointer; transition: all 0.2s;">Bulk CSV Upload</button>
            </div>
        </div>

        <div class="card-body">
            <!-- COMMON SELECTOR -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 12px;">
                <div>
                    <label style="font-weight: 700; color: #34495e; display: block; margin-bottom: 8px;">Choose Class / Subject</label>
                    <select id="main-class-select" class="form-control" style="height: 50px; border-radius: 12px; border: 2px solid #e2e8f0; font-weight: 600;">
                        <option value="">Loading Classes...</option>
                    </select>
                </div>
                <div id="date-selector-wrapper">
                    <label style="font-weight: 700; color: #34495e; display: block; margin-bottom: 8px;">Date</label>
                    <input type="date" id="main-date-select" class="form-control" style="height: 50px; border-radius: 12px; border: 2px solid #e2e8f0;" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <!-- SESSION TYPE SELECTOR -->
            <div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; background: #f8fafc; padding: 1rem; border-radius: 12px; border: 1px solid #e2e8f0; flex-wrap: wrap;">
                <div style="font-weight: 700; color: #34495e;">
                    <i class="fas fa-clock"></i> Session Duration:
                </div>
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="session_type" value="1h" checked style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; color: #2c3e50;">1 Hour</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="session_type" value="2h" style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; color: #2c3e50;">2 Hours</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="session_type" value="3h" style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; color: #2c3e50;">3 Hours</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="session_type" value="4h" style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; color: #2c3e50;">4 Hours</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="session_type" value="5h" style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; color: #2c3e50;">5 Hours</span>
                    </label>
                </div>
            </div>

            <!-- TAB 1: MANUAL & FACE AI -->
            <div id="tab-manual">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 style="margin: 0; color: #2c3e50; display: flex; align-items: center; gap: 8px;"><i class="fas fa-fingerprint"></i> Student Roll Call</h4>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <button onclick="openFaceAI()" class="btn-primary" style="width: auto; padding: 8px 20px; background: #6c5ce7; border-radius: 10px; font-size: 0.85rem; border: none; color: white; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-robot"></i> Use AI Scanner
                        </button>
                        <div style="display: flex; align-items: center; background: #f1f5f9; padding: 4px; border-radius: 10px; gap: 5px;">
                            <button onclick="markAll('present')" class="btn-secondary" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 6px 14px; font-size: 0.75rem; border-radius: 8px; color: #2ecc71; font-weight: 700; cursor: pointer;">Mark All P</button>
                            <button onclick="markAll('absent')" class="btn-secondary" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 6px 14px; font-size: 0.75rem; border-radius: 8px; color: #e74c3c; font-weight: 700; cursor: pointer;">Mark All A</button>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th style="width: 150px;">Roll No</th>
                                <th>Student Name</th>
                                <th style="text-align: center; width: 200px;">Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody id="manual-student-list">
                            <tr><td colspan="3" style="text-align:center; padding: 3rem; color: #999;">Select a class to load students</td></tr>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 2rem; text-align: right;">
                    <button onclick="saveAttendance('manual')" class="btn-primary" style="width: auto; padding: 15px 40px; border-radius: 30px; background: var(--primary-color); border: none; color: white; font-weight: 700; cursor: pointer;">
                        <i class="fas fa-cloud-upload-alt"></i> Save & Sync Records
                    </button>
                </div>
            </div>

            <!-- TAB 3: ATTENDANCE HISTORY -->
            <div id="tab-history" style="display: none;">
                <div class="card-header" style="padding-bottom: 1rem;">
                    <h4 style="margin:0; color: #2c3e50;"><i class="fas fa-history"></i> Past Attendance Sessions</h4>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr style="background: #f1f5f9;">
                                <th>Date</th>
                                <th style="text-align: center;">Present</th>
                                <th style="text-align: center;">Absent</th>
                                <th style="text-align: center;">Late</th>
                                <th style="text-align: center;">Total</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="history-session-list">
                            <tr><td colspan="6" style="text-align:center; padding: 3rem; color: #999;">Select a class to load history</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: BULK CSV UPLOAD -->
            <div id="tab-bulk" style="display: none;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Upload Side -->
                    <div style="border: 2px dashed #cbd5e0; border-radius: 16px; padding: 3rem; text-align: center; background: #fdfdfd;">
                        <i class="fas fa-file-csv" style="font-size: 4rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
                        <h4 style="color: #2c3e50; margin-bottom: 0.5rem;">Upload Attendance CSV</h4>
                        <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1.5rem;">Format: Name, Roll No, Status (P/A/L)</p>

                        <input type="file" id="csv-file-input" accept=".csv" style="display: none;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button onclick="document.getElementById('csv-file-input').click()" class="btn-secondary" style="width: auto; padding: 10px 30px; border-radius: 10px; background: white; border: 1px solid #cbd5e1; color: #2c3e50; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-search"></i> Browse Files
                            </button>
                            <button id="clear-csv-btn" onclick="clearCSV()" class="btn-secondary" style="display: none; width: auto; padding: 10px 30px; border-radius: 10px; background: #fff; color: #e74c3c; border: 1px solid #e74c3c; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-times"></i> Clear File
                            </button>
                        </div>

                        <div style="margin-top: 2rem;">
                            <a href="javascript:void(0)" onclick="downloadTemplate()" style="color: #3498db; text-decoration: underline; font-size: 0.85rem;">
                                <i class="fas fa-download"></i> Download CSV Template
                            </a>
                        </div>
                    </div>

                    <!-- Preview Side -->
                    <div style="display: flex; flex-direction: column;">
                        <h4 style="margin-bottom: 1rem; color: #2c3e50;">CSV Preview & Validation</h4>
                        <div id="csv-preview-container" style="flex: 1; border: 1px solid #e2e8f0; border-radius: 12px; overflow-y: auto; max-height: 350px; background: #fff;">
                            <div style="padding: 2rem; text-align: center; color: #999;">No file selected for preview</div>
                        </div>
                        <div id="csv-report" style="margin-top: 1rem; font-size: 0.85rem;"></div>
                    </div>
                </div>

                <div style="margin-top: 2rem; text-align: right;">
                    <button id="commit-bulk-btn" onclick="saveAttendance('bulk')" class="btn-primary" disabled style="width: auto; padding: 15px 40px; border-radius: 30px; opacity: 0.5; background: var(--primary-color); border: none; color: white; font-weight: 700; cursor: pointer;">
                        <i class="fas fa-cloud-upload-alt"></i> Commit Bulk Attendance
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FACE RECOGNITION MODAL -->
<div id="face-ai-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
    <div class="card-panel animate__animated animate__zoomIn" style="width: 95%; max-width: 800px; background: #1a1a1a; color: white; padding: 1.5rem; border-radius: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: white; margin: 0;"><i class="fas fa-brain" style="color: #6c5ce7;"></i> SBBU AI Scanner</h3>
            <button onclick="closeFaceAI()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>

        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">
            <div style="position: relative; aspect-ratio: 4/3; background: #000; border-radius: 12px; overflow: hidden; border: 2px solid #333;">
                <video id="ai-video" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                <canvas id="ai-canvas" style="position: absolute; inset: 0;"></canvas>

                <div id="ai-loader" style="position: absolute; inset: 0; background: rgba(0,0,0,0.8); display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10;">
                    <i class="fas fa-cog fa-spin fa-3x" style="color: #6c5ce7; margin-bottom: 1rem;"></i>
                    <p id="ai-loader-text" style="color: white;">Loading Models...</p>
                    <div style="width: 60%; height: 6px; background: #333; border-radius: 3px; margin-top: 15px; overflow: hidden;">
                        <div id="ai-progress-bar" style="width: 0%; height: 100%; background: #6c5ce7; transition: width 0.3s;"></div>
                    </div>
                    <button id="ai-retry-btn" onclick="initAI(true)" style="display: none; margin-top: 15px;" class="btn-primary">Retry Loading</button>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="background: #2d2d2d; padding: 1rem; border-radius: 12px; border-left: 4px solid #6c5ce7;">
                    <div style="font-size: 0.7rem; color: #888;">AI STATUS</div>
                    <div id="ai-live-status" style="font-weight: 700; color: #f1c40f;">System initializing...</div>
                </div>
                <div style="flex: 1; background: #2d2d2d; border-radius: 12px; padding: 1rem; overflow-y: auto;">
                    <div style="font-size: 0.7rem; color: #888; margin-bottom: 10px;">RECOGNITION LOG</div>
                    <div id="ai-log" style="display: flex; flex-direction: column; gap: 8px;">
                        <div style="font-style: italic; color: #555; font-size: 0.8rem;">Waiting for detections...</div>
                    </div>
                </div>
                <button onclick="closeFaceAI()" class="btn-primary" style="background: #6c5ce7; border: none; color: white; padding: 10px; border-radius: 10px; cursor: pointer; font-weight: 600;">Finish Scanning</button>
            </div>
        </div>
    </div>
</div>

<style>
.action-btn { padding: 8px 20px; border: none; background: #edf2f7; border-radius: 10px; cursor: pointer; font-weight: 600; color: #4a5568; transition: all 0.2s; }
.active-tab-btn { background: white !important; color: #2c3e50 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-weight: 700 !important; }
.status-radio { display: flex; gap: 5px; justify-content: center; }
.status-radio label { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: 700; font-size: 0.8rem; border: 2px solid #edf2f7; transition: all 0.2s; }
.status-radio input { display: none; }
.status-radio input:checked + span { color: white; border-color: transparent; }
.p-label input:checked + span { background: #2ecc71; box-shadow: 0 0 10px rgba(46, 204, 113, 0.4); }
.a-label input:checked + span { background: #e74c3c; box-shadow: 0 0 10px rgba(231, 76, 60, 0.4); }
.l-label input:checked + span { background: #f1c40f; box-shadow: 0 0 10px rgba(241, 196, 15, 0.4); }
</style>

<script>
// --- DATABASE & DATA INITIALIZATION ---
const SBBU_DB = {
    get(key) { return JSON.parse(localStorage.getItem('sbbu_' + key)) || []; },
    set(key, val) { localStorage.setItem('sbbu_' + key, JSON.stringify(val)); },

    init() {
        if (this.get('classes').length === 0) {
            this.set('classes', [
                { id: 1, name: 'Advanced Programming', code: 'CS-401', time: '09:00' },
                { id: 2, name: 'Software Engineering', code: 'CS-302', time: '11:00' },
                { id: 3, name: 'Database Systems', code: 'CS-205', time: '14:00' }
            ]);
        }
        if (this.get('students').length === 0) {
            this.set('students', [
                { id: 101, name: 'Ali Ahmed', roll: '24-BSCS-01', classId: 1 },
                { id: 102, name: 'Sara Khan', roll: '24-BSCS-02', classId: 1 },
                { id: 103, name: 'John Doe', roll: '24-BSCS-32', classId: 2 },
                { id: 104, name: 'Emma Wilson', roll: '24-BSCS-15', classId: 1 }
            ]);
        }
        console.log("Database initialized from localStorage.");
    }
};

// --- CORE LOGIC ---
document.addEventListener('DOMContentLoaded', () => {
    populateClasses();
    initGeoLocation();
    updateTimeGUI();
    setInterval(updateTimeGUI, 10000);
});

function populateClasses() {
    const select = document.getElementById('main-class-select');
    select.innerHTML = '<option value="">Loading Classes...</option>';

    fetch('api/get_classes.php')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.classes) {
                select.innerHTML = '<option value="">-- Choose Class / Subject --</option>';
                data.classes.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    const subjectName = c.subject_name || c.class_name || 'No Subject';
                    const teacherName = c.teacher_name ? `(${c.teacher_name})` : '';
                    const batchInfo = c.batch_name ? `[${c.batch_name}]` : '';
                    opt.textContent = `${c.class_code} | ${subjectName} ${teacherName} ${batchInfo}`;
                    opt.dataset.time = c.start_time || '09:00';
                    select.appendChild(opt);
                });
            } else {
                select.innerHTML = '<option value="">No classes found</option>';
            }
        });
}

document.getElementById('main-class-select').onchange = loadAttendanceData;
document.getElementById('main-date-select').onchange = loadAttendanceData;

function loadAttendanceData() {
    const classId = document.getElementById('main-class-select').value;
    const date = document.getElementById('main-date-select').value;
    const selectedOption = document.getElementById('main-class-select').options[document.getElementById('main-class-select').selectedIndex];

    if (!classId || !date) return;

    const tbody = document.getElementById('manual-student-list');
    tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding: 2rem;"><i class="fas fa-spinner fa-spin"></i> Fetching Records...</td></tr>';

    Promise.all([
        fetch(`api/get_students.php?class_id=${classId}`).then(r => r.json()),
        fetch(`api/get_attendance.php?class_id=${classId}&date=${date}`).then(r => r.json())
    ]).then(([sData, aData]) => {
        tbody.innerHTML = '';
        if (sData.success && sData.students && sData.students.length > 0) {
            const existingAttendance = aData.success ? aData.attendance : {};
            const isEditMode = Object.keys(existingAttendance).length > 0;

            if (isEditMode) {
                logAlert(`Loaded existing records for ${date}. (Edit Mode)`);
            } else {
                logAlert(`No records found for ${date}. (New Entry)`);
            }

            sData.students.forEach(s => {
                const status = existingAttendance[s.id] || 'present'; // Default to P for new
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="font-weight: 700; color: #7f8c8d;">${s.student_id}</td>
                    <td style="font-weight: 600; color: #2c3e50;">${s.full_name}</td>
                    <td>
                        <div class="status-radio">
                            <label class="p-label"><input type="radio" name="st_${s.id}" value="present" ${status === 'present' ? 'checked' : ''}><span>P</span></label>
                            <label class="a-label"><input type="radio" name="st_${s.id}" value="absent" ${status === 'absent' ? 'checked' : ''}><span>A</span></label>
                            <label class="l-label"><input type="radio" name="st_${s.id}" value="late" ${status === 'late' ? 'checked' : ''}><span>L</span></label>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding: 3rem; color: #999;">No students in this class.</td></tr>';
        }
    });

    if (selectedOption && selectedOption.dataset.time) {
        updateTimeGUI(selectedOption.dataset.time);
    }
}

// --- GUI: GEO-LOCATION ---
function initGeoLocation() {
    const statusText = document.getElementById('gui-geo-status');
    const badge = document.getElementById('geo-badge');
    const coordsText = document.getElementById('gui-coords');
    const bar = document.getElementById('geo-accuracy-bar');

    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            coordsText.textContent = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            bar.style.width = "100%";

            const distance = calculateDistance(lat, lng, CAMPUS_LAT, CAMPUS_LNG);
            const isInside = distance <= CAMPUS_RADIUS;

            const distStr = distance > 1000 ? (distance/1000).toFixed(2) + " km" : Math.round(distance) + "m";

            if (isInside) {
                statusText.textContent = "SBBU Campus (" + distStr + ")";
                statusText.style.color = "#2ecc71";
                badge.style.display = 'block';
                badge.style.background = '#2ecc71';
                badge.textContent = 'INSIDE';
            } else {
                statusText.textContent = "Outside (" + distStr + ")";
                statusText.style.color = "#e74c3c";
                badge.style.display = 'block';
                badge.style.background = '#e74c3c';
                badge.textContent = 'OUTSIDE';
            }
        }, err => {
            statusText.textContent = "GPS Disabled";
            statusText.style.color = "#e74c3c";
            coordsText.textContent = "Access Denied";
            bar.style.width = "30%";
            bar.style.background = "#e74c3c";
        }, { enableHighAccuracy: true });
    }
}

// --- GUI: TIME RULES ---
function updateTimeGUI(classStartTime = null) {
    const statusText = document.getElementById('gui-time-status');
    if (!classStartTime) {
        statusText.textContent = "Select a Class";
        return;
    }

    const now = new Date();
    const [h, m] = classStartTime.split(':');
    const start = new Date(); start.setHours(h, m, 0);

    const diff = (now - start) / (1000 * 60); // minutes

    if (diff < 0) {
        statusText.textContent = "Early Arrival";
        statusText.style.color = "#3498db";
    } else if (diff <= 10) {
        statusText.textContent = "Status: ON TIME";
        statusText.style.color = "#2ecc71";
    } else if (diff <= 30) {
        statusText.textContent = "Status: LATE";
        statusText.style.color = "#f1c40f";
    } else {
        statusText.textContent = "Status: ABSENT";
        statusText.style.color = "#e74c3c";
    }
}

// --- GUI: ALERTS LOG ---
function logAlert(msg) {
    const log = document.getElementById('gui-alert-log');
    const entry = document.createElement('div');
    entry.style.padding = '4px';
    entry.style.borderBottom = '1px solid #eee';
    entry.innerHTML = `• [${new Date().toLocaleTimeString()}] ${msg}`;
    log.prepend(entry);
}

// --- TAB SWITCHING ---
function switchTab(tab) {
    document.getElementById('tab-manual').style.display = tab === 'manual' ? 'block' : 'none';
    document.getElementById('tab-bulk').style.display = tab === 'bulk' ? 'block' : 'none';
    document.getElementById('tab-history').style.display = tab === 'history' ? 'block' : 'none';

    const btnManual = document.getElementById('tab-btn-manual');
    const btnHistory = document.getElementById('tab-btn-history');
    const btnBulk = document.getElementById('tab-btn-bulk');

    const activeStyle = "border: none; background: white; color: #2c3e50; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-weight: 700; padding: 8px 16px; border-radius: 8px; cursor: pointer; transition: all 0.2s;";
    const inactiveStyle = "border: none; background: transparent; color: #718096; font-weight: 600; padding: 8px 16px; border-radius: 8px; cursor: pointer; transition: all 0.2s;";

    btnManual.style.cssText = tab === 'manual' ? activeStyle : inactiveStyle;
    btnHistory.style.cssText = tab === 'history' ? activeStyle : inactiveStyle;
    btnBulk.style.cssText = tab === 'bulk' ? activeStyle : inactiveStyle;

    if (tab === 'history') {
        loadHistory();
    }
}

// --- BULK CSV SYSTEM ---
let bulkData = [];
document.getElementById('csv-file-input').onchange = function(e) {
    const file = e.target.files[0];
    if (!file) return;

    document.getElementById('clear-csv-btn').style.display = 'block';
    const reader = new FileReader();
    reader.onload = function(event) {
        const text = event.target.result;
        processCSV(text);
    };
    reader.readAsText(file);
};

function clearCSV() {
    document.getElementById('csv-file-input').value = '';
    document.getElementById('csv-preview-container').innerHTML = '<div style="padding: 2rem; text-align: center; color: #999;">No file selected for preview</div>';
    document.getElementById('csv-report').innerHTML = '';
    document.getElementById('commit-bulk-btn').disabled = true;
    document.getElementById('commit-bulk-btn').style.opacity = '0.5';
    document.getElementById('clear-csv-btn').style.display = 'none';
    bulkData = [];
    logAlert("CSV selection cleared.");
}

function processCSV(text) {
    const classId = document.getElementById('main-class-select').value;
    if (!classId) {
        alert("Please select a class first to validate CSV students.");
        return;
    }

    fetch(`api/get_students.php?class_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert("Error fetching students for validation: " + data.message);
                return;
            }

            const students = data.students;
            const rows = text.split('\n').filter(r => r.trim() !== '');
            const container = document.getElementById('csv-preview-container');
            const report = document.getElementById('csv-report');
            const commitBtn = document.getElementById('commit-bulk-btn');

            bulkData = [];
            let html = '<table style="width:100%; font-size:0.8rem;"><thead><tr style="background:#f7fafc;"><th>Roll No</th><th>Name</th><th>Status</th></tr></thead><tbody>';

            let validCount = 0;

            rows.forEach((row, i) => {
                if (i === 0 && (row.toLowerCase().includes('name') || row.toLowerCase().includes('roll'))) return; // skip header

                const cols = row.split(',').map(c => c.trim());
                if (cols.length >= 3) {
                    const name = cols[0];
                    const roll = cols[1];
                    const statusRaw = cols[2].toLowerCase();
                    let status = 'absent';
                    if (statusRaw.startsWith('p')) status = 'present';
                    if (statusRaw.startsWith('l')) status = 'late';

                    const student = students.find(s => s.student_id === roll);
                    const exists = !!student;
                    const rowStyle = exists ? '' : 'background: #fff5f5; color: #e53e3e;';

                    html += `<tr style="${rowStyle}"><td>${roll}</td><td>${name}</td><td>${status.toUpperCase()}</td></tr>`;

                    if (exists) {
                        bulkData.push({ student_id: student.id, status: status });
                        validCount++;
                    }
                }
            });

            html += '</tbody></table>';
            container.innerHTML = html;
            report.innerHTML = `<span style="color: #2ecc71; font-weight:700;">${validCount} Valid Students matched.</span> | <span style="color: #e53e3e;">${rows.length - 1 - validCount} Errors/Unmatched found.</span>`;

            if (validCount > 0) {
                commitBtn.disabled = false;
                commitBtn.style.opacity = '1';
            }
        });
}

function downloadTemplate() {
    const csvContent = "data:text/csv;charset=utf-8,Name,Roll No,Status\nAli Ahmed,24-BSCS-01,P\nSara Khan,24-BSCS-02,A\nJohn Doe,24-BSCS-32,L";
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "sbbu_attendance_template.csv");
    document.body.appendChild(link);
    link.click();
}

// --- FACE RECOGNITION (FIXED) ---
let aiModelsLoaded = false;
async function initAI(isRetry = false) {
    const loader = document.getElementById('ai-loader');
    const bar = document.getElementById('ai-progress-bar');
    const text = document.getElementById('ai-loader-text');
    const retryBtn = document.getElementById('ai-retry-btn');

    if (aiModelsLoaded && !isRetry) return;

    loader.style.display = 'flex';
    retryBtn.style.display = 'none';

    try {
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';

        text.textContent = "Loading Detector..."; bar.style.width = "30%";
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);

        text.textContent = "Loading Landmarks..."; bar.style.width = "60%";
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);

        text.textContent = "Loading Recognition..."; bar.style.width = "100%";
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);

        aiModelsLoaded = true;
        loader.style.display = 'none';
        document.getElementById('ai-live-status').textContent = "AI Ready. Scanning...";
    } catch (e) {
        text.textContent = "Loading Failed!";
        retryBtn.style.display = 'block';
        console.error(e);
    }
}

async function openFaceAI() {
    document.getElementById('face-ai-modal').style.display = 'flex';
    await initAI();

    const video = document.getElementById('ai-video');
    const canvas = document.getElementById('ai-canvas');
    const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
    video.srcObject = stream;

    video.onplay = () => {
        const displaySize = { width: video.offsetWidth, height: video.offsetHeight };
        faceapi.matchDimensions(canvas, displaySize);

        setInterval(async () => {
            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
            const resized = faceapi.resizeResults(detections, displaySize);
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            faceapi.draw.drawDetections(canvas, resized);

            if (detections.length > 0) {
                document.getElementById('ai-live-status').textContent = "Face Detected! Analyzing...";
            }
        }, 500);
    };
}

function closeFaceAI() {
    document.getElementById('face-ai-modal').style.display = 'none';
    const video = document.getElementById('ai-video');
    if (video.srcObject) video.srcObject.getTracks().forEach(t => t.stop());
}

// --- FINAL SAVE LOGIC (CRUD) ---
function saveAttendance(mode) {
    const classId = document.getElementById('main-class-select').value;
    const date = document.getElementById('main-date-select').value;

    if (!classId) return alert("Please select a class first.");

    const btn = event.target;
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
    btn.disabled = true;

    let attendanceRecords = [];

    if (mode === 'manual') {
        document.querySelectorAll('#manual-student-list tr').forEach(row => {
            const radio = row.querySelector('input[type="radio"]:checked');
            if (radio) {
                attendanceRecords.push({
                    student_id: radio.name.replace('st_', ''),
                    status: radio.value
                });
            }
        });
    } else {
        attendanceRecords = bulkData;
    }

    if (attendanceRecords.length === 0) {
        alert("No attendance data to save.");
        btn.innerHTML = originalContent;
        btn.disabled = false;
        return;
    }

    const performSync = (latitude = null, longitude = null) => {
        const sessionType = document.querySelector('input[name="session_type"]:checked').value;
        const payload = {
            class_id: classId,
            date: date,
            attendance: attendanceRecords,
            session_type: sessionType,
            lat: latitude,
            lng: longitude
        };

        fetch('api/save_attendance.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Sync Successful!';
                btn.style.background = '#2ecc71';
                logAlert(`Attendance updated for ${date} (Class ID: ${classId}).`);

                setTimeout(() => {
                    btn.innerHTML = originalContent;
                    btn.style.background = '';
                    btn.disabled = false;
                }, 2000);
            } else {
                if (data.type === 'GEOFENCE_ERROR') {
                    alert("⚠️ CAMPUS BOUNDARY WARNING\n\n" + data.message);
                } else {
                    alert("Error: " + data.message);
                }
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        })
        .catch(err => {
            alert("Network Error: Failed to save.");
            btn.innerHTML = originalContent;
            btn.disabled = false;
        });
    };

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            pos => performSync(pos.coords.latitude, pos.coords.longitude),
            err => {
                console.warn("GPS Acquisition failed:", err);
                if (confirm("Unable to acquire GPS location. This usually happens on desktops or if location is disabled.\n\nDo you want to proceed with synchronization anyway? (Campus boundary check will be bypassed)")) {
                    performSync(null, null);
                } else {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }
            },
            { timeout: 8000, enableHighAccuracy: true }
        );
    } else {
        performSync(null, null);
    }
}

function markAll(status) {
    document.querySelectorAll(`#manual-student-list input[value="${status}"]`).forEach(r => r.checked = true);
    logAlert(`All students marked as ${status.toUpperCase()}`);
}

function loadHistory() {
    const classId = document.getElementById('main-class-select').value;
    const tbody = document.getElementById('history-session-list');

    if (!classId) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 3rem; color: #999;">Please select a class first.</td></tr>';
        return;
    }

    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 2rem;"><i class="fas fa-spinner fa-spin"></i> Loading History...</td></tr>';

    fetch(`api/get_attendance_sessions.php?class_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.sessions && data.sessions.length > 0) {
                tbody.innerHTML = '';
                data.sessions.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="font-weight: 600; color: #2c3e50;">${s.attendance_date}</td>
                        <td style="text-align: center; color: #2ecc71; font-weight: 700;">${s.present_count}</td>
                        <td style="text-align: center; color: #e74c3c; font-weight: 700;">${s.absent_count}</td>
                        <td style="text-align: center; color: #f1c40f; font-weight: 700;">${s.late_count}</td>
                        <td style="text-align: center; font-weight: 700; color: #2c3e50;">${s.total_students}</td>
                        <td style="text-align: right;">
                            <button onclick="editSession('${s.attendance_date}')" class="btn-secondary" style="padding: 5px 15px; font-size: 0.8rem; border-radius: 8px; background: white; border: 1px solid #cbd5e1; cursor: pointer; color: #2c3e50;">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="deleteSession('${s.attendance_date}')" class="btn-secondary" style="padding: 5px 15px; font-size: 0.8rem; border-radius: 8px; color: #e74c3c; border: 1px solid #e74c3c; background: white; cursor: pointer; margin-left: 5px;">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 3rem; color: #999;">No attendance history found for this class.</td></tr>';
            }
        });
}

function editSession(date) {
    document.getElementById('main-date-select').value = date;
    switchTab('manual');
    loadAttendanceData();
}

function deleteSession(date) {
    const classId = document.getElementById('main-class-select').value;
    if (!classId || !date) return;

    if (!confirm(`Are you sure you want to delete all attendance records for ${date}? This action cannot be undone.`)) return;

    fetch('api/delete_attendance_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ class_id: classId, date: date })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadHistory();
        } else {
            alert("Error: " + data.message);
        }
    });
}
</script>
