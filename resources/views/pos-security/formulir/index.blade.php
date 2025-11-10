@extends('pos-security.layouts.base')

@section('title', 'Dashboard')

@push('styles')
    <style>
        /* Container form saja */
        .card .form-control,
        .card .form-label,
        .card .form-check-label,
        .card .btn {
            font-size: 1.2rem !important;
        }

        /* Placeholder text */
        .card ::placeholder {
            font-size: 1.1rem !important;
        }

        /* Untuk input besar */
        .card .form-control-lg {
            font-size: 1.2rem !important;
            padding: 0.75rem 1rem;
        }

        /* Tombol lebih besar dan teks jelas */
        .card .btn-lg {
            font-size: 1.2rem !important;
            padding: 0.75rem 1rem;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(48%, 1fr));
            /* 2 kolom */
            gap: 10px;
        }

        .photo-grid img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        @include('pos-security.formulir.panduan')

        <div class="row g-4 mt-3">

            <!-- POS 1: Supplier / Transporter -->
            <div class="col-md-6">
                <a href="{{ route('pos-security.formulir.supplier') }}" class="text-decoration-none text-dark">
                    <div class="card border-0 rounded-4 shadow-lg h-100 position-relative">
                        <div class="card-body py-5 d-flex flex-column align-items-center text-center">
                            <i class="mdi mdi-truck fs-1 mb-3" style="font-size: 4rem;"></i>
                            <h4 class="fw-bold mb-2">Supplier / Transporter</h4>
                            <span class="badge bg-light text-primary mb-3 fw-semibold">Mobil • Truk • Kontainer</span>
                            <p class="mb-0 small">
                                Formulir untuk aktivitas bongkar muat kendaraan besar
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- POS 2: Tamu / Vendor / Transporter (Motor) -->
            <div class="col-md-6">
                <a href="{{ route('pos-security.formulir.tamu') }}" class="text-decoration-none text-dark">
                    <div class="card border-0 rounded-4 shadow-lg h-100 position-relative">
                        <div class="card-body py-5 d-flex flex-column align-items-center text-center">
                            <i class="mdi mdi-car fs-1 mb-3" style="font-size: 4rem;"></i>
                            <h4 class="fw-bold mb-2">Tamu / Vendor / Transporter</h4>
                            <span class="badge bg-light text-success mb-3 fw-semibold">Sepeda Motor</span>
                            <p class="mb-0 small">
                                Formulir untuk Tamu, Vendor, Kontraktor, dan bongkar muat kendaraan kecil
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
