@extends('pages.halo-security.layout.base')

@section('title', 'BA Laporan Kejadian')

@section('content')

<div class="col-md-12">
    <div class="tabel-modal">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title" style="font-size: 13px;">Contoh Informasi Template Tanya Jawab Introgasi</h4>
                    <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#myModal">Tambah Data Template</a>
                </div>
                <div id="success_message" role="alert" class="mt-3"></div>
            </div>
            <div class="card-body">
                <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pertanyaan</th>
                            <th>Jawaban</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                {{-- <div class="d-flex justify-content-center mt-4">
                    {!! $templates->links() !!}
                </div> --}}
            </div>
        </div>
    </div>
</div>

{{-- Tambah Data Template BAI Items --}}
<div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Tambah Data Template BA Items Introgasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
                <div class="modal-body">
                        <ul id="saveform_errList"></ul>
                        <div>
                            <label for="pertanyaan_introgasi" class="form-label">Pertanyaan</label>
                            <textarea class="form-control pertanyaan_introgasi" id="pertanyaan_introgasi" name="pertanyaan_introgasi" rows="3"></textarea>
                        </div>
                        <div>
                            <label for="jawaban_introgasi" class="form-label">Jawaban</label>
                            <textarea class="form-control jawaban_introgasi" id="jawaban_introgasi" name="jawaban_introgasi" rows="3"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary add_template">Simpan</button>
                    </div>
                </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->

{{-- Ubah Data Template BAI Items --}}
{{-- <div id="EditTemplateModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ubah Data Template BA Items Introgasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
                <div class="modal-body">
                        <ul id="updateform_errList"></ul>
                        <input type="hidden" id="edit_template_id">
                        <div>
                            <label for="pertanyaan_introgasi" class="form-label">Pertanyaan</label>
                            <textarea class="form-control pertanyaan_introgasi" id="edit_pertanyaan_introgasi" name="pertanyaan_introgasi" rows="3"></textarea>
                        </div>
                        <div>
                            <label for="jawaban_introgasi" class="form-label">Jawaban</label>
                            <textarea class="form-control jawaban_introgasi" id="edit_jawaban_introgasi" name="jawaban_introgasi" rows="3"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary update_template">Ubah</button>
                    </div>
                </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div> --}}

@endsection

@push('scripts')
<script>
    // Template CRUD data pertanyaan dan jawaban introgasi
    $(document).ready(function(){

        // Menampilkan data
        // fetchtemplate();

        // function fetchtemplate()
        // {
        //     $.ajax({
        //         type: "GET",
        //         url: "{{ route('get-template-introgasi') }}",
        //         dataType: "json",
        //         success: function (response){
        //             // console.log(response.templates);
        //             // $('table').html("");
        //             $.each(response.templates, function (key, item) {
        //                 $('table').append('<tbody>\
        //                     <tr>\
        //                     <td>'+item.id+'</td>\
        //                     <td>'+item.pertanyaan_introgasi+'</td>\
        //                     <td>'+item.jawaban_introgasi+'</td>\
        //                     <td class="text-center">\
        //                         <a href="#" value="'+item.id+'" class="btn btn-warning btn-sm edit_template">Edit</a>\
        //                         <a href="#" value="'+item.id+'" class="btn btn-danger btn-sm delete_template">Hapus</a>\
        //                     </td>\
        //                     </tr>\
        //                     </tbody>\
        //                     ');
        //             });
        //         }
        //     });
        // }

        // Tambah data
        $(document).on('click', '.add_template', function(e){
            e.preventDefault();

            var template = {
                'pertanyaan_introgasi': $('.pertanyaan_introgasi').val(),
                'jawaban_introgasi': $('.jawaban_introgasi').val(),
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('create-template-introgasi') }}",
                data: template,
                dataType: "json",
                success: function (response) {
                    if (response.status == 400) 
                    {
                        $('#saveform_errList').html("");
                        $('#saveform_errList').addClass('alert alert-danger shadow');
                        $.each(response.errors, function (key, err_values){
                            $('#saveform_errList').append('<li>'+err_values+'</li>');
                        });
                    }
                    else
                    {
                        $('#saveform_errList').html("");
                        $('#success_message').addClass('alert alert-success shadow')
                        $('#success_message').text(response.message)
                        $('#myModal').modal('hide');
                        $('#myModal').find('textarea').val("");
                        fetchtemplate();
                    }
                }
            });
        });

        // Ubah data
        // $(document).on('click', '.edit_template', function(e){
        //     e.preventDefault();

        //     var template_id = $(this).val();
        //     $('#EditTemplateModal').modal('show');
        //     $.ajax({
        //         type: "GET",
        //         url: '{{ URL::to('prosesgetedittemplateintrogasi') }}/'+template_id,
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         success: function (response){
        //             console.log(response);
        //             // if(response.status == 404){
        //             //     $('#success_message').html("");
        //             //     $('#success_message').addClass('alert alert-danger shadow');
        //             //     $('#success_message').text(response.message);
        //             // }
        //             // else
        //             // {
        //             //     $('#edit_pertanyaan_introgasi').val(response.template.pertanyaan_introgasi);
        //             //     $('#edit_jawaban_introgasi').val(response.template.jawaban_introgasi);
        //             //     $('#edit_template_id').val(template_id);
        //             // }
        //         }
        //     });
        // });
    });
</script>
@endpush