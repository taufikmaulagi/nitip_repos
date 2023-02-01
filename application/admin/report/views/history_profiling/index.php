<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="main-container">
		<div class="row">
			<div class="col-md-3 mb-3">
				<div class="card sticky-top">
					<div class="card-header">Filter</div>
					<div class="card-body">
						<form method="post" action="javascript:;" id="form-filter">
							<div class="form-group row">
								<label class="col-form-label col-12" for="periode"><?php echo lang('periode'); ?></label>
								<div class="col-7">
									<select class="select2 form-control" id="fbulan" name="fbulan">
										<option value="">Pilih Bulan</option>
										<option value="01" <?=(date('m') == '01' ?  'selected="selected"' : '')?>>Januari</option>
										<option value="02" <?=(date('m') == '02' ?  'selected="selected"' : '')?>>Februari</option>
										<option value="03" <?=(date('m') == '03' ?  'selected="selected"' : '')?>>Maret</option>
										<option value="04" <?=(date('m') == '04' ?  'selected="selected"' : '')?>>April</option>
										<option value="05" <?=(date('m') == '05' ?  'selected="selected"' : '')?>>Mei</option>
										<option value="06" <?=(date('m') == '06' ?  'selected="selected"' : '')?>>Juni</option>
										<option value="07" <?=(date('m') == '07' ?  'selected="selected"' : '')?>>Juli</option>
										<option value="08" <?=(date('m') == '08' ?  'selected="selected"' : '')?>>Agustus</option>
										<option value="09" <?=(date('m') == '09' ?  'selected="selected"' : '')?>>September</option>
										<option value="10" <?=(date('m') == '10' ?  'selected="selected"' : '')?>>Oktober</option>
										<option value="11" <?=(date('m') == '11' ?  'selected="selected"' : '')?>>November</option>
										<option value="12" <?=(date('m') == '12' ?  'selected="selected"' : '')?>>Desember</option>
									</select>
								</div>
								<div class="col-5">
									<select class="select2 form-control" name="ftahun" id="ftahun">
										<option value="">Pilih Tahun</option>
										<?php for($i = date('Y'); $i >= 2018; $i--) { ?>
										<option value="<?php echo $i; ?>"<?php if($i == date('Y')) echo ' selected'; ?>><?php echo $i; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-12" for="fteam">Team</label>
								<div class="col-12">
									<select class="select2 form-control" name="fteam" id="fteam">
										<?php
										$where = [];
										if(in_array(user('id_group'), [AM_ROLE_ID,MR_ROLE_ID])){
											if($this->session->userdata('team')){
												$tmp_team = [];
												foreach($this->session->userdata('team') as $val){
													array_push($tmp_team, $val['kode_team']);
												}
												$where['kode'] = $tmp_team;
											}
										}
										$where['divisi'] = 'E';
										foreach (get_data('tim',[
											'where' => $where
										])->result_array() as $val) { ?>
										<option value="<?php echo $val['kode']; ?>"><?php echo $val['nama'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-12" for="fpgroup">Product Group</label>
								<div class="col-12">
									<select class="select2 form-control" name="fpgroup" id="fpgroup">
									<?php
									if(user('id_group') == 1){
										$produk = get_data('produk_grup','is_active',1)->result_array();
									} else {
										$produk = $this->session->userdata('produk_group');
									}
									foreach($produk as $val){
										echo '<option value="'.$val['kode'].'">'.$val['nama'].'</option>';
									} ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-12" for="fam">Nama AM</label>
								<div class="col-12">
									<select class="select2 form-control" name="fam" id="fam">
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-12" for="fmr">Nama MR</label>
								<div class="col-12">
									<select class="select2 form-control" name="fmr" id="fmr">
									</select>
								</div>
							</div>
							<div class="form-group row mt-3">
								<div class="col-12">
									<button type="submit" class="btn btn-sky btn-block"><i class="fa fa-search"></i>&nbsp;Filter</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="card">
					<div class="card-header">History Profiling</div>
					<div class="card-body" style="margin: 0; padding:0" id="result">
						<div class="text-center">
							<img src="<?=base_url('assets/images/no-data.svg')?>" width="40%">
							<h3> Oops! Data Tidak Ditemukan :(</h3> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
modal_open('rs-profiling','Rumah Sakit Profiling','modal-lg','');
	// modal_body('rs-profiling');
		echo '<div class="table-responsive">';
		echo '<table class="table table-bordered table-app" id="rs-table">';
		echo '	<thead>';
		echo '		<tr>';
		echo '			<th>#</th>';
		echo '			<th>Rumah Sakit</th>';
		echo '			<th>Jumlah Pasien</th>';
		echo '			<th>Potensi</th>';
		echo '			<th></th>';
		echo '		</tr>';
		echo '	</thead>';
		echo '	<tbody>';
		echo '	</tbody>';
		echo '</table>';
		echo '</div>';
modal_close();
modal_open('edit-form','Detail Profiling','','','bg-secondary');
	modal_body();
		form_open('','post','form-edit');
			col_init(4,8);
			input('hidden','id','id');
			label('A. Product');
			input('text','Product Group','eproduk_grup','required','','disabled="disabled"');
			label('B. Doctor');
			input('hidden','edokter','edokter');
			input('text','Doctor','dokter_list','required','','disabled="disabled"');
			input('text','Spesialist','espesialist','','','disabled="disabled"');
			// input('text','Branch','branch','required','','disabled="disabled"');
			input('text','Practice/Outlet','eoutlet','required','','disabled="disabled"');
			radio('Channel Outlet','echannel_outlet',[
				'Goverment Hospital' => 'Goverment Hospital',
				'Private Hospital' => 'Private Hospital',
				'Apotek' => 'Apotek'
			],'data-validation="required" disabled="disabled"');
			radio('Patient Type','etipe_pasien',[
				'Regular' => 'Regular',
				'Non Regular' => 'Non Regular',
			],'data-validation="required" disabled="disabled"');
			input('number','Number Of Patiens Per Month','ejumlah_pasien','required','');
			label('C. Indications');
			echo '<div id="eindikasi_box"></div>';
			echo '<div id="eadditional_box" style="margin-bottom:10px"></div>';
			label('D. Marketing');
			echo '<div id="emarketing_box"></div>';
			echo '<p>&nbsp;</p>';
			form_button(lang('simpan'),lang('batal'));
		form_close();
modal_close();
modal_open('not-approved-form','Approval Profiling','','','bg-danger');
	modal_body();
		form_open('javascript:void(0)','post','save_na');
			col_init(4,8);
			input('hidden','nid','nid');
			textarea('Reason Not Approve','nreason','required');
			form_button(lang('simpan'),lang('batal'));
		form_close();
modal_close();
?>
<script type="text/javascript">

	$('#form-filter').submit(function(e){
		e.preventDefault();
		getData();
		$('#form-edit').attr('action', base_url + 'report/history_profiling/update/'+$('#fbulan').val()+'/'+$('#ftahun').val());
	});

	function getData(){
		var form = $('#form-filter');
		$.ajax({
			url : base_url + 'report/history_profiling/data',
			data : form.serialize(),
			type : 'post',
			success : function(r) {
				document.querySelector('#result').innerHTML = r;
			}
		});
	}

	$(document).on('click','.btn-detail', function(){
		$(this).html('<i class="fa fa-spinner"></i>');
		$(this).attr('disabled','disabled');
		var id = $(this).attr('data-id');
		$.ajax({
			url: '<?=base_url()?>'+'report/history_profiling/get_all/'+$('#fbulan').val()+'/'+$('#ftahun').val()+'/'+$('#fmr').val()+'?id='+id,
			success: function(resp){
				console.log(resp);
				var appendRs = '';
				var tmpIndex = 1;
				$.each(resp, function(i, val){
					appendRs += '<tr>'+
									'<td>'+(tmpIndex++)+'</td>'+
									'<td>'+(val.nama_outlet ? val.nama_outlet : 'Reguler')+'</td>'+
									// '<td>'+(val.channel_outlet ?  val.channel_outlet : '<center>-</center>')+'</td>'+
									// '<td>'+(val.tipe_pasien ? val.tipe_pasien : '<center>-</center>')+'</td>'+
									'<td>'+(val.jumlah_pasien)+'</td>'+
									'<td>'+(val.total_potensi_tablet ? val.total_potensi_tablet : 0)+'</td>'+
									'<td style="width: 1px; white-space:nowrap;"><button class="btn btn-sky btn-sm btn-detail-rs" data-id="'+val.id+'"><i class="fa-search"></i></button></td>'
								'</tr>';
				});
				$(this).html('<i class="fa fa-search"></i>');
				$(this).prop("disabled", false);
				$('#rs-table > tbody').empty();
				$('#rs-table').append(appendRs);
				$('#rs-profiling').modal();
			}.bind(this)
		});
	});

	$(document).on('click','.btn-detail-rs', function(){
		var id = $(this).attr('data-id');
		$.ajax({
			url: '<?=base_url()?>'+'report/history_profiling/get_data/'+$('#fbulan').val()+'/'+$('#ftahun').val(),
			method: 'post',
			data: {id: id},
			success: function(resp){
				console.log(resp);
				$('#id').val(resp.id);
				$('#eproduk_grup').val(resp.nama_produk_grup);
				$('#eproduk_subgrup').val(resp.nama_produk_sub_grup);
				$('#eproduk').val(resp.nama_produk);
				$('#edokter').val(resp.dokter);
				$('#dokter_list').val(resp.nama_dokter);
				$('#branch').val(resp.nama_branch);
				$('#select2-ebranch-container').attr('title', resp.nama_branch);
				$('#select2-ebranch-container').html(resp.nama_branch);
				$('#eoutlet').val(resp.nama_outlet);
				$('#ejumlah_pasien').val(resp.total_jumlah_pasien);
				$('#espesialist').val(resp.nama_spesialist);
				$("input[name=echannel_outlet][value='" + resp.channel_outlet + "']").attr('checked', 'checked');	
				$("input[name=etipe_pasien][value='" + resp.tipe_pasien + "']").attr('checked', 'checked');
			
				init_indikasi(resp.produk_grup, 'edit', resp.val_indikasi_1, resp.val_indikasi_2, resp.val_indikasi_3, resp.val_indikasi_4, resp.val_indikasi_5, resp.val_indikasi_6, resp.val_indikasi_7, resp.val_indikasi_8, resp.val_indikasi_9, resp.val_indikasi_10);
				init_additional(resp.produk_grup, 'edit', resp.fee_patient, resp.ap_original);
				init_marketing(resp.produk_grup, 'edit', resp.marketing_bulan_1, resp.marketing_bulan_2, resp.marketing_bulan_3, resp.marketing_bulan_4);

				// $('#indikasi_1').val(resp.indikasi_1);
				$('#edit-form').modal();
			}
		})
	});

	function init_indikasi(grup, type = 'add', indikasi_1 = '', indikasi_2 = '', indikasi_3 = '', indikasi_4 = '', indikasi_5 = '', indikasi_6 = '', indikasi_7 = '', indikasi_8 = '', indikasi_9 = '', indikasi_10 = '') {
		$('#indikasi_box').html('');
		$('#eindikasi_box').html('');
		$.ajax({
			url: '<?= base_url('report/history_profiling/get_indikasi?pgrup=') ?>' + grup,
			success: function(resp) {
				var html_indikasi = '';
				var index_indikasi = 1;
				$.each(resp, function(i, val) {
					if (index_indikasi == 11) {
						return false;
					}
					var tmp_val = '';
					switch (index_indikasi) {
						case 1:
							tmp_val = indikasi_1;
							break;
						case 2:
							tmp_val = indikasi_2;
							break;
						case 3:
							tmp_val = indikasi_3;
							break;
						case 4:
							tmp_val = indikasi_4;
							break;
						case 5:
							tmp_val = indikasi_5;
							break;
						case 6:
							tmp_val = indikasi_6;
							break;
						case 7:
							tmp_val = indikasi_7;
							break;
						case 8:
							tmp_val = indikasi_8;
							break;
						case 9:
							tmp_val = indikasi_9;
							break;
						case 10:
							tmp_val = indikasi_10;
							break;
					}
					html_indikasi += '<div class="form-group row">' +
						'<label class="col-form-label col-md-4" for="specialist">' + val.nama + '</label>' +
						'<div class="col-md-8">' +
						'<input type="hidden" name="indikasi_' + index_indikasi + '" id="indikasi_' + index_indikasi + '" autocomplete="off" value="' + val.id + '">' +
						'<input type="number" name="val_indikasi_' + index_indikasi + '" id="val_indikasi_' + index_indikasi + '" autocomplete="off" class="form-control" data-validation="" value="' + tmp_val + '">' +
						'</div>' +
						'</div>';
					index_indikasi++;
				});
				if (type == 'add') {
					$('#indikasi_box').html(html_indikasi);
				} else {
					$('#eindikasi_box').html(html_indikasi);
				}
			}
		});
	}

	<?php if(in_array(user('id_group'), [AM_ROLE_ID, NSM_ROLE_ID])): ?>
	$(document).on('click','tbody td .badge',function(){
		var data_id = $(this).closest('tr').find('.btn-detail').attr('data-id');
		if($(this).attr('class') == 'badge badge-danger'){
			// id_approve = data_id;
			// cConfirm.open('Apakah mau dikembalikan menjadi approve ?', 'approve');
			$.ajax({
				url: '<?=base_url('report/history_profiling/approval/')?>'+$('#fbulan').val()+'/'+$('#ftahun').val(),
				method: 'post',
				data: {id: data_id},
				success: function(resp){
					if(resp.status==true){
						// cAlert.open('Sudah diubah kembali menjadi approve','success');
						refreshData();
					} else {
						cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
					}
				}
			})
		} else {
			// $('#nid').val(data_id);
			$.ajax({
				url: "<?=base_url('report/history_profiling/approval/')?>"+$('#fbulan').val()+'/'+$('#ftahun').val(),
				method: 'post',
				data: {id: data_id,alasan_not_approve:'none'},
				success: function(resp){
					if(resp.status==true){
						// cAlert.open('Sudah diubah menjadi Not Approve','success');
						// $('#not-approved-form').modal();
						// $(this).trigger("reset");
						refreshData();
					} else {
						cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
					}
				}
			})
		}
	});
	<?php endif; ?>

	function init_additional(grup, type='add', fee_patient='', ap_original=0){
		$('#additional_box').html('');
		$('#eadditional_box').html('');
		html_additional = '';
		if(grup == 'EH'){
			html_additional = '<div class="row">'+
				'<h4 class="col-form-label col-12 ">D. Additional</h4>'+
			'</div>'+
			'<div class="form-group row">'+
				'<label class="col-form-label col-md-4 required" for="fee_patient">Fee Patient</label>'+
				'<div class="col-md-8">'+
					'<input type="number" name="fee_patient" id="fee_patient" autocomplete="off" class="form-control" data-validation="" value="'+fee_patient+'">'+
				'</div>'+
			'</div>'+
			'<div class="form-group row">'+
				'<label class="col-form-label col-md-4 required" for="ap_original">R/ Original</label>'+
				'<div class="col-md-8">'+
					'<label class="switch"><input type="checkbox" name="ap_original" id="ap_original" '+(ap_original == 1 ? 'checked' : '')+'>'+
						'<span class="slider"></span>'+
					'</label>'+
				'</div>'+
			'</div>';
		}
		if(type == 'add'){
			$('#additional_box').html(html_additional);
		} else {
			$('#eadditional_box').html(html_additional);
		}
	}
	
	$(document).ready(function(){
		var fteam = $('#fteam').val();
		get_produk_grup(fteam);
	})

	$(document).on('change', '#fteam', function(){
		get_produk_grup($(this).val());
	})

	$(document).on('change','#fam', function(){
		get_mr($('#fteam').val(), $(this).val());
	});

	$(document).on('change','#fpgroup', function(){
		get_am($('#fteam').val());
	});

	function get_produk_grup(team=''){
		// $.ajax({
		// 	url: base_url+'report/history_profiling/get_produk_grup?team='+team,
		// 	success: function(resp){
		// 		var html_pgroup = '';
		// 		$('#fpgroup').html('');
		// 		$.each(resp, function(i, val){
		// 			html_pgroup += '<option value="'+val.kode+'">'+val.nama+'</option>';
		// 		});
		// 		$('#fpgroup').html(html_pgroup);
				get_am(team);
		// 	}
		// });
	}

	function get_am(team){
		$.ajax({
			url: base_url+'report/history_profiling/get_am?team='+team,
			success: function(resp){
				var html_am = '';
				$('#fam').html('');
				$.each(resp, function(i, val){
					html_am += '<option value="'+val.n_am+'">'+val.nama_am+'</option>';
				});
				$('#fam').html(html_am);
				get_mr(team, $('#fam').val());
			}
		});
	}

	function get_mr(team, am){
		$.ajax({
			url: base_url+'report/history_profiling/get_mr?team='+team+'&am='+am,
			success: function(resp){
				var html_am = '';
				$('#fmr').html('');
				$.each(resp, function(i, val){
					html_am += '<option value="'+val.n_mr+'">'+val.nama_mr+'</option>';
				});
				$('#fmr').html(html_am);
			}
		});
	}

	function init_marketing(pgroup, type = 'add', value_1='', value_2='', value_3='', value_4='') {
		$('#marketing_box').html('');
		$('#emarketing_box').html();
		$.ajax({
			url: '<?= base_url('report/history_profiling/get_marketing?pgroup=') ?>' + pgroup,
			success: function(resp) {
				var html_marketing = '<div class="accordion" id="marketingAcc">';
				for(i=1;i<=4;i++){
					var value = '';
					switch(i){
						case 1:
							value = value_1;
						break;
						case 2:
							value = value_2;
						break;
						case 3:
							value = value_3;
						break;
						case 4:
							value = value_4;
						break;
					}
					if (value != null) {
						value = (value).split(',');
					} else {
						value = [];
					}
					var cycle = '<?=active_cycle()?>';
					var bulan = [];
					if(cycle == 1){
						bulan = ['Januari','Februari','Maret','April'];
					} else if(cycle == 2){
						bulan = ['Mei','Juni','Juli','Agustus'];
					} else {
						bulan = ['September','Oktober','November','Desember'];
					}
					var isShow = '';
					if(i == 1){
						isShow = 'show';
					}
					html_marketing += '<div class="card">'+
					'<div class="card-header" id="heading'+i+'">'+
						'<h5 class="mb-0">'+
						'<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse'+i+'" aria-expanded="true" aria-controls="collapse'+i+'"><b>'+
							bulan[i-1]+
						'</b></button>'+
						'</h5>'+
					'</div>'+
					'<div id="collapse'+i+'" class="collapse '+isShow+'" aria-labelledby="heading'+i+'" data-parent="#marketingAcc">'+
						'<div class="card-body">';
					$.each(resp, function(k, val) {
						var tmp_checked = '';
						if (value.length > 0) {
							for (var j = 0; j < value.length; j++) {
								if (value[j] == val.id) {
									tmp_checked = 'checked="true"';
								}
							}
						}
						//input type checkbox
						html_marketing += '<input type="checkbox" id="' + (val.nama).replace(' ', '_') + i + '" name="marketing_bulan_'+i+'[]" value="' + val.id + '" ' + tmp_checked + '>&nbsp;&nbsp;<label for="' + (val.nama).replace(' ', '_') + i + '">' + val.nama + '</label><br>';
					});
					html_marketing += '</div>'+
									'</div>'+
								'</div>';
				}
				html_marketing += '</div>';
				if (type == 'add') {
					$('#marketing_box').html(html_marketing);
				} else {
					$('#emarketing_box').html(html_marketing);
				}
			}
		})
	}
</script>