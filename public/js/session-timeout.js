/**
 * Session Timeout Handler
 * Auto logout user jika tidak ada aktivitas selama waktu tertentu
 */

class SessionTimeoutHandler {
    constructor() {
        this.timeout = 30 * 60 * 1000; // 30 menit dalam milliseconds
        this.warningTime = 5 * 60 * 1000; // 5 menit warning sebelum logout
        this.timer = null;
        this.warningTimer = null;
        this.isWarningShown = false;
        
        this.init();
    }
    
    init() {
        // Reset timer pada setiap aktivitas user
        this.resetTimer();
        
        // Event listeners untuk aktivitas user
        this.addActivityListeners();
        
        // Check session setiap menit
        setInterval(() => this.checkSession(), 60000);
    }
    
    resetTimer() {
        // Clear existing timers
        if (this.timer) clearTimeout(this.timer);
        if (this.warningTimer) clearTimeout(this.warningTimer);
        
        // Set warning timer
        this.warningTimer = setTimeout(() => {
            this.showWarning();
        }, this.timeout - this.warningTime);
        
        // Set logout timer
        this.timer = setTimeout(() => {
            this.logout();
        }, this.timeout);
    }
    
    addActivityListeners() {
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                if (!this.isWarningShown) {
                    this.resetTimer();
                }
            });
        });
    }
    
    showWarning() {
        this.isWarningShown = true;
        
        // Show warning modal
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Session Akan Berakhir',
                text: 'Session Anda akan berakhir dalam 5 menit karena tidak ada aktivitas. Klik "Lanjutkan" untuk memperpanjang session.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Logout Sekarang',
                reverseButtons: true,
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // User klik lanjutkan, reset timer
                    this.isWarningShown = false;
                    this.resetTimer();
                    
                    // Show success message
                    Swal.fire({
                        title: 'Session Diperpanjang',
                        text: 'Session Anda telah diperpanjang.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // User klik logout, logout sekarang
                    this.logout();
                }
            });
        } else {
            // Fallback jika SweetAlert tidak tersedia
            if (confirm('Session Anda akan berakhir dalam 5 menit. Klik OK untuk memperpanjang session, atau Cancel untuk logout sekarang.')) {
                this.isWarningShown = false;
                this.resetTimer();
            } else {
                this.logout();
            }
        }
    }
    
    checkSession() {
        // Check session dengan server
        fetch('/check-session', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        }).then(response => {
            if (response.status === 401) {
                // Session expired, logout
                this.logout();
            }
        }).catch(() => {
            // Error, ignore
        });
    }
    
    logout() {
        // Clear timers
        if (this.timer) clearTimeout(this.timer);
        if (this.warningTimer) clearTimeout(this.warningTimer);
        
        // Show logout message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Session Berakhir',
                text: 'Session Anda telah berakhir karena tidak ada aktivitas. Anda akan diarahkan ke halaman login.',
                icon: 'info',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                window.location.href = '/login';
            });
        } else {
            alert('Session Anda telah berakhir. Anda akan diarahkan ke halaman login.');
            window.location.href = '/login';
        }
    }
}

// Initialize session timeout handler when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if user is logged in (check for auth elements)
    if (document.querySelector('[data-user-id]') || document.querySelector('.user-menu')) {
        new SessionTimeoutHandler();
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SessionTimeoutHandler;
}
