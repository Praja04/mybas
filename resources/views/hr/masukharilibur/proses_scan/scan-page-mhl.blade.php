@extends('layouts.base-display')

@push('styles')
    <style>
        .not-visible {
            opacity: 0 !important;
        }
    </style>
@endpush

@section('content')

<div class="container">
    <div class="card card-custom gutter-b w-100">
        <div class="card-body">
            <form action="" id="form-scan" method="post">
                @csrf
            <div class="row">
                <div class="col-md-3">
                    <img id="image" alt="Pic" class="rounded w-100" src="{{ asset('assets/media/images/no-image.jpg') }}">
                </div>
                <div class="col-md-6">
                    <div style="border-top: 10px solid #ffffff; width: 100%;" class="indikator-normal indikator"></div>
                    <div style="border-top: 10px solid #1BC5BD; width: 120%; display:none" class="indikator-berhasil indikator"></div>
                    <div style="border-top: 10px solid #F64E60; width: 120%; display:none" class="indikator-gagal indikator"></div>
                    <div style="border-top: 10px solid #f6ff77; width: 120%; display:none" class="indikator-warning indikator"></div>
                    <div class="form-group">
                        <label for="nik" style="font-weight: bold; font-size: 29px">NIK</label>
                        <input id="nik" type="text" class="form-control text-white" style="font-size: 29px; background-color: #525461; font-weight: bold" placeholder="NIK">
                    </div>
                    <div class="form-group">
                        <label for="nama" style="font-weight: bold; font-size: 29px">NAMA</label>
                        <input id="name" type="text" class="form-control text-white" style="font-size: 29px; background-color: #525461; font-weight: bold" placeholder="Nama">
                    </div>
                    <div class="form-group">
                        <label for="department" style="font-weight: bold; font-size: 29px">DEPARTMENT</label>
                        <input id="department" type="text" class="form-control text-white" style="font-size: 29px; background-color: #525461; font-weight: bold" placeholder="Department">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-success indikator-berhasil indikator" style="font-size: 20px; display: none">
                        <strong>Scan Berhasil !</strong><br /><span class="message">Silahkan Masuk</span>
                    </div>
                    <div class="alert alert-danger indikator-gagal indikator" style="font-size: 20px; display: none">
                        <strong>Scan Ditolak !</strong><br /><span class="message">Anda Tidak Boleh Masuk</span>
                    </div>
                    <div class="alert alert-danger indikator-bug indikator" style="font-size: 20px; display: none">
                        <strong>Scan Gagal !</strong><br /><span class="message"></span>
                    </div>
                    <div class="alert alert-warning indikator-warning indikator" style="font-size: 20px; display: none">
                        <strong>PENDING !</strong><br /><span class="message"></span>
                    </div>
                </div>
            </div>
            <div class="separator separator-solid my-7"></div>
            <div class="row">
                <div class="col-3">
                    <input id="scanner" autofocus type="text" class="form-control text-white" placeholder="Scan here.." style="background-color: #525461">
                </div>
                <div class="col-5">
                    <span id="loading" style="font-size: 26px; background-color: #eee; font-weight: bold" class="rounded px-2">
                        <i class="fas fa-spinner fa-spin text-dark-75" style="font-size: 26px"></i>
                        LOADING.. MOHON TUNGGU
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
    <script>
        setTimeout( function () {
            $('#loading').hide()
        }, 2000);
        var number = 0;
        setInterval(() => {
            if(number%2 == 0) {
                $('.indikator').addClass('not-visible')
            }else{
                $('.indikator').removeClass('not-visible')
            }
            number = number+1
        }, 1000);

        $('#scanner').keypress(function(e) {
			if(e.which == 13)
			{
				var scanner_value = $('#scanner').val();
				$('#scanner').val('');

				if(scanner_value != $('#temp_rfid').val())
				{
					var data = {
						'rfid' : scanner_value
					}

					doScan(data);
					$('#temp_rfid').val(scanner_value);
				}
			}
		});

        function playSound (sound) {
            var src = "{{ asset('assets/media/sounds') }}/"+sound;
            var sound = new Audio();
            sound.src = src;
            sound.play();
            sound.onended = function () {
                sound = null;
            }
        }

        function doScan(data)
        {
            $('#loading').show()
            // console.log( data );
            $.ajax({
                url: "{{ url('/masukharilibur-scan/do-scan') }}",
                data: data,
                type: "POST",
                dataType: "JSON",
                success : function ( response ) {
                    if(response.success == 1)
                    {
                        success(response.data, response.message)
                    }
                    else
                    {
                        failed(response.data, response.message, response.status)
                    }
                    $('#loading').hide()
                },
                error : function ( error ) {
                    // warning()
                    $('#temp_rfid').val('');
                    $('#loading').hide()
                    $('.indikator-bug').show()
                    $('.message').text('Ada kesalahan, silahkan coba lagi')
                }
            })
        }

        function success(data, message)
        {
            playSound('ecafesedaap-scan-success.mp3')
            $('.indikator-berhasil').show()
            $('.indikator-normal').hide()
            $('.indikator-gagal').hide()
            $('.indikator-warning').hide()
            $('.message').html(message)

            $('#image').attr('src', 'data:image/jpg;base64,'+data.image);
            $('#name').val(data.name);
            $('#nik').val(data.nik);
            $('#department').val(data.department);
        }

        function failed(data, message, status)
        {
            playSound('ecafesedaap-scan-failed.mp3')
            $('.indikator-berhasil').hide()
            $('.indikator-normal').hide()
            $('.indikator-gagal').hide()
            $('.indikator-warning').hide()
            if(status==0){
                $('.indikator-gagal').show()
            }
            else {
                $('.indikator-warning').show()

            }
            $('.message').html(message)

            if(data == null) {
                $('#image').attr('src', "{{ asset('assets/media/images/no-image.jpg') }}");
                $('#name').val('');
                $('#nik').val('');
                $('#department').val('');
                return;
            }
            

            
            $('#image').attr('src', 'data:image/jpg;base64,'+data.image);
            $('#name').val(data.name);
            $('#nik').val(data.nik);
            $('#department').val(data.department);
        }

    </script>
@endpush

{{-- @push('scripts')
    <script>
        $('#loading').hide()
        $('#scanner').keypress(function(e) {
			if(e.which == 13)
			{
				var scanner_value = $('#scanner').val();

                $('#loading').show()
            // console.log( data );
            $.ajax({
                url: "{{ url('/masukharilibur-scan') }}/" + scanner_value,
                type: "get",
                dataType: "JSON",
                success : function ( response ) {
                    if(response.status == 0)
                    {
                        alert('Kartu Tidak Terdafatar')
                    }
                    else if(response    .status == 2)
                    {
                        alert('Tidak Masuk List')
                    }
                    else
                    {
                        alert('OK GOOD')
                    }
                    $('#loading').hide()
                },
                error : function ( error ) {
                    console.log(error)
                    $('#temp_rfid').val('');
                    $('#loading').hide()
                    $('.indikator-bug').show()
                    $('.message').text('Ada kesalahan, silahkan coba lagi')
                }
            })
			}
		});

    </script>
@endpush --}}