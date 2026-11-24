<style>
    #pmsOtpCountdown {
        position: fixed;
        right: 18px;
        bottom: 18px;
        z-index: 2400;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid #e1e4e8;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        font-size: 12px;
        color: #57606a;
        user-select: none;
    }
    #pmsOtpCountdown .pms-otp-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #2e7d32;
        display: inline-block;
    }
    #pmsOtpCountdown.pms-otp-warning {
        background: #a80000;
        border-color: #a80000;
        color: #ffffff;
    }
    #pmsOtpCountdown.pms-otp-warning .pms-otp-dot {
        background: #ffeb3b;
    }
    #pmsOtpCountdown strong {
        font-variant-numeric: tabular-nums;
    }
    body.pms-otp-locked {
        overflow: hidden;
    }
    body.pms-otp-locked #kt_content,
    body.pms-otp-locked #kt_header,
    body.pms-otp-locked #kt_header_mobile,
    body.pms-otp-locked #kt_footer {
        filter: blur(6px);
        pointer-events: none;
        user-select: none;
    }
    #pmsOtpLock {
        position: fixed;
        inset: 0;
        z-index: 3000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(255, 255, 255, .97);
    }
    #pmsOtpLock .pms-otp-lock-card {
        width: 100%;
        max-width: 460px;
        border: 1px solid #e1e4e8;
        border-radius: 12px;
        box-shadow: 0 18px 50px rgba(0, 0, 0, .14);
        background: #ffffff;
        padding: 34px 28px;
        text-align: center;
    }
    #pmsOtpLock .pms-otp-lock-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        background: #fdecea;
        color: #a80000;
        font-size: 28px;
    }
    #pmsOtpModal {
        z-index: 3010;
    }
    #pmsOtpCode {
        letter-spacing: .45em;
        font-weight: 700;
        font-size: 24px;
        text-align: center;
    }
    .pms-otp-email-box {
        border: 1px solid #e1e4e8;
        border-radius: 8px;
        background: #f8f9fa;
        padding: 12px 14px;
    }
</style>

<div id="pmsOtpCountdown" class="d-none" title="Sisa waktu akses PMS">
    <span class="pms-otp-dot"></span>
    <span>Akses PMS</span>
    <strong id="pmsOtpCountdownText">--:--</strong>
</div>

<div id="pmsOtpLock" class="d-none" role="dialog" aria-modal="true" aria-labelledby="pmsOtpLockTitle">
    <div class="pms-otp-lock-card">
        <div class="pms-otp-lock-icon"><i class="fa fa-lock"></i></div>
        <h4 class="font-weight-bold mb-2" id="pmsOtpLockTitle" style="color:#a80000;">Sesi Akses PMS Berakhir</h4>
        <p class="text-muted mb-4">
            Demi keamanan data IPP &amp; PK, akses hanya berlaku {{ (int) config('pms.access_otp.ttl_minutes', 20) }} menit.
            Verifikasi OTP kembali untuk melanjutkan tanpa kehilangan isian yang sudah ada.
        </p>
        <button type="button" id="pmsOtpExtendBtn" class="btn btn-primary btn-lg btn-block">
            Perpanjang Akses
        </button>
        <a href="{{ url('/') }}" class="btn btn-link btn-sm mt-2">Kembali ke Beranda</a>
    </div>
</div>

<div class="modal fade" id="pmsOtpModal" tabindex="-1" role="dialog" aria-labelledby="pmsOtpModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pmsOtpModalTitle">Verifikasi Akses PMS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="pmsOtpIntro" class="text-muted mb-3">
                    Sesi akses PMS berakhir. Masukkan kode OTP untuk melanjutkan.
                </p>

                <div id="pmsOtpEmailBox" class="pms-otp-email-box mb-3 d-none">
                    <div class="text-muted small mb-1">Kode OTP dikirim ke email terdaftar:</div>
                    <div id="pmsOtpMaskedEmail" class="font-weight-bold">-</div>
                </div>

                <div id="pmsOtpError" class="alert alert-danger d-none" role="alert"></div>
                <div id="pmsOtpInfo" class="alert alert-info d-none" role="alert"></div>

                <div id="pmsOtpCodeWrap" class="d-none">
                    <label for="pmsOtpCode" class="font-weight-bold">Kode OTP 6 digit</label>
                    <input type="text"
                           id="pmsOtpCode"
                           class="form-control mb-2"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           maxlength="6"
                           placeholder="______">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small id="pmsOtpCodeCountdown" class="text-muted"></small>
                        <button type="button" id="pmsOtpResendBtn" class="btn btn-link btn-sm p-0">
                            Kirim ulang kode
                        </button>
                    </div>
                    <button type="button" id="pmsOtpVerifyBtn" class="btn btn-primary btn-block">
                        Verifikasi
                    </button>
                </div>

                <div id="pmsOtpSendWrap">
                    <button type="button" id="pmsOtpSendBtn" class="btn btn-primary btn-block">
                        Kirim Kode OTP
                    </button>
                    <p class="text-muted small text-center mt-3 mb-0">
                        Kode berlaku {{ (int) config('pms.access_otp.code_ttl_minutes', 5) }} menit.
                        Jangan berikan kode kepada siapa pun.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var modalEl = document.getElementById('pmsOtpModal');
        if (!modalEl || typeof window.jQuery === 'undefined') {
            return;
        }

        var $ = window.jQuery;
        var config = {
            expiresAt: {{ (int) ($pmsOtpExpiresAt ?? 0) }},
            serverTime: {{ (int) ($pmsOtpServerTime ?? now()->timestamp) }},
            statusUrl: @json(route('hr.pms-access-otp.status')),
            sendUrl: @json(route('hr.pms-access-otp.send')),
            verifyUrl: @json(route('hr.pms-access-otp.verify')),
            resendCooldown: {{ (int) config('pms.access_otp.resend_cooldown_seconds', 60) }},
            codeTtlMinutes: {{ (int) config('pms.access_otp.code_ttl_minutes', 5) }},
            deferred: @json((bool) ($pmsOtpDeferred ?? false))
        };

        var state = {
            expiresAt: config.expiresAt,
            serverOffset: config.serverTime - Math.floor(Date.now() / 1000),
            requestId: null,
            maskedEmail: '',
            codeSent: false,
            codeExpiresAt: 0,
            resendAvailableAt: 0,
            sending: false,
            verifying: false,
            modalOpen: false,
            lockShown: false,
            statusChecked: false,
            reconciling: false,
            reconcileAttempts: 0,
            requestVersion: 0,
            retryQueue: []
        };

        function byId(id) {
            return document.getElementById(id);
        }

        function serverNow() {
            return Math.floor(Date.now() / 1000) + state.serverOffset;
        }

        function formatSeconds(seconds) {
            seconds = Math.max(0, parseInt(seconds, 10) || 0);
            var m = Math.floor(seconds / 60);
            var s = seconds % 60;
            return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        }

        function toast(type, message) {
            if (window.toastr && typeof window.toastr[type] === 'function') {
                window.toastr[type](message);
            }
        }

        function showError(message) {
            var el = byId('pmsOtpError');
            if (!el) return;
            el.textContent = message || 'Terjadi kesalahan.';
            el.classList.remove('d-none');
        }

        function clearError() {
            var el = byId('pmsOtpError');
            if (el) {
                el.textContent = '';
                el.classList.add('d-none');
            }
        }

        function showInfo(message) {
            var el = byId('pmsOtpInfo');
            if (!el) return;
            el.textContent = message || '';
            el.classList.toggle('d-none', !message);
        }

        function setBusy(button, busy, text) {
            if (!button) return;
            button.disabled = busy;
            if (busy) {
                button.dataset.originalText = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>' + (text || 'Memproses...');
            } else if (button.dataset.originalText) {
                button.innerHTML = button.dataset.originalText;
            }
        }

        function showLock() {
            if (state.lockShown) return;
            state.lockShown = true;
            var lock = byId('pmsOtpLock');
            if (lock) {
                lock.classList.remove('d-none');
                lock.setAttribute('aria-hidden', 'false');
            }
            document.body.classList.add('pms-otp-locked');
        }

        function hideLock() {
            state.lockShown = false;
            var lock = byId('pmsOtpLock');
            if (lock) {
                lock.classList.add('d-none');
                lock.setAttribute('aria-hidden', 'true');
            }
            document.body.classList.remove('pms-otp-locked');
        }

        function updateGrant(expiresAt, serverTime) {
            state.expiresAt = parseInt(expiresAt, 10) || 0;
            state.serverOffset = (parseInt(serverTime, 10) || Math.floor(Date.now() / 1000)) - Math.floor(Date.now() / 1000);
            state.statusChecked = true;
            hideLock();
            updateBadge();
        }

        function updateBadge() {
            var badge = byId('pmsOtpCountdown');
            var text = byId('pmsOtpCountdownText');
            if (!badge || !text) return;

            if (state.expiresAt <= 0) {
                badge.classList.add('d-none');
                if (state.statusChecked && !config.deferred) showLock();
                return;
            }

            var remaining = Math.max(0, state.expiresAt - serverNow());
            badge.classList.remove('d-none');
            badge.classList.toggle('pms-otp-warning', remaining <= 120);
            text.textContent = formatSeconds(remaining);

            if (remaining <= 0 && state.statusChecked) {
                if (config.deferred) {
                    badge.classList.add('d-none');
                } else {
                    showLock();
                }
            }
        }

        function updateCodeUi() {
            var codeWrap = byId('pmsOtpCodeWrap');
            var sendWrap = byId('pmsOtpSendWrap');
            var codeInput = byId('pmsOtpCode');
            var verifyBtn = byId('pmsOtpVerifyBtn');
            var resendBtn = byId('pmsOtpResendBtn');
            var sendBtn = byId('pmsOtpSendBtn');
            var countdown = byId('pmsOtpCodeCountdown');
            var emailBox = byId('pmsOtpEmailBox');
            var emailText = byId('pmsOtpMaskedEmail');
            var remainingCode = state.codeExpiresAt > 0 ? state.codeExpiresAt - serverNow() : 0;
            var remainingResend = state.resendAvailableAt > 0 ? state.resendAvailableAt - serverNow() : 0;

            if (state.maskedEmail && emailText) {
                emailText.textContent = state.maskedEmail;
            }
            if (emailBox) {
                emailBox.classList.toggle('d-none', !state.maskedEmail);
            }

            if (codeWrap) codeWrap.classList.toggle('d-none', !state.codeSent);
            if (sendWrap) sendWrap.classList.toggle('d-none', state.codeSent);

            if (codeInput) {
                codeInput.disabled = !state.codeSent || remainingCode <= 0;
            }
            if (verifyBtn) {
                verifyBtn.disabled = !state.codeSent || remainingCode <= 0
                    || state.verifying || state.sending || state.reconciling;
            }
            if (sendBtn) {
                sendBtn.disabled = state.sending || state.verifying || state.reconciling;
            }

            if (countdown) {
                if (state.codeSent && remainingCode > 0) {
                    countdown.textContent = 'Kode berlaku ' + formatSeconds(remainingCode);
                } else if (state.codeSent) {
                    countdown.textContent = 'Kode kedaluwarsa. Kirim ulang kode.';
                } else {
                    countdown.textContent = '';
                }
            }

            if (resendBtn) {
                resendBtn.disabled = remainingResend > 0 || state.sending
                    || state.verifying || state.reconciling;
                resendBtn.textContent = remainingResend > 0
                    ? 'Kirim ulang dalam ' + formatSeconds(remainingResend)
                    : 'Kirim ulang kode';
            }
        }

        function requestStatus(callback) {
            var version = state.requestVersion;

            $.ajax({
                url: config.statusUrl,
                type: 'GET',
                dataType: 'json',
                global: false,
                timeout: 15000,
                data: { request_id: state.requestId || '' },
                success: function (res) {
                    if (!res || !res.enabled) {
                        hideLock();
                        return;
                    }

                    state.statusChecked = true;
                    state.maskedEmail = res.masked_email || state.maskedEmail;
                    state.serverOffset = (parseInt(res.server_time, 10) || Math.floor(Date.now() / 1000)) - Math.floor(Date.now() / 1000);

                    // Jangan biarkan response status lama menimpa state
                    // pengiriman yang lebih baru.
                    if (version === state.requestVersion && !state.sending && !state.verifying) {
                        if (res.request_id && (!state.requestId || res.code_sent)) {
                            state.requestId = res.request_id;
                        }

                        if (res.code_sent) {
                            state.codeSent = true;
                            state.codeExpiresAt = parseInt(res.code_expires_at, 10) || 0;
                            state.resendAvailableAt = parseInt(res.resend_available_at, 10) || 0;
                        } else if (!res.has_grant) {
                            state.codeSent = false;
                            state.codeExpiresAt = 0;
                        }
                    }

                    if (version === state.requestVersion) {
                        if (res.has_grant) {
                            updateGrant(res.expires_at, res.server_time);
                        } else {
                            state.expiresAt = 0;
                            updateBadge();
                            if (!config.deferred) showLock();
                        }
                    }

                    updateCodeUi();

                    if (typeof callback === 'function') callback(res);
                },
                error: function () {
                    if (typeof callback === 'function') callback(null);
                }
            });
        }

        function resetRequestState() {
            state.requestId = null;
            state.codeSent = false;
            state.codeExpiresAt = 0;
            state.resendAvailableAt = 0;
            state.requestVersion++;
            updateCodeUi();
        }

        function applySendSuccess(res) {
            state.sending = false;
            state.reconciling = false;
            state.reconcileAttempts = 0;
            state.requestVersion++;
            state.requestId = res.request_id || state.requestId;
            state.maskedEmail = res.masked_email || state.maskedEmail;
            state.codeSent = true;
            state.codeExpiresAt = parseInt(res.expires_at, 10) || 0;
            state.resendAvailableAt = parseInt(res.resend_available_at, 10) || 0;
            state.serverOffset = (parseInt(res.server_time, 10) || Math.floor(Date.now() / 1000)) - Math.floor(Date.now() / 1000);

            setBusy(byId('pmsOtpSendBtn'), false);
            setBusy(byId('pmsOtpResendBtn'), false);

            clearError();
            showInfo(res.message || 'Kode OTP telah dikirim.');
            updateCodeUi();

            var codeInput = byId('pmsOtpCode');
            if (codeInput) codeInput.focus();
        }

        function reconcileSendState() {
            if (state.reconciling) return;
            state.reconciling = true;
            updateCodeUi();

            $.ajax({
                url: config.statusUrl,
                type: 'GET',
                dataType: 'json',
                data: { request_id: state.requestId || '' },
                global: false,
                timeout: 15000,
                success: function (res) {
                    state.reconciling = false;

                    if (res && res.code_sent) {
                        applySendSuccess({
                            request_id: res.request_id || state.requestId,
                            masked_email: res.masked_email,
                            expires_at: res.code_expires_at,
                            resend_available_at: res.resend_available_at,
                            server_time: res.server_time,
                            message: 'Kode OTP sudah dikirim. Silakan cek email Anda.'
                        });
                        return;
                    }

                    if (res && res.consumed) {
                        showError('Permintaan OTP sudah digunakan. Silakan buka kembali halaman PMS.');
                        return;
                    }

                    if (res && res.request_expired) {
                        showError('Sesi verifikasi OTP sudah kedaluwarsa. Silakan buka kembali halaman PMS.');
                        return;
                    }

                    if (state.reconcileAttempts < 3) {
                        state.reconcileAttempts++;
                        setTimeout(reconcileSendState, 2000);
                        return;
                    }

                    showInfo('Status pengiriman belum dapat dipastikan. Cek email sebelum mengirim ulang.');
                    updateCodeUi();
                },
                error: function () {
                    state.reconciling = false;
                    showInfo('Status pengiriman belum dapat dipastikan. Cek email sebelum mengirim ulang.');
                    updateCodeUi();
                }
            });
        }

        function sendOtp() {
            if (state.sending || state.reconciling) return;
            state.sending = true;
            state.reconcileAttempts = 0;
            clearError();
            showInfo('');

            var button = state.codeSent ? byId('pmsOtpResendBtn') : byId('pmsOtpSendBtn');
            setBusy(button, true, 'Mengirim...');
            updateCodeUi();

            $.ajax({
                url: config.sendUrl,
                type: 'POST',
                dataType: 'json',
                global: false,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    request_id: state.requestId || '',
                    return_url: window.location.pathname + window.location.search
                },
                success: function (res) {
                    applySendSuccess(res);
                },
                error: function (xhr, textStatus) {
                    state.sending = false;
                    setBusy(button, false);

                    if (textStatus === 'timeout'
                        || xhr.status === 0
                        || xhr.status === 502
                        || xhr.status === 504
                        || textStatus === 'parsererror') {
                        showInfo('Permintaan pengiriman sedang diproses. Mohon tunggu...');
                        updateCodeUi();
                        reconcileSendState();
                        return;
                    }

                    var res = xhr.responseJSON || {};
                    if (xhr.status === 409 || xhr.status === 410) {
                        resetRequestState();
                    }
                    if (res.retry_after) {
                        state.resendAvailableAt = serverNow() + (parseInt(res.retry_after, 10) || config.resendCooldown);
                    }
                    showError(res.message || 'Gagal mengirim kode OTP.');
                    updateCodeUi();
                }
            });
        }

        function verifyOtp() {
            if (state.verifying || state.reconciling || state.sending) return;

            var codeInput = byId('pmsOtpCode');
            var code = codeInput ? String(codeInput.value || '').trim() : '';
            if (!/^[0-9]{6}$/.test(code)) {
                showError('Masukkan kode OTP 6 digit.');
                if (codeInput) codeInput.focus();
                return;
            }

            state.verifying = true;
            clearError();
            showInfo('');

            var button = byId('pmsOtpVerifyBtn');
            setBusy(button, true, 'Memverifikasi...');
            updateCodeUi();

            $.ajax({
                url: config.verifyUrl,
                type: 'POST',
                dataType: 'json',
                global: false,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    request_id: state.requestId,
                    code: code
                },
                success: function (res) {
                    state.verifying = false;
                    state.requestVersion++;
                    setBusy(button, false);
                    state.requestId = null;
                    state.codeSent = false;
                    state.codeExpiresAt = 0;
                    state.resendAvailableAt = 0;
                    if (codeInput) codeInput.value = '';

                    updateGrant(res.expires_at, res.server_time);
                    hideModal();
                    toast('success', res.message || 'Verifikasi OTP berhasil. Akses PMS diperpanjang.');
                    retryQueuedGets();
                },
                error: function (xhr) {
                    state.verifying = false;
                    setBusy(button, false);

                    var res = xhr.responseJSON || {};
                    showError(res.message || 'Verifikasi OTP gagal.');
                    if (codeInput) {
                        codeInput.value = '';
                        codeInput.focus();
                    }
                    updateCodeUi();
                }
            });
        }

        function showModal(autoSend, message) {
            if (state.modalOpen) {
                if (autoSend && !state.sending && !state.codeSent) sendOtp();
                return;
            }

            state.modalOpen = true;
            clearError();
            showInfo('');

            var intro = byId('pmsOtpIntro');
            if (intro) {
                intro.textContent = message || 'Sesi akses PMS berakhir. Masukkan kode OTP untuk melanjutkan.';
            }

            updateCodeUi();
            $(modalEl).modal({ backdrop: 'static', keyboard: false });
            $(modalEl).modal('show');

            if (autoSend && !state.codeSent && !state.sending) {
                sendOtp();
            } else if (state.codeSent) {
                var codeInput = byId('pmsOtpCode');
                if (codeInput) codeInput.focus();
            }
        }

        function hideModal() {
            $(modalEl).modal('hide');
        }

        function retryQueuedGets() {
            var queue = state.retryQueue.splice(0, state.retryQueue.length);
            queue.forEach(function (item) {
                $.ajax({
                    url: item.url,
                    type: item.type || 'GET',
                    data: item.data,
                    dataType: item.dataType || 'json',
                    global: false,
                    pmsOtpInternal: true,
                    success: item.success,
                    error: item.error
                });
            });
        }

        $(document).ajaxError(function (event, xhr, settings) {
            if (!xhr || xhr.status !== 401) return;
            if (settings && settings.pmsOtpInternal) return;

            var res = xhr.responseJSON || {};
            if (!res.otp_required) return;

            var method = String((settings && settings.type) || 'GET').toUpperCase();
            if (method === 'GET') {
                state.retryQueue.push({
                    url: settings.url,
                    type: settings.type || 'GET',
                    data: settings.data,
                    dataType: settings.dataType,
                    success: settings.success,
                    error: settings.error
                });
                if (state.retryQueue.length > 5) state.retryQueue.shift();
            }

            showLock();
            showModal(true, 'Sesi akses PMS sudah berakhir. Verifikasi OTP untuk melanjutkan.');

            if (method !== 'GET') {
                toast('warning', 'Aksi terakhir belum dijalankan. Silakan ulangi setelah verifikasi OTP.');
            }
        });

        $(modalEl).on('shown.bs.modal', function () {
            state.modalOpen = true;
            if (state.codeSent) {
                var codeInput = byId('pmsOtpCode');
                if (codeInput) codeInput.focus();
            }
        });

        $(modalEl).on('hidden.bs.modal', function () {
            state.modalOpen = false;
        });

        $(function () {
            var codeInput = byId('pmsOtpCode');
            if (codeInput) {
                codeInput.addEventListener('input', function () {
                    this.value = String(this.value || '').replace(/[^0-9]/g, '').slice(0, 6);
                });
                codeInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        verifyOtp();
                    }
                });
            }

            var sendBtn = byId('pmsOtpSendBtn');
            if (sendBtn) sendBtn.addEventListener('click', sendOtp);

            var resendBtn = byId('pmsOtpResendBtn');
            if (resendBtn) resendBtn.addEventListener('click', sendOtp);

            var verifyBtn = byId('pmsOtpVerifyBtn');
            if (verifyBtn) verifyBtn.addEventListener('click', verifyOtp);

            var extendBtn = byId('pmsOtpExtendBtn');
            if (extendBtn) {
                extendBtn.addEventListener('click', function () {
                    requestStatus(function (res) {
                        if (res && res.has_grant) {
                            hideLock();
                            toast('info', 'Akses PMS masih aktif.');
                            return;
                        }
                        showModal(true, 'Sesi akses PMS berakhir. Verifikasi OTP untuk melanjutkan.');
                    });
                });
            }

            updateBadge();
            updateCodeUi();
            requestStatus();
            setInterval(updateBadge, 1000);
            setInterval(requestStatus, 60000);

            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) requestStatus();
            });
            window.addEventListener('focus', function () {
                requestStatus();
            });
        });
    })();
</script>
