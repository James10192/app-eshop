'use strict';

document.addEventListener('DOMContentLoaded', () => {
    initToasts();
    initFormValidation();
    initNotifications();
    initModalLoader();
});

// --- Toasts Bootstrap : auto-dismiss après 5s ---
function initToasts() {
    document.querySelectorAll('.toast').forEach(el => {
        const toast = new bootstrap.Toast(el, { delay: 5000 });
        toast.show();
    });
}

// --- Validation formulaires HTML5 + Bootstrap ---
function initFormValidation() {
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

// --- Notifications : badge et liste ---
function initNotifications() {
    const btn = document.getElementById('btn-notifications');
    if (!btn) return;

    btn.addEventListener('click', async (e) => {
        e.preventDefault();
        await loadNotifications();
    });
}

async function loadNotifications() {
    try {
        const response = await fetch('/api/notifications', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();
        renderNotificationsModal(data);
    } catch (err) {
        console.error('Notifications fetch error:', err);
    }
}

function renderNotificationsModal(notifications) {
    const container = document.getElementById('modal-container');
    const existing = document.getElementById('modal-notifications');
    if (existing) existing.remove();

    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'modal-notifications';
    modal.setAttribute('tabindex', '-1');

    const list = notifications.length === 0
        ? '<p class="text-muted text-center py-3">Aucune notification</p>'
        : notifications.map(n => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action' + (n.lu ? '' : ' fw-semibold bg-light');

            const title = document.createElement('div');
            title.className = 'd-flex justify-content-between';
            const titleText = document.createElement('span');
            titleText.textContent = n.titre;
            const date = document.createElement('small');
            date.className = 'text-muted';
            date.textContent = n.created_at;
            title.appendChild(titleText);
            title.appendChild(date);

            const msg = document.createElement('small');
            msg.className = 'text-muted';
            msg.textContent = n.message;

            li.appendChild(title);
            li.appendChild(msg);
            return li.outerHTML;
        }).join('');

    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-bell me-2"></i>Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="list-group list-group-flush">${list}</ul>
                </div>
            </div>
        </div>`;

    container.appendChild(modal);
    new bootstrap.Modal(modal).show();
}

// --- Loader de modals dynamiques via AJAX ---
// Usage : <button data-modal-url="/commandes/1/detail">Voir</button>
function initModalLoader() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-modal-url]');
        if (!btn) return;

        e.preventDefault();
        const url = btn.dataset.modalUrl;
        if (!url) return;

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const html = await response.text();

            const container = document.getElementById('modal-container');
            const existing = document.getElementById('modal-dynamic');
            if (existing) existing.remove();

            const wrapper = document.createElement('div');
            wrapper.id = 'modal-dynamic';
            wrapper.innerHTML = html;
            container.appendChild(wrapper);

            const modalEl = wrapper.querySelector('.modal');
            if (modalEl) new bootstrap.Modal(modalEl).show();
        } catch (err) {
            console.error('Modal load error:', err);
        }
    });
}

// --- POST AJAX avec protection CSRF ---
async function postJson(url, data) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': token,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(data),
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
}

async function postForm(url, formData) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    formData.append('csrf_token', token);
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
}
