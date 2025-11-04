@extends('layouts.base')

@push('styles')
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Edit Profile
                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="form-edit" action="{{ route('user.update.profile') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <input type="hidden" name="id" id="editProfileId" value="{{ $user->id }}">

                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <div class="input-group">
                                        <input name="password" type="password" class="form-control" id="password"
                                            placeholder="Enter new password">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="togglePasswordVisibility('password', 'password-eye-icon')">
                                                <i id="password-eye-icon" class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p id="password_min" class="text-muted mt-1">Password minimal
                                        6 karakter.</p>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <div class="input-group">
                                        <input name="password_confirmation" type="password" class="form-control"
                                            id="password_confirmation" placeholder="Re-enter new password">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="togglePasswordVisibility('password_confirmation', 'confirm-eye-icon')">
                                                <i id="confirm-eye-icon" class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p id="password_mismatch" class="text-danger mt-1" style="display: none;">Password tidak
                                        cocok.</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" id="btn-edit" class="btn btn-primary">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    <span class="btn-text">Edit</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#form-edit').on('submit', function(e) {
            e.preventDefault();

            let $btn = $('#btn-edit');
            let $spinner = $btn.find('.spinner-border');
            let $text = $btn.find('.btn-text');

            // disable button and show spinner
            $btn.prop('disabled', true);
            $spinner.removeClass('d-none');
            $text.text('Processing...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        location.reload();
                        toastr.success(response.message, 'Success!');
                    } else {
                        toastr.error(response.message, 'Error!');
                        console.log("200 but error");
                    }
                },
                error: function(error) {
                    var res = error.responseJSON;
                    if (res && res.errors) {
                        $.each(res.errors, function(key, value) {
                            toastr.error(value, 'Error!');
                        });
                    } else {
                        toastr.error('Server error occurred.', 'Error!');
                    }
                },
                complete: function() {
                    // re-enable button and hide spinner
                    $btn.prop('disabled', false);
                    $spinner.addClass('d-none');
                    $text.text('Edit');
                }
            });
        });

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // password validation
        $('#password, #password_confirmation').on('input', function() {
            let password = $('#password').val();
            let confirmPassword = $('#password_confirmation').val();
            let $btn = $('#btn-edit');
            let $errMiss = $('#password_mismatch');
            let $errMin = $('#password_min');

            $errMiss.hide();
            $errMin.removeClass('text-danger').addClass('text-muted');

            let isValid = true;

            // validasi minimal 6 karakter
            if (password && password.length < 6) {
                $errMin.removeClass('text-muted').addClass('text-danger');
                isValid = false;
            }

            // validasi konfirmasi password
            if (password !== '' && confirmPassword !== '' && password !== confirmPassword) {
                $errMiss.show();
                isValid = false;
            }

            $btn.prop('disabled', !isValid);
        });

        // required attribute
        $('#password, #password_confirmation').on('input', function() {
            let password = $('#password').val();
            let confirmPassword = $('#password_confirmation').val();

            if (password !== '' || confirmPassword !== '') {
                $('#password').attr('required', true);
                $('#password_confirmation').attr('required', true);
            } else {
                $('#password').removeAttr('required');
                $('#password_confirmation').removeAttr('required');
            }
        });
    </script>
@endpush
