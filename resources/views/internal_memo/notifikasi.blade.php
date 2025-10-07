@extends('internal_memo.master.layout')


    @push('styles')
        <style type="text/css">
            .hide {
                display: none;
            }

            .message {
                transition-duration: 0.7ms;
            }

        </style>
    @endpush

@section('content')

    <div class="container-fluid">
        <div class="main-body">

            <div class="row">
                <div class="col-md-12 mb-3">
                 
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>


    <script type="text/javascript">
        $('.table').DataTable();

         $(function () {
        $('[data-toggle="tooltip"]').tooltip()
        })

    </script>

@endpush
