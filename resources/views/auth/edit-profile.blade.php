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
                                {{-- <div class="form-group">
                                    <label for="password">New Password</label>
                                    <div class="input-group">
                                        <input name="password" type="password" class="form-control" id="password"
                                            placeholder="Enter password">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="toggleEditPasswordVisibility()">
                                                <i id="password-eye-icon" class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div> --}}

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
                    if ($.isEmptyObject(res) == false) {
                        $.each(res.errors, function(key, value) {
                            toastr.error(value, 'Error!');
                        });
                    }
                    console.log("server error");
                },
                complete: function() {
                    // re-enable button and hide spinner
                    $btn.prop('disabled', false);
                    $spinner.addClass('d-none');
                    $text.text('Edit');
                }
            });
        });

        // function toggleEditPasswordVisibility() {
        //     let passwordInput = document.getElementById('password');
        //     let toggleIcon = document.getElementById('password-eye-icon');

        //     if (passwordInput.type === 'password') {
        //         passwordInput.type = 'text';
        //         toggleIcon.classList.remove('fa-eye');
        //         toggleIcon.classList.add('fa-eye-slash');
        //     } else {
        //         passwordInput.type = 'password';
        //         toggleIcon.classList.remove('fa-eye-slash');
        //         toggleIcon.classList.add('fa-eye');
        //     }
        // }
    </script>
@endpush
