@extends('layouts.base-display')

@section('title', 'Form Edit Master Checksheet')

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

    <div class="container">
        <div class="main-body">


            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <a href="javascript:history.back()" class="btn btn-info"><i class="fas fa-arrow-left"></i>
                                    Kembali</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('/logging_machine/update_logging_master') }}" method="POST">
                                @csrf

                                <input type="hidden" name="nik" value="{{$detail->nik}}">
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">No. Mesin</label>
                                            <select class="form-control" name="no_mesin_before">
                                                <option value="{{ $detail->no_mesin }}" selected>{{ $detail->no_mesin }}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <p class="text-center"> <b>UBAH MENJADI</b></p>
                                <div class="row">
                                    <div class="col-sm-12">
                                         <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Line</label>
                                    <select class="form-control" name="line" required>
                                        <option disabled selected>Silahkan Pilih</option>
                                        @foreach ($no_mesin as $item)
                                            <option value="{{ $item->group }}">{{ $item->line }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">No. Mesin</label>
                                    <select class="form-control" name="no_mesin" required>
                                        <option disabled selected>Silahkan Pilih</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Varian</label>
                                    <select class="form-control" name="varian" required>
                                        <option value="Garnish">Garnish</option>
                                        <option value="Sayuran">Sayuran</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                         <div class="row">
                            <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Group</label>
                                        <select class="form-control" name="group" required>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                @if (date('l') != $hari_sabtu)                                        
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Shift</label>
                                    <select class="form-control" name="shift" required style="width: 105%">
                                        @for ($i = 1; $i < 4; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @else
                                 <div class="form-group">
                                    <label for="exampleFormControlSelect1">Shift</label><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i>
                                    <select class="form-control bg-primary text-white" name="shift" required style="width: 105%">
                                            <option disabled selected>Silahkan Dipilih</option>
                                            <option value="1">1 </option>
                                            <option value="2">2 </option>
                                            <option value="3">3 </option>
                                    </select>
                                </div>
                                 @endif
                                </div>
                                <hr>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                  <div class="form-group">
                                    <label for="exampleFormControlSelect1">Rasa</label>
                                    <select class="form-control" name="varian_rasa" required>
                                        <option disabled selected>Silahkan Pilih</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5"></div>
                            <div class="col-sm-5"></div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary mb-2 btn-block"
                                    style="border-radius: 10px"><i class="fas fa-save"></i> Simpan</button>
                            </div>
                        </div>
                    </form>
                 </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection

@push('scripts')

    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('select[name="rasa"]').on('change', function() {
                var id_varian = jQuery(this).val();
                if (id_varian) {
                    jQuery.ajax({
                        url: '/logging_machine/get_mesin/' + id_varian,
                        type: "GET",
                        data: {
                            id: id_varian
                        },
                        dataType: "json",
                        success: function(data) {
                            // console.log(data.no_mesin);
                            jQuery('select[name="no_mesin"]').empty();
                            jQuery.each(data, function(id, mesin) {
                                $('select[name="no_mesin"]').append('<option value="' +
                                    mesin.no_mesin + '">' + mesin.no_mesin +
                                    '</option>');
                            });
                        }
                    });
                } else {
                    $('select[name="no_mesin"]').empty();
                }
            });
        });

        $('.Rasa').select2({
            placeholder: 'Select an option'
        });

        $('.Mesin').select2({
            placeholder: 'Select an option'
        });


        jQuery('select[name="line"]').on('change', function() {
            var group = this.value;
            jQuery.ajax({
                url: '/logging_machine/get_mesin/' + group,
                type: "GET",
                data: {
                    group: group
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        jQuery('select[name="no_mesin"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="no_mesin"]').append('<option value="' + value
                                .no_mesin + '">' + value.no_mesin + '</option>');
                        });
                    }
                }
            });
        });

        jQuery('select[name="line"]').on('change', function() {
            var group = this.value;
            jQuery.ajax({
                url: '/logging_machine/get_rasa/' + group,
                type: "GET",
                data: {
                    group: group
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        jQuery('select[name="varian_rasa"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="varian_rasa"]').append('<option value="' + value
                                .varian_rasa + '">' + value.varian_rasa + '</option>');
                        });
                    }
                }
            });
        });

    </script>
    


@endpush
