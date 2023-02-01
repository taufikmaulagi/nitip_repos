<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label>Produk Group</label>
			<select class="select2" id="fpgroup" style="width: 150px;" onchange="filter()">
				<label> Product Group </label>
				<option value="">Select Product Group</option>
				<?php 
					foreach ($this->session->userdata('produk_group') as $val) {
						echo '<option value="' . $val['kode'] . '" '.(get('produk_group') == $val['kode'] ? 'selected="selected"' : '').'>' . $val['nama'] . '</option>';
					}  ?>
			</select>
			<label>Bulan</label>
			<select class="select2 infinity" id="fbulan" style="width: 100px;" onchange="filter()">
				<option value="01" <?=get('bulan') == "01" ? 'selected="selected"' : ''?>>Januari</option>
				<option value="02" <?=get('bulan') == "02" ? 'selected="selected"' : ''?>>Februari</option>
				<option value="03" <?=get('bulan') == "03" ? 'selected="selected"' : ''?>>Maret</option>
				<option value="04" <?=get('bulan') == "04" ? 'selected="selected"' : ''?>>April</option>
				<option value="05" <?=get('bulan') == "05" ? 'selected="selected"' : ''?>>Mei</option>
				<option value="06" <?=get('bulan') == "06" ? 'selected="selected"' : ''?>>Juni</option>
				<option value="07" <?=get('bulan') == "07" ? 'selected="selected"' : ''?>>Juli</option>
				<option value="08" <?=get('bulan') == "08" ? 'selected="selected"' : ''?>>Agustus</option>
				<option value="09" <?=get('bulan') == "09" ? 'selected="selected"' : ''?>>September</option>
				<option value="10" <?=get('bulan') == "10" ? 'selected="selected"' : ''?>>Oktober</option>
				<option value="11" <?=get('bulan') == "11" ? 'selected="selected"' : ''?>>November</option>
				<option value="12" <?=get('bulan') == "12" ? 'selected="selected"' : ''?>>Desember</option>
			</select>
			<label>Tahun</label>
			<select class="select2 infinity" id="ftahun" style="width: 100px;" onchange="filter()">
				<?php for($i=date('Y');$i>=2018;$i--){
					echo '<option value="'.$i.'" '.(get('tahun') == $i ? 'selected="selected"' : '').'>'.$i.'</option>';
				} ?>
			</select>
			<!-- <button class="btn btn-sky"><i class="fa-plus"></i>Add New Doctor</button> -->
			<!-- <span style="height: 50px; width:1px; border:1px solid; position: auto; margin-left:3px; margin-right:5px"></span>
			<button class="btn btn-sky" id="bSubmit" onclick="popUpSubmit()"><i class="fa-paper-plane"></i>Submit New Data Actual</button> -->
			<!-- <button class="btn btn-fresh" id="bSubmit" onclick="popUpSubmit()"><i class="fa-paper-plane"></i>Submit New Data Actual</button> -->
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
		table_open('',true,base_url('transaction/data_marketing/data?produk_group='.get('produk_group').'&bulan='.get('bulan').'&tahun='.get('tahun')),'trxdact_'.get('tahun').'_'.get('bulan'));
			thead();
				tr();
					th('#','text-center','width="30" data-content="id"');
					th('Dokter','','data-content="nama_dokter"');
					th('Specialist','','data-content="nama_spesialist" data-custom="true"');
					th('Sub Specialist','','data-content="nama_sub_spesialist" data-custom="true"');
					th('Practice','','data-content="nama_outlet"');
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
<?php
modal_open('modal-edit','Edit Data Actual','modal-xl');
	modal_body();
		form_open(base_url('transaction/data_marketing/update/'.get('tahun').'/'.get('bulan')),'post','edit-form');
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
			echo '<div class="table-responsive"><table class="table table-sm table-hover table-app" style="margin-top:20px; margin-bottom:20px" id="table_indikasi">
				<thead id="indikasi_box">
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
			location.replace(base_url + 'transaction/data_marketing?produk_group='+pgroup+'&bulan='+bulan+'&tahun='+tahun);
		}
	}
	$(document).on('click','.btn-edit', function(){
		$.ajax({
			url: base_url + 'transaction/data_marketing/get_detail_dokter/'+$('#fbulan').val()+'/'+$('#ftahun').val()+'?id='+$(this).attr('data-id'),
			success: function(resp){
				console.log(resp);
				var data_marketing = resp.data_actual
				var marketing = resp.marketing
				var value_marketing = resp.value_marketing
				var persepsi = resp.persepsi
				var sub_marketing = resp.sub_marketing
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
					// var html_persepsi_sebelum = '<option value=""> -- </option>';
					// $.each(persepsi_sebelum, function(ip, vp){
					// 	html_persepsi_sebelum += '<option value="'+vp.id+'" '+(value_persepsi_sebelum == vp.id ? 'selected="selected"' : '')+'>'+vp.persepsi+'</option>';
					// })
					// var html_persepsi_setelah = '<option value=""> -- </option>';
					// $.each(persepsi_setelah, function(ip, vp){
					// 	html_persepsi_setelah += '<option value="'+vp.id+'" '+(value_persepsi_setelah == vp.id ? 'selected="selected"' : '')+'>'+vp.persepsi+'</option>';
					// })

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
						// '<td><select class="form-control" name="persepsi_setelah|'+v.id+'">'+html_persepsi_setelah+'<select></td>'+
					'</tr>'
				})
				
				$('#indikasi_body').html(html_marketing)
				$('#indikasi_box').html(html_marketing_head)
				$('#modal-edit').modal()
			}
		})
	})
</script>