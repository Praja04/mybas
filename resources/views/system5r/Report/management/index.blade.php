@extends('system5r.layouts.base')

@section('title', 'Report For Management')

@push('styles')
    <style>
        table p {
            margin-bottom: 0 !important
        }
        table td {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }
    </style>
@endpush

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h3>Report 5R For Management</h3>
            <div class="mt-3">
                <form action="">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label for="filter_jadwal">JADWAL</label>
                                <select name="filter_jadwal" id="filter_jadwal" class="form-control">
                                    <option value="---">PILIH JADWAL</option>
                                    @foreach ($allJadwal as $_jadwal)
                                    <option @if(isset($_GET['filter_jadwal']) && $_GET['filter_jadwal'] == $_jadwal->id_jadwal) selected @endif value="{{ $_jadwal->id_jadwal }}">{{ $_jadwal->tahun }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary waves-effect waves-light">
                        LIHAT LAPORAN
                    </button>
                </form>
            </div>
            @if(isset($_GET['filter_jadwal']) && $_GET['filter_jadwal'] != '---')
            <hr />
            @if($workspace == null)
            <div class="alert alert-warning">
                <strong>Woops!</strong> Maaf anda belum terdaftar sebagai comittee dari department manapun.
            </div>
            @else
            @foreach ($workspace as $item)
                <div class="card shadow-none border">
                    <div class="card-body">
                        <h3>{{ $item->name }}</h3>
                        @php $data = $item->departments @endphp
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    {{-- <table class="table align-middle table-bordered" id="table-summary-penilaian">
                                        <thead>
                                            <tr class="pas-background-color" style="border-color: #000">
                                                <th class="text-white">DEPT</th>
                                                <th class="text-white">PERIODE</th>
                                                <th class="text-white">JURI</th>
                                                <th class="text-white">NILAI</th>
                                                <th class="text-white">TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data->sortByDesc('__total') as $department)
                                                <tr>
                                                    <td>{{ $department->id_department }}</td>
                                                    <td>{{ $department->periode[0]->nama_periode }}</td>
                                                    <td>
                                                        @if(count($department->periode[0]->juri) > 0)
                                                        @foreach($department->periode[0]->juri as $_juri)<span>{{ $_juri }}@if(!$loop->last), @endif</span>@endforeach
                                                        @else-@endif
                                                    </td>
                                                    <td>{{ $department->periode[0]->totalNilai }}</td>
                                                    <td>{{ round($department->__total, 1) }}</td>
                                                </tr>
                                                @foreach ($department->periode as $periode)
                                                    @if($loop->iteration > 1)
                                                    <tr>
                                                        <td style="color: white">{{ $department->id_department }}</td>
                                                        <td>{{ $periode->nama_periode }}</td>
                                                        <td>
                                                            @if(count($periode->juri) > 0)
                                                            @foreach($periode->juri as $_juri)<span>{{ $_juri }}@if(!$loop->last), @endif</span>@endforeach
                                                            @else-@endif
                                                        </td>
                                                        <td>{{ round($periode->totalNilai, 1) }}</td>
                                                        <td style="color: white">{{ round($department->__total, 1) }}</td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table> --}}
                                    <table class="table align-middle table-bordered" id="table-summary-penilaian">
                                        <thead>
                                            <tr class="pas-background-color" style="border-color: #000">
                                                <th class="text-white">DEPT</th>
                                                <th class="text-white">PERIODE</th>
                                                <th class="text-white">JURI</th>
                                                <th class="text-white">NILAI</th>
                                                <th class="text-white">TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data->sortByDesc('__total') as $department)
                                                <tr>
                                                    <td>{{ $department->id_department }}</td>
                                                    <td>{{ $department->periode[0]->nama_periode }}</td>
                                                    <td>
                                                        @if(count($department->periode[0]->juri) > 0)
                                                            @foreach($department->periode[0]->juri as $_juri)
                                                                <span>{{ $_juri }}@if(!$loop->last), @endif</span>
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ round($department->periode[0]->totalNilai, 1) }}</td>
                                                    <td>{{ round($department->__total, 1) }}</td>
                                                </tr>
                                                @foreach ($department->periode as $periode)
                                                    @if($loop->iteration > 1)
                                                        <tr>
                                                            <td style="color: white">{{ $department->id_department }}</td>
                                                            <td>{{ $periode->nama_periode }}</td>
                                                            <td>
                                                                @if(count($periode->juri) > 0)
                                                                    @foreach($periode->juri as $_juri)
                                                                        <span>{{ $_juri }}@if(!$loop->last), @endif</span>
                                                                    @endforeach
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>{{ round($periode->totalNilai, 1) }}</td>
                                                            <td style="color: white">{{ round($department->__total, 1) }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="nav nav-pills flex-column nav-pills-tab custom-verti-nav-pills text-center" style="margin-top: 40px" role="tablist" aria-orientation="vertical">
                                                @foreach ($data->sortByDesc('__total') as $department)
                                                <a class="nav-link @if($loop->iteration == 1) active show @endif" style="margin-top: 8.6px" id="custom-v-pills-{{ $department->id_department }}-tab" data-bs-toggle="pill" href="#custom-v-pills-{{ $department->id_department }}" role="tab" aria-controls="custom-v-pills-{{ $department->id_department }}" aria-selected="true">
                                                    {{ $department->id_department }}
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <div class="tab-content text-muted">
                                                @foreach ($data->sortByDesc('__total') as $department)
                                                <div class="tab-pane fade @if($loop->iteration == 1) active show @endif" id="custom-v-pills-{{ $department->id_department }}" role="tabpanel" aria-labelledby="custom-v-pills-{{ $department->id_department }}-tab">
                                                    <div class="row">
                                                      
                                                        @foreach ($department->periode as $periode)
                                                            <div class="col-md-12">
                                                                <table class="table align-middle">
                                                                    <thead>
                                                                        <tr class="pas-background-color">
                                                                            <th class="text-white" colspan="2">{{ $periode->nama_periode }}</th>
                                                                            <th class="text-white">
                                                                                <strong>{{ $periode->totalNilai }}</strong>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($periode->group as $group)
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="d-flex justify-content-between">
                                                                                        <a href="javascript:;" onClick="getDetail('{{ $periode->id_periode }}', '{{ $group->id_group }}')">
                                                                                            <div>{{ $group->nama_group }} <i class="mdi mdi-open-in-new"></i></div>
                                                                                            <div style="margin-top: -5px"><small>Click For Detail</small></div>
                                                                                        </a>
                                                                                        <div>
                                                                                            <a target="_blank" href="{{ url('5r-system/report/download') . '/' . encrypt($department->id_department . '/' . $_GET['filter_jadwal'] . '/' . $periode->id_periode . '/' . $group->id_group) }}" data-bs-toggle="tooltip" title="Print hasil penilaian" type="button" class="btn p-0 btn-sm waves-effect waves-light shadow-none">
                                                                                                <i class="mdi mdi-printer mdi-24px"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td>{{ $group->persentase }}%</td>
                                                                                <td>{{ $group->totalNilai / ($group->persentase / 100) }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @endif
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">DETAIL PENILAIAN</h5>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <table id="table-detail" class="table table-striped">
                    <thead>
                        <tr class="pas-background-color">
                            <th class="text-white px-1" style="border-width: 1px solid #fff">GROUP</th>
                            <th class="text-white">PERTANYAAN</th>
                            <th class="text-white">NILAI</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')

<script>
    $('#table-summary-penilaian').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Export to Excel',
                className: 'btn btn-success waves-effect waves-light',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                },
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('row:first c', sheet).attr('s', '42');
                }
            }
        ],
        "paging": false,
        "ordering": false,
        "info": false,
        "searching": false,
        // "columnDefs": [
        //     { "width": "10%", "targets": 0 },
        //     { "width": "10%", "targets": 1 },
        //     { "width": "10%", "targets": 2 },
        //     { "width": "10%", "targets": 3 },
        //     { "width": "10%", "targets": 4 },
        // ]
    });

    // function getDetail(idPeriode, idGroup)
    // {
    //     var colors = ['#264653', '#2a9d8f', 'e9c46a', '#f4a261', '#e76f51'];
    //     var textColors = ['#fff', '#fff', '#000', '#000', '#000'];

    //     $.ajax({
    //         url: "{{ route('5r-system.report.detail') }}",
    //         type: 'POST',
    //         data: {
    //             id_periode: idPeriode,
    //             id_group: idGroup
    //         },
    //         success: function(response) {
    //             if(response.status == 'success') {
    //                 $('#table-detail tbody').html('')
    //                 var noParent = 1;
    //                 console.log(response.data)
    //                 Object.values(response.data).forEach(function(item) {
    //                     var no = 1;

    //                     item.forEach(function(jawaban) {
    //                         console.log(item, jawaban)

    //                         var firtColumn = '';

    //                         if(no == 1) {
    //                             firtColumn = `<td class="p-0" style="vertical-align: middle; font-size: 10px; font-weight: bold; text-align: center; background-color: ${colors[noParent-1]}; color: ${textColors[noParent-1]}" rowspan="${item.length}">${jawaban.pertanyaan.jenis}</td>`
    //                         }

    //                         if(jawaban.foto != null) {
    //                             var foto = '';
    //                             var fotoNameArray = jawaban.foto.split(',');
    //                             fotoNameArray.forEach(function(fotoName) {
    //                                 foto += `
    //                                     <div class="d-flex mb-1">
    //                                         <img src="{{ asset('images/5r') }}/${fotoName}" alt="Foto" style="width: 300px" />
    //                                     </div>`
    //                             })
    //                         }else{
    //                             var foto = `<i class="text-muted">No Foto</i>`
    //                         }

    //                         $('#table-detail tbody').append(`
    //                             <tr>
    //                                 ${firtColumn}
    //                                 <td>
    //                                     <div style="width: 300px">
    //                                         <h6>ITEM PERIKSA</h6>
    //                                         ${jawaban.pertanyaan.item_periksa}
    //                                         <h6 class="mt-3">KETERANGAN</h6>
    //                                         ${jawaban.pertanyaan.keterangan}
    //                                     </div>
    //                                 </td>
    //                                 <td>
    //                                     <h6>NILAI <span title="Wajib diisi" class="text-danger">*</span></h6>
    //                                     <input style="width: 100px" class="form-control" disabled value="${jawaban.nilai}" />
    //                                     <div class="mt-3">
    //                                         <h6>FOTO</h6>
    //                                         ${foto}
    //                                     </div>
    //                                     <div class="mt-3 rounded bg-light p-1">
    //                                         <h6>KETERANGAN</h6>
    //                                         <p>${jawaban.keterangan}</p>
    //                                     </div>
    //                                 </td>
    //                             </tr>
    //                         `)

    //                         no++;
    //                     })

    //                     noParent++;
    //                 })

    //                 $('#detailModal').modal('show')
    //             }else{
    //                 Swal.fire({
    //                     title: 'Woops!',
    //                     text: response.message,
    //                     icon: 'error',
    //                     confirmButtonText: 'OK'
    //                 })
    //             }
    //         }
    //     });
    // }
    function getDetail(idPeriode, idGroup) {
    $.ajax({
        url: "{{ route('5r-system.report.detail') }}",
        type: 'POST',
        data: {
            id_periode: idPeriode,
            id_group: idGroup
        },
        success: function(response) {
            if (response.status == 'success') {
                $('#table-detail tbody').html('');
                var noParent = 1;
                Object.values(response.data).forEach(function(item) {
                    var no = 1;
                    item.forEach(function(jawaban) {
                        var foto = '';
                        if (jawaban.foto != null) {
                            var fotoNameArray = jawaban.foto.split(',');
                            fotoNameArray.forEach(function(fotoName) {
                                foto += `
                                    <div class="d-flex mb-1">
                                        <img src="{{ asset('images/5r') }}/${fotoName}" alt="Foto" style="width: 300px" />
                                    </div>`;
                            });
                        } else {
                            foto = `<i class="text-muted">No Foto</i>`;
                        }
                        $('#table-detail tbody').append(`
                            <tr>
                                <td>${jawaban.pertanyaan.jenis}</td>
                                <td>
                                    <div style="width: 300px">
                                        <h6>ITEM PERIKSA</h6>
                                        ${jawaban.pertanyaan.item_periksa}
                                        <h6 class="mt-3">KETERANGAN</h6>
                                        ${jawaban.pertanyaan.keterangan}
                                    </div>
                                </td>
                                <td>
                                    <h6>NILAI</h6>
                                    <input style="width: 100px" class="form-control" disabled value="${jawaban.nilai}" />
                                    <div class="mt-3">
                                        <h6>FOTO</h6>
                                        ${foto}
                                    </div>
                                    <div class="mt-3 rounded bg-light p-1">
                                        <h6>KETERANGAN</h6>
                                        <p>${jawaban.keterangan}</p>
                                    </div>
                                </td>
                            </tr>
                        `);
                        no++;
                    });
                    noParent++;
                });
                $('#detailModal').modal('show');
            } else {
                Swal.fire({
                    title: 'Woops!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }
    });
}
</script>

@endpush
