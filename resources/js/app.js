import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Attach CSRF token header globally for Axios requests
const csrfMeta = document.querySelector('meta[name="csrf-token"]');
if (csrfMeta && window.axios) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content');
}

// Prevent accidental double-submission on logout form
document.addEventListener('DOMContentLoaded', () => {
    const logoutForm = document.getElementById('logout-form');
    const logoutBtn = document.getElementById('logout-btn');

    if (logoutForm && logoutBtn) {
        logoutForm.addEventListener('submit', () => {
            logoutBtn.disabled = true;
        });
    }
});
