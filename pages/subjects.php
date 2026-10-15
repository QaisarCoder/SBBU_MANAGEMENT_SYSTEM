<div class="animate__animated animate__fadeIn">
    <!-- Header Hero Section -->
    <div class="card-panel" style="background: linear-gradient(135deg, #7FAFB7 0%, #638d93 100%); color: white; margin-bottom: 2rem; border: none; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 20px rgba(127, 175, 183, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 1.8rem;"><i class="fas fa-book-open"></i> Academic Subjects</h2>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Manage curriculum and course definitions across programs.</p>
            </div>
            <button class="btn-primary" onclick="showAddSubjectModal()" style="background: white; color: #6aafbc; border: none; width: auto; padding: 12px 30px; border-radius: 30px; font-weight: 700; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <i class="fas fa-plus-circle"></i> Add New Subject
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card-panel animate__animated animate__fadeInUp" style="padding: 0; overflow: hidden; border-radius: 20px; border: 1px solid #eee;">
        <div style="padding: 1.5rem; background: #fafafa; border-bottom: 1px solid #eee;">
            <h3 style="margin: 0; font-size: 1.1rem; color: #444;"><i class="fas fa-list-ul"></i> Subject Directory</h3>
        </div>
        <div class="table-container" style="box-shadow: none; border-radius: 0;">
            <table style="width: 100%;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 1.2rem;">Code</th>
                        <th>Subject Name</th>
                        <th>Program</th>
                        <th>Semester</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="subjects-table-body">
                    <!-- Loaded via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Subject Modal -->
<div id="subject-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card-panel animate__animated animate__zoomIn" style="width: 95%; max-width: 500px; border-radius: 25px; padding: 2rem; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 id="modal-title" style="margin: 0; color: #2c3e50;"><i class="fas fa-edit" style="color: #8BC9D4;"></i> Add New Subject</h3>
            <button onclick="closeSubjectModal()" style="background: #f1f5f9; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; color: #64748b; transition: 0.2s;"><i class="fas fa-times"></i></button>
        </div>
        <form id="subject-form">
            <input type="hidden" name="id" id="subject_id_hidden">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Subject Code</label>
                <input type="text" name="subject_code" class="form-control" placeholder="e.g. CS-101" required style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Subject Name</label>
                <input type="text" name="subject_name" class="form-control" placeholder="e.g. Intro to Computing" required style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Academic Program</label>
                <select name="program_id" id="modal_subject_program" class="form-control" required style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                    <option value="">Select Program</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Semester</label>
                <select name="semester" class="form-control" required style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                    <?php for($i=1; $i<=8; $i++) echo "<option value='$i'>Semester $i</option>"; ?>
                </select>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="closeSubjectModal()" class="btn-secondary" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; height: 50px; border-radius: 12px;">Cancel</button>
                <button type="submit" class="btn-primary" style="height: 50px; border-radius: 12px; font-weight: 600; box-shadow: 0 5px 15px rgba(139, 201, 212, 0.4);">Save Subject</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSubjects();
    loadProgramsForSubject();

    document.getElementById('subject-form').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const action = data.id ? 'update' : 'add';

        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch(`api/manage_subjects.php?action=${action}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        }).then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = 'Save Subject';
            if (res.success) {
                closeSubjectModal();
                loadSubjects();
            } else {
                alert('Error: ' + res.message);
            }
        });
    };
});

function loadSubjects() {
    const tbody = document.getElementById('subjects-table-body');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 3rem;"><i class="fas fa-circle-notch fa-spin" style="font-size: 2rem; color: #8BC9D4;"></i></td></tr>';

    fetch('api/manage_subjects.php?action=list')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                tbody.innerHTML = '';
                if(data.subjects.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 3rem; color: #999;">No subjects found.</td></tr>';
                    return;
                }
                data.subjects.forEach(sub => {
                    const tr = document.createElement('tr');
                    tr.style.borderBottom = "1px solid #f1f5f9";
                    tr.innerHTML = `
                        <td style="padding: 1.2rem;"><span style="background: #ebf8ff; color: #2b6cb0; padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 0.85rem;">${sub.subject_code}</span></td>
                        <td><strong style="color: #2c3e50;">${sub.subject_name}</strong></td>
                        <td><span style="color: #64748b; font-size: 0.9rem;">${sub.program_name}</span></td>
                        <td><span class="status-badge" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">Sem ${sub.semester}</span></td>
                        <td style="text-align: center;">
                            <button class="action-icon-btn" onclick='editSubject(${JSON.stringify(sub).replace(/'/g, "&apos;")})' style="color: #8BC9D4; background: #f0f7f8; border: none; width: 35px; height: 35px; border-radius: 10px; cursor: pointer; margin-right: 8px;"><i class="fas fa-edit"></i></button>
                            <button class="action-icon-btn" onclick="deleteSubject(${sub.id})" style="color: #e74c3c; background: #fde8e8; border: none; width: 35px; height: 35px; border-radius: 10px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        });
}

function loadProgramsForSubject() {
    fetch('api/get_programs.php').then(r => r.json()).then(data => {
        const select = document.getElementById('modal_subject_program');
        data.programs.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.program_name;
            select.appendChild(opt);
        });
    });
}

function showAddSubjectModal() {
    document.getElementById('modal-title').innerHTML = '<i class="fas fa-plus-circle" style="color: #8BC9D4;"></i> Add New Subject';
    document.getElementById('subject-form').reset();
    document.getElementById('subject_id_hidden').value = '';
    document.getElementById('subject-modal').style.display = 'flex';
}

function editSubject(sub) {
    document.getElementById('modal-title').innerHTML = '<i class="fas fa-edit" style="color: #8BC9D4;"></i> Edit Subject';
    document.getElementById('subject_id_hidden').value = sub.id;
    const form = document.getElementById('subject-form');
    form.subject_code.value = sub.subject_code;
    form.subject_name.value = sub.subject_name;
    form.program_id.value = sub.program_id;
    form.semester.value = sub.semester;
    document.getElementById('subject-modal').style.display = 'flex';
}

function closeSubjectModal() { document.getElementById('subject-modal').style.display = 'none'; }

function deleteSubject(id) {
    if (confirm('Permanently delete this subject?')) {
        fetch(`api/manage_subjects.php?action=delete&id=${id}`)
            .then(r => r.json()).then(res => { if (res.success) loadSubjects(); });
    }
}
</script>

<style>
.action-icon-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.modal-overlay { animation: fadeIn 0.3s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
