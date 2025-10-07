@extends('layouts.base')

@section('content')

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">MEN
                                <span class="d-block text-muted pt-2 font-size-sm">Master Material Type</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder"><i class="fa fa-plus-circle"></i> Create</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection