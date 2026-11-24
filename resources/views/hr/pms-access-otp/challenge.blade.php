@extends('layouts.base')

@section('content')
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="pms-otp-shield mx-auto mb-3">
                                <i class="fa fa-shield-alt"></i>
                            </div>
                            <h3 class="font-weight-bold mb-2" style="color:#a80000;">Verifikasi Akses PMS</h3>
                            <p class="text-muted mb-0">
                                Data sensitif PMS (skor IPP &amp; PK) dilindungi OTP. Setelah verifikasi berhasil,
                                akses berlaku <strong>{{ (int) $ttlMinutes }} menit</strong>.
                            </p>
                        </div>

                        @if (!empty($errorMessage))
                            <div class="alert alert-danger" id="pmsChallengeServerError" role="alert">{{ $errorMessage }}</div>
                        @endif
                        @if (!empty($statusMessage))
                            <div class="alert alert-success" role="alert">{{ $statusMessage }}</div>
                        @endif

                        <div id="pmsChallengeError" class="alert alert-danger d-none" role="alert"></div>
                        <div id="pmsChallengeInfo" class="alert alert-info d-none" role="alert"></div>

                        @if (!$hasEmail)
                            <div class="alert alert-danger mb-4">
                                Email Anda belum terdaftar atau tidak valid. Hubungi HR/IT untuk
                                memperbaiki data email sebelum mengakses PMS.
                            </div>
                            <div class="text-center">
                                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">Kembali</a>
                            </div>
                        @elseif ($requestExpired || $consumed)
                            <div class="alert alert-warning mb-4">
                                {{ $consumed ? 'Permintaan OTP ini sudah digunakan.' : 'Sesi verifikasi OTP sudah kedaluwarsa.' }}
                                Silakan buka kembali halaman PMS untuk meminta kode baru.
                            </div>
                            <div class="text-center">
                                <a href="{{ $backUrl }}" class="btn btn-primary">Kembali ke PMS</a>
                            </div>
                        @else
                            <div class="pms-otp-email-box mb-4">
                                <div class="text-muted small mb-1">Kode OTP dikirim ke email terdaftar:</div>
                                <div class="font-weight-bold">{{ $maskedEmail ?: 'Email terdaftar' }}</div>
                            </div>

                            <input type="hidden" id="pmsChallengeRequestId" value="{{ $requestId }}">

                            <div id="pmsChallengeCodeWrap" class="{{ $codeSent ? '' : 'd-none' }}">
                                <label for="pmsChallengeCode" class="font-weight-bold">Kode OTP 6 digit</label>
                                <input type="text"
                                       id="pmsChallengeCode"
                                       class="form-control form-control-lg text-center mb-2"
                                       inputmode="numeric"
                                       autocomplete="one-time-code"
                                       maxlength="6"
                                       placeholder="______"
                                       style="letter-spacing:.45em;font-weight:700;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small id="pmsChallengeCodeCountdown" class="text-muted"></small>
                                    <button type="button" id="pmsChallengeResendBtn" class="btn btn-link btn-sm p-0">
                                        Kirim ulang kode
                                    </button>
                                </div>
                                <button type="button" id="pmsChallengeVerifyBtn" class="btn btn-primary btn-lg btn-block">
                                    Verifikasi &amp; Masuk PMS
                                </button>
                            </div>

                            <div id="pmsChallengeSendWrap" class="{{ $codeSent ? 'd-none' : '' }}">
                                <button type="button" id="pmsChallengeSendBtn" class="btn btn-primary btn-lg btn-block">
                                    Kirim Kode OTP
                                </button>
                                <p class="text-muted small text-center mt-3 mb-0">
                                    Kode berlaku {{ (int) $codeTtlMinutes }} menit. Jangan berikan kode kepada siapa pun.
                                </p>
                            </div>

                            <div class="text-center mt-4">
                                <a href="{{ $backUrl }}" class="text-muted small">Kembali ke PMS</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .pms-otp-shield {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fdecea;
            color: #a80000;
            font-size: 28px;
        }
        .pms-otp-email-box {
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 14px 16px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var requestId = @json($requestId);
            var sendUrl = @json(route('hr.pms-access-otp.send'));
            var verifyUrl = @json(route('hr.pms-access-otp.verify'));
            var statusUrl = @json(route('hr.pms-access-otp.status'));
            var resendCooldown = {{ (int) $resendCooldownSeconds }};

            var state = {
                requestId: requestId,
                codeSent: @json((bool) $codeSent),
                codeExpiresAt: {{ (int) $codeExpiresAt }},
                resendAvailableAt: {{ (int) $resendAvailableAt }},
                serverOffset: {{ (int) $serverTime }} - Math.floor(Date.now() / 1000),
                busy: false,
                reconciling: false,
                reconcileAttempts: 0
            };

            function serverNow() {
                return Math.floor(Date.now() / 1000) + state.serverOffset;
            }

            function formatSeconds(seconds) {
                seconds = Math.max(0, parseInt(seconds, 10) || 0);
                var m = Math.floor(seconds / 60);
                var s = seconds % 60;
                return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
            }

            function byId(id) {
                return document.getElementById(id);
            }

            function showError(message) {
                var el = byId('pmsChallengeError');
                if (!el) return;
                el.textContent = message || 'Terjadi kesalahan.';
                el.classList.remove('d-none');
            }

            function clearError() {
                var el = byId('pmsChallengeError');
                if (el) {
                    el.textContent = '';
                    el.classList.add('d-none');
                }

                var serverError = byId('pmsChallengeServerError');
                if (serverError) {
                    serverError.classList.add('d-none');
                }
            }

            function showInfo(message) {
                var el = byId('pmsChallengeInfo');
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

            function updateCodeUi() {
                var codeWrap = byId('pmsChallengeCodeWrap');
                var sendWrap = byId('pmsChallengeSendWrap');
                var verifyBtn = byId('pmsChallengeVerifyBtn');
                var resendBtn = byId('pmsChallengeResendBtn');
                var sendBtn = byId('pmsChallengeSendBtn');
                var codeCountdown = byId('pmsChallengeCodeCountdown');
                var codeInput = byId('pmsChallengeCode');
                var remainingCode = state.codeExpiresAt > 0 ? state.codeExpiresAt - serverNow() : 0;
                var remainingResend = state.resendAvailableAt > 0 ? state.resendAvailableAt - serverNow() : 0;

                if (state.codeSent) {
                    if (codeWrap) codeWrap.classList.remove('d-none');
                    if (sendWrap) sendWrap.classList.add('d-none');
                } else {
                    if (codeWrap) codeWrap.classList.add('d-none');
                    if (sendWrap) sendWrap.classList.remove('d-none');
                }

                if (codeInput) {
                    codeInput.disabled = !state.codeSent || remainingCode <= 0;
                }
                if (verifyBtn) {
                    verifyBtn.disabled = !state.codeSent || remainingCode <= 0 || state.busy || state.reconciling;
                }
                if (sendBtn) {
                    sendBtn.disabled = state.busy || state.reconciling;
                }

                if (codeCountdown) {
                    if (state.codeSent && remainingCode > 0) {
                        codeCountdown.textContent = 'Kode berlaku ' + formatSeconds(remainingCode);
                    } else if (state.codeSent) {
                        codeCountdown.textContent = 'Kode sudah kedaluwarsa. Kirim ulang kode.';
                    } else {
                        codeCountdown.textContent = '';
                    }
                }

                if (resendBtn) {
                    if (remainingResend > 0 || state.busy || state.reconciling) {
                        resendBtn.disabled = true;
                        resendBtn.textContent = remainingResend > 0
                            ? 'Kirim ulang dalam ' + formatSeconds(remainingResend)
                            : (state.reconciling ? 'Memeriksa status...' : 'Mengirim...');
                    } else {
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Kirim ulang kode';
                    }
                }

                if (remainingCode <= 0 && state.codeSent) {
                    state.codeSent = false;
                }
            }

            function tick() {
                updateCodeUi();
            }

            function applySendSuccess(res) {
                state.busy = false;
                state.reconciling = false;
                state.reconcileAttempts = 0;
                state.requestId = res.request_id || state.requestId;
                state.codeSent = true;
                state.codeExpiresAt = parseInt(res.expires_at, 10) || 0;
                state.resendAvailableAt = parseInt(res.resend_available_at, 10) || 0;
                state.serverOffset = (parseInt(res.server_time, 10) || Math.floor(Date.now() / 1000)) - Math.floor(Date.now() / 1000);

                if (byId('pmsChallengeRequestId')) {
                    byId('pmsChallengeRequestId').value = state.requestId;
                }

                setBusy(byId('pmsChallengeSendBtn'), false);
                setBusy(byId('pmsChallengeResendBtn'), false);

                clearError();
                showInfo(res.message || 'Kode OTP telah dikirim.');
                updateCodeUi();

                var codeInput = byId('pmsChallengeCode');
                if (codeInput) codeInput.focus();
            }

            function reconcileSendState() {
                if (state.reconciling) return;
                state.reconciling = true;
                updateCodeUi();

                $.ajax({
                    url: statusUrl,
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

            function ajaxPost(url, data, onSuccess, onError) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    global: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: onSuccess,
                    error: function (xhr, textStatus) {
                        var res = xhr.responseJSON || {};
                        if (onError) onError(xhr, res, textStatus);
                    }
                });
            }

            function sendCode() {
                if (state.busy || state.reconciling) return;
                state.busy = true;
                state.reconcileAttempts = 0;
                clearError();
                var button = state.codeSent ? byId('pmsChallengeResendBtn') : byId('pmsChallengeSendBtn');
                setBusy(button, true, 'Mengirim...');
                updateCodeUi();

                ajaxPost(sendUrl, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    request_id: state.requestId || '',
                    return_url: window.location.pathname + window.location.search
                }, function (res) {
                    applySendSuccess(res);
                }, function (xhr, res, textStatus) {
                    state.busy = false;
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

                    if (res.retry_after) {
                        state.resendAvailableAt = serverNow() + parseInt(res.retry_after, 10);
                    }
                    showError(res.message || 'Gagal mengirim kode OTP.');
                    updateCodeUi();
                });
            }

            function verifyCode() {
                if (state.busy || state.reconciling) return;
                var codeInput = byId('pmsChallengeCode');
                var code = codeInput ? String(codeInput.value || '').trim() : '';
                if (!/^[0-9]{6}$/.test(code)) {
                    showError('Masukkan kode OTP 6 digit.');
                    if (codeInput) codeInput.focus();
                    return;
                }

                state.busy = true;
                clearError();
                var button = byId('pmsChallengeVerifyBtn');
                setBusy(button, true, 'Memverifikasi...');

                ajaxPost(verifyUrl, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    request_id: state.requestId,
                    code: code
                }, function (res) {
                    state.busy = false;
                    setBusy(button, false);
                    showInfo(res.message || 'Verifikasi berhasil.');
                    window.location.href = res.redirect_url || @json(url('/hr'));
                }, function (xhr, res) {
                    state.busy = false;
                    setBusy(button, false);
                    if (res.remaining !== undefined && res.remaining !== null) {
                        showError((res.message || 'Kode OTP tidak sesuai.') + ' Sisa percobaan: ' + res.remaining + '.');
                    } else {
                        showError(res.message || 'Verifikasi OTP gagal.');
                    }
                    if (codeInput) {
                        codeInput.value = '';
                        codeInput.focus();
                    }
                    updateCodeUi();
                });
            }

            $(function () {
                var codeInput = byId('pmsChallengeCode');
                if (codeInput) {
                    codeInput.addEventListener('input', function () {
                        this.value = String(this.value || '').replace(/[^0-9]/g, '').slice(0, 6);
                    });
                    codeInput.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            verifyCode();
                        }
                    });
                }

                var sendBtn = byId('pmsChallengeSendBtn');
                if (sendBtn) sendBtn.addEventListener('click', sendCode);

                var resendBtn = byId('pmsChallengeResendBtn');
                if (resendBtn) resendBtn.addEventListener('click', sendCode);

                var verifyBtn = byId('pmsChallengeVerifyBtn');
                if (verifyBtn) verifyBtn.addEventListener('click', verifyCode);

                updateCodeUi();
                setInterval(tick, 1000);
                if (state.codeSent && codeInput) codeInput.focus();
            });
        })();
    </script>
@endpush
