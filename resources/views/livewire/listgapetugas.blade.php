<div class="container-fluid">
    {{-- <form action="{{route('petugas')}}" method="get">
        {{csrf_field()}}
        <div class="mb-3 row d-flex justify-content-between">
            <div class="col-10 d-flex">
                <div class="input-group">
                    <span class="input-group-text" id="addon-wrapping"><i class="ri-search-2-line"></i></span>
                    <input type="text" name="search" value="{{isset($_GET['search']) ? $_GET['search'] : ''}}" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="col-2 d-flex">
                <button type="submit" class="btn btn-md btn-primary"><i class="ri-search-fill" style="margin-top: 10px; margin-right: 4px;"></i> Search</button>
            </div>
        </div>
    </form> --}}
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1 text-center">List GA Petugas</h4>
        </div>
        <div class="card-body">
            <table id="petugas" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">Nik</th>
                            <th scope="col" class="text-center">Nama</th>
                            <th scope="col" class="text-center">Active</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($petugass as $item)
                        <tr>
                            <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                            <td scope="row" class="text-center">{{  $item->nik  }}</td>
                            <td scope="row" class="text-center">{{  $item->nama  }}</td>
                            <td scope="row" class="text-center">
                                <a href="{{ route('active-petugas', ['id_petugas' => $item->id_petugas]) }}" class="btn btn-outline-{{ $item->active ? 'success' : 'danger' }}">{{ $item->active ? 'Aktif' : 'Tidak Aktif' }}</a>
                            </td>
                            <td>
                                <div class="hstack gap-3 fs-15 d-flex justify-content-center">
                                    {{-- @if (in_array('hs_edit_gapetugas', $permissions)) --}}
                                    <a href="{{ route('edit-petugas', ['id_petugas' => $item->id_petugas]) }}" class="btn btn-outline-warning"><i class="ri-pencil-fill"></i></a>
                                    {{-- @endif --}}
                                    {{-- @if (in_array('hs_hapus_gapetugas', $permissions)) --}}
                                    <button onclick="deleteGaPetugas(this)" class="btn btn-outline-danger" data-id="{{ $item->id_petugas }}"><i class="ri-delete-bin-2-line"></i></button>
                                    {{-- @endif --}}
                                    {{-- <form action="{{ route('destroy-petugas', ['id_petugas'=>$item->id_petugas]) }}" method="post">
                                        <button class="btn btn-outline-danger" onclick="return confirm('Apakah anda yakin ingin menghapus Data GA Petugas ini ?');" type="submit"><i class="ri-delete-bin-2-line"></i></button>
                                        @csrf
                                        @method('delete')
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="d-flex justify-content-center">
                                        <img src="/found.png" style="width: 200px; margin: 50px;">
                                    </div>
                                    <div class="text-center">
                                        <h1>Data tidak ditemukan</h1>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
            </table>
            {{-- <div class="d-flex justify-content-center mt-4">
                {!! $petugass->links() !!}
            </div> --}}
        </div>
    </div>
</div>

@push('scripts')
<script type="application/javascript">

    function deleteGaPetugas(e){

        let id_petugas = e.getAttribute('data-id');

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: 'Apakah anda yakin?',
            text: "Ingin menghapus data GA Monitoring Petugas ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Tidak, Batalkan',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                if (result.isConfirmed){

                    $.ajax({
                        type:'DELETE',
                        url:'{{url("/ga-monitoring/ga-petugas/destroypetugas")}}/' +id_petugas,
                        data:{
                            "_token": "{{ csrf_token() }}",
                        },
                        success:function(data) {
                            if (data.success){
                                swalWithBootstrapButtons.fire(
                                    'Dihapus!',
                                    'Data GA Monitoring Petugas berhasil dihapus.',
                                    "success"
                                );
                                $("#"+id_petugas+"").remove();
                                setTimeout(function(data) { 
                                    window.location = `{{ url('/ga-monitoring/ga-petugas/listpetugas') }}`;
                                }, 2000);
                            }

                        }
                    });

                }

            } else if (
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Dibatalkan!',
                    'Proses menghapus data GA Monitoring Petugas dibatalkan',
                    'error'
                );
            }
        });

    }

</script>

<script>
    $(document).ready(function () {
        $('#petugas').DataTable();
    });
</script>
@endpush