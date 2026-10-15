<div class="animate__animated animate__fadeIn">
    <!-- Header Hero Section -->
    <div class="card-panel" style="background: linear-gradient(135deg, #7FAFB7 0%, #638d93 100%); color: white; margin-bottom: 2rem; border: none; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 20px rgba(127, 175, 183, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 1.8rem;"><i class="fas fa-exclamation-circle"></i> Attendance Disputes</h2>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Review and resolve student claims for attendance corrections.</p>
            </div>
            <div id="dispute-stats" style="display: flex; gap: 1rem;">
                <!-- Loaded via JS -->
            </div>
        </div>
    </div>

    <!-- Disputes List -->
    <div id="disputes-list" style="display: flex; flex-direction: column; gap: 1rem;">
        <!-- Loaded via JS -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDisputes();
});

function loadDisputes() {
    const list = document.getElementById('disputes-list');
    list.innerHTML = '<div style="text-align: center; padding: 5rem;"><i class="fas fa-circle-notch fa-spin" style="font-size: 3rem; color: #f39c12;"></i></div>';

    fetch('api/manage_disputes.php?action=list')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                list.innerHTML = '';
                const pending = data.disputes.filter(d => d.status === 'pending').length;

                document.getElementById('dispute-stats').innerHTML = `
                    <div style="background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 15px; text-align: center; min-width: 100px;">
                        <span style="display: block; font-size: 1.5rem; font-weight: 800;">${data.disputes.length}</span>
                        <span style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px;">Total</span>
                    </div>
                    <div style="background: #fff; color: #f39c12; padding: 10px 20px; border-radius: 15px; text-align: center; min-width: 100px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                        <span style="display: block; font-size: 1.5rem; font-weight: 800;">${pending}</span>
                        <span style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px;">Pending</span>
                    </div>
                `;

                if (data.disputes.length === 0) {
                    list.innerHTML = '<div class="card-panel" style="text-align:center; padding: 4rem; color: #94a3b8; border-radius: 20px;">No disputes found in the system.</div>';
                    return;
                }

                data.disputes.forEach(d => {
                    const card = document.createElement('div');
                    card.className = 'card-panel animate__animated animate__fadeInUp';
                    card.style = `border-radius: 20px; border-left: 6px solid ${getStatusColor(d.status)}; padding: 1.5rem; display: flex; gap: 1.5rem; align-items: center; transition: 0.3s;`;

                    card.innerHTML = `
                        <div style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; flex-shrink: 0; border: 3px solid #f8fafc;">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(d.student_name)}&background=random" style="width:100%; height:100%; object-fit:cover;">
                        </div>

                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h4 style="margin: 0; font-size: 1.1rem; color: #2c3e50;">${d.student_name}</h4>
                                    <span style="font-size: 0.85rem; color: #64748b;">Requested correction for <strong>${d.class_code}</strong> on ${moment(d.attendance_date).format('MMMM D, YYYY')}</span>
                                </div>
                                <span class="status-badge status-${d.status}" style="font-size: 0.7rem; font-weight: 800; padding: 5px 12px; border-radius: 20px;">${d.status.toUpperCase()}</span>
                            </div>

                            <div style="margin-top: 1rem; background: #f8fafc; padding: 1rem; border-radius: 12px; border: 1px solid #f1f5f9; position: relative;">
                                <i class="fas fa-quote-left" style="position: absolute; top: 10px; left: 10px; color: #e2e8f0; font-size: 1.2rem;"></i>
                                <p style="margin: 0 0 0 1.5rem; font-size: 0.9rem; color: #475569; font-style: italic;">${d.reason}</p>
                            </div>

                            <div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                                <small style="color: #94a3b8;"><i class="far fa-clock"></i> Submitted ${moment(d.created_at).fromNow()}</small>

                                ${d.status === 'pending' ? `
                                    <div style="display: flex; gap: 8px;">
                                        <button onclick="resolveDispute(${d.id}, 'approved')" class="btn-primary" style="width: auto; padding: 8px 20px; font-size: 0.8rem; border-radius: 10px; background: #2ecc71; border: none;">Approve & Mark Present</button>
                                        <button onclick="resolveDispute(${d.id}, 'rejected')" class="btn-secondary" style="width: auto; padding: 8px 20px; font-size: 0.8rem; border-radius: 10px; color: #e74c3c;">Reject Claim</button>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    list.appendChild(card);
                });
            }
        });
}

function getStatusColor(status) {
    switch(status) {
        case 'pending': return '#f1c40f';
        case 'approved': return '#2ecc71';
        case 'rejected': return '#e74c3c';
        default: return '#eee';
    }
}

function resolveDispute(id, status) {
    const actionText = status === 'approved' ? 'approve' : 'reject';
    if (confirm(`Are you sure you want to ${actionText} this dispute?`)) {
        fetch('api/manage_disputes.php?action=resolve', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id, status})
        }).then(r => r.json()).then(res => {
            if (res.success) {
                loadDisputes();
            } else {
                alert('Error: ' + res.message);
            }
        });
    }
}
</script>

<style>
.card-panel:hover { transform: translateX(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
</style>
