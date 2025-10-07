@extends('layouts.base')

@section('content')
    <style>
        .select2-container--default .select2-results__option {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
        }

        .loading-spinner {
            border: 4px solid rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10000;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Edit Password</div>
                    <div class="card-body" id="card-body">
                        <div class="text-center mb-4">
                            <a href="{{ url('/') }}">
                                <img alt="Logo" src="{{ url('/') }}/assets/media/logos/user_logo.png" />
                            </a>
                        </div>
                        <form method="POST" action="{{ route('user.update.password') }}">
                            @csrf
                            <div class="form-group">
                                <label for="user_id">Pilih Nama User:</label>
                                <select name="user_id" id="user_id" class="form-control cari-users">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="new_password">Password Baru:</label>
                                <div class="input-group">
                                    <input type="password" name="new_password" id="new_password" class="form-control">
                                    <div class="input-group-append">
                                        <button type="button" id="toggle_password" class="btn btn-outline-secondary">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Loading Overlay and Spinner -->
        <div class="loading-overlay" id="loading-overlay">
            <div class="loading-spinner">aloo</div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- ini script untuk animasi spinner --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function showSpinner() {
            document.getElementById('loading-overlay').style.display = 'block';
            document.getElementById('card-body').style.opacity = '0.5';
        }

        function hideSpinner() {
            document.getElementById('loading-overlay').style.display = 'none';
            document.getElementById('card-body').style.opacity = '1';
        }

        window.onload = function() {
            hideSpinner();
        };

        document.querySelector('form').addEventListener('submit', function() {
            showSpinner();
        });

        document.querySelector('form').addEventListener('submit', function() {
            setTimeout(function() {
                hideSpinner();
            }, 2000);
        });
    </script>

    {{-- ini scripts show dan hidden password --}}
    <script>
        document.getElementById('toggle_password').addEventListener('click', function() {
            var passwordInput = document.getElementById('new_password');
            var icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('far', 'fa-eye');
                icon.classList.add('far', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('far', 'fa-eye-slash');
                icon.classList.add('far', 'fa-eye');
            }
        });
    </script>

    {{-- ini script untuk select 2 --}}
    <script>
        $(document).ready(function() {
            $('#user_id').select2();
        });
    </script>
@endpush
