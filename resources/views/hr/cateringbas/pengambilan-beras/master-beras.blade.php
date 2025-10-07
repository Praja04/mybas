@extends('hr.cateringbas.pengambilan-beras.layouts.app')

@section('title', 'Dashboard')

@push('after-style')
    <style>
        /* styling loading spinner */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
        }

        .card:hover {
            transform: scale(1.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .card a {
            display: block;
            width: 100%;
            height: 100%;
            text-decoration: none;
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
@endpush

@section('content')
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Dashboard Data Beras</h3>
        </div>
        <div class="page-content">
            <div class="row">
                {{-- <div id="loading-overlay" class="loading-overlay">
                    <div class="loader"></div>
                </div> --}}
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <a href="{{ route('kedatangan-beras.index') }}">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon purple mb-2">
                                            <img src="{{ asset('assets/mazer/dist/assets/compiled/png/truck.png') }}"
                                                alt="mobil truck" width="30px">
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Kedatangan Beras</h6>
                                        @php
                                            $lastItem = $berasStockData->last();
                                        @endphp
                                        @if ($lastItem)
                                            <h6 class="font-extrabold mb-0">{{ $lastItem->kedatangan_stock }} <span
                                                    class="badge bg-secondary">{{ $lastItem->satuan_berat }}</span></h6>
                                        @else
                                            <p>Data kedatangan belum ada.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <a href="{{ route('kedatangan-beras.jumlah.beras') }}">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon green mb-2">
                                            <img src="{{ asset('assets/mazer/dist/assets/compiled/png/rice.png') }}"
                                                alt="mobil truck" width="30px">
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Jumlah Stock Beras</h6>

                                        @php
                                            $lastItem = $berasStockData->last();
                                            $stockData = $lastItem ? $lastItem->stock->last() : null;
                                        @endphp

                                        @if ($stockData)
                                            <h6 class="font-extrabold mb-0">{{ $stockData->jumlah_stock }} <span
                                                    class="badge bg-secondary">{{ $stockData->satuan_berat }}</span></h6>
                                        @else
                                            <p>Data stock belum ada.</p>
                                        @endif

                                    </div>

                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <a href="{{ route('kedatangan-beras.pengambilan') }}">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon blue mb-2">
                                            <img src="{{ asset('assets/mazer/dist/assets/compiled/png/hand-take.png') }}" alt="icon" width="30px">
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Pengambilan</h6>
                                        <h6 class="font-extrabold mb-0">
                                            <?php
                                            $JumlahPengambilan = App\BerasPengambilan::sum('jumlah_pengambilan');
                                            echo $JumlahPengambilan !== null && $JumlahPengambilan !== 0 ? $JumlahPengambilan : 'Data pengambilan belum ada.';
                                            ?>
                                            <span class="badge bg-secondary">sak</span> <!-- Adjust this as needed -->
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <a href="{{ route('kedatangan-beras.pemakaian') }}">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                        <div class="stats-icon red mb-2">
                                            <img src="{{ asset('assets/mazer/dist/assets/compiled/png/out-rice.png') }}" alt="icon" width="30px">
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Pemakaian</h6>
                                        <h6 class="font-extrabold mb-0">
                                            <?php
                                            $JumlahPemakaian = App\BerasPemakaian::sum('jumlah_pemakaian');
                                            echo $JumlahPemakaian !== null && $JumlahPemakaian !== 0 ? $JumlahPemakaian : 'Data pemakaian belum ada.';
                                            ?>
                                            <span class="badge bg-secondary">Liter</span> <!-- Adjust this as needed -->
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
            </div>
            {{-- <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Total Kedatangan beras Bulan Ini</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-profile-visit"></div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        </section>
    </div>
    </div>

    <div class="modal fade" id="create-modal-stock" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i> Buat Perizinan Baru
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tambahKedatanganStock">
                        @csrf
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="kedatangan-stock">Jumlah Stock Beras</label>
                            <div class="col-9">
                                <div class="input-group gap-2">
                                    <input name="kedatangan_stock" required placeholder="masukkan dalam bentuk angka"
                                        class="form-control" type="number" value="" id="kedatangan-stock">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-secondary text-white">SAK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-3"></div>
                            <div class="col-9">
                                <button id="submitButton" type="submit" class="btn btn-primary"><i
                                        class="fa fa-paper-plane"></i> Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-script')
    {{-- <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> --}}
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        // eksekusi loading screen
        $(window).on('load', function() {
            $('#loading-overlay').fadeOut('slow');
        });
    </script>
    <script type="text/javascript">
        // eksekusi loading screen
        $(window).on('load', function() {
            $('#loading-overlay').fadeOut('slow');

            // Check if kedatangan_stock is null or empty
            @if (!$lastItem || is_null($lastItem->kedatangan_stock))
                // Show SweetAlert
                Swal.fire({
                    title: 'Data Kedatangan Kosong nih!',
                    text: 'Jika data kosong, silahkan ke menu kedatangan.',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
@endpush
