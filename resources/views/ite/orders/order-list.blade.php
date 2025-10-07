@extends('layouts.base')

@section('content')
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-2">
                    <div class="card-title">
                        <h3 class="card-label">Ite Orders Manager
                            <span class="d-block text-muted pt-2 font-size-sm">Seluruh order ITE PT. PAS</span>
                        </h3>
                        <button onClick="showCreateNewOrder()" title="Create new order" class="btn btn-xs btn-secondary btn-icon"><i class="la la-plus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="orders-table" class="table table-bordered table-hover">
                            <thead>
                                <tr class="table-secondary">
                                    <th width="2%">NO</th>
                                    <th>PROJECT</th>
                                    <th>MATERIAL</th>
                                    <th>QTY</th>
                                    <th>IO</th>
                                    <th>PR</th>
                                    <th>PO</th>
                                    <th>Datang</th>
                                    <th>Masuk Gudang</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="create-new-order-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Create New Order</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="la la-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="create-new-order-form">
                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="project" class="col-form-label">Project</label>
                            </div>
                            <div class="col-md-5">
                                <select required name="project" id="project" class="form-control">
                                    <option value="">Pilih Project</option>
                                    <option value="0">Tanpa Project</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <h4 class="col-md-12">Materials
                                <button type="button" onClick="orderAddMaterial()" title="Add material" class="btn btn-xs btn-secondary btn-icon"><i class="la la-plus"></i></button>
                            </h4>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th width="10">No</th>
                                            <th>Material</th>
                                            <th width="10">QTY</th>
                                            <th width="50">ACT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center pt-3">1</td>
                                            <td>
                                                <select name="material" id="material-9" class="form-control form-control-sm">
                                                    <option value="1">60012241 - CONNECTOR RJ 45 PANDUIT</option>
                                                </select>
                                            </td>
                                            <td class="text-center pt-3">10</td>
                                            <td class="text-center pt-3"><button type="button" class="btn btn-icon btn-xs btn-danger"><i class="la la-trash-alt"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-sticky modal-sticky-bottom-right" id="order-items" role="dialog" data-backdrop="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!--begin::Card-->
                <div class="card card-custom">
                    <!--begin::Header-->
                    <div class="card-header align-items-center px-4 py-3">
                        <div class="text-left flex-grow-1">
                            <i class="la la-cubes"></i>
                        </div>
                        <div class="text-center flex-grow-1">
                            <div class="text-dark-75 font-weight-bold font-size-h5" style="width: 380px">
                                <span id="order-item-mid"></span> - <span id="order-item-description"></span>
                            </div>
                        </div>
                        <div class="text-right flex-grow-1">
                            <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-dismiss="modal">
                                <i class="la la-close"></i>
                            </button>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="timeline timeline-3">
                            <div class="timeline-items">
                                <div class="timeline-item">
                                    <div class="timeline-media ribbon">
                                        <span id="order-item-diambil-check" style="display: none">
                                            <i class="flaticon2-shield text-success"></i>
                                        </span>
                                        <a id="order-item-diambil-button" href="javascript:" class="btn btn-icon btn-outline-secondary btn-circle">
                                            <i class="flaticon2-shield text-danger"></i>
                                        </a>
                                        <div class="ribbon-target bg-primary rounded p-2" style="bottom: -10px; font-size: 10px">RESERVASI</div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="mr-2">
                                                <a href="javascript:" class="text-dark-75 text-hover-primary font-weight-bold"><span id="order-item-diambil-date"></span></a>
                                                <span class="label label-light-danger font-weight-bolder label-inline ml-2"></span>
                                            </div>
                                        </div>
                                        <p class="p-0"><span id="order-item-diambil-keterangan"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-items mt-5">
                                <div class="timeline-item">
                                    <div class="timeline-media ribbon">
                                        <span id="order-item-arrive-check" style="display: none">
                                            <i class="flaticon2-shield text-success"></i>
                                        </span>
                                        <a id="order-item-arrive-button" href="javascript:" class="btn btn-icon btn-outline-secondary btn-circle">
                                            <i class="flaticon2-shield text-danger"></i>
                                        </a>
                                        <div class="ribbon-target bg-primary rounded p-2" style="bottom: -10px; font-size: 10px">WSP</div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="mr-2">
                                                <a href="javascript:" class="text-dark-75 text-hover-primary font-weight-bold"><span id="order-item-arrive-date"></span></a>
                                                <span class="label label-light-danger font-weight-bolder label-inline ml-2"></span>
                                            </div>
                                        </div>
                                        <p class="p-0"><span id="order-item-arrive-keterangan"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-items mt-5">
                                <div class="timeline-item">
                                    <div class="timeline-media ribbon">
                                        <span id="order-item-po-check" style="display: none">
                                            <i class="flaticon2-shield text-success"></i>
                                        </span>
                                        <a id="order-item-po-button" href="javascript:" class="btn btn-icon btn-outline-secondary btn-circle">
                                            <i class="flaticon2-shield text-danger"></i>
                                        </a>
                                        <div class="ribbon-target bg-primary rounded p-2" style="bottom: -10px; font-size: 10px">PO</div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="mr-2">
                                                <a href="javascript:" class="text-dark-75 text-hover-primary font-weight-bold"><span id="order-item-po-date"></span></a>
                                                <span class="label label-light-danger font-weight-bolder label-inline ml-2"><span id="order-item-po-number"></span></span>
                                            </div>
                                        </div>
                                        <p class="p-0"><span id="order-item-po-keterangan"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-items mt-5">
                                <div class="timeline-item">
                                    <div class="timeline-media ribbon">
                                        <span id="order-item-pr-check" style="display: none">
                                            <i class="flaticon2-shield text-success"></i>
                                        </span>
                                        <a id="order-item-pr-button" href="javascript:" class="btn btn-icon btn-outline-secondary btn-circle">
                                            <i class="flaticon2-shield text-danger"></i>
                                        </a>
                                        <div class="ribbon-target bg-primary rounded p-2" style="bottom: -10px; font-size: 10px">PR</div>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="mr-2">
                                                <a href="javascript:" class="text-dark-75 text-hover-primary font-weight-bold"><span id="order-item-pr-date"></span></a>
                                                <span class="label label-light-danger font-weight-bolder label-inline ml-2"><span id="order-item-pr-number"></span></span>
                                            </div>
                                        </div>
                                        <p class="p-0"><span id="order-item-pr-keterangan"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="order_item_id" id="order-item-id">
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer align-items-center">
                        <div class="text-right mt-5">
                            <div class="align-right">
                                <button type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6"><i class="la la-trash"></i> HAPUS</button>
                            </div>
                        </div>
                        <!--begin::Compose-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end::Card-->
            </div>
        </div>
    </div>

    <!-- Modal-->
    <div class="modal" id="order-item-diambil-modal" tabindex="-1" role="dialog" aria-labelledby="order-item-diambil-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="order-item-diambil-modal-label">RESERVASI & MASUK GUDANG</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="order-item-diambil-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar-alt" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                    <input name="order_item_diambil_date" required type="date" class="form-control" placeholder="Tanggal" aria-describedby="basic-addon2">
                                </div>
                            </div>
                            <div class="col-md-12 pt-5">
                                <div class="form-group">
                                    <label for="order-item-diambil-input-keterangan">Keterangan</label>
                                    <textarea name="order_item_diambil_keterangan" id="order-item-diambil-input-keterangan" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="order-item-diambil-form" class="btn btn-primary" id="order-item-diambil-form-button">Simpan</button>
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal-->
    <div class="modal" id="order-item-arrive-modal" tabindex="-1" role="dialog" aria-labelledby="order-item-arrive-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="order-item-arrive-modal-label">DATANG KE WSP</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="order-item-arrive-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar-alt" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                    <input name="order_item_arrive_date" required type="date" class="form-control" placeholder="Tanggal" aria-describedby="basic-addon2">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="order-item-arrive-input-keterangan">Keterangan</label>
                                    <textarea name="order_item_arrive_keterangan" id="order-item-arrive-input-keterangan" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="order-item-arrive-form" class="btn btn-primary" id="order-item-arrive-form-button">Simpan</button>
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal-->
    <div class="modal" id="order-item-po-modal" tabindex="-1" role="dialog" aria-labelledby="order-item-po-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="order-item-po-modal-label">PO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="order-item-po-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar-alt" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                    <input name="order_item_po_date" required type="date" class="form-control" placeholder="Tanggal" aria-describedby="basic-addon2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input name="order_item_po_number" required type="text" class="form-control" placeholder="PO Number">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="order-item-po-input-keterangan">Keterangan</label>
                                    <textarea name="order_item_po_keterangan" id="order-item-po-input-keterangan" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="order-item-po-form" class="btn btn-primary" id="order-item-po-form-button">Simpan</button>
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal-->
    <div class="modal" id="order-item-pr-modal" tabindex="-1" role="dialog" aria-labelledby="order-item-pr-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="order-item-pr-modal-label">PR</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="order-item-pr-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar-alt" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                    <input name="order_item_pr_date" required type="date" class="form-control" placeholder="Tanggal" aria-describedby="basic-addon2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input name="order_item_pr_number" required type="text" class="form-control" placeholder="PR Number">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="order-item-pr-input-keterangan">Keterangan</label>
                                    <textarea name="order_item_pr_keterangan" id="order-item-pr-input-keterangan" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="order-item-pr-form" class="btn btn-primary" id="order-item-pr-form-button">Simpan</button>
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript">

        function orderAddMaterial()
        {
            alert("Add material");
        }

        function showCreateNewOrder()
        {
            $("#create-new-order-modal").modal("show");
        }

        $("#order-item-diambil-form").on("submit", function(e) {
            e.preventDefault();
            $.ajax({
                url : "{{ url('/ite/order/item/submit-diambil') }}",
                data : {
                    id : $("#order-item-id").val(),
                    diambil_date : $("input[name=order_item_diambil_date]").val(),
                    diambil_keterangan : $("textarea[name=order_item_diambil_keterangan]").val()
                },
                dataType : "JSON",
                type : "POST",
                success : function (response) {
                    if(response.success == 1) {
                        $("#order-item-diambil-modal").modal("hide");
                        $("input[name=order_item_diambil_date]").val('');
                        $("textarea[name=order_item_diambil_keterangan]").val('');
                        getAllOrder();
                        hideOrderItem()
                    }
                },
                error : function (error) {
                    console.log(error);
                }
            })
        });

        $("#order-item-arrive-form").on("submit", function(e) {
            e.preventDefault();
            $.ajax({
                url : "{{ url('/ite/order/item/submit-arrive') }}",
                data : {
                    id : $("#order-item-id").val(),
                    arrive_date : $("input[name=order_item_arrive_date]").val(),
                    arrive_keterangan : $("textarea[name=order_item_arrive_keterangan]").val()
                },
                dataType : "JSON",
                type : "POST",
                success : function (response) {
                    if(response.success == 1) {
                        $("#order-item-arrive-modal").modal("hide");
                        $("input[name=order_item_arrive_date]").val('');
                        $("textarea[name=order_item_arrive_keterangan]").val('');
                        getAllOrder();
                        hideOrderItem()
                    }
                },
                error : function (error) {
                    console.log(error);
                }
            })
        });

        $("#order-item-po-form").on("submit", function(e) {
            e.preventDefault();
            $.ajax({
                url : "{{ url('/ite/order/item/submit-po') }}",
                data : {
                    id : $("#order-item-id").val(),
                    po_date : $("input[name=order_item_po_date]").val(),
                    po_number : $("input[name=order_item_po_number]").val(),
                    po_keterangan : $("textarea[name=order_item_po_keterangan]").val()
                },
                dataType : "JSON",
                type : "POST",
                success : function (response) {
                    if(response.success == 1) {
                        $("#order-item-po-modal").modal("hide");
                        $("input[name=order_item_po_date]").val('');
                        $("input[name=order_item_po_number]").val('');
                        $("textarea[name=order_item_po_keterangan]").val('');
                        getAllOrder();
                        hideOrderItem()
                    }
                },
                error : function (error) {
                    console.log(error);
                }
            })
        });

        $("#order-item-pr-form").on("submit", function(e) {
            e.preventDefault();
            $.ajax({
                url : "{{ url('/ite/order/item/submit-pr') }}",
                data : {
                    id : $("#order-item-id").val(),
                    pr_date : $("input[name=order_item_pr_date]").val(),
                    pr_number : $("input[name=order_item_pr_number]").val(),
                    pr_keterangan : $("textarea[name=order_item_pr_keterangan]").val()
                },
                dataType : "JSON",
                type : "POST",
                success : function (response) {
                    if(response.success == 1) {
                        $("#order-item-pr-modal").modal("hide");
                        $("input[name=order_item_pr_date]").val('');
                        $("input[name=order_item_pr_number]").val('');
                        $("textarea[name=order_item_pr_keterangan]").val('');
                        getAllOrder();
                        hideOrderItem()
                    }
                },
                error : function (error) {
                    console.log(error);
                }
            })
        });

        $("#order-item-diambil-button").on("click", function() {
            $("#order-item-diambil-modal").modal("show");
        });

        $("#order-item-arrive-button").on("click", function() {
            $("#order-item-arrive-modal").modal("show");
        });

        $("#order-item-po-button").on("click", function() {
            $("#order-item-po-modal").modal("show");
        });

        $("#order-item-pr-button").on("click", function() {
            $("#order-item-pr-modal").modal("show");
        });

        function hideOrderItem()
        {
            $("#order-items").modal("hide");
        }

        function showOrderItem(id) {
            $("#order-items").modal("show");
            
            KTApp.block("#order-items", {
                overlayColor: "#000000",
                state : "danger",
                opacity : 0.2,
                message : "Loading..."
            });

            $.ajax({
                url : "{{ url('ite/order/item') }}/"+id,
                type: "GET",
                dataType: "JSON",
                success: function ( response ) {
                    if(response.success == 1) {
                        setTimeout(() => {
                            KTApp.unblock("#order-items");                            
                        }, 200);

                        $("#order-item-id").val(response.data.id);
                        $("#order-item-mid").text(response.data.material.mid);
                        $("#order-item-description").text(response.data.material.name);

                        if(response.data.pr == '1') {
                            $("#order-item-pr-check").show();
                            $("#order-item-pr-button").hide();
                        }else{
                            $("#order-item-pr-check").hide();
                            $("#order-item-pr-button").show();
                        }
                        $("#order-item-pr-date").text(formatTanggalIndonesia2(response.data.pr_date));
                        $("#order-item-pr-number").text(response.data.pr_number);
                        $("#order-item-pr-keterangan").text(response.data.pr_keterangan);

                        if(response.data.po == '1') {
                            $("#order-item-po-check").show();
                            $("#order-item-po-button").hide();
                        }else{
                            $("#order-item-po-check").hide();
                            $("#order-item-po-button").show();
                        }
                        $("#order-item-po-date").text(formatTanggalIndonesia2(response.data.po_date));
                        $("#order-item-po-number").text(response.data.po_number);
                        $("#order-item-po-keterangan").text(response.data.po_keterangan);

                        if(response.data.arrive == '1') {
                            $("#order-item-arrive-check").show();
                            $("#order-item-arrive-button").hide();
                        }else{
                            $("#order-item-arrive-check").hide();
                            $("#order-item-arrive-button").show();
                        }
                        $("#order-item-arrive-date").text(formatTanggalIndonesia2(response.data.arrive_date));
                        $("#order-item-arrive-keterangan").text(response.data.arrive_keterangan);
                        
                        if(response.data.diambil == '1') {
                            $("#order-item-diambil-check").show();
                            $("#order-item-diambil-button").hide();
                        }else{
                            $("#order-item-diambil-check").hide();
                            $("#order-item-diambil-button").show();
                        }
                        $("#order-item-diambil-date").text(formatTanggalIndonesia2(response.data.diambil_date));
                        $("#order-item-diambil-keterangan").text(response.data.diambil_keterangan);
                    }
                },
                error: function ( error ) {
                    console.log( error );
                    setTimeout(() => {
                        KTApp.unblock("#order-items");                            
                    }, 300);
                    $("#order-items").modal("hide");
                }
            })
        }

        getAllOrder();

        function getAllOrder()
        {
            var table = $("#orders-table tbody");
            // table.html("<tr><td colspan='9' class='text-center p-5'><span class='spinner spinner-danger'></span></td></tr>")
            KTApp.block("#orders-table", {
                overlayColor: "#000000",
                state : "danger",
                opacity : 0.2,
                message : "Loading..."
            });
            $.ajax({
                url: "{{ url('ite/orders/all') }}",
                dataType: "JSON",
                type: "GET",
                success: function ( response ) {
                    table.html("");
                    $.each(response.data, function(key, value) {
                        var number = 1;
                        if(value.project != null) {
                            var project = value.project.name;
                            table.append("<tr><td colspan='9' class='pt-1 pb-1 bg-secondary'><strong>"+value.project.name+"</strong></td></tr>");
                        }else{
                            var project = '';
                            table.append("<tr><td colspan='9' class='pt-1 pb-1 bg-dark'><i>-</i></td></tr>");
                        }
                        $.each(value.items, function(_key, item) {
                            // console.log(item)
                            if(item.io_id == '0') {
                                var ket_io = '-';
                                var io_status = 'bg-warning';
                            }else{
                                var ket_io = item.io.status;
                                var io_status = 'bg-info';
                            }

                            if(item.pr_number != null) {
                                var pr = item.pr_number;
                                var pr_status = 'bg-success';
                            }else{
                                var pr = '';
                                var pr_status = 'bg-danger';
                            }

                            if(item.po_number != null) {
                                var po = item.po_number;
                                var po_status = 'bg-success';
                            }else{
                                var po = '';
                                var po_status = 'bg-danger';
                            }

                            if(item.arrive_date != null) {
                                var datang = item.arrive_date;
                                var datang_status = 'bg-success';
                            }else{
                                var datang = '';
                                var datang_status = 'bg-danger';
                            }

                            if(item.diambil != null) {
                                var masuk_gudang = item.diambil;
                                var masuk_gudang_status = 'bg-success';
                            }else{
                                var masuk_gudang = '';
                                var masuk_gudang_status = 'bg-danger';
                            }

                            var row = '<tr style="cursor: context-menu" onClick="showOrderItem(\''+item.id+'\')">'+
                                '<td class="text-center">'+number+'</td>'+
                                '<td>'+project+'</td>'+
                                '<td>'+item.material.mid+' - '+item.material.name+'</td>'+
                                '<td>'+item.quantity+'</td>'+
                                '<td class="text-white '+io_status+' hover-opacity-80">'+ket_io+'</td>'+
                                '<td class="text-white '+pr_status+' hover-opacity-80">'+pr+'</td>'+
                                '<td class="text-white '+po_status+' hover-opacity-80">'+po+'</td>'+
                                '<td class="text-white '+datang_status+' hover-opacity-80">'+datang+'</td>'+
                                '<td class="text-white '+masuk_gudang_status+' hover-opacity-80">'+masuk_gudang+'</td>'+
                            '</tr>';
                            table.append(row);
                            number++;
                        });
                    });
                    KTApp.unblock("#orders-table");
                },
                error: function ( error ) {
                    console.log( error );
                }
            });
        }
    </script>
@endpush