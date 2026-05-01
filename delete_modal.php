<?php /* delete_modal.php — Include this file once per page, just before </body> */ ?>

<!-- ===== Custom Delete / Confirm Modal ===== -->
<div class="dm-overlay" id="dmOverlay">
    <div class="dm-box">
        <div class="dm-icon-wrap" id="dmIconWrap">
            <i class="fas fa-trash-alt" id="dmIcon"></i>
        </div>
        <h4 class="dm-title" id="dmTitle">Delete Item?</h4>
        <p class="dm-subtitle" id="dmSubtitle">You are about to permanently delete:</p>
        <div class="dm-badge" id="dmBadge" style="display:none;"></div>
        <div class="dm-warn" id="dmWarn">
            <i class="fas fa-exclamation-triangle"></i>
            <span id="dmWarnText">This action cannot be undone.</span>
        </div>
        <div class="dm-actions">
            <button class="dm-btn-cancel" id="dmCancelBtn"><i class="fas fa-times"></i> Cancel</button>
            <a href="#" class="dm-btn-confirm" id="dmConfirmLink" style="display:none;"><i class="fas fa-trash-alt" id="dmConfirmIcon"></i> <span id="dmConfirmText">Yes, Delete</span></a>
            <button class="dm-btn-confirm" id="dmConfirmBtn" style="display:none;"><i class="fas fa-trash-alt" id="dmConfirmBtnIcon"></i> <span id="dmConfirmBtnText">Confirm</span></button>
        </div>
    </div>
</div>

<style>
.dm-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 99999;
    align-items: center;
    justify-content: center;
}
.dm-overlay.dm-active {
    display: flex;
    animation: dmFadeIn 0.2s ease;
}
@keyframes dmFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
.dm-box {
    background: #fff;
    border-radius: 20px;
    padding: 40px 36px 32px;
    max-width: 430px;
    width: 92%;
    text-align: center;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
    animation: dmPopIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes dmPopIn {
    from { transform: scale(0.75); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
.dm-icon-wrap {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 8px 24px rgba(238,90,82,0.35);
}
.dm-icon-wrap.dm-warn-icon {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    box-shadow: 0 8px 24px rgba(255,152,0,0.35);
}
.dm-icon-wrap i {
    font-size: 30px;
    color: #fff;
}
.dm-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 8px;
}
.dm-subtitle {
    color: #6c757d;
    font-size: 0.95rem;
    margin: 0 0 6px;
}
.dm-badge {
    display: inline-block;
    background: #fff3f3;
    color: #ee5a52;
    border: 1.5px solid #ffcdd2;
    border-radius: 8px;
    padding: 6px 16px;
    font-weight: 700;
    font-size: 1rem;
    margin: 8px 0 16px;
    letter-spacing: 0.5px;
    word-break: break-word;
}
.dm-warn {
    background: #fff8e1;
    border-left: 4px solid #ffc107;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 0.83rem;
    color: #856404;
    margin-bottom: 24px;
    text-align: left;
    display: flex;
    gap: 8px;
    align-items: flex-start;
}
.dm-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}
.dm-btn-cancel {
    flex: 1;
    padding: 12px 16px;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
}
.dm-btn-cancel:hover {
    background: #e9ecef;
    border-color: #ced4da;
}
.dm-btn-confirm {
    flex: 1;
    padding: 12px 16px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: #fff;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 14px rgba(238,90,82,0.35);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.dm-btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(238,90,82,0.5);
    color: #fff;
    text-decoration: none;
}
</style>

<script>
(function() {
    // ---- Core helpers ----
    var overlay   = document.getElementById('dmOverlay');
    var cancelBtn = document.getElementById('dmCancelBtn');
    var confLink  = document.getElementById('dmConfirmLink');
    var confBtn   = document.getElementById('dmConfirmBtn');
    var _callback = null;

    function openDM() { overlay.classList.add('dm-active'); }
    function closeDM() {
        overlay.classList.remove('dm-active');
        _callback = null;
        confLink.style.display = 'none';
        confBtn.style.display  = 'none';
    }

    cancelBtn.addEventListener('click', closeDM);
    overlay.addEventListener('click', function(e){ if(e.target===this) closeDM(); });
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeDM(); });

    confBtn.addEventListener('click', function(){
        if(typeof _callback === 'function') _callback();
        closeDM();
    });

    /**
     * showDeleteModal(label, url, options)
     *   label   — item name shown in the badge (e.g. "COMMERCE")
     *   url     — href for the confirm link (page redirect delete)
     *   options — optional object:
     *     { title, subtitle, warnText, confirmText, icon }
     */
    window.showDeleteModal = function(label, url, options) {
        options = options || {};
        document.getElementById('dmTitle').textContent    = options.title    || 'Delete Item?';
        document.getElementById('dmSubtitle').textContent = options.subtitle || 'You are about to permanently delete:';
        document.getElementById('dmWarnText').textContent = options.warnText || 'This action cannot be undone. This item will be permanently removed.';
        document.getElementById('dmConfirmText').textContent = options.confirmText || 'Yes, Delete';
        document.getElementById('dmIcon').className      = 'fas ' + (options.icon || 'fa-trash-alt');
        document.getElementById('dmIconWrap').className  = 'dm-icon-wrap';
        var badge = document.getElementById('dmBadge');
        if (label) {
            badge.textContent = label;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
        confLink.href = url || '#';
        confLink.style.display = 'inline-flex';
        confBtn.style.display  = 'none';
        openDM();
    };

    /**
     * showConfirmModal(message, callback, options)
     *   message  — warning message text
     *   callback — function to call when confirmed
     *   options  — optional { title, confirmText, icon, isWarning }
     */
    window.showConfirmModal = function(message, callback, options) {
        options = options || {};
        document.getElementById('dmTitle').textContent    = options.title    || 'Are you sure?';
        document.getElementById('dmSubtitle').textContent = '';
        document.getElementById('dmWarnText').textContent = message;
        document.getElementById('dmConfirmBtnText').textContent = options.confirmText || 'Confirm';
        document.getElementById('dmConfirmBtnIcon').className   = 'fas ' + (options.icon || 'fa-check');
        document.getElementById('dmIcon').className      = 'fas ' + (options.icon || 'fa-exclamation-triangle');
        var wrap = document.getElementById('dmIconWrap');
        wrap.className = options.isWarning === false ? 'dm-icon-wrap' : 'dm-icon-wrap dm-warn-icon';
        document.getElementById('dmBadge').style.display = 'none';
        confLink.style.display = 'none';
        confBtn.style.display  = 'inline-flex';
        _callback = callback;
        openDM();
    };
})();
</script>
