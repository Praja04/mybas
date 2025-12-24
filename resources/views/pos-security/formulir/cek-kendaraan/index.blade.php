@extends('pos-security.layouts.base')

@section('title', 'Form Pengecekan Kendaraan')

@push('styles')
    <style>
        .tab-card {
            border: 1px solid #dee2e6;
            background-color: #fff;
            padding: 1rem 2rem;
            font-weight: bold;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .tab-card.active {
            border-color: #0d6efd;
            background-color: #e7f1ff;
            color: #0d6efd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .tab-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .foto-slot {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .foto-slot .preview-container {
            flex: 1 1 auto;
        }

        .foto-slot button {
            margin-top: auto;
        }

        .cek-stepper {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #adb5bd;
            font-weight: 500;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #adb5bd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            background: #fff;
        }

        .step-label {
            white-space: nowrap;
        }

        .step-line {
            width: 60px;
            height: 2px;
            background: #dee2e6;
        }

        .step-item.active {
            color: #4f46e5;
        }

        .step-item.active .step-circle {
            border-color: #4f46e5;
            background: #4f46e5;
            color: #fff;
        }

        .step-item.done {
            color: #16a34a;
        }

        .step-item.done .step-circle {
            border-color: #16a34a;
            background: #16a34a;
            color: #fff;
        }

        @media (max-width: 767px) {
            .cek-stepper {
                gap: 12px;
            }

            .step-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 4px;
            }

            .step-label {
                display: block;
                font-size: 12px;
                line-height: 1.2;
                white-space: normal;
                max-width: 80px;
            }

            .step-line {
                width: 30px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- panduan dan menu tabs --}}
        @include('pos-security.formulir.cek-kendaraan.panduan')

        <div class="tab-content" id="formTabsContent">
            {{-- @include('pos-security.formulir.cek-kendaraan.form') --}}

            {{-- IN KENDARAAN --}}
            @include('pos-security.formulir.cek-kendaraan.form-in')

            {{-- OUT KENDARAAN --}}
            @include('pos-security.formulir.cek-kendaraan.form-out')
        </div>
    </div>
@endsection
