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
								History Data Actual
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
modal_open('modal-form','Edit Data Actual','modal-xl');
modal_body();
	form_open(base_url('transaction/data_actual/save'),'post','edit-form');
		col_init(3,9);
		input('hidden','id','id');
		label('A. Info Dokter');
		input('text','Dokter','dokter','','','disabled="disabled"');
		input('text','Spesialist','spesialist','','','disabled="disabled"');
		input('text','Sub Spesialist','sub_spesialist','','','disabled="disabled"');
		input('text','Outlet','outlet','','','disabled="disabled"');
		label('B. Periode');
		input('text','Bulan','bulan','','','disabled="disabled"');
		input('text','Tahun','tahun','','','disabled="disabled"');
		label('C. Produk');
		input('text','Produk Group','produk_group','','','disabled="disabled"');
		if(get('produk_group') == 'EH'){
			input('text','Total ALAI','total_alai');
			input('text','Total TLAI','total_tlai');
			input('text','AP Original','other_ap_original');
		}
		echo '<div class=""><table class="table table-sm table-hover table-app" style="background-color:white; margin-top:20px; margin-bottom:20px" id="table_indikasi">
			<thead id="indikasi_box" style="max-height:300px;overflow-y: auto"></thead>
			<tbody id="indikasi_body"></tbody>
		</table></div>';

		echo '<div id="adt_produk"></div>';
		// form_button(lang('simpan'),lang('batal'));
	form_close();
// modal_footer();
modal_close();
?>
<script type="text/javascript">

	$('#form-filter').submit(function(e){
		e.preventDefault();
		getData();
		$('#form-edit').attr('action', base_url + 'report/history_data_actual/update/'+$('#fbulan').val()+'/'+$('#ftahun').val());
	});

	function getData(){
		var form = $('#form-filter');
		$.ajax({
			url : base_url + 'report/history_data_actual/data',
			data : form.serialize(),
			type : 'post',
			success : function(r) {
				document.querySelector('#result').innerHTML = r;
			}
		});
	}

	$(document).on('click','.btn-detail', function(){
		$(this).html('<i class="fa fa-spinner-third spin"></i>');
		$(this).attr('disabled','disabled');
		var id = $(this).attr('data-id');
		$.ajax({
			url: '<?=base_url()?>'+'report/history_data_actual/get_data/',
			method: 'post',
			data: {id: id},
			success: function(r){

				let res_edit = r

				$('#dokter').val(res_edit.nama_dokter)
				$('#spesialist').val(res_edit.nama_spesialist)
				$('#sub_spesialist').val(res_edit.nama_sub_spesialist)
				$('#outlet').val(res_edit.nama_outlet)
				$('#tahun').val('<?=strftime('%Y', strtotime(get('tahun').'-01-01'))?>')
				$('#bulan').val('<?=strftime('%B', strtotime(get('tahun').'-'.get('bulan').'-01'))?>')
				$('#produk_group').val(res_edit.nama_produk_grup)

				let indikasi = res_edit.indikasi
				let value_sku = res_edit.sku
				let produk = res_edit.produk
				let price = 0

				var html_indikasi_head = '<th>No.</th><th>Nama</th><th>Units</th>'
				$.each(indikasi, function(i,v){
					html_indikasi_head += '<th>'+v.nama+'</th>'
				})
				html_indikasi_head += '<th>Jumlah</th>';
				var html_sku = ''
				$.each(produk, function(i, v){
					html_sku += '<tr><td>'+(i+1)+'</td><td>'+v.nama+'</td><td><input type="hidden" name="sku[]" value="'+v.id+'">'
					var val_indikasi_1 = 0
					var val_indikasi_2 = 0
					var val_indikasi_3 = 0
					var val_indikasi_4 = 0
					var val_indikasi_5 = 0
					var val_indikasi_6 = 0
					var val_indikasi_7 = 0
					var val_indikasi_8 = 0
					var val_indikasi_9 = 0
					var val_indikasi_10 = 0
					var number_of_unit = 0

					$.each(value_sku, function(j,w){
						if(v.id == w.produk){
							val_indikasi_1 = w.value_1
							val_indikasi_2 = w.value_2
							val_indikasi_3 = w.value_3
							val_indikasi_4 = w.value_4
							val_indikasi_5 = w.value_5
							val_indikasi_6 = w.value_6
							val_indikasi_7 = w.value_7
							val_indikasi_8 = w.value_8
							val_indikasi_9 = w.value_9
							val_indikasi_10 = w.value_10
							number_of_unit = w.number_of_unit
							price = w.price
						}
					})
					$.each(indikasi, function(k,x){
						var tmp_val = 0;
						switch(k){
							case 0:
								tmp_val = val_indikasi_1
							break;
							case 1:
								tmp_val = val_indikasi_2
							break;
							case 2:
								tmp_val = val_indikasi_3
							break;
							case 3:
								tmp_val = val_indikasi_4
							break;
							case 4:
								tmp_val = val_indikasi_5
							break;
							case 5:
								tmp_val = val_indikasi_6
							break;
							case 6:
								tmp_val = val_indikasi_7
							break;
							case 7:
								tmp_val = val_indikasi_8
							break;
							case 8:
								tmp_val = val_indikasi_9
							break;
							case 9:
								tmp_val = val_indikasi_10
							break;
						}
						html_sku += '<input type="number" name="units[]" class="form-control form-control-sm" style="width:75px" value="'+number_of_unit+'"></td><td><input type="number" name="value_'+(k+1)+'[]" class="form-control form-control-sm" style="width:75px" value="'+tmp_val+'"></td>'
					})
					html_sku += '<td> Rp.'+numberFormat(price * number_of_unit)+',-</td></tr>';
				})
				
				$('#indikasi_body').html(html_sku)
				$('#indikasi_box').html(html_indikasi_head)

				let sku_adt = res_edit.sku_adt

				let html_adt = '<div id="accordion">'
				$.each(sku_adt, function(i ,v){
					if(v['kode'] != '<?=get('produk_group')?>'){
						let sku = v.sku
						html_adt += '<div class="card">'
							html_adt += '<div class="card-header" id="heading'+v.kode+'">'
								html_adt += '<h5 class="mb-0">'
									html_adt += '<button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#produk'+v.kode+'" aria-controls="produk'+v.kode+'">'
										html_adt += '<b>'+v.nama+'</b>'
									html_adt += '</button>'
								html_adt += '</h5>'
							html_adt += '</div>'
							html_adt += '<div id="produk'+v.kode+'" class="collapse" aria-labelledby="heading'+v.kode+'" data-parent="#accordion">'
								html_adt += '<div class="card-body p-0">'
									html_adt += '<table class="table table-bordered table-stripped">'
										html_adt += '<thead>'
											html_adt += '<th>No.</th><th>Nama</th><th>Units</th>'
											$.each(v.indikasi, function(i,v){
												html_adt += '<th>'+v.nama+'</th>'
											})
											html_adt += '<th>Jumlah</th>';
										html_adt += '</thead>'
										html_adt += '<tbody>'
											$.each(v.produk, function(i, vv){

											var val_indikasi_1 = 0
											var val_indikasi_2 = 0
											var val_indikasi_3 = 0
											var val_indikasi_4 = 0
											var val_indikasi_5 = 0
											var val_indikasi_6 = 0
											var val_indikasi_7 = 0
											var val_indikasi_8 = 0
											var val_indikasi_9 = 0
											var val_indikasi_10 = 0
											var number_of_unit = 0

											$.each(sku, function(j,w){
												if(vv.id == w.produk){
													val_indikasi_1 = w.value_1
													val_indikasi_2 = w.value_2
													val_indikasi_3 = w.value_3
													val_indikasi_4 = w.value_4
													val_indikasi_5 = w.value_5
													val_indikasi_6 = w.value_6
													val_indikasi_7 = w.value_7
													val_indikasi_8 = w.value_8
													val_indikasi_9 = w.value_9
													val_indikasi_10 = w.value_10
													number_of_unit = w.number_of_unit
													price = w.price
												}
											})
											html_adt += '<tr><td>'+(i+1)+'</td><td>'+vv.nama+'</td><td><input type="hidden" name="sku_adt_'+v.kode+'[]" value="'+vv.id+'"><input type="number" name="units_adt_'+v.kode+'[]" class="form-control form-control-sm" style="width:75px" value="'+number_of_unit+'"></td>'
											
											$.each(v.indikasi, function(k,x){
												var tmp_val = 0;
												switch(k){
													case 0:
														tmp_val = val_indikasi_1
													break;
													case 1:
														tmp_val = val_indikasi_2
													break;
													case 2:
														tmp_val = val_indikasi_3
													break;
													case 3:
														tmp_val = val_indikasi_4
													break;
													case 4:
														tmp_val = val_indikasi_5
													break;
													case 5:
														tmp_val = val_indikasi_6
													break;
													case 6:
														tmp_val = val_indikasi_7
													break;
													case 7:
														tmp_val = val_indikasi_8
													break;
													case 8:
														tmp_val = val_indikasi_9
													break;
													case 9:
														tmp_val = val_indikasi_10
													break;
												}
												html_adt += '<td><input type="number" name="value_adt_'+v.kode+(k+1)+'[]" class="form-control form-control-sm" style="width:75px" value="'+tmp_val+'"></td>'
											})
											html_adt += '<td> Rp.'+numberFormat(price * number_of_unit)+',-</td></tr>';
										})
										html_adt += '</tbody>'
									html_adt += '</table>'
								html_adt += '</div>'
							html_adt += '</div>'
						html_adt += '</div>'
					}
				})
				html_adt += '</div>'
				$('#adt_produk').html(html_adt)
				$('#modal-form').modal();
				$('.btn-detail').html('<i class="fa fa-search"></i>');
				$('.btn-detail').removeAttr('disabled');
			}
		})
	});

	$('#produk_grup').on('change', function() {
		var tpgroup = $(this).val();

		init_indikasi(tpgroup);
		init_additional(tpgroup);
	});
	
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
			url: base_url+'report/history_data_actual/get_am?team='+team,
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
			url: base_url+'report/history_data_actual/get_mr?team='+team+'&am='+am,
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

	$(document).on('click', '.btn-export', function(){
		let produk_group = $('#fpgroup').val()
		let mr = $('#fmr').val()
		let bulan = $('#fbulan').val()
		let tahun = $('#ftahun').val()

		$(this).html('<i class="fa fa-spinner-third spin mr-2"></i> Loading')
		$(this).attr('disabled',true)
		location.href = base_url + 'report/history_data_actual/export?mr='+mr+'&tahun='+tahun+'&produk_group='+produk_group+'&bulan='+bulan

		$(this).html('<i class="fa fa-download mr-2"></i> Export')
		$(this).removeAttr('disabled', false)
	})

</script>