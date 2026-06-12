{{--
    admin/delivery_zones/_styles.blade.php — Shared styles for the delivery
    zone pages. Curved (18px) cards, themed inputs and the floating action bar,
    matching the coupon admin pages.
--}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid { font-family: 'Inter', sans-serif; }
.rounded-4 { border-radius: 18px !important; }
.rounded-3 { border-radius: 12px !important; }
.gap-4     { gap: 24px !important; }

/* Themed cards */
html[data-admin-theme="light"] .theme-card-bg { background-color: #ffffff !important; }
html[data-admin-theme="dark"]  .theme-card-bg { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }
html[data-admin-theme="light"] .text-theme-dark-bold { color: #1e293b !important; }
html[data-admin-theme="dark"]  .text-theme-dark-bold { color: #f1f5f9 !important; }

/* Soft colour tokens */
.bg-soft-primary { background: rgba(108,92,231,0.1) !important;  color: #6c5ce7 !important; }
.bg-soft-success { background: rgba(16,185,129,0.1) !important;  color: #10b981 !important; }
.bg-soft-warning { background: rgba(245,158,11,0.1) !important;  color: #f59e0b !important; }
.bg-soft-danger  { background: rgba(255,51,102,0.1) !important;  color: #ff3366 !important; }

/* Form inputs */
.form-control, .form-select {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0,0,0,0.07) !important;
    padding: 0.5rem 0.95rem !important;
    font-size: 0.84rem !important;
    transition: all 0.25s ease !important;
    background-color: #ffffff !important;
    color: #1e293b !important;
}
.form-control:focus, .form-select:focus {
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 3.5px rgba(108,92,231,0.15) !important;
}
html[data-admin-theme="dark"] .form-control, html[data-admin-theme="dark"] .form-select {
    background-color: #080f1d !important;
    border-color: rgba(255,255,255,0.08) !important;
    color: #e2e8f0 !important;
}
html[data-admin-theme="dark"] .form-control:focus, html[data-admin-theme="dark"] .form-select:focus {
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 3.5px rgba(167,139,250,0.2) !important;
}
.form-control.is-invalid {
    border-color: #ff3366 !important;
    box-shadow: 0 0 0 3.5px rgba(255,51,102,0.15) !important;
}
.form-control:disabled { opacity: 0.45; }

.border-bottom-subtle { border-bottom: 1.5px solid rgba(108,92,231,0.06) !important; }
html[data-admin-theme="dark"] .border-bottom-subtle { border-bottom: 1.5px solid rgba(255,255,255,0.05) !important; }

/* Zone list rows — curved, clickable */
.zone-row {
    border-radius: 14px !important;
    border: 1.5px solid rgba(0,0,0,0.06) !important;
    transition: all 0.2s ease;
    cursor: pointer;
}
.zone-row:hover {
    border-color: #6c5ce7 !important;
    box-shadow: 0 6px 18px rgba(108,92,231,0.12) !important;
    transform: translateY(-1px);
}
html[data-admin-theme="dark"] .zone-row { border-color: rgba(255,255,255,0.07) !important; }
html[data-admin-theme="dark"] .zone-row:hover { border-color: #a78bfa !important; }

.zone-badge {
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    white-space: nowrap;
}

.btn-zone-delete {
    width: 34px; height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50% !important;
    border: 1.5px solid rgba(255,51,102,0.25) !important;
    background: rgba(255,51,102,0.08) !important;
    color: #ff3366 !important;
    transition: all 0.2s ease !important;
}
.btn-zone-delete:hover { background: #ff3366 !important; color: #fff !important; }

/* Floating action bar */
.floating-save-bar {
    position: fixed; bottom: 24px; left: calc(50% + 120px);
    transform: translateX(-50%); z-index: 1000;
    width: calc(100% - 32px - 240px); max-width: 920px;
    background: rgba(255,255,255,0.85) !important;
    backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(0,0,0,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06) !important;
    border-radius: 50px !important;
    transition: all 0.3s ease;
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(15,23,42,0.85) !important;
    border-color: rgba(255,255,255,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
}
.floating-save-bar .button-group { display: flex; align-items: center; gap: 12px; }
.floating-save-bar .btn {
    display: inline-flex; align-items: center; justify-content: center;
    height: 38px !important; min-width: 100px !important;
    padding: 0 22px !important; font-size: 0.82rem !important; font-weight: 700 !important;
    border-radius: 30px !important; transition: all 0.2s ease !important;
}
.floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(0,0,0,0.15) !important; background: transparent !important; color: #475569 !important;
}
.floating-save-bar .btn-outline-light:hover { background: rgba(0,0,0,0.04) !important; color: #1e293b !important; }
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light { border-color: rgba(255,255,255,0.3) !important; color: #fff !important; }
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light:hover { background: rgba(255,255,255,0.1) !important; }
.floating-save-bar .btn-primary {
    background: linear-gradient(135deg,#6c5ce7,#a78bfa) !important;
    border: none !important; color: #fff !important;
    box-shadow: 0 4px 12px rgba(108,92,231,0.2) !important;
}
.floating-save-bar .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(108,92,231,0.3) !important; color:#fff !important; }
.floating-save-bar .btn-danger {
    background: rgba(255,51,102,0.1) !important;
    border: 1.5px solid rgba(255,51,102,0.35) !important; color: #ff3366 !important;
}
.floating-save-bar .btn-danger:hover { background: #ff3366 !important; color: #fff !important; }
.floating-save-bar .floating-bar-title { color: #0f172a !important; }
html[data-admin-theme="dark"] .floating-save-bar .floating-bar-title { color: #ffffff !important; }

.pulse-green {
    width: 8px; height: 8px; background: #10b981; border-radius: 50%;
    display: inline-block; animation: blinkDot 1.5s infinite ease-in-out;
}
@keyframes blinkDot {
    0%,100% { opacity: 0.3; transform: scale(0.9); }
    50%      { opacity: 1;   transform: scale(1.15); }
}

@media (max-width: 991px) { .floating-save-bar { left: 50% !important; width: calc(100% - 32px) !important; } }
@media (max-width: 575px) {
    .floating-save-bar {
        border-radius: 20px !important; padding: 12px 16px !important;
        bottom: 16px !important; flex-direction: column; gap: 10px;
        align-items: stretch !important; text-align: center;
        width: calc(100% - 24px) !important;
    }
    .floating-save-bar .button-group { width: 100%; gap: 8px; }
    .floating-save-bar .btn { min-width: 0 !important; padding: 0 8px !important; font-size: 0.76rem !important; flex: 1; }
}
</style>
