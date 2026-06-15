<!-- Panel Notifikasi Pengingat Melayang (Glassmorphism) -->
<div id="reminders-popup-panel">
    <!-- Header -->
    <div class="reminders-header">
        <div class="d-flex align-items-center gap-2">
            <div class="bell-icon-wrapper">
                <i class="mdi mdi-bell-ring-outline bell-ring-animation text-white"></i>
            </div>
            <div>
                <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.95rem; letter-spacing: -0.2px;">Tindak Lanjut Tertunda</h6>
                <span class="text-muted" style="font-size: 0.75rem;" id="reminders-subtitle">Memerlukan tindakan Anda</span>
            </div>
        </div>
        <button type="button" class="btn-close-custom" id="reminders-close-btn" title="Tutup">
            <i class="mdi mdi-close"></i>
        </button>
    </div>

    <!-- Body (Scrollable List) -->
    <div class="reminders-body" id="reminders-list-container">
        <!-- Dynamic content will be injected here -->
        <div class="d-flex justify-content-center py-4">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
        </div>
    </div>
</div>

<!-- Tombol Lonceng Melayang (Saat Panel Ditutup) -->
<button type="button" id="reminders-bell-toggle" class="d-none">
    <i class="mdi mdi-bell-outline"></i>
    <span class="badge rounded-pill bg-danger shadow-sm" id="reminders-badge-count">0</span>
</button>

<style>
    /* ===== FLOATING PANEL STYLE ===== */
    #reminders-popup-panel {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 390px;
        max-width: calc(100vw - 48px);
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 2px solid #1a3a5c;
        border-radius: 20px;
        box-shadow: 0 15px 45px rgba(26, 58, 92, 0.22), inset 0 0 0 1px rgba(255, 255, 255, 0.7);
        z-index: 1060;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease;
        transform: translateY(150%) scale(0.95);
        opacity: 0;
        pointer-events: none;
    }

    #reminders-popup-panel.show {
        transform: translateY(0) scale(1);
        opacity: 1;
        pointer-events: auto;
    }

    /* ===== PANEL HEADER ===== */
    .reminders-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: rgba(26, 58, 92, 0.04);
        border-bottom: 2px solid #1a3a5c;
    }

    .bell-icon-wrapper {
        background: #1a3a5c;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 8px rgba(26, 58, 92, 0.3);
    }

    .bell-ring-animation {
        animation: ring-bell 2.5s ease infinite;
        font-size: 1.15rem;
    }

    @keyframes ring-bell {
        0%, 100% { transform: rotate(0); }
        10%, 30%, 50%, 70%, 90% { transform: rotate(10deg); }
        20%, 40%, 60%, 80% { transform: rotate(-10deg); }
    }

    .btn-close-custom {
        background: rgba(0, 0, 0, 0.05);
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a3a5c;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-close-custom:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        transform: rotate(90deg) scale(1.05);
    }

    .btn-close-custom:active {
        transform: scale(0.9) rotate(90deg) !important;
    }

    /* ===== PANEL BODY & ITEMS ===== */
    .reminders-body {
        padding: 12px 16px 20px;
        max-height: 360px;
        overflow-y: auto;
    }

    .reminders-body::-webkit-scrollbar {
        width: 6px;
    }
    .reminders-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .reminders-body::-webkit-scrollbar-thumb {
        background: rgba(26, 58, 92, 0.2);
        border-radius: 10px;
    }
    .reminders-body::-webkit-scrollbar-thumb:hover {
        background: rgba(26, 58, 92, 0.4);
    }

    .reminder-item {
        background: #ffffff;
        border: 2px solid rgba(26, 58, 92, 0.12);
        border-radius: 14px;
        padding: 14px;
        margin-top: 10px;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s, box-shadow 0.25s;
    }

    /* Accent Left Borders for urgency mapping */
    .reminder-item.future-item {
        border-left: 5px solid #1a3a5c;
    }

    .reminder-item.overdue-item {
        border-left: 5px solid #ef4444;
    }

    .reminder-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(26, 58, 92, 0.12);
    }

    .reminder-item.future-item:hover {
        border-color: #1a3a5c;
    }

    .reminder-item.overdue-item:hover {
        border-color: #ef4444;
    }

    .reminder-meta {
        font-size: 0.75rem;
        font-weight: 800;
        color: #1a3a5c;
        margin-bottom: 6px;
    }

    .reminder-text {
        font-size: 0.85rem;
        color: #1f2937;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ===== BADGES ===== */
    .countdown-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 800;
        padding: 7px 12px;
        border-radius: 8px;
        width: 100%;
        margin-bottom: 12px;
        border: 2px solid transparent;
        transition: all 0.3s;
    }

    .countdown-badge.future {
        background: rgba(59, 130, 246, 0.08);
        color: #1e40af;
        border-color: rgba(59, 130, 246, 0.25);
    }

    .countdown-badge.overdue {
        background: rgba(239, 68, 68, 0.08);
        color: #dc2626;
        border-color: rgba(239, 68, 68, 0.25);
        animation: pulse-overdue-border 2s infinite;
    }

    @keyframes pulse-overdue-border {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.25); }
        50% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
    }

    /* ===== ACTION BUTTON ===== */
    .btn-tindak-lanjut {
        background: #1a3a5c;
        color: white;
        border: 2px solid #1a3a5c;
        border-radius: 8px;
        width: 100%;
        padding: 8px;
        font-size: 0.82rem;
        font-weight: 800;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-tindak-lanjut:hover {
        background: #2d6a9f;
        border-color: #2d6a9f;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(45, 106, 159, 0.3);
    }

    .btn-tindak-lanjut:active {
        transform: scale(0.96) !important;
    }

    /* ===== TOGGLE BELL STYLE ===== */
    #reminders-bell-toggle {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: #1a3a5c;
        color: white;
        border: 2px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        box-shadow: 0 4px 20px rgba(26, 58, 92, 0.4);
        z-index: 1059;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        transform: scale(0.5);
        opacity: 0;
        pointer-events: none;
    }

    #reminders-bell-toggle:hover {
        background: #2d6a9f;
        transform: scale(1.08) !important;
        box-shadow: 0 6px 22px rgba(45, 106, 159, 0.5);
    }

    #reminders-bell-toggle:active {
        transform: scale(0.94) !important;
    }

    #reminders-bell-toggle.show {
        transform: scale(1);
        opacity: 1;
        pointer-events: auto;
        display: flex !important;
        animation: pulse-ring 2.5s infinite;
    }

    @keyframes pulse-ring {
        0% {
            box-shadow: 0 0 0 0 rgba(26, 58, 92, 0.5), 0 4px 20px rgba(26, 58, 92, 0.4);
        }
        70% {
            box-shadow: 0 0 0 15px rgba(26, 58, 92, 0), 0 4px 20px rgba(26, 58, 92, 0.4);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(26, 58, 92, 0), 0 4px 20px rgba(26, 58, 92, 0.4);
        }
    }

    #reminders-badge-count {
        position: absolute;
        top: -4px;
        right: -4px;
        font-size: 0.72rem;
        padding: 4px 7px;
        border: 2px solid #1a3a5c;
        font-weight: 800;
    }

    #reminders-bell-toggle:hover #reminders-badge-count {
        border-color: #2d6a9f;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const popupPanel = document.getElementById('reminders-popup-panel');
        const bellToggle = document.getElementById('reminders-bell-toggle');
        const listContainer = document.getElementById('reminders-list-container');
        const closeBtn = document.getElementById('reminders-close-btn');
        const badgeCount = document.getElementById('reminders-badge-count');
        const subtitle = document.getElementById('reminders-subtitle');
        
        let remindersList = [];
        let timerInterval = null;

        // Fetch reminders
        fetch("{{ route('audit.my-reminders') }}")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response error');
                }
                return response.json();
            })
            .then(data => {
                remindersList = data;
                
                if (remindersList.length > 0) {
                    // Update badges
                    badgeCount.textContent = remindersList.length;
                    subtitle.textContent = `${remindersList.length} rekomendasi aktif`;
                    
                    // Render list
                    renderReminders();
                    
                    // Start countdown clock updates
                    startCountdownClock();
                    
                    // Show based on session storage status
                    const isDismissed = sessionStorage.getItem('reminders_dismissed') === 'true';
                    if (isDismissed) {
                        bellToggle.classList.remove('d-none');
                        // Use timeout to trigger CSS transitions correctly
                        setTimeout(() => bellToggle.classList.add('show'), 50);
                    } else {
                        popupPanel.classList.add('show');
                    }
                } else {
                    // No reminders, ensure elements are removed/hidden
                    popupPanel.remove();
                    bellToggle.remove();
                }
            })
            .catch(error => {
                console.error('Error fetching reminders:', error);
                if (listContainer) {
                    listContainer.innerHTML = '<div class="text-center py-3 text-danger" style="font-size:0.8rem;"><i class="mdi mdi-alert-circle-outline"></i> Gagal memuat data pengingat.</div>';
                }
            });

        // Close Panel Handler
        closeBtn.addEventListener('click', function() {
            popupPanel.classList.remove('show');
            sessionStorage.setItem('reminders_dismissed', 'true');
            
            bellToggle.classList.remove('d-none');
            setTimeout(() => bellToggle.classList.add('show'), 100);
        });

        // Reopen Panel Handler
        bellToggle.addEventListener('click', function() {
            bellToggle.classList.remove('show');
            setTimeout(() => bellToggle.classList.add('d-none'), 300);
            
            sessionStorage.removeItem('reminders_dismissed');
            popupPanel.classList.add('show');
        });

        function renderReminders() {
            let html = '';
            
            remindersList.forEach(item => {
                // Shorten recommendation description if too long
                const text = item.rekomendasi;
                
                // Determine initial urgency for left-border accent
                const targetDate = new Date(item.target_waktu + 'T23:59:59');
                const isOverdue = targetDate.getTime() - new Date().getTime() <= 0;
                const itemClass = isOverdue ? 'overdue-item' : 'future-item';
                
                html += `
                    <div class="reminder-item ${itemClass}" id="reminder-item-${item.id}">
                        <div class="reminder-meta d-flex justify-content-between align-items-center">
                            <span>ST: ${item.nomor_surat_tugas}</span>
                            <span class="text-primary fw-bold">ISS: ${item.nomor_iss}</span>
                        </div>
                        <div class="reminder-text" title="${text}">${text}</div>
                        
                        <div class="countdown-badge" id="countdown-badge-${item.id}" data-target="${item.target_waktu}">
                            <i class="mdi mdi-clock-outline"></i>
                            <span class="countdown-text">Menghitung...</span>
                        </div>
                        
                        <a href="${item.link}" class="btn-tindak-lanjut">
                            <i class="mdi mdi-clipboard-text-play-outline"></i>
                            Tindak Lanjuti
                        </a>
                    </div>
                `;
            });
            
            listContainer.innerHTML = html;
        }

        function startCountdownClock() {
            updateCountdowns(); // run immediately first
            timerInterval = setInterval(updateCountdowns, 1000);
        }

        function updateCountdowns() {
            const badges = document.querySelectorAll('.countdown-badge');
            
            badges.forEach(badge => {
                const targetStr = badge.getAttribute('data-target');
                if (!targetStr) return;

                // Target represents the end of the specified day (local time)
                const targetDate = new Date(targetStr + 'T23:59:59');
                const now = new Date();
                const diff = targetDate.getTime() - now.getTime();
                
                const textNode = badge.querySelector('.countdown-text');
                const iconNode = badge.querySelector('i');
                
                if (diff > 0) {
                    // In the future
                    badge.className = 'countdown-badge future';
                    iconNode.className = 'mdi mdi-clock-outline';
                    
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    
                    let timeString = 'Sisa: ';
                    if (days > 0) timeString += `${days}h `;
                    timeString += `${hours}j ${minutes}m ${seconds}d`;
                    
                    textNode.textContent = timeString;
                } else {
                    // Overdue
                    badge.className = 'countdown-badge overdue';
                    iconNode.className = 'mdi mdi-alert-circle-outline';
                    
                    const delay = Math.abs(diff);
                    const days = Math.floor(delay / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((delay % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((delay % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((delay % (1000 * 60)) / 1000);
                    
                    let timeString = 'Terlambat: ';
                    if (days > 0) timeString += `${days}h `;
                    timeString += `${hours}j ${minutes}m ${seconds}d`;
                    
                    textNode.textContent = timeString;
                }
            });
        }
    });
</script>
