<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('add', [
				[ 'import-btn','Import','fa-download' ],
				[ 'submit-btn','Submit','fa-check' ]
			]); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('', true, base_url('transaction/profiling/data'), (table_exists('trxprof_' . date('Y') . '_' . active_cycle()) ? 'trxprof_' . date('Y') . '_' . active_cycle() : 'draft_profiling'
	));
	thead();
		tr();
			th('No.', 'text-center', 'width="30" data-content="id"');
			th(lang('dokter'), '', 'data-content="nama_dokter" data-custom="true"');
			th('Specialist', '', 'data-content="nama_spesialist" data-custom="true"');
			th('Outlet / ' . lang('tempat_praktek'), '', 'data-content="nama_outlet" data-custom="true"');
			th(lang('produk') . ' Group', '', 'data-content="nama_produk_group" data-custom="true"');
			th(lang('jumlah_pasien'), 'text-center', 'data-content="jumlah_pasien_perbulan"');
			th('Potensi', 'text-center', 'data-content="jumlah_potensi" data-custom="true"');
			th('&nbsp;', '', 'width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php

$year = [];
for ($i = date('Y'); $i >= 2018; $i--) {
	$year[$i] = $i;
}

// Modal Import Profiling
modal_open('import-form', 'Import Profiling', 'modal-sm');
	modal_body();
		col_init(4, 8);
			select2('MR', 'imr', 'required', get_data('tbl_user', 'id_group', MR_ROLE_ID)->result_array(), 'username', 'nama',user('username'));
			select('Cycle', 'icycle', 'required', [
				'1' => 1,
				'2' => 2,
				'3' => 3,
			]);
			select('Year', 'itahun', 'required', $year);
	echo '<div style="text-align:center"><button class="btn btn-sky btn-sm" id="import-profiling"><i class="fa fa-download mr-2"></i>Import</button></div>';
modal_close();

// Modal Profiling Baru
modal_open('modal-form');
	modal_body();
		form_open(base_url('transaction/profiling/save'), 'post', 'form');
			col_init(4, 8);
				input('hidden', 'id', 'id');
				label('A. Product');
				select2('Product Group', 'produk_grup', 'required', $this->session->userdata('produk_group'), 'kode', 'nama');
				label('B. Doctor');
				select('Dokter', 'dokter', 'required', []);
				input('text','Spesialist','spesialist','','','disabled');
				input('text','Sub Spesialist','sub_spesialist','','','disabled');
				select2('Branch', 'branch', 'required', get_data('branch')->result_array(), 'id', 'nama');
				select2('Practice/Outlet', 'outlet', 'required');
				radio('Channel Outlet', 'channel_outlet', [
					'Goverment Hospital' => 'Goverment Hospital',
					'Private Hospital' => 'Private Hospital',
					'Apotek' => 'Apotek'
				], 'data-validation="required"');
				radio('Patient Type', 'tipe_pasien', [
					'Regular' => 'Regular',
					'Non Regular' => 'Non Regular',
				], 'data-validation="required"');
				input('number', 'Number Of Patiens Per Month', 'jumlah_pasien_perbulan', 'required');
				label('C. Indications');
				echo '<div id="indikasi_box"></div>';
				echo '<div id="additional_box"></div>';
				echo '<p>&nbsp;</p>';
			form_button(lang('simpan'), lang('batal'));
		form_close();
	modal_footer();
modal_close();

?>

<div class="filter-panel">
	<div class="filter-header bg-primary text-white">
		<i class="fa-search mr-2"></i> Search
	</div>
	<div class="filter-body">
		<?php
			col_init(12, 12);
			select2('Product Group', 'fpgroup', '', $this->session->userdata('produk_group'), 'kode', 'nama');
		?>
	</div>
</div>
<script>

	$(document).on('change','#dokter',function(){
		let id = $(this).val()
		$.ajax({
			url: base_url + 'transaction/profiling/get_detail_dokter?id='+id,
			success: function(r){
				$('#spesialist').val(r.nama_spesialist)
				$('#sub_spesialist').val(r.nama_sub_spesialist)
			}
		})
	})

	$('.import-btn').on('click', function(){
		$('#import-form').modal()
	})

	//filter
	$('#fpgroup').on('change', function() {
		var pgroup = $('#fpgroup').val();
		$('[data-serverside]').attr('data-serverside', base_url + 'transaction/profiling/data?grup=' + pgroup);
		refreshData();
	});

	$('#import-profiling').on('click', function(e) {
		cConfirm.open('Apakah anda yakin ingin mengambil data profiling dari\n Cycle ' + $('#icycle').val() + ' Tahun ' + $('#itahun').val(), 'importProfiling')
	});


	$('#produk_grup').on('change', function() {
		var tpgroup = $(this).val();

		init_indikasi(tpgroup);
		init_additional(tpgroup);
	});

	$('#branch').on('change', function() {
		var branch = $(this).val();
		$('select[name="outlet"]').select2({
			placeholder: 'Pilih outlet',
			minimumInputLength: 3,
			allowClear: true,
			dropdownParent: $('#modal-form'),
			ajax: {
				url: base_url + 'transaction/profiling/get_outlet/' + branch,
				dataType: 'json',
				delay: 250,
				processResults: function(data) {
					return {
						results: data
					};
				},
				cache: true
			}
		});
	});
	
	$('#modal-form').on('shown.bs.modal', function(e){
		if(parseInt($('#id').val()) > 0){
			let dokter = response_edit.dokter != undefined ? response_edit.dokter : ''
			let outlet = response_edit.outlet != undefined ? response_edit.outlet : ''
			if(dokter != ''){
				$('#dokter').html('<option value="'+dokter+'" selected>'+ response_edit.nama_dokter + '</option>')
				$('#dokter').trigger('change')
			}

			if(outlet != ''){
				$('#outlet').html('<option value="'+outlet+'" selected>'+ response_edit.nama_outlet + '</option>')
			}
		} else {
			$('#dokter').html('')
			$('#outlet').html('')
			$('#spesialist').val('')
		}
	})

	$('.submit-btn').on('click', function(){
		var pgroup = $('#fpgroup').val();
		var fmr = $('#fmr').val();
		if (pgroup != '' && fmr != '') {
			cConfirm.open('Submit Sekarang ? Pastikan semua data profiling sudah benar', 'submitProfiling');
		}
	})
		
	function submitProfiling() {
		$.ajax({
			url: '<?= base_url('transaction/profiling/submit/') ?>',
			method: 'post',
			data: {
				grup: $('#fpgroup').val()
			},
			success: function(resp) {
				if (resp.status == 'prof_kurang') {
					cAlert.open('Oops! Pastikan untuk produk group ' + resp.nama + ' minimal ada ' + resp.jumlah + ' Dokter', 'info');
				} else if (resp.status == 'pernah_submit') {
					cAlert.open('Oops! Anda telah melakukan submit profiling untuk cycle sekarang. Tidak dapat melakukan submit lagi', 'info');
				} else if (resp.status == true) {
					if (resp.err_data.length > 0) {
						cAlert.open('Oops! beberapa dokter tidak dapat disubmit yaitu: \n\n ' + resp.err_data.join(',\n ') + ' \n\n Dikarenakan dokter ini sudah terinput pada cycle <?= active_cycle() ?> dan tahun <?= date('Y') ?>. Silahkan hubungi AM terkait jika ingin ada perubahan profiling dokter tersebut. \n\n Untuk melihat datanya anda dapat cek dihistory profiling', 'warning');
					} else {
						cAlert.open('Profiling Berhasil Disubmit', 'success');
					}
				} else {
					cAlert.open('Oops! Terjadi Kesalahan Silahkan Coba Lagi', 'error');
				}
				refreshData();
			}
		})

	}

	function importProfiling() {

		var cycle = $('#icycle').val();
		var tahun = $('#itahun').val();
		var mr = $('#imr').val();

		$.ajax({
			url: '<?= base_url('transaction/profiling/import') ?>',
			method: 'post',
			data: {
				cycle: cycle,
				tahun: tahun,
				mr: mr
			},
			success: function(resp) {
				if (resp.status == true) {
					cAlert.open('Import Profiling Cycle ' + cycle + ' Tahun ' + tahun + ' Selesai.', 'success');
					$('#import-form').modal('hide');
				} else {
					cAlert.open('Import Profiling Cycle ' + cycle + ' Tahun ' + tahun + ' Gagal.', 'error');
				}
				refreshData();
			}
		})

	}

	function init_indikasi(grup) {
		$('#indikasi_box').html('<div class="text-center"> Loading Data <i class="fa fa-spinner fa-spin fa-3x"></i></div>');
		$.ajax({
			url: '<?= base_url('transaction/profiling/get_indikasi?pgrup=') ?>' + grup,
			success: function(resp) {
				var html_indikasi = '';
				var index_indikasi = 1;
				$.each(resp, function(i, val) {
					if (index_indikasi == 11) {
						return false;
					}
					let v_value = 0

					if(response_edit['indikasi_'+(i+1)] != ''){
						if(response_edit['indikasi_' + (i+1)] == val['id']){
							v_value = response_edit['val_indikasi_'+(i+1)]
						}
					} else {
						// send admin to add new indications wlwkwkwk
					}
					
					html_indikasi += '<div class="form-group row">' +
						'<label class="col-form-label col-md-4" for="specialist">' + val.nama + '</label>' +
						'<div class="col-md-8">' +
							'<input type="hidden" name="indikasi_' + index_indikasi + '" id="indikasi_' + index_indikasi + '" autocomplete="off" value="' + val.id + '">' +
							'<input type="number" name="val_indikasi_' + index_indikasi + '" id="val_indikasi_' + index_indikasi + '" autocomplete="off" class="form-control" data-validation="" value="' + v_value + '">' +
						'</div>' +
					'</div>';
					index_indikasi++;
				});
				html_indikasi = html_indikasi ? html_indikasi : '<div class="text-center"> No Data !</div>';
				$('#indikasi_box').html(html_indikasi);
			}
		});
	}

	function init_additional(grup, type = 'add', fee_patient = '', ap_original = 0) {
		$('#additional_box').html('<div class="text-center"> Loading Data <i class="fa fa-spinner fa-spin fa-3x"></i></div>');
		$('#eadditional_box').html('<div class="text-center"> Loading Data <i class="fa fa-spinner fa-spin fa-3x"></i></div>');
		html_additional = '';
		if (grup == 'EH') {
			html_additional = 	'<div class="row">' +
									'<h4 class="col-form-label col-12 ">D. Additional</h4>' +
								'</div>' +
								'<div class="form-group row">' +
									'<label class="col-form-label col-md-4 required" for="fee_patient">Fee Patient</label>' +
									'<div class="col-md-8">' +
										'<input type="number" name="fee_patient" id="fee_patient" autocomplete="off" class="form-control" data-validation="" value="' + fee_patient + '">' +
									'</div>' +
								'</div>' +
								'<div class="form-group row">' +
									'<label class="col-form-label col-md-4 required" for="ap_original">R/ Original</label>' +
									'<div class="col-md-8">' +
										'<label class="switch"><input type="checkbox" name="ap_original" id="ap_original" ' + (ap_original == 1 ? 'checked' : '') + '>' +
										'<span class="slider"></span>' +
										'</label>' +
									'</div>' +
								'</div>';
		}
		if (type == 'add') {
			$('#additional_box').html(html_additional);
		} else {
			$('#eadditional_box').html(html_additional);
		}
	}

	$(document).ready(function() {
		$('select[name="dokter"]').select2({
			placeholder: 'Pilih Dokter',
			minimumInputLength: 3,
			allowClear: true,
			dropdownParent: $('#modal-form'),
			ajax: {
				url: base_url + 'transaction/profiling/get_dokter',
				dataType: 'json',
				delay: 250,
				processResults: function(data) {
					return {
						results: data
					};
				},
				cache: true
			}
		});
	})
</script>