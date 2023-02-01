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
				<th rowspan="3">No.</th>
				<th rowspan="3">Name</th>
				<th rowspan="3">Region</th>
				<th rowspan="3">Total Dokter</th>
				<th colspan="<?=count($spesialist)*3?>">Jumlah Dokter Per Spesialist</th>
            </tr>
            <tr>
                <?php
					foreach($spesialist as $val){
						echo '<th colspan="3">'.$val.'</th>';
					}
				?>
            </tr>
			<tr>
				<?php
					foreach($spesialist as $val){
						echo '<th>NU</th>';
						echo '<th>USE</th>';
						echo '<th>CONFIRM</th>';
					}
				?>
			</tr>
			<tbody>
			<?php
				$nasional_counter = [
					'spesialist' => [],
				];
				$tmp_where = [
					'produk_grup' => get('pgroup'),
				];
				if(user('id_group') == RM_ROLE_ID){
					$tmp_where['rm'] = user('username');
				}
				$rm = get_data('trxdact_'.get('tahun').'_'.get('bulan').' a',[
					'select' => 'rm.nama as nama_rm, rm.username as rm, region.kode as nama_region',
					'where' => $tmp_where,
					'join' => [
						'tbl_user rm on rm.username = a.rm',
						'region on region.id = rm.region'
					],
					'group_by' => 'rm',
					'sort_by' => 'region.kode',
					'sort' => 'ASC'
				])->result_array();
				$total_di_national = 0;
				foreach($rm as $key => $val){
					$nama_region = $val['nama_region'];
					$rm_counter = [
						'spesialist' => [],
					];
					$tmp_where = [
						'produk_grup' => get('pgroup'),
						'a.rm' => $val['rm']
					];
					if(user('id_group') == AM_ROLE_ID){
						$tmp_where['a.am'] = user('username');
					}
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
					$total_di_rm = 0;
					foreach($am as $akey => $aval){
						$am_counter = [
							'spesialist' => [],
						];
						$tmp_where = [
							'am' => $aval['am'],
							'produk_grup' => get('pgroup'),
						];
						if(user('id_group') == MR_ROLE_ID){
							$tmp_where['mr'] = user('username');
						}
						$key_m_query = '';
						foreach($spesialist as $kkey => $kval){
							$key_m_query .= 'count(case when (IF(sub_spesialist.nama is null, spesialist.nama, sub_spesialist.nama)) = "'.$kval.'" and status_dokter = "NU" then 1 end) as spesialist_NU_'.str_replace([' ','-'],['_','_'],$kval).',';
							$key_m_query .= 'count(case when (IF(sub_spesialist.nama is null, spesialist.nama, sub_spesialist.nama)) = "'.$kval.'" and status_dokter = "USE" then 1 end) as spesialist_USE_'.str_replace([' ','-'],['_','_'],$kval).',';
							$key_m_query .= 'count(case when (IF(sub_spesialist.nama is null, spesialist.nama, sub_spesialist.nama)) = "'.$kval.'" and status_dokter = "CONFIRM" then 1 end) as spesialist_CONFIRM_'.str_replace([' ','-'],['_','_'],$kval);
							// $key_m_query .= '(customer_matrix = "'.($kval['matrix']).'",count(a.id),0) as matrix_'.$kval['matrix'].',';
							if(count($spesialist) != $kkey){
								$key_m_query .= ',';
							}
						}
						//select sum plan call uniq dokter and mr
						// (select count(*) from trxdact_'.get('tahun').'_'.get('bulan').' where mr = a.mr and produk_grup = "'.get('pgroup').'" and status = 3) as plan_percent_coverage,
						$mr = get_data('trxdact_'.get('tahun').'_'.get('bulan'),[
							'select' => 'trxdact_'.get('tahun').'_'.get('bulan').'.*, '.$key_m_query.', mr.nama as nama_mr, spesialist.nama as nm_sp, sub_spesialist.nama as nm_sub_sp',
							'where' => $tmp_where,
							'join' => [
								'tbl_user mr on mr.username = trxdact_'.get('tahun').'_'.get('bulan').'.mr',
								'dokter on dokter.id = trxdact_'.get('tahun').'_'.get('bulan').'.dokter',
								'spesialist on spesialist.id = dokter.spesialist',
								'sub_spesialist on sub_spesialist.id = dokter.subspesialist type left'
							],
							'group_by' => 'mr',
						])->result_array();
						if(!$mr) continue;
						$total_di_am = 0;
						foreach($mr as $mkey => $mval){
							echo '<tr style="background-color:#b4d6c1">';
							echo '<td>'.($mkey+1).'</td>';
							echo '<td style="white-space:nowrap">'.$mval['nama_mr'].' (MR)</td>';
							echo '<td>'.$nama_region.'</td>';
							$total_di_mr = 0;
							foreach($spesialist as $sval){
								$total_di_mr += $mval['spesialist_NU_'.str_replace([' ','-'],['_','_'],$sval)] + $mval['spesialist_USE_'.str_replace([' ','-'],['_','_'],$sval)] + $mval['spesialist_CONFIRM_'.str_replace([' ','-'],['_','_'],$sval)];
							}
							echo '<td> '.$total_di_mr.' </td>';
							foreach($spesialist as $sval){
								echo '<td>'.$mval['spesialist_NU_'.str_replace([' ','-'],['_','_'],$sval)].'</td>';
								echo '<td>'.$mval['spesialist_USE_'.str_replace([' ','-'],['_','_'],$sval)].'</td>';
								echo '<td>'.$mval['spesialist_CONFIRM_'.str_replace([' ','-'],['_','_'],$sval)].'</td>';
								$am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)] = [
									'NU' => (!isset($am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU']) ? $mval['spesialist_NU_'.str_replace([' ','-'],['_','_'],$sval)] : $am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'] + $mval['spesialist_NU_'.str_replace([' ','-'],['_','_'],$sval)]),
									'USE' => (!isset($am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE']) ? $mval['spesialist_USE_'.str_replace([' ','-'],['_','_'],$sval)] : $am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'] + $mval['spesialist_USE_'.str_replace([' ','-'],['_','_'],$sval)]),
									'CONFIRM' => (!isset($am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM']) ? $mval['spesialist_CONFIRM_'.str_replace([' ','-'],['_','_'],$sval)] : $am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'] + $mval['spesialist_CONFIRM_'.str_replace([' ','-'],['_','_'],$sval)]),
								];
								
							}
							echo '</tr>';
							$total_di_am += $total_di_mr;
						}
						$total_di_rm += $total_di_am;
						echo '<tr style="background-color:#8dc3a7">';
						echo '<td>'.($akey+1).'</td>';
						echo '<td>'.$aval['nama_am'].'(AM)</td>';
						echo '<td>'.$nama_region.'</td>';
						echo '<td> '.$total_di_rm.' </td>';
						foreach($spesialist as $sval){
							echo '<td>'.$am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'].'</td>';
							echo '<td>'.$am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'].'</td>';
							echo '<td>'.$am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'].'</td>';
							$rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)] = [
								'NU' => (!isset($rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU']) ? $am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'] : $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'] + $am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU']),
								'USE' => (!isset($rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE']) ? $am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'] : $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'] + $am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE']),
								'CONFIRM' => (!isset($rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM']) ? $am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'] : $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'] + $am_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM']),
							];
						}
						echo '</tr>';
					}
					// debug($rm_counter); die;
					$total_di_national += $total_di_rm;
					echo '<tr style="background-color:#6baf92;'.(count($rm_counter['spesialist']) <= 0 ? 'display:none':'').'">';
					echo '<td>'.($key+1).'</td>';
					echo '<td>'.$val['nama_rm'].' (RM)</td>';
					echo '<td>'.$nama_region.'</td>';
					echo '<td> '.$total_di_rm.' </td>';
					foreach($spesialist as $sval){
							$rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'] = isset($rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU']) ? $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'] : 0; 
							$rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'] =  isset($rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE']) ? $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'] : 0;
							$rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'] =  isset($rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM']) ? $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'] : 0;
							echo '<td>'.$rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'].'</td>';
							echo '<td>'.$rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'].'</td>';
							echo '<td>'.$rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'].'</td>';
							$national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)] = [
								'NU' => (!isset($national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU']) ? $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'] : $national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'] + $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU']),
								'USE' => (!isset($national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE']) ? $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'] : $national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'] + $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE']),
								'CONFIRM' => (!isset($national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM']) ? $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'] : $national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'] + $rm_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM']),
							];
					}
					echo '</tr>';
				}
				echo '<tr>';
				echo '<th></th>';
				echo '<th>National</th>';
				echo '<th> -- </th>';
				echo '<th> '.$total_di_national.' </th>';
				foreach($spesialist as $sval){
					echo '<th>'.$national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['NU'].'</th>';
					echo '<th>'.$national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['USE'].'</th>';
					echo '<th>'.$national_counter['spesialist'][str_replace([' ','-'],['_','_'],$sval)]['CONFIRM'].'</th>';
				}
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
			location.replace(base_url + 'report/contribution?pgroup='+pgroup+'&bulan='+bulan+'&tahun='+tahun);
		}
	}
</script>