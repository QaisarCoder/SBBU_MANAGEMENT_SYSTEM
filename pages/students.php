<div class="animate__animated animate__fadeIn">
    <!-- Manage Students Header Card -->
    <div class="card-panel animate__animated animate__fadeInUp" style="margin-bottom: 2rem; border-left: 5px solid var(--primary-color);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3><i class="fas fa-user-graduate"></i> Student Management</h3>
            <button onclick="toggleBulkUpload()" class="btn-secondary" style="width: auto; padding: 10px 25px; border-radius: 30px; margin-right: 10px;">
                <i class="fas fa-file-upload"></i> Bulk Upload
            </button>
            <button onclick="toggleAddForm()" class="btn-primary" style="width: auto; padding: 10px 25px; border-radius: 30px;">
                <i class="fas fa-user-plus"></i> Register New Student
            </button>
        </div>

        <!-- Bulk Upload Section -->
        <div id="bulk-upload-section" style="display: none; padding: 1.5rem; background: #f8fafc; border-top: 1px solid #edf2f7;">
            <div style="margin-bottom: 1.5rem; background: #fff; padding: 1rem; border-radius: 15px; border: 1px solid #e2e8f0;">
                <label style="font-weight: 700; color: #2c3e50; display: block; margin-bottom: 8px;">
                    <i class="fas fa-bullseye" style="color: #e74c3c;"></i> Target Course (Optional)
                </label>
                <p style="font-size: 0.8rem; color: #666; margin-bottom: 10px;">If selected, all students in the CSV will be automatically enrolled in this course's Program, Batch, and Semester.</p>
                <select id="bulk-target-class" class="form-control" style="height: 45px; border-radius: 10px; border: 2px solid #edf2f7;">
                    <option value="">-- Apply CSV values (Default) --</option>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div style="border: 2px dashed #cbd5e0; border-radius: 20px; padding: 2rem; text-align: center; background: #fff;">
                    <i class="fas fa-file-csv" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
                    <h4>Upload Students CSV</h4>
                    <p style="color: #718096; font-size: 0.85rem; margin-bottom: 1.5rem;">Format: ID, Name, Email, Program ID, Batch ID, Semester</p>
                    <input type="file" id="student-csv-file" accept=".csv" style="display: none;">
                    <button onclick="document.getElementById('student-csv-file').click()" class="btn-secondary" style="width: auto; padding: 8px 25px; border-radius: 10px;">
                        <i class="fas fa-search"></i> Browse CSV
                    </button>
                </div>
                <div id="csv-preview-area" style="background: white; border-radius: 15px; border: 1px solid #e2e8f0; padding: 1rem; max-height: 250px; overflow-y: auto;">
                    <p style="text-align: center; color: #999; margin-top: 2rem;">Preview will appear here</p>
                </div>
            </div>
            <div style="margin-top: 1.5rem; text-align: right;">
                <button id="commit-bulk-students" onclick="uploadStudentsBulk()" class="btn-primary" disabled style="width: auto; padding: 12px 35px; border-radius: 30px; opacity: 0.5;">
                    <i class="fas fa-check-circle"></i> Import Students
                </button>
            </div>
        </div>

        <div id="add-student-form" style="display: none; padding: 1.5rem;">
            <div style="margin-bottom: 1.5rem; background: #fffbeb; padding: 1rem; border-radius: 12px; border: 1px solid #fef3c7;">
                <label style="font-weight: 700; color: #92400e; display: block; margin-bottom: 5px;">
                    <i class="fas fa-magic"></i> Quick Select from Course (Optional)
                </label>
                <select id="quick-course-select" class="form-control" style="height: 45px; border-radius: 10px; border: 2px solid #fef3c7;" onchange="autoFillFormFromCourse(this.value)">
                    <option value="">-- Manual Selection --</option>
                </select>
            </div>
            <form id="student-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: var(--text-muted);">Student University ID</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" name="student_id" id="student_id_input" class="form-control" required placeholder="e.g. 24-BSCS-01" style="height: 45px; border-radius: 10px;">
                        <button type="button" onclick="generateStudentIdFormat()" class="btn-secondary" style="width: auto; padding: 0 15px; border-radius: 10px; font-size: 0.8rem; background: #f8fafc; border: 1px solid #cbd5e0; color: #4a5568;">
                            <i class="fas fa-magic"></i> Auto
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: var(--text-muted);">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="John Doe" style="height: 45px; border-radius: 10px;">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: var(--text-muted);">Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="john.doe@sbbu.edu.pk" style="height: 45px; border-radius: 10px;">
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: var(--text-muted);">Academic Program</label>
                    <select name="program_id" id="program_id_select" class="form-control" required style="height: 45px; border-radius: 10px;">
                        <option value="">Select Program...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: var(--text-muted);">Select Batch</label>
                    <select name="batch_id" id="batch_id_select" class="form-control" required style="height: 45px; border-radius: 10px;">
                        <option value="">Select Batch...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: var(--text-muted);">Current Semester</label>
                    <input type="number" name="semester" class="form-control" min="1" max="8" required placeholder="e.g. 4" style="height: 45px; border-radius: 10px;">
                </div>

                <div class="form-group" style="grid-column: 1 / -1; background: #f8fafc; padding: 1.2rem; border-radius: 15px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="font-weight: 700; color: #2d3748;">
                            <i class="fas fa-camera" style="color: var(--primary-color);"></i> Face Enrollment (Optional)
                        </label>
                        <span class="badge" style="background: #edf2f7; color: #718096; position: static; padding: 4px 10px; font-size: 0.7rem;">Can be added later</span>
                    </div>
                    <p style="font-size: 0.8rem; color: #718096; margin-bottom: 1rem;">You can register the student now and enroll their face descriptor later from the student directory.</p>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <button type="button" id="enroll-btn-main" onclick="openFaceEnrollment()" class="btn-secondary" style="width: auto; padding: 10px 25px; border-radius: 10px; background: #fff; border: 2px solid var(--primary-color); color: var(--primary-dark);">
                            <i class="fas fa-user-plus"></i> Capture Face Now
                        </button>
                        <div id="enrollment-status-container" style="display: flex; align-items: center; gap: 8px;">
                            <span id="enrollment-status" style="font-size: 0.85rem; color: #666; font-weight: 500;">AI System initializing...</span>
                        </div>
                        <input type="hidden" name="face_descriptor" id="face_descriptor">
                    </div>
                </div>

                <div style="grid-column: 1 / -1; display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #edf2f7;">
                    <button type="button" onclick="toggleAddForm()" class="action-btn" style="border-radius: 30px; padding: 12px 30px;">Cancel</button>
                    <button type="submit" id="register-student-btn" class="btn-primary" style="width: auto; min-width: 220px; border-radius: 30px; box-shadow: 0 4px 15px rgba(139, 201, 212, 0.3);">
                        <i class="fas fa-check-circle"></i> Complete Registration
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Face Enrollment Modal -->
    <div id="face-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
        <div class="card-panel animate__animated animate__zoomIn" style="width: 95%; max-width: 700px; padding: 0; border-radius: 25px; overflow: hidden; background: #fff;">
            <div style="background: var(--primary-color); padding: 1.5rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: white;"><i class="fas fa-camera"></i> Face Enrollment System</h3>
                <button onclick="closeFaceEnrollment()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;"><i class="fas fa-times"></i></button>
            </div>

            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 0;">
                <!-- Video Area -->
                <div style="padding: 1.5rem; background: #f8fafc;">
                    <div style="position: relative; background: #000; border-radius: 15px; overflow: hidden; aspect-ratio: 4/3; box-shadow: var(--shadow-md);">
                        <video id="enroll-video" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                        <canvas id="enroll-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>

                        <!-- Overlay for status -->
                        <div id="lighting-warning" style="position: absolute; top: 10px; left: 10px; right: 10px; background: rgba(231, 76, 60, 0.9); color: white; padding: 8px; border-radius: 8px; font-size: 0.8rem; display: none; text-align: center;">
                            <i class="fas fa-sun"></i> Poor Lighting Detected: Move to a brighter area.
                        </div>

                        <div id="enroll-loader" style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); display: none; flex-direction: column; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-circle-notch fa-spin fa-3x"></i>
                            <p style="margin-top: 10px; font-weight: 600;">Processing Face Data...</p>
                        </div>
                    </div>

                    <div id="confidence-display" style="margin-top: 10px; text-align: center; font-weight: 700; color: var(--primary-dark); font-size: 0.9rem;">
                        Detection Confidence: 0%
                    </div>
                </div>

                <!-- Controls & Tips Area -->
                <div style="padding: 1.5rem; border-left: 1px solid #edf2f7;">
                    <div id="enroll-feedback" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 12px; background: #fff8e1; color: #b7791f; font-weight: 600; font-size: 0.9rem; border: 1px solid #fef3c7;">
                        Initializing camera...
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="font-size: 0.9rem; margin-bottom: 8px;"><i class="fas fa-lightbulb" style="color: #ecc94b;"></i> Tips for Success:</h4>
                        <ul style="font-size: 0.8rem; color: #718096; padding-left: 1.2rem; line-height: 1.6;">
                            <li>Ensure good lighting (face should be bright)</li>
                            <li>Remove glasses and face masks</li>
                            <li>Look directly at the camera</li>
                            <li>Keep a neutral expression</li>
                        </ul>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button id="capture-btn" onclick="captureFace()" class="btn-primary" style="width: 100%; border-radius: 12px; padding: 12px;">
                            <i class="fas fa-camera-retro"></i> Capture & Save AI Data
                        </button>
                        <button id="test-camera-btn" onclick="testCamera()" class="btn-secondary" style="width: 100%; border-radius: 12px; padding: 10px; background: #fff; color: #4a5568; border: 1px solid #cbd5e0;">
                            <i class="fas fa-vial"></i> Test Camera Only
                        </button>
                        <button id="manual-fallback-btn" onclick="manualCapture()" class="btn-secondary" style="width: 100%; border-radius: 12px; padding: 10px; background: #edf2f7; color: #4a5568; border: none; margin-top: 5px;">
                            <i class="fas fa-image"></i> Manual Fallback (Skip AI)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Directory Card -->
    <div class="card-panel animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h3><i class="fas fa-address-book"></i> Student Directory</h3>
            <div class="filter-controls" style="display: flex; align-items: center; gap: 10px;">
                <button onclick="clearAllFaceData()" class="btn-secondary" style="width: auto; padding: 5px 15px; font-size: 0.75rem; border-radius: 15px; background: #fff; color: #e74c3c; border: 1px solid #e74c3c; margin-right: 1rem;">
                    <i class="fas fa-trash-alt"></i> Clear All Face Data
                </button>
                <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600;">Filter:</span>
                <select id="class-filter" class="form-control" style="width: 220px; height: 38px; border-radius: 20px; font-size: 0.85rem;">
                    <option value="">All Classes / Students</option>
                </select>
            </div>
        </div>

        <div class="card-body" style="padding: 0;">
            <div class="table-container" style="box-shadow: none; border-radius: 0;">
                <table style="width: 100%;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Batch</th>
                            <th>Face Status</th>
                            <th style="text-align: center;">Semester</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="students-list-full">
                        <tr><td colspan="6" style="text-align:center; padding: 3rem;"><div class="skeleton-loader"></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
#students-list-full td { padding: 1.2rem 1rem; vertical-align: middle; }
#students-list-full tr:hover { background: #f8fafc; }

.id-badge {
    background: #edf2f7;
    color: var(--primary-dark);
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    font-family: monospace;
    font-size: 0.9rem;
}

.semester-badge {
    display: inline-block;
    width: 28px;
    height: 28px;
    line-height: 28px;
    text-align: center;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    font-weight: 700;
    font-size: 0.8rem;
}

.action-icon-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    transition: all 0.2s;
    font-size: 1.1rem;
}

.delete-icon { color: var(--danger-color); opacity: 0.7; }
.delete-icon:hover { opacity: 1; transform: scale(1.1); }
</style>

<script>
    function toggleAddForm() {
        const form = document.getElementById("add-student-form");
        const bulk = document.getElementById("bulk-upload-section");
        form.style.display = form.style.display === "none" ? "block" : "none";
        bulk.style.display = "none";
        if(form.style.display === "block") {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function toggleBulkUpload() {
        const bulk = document.getElementById("bulk-upload-section");
        const form = document.getElementById("add-student-form");
        bulk.style.display = bulk.style.display === "none" ? "block" : "none";
        form.style.display = "none";
        if(bulk.style.display === "block") {
            bulk.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function loadInitialData() {
        // Load Programs for select
        fetch("api/get_programs.php")
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById("program_id_select");
                if(data.programs) {
                    data.programs.forEach(p => {
                        const opt = document.createElement("option");
                        opt.value = p.id;
                        opt.textContent = p.program_name;
                        select.appendChild(opt);
                    });
                }
            });

        // Load Batches for select
        fetch("api/manage_batches.php?action=list")
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById("batch_id_select");
                if(data.batches) {
                    data.batches.forEach(b => {
                        const opt = document.createElement("option");
                        opt.value = b.id;
                        opt.textContent = b.batch_name;
                        select.appendChild(opt);
                    });
                }
            });

        // Load Classes for filter, target, and quick select
        fetch("api/get_classes.php")
            .then(r => r.json())
            .then(data => {
                if (data.classes) {
                    const filterSelect = document.getElementById("class-filter");
                    const targetSelect = document.getElementById("bulk-target-class");
                    const quickSelect = document.getElementById("quick-course-select");

                    data.classes.forEach(c => {
                        const opt = document.createElement("option");
                        opt.value = c.id;
                        opt.textContent = `${c.class_code} - ${c.class_name}`;

                        // Add to filter
                        filterSelect.appendChild(opt.cloneNode(true));
                        // Add to target
                        targetSelect.appendChild(opt.cloneNode(true));
                        // Add to quick select (store meta in dataset)
                        const qOpt = opt.cloneNode(true);
                        qOpt.dataset.prog = c.program_id;
                        qOpt.dataset.batch = c.batch_id;
                        qOpt.dataset.sem = c.semester;
                        quickSelect.appendChild(qOpt);
                    });
                }
            });

        initFaceAI();
        loadStudents();
    }

    function autoFillFormFromCourse(classId) {
        if (!classId) return;
        const select = document.getElementById("quick-course-select");
        const selected = select.options[select.selectedIndex];

        if (selected.dataset.prog) {
            document.getElementById("program_id_select").value = selected.dataset.prog;
            document.getElementById("batch_id_select").value = selected.dataset.batch;
            document.querySelector('input[name="semester"]').value = selected.dataset.sem;

            // Add a little highlight effect
            const fields = ["program_id_select", "batch_id_select", "semester"];
            fields.forEach(id => {
                const el = document.getElementById(id) || document.querySelector(`input[name="${id}"]`);
                if (el) {
                    el.style.borderColor = "#2ecc71";
                    setTimeout(() => el.style.borderColor = "", 1500);
                }
            });
        }
    }

    let bulkStudentsData = [];
    document.getElementById('student-csv-file').onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            const text = event.target.result;
            processStudentCSV(text);
        };
        reader.readAsText(file);
    };

    function processStudentCSV(text) {
        const rows = text.split('\n').filter(r => r.trim() !== '');
        const preview = document.getElementById('csv-preview-area');
        const commitBtn = document.getElementById('commit-bulk-students');
        const targetClassId = document.getElementById('bulk-target-class').value;

        bulkStudentsData = [];
        let html = '<table style="width:100%; font-size:0.8rem;"><thead><tr style="background:#f7fafc;"><th>ID</th><th>Name</th><th>Email</th><th>Sem</th></tr></thead><tbody>';

        rows.forEach((row, i) => {
            if (i === 0 && (row.toLowerCase().includes('id') || row.toLowerCase().includes('name'))) return; // skip header

            const cols = row.split(',').map(c => c.trim());
            if (cols.length >= 2) {
                const student_id = cols[0];
                const full_name = cols[1];
                const email = cols[2] || '';
                const program_id = cols[3] || document.getElementById('program_id_select').value;
                const batch_id = cols[4] || document.getElementById('batch_id_select').value;
                const semester = cols[5] || 1;

                html += `<tr><td>${student_id}</td><td>${full_name}</td><td>${email}</td><td>${semester}</td></tr>`;
                bulkStudentsData.push({
                    student_id,
                    full_name,
                    email,
                    program_id,
                    batch_id,
                    semester,
                    target_class_id: targetClassId
                });
            }
        });

        html += '</tbody></table>';
        preview.innerHTML = html;

        if (bulkStudentsData.length > 0) {
            commitBtn.disabled = false;
            commitBtn.style.opacity = '1';
        }
    }

    function uploadStudentsBulk() {
        const btn = document.getElementById('commit-bulk-students');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';

        const fd = new FormData();
        fd.append('students', JSON.stringify(bulkStudentsData));

        fetch('api/bulk_upload_students.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Import Students';
                if (data.success) {
                    alert(data.message);
                    toggleBulkUpload();
                    loadStudents();
                } else {
                    alert(data.message);
                }
            });
    }

    async function initFaceAI() {
        const statusText = document.getElementById('enrollment-status');
        const enrollBtn = document.querySelector('button[onclick="openFaceEnrollment()"]');

        if (enrollBtn) enrollBtn.disabled = true;

        await FaceLogic.init((type, data) => {
            if (type === 'progress') {
                statusText.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Loading AI: ${data.name} (${data.percent}%)`;
                statusText.style.color = "var(--primary-color)";
            } else if (type === 'error') {
                statusText.innerHTML = `<i class="fas fa-exclamation-triangle"></i> AI Failed: ${data}. <a href="javascript:void(0)" onclick="initFaceAI()" style="text-decoration: underline;">Retry</a>`;
                statusText.style.color = "var(--danger-color)";
            } else if (type === 'complete') {
                statusText.innerHTML = `<i class="fas fa-check-circle"></i> AI System Ready.`;
                statusText.style.color = "#2ecc71";
                if (enrollBtn) enrollBtn.disabled = false;
            }
        });
    }

    function loadStudents(classId = "") {
        const url = classId ? `api/get_students.php?class_id=${classId}` : `api/get_students_all.php`;

        Promise.all([
            fetch(url).then(r => r.json()),
            FaceLogic.getAllEnrolled()
        ]).then(([data, enrolledFaces]) => {
            const tbody = document.getElementById("students-list-full");
            tbody.innerHTML = "";
            if (data.students && data.students.length > 0) {
                data.students.forEach(s => {
                    const isEnrolled = enrolledFaces.some(f => f.studentId === s.student_id);
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td><span class="id-badge">${s.student_id}</span></td>
                        <td style="font-weight: 600; color: var(--text-dark);">${s.full_name}</td>
                        <td><span style="font-size: 0.85rem; color: var(--text-muted);">${s.batch_name || 'N/A'}</span></td>
                        <td>
                            ${isEnrolled
                                ? '<span style="color: #2ecc71; font-size: 0.85rem; font-weight: 600;"><i class="fas fa-check-circle"></i> Enrolled</span>'
                                : '<button onclick="openFaceEnrollment(\''+s.student_id+'\', \''+s.full_name+'\')" class="btn-secondary" style="font-size: 0.7rem; padding: 4px 10px; width: auto; background: #fff8e1; color: #b7791f; border: 1px solid #fef3c7;"><i class="fas fa-plus-circle"></i> Enroll Face</button>'}
                        </td>
                        <td style="text-align: center;"><span class="semester-badge">${s.semester}</span></td>
                        <td style="text-align: right;">
                            <button class="action-icon-btn" onclick='editStudent(${JSON.stringify(s)})' title="Edit Student" style="color: var(--secondary-color); margin-right: 5px;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-icon-btn" onclick="openFaceEnrollment('${s.student_id}', '${s.full_name}')" title="Re-enroll Face" style="color: var(--primary-color);">
                                <i class="fas fa-camera"></i>
                            </button>
                            <button class="action-icon-btn delete-icon" onclick="deleteStudent(${s.id}, '${s.student_id}')" title="Delete Student">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = "<tr><td colspan=6 style=\"text-align:center; padding: 4rem; color: var(--text-muted);\">No students found.</td></tr>";
            }
        });
    }

    function editStudent(s) {
        // Show the add form but change it to edit mode
        const formDiv = document.getElementById("add-student-form");
        formDiv.style.display = "block";
        formDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });

        document.getElementById("commit-bulk-students").parentElement.parentElement.style.display = "none"; // Hide bulk upload

        const form = document.getElementById("student-form");
        form.action.value = "update";

        // Add hidden ID if not exists
        if (!document.getElementById("edit-student-id-val")) {
            const hid = document.createElement("input");
            hid.type = "hidden";
            hid.name = "id";
            hid.id = "edit-student-id-val";
            form.appendChild(hid);
        }
        document.getElementById("edit-student-id-val").value = s.id;

        form.student_id.value = s.student_id;
        form.full_name.value = s.full_name;
        form.email.value = s.email;
        form.program_id.value = s.program_id;
        form.batch_id.value = s.batch_id;
        form.semester.value = s.semester;

        // Add password field for login update
        if (!document.getElementById("edit-password-field")) {
            const pGroup = document.createElement("div");
            pGroup.className = "form-group";
            pGroup.id = "edit-password-field";
            pGroup.innerHTML = `
                <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: #e67e22;">
                    <i class="fas fa-key"></i> New Password (Login)
                </label>
                <input type="text" name="password" class="form-control" placeholder="Leave blank to keep current" style="height: 45px; border-radius: 10px; border: 1px solid #e67e22;">
                <small style="color: #666;">This will update their Student Portal login password.</small>
            `;
            // Insert before the face enrollment section
            const faceSection = document.querySelector(".form-group[style*='grid-column: 1 / -1']");
            form.insertBefore(pGroup, faceSection);
        }

        document.getElementById("register-student-btn").innerHTML = '<i class="fas fa-save"></i> Update Student & Login';
    }

    function generateStudentIdFormat() {
        const progSelect = document.getElementById("program_id_select");
        const batchSelect = document.getElementById("batch_id_select");

        if (!progSelect.value || !batchSelect.value) {
            alert("Please select Program and Batch first.");
            return;
        }

        const progName = progSelect.options[progSelect.selectedIndex].text;
        const batchName = batchSelect.options[batchSelect.selectedIndex].text;

        // 1. Extract Batch (last 2 digits of start year, e.g., 2024 -> 24)
        const batchYear = batchName.split('-')[0].trim().slice(-2);

        // 2. Extract Program Code (e.g., "BS Computer Science" -> "BSCS")
        let progCode = progName.match(/[A-Z]/g)?.join('') || progName.substring(0, 4).toUpperCase();
        if (progName.toLowerCase().includes("software engineering")) progCode = "BSSE";
        if (progName.toLowerCase().includes("computer science")) progCode = "BSCS";
        if (progName.toLowerCase().includes("information technology")) progCode = "BSIT";

        const rollNo = prompt("Enter Student Roll Number (e.g., 01, 15, 102):");
        if (!rollNo) return;

        // Format: 24-BSCS-01
        const formattedId = `${batchYear}-${progCode}-${rollNo.padStart(2, '0')}`;
        document.getElementById("student_id_input").value = formattedId;
    }

    function toggleAddForm() {
        const form = document.getElementById("add-student-form");
        const bulk = document.getElementById("bulk-upload-section");

        // Reset form to add mode
        const sForm = document.getElementById("student-form");
        sForm.action.value = "add";
        sForm.reset();
        document.getElementById("register-student-btn").innerHTML = '<i class="fas fa-check-circle"></i> Complete Registration';
        if (document.getElementById("edit-password-field")) document.getElementById("edit-password-field").remove();
        if (document.getElementById("edit-student-id-val")) document.getElementById("edit-student-id-val").remove();

        form.style.display = form.style.display === "none" ? "block" : "none";
        bulk.style.display = "none";
        if(form.style.display === "block") {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // --- Face Enrollment Logic ---
    let currentEnrollmentId = null;
    let currentEnrollmentName = null;
    let detectionInterval = null;

    async function openFaceEnrollment(id = null, name = null) {
        if (!FaceLogic.modelsLoaded) {
            alert("AI Models are still loading. Please wait...");
            return;
        }

        currentEnrollmentId = id || document.querySelector('input[name="student_id"]').value;
        currentEnrollmentName = name || document.querySelector('input[name="full_name"]').value;

        if (!currentEnrollmentId) {
            alert("Please enter Student University ID first.");
            return;
        }

        document.getElementById('face-modal').style.display = 'flex';
        const video = document.getElementById('enroll-video');
        const canvas = document.getElementById('enroll-canvas');
        const feedback = document.getElementById('enroll-feedback');
        const confidenceDisplay = document.getElementById('confidence-display');
        const lightingWarning = document.getElementById('lighting-warning');

        feedback.textContent = "Starting camera...";
        feedback.className = "alert-info"; // Custom style needed or just use inline

        const success = await FaceLogic.startVideo(video);
        if (!success) {
            alert("Could not access webcam. Please ensure you have given permission.");
            closeFaceEnrollment();
            return;
        }

        video.onplay = () => {
            const displaySize = { width: video.offsetWidth, height: video.offsetHeight };
            faceapi.matchDimensions(canvas, displaySize);

            detectionInterval = setInterval(async () => {
                // Check brightness
                const brightness = FaceLogic.getBrightness(video);
                if (brightness < 40) lightingWarning.style.display = 'block';
                else lightingWarning.style.display = 'none';

                const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.5 })).withFaceLandmarks();
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (detection) {
                    const resizedDetection = faceapi.resizeResults(detection, displaySize);
                    faceapi.draw.drawDetections(canvas, resizedDetection);

                    const score = Math.round(detection.score * 100);
                    confidenceDisplay.textContent = `Detection Confidence: ${score}%`;
                    confidenceDisplay.style.color = score > 70 ? "#2ecc71" : "#f1c40f";

                    feedback.textContent = "Face Detected! You can capture now.";
                    feedback.style.background = "#e6fffa";
                    feedback.style.color = "#2c7a7b";
                    feedback.style.borderColor = "#b2f5ea";
                } else {
                    confidenceDisplay.textContent = "Detection Confidence: 0%";
                    confidenceDisplay.style.color = "#e74c3c";
                    feedback.textContent = "No face detected. Align your face in the center.";
                    feedback.style.background = "#fff5f5";
                    feedback.style.color = "#c53030";
                    feedback.style.borderColor = "#feb2b2";
                }
            }, 200);
        };
    }

    function closeFaceEnrollment() {
        document.getElementById('face-modal').style.display = 'none';
        FaceLogic.stopVideo(document.getElementById('enroll-video'));
        if (detectionInterval) clearInterval(detectionInterval);
        const canvas = document.getElementById('enroll-canvas');
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    }

    function testCamera() {
        alert("Camera is working correctly if you can see yourself. If not, check browser permissions.");
    }

    function manualCapture() {
        if(confirm("Manual fallback will skip AI descriptor generation. The student will be registered without face data. Proceed?")) {
            document.getElementById('enrollment-status').textContent = "Face capture skipped (Manual Fallback).";
            document.getElementById('enrollment-status').style.color = "#718096";
            document.getElementById('face_descriptor').value = ""; // Empty means skipped
            closeFaceEnrollment();
        }
    }

    async function captureFace() {
        const video = document.getElementById('enroll-video');
        const loader = document.getElementById('enroll-loader');
        loader.style.display = 'flex';

        try {
            const detection = await FaceLogic.getFaceDescriptor(video);
            if (detection) {
                await FaceLogic.enrollStudent(currentEnrollmentId, currentEnrollmentName, detection.descriptor);

                // Update UI status
                const statusEl = document.getElementById('enrollment-status');
                if (statusEl) {
                    statusEl.textContent = "Face data captured successfully!";
                    statusEl.style.color = "#2ecc71";
                }
                document.getElementById('face_descriptor').value = "captured";

                alert("Face enrollment successful for " + currentEnrollmentName);
                closeFaceEnrollment();
                loadStudents(document.getElementById("class-filter").value);
            } else {
                alert("Face not detected clearly enough. Please ensure good lighting and face the camera directly.");
            }
        } catch (err) {
            console.error(err);
            alert("Error during capture: " + err.message);
        } finally {
            loader.style.display = 'none';
        }
    }

    async function clearAllFaceData() {
        if (confirm("Are you sure you want to clear ALL face enrollment data? This cannot be undone.")) {
            const db = await new Promise((resolve) => {
                const request = indexedDB.open(FaceLogic.dbName, 1);
                request.onsuccess = (e) => resolve(e.target.result);
            });
            const transaction = db.transaction([FaceLogic.storeName], "readwrite");
            transaction.objectStore(FaceLogic.storeName).clear();
            transaction.oncomplete = () => {
                alert("All face data cleared.");
                loadStudents();
            };
        }
    }

    document.getElementById("student-form").addEventListener("submit", function(e) {
        e.preventDefault();

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        fetch("api/manage_students.php", { method: "POST", body: new FormData(this) })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;

                if (data.success) {
                    toggleAddForm();
                    this.reset();
                    loadStudents(document.getElementById("class-filter").value);
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });

    document.getElementById("class-filter").addEventListener("change", function() {
        loadStudents(this.value);
    });

    loadInitialData();
</script>
