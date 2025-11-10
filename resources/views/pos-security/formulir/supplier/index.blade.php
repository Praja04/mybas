@extends('pos-security.layouts.base')

@section('title', 'Form Supplier')

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
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- panduan dan menu tabs --}}
        @include('pos-security.formulir.supplier.panduan')

        <!-- Tabs Content -->
        <div class="tab-content" id="formTabsContent">
            <!-- Supplier IN Tab -->
            @include('pos-security.formulir.supplier.form-in')
            <!-- Supplier OUT Tab -->
            @include('pos-security.formulir.supplier.form-out')
        </div>
    </div>
@endsection
