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
				<th rowspan="2">No.</th>
				<th rowspan="2">Name</th>
				<th rowspan="2">Region</th>
				<th colspan="<?=count($matrix)+1?>">Matrix</th>
			</tr>
			<tr>
				<!-- <th>A</th>
				<th>B</th>
				<th>C</th> -->
				<?php foreach($matrix as $key => $val){
					echo '<th>'.($val['matrix']).'</th>';
				} ?>
				<th>Total</th>
				
			</tr>
			<tbody>
			<?php
				$national_counter = [
					'matrix' => [],
					'total' => 0	
				];
				$tmp_where = [
					'produk_grup' => get('pgroup'),
				];
				if(user('id_group') == RM_ROLE_ID) $tmp_where['a.rm'] = user('username');
				$rm = get_data('trxdact_'.get('tahun').'_'.get('bulan').' a',[
					'select' => 'rm.nama as nama_rm, rm.username as rm',
					'where' => $tmp_where,
					'join' => [
						'tbl_user rm on rm.username = a.rm'
					],
					'group_by' => 'rm',
					'sort_by' => 'rm.nama',
					'sort' => 'ASC'
				])->result_array();
				foreach($rm as $key => $val){
					$rm_counter = [
						'matrix' => [],
						'total' => 0	
					];
					$tmp_where = [
						'produk_grup' => get('pgroup'),
						'a.rm' => $val['rm'],	
					];
					if(user('id_group') == AM_ROLE_ID) $tmp_where['a.am'] = user('username');
					$am = get_data('trxdact_'.get('tahun').'_'.get('bulan').' a',[
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
							'matrix' => [],
							'total' => 0	
						];
						$key_m_query = '';
						foreach($matrix as $kkey => $kval){
							$key_m_query .= 'count(case when a.customer_matrix = "'.$kval['matrix'].'" then 1 end) as matrix_'.$kval['matrix'];
							// $key_m_query .= '(customer_matrix = "'.($kval['matrix']).'",count(a.id),0) as matrix_'.$kval['matrix'].',';
							if(count($matrix) != $kkey){
								$key_m_query .= ',';
							}
						}
						$tmp_where = [
							'produk_grup' => get('pgroup'),
							'a.am' => $aval['am'],	
						];
						if(user('id_group') == MR_ROLE_ID){
							$tmp_where['a.mr'] = user('username');
						}
						$mr = get_data('trxdact_'.get('tahun').'_'.get('bulan').' a',[
							'select' => 'mr.nama as nama_mr, mr.username as mr, '.$key_m_query,
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
							// echo '<tr style="background-color:#b4d6c1">';
							// echo '<td>'.($mkey+1).'</td>';
							// echo '<td>'.$mval['nama_mr'].' (MR)</td>';
							// echo '<td> -- </td>';
							$total = 0;
							foreach($matrix as $mkey2 => $mval2){
								// echo '<td>'.$mval['matrix_'.$mval2['matrix']].'</td>';
								$total += $mval['matrix_'.$mval2['matrix']];
								if(!isset($am_counter['matrix'][$mkey2])){
									$am_counter['matrix'][$mkey2] = 0;
								}
								$am_counter['matrix'][$mkey2] += $mval['matrix_'.$mval2['matrix']];
								$am_counter['total'] += $mval['matrix_'.$mval2['matrix']];
							}
							// echo '<td>'.$total.'</td>';
							// echo '</tr>';
						}
						// echo '<tr style="background-color:#8dc3a7">';
						// echo '<td>'.($akey+1).'</td>';
						// echo '<td>'.$aval['nama_am'].'(AM)</td>';
						// echo '<td> -- </td>';
						$total = 0;
						foreach($matrix as $mkey2 => $mval2){
							if(!isset($am_counter['matrix'][$mkey2])){
								$am_counter['matrix'][$mkey2] = 0;
							}
							// echo '<td>'.$am_counter['matrix'][$mkey2].'</td>';
							$total += $am_counter['matrix'][$mkey2];
							if(!isset($rm_counter['matrix'][$mkey2])){
								$rm_counter['matrix'][$mkey2] = 0;
							}
								$rm_counter['matrix'][$mkey2] += $am_counter['matrix'][$mkey2];
								$rm_counter['total'] += $am_counter['matrix'][$mkey2];
						}
						// echo '<td>'.$total.'</td>';
						// echo '</tr>';
					}
					echo '<tr style="background-color:#6baf92">';
					echo '<td>'.($key+1).'</td>';
					echo '<td>'.$val['nama_rm'].' (RM)</td>';
					echo '<td> -- </td>';
					$total = 0;
					foreach($matrix as $mkey2 => $mval2){
						if(!isset($rm_counter['matrix'][$mkey2])){
							$rm_counter['matrix'][$mkey2] = 0;
						}
						echo '<td>'.$rm_counter['matrix'][$mkey2].'</td>';
						$total += intval($rm_counter['matrix'][$mkey2]);
						if(!isset($national_counter['matrix'][$mkey2])){
							$national_counter['matrix'][$mkey2] = 0;
						}
						$national_counter['matrix'][$mkey2] += $rm_counter['matrix'][$mkey2];
						$national_counter['total'] += $rm_counter['matrix'][$mkey2];
					}
					echo '<td>'.$total.'</td>';
					echo '</tr>';
				}
				echo '<tr>';
				echo '<th></th>';
				echo '<th>National</th>';
				echo '<th> -- </th>';
				$total = 0;
				foreach($matrix as $mkey2 => $mval2){
					echo '<th>'.(isset($national_counter['matrix'][$mkey2]) ? $national_counter['matrix'][$mkey2] : 0).'</th>';
					$total += isset($national_counter['matrix'][$mkey2]) ? $national_counter['matrix'][$mkey2] : 0;
				}
				echo '<th>'.$total.'</th>';
				echo '</tr>';
			?>
			</tbody>
		</thead>
	</table>
	<?php else: ?>
		<div class="text-center">
		<img src="<?=base_url('assets/images/no-data.svg')?>" width="40%">
		<h3> Filter untuk melihat hasil </h3> 
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
			location.replace(base_url + 'report/matrix_by_rm?pgroup='+pgroup+'&bulan='+bulan+'&tahun='+tahun);
		}
	}
</script>