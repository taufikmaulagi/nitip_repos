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
			<label> Spesialist </label>
			<select class="select2" id="fspesialist" style="width:100px" onchange="filter()">
				<option value=""> Internist </option>
				<option value=""> Nefrologist </option>
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

			$no = 1;
	?>
	<table class="table table-app table-bordered table-hover">
		<thead>
			<tr>
				<th rowspan="2">No.</th>
				<th rowspan="2">Dokter</th>
				<th rowspan="2">MR</th>
				<th rowspan="2">Spesialist</th>
				<th rowspan="2">Practice</th>
				<th class="text-center" colspan="<?=count($indikasi)+1?>">Potensi</th>
				<th class="text-center" colspan="<?=count($indikasi)+1?>">Kriteria Potensi</th>
				<th class="text-center" colspan="<?=count($indikasi)+1?>">Matrix</th>
				<th class="text-center" colspan="<?=(count($indikasi)+1)*2?>">Pasien</th>
				<th class="text-center" colspan="<?=(count($produk)+1)?>">Unit</th>
				<th class="text-center" colspan="<?=(count($produk)+1)?>">Value</th>
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
					foreach($indikasi as $val){
						echo '<th>'.$val['nama'].'</th>';
					}
					echo '<th>Total</th>';
				?>
				<?php
					foreach($indikasi as $val){
						echo '<th>'.$val['nama'].'</th>';
						echo '<th></th>';
					}
					echo '<th>Total</th>';
					echo '<th></th>';
				?>
				<?php
					foreach($produk as $val){
						echo '<th>'.$val['nama'].'</th>';
					}
					echo '<th>Total</th>';
				?>
				<?php
					foreach($produk as $val){
						echo '<th>'.$val['nama'].'</th>';
					}
					echo '<th>Total</th>';
				?>
			</tr>
		</thead>
		<tbody> 
		<?php
		 	$indikasi_query = '';
			foreach($indikasi as $v){
				
			}
			if(in_array(get('bulan'), ['01','02','03','04'])){
				$cycle = 1;
			} else if(in_array(get('bulan'), ['05','06','07','08'])){
				$cycle = 2;
			} else if(in_array(get('bulan'), ['09','10','11','12'])){
				$cycle = 3;
			}
			$matrix = get_data('trxdact_'.get('tahun').'_'.get('bulan').' a',[
				'select' => 'a.id, a.dokter, a.nama_dokter, group_concat(mr.nama,"<br/>") as nama_mr, group_concat(a.nama_outlet,"<br/>") as nama_outlet, group_concat(a.nama_spesialist,"<br/>") as nama_spesialist, sum(d.val_indikasi_1) as potensi_1, sum(d.val_indikasi_2) as potensi_2, sum(d.val_indikasi_3) as potensi_3, sum(d.val_indikasi_4) as potensi_4, sum(d.val_indikasi_5) as potensi_5, sum(d.val_indikasi_6) as potensi_6, sum(d.val_indikasi_7) as potensi_7, sum(d.val_indikasi_8) as potensi_8, sum(d.val_indikasi_9) as potensi_9, sum(d.val_indikasi_10) as potensi_10',
				'join' => [
					'tbl_user mr on mr.username = a.mr',
					'trxvisit_'.get('tahun').'_'.get('bulan').' b on b.id = a.visit_plan',
					'trxprof_'.get('tahun').'_'.$cycle.' c on c.id = b.profiling',
					'trxprof_indikasi_'.get('tahun').'_'.$cycle.' d on d.profiling = c.id and d.bulan = '.get('bulan'),
				],
				'where' => [
					'a.produk_grup' => get('pgroup'),
				],
				'group_by' => 'a.nama_dokter',
				'sort_by' => 'mr.nama',
				'sort' => 'ASC'
			])->result_array();
			$no = 1;
			foreach($matrix as $v){
				echo '<tr>';
				echo '<td>'.$no++.'</td>';
				echo '<td style="width:1px;white-space:nowrap">'.$v['nama_dokter'].'</td>';
				echo '<td style="width:1px;white-space:nowrap">'.$v['nama_mr'].'</td>';
				echo '<td style="width:1px;white-space:nowrap">'.$v['nama_spesialist'].'</td>';
				echo '<td style="width:1px;white-space:nowrap">'.$v['nama_outlet'].'</td>';
				$ttl_potensi = 0;
				for($i=1;$i<=count($indikasi);$i++){
					echo '<td class="text-center">'.$v['potensi_'.$i].'</td>';
					$ttl_potensi += $v['potensi_'.$i];
				}
				echo '<td class="text-center">'.$ttl_potensi.'</td>';	
				$ttl_kp = 2;
				$tmp_kp = [];
				for($i=1;$i<=count($indikasi);$i++){
					$kp = get_kriteria_potensi(get('pgroup'), $v['potensi_'.$i]);
					array_push($tmp_kp, $kp);
					echo '<td class="text-center">'.$kp.'</td>';
					if($kp == 1){
						$ttl_kp = 1;
					}
				}
				echo '<td class="text-center">'.$ttl_kp.'</td>';
				// $this->db->having('total >= a.min_pasien and total <= a.max_pasien');
				$pasien = get_data('trxdact_sku_'.get('tahun').'_'.get('bulan').' a', [
					'select' => 'sum(a.value_1) as value_1, sum(a.value_2) as value_2, sum(a.value_3) as value_3, sum(a.value_4) as value_4, sum(a.value_5) as value_5, sum(a.value_6) as value_6, sum(a.value_7) as value_7, sum(a.value_8) as value_8, sum(a.value_9) as value_9, sum(a.value_10) as value_10',
					'join' => [
						'trxdact_'.get('tahun').'_'.get('bulan').' b on a.data_sales = b.id'
					],
					'where' => [
						'b.dokter' => $v['dokter']
					]
				])->row_array();

				$uv = get_data('trxdact_sku_'.get('tahun').'_'.get('bulan').' a', [
					'select' => 'sum(a.number_of_unit) as unit, a.produk',
					'join' => [
						'trxdact_'.get('tahun').'_'.get('bulan').' b on a.data_sales = b.id'
					],
					'where' => [
						'b.dokter' => $v['dokter']
					],
					'group_by' => 'produk'
				])->result_array();

				for($i=1;$i<=count($indikasi);$i++){
					echo '<td class="text-center">'.get_customer_matrix(
						get('pgroup'), 
						get_status_dokter(get('pgroup'), $pasien['value_'.$i]), 
						$tmp_kp[$i-1]
					).'</td>';
				}

				echo '<td class="text-center">'.get_customer_matrix(
					get('pgroup'), 
					get_status_dokter(get('pgroup'), $pasien['value_1'] + $pasien['value_2'] + $pasien['value_3'] + $pasien['value_4'] + $pasien['value_5'] + $pasien['value_6'] + $pasien['value_7'] + $pasien['value_8'] + $pasien['value_9'] + $pasien['value_10']),
					$ttl_kp
				).'</td>';

				$ttl_pasien = 0;
				for($i=1;$i<=count($indikasi);$i++){
					echo '<td class="text-center">'.($pasien['value_'.$i] ? $pasien['value_'.$i] : 0).'</td>';
					echo '<td class="text-center">'.get_status_dokter(get('pgroup'), $pasien['value_'.$i]).'</td>';
					$ttl_pasien += $pasien['value_'.$i];
				}
				echo '<td class="text-center">'.$ttl_pasien.'</td>';
				echo '<td class="text-center">'.get_status_dokter(get('pgroup'), $ttl_pasien).'</td>';

				$total = 0;
				for($i=0;$i<count($produk);$i++){
					$val = 0;
					foreach($uv as $uav){
						if($uav['produk'] == $produk[$i]['id']){
							$val = $uav['unit'];
						}
					}
					$total += intval($val);
					echo '<td class="text-center">'.$val.'</td>';
				}
				echo '<td class="text-center">'.$total.'</td>';

				$total = 0;
				for($i=0;$i<count($produk);$i++){
					$val = 0;
					foreach($uv as $uav){
						if($uav['produk'] == $produk[$i]['id']){
							$price = get_price_data('MUP','001',$uav['produk']);
							if($price){
								$price = $price->hjp;
							} else {
								$price = 0;
							}
							
							$val = $uav['unit'] * $price;
							// $val = 0;
						}
					}
					$total += intval($val);
					echo '<td class="text-center">'.$val.'</td>';
				}
				echo '<td class="text-center">'.$total.'</td>';
				
				echo '</tr>';
			}

			// if(in_array($bulan, ['01','02','03','04'])){
			// 	$cycle = 1;
			// } else if(in_array($bulan, ['05','06','07','08'])){
			// 	$cycle = 2;
			// } else {
			// 	$cycle = 3;
			// }
			// // $profiling = get_data('trxprof_'.$tahun.'_'.$cycle, [
				
			// // ])->result_array()
			// // $pg_detail = get_data('produk_grup', 'kode', $produk_grup)->row_array();
			// // $mr = get_data('history_organogram_detail', [
			// // 	'select' => 'n_mr, nama_mr',
			// // 	'join' => [
			// // 		'history_organogram on history_organogram.id = history_organogram_detail.id_history_organogram',
			// // 	], 
			// // 	'where' => [
			// // 		'history_organogram.tanggal_end' => '0000-00-00',
			// // 		'history_organogram.divisi' => 'TMBG',
			// // 		'n_mr !=' => '',
			// // 		'history_organogram.kode_team' => $pg_detail['kode_team']
			// // 	],
			// // 	'group_by' => 'n_mr',
			// // 	'sort_by' => 'nama_mr',
			// // 	'sort' => 'ASC'
			// // ])->result_array();
			
			// // foreach($mr as $val){
			// // 	echo '<tr><td colspan="99" style="background-color:black; color:white">'.$val['nama_mr'].'</td></tr>';
			// 	$q_produk = '';
			// 	foreach($produk as $k => $v){
			// 		$q_produk = 'sum('.$v['id'].'_produk) as total_produk_'.$v['kode'];
			// 		if($k != count($produk)-1){
			// 			$q_produk .= ',';
			// 		}
			// 	}
			// 	// $this->db->having('sum(a.total_potensi) between b.min_pasien and b.max_pasien and sum(a.total_pasien) between c.min_pasien and c.max_pasien and b.potensi = d.potensi and c.status = d.status_dokter');
			// 	$data = get_data('raw_data_'.$produk_grup.'_'.$tahun.'_'.$bulan.' a', [
			// 		'select' => 'a.*, group_concat(nama_mr,"<br/>") as all_mr, 
			// 		group_concat(nama_outlet,"<br/>") as all_outlet,
			// 		group_concat(nama_spesialist,"<br/>") as all_spesialist,sum(a.indikasi_1) as total_indikasi_1,
			// 		sum(a.indikasi_2) as total_indikasi_2,
			// 		sum(a.indikasi_3) as total_indikasi_3,
			// 		sum(a.indikasi_4) as total_indikasi_4,
			// 		sum(a.indikasi_5) as total_indikasi_5,
			// 		sum(a.total_potensi) as grand_total_indikasi,
			// 		sum(a.pasien_1) as total_pasien_1,
			// 		sum(a.pasien_2) as total_pasien_2,
			// 		sum(a.pasien_3) as total_pasien_3,
			// 		sum(a.pasien_4) as total_pasien_4,
			// 		sum(a.pasien_5) as total_pasien_5,
			// 		sum(a.total_pasien) as grand_total_pasien,
			// 		sum(a.plan_call) as total_plan_call,
			// 		sum(a.actual_call) as total_actual_call,
			// 		sum(a.percent_call) as total_percent_call,
			// 		sum(a.plan_dokter_coverage) as total_plan_dokter_coverage,
			// 		sum(a.actual_dokter_coverage) as total_actual_dokter_coverage,
			// 		sum(a.percent_dokter_coverage) as total_percent_dokter_coverage,
			// 		sum(a.plan_percent_coverage) as total_plan_percent_coverage,
			// 		sum(a.call_type_a) as total_call_type_a,
			// 		sum(a.call_type_b) as total_call_type_b,
			// 		sum(a.call_type_c) as total_call_type_c,
			// 		sum(a.sub_call_type_a) as total_sub_call_type_a,
			// 		sum(a.sub_call_type_b) as total_sub_call_type_b,
			// 		sum(a.sub_call_type_c) as total_sub_call_type_c,
			// 		'.$q_produk,
			// 		// 'where' => [
			// 		// 	'mr' => $val['n_mr']
			// 		// ]
			// 		'group_by' => 'nama_dokter',
			// 		'produk_group' => get('pgroup'),
			// 		'join' => [
			// 			// 'rumus_kriteria_potensi b on b.produk_grup = a.produk_grup',
			// 			// 'rumus_status_dokter c on c.produk_grup = a.produk_grup',
			// 			// 'rumus_customer_matrix d on d.produk_grup = a.produk_grup',
			// 		],
			// 	])->result_array();
			// 	foreach($data as $dkey => $dval){
			// 		$total = 0;
			// 		$status_dokter = get_status_dokter(get('pgroup'), $dval['grand_total_pasien']);
			// 		$kriteria_potensi = get_kriteria_potensi(get('pgroup'), $dval['grand_total_indikasi']);
			// 		$customer_matrix = get_customer_matrix(get('pgroup'), $status_dokter, $kriteria_potensi);
			// 		echo '<tr>';
			// 		echo '<td>'.$no++.'</td>';
			// 		echo '<td style="white-space:nowrap">'.$dval['nama_dokter'].'</td>';
			// 		echo '<td style="white-space:nowrap">'.$dval['all_mr'].'</td>';
			// 		echo '<td style="white-space:nowrap">'.$dval['all_spesialist'].'</td>';
			// 		echo '<td style="white-space:nowrap">'.($dval['all_outlet'] ? $dval['all_outlet'] : 'Regular').'</td>';
			// 		for($i=1;$i<=count($indikasi);$i++){
			// 			echo '<td class="text-center">'.$dval['indikasi_'.$i].'</td>';
			// 		}
			// 		echo '<td class="text-center">'.$dval['total_potensi'].'</td>';
			// 		for($i=1;$i<=count($indikasi);$i++){
			// 			$tmpval = $dval['pasien_'.$i] ? $dval['pasien_'.$i] : 0;
			// 			echo '<td class="text-center">'.$tmpval.'</td>';
			// 		}
			// 		echo '<td class="text-center">'.$dval['total_pasien'].'</td>';
			// 		echo '<td class="text-center">'.$dval['plan_call'].'</td>';
			// 		echo '<td class="text-center">'.$dval['actual_call'].'</td>';
			// 		echo '<td class="text-center" style="white-space:nowrap">'.$dval['percent_call'].' %</td>';
			// 		echo '<td class="text-center">'.$dval['plan_dokter_coverage'].'</td>';
			// 		echo '<td class="text-center">'.$dval['actual_dokter_coverage'].'</td>';
			// 		echo '<td class="text-center" style="white-space:nowrap">'.$dval['percent_dokter_coverage'].' %</td>';
			// 		echo '<td class="text-center">'.$dval['plan_percent_coverage'].'</td>';
			// 		echo '<td class="text-center">'.$dval['actual_percent_coverage'].'</td>';
			// 		echo '<td class="text-center" style="white-space:nowrap">'.$dval['percent_percent_coverage'].' %</td>';
			// 		echo '<td class="text-center">'.$status_dokter.'</td>';
			// 		echo '<td class="text-center">'.$customer_matrix.'</td>';
			// 		echo '<td class="text-center">'.$dval['call_type_a'].'</td>';
			// 		echo '<td class="text-center">'.$dval['call_type_b'].'</td>';
			// 		echo '<td class="text-center">'.$dval['call_type_c'].'</td>';
			// 		$call_a = json_decode($dval['sub_call_type_a']);
			// 		$call_b = json_decode($dval['sub_call_type_b']);
			// 		$call_c = json_decode($dval['sub_call_type_c']);
					
			// 		foreach($sub_type_a as $sval){
			// 			$c_type = 0;
			// 			for($i=0;$i<count($call_a);$i++){
			// 				if($call_a[$i] == $sval['id']){
			// 					$c_type++;
			// 				}
			// 			}
			// 			echo '<td class="text-center">'.$c_type.'</td>';
			// 		}
					
			// 		foreach($sub_type_b as $sval){
			// 			$c_type = 0;
			// 			for($i=0;$i<count($call_b);$i++){
			// 				if($call_b[$i] == $sval['id']){
			// 					$c_type++;
			// 				}
			// 			}
			// 			echo '<td class="text-center">'.$c_type.'</td>';
			// 		}
					
			// 		foreach($sub_type_c as $sval){
			// 			$c_type = 0;
			// 			for($i=0;$i<count($call_c);$i++){
			// 				if($call_c[$i] == $sval['id']){
			// 					$c_type++;
			// 				}
			// 			}
			// 			echo '<td class="text-center">'.$c_type.'</td>';
			// 		}

			// 		foreach($produk as $rval){
			// 			// echo $rval['id'].'_produk ||';
			// 			if(!empty($dval[$rval['id'].'_produk'])){
			// 				$total += intval($dval[$rval['id'].'_produk']) * intval($dval[$rval['id'].'_price']);
			// 				echo '<td class="text-center">'.$dval[$rval['id'].'_produk'].'</td>';
			// 			} else {
			// 				echo '<td class="text-center"> 0 </td>';
			// 			}
			// 		}
			// 		echo '<td class="text-center">'.number_format($total).'</td>';
			// 		echo '</tr>';
			// 	}
			// }
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
		location.replace(base_url + 'report/matrix_by_doctor?pgroup='+pgroup+'&bulan='+bulan+'&tahun='+tahun);
	}
}
				
</script>
