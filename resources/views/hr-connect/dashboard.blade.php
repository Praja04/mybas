@extends('hr-connect.layouts.base')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <h4 class="mt-4 fw-semibold">HRConnect</h4>
                            <p class="text-muted mt-3">"Integrasi data karyawan masuk dan keluar"<br> Program ini khusus dibuat untuk Department GA, Admin, dan HRD. Tujuan membuat program ini agar mempermudah proses masuk maupun keluar karyawan.</p>
                            <div class="mt-4">
                                {{-- <a class="btn btn-primary rounded-pill" href="/assets/media/tutorial_hr_connect/User Guide HR Connect.pdf">
                                    Book Guidance
                                </a>&nbsp;&nbsp; --}}
                                @if (in_array('hr_connect_ga', $permissions))
                                <a class="btn btn-success rounded-pill" href="/assets/media/tutorial_hr_connect/HRGA_Full.mp4">
                                    Tutorial Untuk HRGA
                                </a>
                                @endif
                                @if (in_array('hr_connect_ir', $permissions))
                                <a class="btn btn-success rounded-pill" href="/assets/media/tutorial_hr_connect/HRIR_Full.mp4">
                                    Tutorial Untuk HRIR
                                </a>
                                @endif
                                @if (in_array('hr_connect_admin', $permissions))
                                <a class="btn btn-success rounded-pill" href="/assets/media/tutorial_hr_connect/ADMIN_Full.mp4">
                                    Tutorial Untuk Admin
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5 mb-2">
                        <div class="col-sm-7 col-8">
                            <img src="/assets/velzon/images/verification-img.png" alt="" class="img-fluid" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#book_guidance').click(function(){
        Swal.fire({
            title: "FYI Aja",
            icon: "info",
            text: "Book guidance belum tersedia"
        })
    });
});
</script>
@endpush