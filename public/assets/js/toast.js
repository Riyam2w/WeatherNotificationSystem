
const Toast = {
    init() {
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
    },

    show(message, type = 'info', duration = 3000) {
        this.init();
        const container = document.getElementById('toast-container');

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        const icons = {
            success: '✅',
            error: '❌',
            info: 'ℹ️',
            warning: '⚠️'
        };

        toast.innerHTML = `
            <span class="toast-icon">${icons[type] || '✨'}</span>
            <span class="toast-message">${message}</span>
        `;

        container.appendChild(toast);

        // Auto remove
        const removeTimeout = setTimeout(() => {
            this.hide(toast);
        }, duration);

        // Remove on click
        toast.addEventListener('click', () => {
            clearTimeout(removeTimeout);
            this.hide(toast);
        });
    },

    hide(toast) {
        toast.classList.add('toast-fade-out');
        toast.addEventListener('animationend', () => {
            toast.remove();
        });
    },

    success(message, duration) { this.show(message, 'success', duration); },
    error(message, duration) { this.show(message, 'error', duration); },
    info(message, duration) { this.show(message, 'info', duration); },
    warning(message, duration) { this.show(message, 'warning', duration); }
};

// Global polyfill for alert if desired, or just use Toast.show
window.softAlert = (message, type = 'info') => Toast.show(message, type);
