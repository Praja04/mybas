@extends('layouts.base-display')

@push('styles')
    <style>
        .not-visible {
            opacity: 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="card card-custom gutter-b w-100">
            <div class="card-body text-center py-5">
                <h1 class="fw-bold mb-4">Halaman Scan Makan Telah Dipindahkan</h1>
                <p class="mb-4">Silakan pilih kategori pengguna Anda untuk melanjutkan ke halaman scan yang baru.</p>
                <div>
                    <a href="/ecafesedaap-scan/staff" class="btn btn-primary btn-lg mx-3 px-5 py-2 rounded-pill">
                        <h4>Staff</h4>
                    </a>
                    <a href="/ecafesedaap-scan/non-staff" class="btn btn-primary btn-lg mx-3 px-5 py-2 rounded-pill">
                        <h4>Non-staff</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
