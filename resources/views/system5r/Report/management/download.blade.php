<!DOCTYPE html>
<html lang="en">
	<head>
        <base href="{{ url('/') }}">
		<title></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		
		<meta name="description" content="PT. Prakarsa Alam Segar Applications Base" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="shortcut icon" href="{{ url('/') }}/assets/media/logos/logo-pas.jpg" />

		<!-- Layout config Js -->
		<script src="{{ asset('assets/velzon/js/layout.js') }}"></script>
		<link href="{{ asset('assets/velzon/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/velzon/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/velzon/css/app.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/velzon/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

		<!--datatable css-->
		<link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/responsive.bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/buttons.dataTables.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/select.bootstrap5.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/fixedColumns.dataTables.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.css') }}">
		<style>
		.pas-background-color {
			background-color: #a80000 !important;
		}

		.pas-color {
			color: #a80000 !important;
		}
        table p {
            margin-bottom: 0 !important
        }
        .modal-backdrop
        {
            opacity:0.99 !important;
        }
		</style>
	</head>
	<body class="pt-5 pb-5">
		<div class="text-center">
			<h1 class="text-center">PENILAIAN 5R</h1>
			<h3 class="text-center">PT. PRAKARSA ALAM SEGAR</h3>
			<h6>{{ $info['tahun'] }} - {{ $info['periode'] }} - {{ $info['department'] }} - {{ $info['group'] }}</h6>
			..............................................................................
		</div>
		@php
			$colors = ['#264653', '#2a9d8f', 'e9c46a', '#f4a261', '#e76f51'];
			$textColors = ['#fff', '#fff', '#000', '#000', '#000'];
		@endphp
        @foreach ($pertanyaan as $group)
		{{-- {{ dd($pertanyaan) }} --}}
		@php
			$jawaban = $jawabanGroup;

			if ($jawaban != null) {
				$jawaban = $jawaban->jawaban;
			}
		@endphp
		<div class="container-fluid">
			<div>
				<form class="form-pertanyaan" id="form-{{ $group->id_group }}">
					<input type="hidden" name="id_group" value="{{ $group->id_group }}">
					<div class="table-responsive">
						<table class="table table-striped">
							{{-- <thead> --}}
								{{-- </thead> --}}
								<tbody>
								<tr class="pas-background-color">
									<th class="text-white px-1" style="border-width: 1px solid #fff; width: 5% !important">GROUP</th>
									<th class="text-white" style="width: 50%">PERTANYAAN</th>
									{{-- <th class="text-white">KETERANGAN</th> --}}
									<th class="text-white">NILAI</th>
								</tr>
								@foreach (['RINGKAS', 'RAPI', 'RESIK', 'RAWAT', 'RAJIN'] as $jenis)
									@php
										$__pertanyaan = $group->pertanyaan->where('jenis', $jenis);
									@endphp
									@foreach ($__pertanyaan as $_pertanyaan)
									<tr>
										@if($loop->first)
										<td class="p-0" style="vertical-align: middle; font-size: 10px; font-weight: bold; text-align: center; background-color: {{ $colors[$loop->parent->iteration-1] }}; color: {{ $textColors[$loop->parent->iteration-1] }}" rowspan="{{ count($__pertanyaan) }}">
											{{ $_pertanyaan->jenis }}<br />
										</td>
										@endif
										<td>
											<div style="width: 300px">
												<h6>ITEM PERIKSA</h6>
												{!! str_replace('||--||', '&', $_pertanyaan->item_periksa) !!}
												<h6 class="mt-3">KETERANGAN</h6>
												{!! str_replace('||--||', '&', $_pertanyaan->keterangan) !!}
											</div>
										</td>
										<td>
											<div>
												<h6>NILAI <span title="Wajib diisi" class="text-danger">*</span></h6>
												@if($jawaban != null)
												@php
													$__jawaban = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first();
													$nilai = $__jawaban != null ? $__jawaban->nilai : '';
												@endphp
												<select disabled class="form-control" style="width: 100px" name="nilai[{{ $_pertanyaan->id_pertanyaan }}]">
													<option value="">PILIH</option>
													<option @if($nilai == '1') selected @endif value="1">1</option>
													<option @if($nilai == '2') selected @endif value="2">2</option>
													<option @if($nilai == '3') selected @endif value="3">3</option>
													<option @if($nilai == '4') selected @endif value="4">4</option>
												</select>
												@else
												<select required onchange="changeNilai('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" class="form-control" style="width: 100px" name="nilai[{{ $_pertanyaan->id_pertanyaan }}]" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}">
													<option value="">PILIH</option>
													<option value="1">1</option>
													<option value="2">2</option>
													<option value="3">3</option>
													<option value="4">4</option>
												</select>
												@endif
											</div>
											<div class="mt-3">
												<h6>FOTO</h6>
												@if($jawaban != null)
													@php
														$__jawaban = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first();
														$foto = $__jawaban != null ? $__jawaban->foto : null;
													@endphp
													@if($foto != null)
													<div class="image-container">
														<div class="row">
															@foreach (explode(',', $foto) as $_foto)
															<div class="col-6">
																<img src="{{ asset('images/5r/'. $_foto) }}" style="width: 100%; height: 100%; object-fit: contain; margin-bottom: 5px" alt="">
															</div>
															@endforeach
														</div>                                                     
													</div>
													@else
													<i>No Image</i>
													@endif
												@else
												<div class="image-container mb-1" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-image-container">
												</div>
												<input onchange="addImage('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" type="file" accept="image/jpeg" class="form-control" name="{{ $_pertanyaan->id_group }}_{{ $_pertanyaan->id_pertanyaan }}_foto" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-input-file">
												<div class="textarea_image_container" style="display: none" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}_image_container">
													{{-- <textarea name="image[{{ $_pertanyaan->id_pertanyaan }}]" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}_image"></textarea> --}}
												</div>
												@endif
											</div>
											<div class="mt-3 rounded bg-light p-1">
												<h6>KETERANGAN</h6>
												@if($jawaban != null)
												@php
													$__jawaban = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first();
													$keterangan = $__jawaban != null ? $__jawaban->keterangan : '-';
												@endphp
												<p>{{ $keterangan }}</p>
												@endif
											</div>
										</td>
									</tr>
								@endforeach
								@endforeach
							</tbody>
						</table>
					</div>
					@if($jawabanGroup == null)
					<div class="mt-3">
						<button type="submit" form="form-{{ $group->id_group }}" class="btn btn-full btn-success waves-effect">
							<i class="mdi mdi-content-save"></i>
							SIMPAN
						</button>
					</div>
					@endif
				</form>
			</div>
		</div>
		@endforeach
	</body>
	<!--end::Body-->

	<script>
		// Print and close
		window.print();
		setTimeout(() => {
			window.close();
		}, 7000);
	</script>
</html>