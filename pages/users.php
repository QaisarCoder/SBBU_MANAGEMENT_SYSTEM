<div class="animate__animated animate__fadeIn">
    <!-- Manage Users Header Card -->
    <div class="card-panel" style="background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%); color: white; margin-bottom: 2rem; border: none; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 20px rgba(108, 92, 231, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 1.8rem;"><i class="fas fa-user-shield"></i> User Management</h2>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Control access levels and manage credentials for all staff and students.</p>
            </div>
            <button class="btn-primary" onclick="showAddUserModal()" style="background: white; color: #6c5ce7; border: none; width: auto; padding: 12px 30px; border-radius: 30px; font-weight: 700;">
                <i class="fas fa-plus-circle"></i> Create New User
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card-panel animate__animated animate__fadeInUp" style="padding: 0; overflow: hidden; border-radius: 20px;">
        <div class="table-container" style="box-shadow: none; border-radius: 0;">
            <table style="width: 100%;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 1.2rem;">ID Code</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-list-full">
                    <!-- Loaded via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="user-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card-panel" style="width: 95%; max-width: 600px; border-radius: 25px; padding: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 id="modal-title">Add New User</h3>
            <button onclick="closeUserModal()" class="icon-btn"><i class="fas fa-times"></i></button>
        </div>
        <form id="user-form">
            <input type="hidden" name="action" id="user-action" value="add">
            <input type="hidden" name="id" id="user-id">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required class="form-control" placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label>User ID Code</label>
                    <input type="text" name="user_code" id="user-code" class="form-control" placeholder="e.g. ADM-001 or 24-BSCS-01">
                    <small style="color: #666;">Leave blank to auto-generate (for new users).</small>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="user-role-select" required class="form-control">
                        <option value="teacher">Teacher</option>
                        <option value="clerk">Clerk / Staff</option>
                        <option value="admin">Administrator</option>
                        <option value="student">Student</option>
                    </select>
                    <small style="color: #666;">Note: Admin role changes are restricted to authorized users.</small>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" name="username" id="user-username" required class="form-control" placeholder="johndoe">
                        <button type="button" onclick="generateUsername()" class="btn-secondary" style="width: auto; font-size: 0.7rem; padding: 0 10px;">Auto</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required class="form-control" placeholder="john@sbbu.edu.pk">
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Password</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="password" id="user-password" class="form-control" placeholder="Enter or generate password">
                        <button type="button" onclick="generatePassword()" class="btn-secondary" style="width: auto; padding: 0 20px;">Generate</button>
                    </div>
                    <small id="password-hint" style="color: #666; margin-top: 5px; display: block;">Leave blank when editing to keep current password.</small>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeUserModal()" class="action-btn">Cancel</button>
                <button type="submit" class="btn-primary" style="width: auto; padding: 0 40px;">Save User</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadUsers);

    function loadUsers() {
        fetch('api/manage_users.php?action=list')
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById('users-list-full');
                tbody.innerHTML = '';
                data.users.forEach(u => {
                    const tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid #f1f5f9';
                    tr.innerHTML = `
                        <td style="padding: 1.2rem; font-weight: 800; color: #1e293b; font-family: monospace; font-size: 1rem; min-width: 120px;">
                            ${u.user_code ? u.user_code : '<span style="color:#e2e8f0">NONE</span>'}
                        </td>
                        <td style="font-weight: 600; color: #1e293b;">${u.full_name}</td>
                        <td style="color: #64748b;">@${u.username}</td>
                        <td style="color: #64748b;">${u.email}</td>
                        <td><span class="role-badge ${u.role}">${u.role.toUpperCase()}</span></td>
                        <td style="text-align: right; padding-right: 1rem;">
                            <button onclick='editUser(${JSON.stringify(u)})' class="action-icon-btn" style="color: #6c5ce7; margin-right: 8px;"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteUser(${u.id})" class="action-icon-btn text-danger"><i class="fas fa-trash"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
    }

    function showAddUserModal() {
        document.getElementById('modal-title').textContent = 'Add New User';
        document.getElementById('user-action').value = 'add';
        document.getElementById('user-form').reset();
        document.getElementById('user-id').value = '';
        document.getElementById('user-modal').style.display = 'flex';
        document.getElementById('password-hint').style.display = 'none';
        document.getElementById('user-password').required = true;
    }

    function editUser(u) {
        document.getElementById('modal-title').textContent = 'Edit User: ' + u.full_name;
        document.getElementById('user-action').value = 'update';
        document.getElementById('user-id').value = u.id;

        const form = document.getElementById('user-form');
        form.full_name.value = u.full_name;
        form.user_code.value = u.user_code || '';
        form.username.value = u.username;
        form.email.value = u.email;
        form.role.value = u.role;
        form.password.value = '';

        document.getElementById('user-password').required = false;
        document.getElementById('password-hint').style.display = 'block';
        document.getElementById('user-modal').style.display = 'flex';
    }

    function closeUserModal() {
        document.getElementById('user-modal').style.display = 'none';
    }

    function generateUsername() {
        const name = document.querySelector('input[name="full_name"]').value;
        const role = document.getElementById('user-role-select').value;
        if (!name) return;

        const cleanName = name.toLowerCase().replace(/\s+/g, '_');
        document.getElementById('user-username').value = role + '_' + cleanName;
    }

    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let pass = "";
        for (let i = 0; i < 12; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        document.getElementById('user-password').value = pass;

        // Copy to clipboard
        navigator.clipboard.writeText(pass);
        alert("Password generated and copied to clipboard: " + pass);
    }

    document.getElementById('user-form').onsubmit = function(e) {
        e.preventDefault();
        const action = document.getElementById('user-action').value;
        const fd = new FormData(this);

        fetch('api/manage_users.php?action=' + action, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeUserModal();
                    loadUsers();
                } else alert(data.message);
            });
    }

    function deleteUser(id) {
        if (!confirm("Are you sure? This will remove all access for this user.")) return;
        const fd = new FormData();
        fd.append('id', id);
        fetch('api/manage_users.php?action=delete', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) loadUsers();
                else alert(data.message);
            });
    }
</script>

<style>
.role-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}
.role-badge.admin { background: #fee2e2; color: #ef4444; }
.role-badge.teacher { background: #dcfce7; color: #22c55e; }
.role-badge.clerk, .role-badge.staff { background: #fef9c3; color: #ca8a04; }
.role-badge.student { background: #e0f2fe; color: #0ea5e9; }
</style>