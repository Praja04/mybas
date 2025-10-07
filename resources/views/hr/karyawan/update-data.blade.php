<div class="row">
    <div class="col-md-12">
        <button onClick="createNew()" class="btn btn-primary"><i class="flaticon flaticon-add-circular-button"></i> Buat Batch Update Data</button>
    </div>
    <div class="col-md-12">
        <div class="mt-5">
            <div id="update-list"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="create-batch" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Batch Update Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-create-batch">
                    <div class="form-group mb-1">
                        <label for="keterangan">Keterangan 
                        <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control" id="keterangan" rows="3" spellcheck="false"></textarea>
                    </div>
                    <button id="submit-button" type="submit" class="btn btn-primary pull-left mt-5"><i class="flaticon flaticon2-telegram-logo"></i> Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-update-data" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="get-update-status" class="bg-warning p-1 rounded"></div>
                <button onClick="generateUpdate()" id="generate-update" class="btn btn-success mt-5" type="button">Generate Update</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">


    function generateUpdate()
    {
        $("#generate-update").attr("disabled", true)

        $("#get-update-status").html("Generating update..")
        var batchId = localStorage.getItem('batch_update_id');

        // Check data comparation with payrill
        $("#get-update-status").html("Cek selisih payroll")

        $.ajax({
            type: "POST",
            dataType: "JSON",
            url: "{{ url('/hr/karyawan/compare-data') }}",
            success: function ( response ) {
                $("#generate-update").removeAttr("disabled")
                
                if(response.difference_count > 0)
                {
                    $("#get-update-status").html("Ada selisih dengan payroll, mohon lakukan sinkronisasi terlebih dahulu..")
                    toastr.error("Ada selisih data dengan payroll. Mohon lakukan sinkronisasi terlebih dahulu");
                }

                if(response.difference_count <= 0 )
                {
                    $.ajax({
                        url: "{{ url('/hr/karyawan/update/generate-update') }}",
                        type: "POST",
                        data: {
                            'batch_id' : batchId
                        },
                        dataType: "JSON",
                        success: function (response) {
                            if(response.data.length == 0) {
                                $("#get-update-status").html("Update belum dibuat")
                                $("#generate-update").show()
                            }    
                        },
                        error: function ( e ) {
                            console.log( e )
                        }
                    })
                }
            },
            error: function ( e ) {
                toastr.error("Compare data failed");
                $("#generate-update").removeAttr("disabled")
            }
        })

    }

    function showUpdateData(id)
    {
        $("#get-update-status").html("Geting update..")

        localStorage.setItem('batch_update_id', id)

        $.ajax({
            url: "{{ url('/hr/karyawan/update/get-update') }}/"+id,
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                if(response.data.length == 0) {
                    $("#get-update-status").html("Update belum dibuat")
                    $("#generate-update").show()
                }

                
            },
            error: function ( e ) {
                console.log( e )
            }
        })

        $("#modal-update-data").modal('show')
    }

    getUpdateBatch()
    function getUpdateBatch()
    {
        $("#update-list").html('Loading..')
        $.ajax({
            url: "{{ url('/hr/karyawan/update/get-batch') }}",
            type: "GET",
            dataType: "JSON",
            success: function ( response ) {
                $("#update-list").html('')
                $.each(response.data, function (key, value) {
                    $("#update-list").append(`
                        <div onClick="showUpdateData('${value.id}')" class="d-flex flex-justify rounded rounded border p-5 cursor-pointer box mb-2 list-group-item-action">
                            <div>
                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${value.keterangan}</span>
                                <span class="text-muted font-weight-bold">${formatTanggalIndonesia2(value.created_at)}</span>
                            </div>
                            <div class="d-flex flex-column ml-10">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted mr-2 font-size-sm font-weight-bold">65%</span>
                                    <span class="text-muted font-size-sm font-weight-bold">Progress</span>
                                </div>
                                <div class="progress progress-xs w-100">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 65%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    `);
                })
            },
            error: function ( e ) {
                console.log( e )
            }
        })
    }

    function createNew()
    {
        $("#create-batch").modal("show");
    }

    $("#form-create-batch").submit(function (e) {
        $("#submit-button").addClass('spinner spinner-left');
        $("#submit-button").attr('disabled', true);
        e.preventDefault();
        $.ajax({
            url: "{{ url('hr/karyawan/update/create-batch') }}",
            type: "POST",
            dataType: "JSON",
            data: $(this).serialize(),
            success: function ( response ) {
                if(response.success == 1) {
                    toastr.success("Create update batch succeed");
                }else{
                    toastr.warning("Error create batch");
                }
                $("#submit-button").removeClass('spinner spinner-left');
                $("#submit-button").removeAttr('disabled');
                location.reload()
            },
            error: function ( e ) {
                $("#submit-button").removeClass('spinner spinner-left');
                $("#submit-button").removeAttr('disabled');
                if(e.status == 422) {
                    alert(e.responseJSON.errors.keterangan);
                } else {
                    alert("Error.. Coba reload browser")
                }
            }
        });
    })

    </script>
@endpush