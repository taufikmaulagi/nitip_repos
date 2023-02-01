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
				<?php foreach($this->session->userdata('produk_group') as $val){
					echo '<option value="'.$val['kode'].'" '.(get('pgroup') == $val['kode'] ? 'selected="selected"' : '').'>'.$val['nama'].'</option>';
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
				<?php for($i=date('Y');$i>=2018;$i--){
					echo '<option value="'.$i.'" '.($i == get('tahun') ? 'selected="selected"' : '').'>'.$i.'</option>';
				}?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
		if(get('pgroup') != '' && get('bulan') != '' && get('tahun') != ''):
	?>
	<table class="table-app table-bordered table-sm table-hover" style="width: 100%; text-align:center">
		<thead>
			<tr>
				<th>No.</th>
				<th>Name</th>
				<th>Region</th>
				<th>A</th>
				<th>HK</th>
				<th>DFR / Day</th>
				<th>TARGET</th>
				<th>%</th>
			</tr>
			<tbody>
			<?php
				$nasional_counter = [
					'name' => 0,
					'region' => 0,
					'a' => 0,
					'hk' => 0,
					'dfr_day' => 0,
					'target' => 0
				];
				$tmp_where = [
					'a.produk_grup' => get('pgroup'),
					'a.call_type' => 1
				];
				if(get('id_group') == RM_ROLE_ID){
					$tmp_where['a.rm'] = user('username');
				}
				$rm = get_data('trxdfr_'.get('tahun').'_'.get('bulan').' a',[
					'select' => 'rm.nama as nama_rm, rm.username as rm, re.kode as nama_region',
					'where' => $tmp_where,
					'join' => [
						'tbl_user rm on rm.username = a.rm',
						'jumlah_hari_kerja hk on hk.user = rm.username and bulan = '.get('bulan').' type left',
						'target_dfr td on td.produk_grup = a.produk_grup type left',
						'region re on re.id = rm.region type left'
					],
					'group_by' => 'rm',
					'sort_by' => 're.kode',
					'sort' => 'ASC'
				])->result_array();
				foreach($rm as $key => $val){
					$nama_region = $val['nama_region'];
					$rm_counter = [
						'name' => 0,
						'region' => 0,
						'a' => 0,
						'hk' => 0,
						'dfr_day' => 0,
						'target' => 0
					];
					$tmp_where = [
						'a.produk_grup' => get('pgroup'),
						'a.rm' => $val['rm'],
						'a.call_type' => 1
					];
					if(user('id_group') == AM_ROLE_ID){
						$tmp_where['a.am'] = user('username');
					}
					$am = get_data('trxdfr_'.get('tahun').'_'.get('bulan').' a',[
						'select' => 'am.nama as nama_am, am.username as am',
						'where' => $tmp_where,
						'join' => [
							'tbl_user am on am.username = a.am',
							'jumlah_hari_kerja hk on hk.user = am.username and bulan = '.get('bulan').' type left',
							'target_dfr td on td.produk_grup = a.produk_grup type left'
						],
						'group_by' => 'am',
						'sort_by' => 'am.nama',
						'sort' => 'ASC'
					])->result_array();
					foreach($am as $akey => $aval){
						$am_counter = [
							'name' => 0,
							'region' => 0,
							'a' => 0,
							'hk' => 0,
							'dfr_day' => 0,
							'target' => 0
						];
						// $key_m_query = '';
						// foreach($key_message as $kkey => $kval){
						// 	$key_m_query .= 'IF(key_message = "'.($kval['id']).'",count(a.id),0) as key_message_'.$kkey;
						// 	if(count($key_message) != $kkey){
						// 		$key_m_query .= ',';
						// 	}
						// }
						$tmp_where = [
							'a.produk_grup' => get('pgroup'),
							'a.am' => $aval['am'],
							'a.call_type' => 1
						];
						if(user('id_group') == MR_ROLE_ID){
							$tmp_where['a.mr'] = user('username');
						}
						$mr = get_data('trxdfr_'.get('tahun').'_'.get('bulan').' a',[
							'select' => 'mr.nama as nama_mr, mr.username as mr, a.id as dfr, 
							if(a.call_type=1,count(a.id),0) as call_type_a, hk.jumlah as hari_kerja, td.target as target_dfr',
							'where' => $tmp_where,
							'join' => [
								'tbl_user mr on mr.username = a.mr',
								'jumlah_hari_kerja hk on hk.user = mr.username and bulan = '.get('bulan').' type left',
								'target_dfr td on td.produk_grup = a.produk_grup and td.tim = mr.tim type left',
							],
							'group_by' => 'mr',
							'sort_by' => 'mr.nama',
							'sort' => 'ASC'
						])->result_array();
						if(!$mr) continue;
						foreach($mr as $mkey => $mval){
							if(!$mval['hari_kerja']) $mval['hari_kerja'] = 0;
							$dfr_day = (intval($mval['call_type_a']) > 0 && intval($mval['hari_kerja']) > 0) ? (intval($mval['call_type_a']) / intval($mval['hari_kerja'])) : 0;
							$per_day = $dfr_day > 0 && $mval['target_dfr'] > 0 ? ($dfr_day / $mval['target_dfr']) : 0;
							echo '<tr style="background-color:#b4d6c1">';
								echo '<td>'.($mkey+1).'</td>';
								echo '<td>'.$mval['nama_mr'].' (MR)</td>';
								echo '<td>'.$nama_region.'</td>';
								echo '<td>'.$mval['call_type_a'].'</td>';
								echo '<td>'.$mval['hari_kerja'].'</td>';
								echo '<td>'.(round($dfr_day, 2)).'</td>';
								echo '<td>'.$mval['target_dfr'].'</td>';
								echo '<td>'.(round($per_day * 100, 2)).' %</td>';
							echo '</tr>';		
							$am_counter['a'] += intval($mval['call_type_a']) > 0 ? intval($mval['call_type_a']) : 0;
							$am_counter['hk'] += intval($mval['hari_kerja']) > 0 ? intval($mval['hari_kerja']) : 0;
							$am_counter['target'] += intval($mval['target_dfr']) > 0 ? intval($mval['target_dfr']) : 0;
						}
						$dfr_day = (intval($am_counter['a']) > 0 && intval($am_counter['hk']) > 0) ? (intval($am_counter['a']) / intval($am_counter['hk'])) : 0;
						$per_day = $dfr_day > 0 && $am_counter['target'] > 0 ? ($dfr_day / $am_counter['target']) : 0;
						echo '<tr style="background-color:#8dc3a7">';
							echo '<td>'.($akey+1).'</td>';
							echo '<td>'.$aval['nama_am'].'(AM)</td>';
							echo '<td>'.$nama_region.'</td>';
							echo '<td>'.$am_counter['a'].'</td>';
							echo '<td>'.$am_counter['hk'].'</td>';
							// echo '<td>'.$am_counter['call_type_b'].'</td>';
							// echo '<td>'.$am_counter['call_type_c'].'</td>';
							echo '<td>'.(round($dfr_day, 2)).'</td>';
							echo '<td>'.($am_counter['target'] / count($mr)).'</td>';
							echo '<td>'.(round($per_day * 100, 2)).' %</td>';
						echo '</tr>';

						$rm_counter['a'] += intval($am_counter['a']) > 0 ? intval($am_counter['a']) : 0;
						$rm_counter['hk'] += intval($am_counter['hk']) > 0 ? intval($am_counter['hk']) : 0;
						$rm_counter['dfr_day'] += intval($am_counter['dfr_day']) > 0 ? intval($am_counter['dfr_day']) : 0;
						$rm_counter['target'] += $am_counter['target'] / count($mr) > 0 ? $am_counter['target'] / count($mr): 0;
					}
					// if($rm_counter['a'] <=0 ) continue;
					$dfr_day = (intval($rm_counter['a']) > 0 && intval($rm_counter['hk']) > 0) ? (intval($rm_counter['a']) / intval($rm_counter['hk'])) : 0;
					$per_day = $dfr_day > 0 && $rm_counter['target'] > 0 ? ($dfr_day / $rm_counter['target']) : 0;
					echo '<tr style="background-color:#8dc3a7">';
						echo '<td>'.($akey+1).'</td>';
						echo '<td>'.$val['nama_rm'].'(RM)</td>';
						echo '<td>'.$nama_region.'</td>';
						echo '<td>'.$rm_counter['a'].'</td>';
						echo '<td>'.$rm_counter['hk'].'</td>';
						// echo '<td>'.$am_counter['call_type_b'].'</td>';
						// echo '<td>'.$am_counter['call_type_c'].'</td>';
						echo '<td>'.(round($dfr_day, 2)).'</td>';
						echo '<td>'.round($rm_counter['target']/count($rm),2).'</td>';
						echo '<td>'.(round($per_day * 100, 2)).' %</td>';
					echo '</tr>';
					$nasional_counter['a'] += intval($rm_counter['a']) > 0 ? intval($rm_counter['a']) : 0;
					$nasional_counter['hk'] += intval($rm_counter['hk']) > 0 ? intval($rm_counter['hk']) : 0;
					$nasional_counter['dfr_day'] += intval($rm_counter['dfr_day']) > 0 ? intval($rm_counter['dfr_day']) : 0;
					$nasional_counter['target'] += $rm_counter['target']/count($rm) > 0 ? $rm_counter['target']/count($rm) : 0;
				}
				$dfr_day = (intval($nasional_counter['a']) > 0 && intval($nasional_counter['hk']) > 0) ? ($nasional_counter['a'] / $nasional_counter['hk']) : 0;
				$per_day = $dfr_day > 0 && $nasional_counter['target'] > 0 ? ($dfr_day / $nasional_counter['target']) : 0;
				echo '<tr style="background-color:#8dc3a7">';
					echo '<td>'.($akey+1).'</td>';
					echo '<td> National </td>';
					echo '<td></td>';
					echo '<td>'.$nasional_counter['a'].'</td>';
					echo '<td>'.$nasional_counter['hk'].'</td>';
					// echo '<td>'.$am_counter['call_type_b'].'</td>';
					// echo '<td>'.$am_counter['call_type_c'].'</td>';
					echo '<td>'.(round($dfr_day, 2)).'</td>';
					echo '<td>'.round($nasional_counter['target'],2).'</td>';
					echo '<td>'.(round($per_day * 100, 2)).' %</td>';
			echo '</tr>';
			?>
			</tbody>
		</thead>
	</table>
	<?php else: ?>
		<div class="text-center">
		<img src="<?=base_url('assets/images/no-data.svg')?>" width="40%">
		<h3> Filter untuk melihat data </h3> 
		</div>
	<?php endif; ?>
</div>
<script>
	
	var id_approve = '';

	function filter(){
		var pgroup = $('#fpgroup').val();
		var bulan = $('#fbulan').val();
		var tahun = $('#ftahun').val();
		if(pgroup != '' && bulan != '' && tahun != ''){
			location.replace(base_url + 'report/dfr_day?pgroup='+pgroup+'&bulan='+bulan+'&tahun='+tahun);
		}
	}
</script>