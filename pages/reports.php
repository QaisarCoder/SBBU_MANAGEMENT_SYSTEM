<div class="glass-card">
    <h3>Attendance Reports</h3>

    <div style="margin: 1rem 0;">
        <form id="report-form" style="display: flex; gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Select Class</label>
                <select id="report-class-id" class="form-control" required>
                    <option value="">Select...</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: auto;">Generate Report</button>
        </form>
    </div>

    <div class="table-container" style="display: none;" id="report-container">
        <!-- Print-only Header -->
        <div class="print-header">
            <h1>SBBU University - Attendance Report</h1>
            <p id="print-class-info"></p>
            <p>Generated on: <span id="print-date"></span></p>
        </div>

        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <h4 id="report-title">Attendance Statistics</h4>
            <button onclick="preparePrint()" class="btn-secondary" style="width: auto;"><i class="fas fa-print"></i> Print Report</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>ID</th>
                    <th style="text-align: center;">Sessions</th>
                    <th style="text-align: center; color: var(--success-color);">Present</th>
                    <th style="text-align: center; color: var(--danger-color);">Absent</th>
                    <th style="text-align: center; color: var(--warning-color);">Late</th>
                    <th style="text-align: center;">Percentage</th>
                </tr>
            </thead>
            <tbody id="report-list">
                <!-- Report data -->
            </tbody>
        </table>

        <!-- Print-only Footer -->
        <div class="print-footer">
            <div class="signature-row">
                <div class="signature-line">Course Instructor</div>
                <div class="signature-line">Head of Department</div>
                <div class="signature-line">Registrar Office</div>
            </div>
            <p style="text-align: center; margin-top: 2rem; color: #888;">&copy; 2024 SBBU Management System - Official Attendance Record</p>
        </div>
    </div>
</div>

<script>
    function preparePrint() {
        const classSelect = document.getElementById('report-class-id');
        const className = classSelect.options[classSelect.selectedIndex].text;
        document.getElementById('print-class-info').textContent = "Course: " + className;
        document.getElementById('print-date').textContent = new Date().toLocaleDateString('en-GB', {
            day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });
        window.print();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Load classes
        function loadClasses() {
            const select = document.getElementById('report-class-id');
            fetch('api/get_classes.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        select.innerHTML = '<option value="">Select Class...</option>';
                        data.classes.forEach(cls => {
                            const option = document.createElement('option');
                            option.value = cls.id;
                            option.textContent = `${cls.class_code} - ${cls.class_name}`;
                            select.appendChild(option);
                        });
                    }
                });
        }

        loadClasses();

        // Generate Report
        document.getElementById('report-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const classId = document.getElementById('report-class-id').value;
            const container = document.getElementById('report-container');
            const tbody = document.getElementById('report-list');
            const btn = this.querySelector('button');

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

            fetch(`api/get_reports.php?class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = 'Generate Report';

                    if (data.success) {
                        tbody.innerHTML = '';
                        if (data.report.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No attendance records found for this class.</td></tr>';
                        } else {
                            data.report.forEach(row => {
                                const tr = document.createElement('tr');
                                // Determine percentage color
                                let pColor = '';
                                if (row.percentage < 75) pColor = 'color: var(--danger-color); font-weight: bold;';

                                tr.innerHTML = `
                                    <td>${row.full_name}</td>
                                    <td>${row.student_code}</td>
                                    <td style="text-align: center;">${row.total_sessions}</td>
                                    <td style="text-align: center;">${row.present_count}</td>
                                    <td style="text-align: center;">${row.absent_count}</td>
                                    <td style="text-align: center;">${row.late_count}</td>
                                    <td style="text-align: center; ${pColor}">${row.percentage}%</td>
                                `;
                                tbody.appendChild(tr);
                            });
                        }
                        container.style.display = 'block';
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = 'Generate Report';
                    alert("A network error occurred.");
                });
        });
    });
</script>
