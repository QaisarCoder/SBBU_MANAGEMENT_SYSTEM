<div class="animate__animated animate__fadeIn">
    <!-- Header Hero Section -->
    <div class="card-panel" style="background: linear-gradient(135deg, #7FAFB7 0%, #638d93 100%); color: white; margin-bottom: 2rem; border: none; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 20px rgba(127, 175, 183, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 1.8rem;"><i class="fas fa-users"></i> Academic Batches</h2>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Define and organize student intake groups by year.</p>
            </div>
            <button class="btn-primary" onclick="showAddBatchModal()" style="background: white; color: #ff9a44; border: none; width: auto; padding: 12px 30px; border-radius: 30px; font-weight: 700; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <i class="fas fa-plus-circle"></i> Create New Batch
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card-panel animate__animated animate__fadeInUp" style="padding: 0; overflow: hidden; border-radius: 20px; border: 1px solid #eee;">
        <div style="padding: 1.5rem; background: #fafafa; border-bottom: 1px solid #eee;">
            <h3 style="margin: 0; font-size: 1.1rem; color: #444;"><i class="fas fa-layer-group"></i> Active Batches</h3>
        </div>
        <div class="table-container" style="box-shadow: none; border-radius: 0;">
            <table style="width: 100%;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 1.2rem;">Batch Name</th>
                        <th>Program</th>
                        <th>Duration Period</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="batches-table-body">
                    <!-- Loaded via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Batch Modal -->
<div id="batch-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="card-panel animate__animated animate__zoomIn" style="width: 95%; max-width: 500px; border-radius: 25px; padding: 2rem; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3 id="batch-modal-title" style="margin: 0; color: #2c3e50;"><i class="fas fa-plus-circle" style="color: #ff9a44;"></i> Create New Batch</h3>
            <button onclick="closeBatchModal()" style="background: #f1f5f9; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; color: #64748b;"><i class="fas fa-times"></i></button>
        </div>
        <form id="batch-form">
            <input type="hidden" name="id" id="batch_id_hidden">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Batch Name</label>
                <input type="text" name="batch_name" class="form-control" placeholder="e.g. Batch 2021" required style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Select Program</label>
                <select name="program_id" id="modal_batch_program" class="form-control" required style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                    <option value="">Select Program</option>
                </select>
            </div>
            <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                <div class="form-group" style="flex: 1;">
                    <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">Start Year</label>
                    <input type="number" name="start_year" class="form-control" value="<?php echo date('Y'); ?>" required style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label style="font-weight: 600; color: #64748b; margin-bottom: 0.5rem; display: block;">End Year</label>
                    <input type="number" name="end_year" class="form-control" value="<?php echo date('Y')+4; ?>" required style="height: 50px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 0 15px;">
                </div>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="closeBatchModal()" class="btn-secondary" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; height: 50px; border-radius: 12px;">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #ff9a44; border: none; height: 50px; border-radius: 12px; font-weight: 600; box-shadow: 0 5px 15px rgba(255, 154, 68, 0.4);">Save Batch</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadBatches();
    loadProgramsForBatch();

    document.getElementById('batch-form').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const action = data.id ? 'update' : 'add';

        fetch(`api/manage_batches.php?action=${action}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        }).then(r => r.json()).then(res => {
            if (res.success) {
                closeBatchModal();
                loadBatches();
            } else {
                alert('Error: ' + res.message);
            }
        });
    };
});

function loadBatches() {
    const tbody = document.getElementById('batches-table-body');
    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 3rem;"><i class="fas fa-circle-notch fa-spin" style="font-size: 2rem; color: #ff9a44;"></i></td></tr>';

    fetch('api/manage_batches.php?action=list')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                tbody.innerHTML = '';
                if(data.batches.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 3rem; color: #999;">No batches created yet.</td></tr>';
                    return;
                }
                data.batches.forEach(b => {
                    const tr = document.createElement('tr');
                    tr.style.borderBottom = "1px solid #f1f5f9";
                    tr.innerHTML = `
                        <td style="padding: 1.2rem;"><strong style="color: #2c3e50; font-size: 1rem;">${b.batch_name}</strong></td>
                        <td><span style="color: #64748b;">${b.program_name}</span></td>
                        <td><span style="background: #fff8f0; color: #f39c12; padding: 6px 15px; border-radius: 20px; font-weight: 600; font-size: 0.85rem;"><i class="far fa-calendar-alt"></i> ${b.start_year} — ${b.end_year}</span></td>
                        <td style="text-align: center;">
                            <button class="action-icon-btn" onclick='editBatch(${JSON.stringify(b).replace(/'/g, "&apos;")})' style="color: #ff9a44; background: #fff8f0; border: none; width: 35px; height: 35px; border-radius: 10px; cursor: pointer; margin-right: 8px;"><i class="fas fa-edit"></i></button>
                            <button class="action-icon-btn" onclick="deleteBatch(${b.id})" style="color: #e74c3c; background: #fde8e8; border: none; width: 35px; height: 35px; border-radius: 10px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        });
}

function loadProgramsForBatch() {
    fetch('api/get_programs.php').then(r => r.json()).then(data => {
        const select = document.getElementById('modal_batch_program');
        data.programs.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.program_name;
            select.appendChild(opt);
        });
    });
}

function showAddBatchModal() {
    document.getElementById('batch-modal-title').innerHTML = '<i class="fas fa-plus-circle" style="color: #ff9a44;"></i> Create New Batch';
    document.getElementById('batch-form').reset();
    document.getElementById('batch_id_hidden').value = '';
    document.getElementById('batch-modal').style.display = 'flex';
}

function editBatch(b) {
    document.getElementById('batch-modal-title').innerHTML = '<i class="fas fa-edit" style="color: #ff9a44;"></i> Edit Batch';
    document.getElementById('batch_id_hidden').value = b.id;
    const form = document.getElementById('batch-form');
    form.batch_name.value = b.batch_name;
    form.program_id.value = b.program_id;
    form.start_year.value = b.start_year;
    form.end_year.value = b.end_year;
    document.getElementById('batch-modal').style.display = 'flex';
}

function closeBatchModal() { document.getElementById('batch-modal').style.display = 'none'; }

function deleteBatch(id) {
    if (confirm('Delete this batch permanently?')) {
        fetch(`api/manage_batches.php?action=delete&id=${id}`)
            .then(r => r.json()).then(res => { if (res.success) loadBatches(); });
    }
}
</script>
