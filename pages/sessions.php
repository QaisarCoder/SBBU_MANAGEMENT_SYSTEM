<div class="animate__animated animate__fadeIn">
    <!-- Header Hero Section -->
    <div class="card-panel" style="background: linear-gradient(135deg, #7FAFB7 0%, #638d93 100%); color: white; margin-bottom: 2rem; border: none; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 20px rgba(127, 175, 183, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 1.8rem;"><i class="fas fa-shield-alt"></i> Security & Sessions</h2>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Monitor and manage active user logins to ensure account security.</p>
            </div>
            <div style="background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 15px; text-align: center;">
                <span id="session-count" style="display: block; font-size: 1.5rem; font-weight: 800;">0</span>
                <span style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">Live Sessions</span>
            </div>
        </div>
    </div>

    <!-- Sessions Container -->
    <div id="sessions-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
        <!-- Loaded via JS -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSessions();
});

function loadSessions() {
    const grid = document.getElementById('sessions-grid');
    grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 5rem;"><i class="fas fa-circle-notch fa-spin" style="font-size: 3rem; color: #e74c3c;"></i></div>';

    fetch('api/manage_sessions.php?action=list')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                grid.innerHTML = '';
                document.getElementById('session-count').textContent = data.sessions.length;

                data.sessions.forEach(s => {
                    const isCurrent = s.session_token === '<?php echo $_SESSION["session_token"] ?? ""; ?>';
                    const browserIcon = getBrowserIcon(s.user_agent);

                    const card = document.createElement('div');
                    card.className = 'card-panel animate__animated animate__fadeInUp';
                    card.style = `border-radius: 20px; border: 1px solid ${isCurrent ? '#8BC9D4' : '#eee'}; position: relative; padding: 1.5rem; transition: 0.3s;`;
                    if(isCurrent) card.style.boxShadow = "0 8px 25px rgba(139, 201, 212, 0.2)";

                    card.innerHTML = `
                        <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem;">
                            <div style="width: 50px; height: 50px; background: ${isCurrent ? '#f0f7f8' : '#f8fafc'}; color: ${isCurrent ? '#8BC9D4' : '#64748b'}; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                <i class="${browserIcon}"></i>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0; font-size: 1.1rem; color: #2c3e50;">${s.full_name}</h4>
                                <span style="font-size: 0.8rem; color: #94a3b8;">@${s.username} • <span style="text-transform: capitalize;">${s.role}</span></span>
                            </div>
                            ${isCurrent ? '<span style="background: #e8fdf0; color: #2ecc71; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; border: 1px solid #2ecc7133;">CURRENT</span>' : ''}
                        </div>

                        <div style="background: #f8fafc; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: #475569;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span><i class="fas fa-network-wired" style="width: 20px; color: #94a3b8;"></i> IP Address:</span>
                                <span style="font-family: monospace; font-weight: 600;">${s.ip_address}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span><i class="fas fa-clock" style="width: 20px; color: #94a3b8;"></i> Last Active:</span>
                                <span style="font-weight: 600;">${moment(s.last_activity).fromNow()}</span>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <small style="color: #94a3b8; font-size: 0.7rem; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${s.user_agent}">${s.user_agent}</small>
                            ${!isCurrent ? `<button onclick="terminateSession(${s.id})" style="background: #fde8e8; color: #e74c3c; border: none; padding: 8px 15px; border-radius: 10px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: 0.2s;"><i class="fas fa-sign-out-alt"></i> Logout</button>` : ''}
                        </div>

                        ${isCurrent ? '<div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 40px; height: 3px; background: #8BC9D4; border-radius: 0 0 10px 10px;"></div>' : ''}
                    `;
                    grid.appendChild(card);
                });
            }
        });
}

function getBrowserIcon(ua) {
    ua = ua.toLowerCase();
    if(ua.includes('chrome')) return 'fab fa-chrome';
    if(ua.includes('firefox')) return 'fab fa-firefox';
    if(ua.includes('safari')) return 'fab fa-safari';
    if(ua.includes('edge')) return 'fab fa-edge';
    return 'fas fa-desktop';
}

function terminateSession(id) {
    if (confirm('Force logout this device?')) {
        fetch(`api/manage_sessions.php?action=terminate&id=${id}`)
            .then(r => r.json()).then(res => { if (res.success) loadSessions(); });
    }
}
</script>
