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
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        @include('pos-security.formulir.cek-kendaraan.panduan')

        @include('pos-security.formulir.cek-kendaraan.form')
    </div>
@endsection
