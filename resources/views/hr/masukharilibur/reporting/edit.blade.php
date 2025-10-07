@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Edit Data Karyawan</span>
                        </h3>
                    </div>
                    <!--end::Header-->

                    <!--begin::Form-->
                    <form action="{{ url('/PostEditKaryawan') }}" method="POST" id="edit">
                        @csrf
                        <input type="hidden" name="id" class="form-control" value="{{ $detail->id }}">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="exampleSelectl">NIK</label>
                                <input type="text" name="nik" class="form-control" value="{{ $detail->nik }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleSelectl">NAMA</label>
                                <input type="text" name="nama" class="form-control" value="{{ $detail->nama }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleSelectl">DEPARTEMEN</label>
                                <input type="text" name="department" class="form-control"
                                    value="{{ $detail->department }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleSelectl">SHIFT</label>
                                <select class="form-control form-control-lg" id="shift" name="shift">
                                    <option @if ($detail->shift == 1) selected @endif value="1">1</option>
                                    <option @if ($detail->shift == 2) selected @endif value="2">2</option>
                                    <option @if ($detail->shift == 3) selected @endif value="3">3</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleSelectl">STATUS KARYAWAN</label>
                                <select class="form-control form-control-lg" id="status_karyawan" name="status_karyawan">
                                    <option @if ($detail->status_karyawan == 'staff') selected @endif value="staff">Staff</option>
                                    <option @if ($detail->status_karyawan == 'non staff') selected @endif value="non staff">non staff
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!--end::Body-->

                        <!--begin::Footer-->
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm BtnUpdateFile"
                                onclick="confirmUpdate(event)" style="border-radius: 13px;">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                        <!--end::Footer-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Advance Table Widget 4-->
            </div>
        </div>
        <!--end::Row-->
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmUpdate(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda Yakin Akan Merubah Data Ini?',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                customClass: {
                    icon: 'swal2-icon-warning'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#edit').submit();
                }
            });
        }
    </script>
@endpush
