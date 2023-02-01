<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<button class="btn btn-sky px-3" id="bSubmit" onclick="popUpSubmit()"><i class="fa-check mr-2"></i>APPROVE ALL WAITING STATUS</button>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('transaction/approval_profiling/data'),'trxprof_'.date('Y').'_'.active_cycle());
		thead();
			tr();
				th('#','text-center','width="30" data-content="id"');
				th(lang('dokter'),'','data-content="nama_dokter" data-custom="true"');
				th('Specialist','','data-content="nama_spesialist" data-custom="true"');
				th('Outlet','','data-content="nama_outlet" data-custom="true"');
				th('Patient Type','text-center','data-content="tipe_pasien"');
				th(lang('jumlah_pasien'),'text-center','data-content="jumlah_pasien_perbulan"');
				th('Potensi','text-center','data-content="jumlah_potensi" data-custom="true"');
				th('Approve ?','text-center','data-content="status" data-type="boolean" data-boolean-text="WAITING, NOT APPROVED" data-boolean-value="WAITING, NOT APPROVED"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('transaction/approval_profiling/save'), 'post', 'form');
			col_init(4, 8);
				input('hidden', 'id', 'id');
				label('A. Product');
				select2('Product Group', 'produk_grup', 'required', $this->session->userdata('produk_group'), 'kode', 'nama','','disabled');
				label('B. Doctor');
				select('Dokter', 'dokter', 'required', [],'','','','disabled');
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
		form_close();
	modal_footer();
modal_close();
?>
<div class="filter-panel">
	<div class="filter-header bg-primary text-white">
		<i class="fa-search mr-2"></i> Pencarian
	</div>
	<div class="filter-body">
		<?php
			$tmp_tahun = [];
			for($i=date('Y');$i>=2019;$i--){
				$tmp_tahun[] = $i;
			}
			col_init(12,12);
			select2('Produk Group', 'fpgroup','',$this->session->userdata('produk_group'), 'kode','nama','','onchange="filter()"');
			select('Cycle', 'fcycle','',[
				1,2,3
			],'','','','onchange="filter()" disabled');
			select('Tahun', 'ftahun','',$tmp_tahun,'','','','onchange="filter()" disabled');
			select2('Field Force','fmr','','','','','','onchange="filter()"');
		?>
	</div>
</div>
<script>
	
	var id_approve = '';

	function filter(){
		var pgroup = $('#fpgroup').val();
		var mr = $('#fmr').val();
		$('[data-serverside]').attr('data-serverside', base_url + 'transaction/approval_profiling/data?grup='+pgroup+'&mr='+mr)
		refreshData()
	}

	$('#fpgroup,#fcycle,#ftahun').on('change', function(){
		var pgroup = $('#fpgroup').val();
		var cycle = $('#fcycle').val();
		var tahun = $('#ftahun').val();
		if(pgroup != '' && cycle != '' && tahun != ''){
			$('#fmr').html('<option value=""> Loading Data... </option>').attr('disabled',true);
			$.ajax({
				url: '<?=base_url('transaction/approval_profiling/getMRPerCycle/')?>'+cycle+'/'+tahun,
				success: function(resp){
					if(resp.length > 0){
						var html_option = '<option value="">Select MR </option>';
						$.each(resp, function(i,v){
							html_option += '<option value="'+v.n_mr+'">'+v.nama_mr+'</option>';
						})
						$('#fmr').html(html_option).attr('disabled',false);
					}
				}
			});
		}
	});

	$(document).on('click','tbody td .badge',function(){
		var data_id = $(this).closest('tr').find('.btn-input').attr('data-id');
		var badge = $(this);
		if(badge.attr('class') == 'badge badge-danger'){
			badge.html('WAITING').removeClass('badge-danger').addClass('badge-success');
			$.ajax({
				url: '<?=base_url('transaction/approval_profiling/approval/')?>'+$('#fcycle').val()+'/'+$('#ftahun').val(),
				method: 'post',
				data: {id: data_id},
				success: function(resp){
					if(resp.status==true){
						// badge.html('WAITING').removeClass('badge-danger').addClass('badge-success');
					} else {
						cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
					}
				}
			})
		} else {
			if(badge.html() == 'WAITING'){
				badge.html('NOT APPROVED').removeClass('badge-success').addClass('badge-danger');
				$.ajax({
					url: "<?=base_url('transaction/approval_profiling/approval/')?>"+$('#fcycle').val()+'/'+$('#ftahun').val(),
					method: 'post',
					data: {id: data_id,alasan_not_approve:'none'},
					success: function(resp){
						if(resp.status==true){
							// badge.html('NOT APPROVED').removeClass('badge-success').addClass('badge-danger');
						} else {
							cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
						}
					}
				})
			}
		}
	});

	function popUpSubmit(){
		var pgroup = $('#fpgroup').val();
		var fmr = $('#fmr').val();
		if(pgroup != '' && fmr != ''){
			cConfirm.open('Pastikan data profiling sudah fix dalam approve atau tidak approve','submit');
		}
	}

	function submit(){
		var pgroup = $('#fpgroup').val();
		var mr = $('#fmr').val();
		$.ajax({
			url: '<?=base_url('transaction/approval_profiling/submit/')?>'+$('#fcycle').val()+'/'+$('#ftahun').val(),
			method: 'post',
			data: {pgroup: pgroup, mr:mr},
			success: function(resp){
				cAlert.open(resp.message, resp.status)
				if(resp.status == 'success'){
					refreshData()
				}
			}
		});
	}

	$('#produk_grup').on('change', function() {
		var tpgroup = $(this).val();

		init_indikasi(tpgroup);
		init_additional(tpgroup);
	});

	$(document).on('change','#dokter',function(){
		let id = $(this).val()
		$.ajax({
			url: base_url + 'transaction/approval_profiling/get_detail_dokter?id='+id,
			success: function(r){
				$('#spesialist').val(r.nama_spesialist)
				$('#sub_spesialist').val(r.nama_sub_spesialist)
			}
		})
	})

	function init_indikasi(grup) {
		$('#indikasi_box').html('<div class="text-center"> Loading Data <i class="fa fa-spinner fa-spin fa-3x"></i></div>');
		$.ajax({
			url: '<?= base_url('transaction/approval_profiling/get_indikasi?pgrup=') ?>' + grup,
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

	function init_additional(grup, fee_patient = '', ap_original = 0) {
		$('#additional_box').html('<div class="text-center"> Loading Data <i class="fa fa-spinner fa-spin fa-3x"></i></div>');
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
		$('#additional_box').html(html_additional);
	}
</script>