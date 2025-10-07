@extends('layouts.base')

@section('content')

<div class="container-fluid">
    <div class="row">

        <div class="col-lg-12">
            <!--begin::Advance Table Widget 4-->
            <div class="card card-custom card-stretch gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">Reporting </span>
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table" id="tbl_report">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>NAMA</th>
                                <th>WAKTU SCAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scan as $d)
                                <tr>
                                    <td>{{ $d->nik }}</td>
                                    <td>{{ $d->nama }}</td>
                                    <td>{{ $d->waktu }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Advance Table Widget 4-->
        </div>
    </div>
</div>


@endsection
@push('scripts')
<script src="{{ url('/') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>

    <script type="text/javascript">
        $('#tbl_report').DataTable({
            dom: "<'row'<'col-sm-6 text-left'f><'col-sm-6 text-right'B>>\
			<'row'<'col-sm-12'tr>>\
			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
            buttons: [
                'copy', 'excel', 'pdf'
            ]
        });
    </script>
@endpush