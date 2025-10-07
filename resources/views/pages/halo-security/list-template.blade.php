@extends('pages.halo-security.layout.base')

@section('title', 'Template BA Introgasi')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1 text-center">List Template Tanya Jawab Introgasi</h4>
            <a href="{{ route('create-template') }}" class="btn btn-primary btn-md"><i class="ri-plus-fill"></i> Tambah Data</a>
        </div>
        <div class="card-body">
            <table id="scroll-horizontal" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col">Pertanyaan Introgasi</th>
                            <th scope="col">Jawaban Introgasi</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $item)
                        <tr>
                            <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                            <td>{!! $item->pertanyaan_introgasi !!}</td>
                            <td>{!! $item->jawaban_introgasi !!}</td>
                            <td>
                                <div class="hstack gap-3 fs-15 d-flex justify-content-center">
                                    <a href="{{ route('edit-template', ['id'=>$item->id]) }}" class="btn btn-outline-warning"><i class="ri-pencil-fill"></i></a>
                                    <form action="{{ route('destroy-template', ['id'=>$item->id]) }}" method="post">
                                        <button class="btn btn-outline-danger" onclick="return confirm('Apakah anda yakin ingin menghapus data Template Tanya Jawab Introgasi ini?');" type="submit"><i class="ri-delete-bin-2-line"></i></button>
                                        @csrf
                                        @method('delete')
                                    </form>
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
            <div class="d-flex justify-content-center mt-4">
                {!! $templates->links() !!}
            </div>
        </div>
    </div>
</div>

@endsection