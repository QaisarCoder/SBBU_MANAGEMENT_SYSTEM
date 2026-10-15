<div class="animate__animated animate__fadeIn">
    <!-- Teacher Management Header Card -->
    <div class="card-panel" style="background: linear-gradient(135deg, #0984e3 0%, #74b9ff 100%); color: white; margin-bottom: 2rem; border: none; padding: 2.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(9, 132, 227, 0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 2rem;"><i class="fas fa-chalkboard-teacher"></i> Teacher Directory</h2>
                <p style="margin: 10px 0 0 0; opacity: 0.9; font-size: 1.1rem;">Manage faculty accounts, assign teacher codes, and oversee academic staff.</p>
            </div>
            <button class="btn-primary" onclick="showAddTeacherModal()" style="background: white; color: #0984e3; border: none; width: auto; padding: 15px 35px; border-radius: 30px; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <i class="fas fa-plus-circle"></i> Add New Teacher
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card-panel" style="text-align: center; padding: 1.5rem;">
            <div style="font-size: 0.8rem; color: #636e72; font-weight: 700; text-transform: uppercase;">Total Faculty</div>
            <div id="total-teachers-count" style="font-size: 2rem; font-weight: 800; color: #2d3436; margin-top: 5px;">0</div>
        </div>
        <div class="card-panel" style="text-align: center; padding: 1.5rem;">
            <div style="font-size: 0.8rem; color: #636e72; font-weight: 700; text-transform: uppercase;">Active Courses</div>
            <div id="teacher-courses-count" style="font-size: 2rem; font-weight: 800; color: #0984e3; margin-top: 5px;">-</div>
        </div>
    </div>

    <!-- Teacher List Table -->
    <div class="card-panel animate__animated animate__fadeInUp" style="padding: 0; overflow: hidden; border-radius: 20px; border: none; box-shadow: var(--shadow-md);">
        <div class="table-container" style="box-shadow: none; border-radius: 0;">
            <table style="width: 100%;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 1.5rem;">Teacher Code</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email Address</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody id="teachers-list">
                    <tr><td colspan="6" style="text-align:center; padding: 3rem;"><div class="skeleton-loader"></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Teacher Modal -->
<div id="teacher-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card-panel animate__animated animate__zoomIn" style="width: 95%; max-width: 600px; border-radius: 25px; padding: 2.5rem; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 id="teacher-modal-title" style="margin:0; font-size: 1.5rem; color: #2d3436;">Add New Teacher</h3>
            <button onclick="closeTeacherModal()" style="background: none; border: none; font-size: 1.5rem; color: #b2bec3; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form id="teacher-form">
            <input type="hidden" name="action" id="teacher-action" value="add">
            <input type="hidden" name="id" id="teacher-id">
            <input type="hidden" name="role" value="teacher">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label style="font-weight: 700; color: #636e72; margin-bottom: 8px; display: block;">Full Name</label>
                    <input type="text" name="full_name" required class="form-control" placeholder="Prof. John Smith" style="height: 50px; border-radius: 12px; border: 2px solid #edf2f7;">
                </div>
                <div class="form-group">
                    <label style="font-weight: 700; color: #636e72; margin-bottom: 8px; display: block;">Username</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" name="username" id="teacher-username" required class="form-control" placeholder="jsmith" style="height: 50px; border-radius: 12px; border: 2px solid #edf2f7;">
                        <button type="button" onclick="autoGenerateTeacherUsername()" class="btn-secondary" style="width: auto; padding: 0 12px; border-radius: 10px; font-size: 0.75rem;">Auto</button>
                    </div>
                </div>
                <div class="form-group">
                    <label style="font-weight: 700; color: #636e72; margin-bottom: 8px; display: block;">Email Address</label>
                    <input type="email" name="email" required class="form-control" placeholder="jsmith@sbbu.edu.pk" style="height: 50px; border-radius: 12px; border: 2px solid #edf2f7;">
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label style="font-weight: 700; color: #636e72; margin-bottom: 8px; display: block;">Account Password</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="password" id="teacher-password" class="form-control" placeholder="Password (min 8 chars)" style="height: 50px; border-radius: 12px; border: 2px solid #edf2f7;">
                        <button type="button" onclick="generateTeacherPassword()" class="btn-secondary" style="width: auto; padding: 0 20px; border-radius: 12px;">Generate</button>
                    </div>
                    <small id="teacher-pass-hint" style="color: #b2bec3; margin-top: 5px; display: block;">Leave blank to keep existing password when editing.</small>
                </div>
            </div>

            <div style="margin-top: 2.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeTeacherModal()" class="action-btn" style="border-radius: 30px; padding: 12px 30px;">Cancel</button>
                <button type="submit" class="btn-primary" style="width: auto; padding: 12px 45px; border-radius: 30px; background: #0984e3; box-shadow: 0 4px 15px rgba(9, 132, 227, 0.3);">
                    <i class="fas fa-save"></i> Save Faculty Member
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadTeachers);

    function loadTeachers() {
        fetch('api/manage_users.php?action=list&role=teacher')
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById('teachers-list');
                tbody.innerHTML = '';
                document.getElementById('total-teachers-count').textContent = data.users.length;

                if (data.users.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 3rem; color: #b2bec3;">No teachers found in directory.</td></tr>';
                    return;
                }

                data.users.forEach(u => {
                    const tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid #edf2f7';
                    tr.innerHTML = `
                        <td style="padding: 1.5rem;"><span class="id-badge" style="background: #e3f2fd; color: #0984e3; border: 1px solid #bbdefb;">${u.user_code}</span></td>
                        <td style="font-weight: 700; color: #2d3436;">${u.full_name}</td>
                        <td style="color: #636e72;">@${u.username}</td>
                        <td style="color: #636e72;">${u.email}</td>
                        <td style="text-align: center;"><span class="badge" style="background: #dcfce7; color: #22c55e; position: static; padding: 5px 12px;">ACTIVE</span></td>
                        <td style="text-align: right; padding-right: 1.5rem;">
                            <button onclick='editTeacher(${JSON.stringify(u)})' class="action-icon-btn" style="color: #0984e3; margin-right: 8px;"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteTeacher(${u.id}, '${u.full_name}')" class="action-icon-btn text-danger"><i class="fas fa-trash"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
    }

    function showAddTeacherModal() {
        document.getElementById('teacher-modal-title').textContent = 'Add New Teacher';
        document.getElementById('teacher-action').value = 'add';
        document.getElementById('teacher-form').reset();
        document.getElementById('teacher-id').value = '';
        document.getElementById('teacher-modal').style.display = 'flex';
        document.getElementById('teacher-pass-hint').style.display = 'none';
        document.getElementById('teacher-password').required = true;
    }

    function editTeacher(u) {
        document.getElementById('teacher-modal-title').textContent = 'Edit Teacher: ' + u.full_name;
        document.getElementById('teacher-action').value = 'update';
        document.getElementById('teacher-id').value = u.id;

        const form = document.getElementById('teacher-form');
        form.full_name.value = u.full_name;
        form.username.value = u.username;
        form.email.value = u.email;
        form.password.value = '';

        document.getElementById('teacher-password').required = false;
        document.getElementById('teacher-pass-hint').style.display = 'block';
        document.getElementById('teacher-modal').style.display = 'flex';
    }

    function closeTeacherModal() {
        document.getElementById('teacher-modal').style.display = 'none';
    }

    function autoGenerateTeacherUsername() {
        const name = document.querySelector('#teacher-form input[name="full_name"]').value;
        if (!name) return;

        const cleanName = name.toLowerCase().trim().replace(/\s+/g, '.');
        document.getElementById('teacher-username').value = cleanName;
    }

    function generateTeacherPassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
        let pass = "";
        for (let i = 0; i < 10; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        document.getElementById('teacher-password').value = pass;

        navigator.clipboard.writeText(pass);
        alert("Teacher Password: " + pass + " (Copied to clipboard)");
    }

    document.getElementById('teacher-form').onsubmit = function(e) {
        e.preventDefault();
        const action = document.getElementById('teacher-action').value;
        const fd = new FormData(this);

        fetch('api/manage_users.php?action=' + action, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeTeacherModal();
                    loadTeachers();
                } else alert(data.message);
            });
    }

    function deleteTeacher(id, name) {
        if (!confirm("Are you sure you want to remove " + name + " from faculty? This will disable their login access.")) return;
        const fd = new FormData();
        fd.append('id', id);
        fetch('api/manage_users.php?action=delete', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) loadTeachers();
                else alert(data.message);
            });
    }
</script>
