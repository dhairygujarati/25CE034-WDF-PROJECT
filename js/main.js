/**
 * CAMPUS MANAGEMENT PORTAL - CORE JAVASCRIPT
 * DOM Manipulation, Event Handling, Modals, Toasts
 */

document.addEventListener('DOMContentLoaded', () => {
  initMobileNav();
  initModalHandlers();
  initAutoDismissAlerts();
});

/**
 * Accessible Mobile Navigation Menu
 */
function initMobileNav() {
  const toggle = document.querySelector('.mobile-nav-toggle');
  const navMenu = document.querySelector('.nav-menu');

  if (toggle && navMenu) {
    toggle.addEventListener('click', () => {
      const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', !isExpanded);
      navMenu.classList.toggle('open');
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
      if (!toggle.contains(e.target) && !navMenu.contains(e.target) && navMenu.classList.contains('open')) {
        navMenu.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }
}

/**
 * Accessible Modal Dialog Component
 * Supports opening, focus trapping, Esc key dismissal, backdrop click
 */
function initModalHandlers() {
  // Event delegation for opening modals via data-modal-target
  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-modal-target]');
    if (trigger) {
      e.preventDefault();
      const targetId = trigger.getAttribute('data-modal-target');
      openModal(targetId);
    }

    // Modal close button or backdrop dismissal
    const closeBtn = e.target.closest('[data-modal-close]');
    if (closeBtn) {
      e.preventDefault();
      const modal = closeBtn.closest('.modal-backdrop');
      if (modal) closeModal(modal);
    }
  });

  // Close when clicking directly on backdrop
  document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
    backdrop.addEventListener('click', (e) => {
      if (e.target === backdrop) {
        closeModal(backdrop);
      }
    });
  });

  // Global Escape key handler
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const activeModal = document.querySelector('.modal-backdrop.active');
      if (activeModal) closeModal(activeModal);
    }
  });
}

function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;

  modal.classList.add('active');
  modal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';

  // Set focus to the first interactive element inside modal
  const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
  if (focusable.length > 0) {
    focusable[0].focus();
  }
}

function closeModal(modal) {
  modal.classList.remove('active');
  modal.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

/**
 * Toast Notification System
 */
function showToast(message, type = 'info', duration = 3500) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.setAttribute('aria-live', 'polite');
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.setAttribute('role', 'alert');

  const icons = {
    success: '✓',
    danger: '⚠',
    warning: '!',
    info: 'ℹ'
  };

  toast.innerHTML = `
    <div style="display: flex; align-items: center; gap: 0.5rem;">
      <strong style="font-size: 1.1rem;">${icons[type] || 'ℹ'}</strong>
      <span>${escapeHtml(message)}</span>
    </div>
    <button type="button" class="btn-close" aria-label="Dismiss notification" style="font-size: 1.1rem;">&times;</button>
  `;

  const closeBtn = toast.querySelector('.btn-close');
  closeBtn.addEventListener('click', () => toast.remove());

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    setTimeout(() => toast.remove(), 400);
  }, duration);
}

/**
 * Auto dismiss standard flash alerts after 5 seconds
 */
function initAutoDismissAlerts() {
  document.querySelectorAll('.alert:not(.alert-permanent)').forEach((alert) => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.5s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }, 6000);
  });
}

/**
 * XSS escaping helper for client templates
 */
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return String(text).replace(/[&<>"']/g, (m) => map[m]);
}
