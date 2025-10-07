@extends('layouts.base-sidebar')

@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .fixTableHead {
            overflow-y: auto;
            height: 400px;
        }

        .fixTableHead thead th {
            position: sticky;
            top: 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px 15px;
        }

        th {
            background: #dbdbdb;
        }

        /* Styles for Scroll to Top Button */
        .scrollToTopBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="main-body">
            <div class="card">
                <form action="{{ url('edoc/post_pic') }}" method="post" id="form-pic">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="float-right">
                                    <a href="javascript:void(0)" class="btn btn-dark btn-lg mb-4" id="Add"><i
                                            class="fas fa-plus-circle"></i> Tambah
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="table">
                                        <thead>
                                            <tr>
                                                <th class="text-center" colspan="5">LIST PIC E-DOC</th>
                                            </tr>
                                            <tr class="text-center">
                                                <th>No.</th>
                                                <th>#</th>
                                                <th>NAMA</th>
                                                <th>NIK</th>
                                                <th>PIC</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pic as $list)
                                                <tr class="text-center">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="javascript:void(0)" onclick="Hapus('{{ $list->id }}')"
                                                            class="btn btn-danger btn-sm"><i style="fas fa-trash"></i>
                                                            Hapus</a>
                                                    </td>
                                                    <td>{{ $list->nama }}</td>
                                                    <td>{{ $list->nik }}</td>
                                                    <td>{{ $list->dept }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="float-right">
                            <button type="submit" class="btn btn-lg btn-info mb-4 BtnSave"><i class="fas fa-save"></i>
                                Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ url('edoc/post_pic') }}" method="post">
                        @csrf
                        <div class="row">

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <select class="form-control" name="pic[]" id="SelectPIC" style="width : 100%;"
                                        required>
                                        <option value="" selected disabled>PILIH PIC</option>
                                        @foreach ($user as $item)
                                            <option value="{{ $item->username }}">{{ $item->name . '-' . $item->dept }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="pic[]" id="SelectPIC" style="width : 100%;"
                                        required>
                                        <option value="" selected disabled>PILIH DEPT</option>
                                        @foreach ($dept as $item)
                                            <option value="{{ $item->name }}">{{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="AppendPic">

                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info btn-lg"> Simpan</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" class="btn btn-info btn-lg scrollToTopBtn"><i class="fas fa-arrow-up"></i></button>
@endsection


@push('scripts')
    <script type="text/javascript">
        function Hapus(id) {
            location.href = "{{ url('edoc/deletepic') }}/" + id;
        }
        var html = `<tr class="text-center">
                        <td></td>
                        <td> 
                        </td>
                        <td> 
                        <div class="form-group">
                                <select class="form-control SelectPIC" name="pic[]" id="" style="width : 100%;"
                                    required>
                                    <option value="" selected disabled>PILIH PIC</option>
                                    @foreach ($user as $item)
                                        <option value="{{ $item->username }}">{{ $item->name . '-' . $item->dept }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>`;
        $('.SelectPIC').select2();

        $("#Add").click(function() {
            $('#table > tbody').append(html);
            $('.SelectPIC').select2();
            scrollToBottom();
        });

        // update tambah data mengarah scroll kebawah
        function scrollToBottom() {
            $('html, body').animate({
                scrollTop: $(document).height()
            }, 'slow');
        }

        $('#form-pic').on('submit', function() {
            $('.BtnSave').toggle('slow')
        });

        // show or hide button arrow ketika masih berada diatas
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('.scrollToTopBtn').fadeIn();
            } else {
                $('.scrollToTopBtn').fadeOut();
            }
        });

        // scroll keatas ketika button arrow di klik
        $('#scrollToTopBtn').click(function() {
            $('html, body').animate({
                scrollTop: 0
            }, 'slow');
            return false;
        });
    </script>
@endpush
