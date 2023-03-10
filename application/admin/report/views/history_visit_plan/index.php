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
					<div class="card-header">
						<div class="row">
							<div class="col-sm-6">
								History Visit Plan
							</div>
							<div class="col-sm-6 text-right">
								<button class="btn btn-fresh btn-sm btn-export"><i class="fa fa-download mr-2"></i> Export </button>
							</div>
						</div>
					</div>
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
modal_open('modal-form','Edit Visit Plan');
modal_body();
	form_open(base_url('transaction/visit_plan/save'),'post','edit-form');
		col_init(3,9);
		input('hidden','id','id');
		label('A. Data Doctor');
		input('text','Product Group','nama_produk_grup','','','disabled="disabled"');
		input('text','Doctor','nama_dokter','required','','disabled="disabled"');
		input('text','Spesialist','nama_spesialist','','','disabled="disabled"');
		input('text','Outlet','nama_outlet','','','disabled="disabled"');
		textarea('Note','note','','','disabled="disabled"');
		label('B. Plan Kunjungan');
		input('number','Week 1','week1');
		input('number','Week 2','week2');
		input('number','Week 3','week3');
		input('number','Week 4','week4');
		input('number','Week 5','week5');
		input('number','Week 6','week6');
		// if(user('id_group') == MR_ROLE_ID){
		// 	form_button(lang('simpan'),lang('batal'));
		// }
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

	var id_approve = '';

	$('#form-filter').submit(function(e){
		e.preventDefault();
		$.ajax({

			url : base_url + 'report/history_visit_plan/data',
			data : $(this).serialize(),
			type : 'post',
			success : function(r) {
				document.querySelector('#result').innerHTML = r;
			}
		});
	});

	$(document).on('dblclick','tbody td .badge',function(){
		if('<?=user('id_group')?>' != '<?=MR_ROLE_ID?>'){
			var data_id = $(this).attr('data-id');
			if($(this).attr('class') == 'badge badge-danger'){
				id_approve = data_id;
				cConfirm.open('Apakah mau dikembalikan menjadi approve ?', 'approve');
			} else {
				$('#nid').val(data_id);
				$('#not-approved-form').modal();
			}
		}
	});

	$(document).on('submit','#save_na', function(e){
		if( $('#nreason').val() != ''){
			e.preventDefault();
			$.ajax({
				url: "<?=base_url('transaction/approval_visit_plan/approval')?>",
				method: 'post',
				data: {id: $('#nid').val(),alasan_not_approve: $('#nreason').val()},
				success: function(resp){
					if(resp.status==true){
						cAlert.open('Sudah diubah menjadi Not Approve','success');
						$('#not-approved-form').modal('toggle');
						$(this).trigger("reset");
						$('span[data-id="'+$('#nid').val()+'"]').attr('class','badge badge-danger');
						$('span[data-id="'+$('#nid').val()+'"]').html('NOT APPROVED');
					} else {
						cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
					}
				}
			})
		}
	});

	function approve(){
		$.ajax({
			url: '<?=base_url('transaction/approval_visit_plan/approval')?>',
			method: 'post',
			data: {id: id_approve},
			success: function(resp){
				if(resp.status==true){
					cAlert.open('Sudah diubah kembali menjadi approve','success');
					$('#form-filter').submit();
					$('span[data-id="'+id_approve+'"]').attr('class','badge badge-success');
					$('span[data-id="'+id_approve+'"]').html('APPROVED');
				} else {
					cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
				}
			}
		})
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
		get_am(team);
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
		$(this).html('<i class="fa fa-spinner-third spin"></i>')
		$(this).attr('disabled', true)
		$.ajax({
			url: '<?=base_url('report/history_visit_plan/get_data/')?>'+$('#fbulan').val()+'/'+$('#ftahun').val(),
			type: 'post',
			data: {id: $(this).attr('data-id')},
			success: function(resp){
				$('#id').val(resp.id);
				$('#nama_produk_grup').val(resp.nama_produk_group);
				$('#nama_dokter').val(resp.nama_dokter);
				$('#nama_spesialist').val(resp.nama_spesialist);
				$('#nama_outlet').val(resp.nama_outlet);
				$('#note').val(resp.note);
				$('#week1').val(resp.week1);
				$('#week2').val(resp.week2);
				$('#week3').val(resp.week3);
				$('#week4').val(resp.week4);
				$('#week5').val(resp.week5);
				$('#week6').val(resp.week6);

				$('.btn-detail').html('<i class="fa fa-search"></i>')
				$('.btn-detail').removeAttr('disabled');
				$('#modal-form').modal();	
			}
		})
	});

	$(document).on('click','.btn-export',function(){
		let bulan = $('#fbulan').val()
		let tahun = $('#ftahun').val()
		let mr = $('#fmr').val()
		let produk_grup = $('#fpgroup').val()

		location.href = base_url + 'report/history_visit_plan/export?bulan='+bulan+'&tahun='+tahun+'&mr='+mr+'&produk_group='+produk_grup
	})

</script>