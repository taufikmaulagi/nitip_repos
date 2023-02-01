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
	if($status == 'no_visit'){
		echo '<div style="text-align:center">
			<img src="'.base_url('assets/images/no-data.svg').'" width="40%">
			<p>&nbsp;</p>
			<h3> Belum Membuat Visit Plan. </h3>
		</div>';
	} else if(get('tahun') != '' && get('bulan') != '' && get('produk_group') != ''){
		table_open('',true,base_url('transaction/data_actual/data?produk_group='.get('produk_group')),'trxdact_'.get('tahun').'_'.get('bulan'));
			thead();
				tr();
					th('#','text-center','width="30" data-content="id"');
					th('Dokter','','data-content="nama_dokter"');
					th('Specialist','','data-content="nama_spesialist"');
					// th('Sub Specialist','','data-content="nama_subspesialist"');
					th('Practice','','data-content="nama_outlet"');
					
					if(get('produk_group') == 'EH'){
						th('Kri. Potensi Abilify','text-center','data-content="kriteria_potensi"');
						th('Pasien Abilify','text-center','data-content="jumlah_pasien"');
						th('Status Dokter Abilify','text-center','data-content="status_dokter"');
						th('Cust. Matrix Abilify','text-center','data-content="customer_matrix"');
						th('Kri. Potensi Maintena','text-center','data-content="kriteria_potensi_maintena"');
						th('Pasien Maintena','text-center','data-content="jumlah_pasien_maintena"');
						th('Status Dokter Maintena','text-center','data-content="status_dokter_maintena"');
						th('Cust. Matrix Maintena','text-center','data-content="customer_matrix_maintena"');
						th('Kri. Potensi Rexulti','text-center','data-content="kriteria_potensi_rexulti"');
						th('Status Dokter Rexulti','text-center','data-content="status_dokter_rexulti"');
						th('Pasien Rexulti','text-center','data-content="jumlah_pasien_rexulti"');
						th('Cust. Matrix Rexulti','text-center','data-content="customer_matrix_rexulti"');
					} else {
						th('Kri. Potensi','text-center','data-content="kriteria_potensi"');
						th('Pasien','text-center','data-content="jumlah_pasien"');
						th('Status Dokter','text-center','data-content="status_dokter"');
						th('Cust. Matrix','text-center','data-content="customer_matrix"');
					}
					th('Total Value','text-center','data-content="total_value" data-type="currency"');
					th('&nbsp;','','width="30" data-content="action_button"');
		table_close();
	} else {
		echo '<div style="text-align:center">
			<img src="'.base_url('assets/images/no-data.svg').'" width="40%">
			<p>&nbsp;</p>
			<h3> Belum Pilih Bulan, Tahun dan Produk Group. </h3>
		</div>';
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
modal_open('modal-edit','Edit Data Actual','modal-xl');
	modal_body();
		form_open(base_url('transaction/data_actual/update/'.get('tahun').'/'.get('bulan')),'post','edit-form');
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
				<thead id="indikasi_box" style="max-height:300px;overflow-y: auto">
				</thead>
				<tbody id="indikasi_body">
				</tbody>
			</table></div>';
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
	$(document).on('click','.btn-edit', function(){
		$.ajax({
			url: base_url + 'transaction/data_actual/get_detail_dokter/'+$('#fbulan').val()+'/'+$('#ftahun').val()+'?id='+$(this).attr('data-id'),
			success: function(resp){
				var data_actual = resp.data_actual
				var indikasi = resp.indikasi
				var produk = resp.produk
				var value_sku = resp.value_sku

				$('#id').val(data_actual.id)
				$('#dokter').val(data_actual.nama_dokter)
				$('#spesialist').val(data_actual.nama_spesialist)
				$('#sub_spesialist').val(data_actual.nama_sub_spesialist)
				$('#outlet').val((data_actual.nama_outlet ? data_actual.nama_outlet : 'Reguler'))
				$('#tahun').val('<?=strftime('%Y', strtotime(get('tahun')))?>')
				$('#bulan').val('<?=strftime('%B', strtotime(get('bulan')))?>')
				$('#produk_group').val(data_actual.nama_produk_grup)
				$('#total_alai').val(data_actual.total_alai)
				$('#total_tlai').val(data_actual.total_tlai)
				$('#other_ap_original').val(data_actual.other_ap_original)

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
					var number_of_unit = 0
					var price = 0
					$.each(value_sku, function(j,w){
						if(v.id == w.produk){
							val_indikasi_1 = w.value_1
							val_indikasi_2 = w.value_2
							val_indikasi_3 = w.value_3
							val_indikasi_4 = w.value_4
							val_indikasi_5 = w.value_5
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
						}
						html_sku += '<input type="number" name="units[]" class="form-control form-control-sm" style="width:75px" value="'+number_of_unit+'"></td><td><input type="number" name="value_'+(k+1)+'[]" class="form-control form-control-sm" style="width:75px" value="'+tmp_val+'"></td>'
					})
					html_sku += '<td> Rp.'+numberFormat(price * number_of_unit)+',-</td></tr>';
				})
				
				$('#indikasi_body').html(html_sku)
				$('#indikasi_box').html(html_indikasi_head)
				$('#modal-edit').modal()
			}
		})
	})
</script>