<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
					<div class="card-header">Report Use Confirm</div>
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
modal_open('modal-detail','Detail Visit Plan');
	modal_body();
		// form_open(base_url('transaction/visit_plan/update'),'post','detail-form');
			col_init(3,9);
			input('hidden','dprofiling','dprofiling');
			label('A. Periode');
			select2('Bulan','dbulan','required',[
				[
					'id' => '1',
					'nama' => 'Januari'
				],
				[
					'id' => '2',
					'nama' => 'Februari'
				],
				[
					'id' => '3',
					'nama' => 'Maret'
				],
				[
					'id' => '4',
					'nama' => 'April'
				],
				[
					'id' => '5',
					'nama' => 'Mei'
				],
				[
					'id' => '6',
					'nama' => 'Juni'
				],
				[
					'id' => '7',
					'nama' => 'Juli'
				],
				[
					'id' => '8',
					'nama' => 'Agustus'
				],
				[
					'id' => '9',
					'nama' => 'September'
				],
				[
					'id' => '10',
					'nama' => 'Oktober'
				],
				[
					'id' => '11',
					'nama' => 'November'
				],
				[
					'id' => '12',
					'nama' => 'Desember'
				],
			],'id','nama','','disabled="disabled"');
			$year = [];
			for($i=date('Y');$i>=2018;$i--){
				$year[$i] = $i;
			}
			select2('Tahun','dtahun','required',$year,'','',date('Y'),'disabled="disabled"');
			label('B. Data Doctor');
			input('text','Product Group','dproduk_grup','required','','disabled="disabled"');
			input('text','Doctor','ddokter','required','','disabled="disabled"');
			input('text','Spesialist','dspesialist','required','','disabled="disabled"');
			input('text','Outlet','doutlet','required','','disabled="disabled"');
			label('C. Plan Kunjungan');
			input('number',lang('standard_call'),'dstandard_call','required','','disabled="disabled"');
			input('number',lang('week1'),'dweek1','','','disabled="disabled"');
			input('number',lang('week2'),'dweek2','','','disabled="disabled"');
			input('number',lang('week3'),'dweek3','','','disabled="disabled"');
			input('number',lang('week4'),'dweek4','','','disabled="disabled"');
			input('number',lang('week5'),'dweek5','','','disabled="disabled"');
			input('number',lang('week6'),'dweek6','','','disabled="disabled"');
			label('D. Marketing');
			select2(lang('marketing_program'),'dmarketing_program','required','','','','','disabled="disabled"');
			echo '<div class="form-group row">';
			echo '	<div class="col-md-3">';
			echo '		Maketing Aktifitas';
			echo '	</div>';
			echo '	<div class="col-md-9" id="dmarketing_aktifitas_box">';
			echo '	</div>';
			echo '</div>';
			// checkbox(lang('marketing_aktivitas'),'marketing_aktivitas');
			// form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
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
		$.ajax({

			url : base_url + 'report/use_confirm/data',
			data : $(this).serialize(),
			type : 'post',
			success : function(r) {
				document.querySelector('#result').innerHTML = r;
				$('#datatables').DataTable({
					"paging": false,
					"lengthChange": false,
					"searching": false,
					"ordering": true,
					"info": true,
					"autoWidth": false,
					"responsive": true,
					"pageLength": 10,
					"language": {
						"lengthMenu": "Menampilkan _MENU_ data per halaman",
						"zeroRecords": "Tidak ada data",
						"info": "Data _PAGE_ dari _PAGES_",
						"infoEmpty": "Tidak ada data",
						"infoFiltered": "(filtered from _MAX_ total records)"
					}
				});
			}
		});
	});

	// $(document).on('dblclick','tbody td .badge',function(){
	// 	if('<?=user('id_group')?>' == '<?=AM_ROLE_ID?>'){
	// 		var data_id = $(this).attr('data-id');
	// 		if($(this).attr('class') == 'badge badge-danger'){
	// 			id_approve = data_id;
	// 			cConfirm.open('Apakah mau dikembalikan menjadi approve ?', 'approve');
	// 		} else {
	// 			$('#nid').val(data_id);
	// 			$('#not-approved-form').modal();
	// 		}
	// 	}
	// });

	// $(document).on('submit','#save_na', function(e){
	// 	if( $('#nreason').val() != ''){
	// 		e.preventDefault();
	// 		$.ajax({
	// 			url: "<?=base_url('transaction/approval_visit_plan/approval')?>",
	// 			method: 'post',
	// 			data: {id: $('#nid').val(),alasan_not_approve: $('#nreason').val()},
	// 			success: function(resp){
	// 				if(resp.status==true){
	// 					cAlert.open('Sudah diubah menjadi Not Approve','success');
	// 					$('#not-approved-form').modal('toggle');
	// 					$(this).trigger("reset");
	// 					$('span[data-id="'+$('#nid').val()+'"]').attr('class','badge badge-danger');
	// 					$('span[data-id="'+$('#nid').val()+'"]').html('NOT APPROVED');
	// 				} else {
	// 					cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
	// 				}
	// 			}
	// 		})
	// 	}
	// });

	// function approve(){
	// 	$.ajax({
	// 		url: '<?=base_url('transaction/approval_visit_plan/approval')?>',
	// 		method: 'post',
	// 		data: {id: id_approve},
	// 		success: function(resp){
	// 			if(resp.status==true){
	// 				cAlert.open('Sudah diubah kembali menjadi approve','success');
	// 				$('#form-filter').submit();
	// 				$('span[data-id="'+id_approve+'"]').attr('class','badge badge-success');
	// 				$('span[data-id="'+id_approve+'"]').html('APPROVED');
	// 			} else {
	// 				cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
	// 			}
	// 		}
	// 	})
	// }
	
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
		// 	url: base_url+'report/history_visit_plan/get_produk_grup?team='+team,
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
			url: base_url+'report/history_visit_plan/get_am?team='+team,
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
			url: base_url+'report/history_visit_plan/get_mr?team='+team+'&am='+am,
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

	$(document).on('click','.btn-detail', function(){
		$.ajax({
			url: '<?=base_url('report/history_visit_plan/get_data/')?>'+$('#fbulan').val()+'/'+$('#ftahun').val(),
			type: 'post',
			data: {id: $(this).attr('data-id')},
			success: function(resp){
				$('#did').val(resp.id);
				$('#dprofiling').val(resp.profiling);
				$('#dbulan').val(resp.bulan).trigger('change');
				$('#dtahun').val(resp.tahun).trigger('change');
				$('#dproduk_grup').val(resp.nama_produk_grup);
				$('#ddokter').val(resp.nama_dokter);
				$('#dspesialist').val(resp.nama_spesialist);
				$('#doutlet').val(resp.nama_outlet);
				$('#dstandard_call').val(resp.standard_call);
				$('#dweek1').val(resp.week1);
				$('#dweek2').val(resp.week2);
				$('#dweek3').val(resp.week3);
				$('#dweek4').val(resp.week4);
				$('#dweek5').val(resp.week5);
				$('#dweek6').val(resp.week6);
				get_outlet(resp.dokter);
				get_marketing_aktifitas(resp.produk_grup,'detail',resp.marketing_aktifitas);
				get_marketing_program(resp.produk_grup,'detail',resp.marketing_program);
				$('#modal-detail').modal();
			}
		})
	});

	function get_marketing_program(id,type='add',value=''){
		$.ajax({	
			url: '<?=base_url('report/history_visit_plan/get_marketing_program?pgroup=')?>'+id,
			success: function(resp){
				var opt_marketing_program = '<option value=""></option>';
				$.each(resp, function(i, val){
					opt_marketing_program += '<option value="'+val.id+'">'+val.nama+'</option>';
				});
				if(type=='add'){
					$('#marketing_program').html('');
					$('#marketing_program').html(opt_marketing_program);
				} else if(type=='edit') {
					$('#emarketing_program').html('');
					$('#emarketing_program').html(opt_marketing_program);
				} else {
					$('#dmarketing_program').html('');
					$('#dmarketing_program').html(opt_marketing_program);
				}
				if(value != ''){
					if(type == 'edit'){
						$('#emarketing_program').val(value).trigger('change');
					} else {
						$('#dmarketing_program').val(value).trigger('change');
					}
				}
			}
		});
	}

	function get_marketing_aktifitas(id,type='add',value=''){
		$.ajax({
			url: '<?=base_url('report/history_visit_plan/get_marketing_aktifitas?pgroup=')?>'+id,
			success: function(resp){
				var opt_marketing_aktifitas = '';
				if(value != null){
					value = value.split(',');
				} else {
					value = [];
				}
				$.each(resp, function(i, val){
					if(type=='add'){
						opt_marketing_aktifitas += '<input type="checkbox" id="'+(val.nama).replace(' ','_')+'" name="marketing_aktifitas[]" value="'+val.id+'">&nbsp;&nbsp;<label for="'+(val.nama).replace(' ','_')+'">'+val.nama+'</label><br>';
					} else {
						var tmp_checked = '';
						if(value.length>0){
							for(var i=0;i<value.length;i++){
								if(value[i] == val.id){
									tmp_checked='checked="true"';
								}
							}
						}
						if(type == 'edit'){
							opt_marketing_aktifitas += '<input type="checkbox" id="'+(val.nama).replace(' ','_')+'" name="emarketing_aktifitas[]" value="'+val.id+'" '+tmp_checked+'>&nbsp;&nbsp;<label for="'+(val.nama).replace(' ','_')+'">'+val.nama+'</label><br>';
						} else {
							opt_marketing_aktifitas += '<input type="checkbox" id="'+(val.nama).replace(' ','_')+'" name="dmarketing_aktifitas[]" value="'+val.id+'" '+tmp_checked+' disabled="disabled">&nbsp;&nbsp;<label for="'+(val.nama).replace(' ','_')+'">'+val.nama+'</label><br>';
						}
					}
				});
				if(type=='add'){
					$('#marketing_aktifitas_box').html('');
					$('#marketing_aktifitas_box').html(opt_marketing_aktifitas);
				} else if(type == 'edit') {
					$('#emarketing_aktifitas_box').html('');
					$('#emarketing_aktifitas_box').html(opt_marketing_aktifitas);
				} else {
					$('#dmarketing_aktifitas_box').html('');
					$('#dmarketing_aktifitas_box').html(opt_marketing_aktifitas);
				}
			}
		})
	}

	function get_outlet(id){
		$('#dokter').attr('disabled',true);
		$('#outlet').html('<option value="">Please Wait..</option>');
		$('#outlet').attr('disabled',true);
		$.ajax({
			url: '<?=base_url('report/history_visit_plan/get_outlet?dokter=')?>'+id+'&pgroup='+$('#fpgroup').val(),
			success: function(resp){
				var opt_outlet = '';
				$.each(resp, function(i, val){
					opt_outlet += '<option value="'+val.id+'">'+val.nama+'</option>';
				});
				if(resp.length > 0){
					$('#profiling').val(resp[0].id);
				}
				$('#outlet').html('');
				$('#outlet').html(opt_outlet);
				$('#outlet').attr('disabled',false);
				$('#dokter').attr('disabled',false);
			}
		})
	}

</script>