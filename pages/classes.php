<div class="animate__animated animate__fadeIn">
    <!-- External Libraries for Clock -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Header Hero Section -->
    <div class="card-panel" style="background: linear-gradient(135deg, #7FAFB7 0%, #638d93 100%); color: white; margin-bottom: 2rem; border: none; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 20px rgba(127, 175, 183, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 1.8rem;"><i class="fas fa-chalkboard-teacher"></i> Manage Classes</h2>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Define classes, assign subjects, teachers, and schedules.</p>
            </div>
            <button class="btn-primary" onclick="showAddClassModal()" style="background: white; color: #6aafbc; border: none; width: auto; padding: 12px 30px; border-radius: 30px; font-weight: 700; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <i class="fas fa-plus-circle"></i> Create New Class
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card-panel animate__animated animate__fadeInUp" style="padding: 0; overflow: hidden; border-radius: 20px; border: 1px solid #eee;">
        <div style="padding: 1.5rem; background: #fafafa; border-bottom: 1px solid #eee;">
            <h3 style="margin: 0; font-size: 1.1rem; color: #444;"><i class="fas fa-list-ul"></i> Active Classes Directory</h3>
        </div>
        <div class="table-container" style="box-shadow: none; border-radius: 0;">
            <table style="width: 100%;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 1.2rem;">Code</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Batch / Sem</th>
                        <th>Schedule</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="classes-list-full">
                    <tr><td colspan="6" style="text-align:center; padding: 3rem;"><div class="skeleton-loader"></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Class Modal -->
<div id="class-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card-panel animate__animated animate__zoomIn" style="width: 95%; max-width: 700px; border-radius: 25px; padding: 2rem; border: none; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 id="modal-title" style="margin: 0; color: #2c3e50;"><i class="fas fa-plus-circle" style="color: #8BC9D4;"></i> Create New Class</h3>
            <button onclick="closeClassModal()" style="background: #f1f5f9; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; color: #64748b; transition: 0.2s;"><i class="fas fa-times"></i></button>
        </div>
        <form id="class-form">
            <input type="hidden" name="action" id="form-action" value="add">
            <input type="hidden" name="id" id="class-id">
            <input type="hidden" name="schedule" id="final_schedule">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div class="form-group">
                    <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Class Code</label>
                    <input type="text" name="class_code" class="form-control" required placeholder="e.g. CS-401" style="height: 45px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                </div>

                <div class="form-group">
                    <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Select Subject</label>
                    <select name="subject_id" id="class_subject_id" class="form-control" required style="height: 45px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                        <option value="">Select Subject...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Assign Teacher</label>
                    <select name="teacher_id" id="class_teacher_id" class="form-control" required style="height: 45px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                        <option value="">Select Teacher...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Academic Program</label>
                    <select name="program_id" id="class_program_id" class="form-control" required style="height: 45px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                        <option value="">Select Program...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Select Batch</label>
                    <select name="batch_id" id="class_batch_id" class="form-control" required style="height: 45px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                        <option value="">Select Batch...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Semester</label>
                    <select name="semester" class="form-control" required style="height: 45px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                        <?php for($i=1;$i<=8;$i++) echo "<option value='$i'>Semester $i</option>"; ?>
                    </select>
                </div>
            </div>

            <!-- Scheduling UI -->
            <div class="form-group" style="margin-top: 1.5rem; background: #f8fafc; padding: 1.5rem; border-radius: 15px; border: 1px solid #edf2f7;">
                <label style="font-weight: 700; color: #2c3e50; margin-bottom: 1rem; display: block;"><i class="far fa-clock"></i> Weekly Class Schedule</label>

                <div style="margin-bottom: 1.5rem;">
                    <p style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.8rem; color: #64748b;">Select Teaching Days:</p>
                    <div id="day-selector" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" class="day-btn" data-day="Mon">Mon</button>
                        <button type="button" class="day-btn" data-day="Tue">Tue</button>
                        <button type="button" class="day-btn" data-day="Wed">Wed</button>
                        <button type="button" class="day-btn" data-day="Thu">Thu</button>
                        <button type="button" class="day-btn" data-day="Fri">Fri</button>
                        <button type="button" class="day-btn" data-day="Sat">Sat</button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div style="position: relative;">
                        <label style="font-size: 0.9rem; font-weight: 600; color: #64748b;">Start Time:</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <i class="far fa-clock" style="position: absolute; left: 15px; color: #8BC9D4; z-index: 10;"></i>
                            <input type="text" name="start_time" id="start_time_picker" class="form-control" placeholder="09:00 AM" style="margin-top: 0.5rem; border-radius: 12px; height: 45px; border: 2px solid #f1f5f9; padding-left: 40px; background: white;">
                        </div>
                    </div>
                    <div style="position: relative;">
                        <label style="font-size: 0.9rem; font-weight: 600; color: #64748b;">End Time:</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <i class="far fa-clock" style="position: absolute; left: 15px; color: #8BC9D4; z-index: 10;"></i>
                            <input type="text" name="end_time" id="end_time_picker" class="form-control" placeholder="10:30 AM" style="margin-top: 0.5rem; border-radius: 12px; height: 45px; border: 2px solid #f1f5f9; padding-left: 40px; background: white;">
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" onclick="closeClassModal()" class="btn-secondary" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; height: 50px; border-radius: 12px; padding: 0 30px;">Cancel</button>
                <button type="submit" class="btn-primary" style="height: 50px; border-radius: 12px; font-weight: 600; padding: 0 30px; box-shadow: 0 5px 15px rgba(139, 201, 212, 0.4);">Save Class</button>
            </div>
        </form>
    </div>
</div>

<style>
.day-btn { padding: 8px 15px; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer; background: white; font-size: 0.85rem; font-weight: 600; transition: all 0.2s; color: #64748b; }
.day-btn:hover { border-color: #8BC9D4; background: #f1f5f9; }
.day-btn.selected { background: #8BC9D4; color: white; border-color: #8BC9D4; box-shadow: 0 4px 10px rgba(139, 201, 212, 0.3); }
.schedule-badge { font-size: 0.8rem; background: #ebf8ff; padding: 5px 12px; border-radius: 20px; color: #2b6cb0; font-weight: 500; }
.action-icon-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.modal-overlay { animation: fadeIn 0.3s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* Custom Flatpickr adjustments */
.flatpickr-calendar { border-radius: 15px !important; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; border: none !important; }
.flatpickr-time input:hover, .flatpickr-time .flatpickr-am-pm:hover { background: #f0f9fa !important; }
</style>

<script>
    let startTimePicker, endTimePicker;

    document.addEventListener('DOMContentLoaded', function() {
        loadInitialData();

        // Initialize Flatpickr for Time Selection
        startTimePicker = flatpickr("#start_time_picker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            defaultDate: "09:00",
            time_24hr: true
        });

        endTimePicker = flatpickr("#end_time_picker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            defaultDate: "10:30",
            time_24hr: true
        });

        document.querySelectorAll(".day-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                this.classList.toggle("selected");
                updateScheduleString();
            });
        });

        document.getElementById("class-form").onsubmit = function(e) {
            e.preventDefault();
            updateScheduleString();
            const btn = this.querySelector('button[type="submit"]');
            const action = document.getElementById("form-action").value;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            fetch(`api/manage_classes.php?action=${action}`, { method: "POST", body: new FormData(this) })
                .then(r => r.json()).then(data => {
                    btn.disabled = false;
                    btn.innerHTML = 'Save Class';
                    if (data.success) {
                        alert(data.message);
                        closeClassModal();
                        loadClasses();
                    } else alert(data.message);
                });
        };
    });

    function showAddClassModal() {
        document.getElementById("modal-title").innerHTML = `<i class="fas fa-plus-circle" style="color: #8BC9D4;"></i> Create New Class`;
        document.getElementById("form-action").value = "add";
        document.getElementById("class-id").value = "";
        document.getElementById("class-form").reset();
        startTimePicker.setDate("09:00");
        endTimePicker.setDate("10:30");
        document.querySelectorAll(".day-btn").forEach(btn => btn.classList.remove("selected"));
        document.getElementById("class-modal").style.display = "flex";
    }

    function closeClassModal() {
        document.getElementById("class-modal").style.display = "none";
    }

    function updateScheduleString() {
        const selectedDays = Array.from(document.querySelectorAll(".day-btn.selected")).map(btn => btn.dataset.day);
        const startTime = document.getElementById("start_time_picker").value;
        const endTime = document.getElementById("end_time_picker").value;
        if (selectedDays.length > 0) {
            document.getElementById("final_schedule").value = `${selectedDays.join(", ")} | ${startTime} - ${endTime}`;
        } else {
            document.getElementById("final_schedule").value = "";
        }
    }

    function loadInitialData() {
        // Programs
        fetch("api/get_programs.php").then(r => r.json()).then(data => {
            const select = document.getElementById("class_program_id");
            select.innerHTML = '<option value="">Select Program...</option>';
            data.programs.forEach(p => {
                const opt = document.createElement("option");
                opt.value = p.id;
                opt.textContent = p.program_name;
                select.appendChild(opt);
            });
        });
        // Subjects
        fetch("api/manage_subjects.php?action=list").then(r => r.json()).then(data => {
            const select = document.getElementById("class_subject_id");
            select.innerHTML = '<option value="">Select Subject...</option>';
            data.subjects.forEach(s => {
                const opt = document.createElement("option");
                opt.value = s.id;
                opt.textContent = `${s.subject_code} - ${s.subject_name}`;
                select.appendChild(opt);
            });
        });
        // Batches
        fetch("api/manage_batches.php?action=list").then(r => r.json()).then(data => {
            const select = document.getElementById("class_batch_id");
            select.innerHTML = '<option value="">Select Batch...</option>';
            data.batches.forEach(b => {
                const opt = document.createElement("option");
                opt.value = b.id;
                opt.textContent = b.batch_name;
                select.appendChild(opt);
            });
        });
        // Teachers
        fetch("api/manage_users.php?action=list&role=teacher").then(r => r.json()).then(data => {
            const select = document.getElementById("class_teacher_id");
            select.innerHTML = '<option value="">Select Teacher...</option>';
            data.users.forEach(u => {
                const opt = document.createElement("option");
                opt.value = u.id;
                opt.textContent = u.full_name;
                select.appendChild(opt);
            });
        });
        loadClasses();
    }

    function loadClasses() {
        const tbody = document.getElementById("classes-list-full");
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 3rem;"><i class="fas fa-circle-notch fa-spin" style="font-size: 2rem; color: #8BC9D4;"></i></td></tr>';

        fetch("api/get_classes.php")
            .then(r => r.json())
            .then(data => {
                tbody.innerHTML = "";
                if (data.classes && data.classes.length > 0) {
                    data.classes.forEach(c => {
                        const tr = document.createElement('tr');
                        tr.style.borderBottom = "1px solid #f1f5f9";
                        tr.innerHTML = `
                            <td style="padding: 1.2rem;"><strong style="color: #2c3e50;">${c.class_code}</strong></td>
                            <td style="font-weight: 500; color: #475569;">${c.subject_name || c.class_name}</td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; color: #2d3748;">${c.teacher_name || 'N/A'}</span>
                                    <span style="font-size: 0.7rem; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <i class="fas fa-user-shield" style="font-size: 0.6rem;"></i> ${c.teacher_role === 'admin' ? 'Administrator' : 'Subject Teacher'}
                                    </span>
                                </div>
                            </td>
                            <td><span class="status-badge" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">${c.batch_name || 'N/A'} / Sem ${c.semester}</span></td>
                            <td><span class="schedule-badge"><i class="far fa-clock"></i> ${c.schedule}</span></td>
                            <td style="text-align: center;">
                                <button class="action-icon-btn" onclick='editClass(${JSON.stringify(c).replace(/'/g, "&apos;")})' style="color: #8BC9D4; background: #f0f7f8; border: none; width: 35px; height: 35px; border-radius: 10px; cursor: pointer; margin-right: 8px;"><i class="fas fa-edit"></i></button>
                                <button class="action-icon-btn" onclick="deleteClass(${c.id})" style="color: #e74c3c; background: #fde8e8; border: none; width: 35px; height: 35px; border-radius: 10px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = "<tr><td colspan=6 style=\"text-align:center; padding: 4rem; color: #999;\">No classes registered.</td></tr>";
                }
            });
    }

    function editClass(c) {
        document.getElementById("modal-title").innerHTML = `<i class="fas fa-edit" style="color: #8BC9D4;"></i> Edit Class: ${c.class_code}`;
        document.getElementById("form-action").value = "update";
        document.getElementById("class-id").value = c.id;

        const form = document.getElementById("class-form");
        form.class_code.value = c.class_code;
        form.subject_id.value = c.subject_id || "";
        form.teacher_id.value = c.teacher_id || "";
        form.program_id.value = c.program_id || "";
        form.batch_id.value = c.batch_id || "";
        form.semester.value = c.semester;

        startTimePicker.setDate(c.start_time || "09:00");
        endTimePicker.setDate(c.end_time || "10:30");

        document.getElementById("final_schedule").value = c.schedule;

        // Reset and set days
        document.querySelectorAll(".day-btn").forEach(btn => btn.classList.remove("selected"));
        if (c.schedule) {
            const daysPart = c.schedule.split("|")[0];
            const days = daysPart.split(",").map(d => d.trim());
            document.querySelectorAll(".day-btn").forEach(btn => {
                if (days.includes(btn.dataset.day)) btn.classList.add("selected");
            });
        }

        document.getElementById("class-modal").style.display = "flex";
    }

    function deleteClass(id) {
        if (!confirm("Delete this class?")) return;
        const fd = new FormData(); fd.append("action", "delete"); fd.append("id", id);
        fetch("api/manage_classes.php?action=delete", { method: "POST", body: fd })
            .then(r => r.json()).then(data => { if (data.success) loadClasses(); else alert(data.message); });
    }
</script>
