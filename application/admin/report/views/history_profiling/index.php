<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.2.1/js/dataTables.fixedColumns.min.js"></script>
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
								History Profiling
							</div>
							<div class="col-sm-6 text-right">
								<button class="btn btn-fresh btn-sm btn-export"><i class="fa fa-download mr-2"></i> Export</button>
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
			if(user('id_group') == MR_ROLE_ID || user('id_group') == DEV_ROLE_ID){
				form_button(lang('simpan'), lang('batal'));
			}
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
				$('.datatable').DataTable({
					fixedHeader: true,
					scrollY:"500px",
					scrollX: true,
					scrollCollapse: true,
					paging: false,
					fixedColumns:   {
						left: 2,
						right: 1
					}
				});
			}
		});
	}

	$(document).on('click','.btn-detail', function(){
		$(this).html('<i class="fa fa-spinner-third spin"></i>');
		$(this).attr('disabled','disabled');
		var id = $(this).attr('data-id');
		$.ajax({
			url: '<?=base_url()?>'+'report/history_profiling/get_data/'+$('#fbulan').val()+'/'+$('#ftahun').val(),
			method: 'post',
			data: {id: id},
			success: function(r){
				$('#id').val(r.id)
				$('#produk_grup').val(r.produk_grup).trigger('change')
				let dokter = r.dokter != undefined ? r.dokter : ''
				let outlet = r.outlet != undefined ? r.outlet : ''
				if(dokter != ''){
					$('#dokter').html('<option value="'+dokter+'" selected>'+ r.nama_dokter + '</option>')
					$('#dokter').trigger('change')
				}
				if(outlet != ''){
					$('#outlet').html('<option value="'+outlet+'" selected>'+ r.nama_outlet + '</option>')
				}
				$('#branch').val(r.branch).trigger('change')
				$("input[name=channel_outlet][value='" + r.channel_outlet + "']").attr('checked', 'checked')
				$("input[name=tipe_pasien][value='" + r.tipe_pasien + "']").attr('checked', 'checked')
				$('#jumlah_pasien_perbulan').val(r.jumlah_pasien_perbulan)

				response_edit = r
				$('.btn-detail').html('<i class="fa fa-search"></i>')
				$('.btn-detail').removeAttr('disabled')
				$('#modal-form').modal();
			}
		})
	});

	$('#produk_grup').on('change', function() {
		var tpgroup = $(this).val();

		init_indikasi(tpgroup);
		init_additional(tpgroup);
	});

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

	$(document).on('click','tbody td .badge',function(){

		if('<?=user('id_group')?>' != '<?=MR_ROLE_ID?>'){
			var data_id = $(this).attr('data-id');
			var badge = $(this);
			if(badge.attr('class') == 'badge badge-danger'){
				badge.html('APPROVED').removeClass('badge-danger').addClass('badge-success');
				$.ajax({
					url: '<?=base_url('transaction/approval_profiling/approval/')?>'+__bulan_to_cycle($('#fbulan').val())+'/'+$('#ftahun').val(),
					method: 'post',
					data: {id: data_id, type_approved: 'approved'},
					success: function(resp){
						if(resp.status==true){
							// badge.html('WAITING').removeClass('badge-danger').addClass('badge-success');
						} else {
							cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
						}
					}
				})
			} else {
				if(badge.html() == 'APPROVED'){
					badge.html('NOT APPROVED').removeClass('badge-success').addClass('badge-danger');
					$.ajax({
						url: "<?=base_url('transaction/approval_profiling/approval/')?>"+__bulan_to_cycle($('#fbulan').val())+'/'+$('#ftahun').val(),
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
		}
	});

	function __bulan_to_cycle(bulan){
		let cycle = 3
		if(['01','02','03','04'].includes(bulan)){
			cycle = 1
		} else if(['05','06','07','08'].includes(bulan)){
			cycle = 2
		}
		return cycle
	}

	$(document).on('click', '.btn-export', function(){
		let produk_group = $('#fpgroup').val()
		let mr = $('#fmr').val()
		let bulan = $('#fbulan').val()
		let tahun = $('#ftahun').val()

		$(this).html('<i class="fa fa-spinner-third spin mr-2"></i> Loading')
		$(this).attr('disabled',true)
		location.href = base_url + 'report/history_profiling/export?mr='+mr+'&tahun='+tahun+'&produk_group='+produk_group+'&bulan='+bulan

		$(this).html('<i class="fa fa-download mr-2"></i> Export')
		$(this).removeAttr('disabled', false)
	})

</script>