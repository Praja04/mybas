    @extends('system5r.layouts.base')

    @section('title', 'Schedule Juri')

    @push('styles')
    @endpush

    @section('content')

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h3>Schedule Juri</h3>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Periode Penilaian</label>
                            <select name="periode_penilaian" id="periode_penilaian" class="form-control">
                                <option value="">PILIH</option>
                                @foreach ($periode_penilaian as $periode)
                                <option @if(isset($_GET['periode']) && $_GET['periode'] == $periode->id_periode) selected @endif value="{{ $periode->id_periode }}">{{ $periode->jadwal->tahun }} - {{ $periode->nama_periode }} ({{ $periode->keterangan }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modalAddJadwal">
                                <i class="mdi mdi-calendar"></i>
                                Jadwal
                            </button>
                            <button class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modalPeriode">
                                <i class="mdi mdi-calendar-range"></i>
                                Periode
                            </button>
                            @if(isset($_GET['periode']))
                            <button class="btn btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modalCreateGroup">
                                <i class="mdi mdi-plus"></i>
                                Create Group
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-hover">
                            <thead>
                                <tr class="pas-background-color">
                                    <th class="text-white p-1">GROUP NAME</th>
                                    <th class="text-white p-1">JURI</th>
                                    <th class="text-white p-1">DEPARTMENT</th>
                                    <th class="text-white p-1">INDEX KESULITAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $d)
                                <tr>
                                    <td class="p-1">
                                        <div class="d-flex">
                                            <div>
                                                <strong>{{ $d->group->nama_group }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-1">
                                        <div class="d-flex">
                                            <div>
                                                <div>{{ implode(', ', $d->group->anggota->pluck('nama_juri')->toArray()) }}</div>
                                            </div>
                                            <div>
                                                <button onClick="openAddJuriModal('{{ $d->id_group_juri }}')" class="btn btn-primary btn-sm shadow-none waves-effect waves-light">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-1">{{ $d->id_department }}</td>
                                    <td class="p-1">{{ $d->index_tingkat_kesulitan }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalAddJuri" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambahkan Juri</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        {{-- <i class="mdi mdi-close"></i> --}}
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addJuriForm" method="POST" action="{{ url('5r-system/schedule-juri/add-juri') }}">
                        <input type="hidden" id="id_group_juri" name="id_group_juri">
                        @csrf
                        <div class="form-group">
                            <label for="username">User</label>
                            <select name="username" id="username" class="form-control">
                                @foreach ($users as $user)
                                <option value="{{ $user->username }}|{{ $user->name }}">{{ $user->name }} | {{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                    <button type="submit" form="addJuriForm" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalAddJadwal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Jadwal Penilaian</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        {{-- <i class="mdi mdi-close"></i> --}}
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                            <form class="bg-light bg-opacity-50 rounded-3 p-3" id="addJadwalForm" method="POST" action="{{ url('5r-system/schedule-juri/add-jadwal') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="kode_jadwal">Kode Jadwal</label>
                                    <input type="text" name="id_jadwal" class="form-control" value="{{ Str::random(10) }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="keterangan">Keterangan Jadwal</label>
                                    <input type="text" name="tahun" required class="form-control">
                                    <small><i>Contoh : 2024 Semester 1 / 2024</i></small>
                                </div>
                                <button type="submit" form="addJadwalForm" class="btn btn-success">Add</button>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <table class="table table-hover table-bordered">
                                <thead class="bg-warning bg-opacity-10">
                                    <tr>
                                        <th>Kode Jadwal</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedule as $sch)
                                    <tr>
                                        <td>{{ $sch->id_jadwal }}</td>
                                        <td>{{ $sch->tahun }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalPeriode" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Periode Penilaian</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        {{-- <i class="mdi mdi-close"></i> --}}
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                            <form class="bg-light bg-opacity-50 rounded-3 p-3" id="addPeriodeForm" method="POST" action="{{ url('5r-system/schedule-juri/add-periode') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="jadwal">Jadwal</label>
                                    <select name="id_jadwal" id="id_jadwal" required class="form-control">
                                        <option value="">Pilih</option>
                                        @foreach ($schedule as $item)
                                        <option value="{{ $item->id_jadwal }}">{{ $item->tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="nama_periode">Nama Periode</label>
                                    <input type="text" name="nama_periode" required class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="keterangan">Keterangan</label>
                                    <input type="text" name="keterangan" required class="form-control">
                                </div>
                                <button type="submit" form="addPeriodeForm" class="btn btn-success">Add</button>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <table class="table table-hover table-bordered">
                                <thead class="bg-warning bg-opacity-10">
                                    <tr>
                                        <th>Jadwal</th>
                                        <th>Nama Periode</th>
                                        <th>Keterangan</th>
                                        <th>Selesai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($periode_penilaian as $periode)
                                    <tr>
                                        <td>{{ $periode->jadwal->tahun }}</td>
                                        <td>{{ $periode->nama_periode }}</td>
                                        <td>{{ $periode->keterangan }}</td>
                                        <td>{{ $periode->selesai }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCreateGroup" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Periode Penilaian</h5>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        {{-- <i class="mdi mdi-close"></i> --}}
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form class="bg-light bg-opacity-50 rounded-3 p-3" id="createGroupForm" method="POST" action="{{ url('5r-system/schedule-juri/create-group-juri') }}">
                                @csrf
                                @if(isset($_GET['periode']))
                                <div class="form-group mb-3">
                                    <label for="id_periode">ID Periode</label>
                                    <input type="text" readonly value="{{ $_GET['periode'] }}" name="id_periode" required class="form-control">
                                </div>
                                @endif
                                <div class="form-group mb-3">
                                    <label for="nama_group">Nama Group</label>
                                    <input type="text" name="nama_group" required class="form-control" placeholder="Contoh : Group 1 / Operasional 1 / Office 1">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="department">Department</label>
                                    <select name="department" id="department" required class="form-control">
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id_department }}">{{ $department->nama_department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="index_tingkat_kesulitan">Index Tingkat Kesulitan</label>
                                    <input placeholder="Contoh: 1.0 / 1.1" type="text" name="index_tingkat_kesulitan" id="index_tingkat_kesulitan" required class="form-control" value="1">
                                    <small>Untuk tingkat kesulitan department | *Gunakan "." untuk bilangan desimal | Contoh : 1.0 / 1.1 / 1.6</small>
                                </div>
                                <button type="submit" form="createGroupForm" class="btn btn-success">Add</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push('scripts')

    <script>
        $('#periode_penilaian').on('change', function (e) {
            var id_periode = $(this).val()
            if (id_periode != '') {
                window.location.href = "{{ url('5r-system/schedule-juri') }}?periode=" + id_periode
            }
        })

        $('#username').select2({
            placeholder: 'PILIH',
            allowClear: true
        })

        function openAddJuriModal(id_group_juri) {
            $('#id_group_juri').val(id_group_juri)
            $('#modalAddJuri').modal('show')
        }
    </script>

    @endpush
