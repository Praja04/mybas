<!-- index.blade.php -->
@extends('layouts.base')

@section('content')
    <style>
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom card-stretch gutter-b">
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Tambah Data</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ url('/masukharilibur/master_approval') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('storeDataMaster') }}">
                            @csrf
                            <div class="form-group">
                                <label for="nama_admin">Nama Admin</label>
                                <select class="form-control cari-users" id="nama_admin" name="nama_admin" required>
                                    <option value="">Select Nama Admin</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->name }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nik_admin">NIK Admin</label>
                                <select class="form-control cari-users" id="nik_admin" name="nik_admin" required>
                                    <option value="">Select NIK Admin</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->username }}">{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nama_approval">Nama Approval</label>
                                <select class="form-control cari-users" id="nama_approval" name="nama_approval" required>
                                    <option value="">Select Nama Approval</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->name }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nik_approval">NIK Approval</label>
                                <select class="form-control cari-users" id="nik_approval" name="nik_approval" required>
                                    <option value="">Select NIK Approval</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->username }}">{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="aktif">aktif</option>
                                    <option value="tidak aktif">tidak aktif</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="reset" class="btn btn-warning ml-3 btn-reset">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="loading-overlay" id="loading-overlay">
            <div class="loading-spinner"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function resetSelect2Dropdowns() {
            $('.cari-users').val(null).trigger('change');
        }

        document.querySelector('.btn-reset').addEventListener('click', function(e) {
            e.preventDefault();
            resetSelect2Dropdowns();
        });

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

        $(document).ready(function() {
            $('.cari-users').select2();
        });
    </script>
@endpush
