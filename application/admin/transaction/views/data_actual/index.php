<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	if(get('tahun') != '' && get('bulan') != '' && get('produk_group') != ''){
		table_open('',true,base_url('transaction/data_actual/data?produk_group='.get('produk_group').'&tahun='.get('tahun').'&bulan='.get('bulan')),'trxdact_'.get('tahun').'_'.get('bulan'));
			thead();
				tr();
					th('#','text-center','width="30" data-content="id"');
					th('Dokter','','data-content="nama_dokter" data-custom="true"');
					th('Specialist','','data-content="nama_spesialist" data-custom="true"');
					th('Practice','','data-content="nama_outlet" data-custom="true"');
					th('Kri. Potensi','text-center','data-content="kriteria_potensi"');
					th('Pasien','text-center','data-content="jumlah_pasien" data-custom="true"');
					th('Status Dokter','text-center','data-content="status_dokter"');
					th('Cust. Matrix','text-center','data-content="customer_matrix"');
					th('&nbsp;','','width="30" data-content="action_button"');
		table_close();
	}
	?>
</div>
<div class="filter-panel">
	<div class="filter-header bg-primary text-white">
		<i class="fa-search mr-2"></i> Search
	</div>
	<div class="filter-body">
		<div class="form-group">
			<label>Produk Group</label>
			<select class="select2" id="fpgroup" style="width: 150px;" onchange="filter()">
				<label> Product Group </label>
				<option value="">Select Product Group</option>
				<?php 
					foreach ($this->session->userdata('produk_group') as $val) {
						echo '<option value="' . $val['kode'] . '" '.(get('produk_group') == $val['kode'] ? 'selected="selected"' : '').'>' . $val['nama'] . '</option>';
					}  ?>
			</select>
		</div>
		<div class="form-group">
			<label>Bulan</label>
			<select class="select2 infinity" id="fbulan" style="width: 100px;" onchange="filter()">
				<option value="01" <?=get('bulan') == "01" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '01' ? 'selected="selected"' : '')?>>Januari</option>
				<option value="02" <?=get('bulan') == "02" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '02' ? 'selected="selected"' : '')?>>Februari</option>
				<option value="03" <?=get('bulan') == "03" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '03' ? 'selected="selected"' : '')?>>Maret</option>
				<option value="04" <?=get('bulan') == "04" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '04' ? 'selected="selected"' : '')?>>April</option>
				<option value="05" <?=get('bulan') == "05" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '05' ? 'selected="selected"' : '')?>>Mei</option>
				<option value="06" <?=get('bulan') == "06" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '06' ? 'selected="selected"' : '')?>>Juni</option>
				<option value="07" <?=get('bulan') == "07" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '07' ? 'selected="selected"' : '')?>>Juli</option>
				<option value="08" <?=get('bulan') == "08" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '08' ? 'selected="selected"' : '')?>>Agustus</option>
				<option value="09" <?=get('bulan') == "09" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '09' ? 'selected="selected"' : '')?>>September</option>
				<option value="10" <?=get('bulan') == "10" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '10' ? 'selected="selected"' : '')?>>Oktober</option>
				<option value="11" <?=get('bulan') == "11" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '11' ? 'selected="selected"' : '')?>>November</option>
				<option value="12" <?=get('bulan') == "12" ? 'selected="selected"' : (get('bulan') == '' && date('m') == '12' ? 'selected="selected"' : '')?>>Desember</option>
			</select>
		</div>
		<div class="form-group">
		<label>Tahun</label>
			<select class="select2 infinity" id="ftahun" style="width: 100px;" onchange="filter()">
				<?php for($i=date('Y');$i>=2018;$i--){
					echo '<option value="'.$i.'" '.(get('tahun') == $i ? 'selected="selected"' : '').'>'.$i.'</option>';
				} ?>
			</select>
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
			form_button(lang('simpan'),lang('batal'));
		form_close();
	// modal_footer();
modal_close();
?>
<script>
	function filter(){
		var pgroup = $('#fpgroup').val();
		var bulan = $('#fbulan').val();
		var tahun = $('#ftahun').val();
		if(pgroup != '' && bulan != '' && tahun != ''){
			location.replace(base_url + 'transaction/data_actual?produk_group='+pgroup+'&bulan='+bulan+'&tahun='+tahun);
		}
	}

	$('#modal-form').on('shown.bs.modal', function (e) {
		let res_edit = response_edit
		
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

	})

</script>