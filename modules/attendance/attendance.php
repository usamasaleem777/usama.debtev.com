<?php
// attendance_api.php

$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    $user_id = $_SESSION['user_id'] ?? null;
    $role_id = $_SESSION['role_id'] ?? null;

    if (!$user_id || !$role_id) {
        throw new Exception("Authentication required");
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'clock_in':
            // Check existing active session
            $existing = DB::queryFirstRow("SELECT id FROM attendance 
                WHERE user_id = %i AND out_time IS NULL", $user_id);
            
            if ($existing) {
                throw new Exception("Already clocked in");
            }

            DB::insert('attendance', [
                'user_id' => $user_id,
                'role_id' => $role_id,
                'date' => DB::sqleval('CURDATE()'),
                'in_time' => DB::sqleval('NOW()')
            ]);
            
            $response = ['status' => 'success', 'message' => 'Clocked in successfully'];
            break;

        case 'clock_out':
            DB::query("UPDATE attendance SET out_time = NOW() 
                WHERE user_id = %i AND out_time IS NULL", $user_id);
            
            $response = ['status' => 'success', 'message' => 'Clocked out successfully'];
            break;

        case 'start_break':
            $break_type = $_POST['break_type'] ?? '';
            if (!in_array($break_type, ['short', 'lunch'])) {
                throw new Exception("Invalid break type");
            }

            DB::insert('breaks', [
                'user_id' => $user_id,
                'role_id' => $role_id,
                'break_type' => $break_type,
                'break_start' => DB::sqleval('NOW()')
            ]);
            
            $response = ['status' => 'success', 'message' => 'Break started'];
            break;

        case 'end_break':
            DB::query("UPDATE breaks SET break_end = NOW() 
                WHERE user_id = %i AND break_end IS NULL", $user_id);
            
            $response = ['status' => 'success', 'message' => 'Break ended'];
            break;

        case 'get_current_session':
            $attendance = DB::queryFirstRow("SELECT * FROM attendance 
                WHERE user_id = %i AND out_time IS NULL
                ORDER BY in_time DESC LIMIT 1", $user_id);

            $active_break = DB::queryFirstRow("SELECT * FROM breaks 
                WHERE user_id = %i AND break_end IS NULL
                ORDER BY break_start DESC LIMIT 1", $user_id);

            $response = [
                'status' => 'success',
                'attendance' => $attendance,
                'active_break' => $active_break
            ];
            break;

        case 'get_attendance_history':
            $history = DB::query("SELECT 
                a.*,
                (SELECT 
                    GROUP_CONCAT(CONCAT_WS('|', b.break_type, b.break_start, b.break_end) SEPARATOR ';') 
                 FROM breaks b 
                 WHERE b.user_id = a.user_id AND DATE(b.break_start) = DATE(a.in_time)
                ) AS breaks
                FROM attendance a
                WHERE a.user_id = %i
                ORDER BY a.in_time DESC 
                LIMIT 30", $user_id);

            // Process breaks into arrays
            foreach ($history as &$record) {
                $record['breaks'] = $record['breaks'] ? array_map(function($b) {
                    list($type, $start, $end) = explode('|', $b);
                    return ['type' => $type, 'start' => $start, 'end' => $end];
                }, explode(';', $record['breaks'])) : [];
            }

            $response = ['status' => 'success', 'history' => $history];
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (MeekroDBException $e) {
    $response = ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
?>
                  

<script>
    // Attendance Tracker
let attendance = {
    activeBreak: null,
    currentSession: null
};

// Initialize attendance tracker
async function initializeAttendance() {
    try {
        const response = await fetch('attendance_api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_current_session'
        });
        const data = await response.json();
        
        if (data.status === 'success') {
            attendance.currentSession = data.attendance;
            attendance.activeBreak = data.active_break;
            updateBreakButtons();
        }
    } catch (error) {
        console.error('Error initializing attendance:', error);
    }
}

// Update timer
async function updateTimer() {
    if (!attendance.currentSession) return;

    try {
        const response = await fetch('attendance_api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_current_session'
        });
        const data = await response.json();
        
        if (data.status === 'success') {
            attendance.currentSession = data.attendance;
            attendance.activeBreak = data.active_break;
            updateBreakButtons();
        }
    } catch (error) {
        console.error('Error updating timer:', error);
    }

    const startTime = new Date(attendance.currentSession.in_time);
    const now = new Date();
    const diff = now - startTime;
    
    // Subtract break durations
    if (attendance.activeBreak) {
        const breakStart = new Date(attendance.activeBreak.break_start);
        diff -= (now - breakStart);
    }

    const hours = Math.floor(diff / 1000 / 60 / 60);
    const minutes = Math.floor((diff / 1000 / 60) % 60);
    const seconds = Math.floor((diff / 1000) % 60);
    
    document.getElementById('timer').textContent = 
        `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

let timerInterval = setInterval(updateTimer, 1000);

// Break Handling
async function toggleBreak(e) {
    const breakType = e.target.classList.contains('short-break-btn') ? 'short' : 'lunch';
    
    try {
        if (!attendance.activeBreak) {
            const formData = new FormData();
            formData.append('action', 'start_break');
            formData.append('break_type', breakType);
            
            const response = await fetch('attendance_api.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.status === 'success') {
                attendance.activeBreak = { break_type: breakType };
                updateBreakButtons();
            }
        } else {
            const response = await fetch('attendance_api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=end_break'
            });
            const data = await response.json();
            
            if (data.status === 'success') {
                attendance.activeBreak = null;
                updateBreakButtons();
                updateAttendanceHistory();
            }
        }
    } catch (error) {
        console.error('Error handling break:', error);
    }
}

function updateBreakButtons() {
    const shortBreakBtn = document.querySelector('.short-break-btn');
    const lunchBreakBtn = document.querySelector('.lunch-break-btn');
    
    if (attendance.activeBreak) {
        const isActive = attendance.activeBreak.break_type === 'short' ? shortBreakBtn : lunchBreakBtn;
        isActive.textContent = `End ${attendance.activeBreak.break_type} Break`;
        isActive.classList.add('btn-danger');
        isActive.classList.remove('btn-outline-primary', 'btn-outline-danger');
    } else {
        shortBreakBtn.textContent = 'Short Break';
        lunchBreakBtn.textContent = 'Lunch Break';
        shortBreakBtn.classList.remove('btn-danger');
        lunchBreakBtn.classList.remove('btn-danger');
        shortBreakBtn.classList.add('btn-outline-primary');
        lunchBreakBtn.classList.add('btn-outline-danger');
    }
}

// Attendance History
async function updateAttendanceHistory() {
    try {
        const response = await fetch('attendance_api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=get_attendance_history'
        });
        const data = await response.json();
        
        if (data.status === 'success') {
            const historyDiv = document.getElementById('attendanceHistory');
            historyDiv.innerHTML = '';
            
            data.history.forEach(record => {
                const recordDiv = document.createElement('div');
                recordDiv.className = 'attendance-record';
                
                const totalHours = calculateTotalHours(record);
                const breaks = record.breaks ? record.breaks.split(',').map(b => {
                    const [type, start, end] = b.split('|');
                    return { type, start, end };
                }) : [];
                
                recordDiv.innerHTML = `
                    <div class="d-flex justify-content-between">
                        <span>${new Date(record.in_time).toLocaleDateString()}</span>
                        <span class="text-muted">${totalHours}</span>
                    </div>
                    ${breaks.map(b => `
                        <div class="small text-muted">
                            ${b.type} break: 
                            ${new Date(b.start).toLocaleTimeString()} - 
                            ${b.end ? new Date(b.end).toLocaleTimeString() : 'N/A'}
                        </div>
                    `).join('')}
                `;
                
                historyDiv.appendChild(recordDiv);
            });
        }
    } catch (error) {
        console.error('Error loading attendance history:', error);
    }
}

function calculateTotalHours(record) {
    if (!record.out_time) return 'In Progress';
    
    const start = new Date(record.in_time);
    const end = new Date(record.out_time);
    let diff = end - start;
    
    // Subtract breaks
    if (record.breaks) {
        record.breaks.split(',').forEach(b => {
            const [type, start, end] = b.split('|');
            if (end) {
                diff -= (new Date(end) - new Date(start));
            }
        });
    }
    
    const hours = Math.floor(diff / 1000 / 60 / 60);
    const minutes = Math.floor((diff / 1000 / 60) % 60);
    return `${hours}h ${minutes}m`;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeAttendance();
    updateAttendanceHistory();
});

// Clock out when window closes
window.addEventListener('beforeunload', async (e) => {
    if (attendance.currentSession) {
        await fetch('attendance_api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=clock_out'
        });
    }
});
</script>