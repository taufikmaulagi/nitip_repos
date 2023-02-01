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
	<h3 style="margin-left: 30px; margin-top:10px"> Key Message </h3>
	<ol>
		<?php
			foreach($key_message as $val){
				echo '<li>'.$val['nama'].'</li>';
			}
		?>
	</ol>
	<table class="table-app table-bordered table-sm table-hover" style="width: 100%; text-align:center">
		<thead>
			<tr>
				<th rowspan="2">No.</th>
				<th rowspan="2">Name</th>
				<th rowspan="2">Region</th>
				<th rowspan="2">Call Type A</th>
				<th colspan="<?=count($key_message)?>">Key Message</th>
				<th colspan="2">Feedback Criteria</th>
			</tr>
			<tr>
				<!-- <th>A</th>
				<th>B</th>
				<th>C</th> -->
				<?php foreach($key_message as $key => $val){
					echo '<th>'.($key+1).'</th>';
				} ?>
				<th>Positive</th>
				<th>Negative</th>
			</tr>
			<tbody>
			<?php
				$nasional_counter = [
					'call_type_a' => 0,
					'call_type_b' => 0,
					'call_type_c' => 0,
					'key_message' => [],
					'feedback_positive' => 0,
					'feedback_negative' => 0
				];
				$tmp_where = [
					'produk_grup' => get('pgroup'),
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
						'region re on re.id = rm.region type left'
					],
					'group_by' => 'rm',
					'sort_by' => 're.kode',
					'sort' => 'ASC'
				])->result_array();
				foreach($rm as $key => $val){
					$nama_region = $val['nama_region'];
					$rm_counter = [
						'call_type_a' => 0,
						'call_type_b' => 0,
						'call_type_c' => 0,
						'key_message' => [],
						'feedback_positive' => 0,
						'feedback_negative' => 0
					];
					$tmp_where = [
						'produk_grup' => get('pgroup'),
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
							'tbl_user am on am.username = a.am'
						],
						'group_by' => 'am',
						'sort_by' => 'am.nama',
						'sort' => 'ASC'
					])->result_array();
					foreach($am as $akey => $aval){
						$am_counter = [
							'call_type_a' => 0,
							'call_type_b' => 0,
							'call_type_c' => 0,
							'key_message' => [],
							'feedback_positive' => 0,
							'feedback_negative' => 0
						];
						$key_m_query = '';
						foreach($key_message as $kkey => $kval){
							$key_m_query .= 'IF(key_message = "'.($kval['id']).'",count(a.id),0) as key_message_'.$kkey;
							if(count($key_message) != $kkey){
								$key_m_query .= ',';
							}
						}
						$tmp_where = [
							'produk_grup' => get('pgroup'),
							'a.am' => $aval['am'],
							'a.call_type' => 1
						];
						if(user('id_group') == MR_ROLE_ID){
							$tmp_where['a.mr'] = user('username');
						}
						$mr = get_data('trxdfr_'.get('tahun').'_'.get('bulan').' a',[
							'select' => 'mr.nama as nama_mr, mr.username as mr, a.id as dfr, 
							if(a.call_type=1,count(a.id),0) as call_type_a,
							if(a.call_type=2,count(a.id),0) as call_type_b,
							if(a.call_type=3,count(a.id),0) as call_type_c,'.$key_m_query,
							'where' => $tmp_where,
							'join' => [
								'tbl_user mr on mr.username = a.mr'
							],
							'group_by' => 'mr',
							'sort_by' => 'mr.nama',
							'sort' => 'ASC'
						])->result_array();
						if(!$mr) continue;
						foreach($mr as $mkey => $mval){
							$feedbact_dfr = get_data('trxdfr_feedback_'.get('tahun').'_'.get('bulan').' a',[
								'select' => 'if(a.penilaian = "Sesuai dengan Tahapan OPSS",count(a.dfr),0) as positif,
								if(a.penilaian = "Belum Sesuai dengan Tahapan OPSS",count(a.dfr),0) as negatif',
								'where' => [
									'a.dfr' => $mval['dfr']
								]
							])->row_array();
							echo '<tr style="background-color:#b4d6c1">';
							echo '<td>'.($mkey+1).'</td>';
							echo '<td>'.$mval['nama_mr'].' (MR)</td>';
							echo '<td>'.$nama_region.'</td>';
							echo '<td>'.$mval['call_type_a'].'</td>';
							// echo '<td>'.$mval['call_type_b'].'</td>';
							// echo '<td>'.$mval['call_type_c'].'</td>';
							foreach($key_message as $key2 => $val2){
								echo '<td>'.$mval['key_message_'.$key2].'</td>';
							}
							echo '<td>'.$feedbact_dfr['positif'].'</td>';
							echo '<td>'.$feedbact_dfr['negatif'].'</td>';
							echo '</tr>';		
							$am_counter['call_type_a'] += intval($mval['call_type_a']);
							$am_counter['call_type_b'] += intval($mval['call_type_b']);
							$am_counter['call_type_c'] += intval($mval['call_type_c']);
							foreach($key_message as $key2 => $val2){
								if(!isset($am_counter['key_message'][$key2])){
									$am_counter['key_message'][$key2] = 0;
								}
								$am_counter['key_message'][$key2] += intval($mval['key_message_'.$key2]);
							}
							$am_counter['feedback_positive'] += intval($feedbact_dfr['positif']);
							$am_counter['feedback_negative'] += intval($feedbact_dfr['negatif']);
						}
						echo '<tr style="background-color:#8dc3a7">';
						echo '<td>'.($akey+1).'</td>';
						echo '<td>'.$aval['nama_am'].'(AM)</td>';
						echo '<td>'.$nama_region.'</td>';
						echo '<td>'.$am_counter['call_type_a'].'</td>';
						// echo '<td>'.$am_counter['call_type_b'].'</td>';
						// echo '<td>'.$am_counter['call_type_c'].'</td>';
						foreach($key_message as $key2 => $val2){
							echo '<td>'.$am_counter['key_message'][$key2].'</td>';
						}
						echo '<td>'.$am_counter['feedback_positive'].'</td>';
						echo '<td>'.$am_counter['feedback_negative'].'</td>';
						echo '</tr>';	
						$rm_counter['call_type_a'] += intval($am_counter['call_type_a']);
						$rm_counter['call_type_b'] += intval($am_counter['call_type_b']);
						$rm_counter['call_type_c'] += intval($am_counter['call_type_c']);
						foreach($key_message as $key2 => $val2){
							if(!isset($rm_counter['key_message'][$key2])){
								$rm_counter['key_message'][$key2] = 0;
							}
							$rm_counter['key_message'][$key2] += intval($am_counter['key_message'][$key2]);
						}
						$rm_counter['feedback_positive'] += intval($am_counter['feedback_positive']);
						$rm_counter['feedback_negative'] += intval($am_counter['feedback_negative']);
					}
					if($rm_counter['call_type_a'] <=0 ) continue;
					echo '<tr style="background-color:#6baf92">';
					echo '<td>'.($key+1).'</td>';
					echo '<td>'.$val['nama_rm'].' (RM)</td>';
					echo '<td>'.$nama_region.'</td>';
					echo '<td>'.$rm_counter['call_type_a'].'</td>';
					// echo '<td>'.$rm_counter['call_type_b'].'</td>';
					// echo '<td>'.$rm_counter['call_type_c'].'</td>';
					foreach($key_message as $key2 => $val2){
						echo '<td>'.$rm_counter['key_message'][$key2].'</td>';
					}
					echo '<td>'.$rm_counter['feedback_positive'].'</td>';
					echo '<td>'.$rm_counter['feedback_negative'].'</td>';
					echo '</tr>';
					$nasional_counter['call_type_a'] += intval($rm_counter['call_type_a']);
					// $nasional_counter['call_type_b'] += intval($rm_counter['call_type_b']);
					// $nasional_counter['call_type_c'] += intval($rm_counter['call_type_c']);
					foreach($key_message as $key2 => $val2){
						if(!isset($nasional_counter['key_message'][$key2])){
							$nasional_counter['key_message'][$key2] = 0;
						}
						$nasional_counter['key_message'][$key2] += intval($rm_counter['key_message'][$key2]);
					}
					$nasional_counter['feedback_positive'] += intval($rm_counter['feedback_positive']);
					$nasional_counter['feedback_negative'] += intval($rm_counter['feedback_negative']);
				}
				echo '<tr>';
				echo '<th></th>';
				echo '<th>National</th>';
				echo '<th> -- </th>';
				echo '<th>'.$nasional_counter['call_type_a'].'</th>';
				// echo '<th>'.$nasional_counter['call_type_b'].'</th>';
				// echo '<th>'.$nasional_counter['call_type_c'].'</th>';
				foreach($key_message as $key2 => $val2){
					echo '<th>'.(isset($nasional_counter['key_message'][$key2])	 ? $nasional_counter['key_message'][$key2] : 0).'</th>';
				}
				echo '<th>'.$nasional_counter['feedback_positive'].'</th>';
				echo '<th>'.$nasional_counter['feedback_negative'].'</th>';
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
			location.replace(base_url + 'report/dfr?pgroup='+pgroup+'&bulan='+bulan+'&tahun='+tahun);
		}
	}
</script>