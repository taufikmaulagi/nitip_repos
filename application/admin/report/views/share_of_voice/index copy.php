<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<select class="select2" style="width:200px" id="fproduk" onchange="filter()">
			<option value=""> Pilih Produk Group </option>
			<?php 
				if(user('id_group') == 1){
					$produk = get_data('produk_grup','is_active',1)->result_array();
				} else {
					$produk = $this->session->userdata('produk_group');
				}
				foreach($produk as $val){
					echo '<option value="'.$val['kode'].'" '.(get('produk') == $val['kode'] ? 'selected="selected"' : '').'>'.$val['nama'].'</option>';
				} ?>
			?>
			</select>
			<select class="select2 form-control" id="fbulan" name="fbulan" onchange="filter()">
				<option value="">Pilih Bulan</option>
				<option value="01" <?=(get('bulan') == '01' ? 'selected="selected"' : (date('m') == '01' && get('bulan') == '' ?  'selected="selected"' : ''))?>>Januari</option>
				<option value="02" <?=(get('bulan') == '02' ? 'selected="selected"' : (date('m') == '02' && get('bulan') == '' ?  'selected="selected"' : ''))?>>Februari</option>
				<option value="03" <?=(get('bulan') == '03' ? 'selected="selected"' : (date('m') == '03' && get('bulan') == '' ?  'selected="selected"' : ''))?>>Maret</option>
				<option value="04" <?=(get('bulan') == '04' ? 'selected="selected"' : (date('m') == '04' && get('bulan') == '' ?  'selected="selected"' : ''))?>>April</option>
				<option value="05" <?=(get('bulan') == '05' ? 'selected="selected"' : (date('m') == '05' && get('bulan') == '' ?  'selected="selected"' : ''))?>>Mei</option>
				<option value="06" <?=(get('bulan') == '06' ? 'selected="selected"' : (date('m') == '06' && get('bulan') == '' ?  'selected="selected"' : ''))?>>Juni</option>
				<option value="07" <?=(get('bulan') == '07' ? 'selected="selected"' : (date('m') == '07' && get('bulan') == '' ?  'selected="selected"' : ''))?>>Juli</option>
				<option value="08" <?=(get('bulan') == '08' ? 'selected="selected"' : (date('m') == '08' && get('bulan') == '' ?  'selected="selected"' : ''))?>>Agustus</option>
				<option value="09" <?=(get('bulan') == '09' ? 'selected="selected"' : (date('m') == '09' && get('bulan') == '' ?  'selected="selected"' : ''))?>>September</option>
				<option value="10" <?=(get('bulan') == '10' ? 'selected="selected"' : (date('m') == '10' && get('bulan') == '' ?  'selected="selected"' : ''))?>>Oktober</option>
				<option value="11" <?=(get('bulan') == '11' ? 'selected="selected"' : (date('m') == '11' && get('bulan') == '' ?  'selected="selected"' : ''))?>>November</option>
				<option value="12" <?=(get('bulan') == '12' ? 'selected="selected"' : (date('m') == '12' && get('bulan') == '' ?  'selected="selected"' : ''))?>>Desember</option>
			</select>
			<select class="select2 form-control" name="ftahun" id="ftahun" onchange="filter()">
				<option value="">Pilih Tahun</option>
				<?php for($i = date('Y'); $i >= 2018; $i--) { ?>
				<option value="<?php echo $i; ?>"<?php 
					if(get('tahun') == $i) {
						echo 'selected="selected"';
					} else if(get('tahun') == '' && $i == date('Y')){
						echo 'selected="selected"';
					}
				?>><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="main-container p-0">
	<?php
		if(!isset($sov) || empty($sov)){
			echo '<h1 class="p-5"> Data belum tersedia. </h1>';
		} else {
	?>
		<table class="table table-app">
			<thead>
				<th> No. </th>
				<th> Nama Dokter </th>
				<th> Spesialist </th>
				<th> Nama MR </th>
				<th> P1 </th>
				<th> P2 </th>
				<th> P3 </th>
			</thead>
			<?php
				$arrIndex = 1;
				foreach($sov as $v): ?>
				<tr>
					<td><?=$arrIndex++?></td>
					<td><?=$v['nama_dokter']?></td>
					<td><?=$v['nama_spesialist']?></td>
					<td><?=$v['nama_mr']?></td>
					<td><?=$v['nama_p1']?></td>
					<td><?=$v['nama_p2']?></td>
					<td><?=$v['nama_p3']?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php
		}
	?>
	</div>
</div>
<script>
	function filter(){
		let produk_grup = $('#fproduk').val()
		let tahun = $('#ftahun').val()
		let bulan = $('#fbulan').val()
		if(produk_grup != '' && tahun != '' && bulan != ''){
			location.replace(base_url + 'report/share_of_voice?produk='+produk_grup+'&tahun='+tahun+'&bulan='+bulan)
		}
	}
</script>
