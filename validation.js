/**
 * Global Custom Validation - Works on ALL forms automatically
 * Just include this file and add novalidate to your forms
 */

(function () {
    'use strict';

    // ── Styles injected once ──────────────────────────────────────────────────
    const style = document.createElement('style');
    style.textContent = `
        .cv-error-msg {
            color: #e74c3c;
            font-size: 0.82rem;
            font-weight: 600;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
            animation: cv-fadein 0.2s ease;
        }
        @keyframes cv-fadein {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .cv-invalid {
            border-color: #e74c3c !important;
            background-color: #fff5f5 !important;
            box-shadow: 0 0 0 3px rgba(231,76,60,0.12) !important;
        }
        .cv-valid {
            border-color: #27ae60 !important;
            background-color: #f0fff4 !important;
            box-shadow: 0 0 0 3px rgba(39,174,96,0.10) !important;
        }
    `;
    document.head.appendChild(style);

    // ── Helpers ───────────────────────────────────────────────────────────────
    function getError(field) {
        let next = field.nextElementSibling;
        while (next) {
            if (next.classList.contains('cv-error-msg')) return next;
            next = next.nextElementSibling;
        }
        return null;
    }

    function showError(field, msg) {
        clearError(field);
        field.classList.add('cv-invalid');
        field.classList.remove('cv-valid');
        const span = document.createElement('span');
        span.className = 'cv-error-msg';
        span.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${msg}`;
        field.insertAdjacentElement('afterend', span);
    }

    function clearError(field) {
        field.classList.remove('cv-invalid');
        const existing = getError(field);
        if (existing) existing.remove();
    }

    function markValid(field) {
        clearError(field);
        field.classList.add('cv-valid');
    }

    // ── Validate a single field ───────────────────────────────────────────────
    function validateField(field) {
        const val = field.value.trim();
        const type = field.type;
        const name = (field.name || field.id || '').toLowerCase();
        const label = field.closest('.form-group, .inputBox, div')
            ?.querySelector('label')?.textContent?.replace('*','').trim()
            || field.placeholder
            || 'This field';

        // Skip hidden, submit, button, file (optional)
        if (['hidden','submit','button','reset','image'].includes(type)) return true;

        // Required
        if (field.required && !val) {
            showError(field, `${label} is required`);
            return false;
        }

        // Skip further checks if empty and not required
        if (!val) { clearError(field); return true; }

        // Email
        if (type === 'email' || name.includes('email')) {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                showError(field, 'Please enter a valid email address');
                return false;
            }
        }

        // Phone
        if (type === 'tel' || name.includes('phone') || name.includes('tel')) {
            if (!/^\+?[\d\s\-\(\)]{7,15}$/.test(val)) {
                showError(field, 'Please enter a valid phone number');
                return false;
            }
        }

        // Number min/max
        if (type === 'number') {
            const num = parseFloat(val);
            if (field.min !== '' && num < parseFloat(field.min)) {
                showError(field, `Minimum value is ${field.min}`);
                return false;
            }
            if (field.max !== '' && num > parseFloat(field.max)) {
                showError(field, `Maximum value is ${field.max}`);
                return false;
            }
        }

        // Date - not in future (for payment/attendance dates)
        if (type === 'date' && name.includes('payment')) {
            if (new Date(val) > new Date()) {
                showError(field, 'Date cannot be in the future');
                return false;
            }
        }

        // Min/maxlength
        if (field.minLength > 0 && val.length < field.minLength) {
            showError(field, `Minimum ${field.minLength} characters required`);
            return false;
        }
        if (field.maxLength > 0 && val.length > field.maxLength) {
            showError(field, `Maximum ${field.maxLength} characters allowed`);
            return false;
        }

        // Select - must pick something
        if (field.tagName === 'SELECT' && field.required && !val) {
            showError(field, `Please select ${label}`);
            return false;
        }

        markValid(field);
        return true;
    }

    // ── Attach to all forms ───────────────────────────────────────────────────
    function attachToForm(form) {
        // Disable browser default validation
        form.setAttribute('novalidate', '');

        const fields = form.querySelectorAll('input, select, textarea');

        // Real-time validation on blur & input
        fields.forEach(field => {
            field.addEventListener('blur', () => validateField(field));
            field.addEventListener('input', () => {
                if (field.classList.contains('cv-invalid')) validateField(field);
            });
            field.addEventListener('change', () => validateField(field));
        });

        // On submit - validate all
        form.addEventListener('submit', function (e) {
            let valid = true;
            let firstInvalid = null;

            fields.forEach(field => {
                if (!validateField(field)) {
                    valid = false;
                    if (!firstInvalid) firstInvalid = field;
                }
            });

            if (!valid) {
                e.preventDefault();
                e.stopPropagation();
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            }
        }, true); // capture phase so it runs before other submit handlers
    }

    // ── Init on DOM ready ─────────────────────────────────────────────────────
    function init() {
        document.querySelectorAll('form').forEach(attachToForm);

        // Watch for dynamically added forms (modals etc.)
        const observer = new MutationObserver(mutations => {
            mutations.forEach(m => {
                m.addedNodes.forEach(node => {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'FORM') attachToForm(node);
                        node.querySelectorAll && node.querySelectorAll('form').forEach(attachToForm);
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
