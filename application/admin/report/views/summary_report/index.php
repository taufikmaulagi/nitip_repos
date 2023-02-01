<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label> Produk Group </label>
			<select class="select2" id="fpgroup" onchange="filter()">
				<option value="">-- Pilih Produk Group --</option>
				<?php foreach($this->session->userdata('produk_group') as $val) { ?>
				<option value="<?php echo $val['kode']; ?>" <?=get('pgroup') == $val['kode'] ? 'selected="selected"' : ''?>><?php echo $val['nama']; ?></option>
				<?php } ?>
			</select>
			<label>Tahun</label>
			<select class="select2 infinity" id="ftahun" style="width:100px" onchange="filter()">
			<option value="">-- Pilih Tahun --</option>
			<?php
				$tahun = date('Y');
				for($i=0; $i<=3; $i++) {
					$tahun_ = $tahun - $i;
					$selected = get('tahun') == $tahun_ ? 'selected="selected"' : '';
					echo '<option value="'.$tahun_.'" '.$selected.'>'.$tahun_.'</option>';
				}
			?>
			</select>
			<label>Bulan</label>
			<select class="select2 infinity" id="fbulan" style="width:100px" onchange="filter()">
				<option value="">-- Pilih Bulan --</option>
				<option <?=get('bulan') == '01' ? 'selected="selected"' : ''?> value="01">Januari</option>
				<option <?=get('bulan') == '02' ? 'selected="selected"' : ''?> value="02">Februari</option>
				<option <?=get('bulan') == '03' ? 'selected="selected"' : ''?> value="03">Maret</option>
				<option <?=get('bulan') == '04' ? 'selected="selected"' : ''?> value="04">April</option>
				<option <?=get('bulan') == '05' ? 'selected="selected"' : ''?> value="05">Mei</option>
				<option <?=get('bulan') == '06' ? 'selected="selected"' : ''?> value="06">Juni</option>
				<option <?=get('bulan') == '07' ? 'selected="selected"' : ''?> value="07">Juli</option>
				<option <?=get('bulan') == '08' ? 'selected="selected"' : ''?> value="08">Agustus</option>
				<option <?=get('bulan') == '09' ? 'selected="selected"' : ''?> value="09">September</option>
				<option <?=get('bulan') == '10' ? 'selected="selected"' : ''?> value="10">Oktober</option>
				<option <?=get('bulan') == '11' ? 'selected="selected"' : ''?> value="11">November</option>
				<option <?=get('bulan') == '12' ? 'selected="selected"' : ''?> value="12">Desember</option>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
		$produk_grup = get('pgroup');
		$bulan = get('bulan');
		$tahun = get('tahun');
		if($produk_grup != '' && $bulan != '' && $tahun != ''):
			$indikasi = get_data('indikasi', [
				'where' => [
					'produk_grup' => $produk_grup,
					'is_active' => 1
				]
			])->result_array();
			$sub_type_a = get_data('sub_call_type', [
				'where' => [
					'call_type' => 1,
					'is_active' => 1
				]
			])->result_array();
			$sub_type_b = get_data('sub_call_type', [
				'where' => [
					'call_type' => 2,
					'is_active' => 1
				]
			])->result_array();
			$sub_type_c = get_data('sub_call_type', [
				'where' => [
					'call_type' => 3,
					'is_active' => 1
				]
			])->result_array();
			$produk = get_data('produk', [
				'select' => 'produk.*',
				'join' => [
					'produk_subgrup on produk_subgrup.kode = produk.kode_subgrup'
				],
				'where' => [
					'produk_subgrup.kode_grup' => $produk_grup,
					'produk.is_active' => 1
				]
			])->result_array();

			
	?>
	<table class="table table-app table-bordered table-hover">
		<thead>
			<tr>
				<th rowspan="2">No.</th>
				<th rowspan="2">Dokter</th>
				<th rowspan="2">Spesialist</th>
				<th rowspan="2">Practice</th>
				<th class="text-center" colspan="<?=count($indikasi)+1?>">Potensi</th>
				<th class="text-center" colspan="<?=count($indikasi)+1?>">Pasien</th>
				<?php if($produk_grup == 'EH'): ?>
					<th class="text-center" colspan="3">Pasien Maintena</th>
					<th class="text-center" colspan="2">Pasien Rexulti</th>
				<?php endif; ?>
				<th class="text-center" colspan="3">Total Call</th>
				<th class="text-center" colspan="3">Doctor Coverage</th>
				<th class="text-center" colspan="3">Percent Converage</th>
				<th rowspan="2">Use - Confirm <?=$produk_grup == 'EH' ? 'Abilify' : ''?></th>
				<?php if($produk_grup == 'EH'): ?>
					<th rowspan="2">Use - Confirm - Maintena</th>
					<th rowspan="2">Use - Confirm - Rexulti</th>
				<?php endif; ?>
				<th rowspan="2">Customer Matrix <?=$produk_grup == 'EH' ? 'Abilify' : ''?></th>
				<?php if($produk_grup == 'EH'): ?>
					<th rowspan="2">Customer Matrix - Maintena</th>
					<th rowspan="2">Customer Matrix - Rexulti</th>
				<?php endif; ?>
				<th class="text-center" colspan="3">Call Type</th>
				<th class="text-center" colspan="<?=count($sub_type_a)?>">Call Type A</th>
				<th class="text-center" colspan="<?=count($sub_type_b)?>">Call Type B</th>
				<th class="text-center" colspan="<?=count($sub_type_c)?>">Call Type C</th>
				<th class="text-center" colspan="<?=count($produk)?>">Sales In Unit SKU</th>
				<th rowspan="2">Total Sales <?=$produk_grup == 'EH' ? 'Abilify' : ''?></th>
				<?php if($produk_grup == 'EH'):  ?>
					<th rowspan="2">Total Sales Maintena</th>
					<th rowspan="2">Total Sales Rexulti</th>
				<?php endif; ?>
			</tr>
			<tr>
				<?php
					foreach($indikasi as $val){
						echo '<th>'.$val['nama'].'</th>';
					}
					echo '<th>Total</th>';
				?>
				<?php
					foreach($indikasi as $val){
						echo '<th>'.$val['nama'].'</th>';
					}
					echo '<th>Total</th>';
				?>
				<?php
					if($produk_grup == 'EH'){
						foreach($indikasi as $key => $val){
							if($key > 1){
								continue;
							}
							echo '<th>'.$val['nama'].'</th>';
						}

						echo '<th>Total</th>';
						foreach($indikasi as $key => $val){
							if($key > 0){
								continue;
							}
							echo '<th>'.$val['nama'].'</th>';
						}
						echo '<th>Total</th>';
					}
				?>
				<th>Plan</th>
				<th>Actual</th>
				<th>%</th>
				<th>Plan</th>
				<th>Actual</th>
				<th>%</th>
				<th>Plan</th>
				<th>Actual</th>
				<th>%</th>
				<th>A</th>
				<th>B</th>
				<th>C</th>
				<?php
					foreach($sub_type_a as $val){
						echo '<th>'.$val['nama'].'</th>';
					}
				?>
				<?php
					foreach($sub_type_b as $val){
						echo '<th>'.$val['nama'].'</th>';
					}
				?>
				<?php
					foreach($sub_type_c as $val){
						echo '<th>'.$val['nama'].'</th>';
					}
				?>
				<?php
					foreach($produk as $val){
						echo '<th>'.$val['nama'].'</th>';
					}
				?>
			</tr>
		</thead>
		<tbody>
		<?php
			if(in_array($bulan, ['01','02','03','04'])){
				$cycle = 1;
			} else if(in_array($bulan, ['05','06','07','08'])){
				$cycle = 2;
			} else {
				$cycle = 3;
			}
			// $profiling = get_data('trxprof_'.$tahun.'_'.$cycle, [
				
			// ])->result_array()
			$pg_detail = get_data('produk_grup', 'kode', $produk_grup)->row_array();
			$mr = get_data('history_organogram_detail', [
				'select' => 'n_mr, nama_mr',
				'join' => [
					'history_organogram on history_organogram.id = history_organogram_detail.id_history_organogram',
				], 
				'where' => [
					'history_organogram.tanggal_end' => '0000-00-00',
					'history_organogram.divisi' => 'TMBG',
					'n_mr !=' => '',
					'history_organogram.kode_team' => $pg_detail['kode_team']
				],
				'group_by' => 'n_mr',
				'sort_by' => 'nama_mr',
				'sort' => 'ASC'
			])->result_array();
			
			foreach($mr as $val){
				$no = 1;
				echo '<tr><td colspan="99" style="background-color:black; color:white">'.$val['nama_mr'].'</td></tr>';
				$data = get_data('raw_data_'.$produk_grup.'_'.$tahun.'_'.$bulan, [
					'where' => [
						'mr' => $val['n_mr']
					]
				])->result_array();
				foreach($data as $dkey => $dval){
					$total = 0;
					echo '<tr>';
					echo '<td>'.$no++.'</td>';
					echo '<td style="white-space:nowrap">'.$dval['nama_dokter'].'</td>';
					echo '<td style="white-space:nowrap">'.$dval['nama_spesialist'].'</td>';
					echo '<td style="white-space:nowrap">'.($dval['nama_outlet'] ? $dval['nama_outlet'] : 'Regular').'</td>';
					for($i=1;$i<=count($indikasi);$i++){
						echo '<td class="text-center">'.$dval['indikasi_'.$i].'</td>';
					}
					echo '<td class="text-center">'.$dval['total_potensi'].'</td>';
					for($i=1;$i<=count($indikasi);$i++){
						
						$tmpval = $dval['pasien_'.$i] ? $dval['pasien_'.$i] : 0;
						echo '<td class="text-center">'.$tmpval.'</td>';
					}
					echo '<td class="text-center">'.$dval['total_pasien'].'</td>';
					if($produk_grup == 'EH'){
						$total_pas_maintena = 0;
						$total_pas_rexulti = 0;
						for($i=1;$i<=count($indikasi);$i++){	
							if($i >= 3) break;
							$tmpval = $dval['pasien_maintena_'.$i] ? $dval['pasien_maintena_'.$i] : 0;
							$total_pas_maintena += $tmpval;
							echo '<td class="text-center">'.$tmpval.'</td>';
						}
						echo '<td class="text-center">'.$total_pas_maintena.'</td>';
						for($i=1;$i<=count($indikasi);$i++){	
							if($i >= 2) break;
							$tmpval = $dval['pasien_rexulti_'.$i] ? $dval['pasien_rexulti_'.$i] : 0;
							$total_pas_rexulti += $tmpval;
							echo '<td class="text-center">'.$tmpval.'</td>';
						}
						echo '<td class="text-center">'.$total_pas_rexulti.'</td>';
					}
					echo '<td class="text-center">'.$dval['plan_call'].'</td>';
					echo '<td class="text-center">'.$dval['actual_call'].'</td>';
					echo '<td class="text-center" style="white-space:nowrap">'.$dval['percent_call'].' %</td>';
					echo '<td class="text-center">'.$dval['plan_dokter_coverage'].'</td>';
					echo '<td class="text-center">'.$dval['actual_dokter_coverage'].'</td>';
					echo '<td class="text-center" style="white-space:nowrap">'.$dval['percent_dokter_coverage'].' %</td>';
					echo '<td class="text-center">'.$dval['plan_percent_coverage'].'</td>';
					echo '<td class="text-center">'.$dval['actual_percent_coverage'].'</td>';
					echo '<td class="text-center" style="white-space:nowrap">'.$dval['percent_percent_coverage'].' %</td>';
					echo '<td class="text-center">'.$dval['use_confirm'].'</td>';
					if($produk_grup == 'EH'){
						echo '<td class="text-center">'.$dval['use_confirm_maintena'].'</td>';
						echo '<td class="text-center">'.$dval['use_confirm_rexulti'].'</td>';
					}
					echo '<td class="text-center">'.$dval['customer_matrix'].'</td>';
					if($produk_grup == 'EH'){
						echo '<td class="text-center">'.$dval['customer_matrix_maintena'].'</td>';
						echo '<td class="text-center">'.$dval['customer_matrix_rexulti'].'</td>';
					}
					echo '<td class="text-center">'.$dval['call_type_a'].'</td>';
					echo '<td class="text-center">'.$dval['call_type_b'].'</td>';
					echo '<td class="text-center">'.$dval['call_type_c'].'</td>';
					$call_a = json_decode($dval['sub_call_type_a']);
					$call_b = json_decode($dval['sub_call_type_b']);
					$call_c = json_decode($dval['sub_call_type_c']);
					
					foreach($sub_type_a as $sval){
						$c_type = 0;
						for($i=0;$i<count($call_a);$i++){
							if($call_a[$i] == $sval['id']){
								$c_type++;
							}
						}
						echo '<td class="text-center">'.$c_type.'</td>';
					}
					
					foreach($sub_type_b as $sval){
						$c_type = 0;
						for($i=0;$i<count($call_b);$i++){
							if($call_b[$i] == $sval['id']){
								$c_type++;
							}
						}
						echo '<td class="text-center">'.$c_type.'</td>';
					}
					
					foreach($sub_type_c as $sval){
						$c_type = 0;
						for($i=0;$i<count($call_c);$i++){
							if($call_c[$i] == $sval['id']){
								$c_type++;
							}
						}
						echo '<td class="text-center">'.$c_type.'</td>';
					}

					foreach($produk as $rval){
						// echo $rval['id'].'_produk ||';
						$total_maintena = 0;
						$total_rexulti = 0;
						if($produk_grup == 'EH'){
							if(!in_array($rval['id'], ['137','138','145','146','147','148'])){
								if(!empty($dval[$rval['id'].'_produk'])){
									$total += intval($dval[$rval['id'].'_produk']) * intval($dval[$rval['id'].'_price']);
									echo '<td class="text-center">'.$dval[$rval['id'].'_produk'].'</td>';
								} else {
									echo '<td class="text-center"> 0 </td>';
								}
							} else {
								if(in_array($rval['id'], ['137','138'])){
									if(!empty($dval[$rval['id'].'_produk'])){
										$total_maintena += intval($dval[$rval['id'].'_produk']) * intval($dval[$rval['id'].'_price']);
										echo '<td class="text-center">'.$dval[$rval['id'].'_produk'].'</td>';
									} else {
										echo '<td class="text-center"> 0 </td>';
									}
								} else {
									if(!empty($dval[$rval['id'].'_produk'])){
										$total_rexulti += intval($dval[$rval['id'].'_produk']) * intval($dval[$rval['id'].'_price']);
										echo '<td class="text-center">'.$dval[$rval['id'].'_produk'].'</td>';
									} else {
										echo '<td class="text-center"> 0 </td>';
									}
								}
							}
						} else {
							if(!empty($dval[$rval['id'].'_produk'])){
								$total += intval($dval[$rval['id'].'_produk']) * intval($dval[$rval['id'].'_price']);
								echo '<td class="text-center">'.$dval[$rval['id'].'_produk'].'</td>';
							} else {
								echo '<td class="text-center"> 0 </td>';
							}
						}
					}
					echo '<td class="text-center">'.number_format($total).'</td>';
					if($produk_grup == 'EH'){
						echo '<td class="text-center">'.$total_maintena.'</td>';
						echo '<td class="text-center">'.$total_rexulti.'</td>';
					}
					echo '</tr>';
				}
			}
		?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/call_type/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('tipe'),'tipe','required|max-length:10');
			textarea(lang('keterangan'),'keterangan','max-length:150');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/call_type/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script>

function filter(){
	var pgroup = $('#fpgroup').val();
	var bulan = $('#fbulan').val();
	var tahun = $('#ftahun').val();

	if(pgroup != '' && bulan != '' && tahun != ''){
		location.replace(base_url + 'report/summary_report?pgroup='+pgroup+'&bulan='+bulan+'&tahun='+tahun);
	}
}
				
</script>
