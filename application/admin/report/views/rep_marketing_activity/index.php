<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label> Product Group </label>
			<select class="select2" id="fpgroup" style="width: 150px;" onchange="filter()">
				<option value="">Select Product Group</option>
				<?php foreach ($this->session->userdata('produk_group') as $val) {
					echo '<option value="' . $val['kode'] . '" ' . (get('pgroup') == $val['kode'] ? 'selected="selected"' : '') . '>' . $val['nama'] . '</option>';
				} ?>
			</select>
			<label> Bulan </label>
			<select class="select2 infinity" id="fbulan" style="width: 100px;" onchange="filter()">
				<option value="01" <?php echo $this->input->get('bulan') == '01' ? 'selected' : ''; ?>>Januari</option>
				<option value="02" <?php echo $this->input->get('bulan') == '02' ? 'selected' : ''; ?>>Februari</option>
				<option value="03" <?php echo $this->input->get('bulan') == '03' ? 'selected' : ''; ?>>Maret</option>
				<option value="04" <?php echo $this->input->get('bulan') == '04' ? 'selected' : ''; ?>>April</option>
				<option value="05" <?php echo $this->input->get('bulan') == '05' ? 'selected' : ''; ?>>Mei</option>
				<option value="06" <?php echo $this->input->get('bulan') == '06' ? 'selected' : ''; ?>>Juni</option>
				<option value="07" <?php echo $this->input->get('bulan') == '07' ? 'selected' : ''; ?>>Juli</option>
				<option value="08" <?php echo $this->input->get('bulan') == '08' ? 'selected' : ''; ?>>Agustus</option>
				<option value="09" <?php echo $this->input->get('bulan') == '09' ? 'selected' : ''; ?>>September</option>
				<option value="10" <?php echo $this->input->get('bulan') == '10' ? 'selected' : ''; ?>>Oktober</option>
				<option value="11" <?php echo $this->input->get('bulan') == '11' ? 'selected' : ''; ?>>November</option>
				<option value="12" <?php echo $this->input->get('bulan') == '12' ? 'selected' : ''; ?>>Desember</option>
			</select>
			<label> Tahun </label>
			<select class="select2 infinity" id="ftahun" style="width: 100px;" onchange="filter()">
				<?php for ($i = date('Y'); $i >= 2018; $i--) {
					echo '<option value="' . $i . '" ' . ($i == get('tahun') ? 'selected="selected"' : '') . '>' . $i . '</option>';
				} ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	if (get('pgroup') != '' && get('bulan') != '' && get('tahun') != '') :
		$marketing = get_data('marketing_aktifitas', [
			'where' => [
				'produk_grup' => get('pgroup'),
				'is_active' => 1
			]
		])->result_array();
	?>
		<table class="table-app table-bordered table-sm table-hover" style="width: 100%; text-align:center">
			<thead>
				<tr>
					<th rowspan="2">No.</th>
					<th rowspan="2">Name</th>
					<th rowspan="2">Region</th>
					<?php
					foreach ($marketing as $v) {
						echo '<th colspan="3">' . $v['nama'] . '</th>';
					}
					?>
					<th colspan="3">Total</th>
					<th colspan="3">Cycle To Date</th>
				</tr>
				<tr>
					<?php foreach ($marketing as $key => $val) {
						echo '<th>T</th>';
						echo '<th>A</th>';
						echo '<th>%</th>';
					} ?>
					<th>T</th>
					<th>A</th>
					<th>%</th>
					<th>T</th>
					<th>A</th>
					<th>%</th>
				</tr>
			<tbody>
				<?php
				$national_counter = [
					'marketing' => [],
					'target' => [],
				];
				$tmp_where = [
					'produk_grup' => get('pgroup'),
				];
				if (user('id_group') == RM_ROLE_ID) $tmp_where['a.rm'] = user('username');
				$rm = get_data('trxvisit_' . get('tahun') . '_' . get('bulan') . ' a', [
					'select' => 'rm.nama as nama_rm, rm.username as rm, re.kode as nama_region',
					'where' => $tmp_where,
					'join' => [
						'tbl_user rm on rm.username = a.rm',
						'region re on re.id = rm.region type left'
					],
					'group_by' => 'rm',
					'sort_by' => 're.kode',
					'sort' => 'ASC'
				])->result_array();

				
				foreach ($rm as $key => $val) {

					$nama_region = $val['nama_region'];

					$rm_counter = [
						'marketing' => [],
						'target' => []
					];
					$tmp_where = [
						'produk_grup' => get('pgroup'),
						'a.rm' => $val['rm'],
					];
					if (user('id_group') == AM_ROLE_ID) $tmp_where['a.am'] = user('username');
					$am = get_data('trxvisit_' . get('tahun') . '_' . get('bulan') . ' a', [
						'select' => 'am.nama as nama_am, am.username as am',
						'where' => $tmp_where,
						'join' => [
							'tbl_user am on am.username = a.am'
						],
						'group_by' => 'am',
						'sort_by' => 'am.nama',
						'sort' => 'ASC'
					])->result_array();
					foreach ($am as $akey => $aval) {
						$am_counter = [
							'marketing' => [],
							'target' => []
						];
						$key_m_query = '';
						foreach ($marketing as $kkey => $kval) {
							$key_m_query .= 'count(case when c.marketing_aktifitas = "' . $kval['id'] . '" and c.tanggal != "0000-00-00" then 1 end) as marketing_' . $kval['id'];
							if (count($marketing) != $kkey) {
								$key_m_query .= ',';
							}
						}
						$tmp_where = [
							'a.produk_grup' => get('pgroup'),
							'a.am' => $aval['am'],
						];

						if (user('id_group') == MR_ROLE_ID) {
							$tmp_where['a.mr'] = user('username');
						}

						if(in_array(get('bulan'), ['01','02','03','04'])){
							$cycle = 1;
						} else if(in_array(get('bulan'), ['05','06','07','08'])){
							$cycle = 2;
						} else {
							$cycle = 3;
						}

						if($cycle == 1){
							if(get('bulan') == '01'){
								$mark_periode = '1';
							} else if(get('bulan') == '02'){
								$mark_periode = '2';
							} else if(get('bulan') == '03'){
								$mark_periode = '3';
							} else {
								$mark_periode = '4';
							}
						} else if($cycle == 2){
							if(get('bulan') == '05'){
								$mark_periode = '1';
							} else if(get('bulan') == '06'){
								$mark_periode = '2';
							} else if(get('bulan') == '07'){
								$mark_periode = '3';
							} else {
								$mark_periode = '4';
							}
						} else {
							if(get('bulan') == '09'){
								$mark_periode = '1';
							} else if(get('bulan') == '10'){
								$mark_periode = '2';
							} else if(get('bulan') == '11'){
								$mark_periode = '3';
							} else {
								$mark_periode = '4';
							}
						}

						$mr = get_data('trxvisit_' . get('tahun') . '_' . get('bulan') . ' a', [
							'select' 	=> 'mr.nama as nama_mr, mr.username as mr, c.tanggal, ' . $key_m_query,
							'where' 	=> $tmp_where,
							'join' 		=> [
								'tbl_user mr on mr.username = a.mr',
								'trxdact_' . get('tahun') . '_' . get('bulan') . ' b on a.id = b.visit_plan type left',
								'trxdact_marketing_' . get('tahun') . '_' . get('bulan') . ' c on b.id = c.data_sales type left',
							],
							'group_by' 	=> 'a.mr, c.tanggal',
							'sort_by' 	=> 'mr.nama',
							'sort' 		=> 'ASC'
						])->result_array();
						

						$tmp_mr = [];
						foreach($mr as $k => $v){
							foreach ($marketing as $mk => $mv) {
								if($v['marketing_'.$mv['id']] > 1) $v['marketing_'.$mv['id']] = 1;
							}
							$push = true;
							foreach($tmp_mr as $tk => $tv){
								if($tv['mr'] == $v['mr']){	
									$push = false;
									foreach ($marketing as $mk => $mv) {
										$tmp_mr[$tk]['marketing_'.$mv['id']] += $v['marketing_'.$mv['id']];
									}
								}
							}
							if($push) array_push($tmp_mr, $v);
							// if(!$tmp_mr) array_push($tmp_mr, $v);
						}

						// debug($tmp_mr);
						$mr = $tmp_mr;
						
						if (!$mr) continue;

						// debug($target); die;
						foreach ($mr as $mkey => $mval) {

							$tmp_where['mr'] = $mval['mr'];

							
							$res_target = get_data('trxprof_'.get('tahun').'_'.$cycle, [
								'select'=> 'marketing_bulan_'.$mark_periode.' as target_marketing',
								'where' => [
									'mr' => $mval['mr']
								]
							])->result_array();
							$target = [];
							foreach($res_target as $rt){
								$target_val = explode(',', $rt['target_marketing']);
								foreach ($target_val as $tv) {
									if (!isset($target[$tv])) {
										$target[$tv] = 1;
									} else {
										$target[$tv]++;
									}
								}
							}
							echo '<tr style="background-color:#b4d6c1">';
							echo '<td>' . ($mkey + 1) . '</td>';
							echo '<td style="width:1px;white-space:nowrap">' . $mval['nama_mr'] . ' (MR)</td>';
							echo '<td> '.$nama_region.'</td>';
							// $tmp_marktifitas = explode(',',$mval['marketing_atifitas']);
							foreach ($marketing as $mkey2 => $mval2) {

								$plan_marketing = get_data('plan_marketing', [
									'where' => [
										'mr' => $mval['mr'],
										'bulan' => date('m'),
										'tahun' => date('Y'),
										'marketing_aktifitas' => $mval2['id']
									]
								])->row_array();

								$tmp_target = 0;
								// if(isset($mval[$mval2['id']])){
								// 	$tmp_target = $target[$mval2['id']];
								// 	echo '<td>'.$target[$mval2['id']].'</td>';
								// } else {
								// 	echo '<td>0</td>';
								// }
								// if (!isset($target[$mval2['id']])) {
								// 	$tmp_target = 0;
								// } else {
								// 	$tmp_target = $target[$mval2['id']];
								// }

								if($plan_marketing) $tmp_target = $plan_marketing['target'];
								echo '<td>' . $tmp_target . '</td>';
								echo '<td>' . $mval['marketing_' . $mval2['id']] . '</td>';
								echo '<td style="width:1px;white-space:nowrap">' . ($tmp_target == 0 || $mval['marketing_' . $mval2['id']] == 0 ? 0 : round($mval['marketing_' . $mval2['id']] / $tmp_target * 100, 2)
								) . ' %</td>';
								if (!isset($am_counter['marketing'][$mval2['id']])) {
									$am_counter['marketing'][$mval2['id']] = 0;
								}
								$am_counter['marketing'][$mval2['id']] += $mval['marketing_' . $mval2['id']];
								if (!isset($am_counter['target'][$mval2['id']])) {
									$am_counter['target'][$mval2['id']] = 0;
								}
								$am_counter['target'][$mval2['id']] += $tmp_target;
							}
							echo '<td>0</td>';
							echo '<td>0</td>';
							echo '<td>0%</td>';
							echo '<td>0</td>';
							echo '<td>0</td>';
							echo '<td>0%</td>';
							echo '</tr>';
						}
						echo '<tr style="background-color:#8dc3a7">';
						echo '<td>' . ($akey + 1) . '</td>';
						echo '<td>' . $aval['nama_am'] . '(AM)</td>';
						echo '<td> '.$nama_region.'</td>';
						foreach ($marketing as $mkey2 => $mval2) {
							echo '<td>' . $am_counter['target'][$mval2['id']] . '</td>';
							echo '<td>' . $am_counter['marketing'][$mval2['id']] . '</td>';
							echo '<td style="width:1px;white-space:nowrap">' . ($am_counter['target'][$mval2['id']] == 0 ? 0 : round($am_counter['marketing'][$mval2['id']] / $am_counter['target'][$mval2['id']] * 100, 2)
							) . ' %</td>';
							if (!isset($rm_counter['marketing'][$mval2['id']])) {
								$rm_counter['marketing'][$mval2['id']] = 0;
							}
							$rm_counter['marketing'][$mval2['id']] += $am_counter['marketing'][$mval2['id']];
							if (!isset($rm_counter['target'][$mval2['id']])) {
								$rm_counter['target'][$mval2['id']] = 0;
							}
							$rm_counter['target'][$mval2['id']] += $am_counter['target'][$mval2['id']];
						}
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0%</td>';
						echo '<td>0</td>';
						echo '<td>0</td>';
						echo '<td>0%</td>';
						echo '</tr>';
					}
					if (count($rm_counter['marketing']) <= 0) continue;
					echo '<tr style="background-color:#6baf92">';
					echo '<td>' . ($key + 1) . '</td>';
					echo '<td>' . $val['nama_rm'] . ' (RM)</td>';
					echo '<td> '.$nama_region.'</td>';
					foreach ($marketing as $mkey2 => $mval2) {
						echo '<td>' . $rm_counter['target'][$mval2['id']] . '</td>';
						echo '<td>' . $rm_counter['marketing'][$mval2['id']] . '</td>';
						echo '<td style="width:1px;white-space:nowrap">' . ($rm_counter['target'][$mval2['id']] == 0 ? 0 : round($rm_counter['marketing'][$mval2['id']] / $rm_counter['target'][$mval2['id']] * 100, 2)
						) . ' %</td>';
						if (!isset($national_counter['marketing'][$mval2['id']])) {
							$national_counter['marketing'][$mval2['id']] = 0;
						}
						$national_counter['marketing'][$mval2['id']] += $rm_counter['marketing'][$mval2['id']];
						if (!isset($national_counter['target'][$mval2['id']])) {
							$national_counter['target'][$mval2['id']] = 0;
						}
						$national_counter['target'][$mval2['id']] += $rm_counter['target'][$mval2['id']];
					}
					echo '<td>0</td>';
					echo '<td>0</td>';
					echo '<td>0%</td>';
					echo '<td>0</td>';
					echo '<td>0</td>';
					echo '<td>0%</td>';
					echo '</tr>';
				}
				echo '<tr>';
				echo '<th></th>';
				echo '<th>National</th>';
				echo '<th> -- </th>';
				foreach ($marketing as $mkey2 => $mval2) {
					echo '<th>' . $national_counter['target'][$mval2['id']] . '</th>';
					echo '<th>' . $national_counter['marketing'][$mval2['id']] . '</th>';
					echo '<th style="width:1px;white-space:nowrap">' . ($national_counter['target'][$mval2['id']] == 0 ? 0 : round($national_counter['marketing'][$mval2['id']] / $national_counter['target'][$mval2['id']] * 100, 2)
					) . ' %</th>';
				}
				echo '<td>0</td>';
				echo '<td>0</td>';
				echo '<td>0%</td>';
				echo '<td>0</td>';
				echo '<td>0</td>';
				echo '<td>0%</td>';
				echo '</tr>';
				?>
			</tbody>
			</thead>
		</table>
	<?php else : ?>
		<div class="text-center">
			<img src="<?= base_url('assets/images/no-data.svg') ?>" width="40%">
			<h3> Filter untuk melihat hasil </h3>
		</div>
	<?php endif; ?>
</div>
<script>
	var id_approve = '';

	function filter() {
		var pgroup = $('#fpgroup').val();
		var bulan = $('#fbulan').val();
		var tahun = $('#ftahun').val();
		if (pgroup != '' && bulan != '' && tahun != '') {
			location.replace(base_url + 'report/rep_marketing_activity?pgroup=' + pgroup + '&bulan=' + bulan + '&tahun=' + tahun);
		}
	}
</script>