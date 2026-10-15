<div class="animate__animated animate__fadeIn">
    <!-- Page Heading -->
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #2c3e50; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-book"></i> Programs Management
    </h1>

    <div class="content-split-layout">
        <!-- Add/Edit Program Column -->
        <div class="left-column">
            <div class="card-panel animate__animated animate__fadeInLeft" style="border-left: 5px solid var(--primary-color);">
                <div class="card-header">
                    <h3 id="form-title"><i class="fas fa-plus-circle"></i> Add Academic Program</h3>
                </div>
                <div class="card-body">
                    <form id="program-form" style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <input type="hidden" name="action" id="program-action" value="add">
                        <input type="hidden" name="id" id="program-id">
                        <div class="form-group">
                            <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: #2d3748;">Program Code</label>
                            <input type="text" name="program_code" id="program_code" class="form-control" required placeholder="e.g. BSCS" style="height: 45px; border-radius: 10px;">
                            <small style="color: #4a5568; margin-top: 5px; display: block;">Unique identifier for the department</small>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: #2d3748;">Full Program Name</label>
                            <input type="text" name="program_name" id="program_name" class="form-control" required placeholder="e.g. BS Computer Science" style="height: 45px; border-radius: 10px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 600; margin-bottom: 0.5rem; display: block; color: #2d3748;">Assign Teacher</label>
                            <select name="assigned_teacher_id" id="teacher-select" aria-label="Assign Teacher" class="form-control" style="height: 45px; border-radius: 10px;">
                                <option value="">Select Teacher...</option>
                            </select>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" id="submit-btn" class="btn-primary" style="flex: 2; height: 50px; border-radius: 30px; font-size: 1rem; margin-top: 1rem;">
                                <i class="fas fa-plus"></i> Create Program
                            </button>
                            <button type="button" id="cancel-edit" onclick="resetProgramForm()" style="display: none; flex: 1; height: 50px; border-radius: 30px; font-size: 1rem; margin-top: 1rem; background: #f1f5f9; color: #64748b; border: none; cursor: pointer;">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vision/Mission Sidebar (Reusing dashboard style) -->
            <div class="card-panel animate__animated animate__fadeInLeft" style="margin-top: 2rem; background: #1e293b; color: white; padding: 1.5rem; border: none; animation-delay: 0.2s;">
                <h4 style="color: white; margin-bottom: 1rem;"><i class="fas fa-info-circle"></i> Academic Setup</h4>
                <p style="font-size: 0.9rem; opacity: 0.95; line-height: 1.6; color: #ffffff;">Programs are the foundation of your university structure. Each program can have multiple students and classes across various semesters.</p>
            </div>
        </div>

        <!-- Programs List Column -->
        <div class="right-column" style="flex: 1.5;">
            <div class="card-panel animate__animated animate__fadeInRight">
                <div class="card-header">
                    <h3><i class="fas fa-list-alt"></i> Available Programs</h3>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-container" style="box-shadow: none; border-radius: 0;">
                        <table style="width: 100%;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th>Code</th>
                                    <th>Program Name</th>
                                    <th>Assigned Teacher</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="programs-list-full">
                                <tr><td colspan="4" style="text-align:center; padding: 3rem;"><div class="skeleton-loader"></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#programs-list-full td { padding: 1.2rem 1rem; vertical-align: middle; }
#programs-list-full tr:hover { background: #f8fafc; }

.program-code-badge {
    background: #ebf8ff;
    color: #2b6cb0;
    font-weight: 800;
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 0.85rem;
}

.subject-badge {
    display: inline-block;
    background: #e2e8f0;
    color: #4a5568;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    margin-right: 5px;
    margin-top: 5px;
}

.action-icon-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
</style>

<script>
    function loadInitialData() {
        // Load teachers for assignment
        fetch("api/manage_users.php?action=list&role=teacher")
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById("teacher-select");
                if (data.users) {
                    data.users.forEach(u => {
                        const opt = document.createElement("option");
                        opt.value = u.id;
                        opt.textContent = `${u.full_name} (@${u.username})`;
                        select.appendChild(opt);
                    });
                }
            });
        loadPrograms();
    }

    function loadPrograms() {
        const tbody = document.getElementById("programs-list-full");

        fetch("api/get_programs.php")
            .then(r => r.json())
            .then(data => {
                tbody.innerHTML = "";
                if(data.programs && data.programs.length > 0) {
                    data.programs.forEach(p => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td><span class="program-code-badge">${p.program_code}</span></td>
                            <td style="font-weight: 600;">
                                ${p.program_name}
                                <div style="margin-top: 5px;">
                                    ${p.subjects.map(s => `<span class="subject-badge">${s}</span>`).join('')}
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-chalkboard-teacher" style="color: #cbd5e0;"></i>
                                    <span style="color: #4a5568; font-size: 0.9rem;">${p.teacher_name || '<em style="color:#a0aec0">Unassigned</em>'}</span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <button class="action-icon-btn" aria-label="Edit Program" onclick='editProgram(${JSON.stringify(p).replace(/'/g, "&apos;")})' style="color: #8BC9D4; background: #f0f7f8; border: none; width: 35px; height: 35px; border-radius: 10px; cursor: pointer;"><i class="fas fa-edit"></i></button>
                                    <button class="action-icon-btn" aria-label="Delete Program" onclick="deleteProgram(${p.id})" style="color: #e74c3c; background: #fde8e8; border: none; width: 35px; height: 35px; border-radius: 10px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = "<tr><td colspan=4 style=\"text-align:center; padding: 4rem; color: #999;\">No programs defined yet.</td></tr>";
                }
            });
    }

    function editProgram(p) {
        document.getElementById("form-title").innerHTML = `<i class="fas fa-edit"></i> Edit Academic Program`;
        document.getElementById("program-action").value = "update";
        document.getElementById("program-id").value = p.id;
        document.getElementById("program_code").value = p.program_code;
        document.getElementById("program_name").value = p.program_name;
        document.getElementById("teacher-select").value = p.assigned_teacher_id || "";

        const btn = document.getElementById("submit-btn");
        btn.innerHTML = '<i class="fas fa-save"></i> Update Program';
        document.getElementById("cancel-edit").style.display = "block";

        // Scroll to form
        document.querySelector(".left-column").scrollIntoView({ behavior: 'smooth' });
    }

    function resetProgramForm() {
        document.getElementById("form-title").innerHTML = `<i class="fas fa-plus-circle"></i> Add Academic Program`;
        document.getElementById("program-form").reset();
        document.getElementById("program-action").value = "add";
        document.getElementById("program-id").value = "";

        const btn = document.getElementById("submit-btn");
        btn.innerHTML = '<i class="fas fa-plus"></i> Create Program';
        document.getElementById("cancel-edit").style.display = "none";
    }

    function deleteProgram(id) {
        if (!confirm("Are you sure? Deleting a program may affect students and classes linked to it.")) return;
        const formData = new FormData();
        formData.append("action", "delete");
        formData.append("id", id);
        fetch("api/manage_programs.php", { method: "POST", body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) loadPrograms();
                else alert(data.message);
            });
    }

    document.getElementById("program-form").addEventListener("submit", function(e) {
        e.preventDefault();

        const btn = document.getElementById("submit-btn");
        const action = document.getElementById("program-action").value;
        const loadingText = action === 'add' ? 'Creating...' : 'Updating...';
        const defaultText = action === 'add' ? '<i class="fas fa-plus"></i> Create Program' : '<i class="fas fa-save"></i> Update Program';

        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;

        fetch("api/manage_programs.php", { method: "POST", body: new FormData(this) })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = defaultText;

                if (data.success) {
                    if (action === 'update') resetProgramForm();
                    else this.reset();
                    loadPrograms();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = defaultText;
            });
    });

    loadInitialData();
</script>
