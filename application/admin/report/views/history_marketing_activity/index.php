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
								History Marketing Activity
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
	form_open(base_url('transaction/data_marketing/save'),'post','edit-form');
		col_init(3,9);
		input('hidden','id','id');
		label('A. Info Dokter');
		input('text','Dokter','dokter','','','disabled="disabled"');
		input('text','Spesialist','spesialist','','','disabled="disabled"');
		input('text','Sub Spesialist','sub_spesialist','','','disabled="disabled"');
		input('text','Outlet','outlet','','','disabled="disabled"');
		label('B. Produk');
		input('text','Produk Group','produk_group','','','disabled="disabled"');
		echo '<div class="table-responsive"><table class="table table-sm table-hover table-app" style="margin-top:20px; margin-bottom:20px" id="table_indikasi">
			<thead id="indikasi_box">
			</thead>
			<tbody id="indikasi_body">
			</tbody>
		</table></div>';
		// form_button(lang('simpan'),lang('batal'));
	form_close();
// modal_footer();
modal_close();
?>
<script type="text/javascript">

	$('#form-filter').submit(function(e){
		e.preventDefault();
		getData();
		$('#form-edit').attr('action', base_url + 'report/history_marketing_activity/update/'+$('#fbulan').val()+'/'+$('#ftahun').val());
	});

	function getData(){
		var form = $('#form-filter');
		$.ajax({
			url : base_url + 'report/history_marketing_activity/data',
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
			url: '<?=base_url()?>'+'report/history_marketing_activity/get_data/',
			method: 'post',
			data: {id: id},
			success: function(r){

				let res_edit = r

				var data_marketing = res_edit.data_actual
				var marketing = res_edit.marketing
				var value_marketing = res_edit.value_marketing
				var persepsi = res_edit.persepsi
				var sub_marketing = res_edit.sub_marketing
				var persepsi_sebelum = [];
				var persepsi_setelah = [];

				$.each(persepsi, function(i, v){
					if(v.tipe == 'Sebelum'){
						persepsi_sebelum.push(v);
					} else {
						persepsi_setelah.push(v);
					}
				})

				$('#id').val(data_marketing.id)
				$('#dokter').val(data_marketing.nama_dokter)
				$('#spesialist').val(data_marketing.nama_spesialist)
				$('#sub_spesialist').val(data_marketing.nama_sub_spesialist)
				$('#outlet').val((data_marketing.nama_outlet ? data_marketing.nama_outlet : 'Reguler'))
				$('#tahun').val('<?=strftime('%Y', strtotime(date(get('tahun').'-m-d')))?>')
				$('#bulan').val('<?=strftime('%B', strtotime(date('Y-'.get('bulan').'-d')))?>')
				$('#produk_group').val(data_marketing.nama_produk_grup)

				var html_marketing_head = '<th>No.</th><th>Nama</th><th>TYPE</th><th>SUB MARKETING</th><th>TANGGAL ACARA</th><th>SEBAGAI</th><th>NAMA PEMBICARA</th>'
				var html_marketing = ''
				$.each(marketing, function(i, v){

					var curret_sub = [];
					$.each(sub_marketing, function(is,vs){
						if(v.id == vs.marketing_aktifitas){
							curret_sub.push(vs);
						}
					})

					var value_tipe = '';
					var value_sub = '';
					var value_tanggal = '';
					var value_persepsi_sebelum = '';
					var value_persepsi_setelah = '';
					var value_sebagai = '';
					var value_nama_pembicara = '';

					$.each(value_marketing, function(iv, vv){
						if(vv.marketing_aktifitas == v.id){
							value_tipe = vv.tipe
							value_sub = vv.sub_marketing_aktifitas
							value_tanggal = vv.tanggal
							value_persepsi_sebelum = vv.persepsi_sebelum
							value_persepsi_setelah = vv.persepsi_setelah
							value_sebagai = vv.sebagai
							value_nama_pembicara = vv.nama_pembicara
						}
					})
					var html_sub_marketing = '<option value=""> -- </option>';
					$.each(curret_sub, function(ic,vc){
						html_sub_marketing += '<option value="'+vc.id+'" '+(value_sub == vc.id ? 'selected="selected"' : '')+'>'+vc.nama+'</option>';
					})
					
					var html_sebagai = '<option value=""> -- </option>'+
										'<option value="Pembicara" '+(value_sebagai == "Pembicara" ? 'selected="selected"' : '')+'>Pembicara</option>'+
										'<option value="Peserta" '+(value_sebagai == "Peserta" ? 'selected="selected"' : '')+'>Peserta</option>';

					html_marketing += '<tr>'+
						'<td>'+(i+1)+'</td><td>'+v.nama+'</td>'+
						'<td><select class="form-control" name="type|'+v.id+'"><option value=""> -- </option><option value="Online" '+(value_tipe == 'Online' ? 'selected="selected"' : '')+'>ONLINE</option><option value="Offline" '+(value_tipe == 'Offline' ? 'selected="selected"' : '')+'>OFFLINE</option></select></td>'+
						'<td><select class="form-control" name="sub|'+v.id+'">'+html_sub_marketing+'<select></td><td>'+
						'<input type="date" class="form-control" name="tanggal|'+v.id+'" value="'+value_tanggal+'"></td>'+
						'<td><select class="form-control" name="sebagai|'+v.id+'">'+html_sebagai+'<select></td>'+
						'<td><input type="text" class="form-control" name="nama_pembicara|'+v.id+'" value="'+value_nama_pembicara+'"></td>'+
					'</tr>'
				})
				
				$('#indikasi_body').html(html_marketing)
				$('#indikasi_box').html(html_marketing_head)
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
			url: base_url+'report/history_marketing_activity/get_am?team='+team,
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
			url: base_url+'report/history_marketing_activity/get_mr?team='+team+'&am='+am,
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
		location.href = base_url + 'report/history_marketing_activity/export?mr='+mr+'&tahun='+tahun+'&produk_group='+produk_group+'&bulan='+bulan

		$(this).html('<i class="fa fa-download mr-2"></i> Export')
		$(this).removeAttr('disabled', false)
	})

</script>