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
                            <span class="card-label font-weight-bolder text-dark">Edit Data</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ url('/masukharilibur/master_approval') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                    <div class="card-body" id="card-body">
                        <form id="form-edit" action="{{ url('/masukharilibur/update_data') }}" method="POST">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="id" value="{{ $data->id }}">
                            <div class="form-group">
                                <label for="nik_admin">NIK Admin</label>
                                <input type="text" class="form-control" id="nik_admin" name="nik_admin" required
                                    value="{{ $data->nik_admin }}">
                            </div>
                            <div class="form-group">
                                <label for="nama_admin">Nama Admin</label>
                                <input type="text" class="form-control" id="nama_admin" name="nama_admin" required
                                    value="{{ $data->nama_admin }}">
                            </div>
                            <div class="form-group">
                                <label for="nik_approval">NIK Approval</label>
                                <input type="text" class="form-control" id="nik_approval" name="nik_approval" required
                                    value="{{ $data->nik_approval }}">
                            </div>
                            <div class="form-group">
                                <label for="nama_approval">Nama Approval</label>
                                <input type="text" class="form-control" id="nama_approval" name="nama_approval" required
                                    value="{{ $data->nama_approval }}">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option @if ($data->status == 'aktif') selected @endif value="aktif">aktif</option>
                                    <option @if ($data->status == 'tidak aktif') selected @endif value="tidak aktif">tidak
                                        aktif</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary" id="btn-submit">Simpan</button>
                                <button type="reset" class="btn btn-warning ml-3">Reset</button>
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
    <script>
        // Function to show the spinner
        function showSpinner() {
            document.getElementById('loading-overlay').style.display = 'block';
            document.getElementById('card-body').style.opacity = '0.5';
        }

        // Function to hide the spinner
        function hideSpinner() {
            document.getElementById('loading-overlay').style.display = 'none';
            document.getElementById('card-body').style.opacity = '1';
        }

        window.onload = function() {
            hideSpinner();
        };

        document.querySelector('#form-edit').addEventListener('submit', function() {
            showSpinner();
        });

        document.querySelector('#form-edit').addEventListener('submit', function() {
            setTimeout(function() {
                hideSpinner();
            }, 2000);
        });
    </script>
@endpush
