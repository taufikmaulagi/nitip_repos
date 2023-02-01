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
				<th class="text-center" colspan="3">Total Call</th>
                <th class="text-center" colspan="3">Doctor Coverage</th>
                <th class="text-center" colspan="3">Percent Coverage</th>
            </tr>
            <tr>
                <th class="text-center">PLAN</th>
                <th class="text-center">ACTUAL</th>
                <th class="text-center">%</th>
                <th class="text-center">PLAN</th>
                <th class="text-center">ACTUAL</th>
                <th class="text-center">%</th>
                <th class="text-center">PLAN</th>
                <th class="text-center">ACTUAL</th>
                <th class="text-center">%</th>
            </tr>
			<tbody>
			<?php
				$nasional_counter = [
					'plan_call' => 0,
					'actual_call' => 0,
					'plan_dokter_coverage' => 0,
					'actual_dokter_coverage' => 0,
					'plan_percent_coverage' => 0,
					'actual_percent_coverage' => 0,
				];
				$tmp_where = [
					'produk_grup' => get('pgroup'),
				];
				if(user('id_group') == RM_ROLE_ID){
					$tmp_where['rm'] = user('username');
				}
				$rm = get_data('trxvisit_'.get('tahun').'_'.get('bulan').' a',[
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

					$nama_region = '--';
					if(!empty($val['nama_region'])) $nama_region = $val['nama_region'];

					$rm_counter = [
						'plan_call' => 0,
						'actual_call' => 0,
						'plan_dokter_coverage' => 0,
						'actual_dokter_coverage' => 0,
						'plan_percent_coverage' => 0,
						'actual_percent_coverage' => 0,
					];
					$tmp_where = [
						'produk_grup' => get('pgroup'),
						'a.rm' => $val['rm']
					];
					if(user('id_group') == AM_ROLE_ID){
						$tmp_where['a.am'] = user('username');
					}
					$am = get_data('trxvisit_'.get('tahun').'_'.get('bulan').' a',[
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
							'plan_call' => 0,
							'actual_call' => 0,
							'plan_dokter_coverage' => 0,
							'actual_dokter_coverage' => 0,
							'plan_percent_coverage' => 0,
							'actual_percent_coverage' => 0,
						];
						$tmp_where = [
							'a.am' => $aval['am'],
							'a.appvr_at !=' => null,
							'a.produk_grup' => get('pgroup'),
						];
						if(user('id_group') == MR_ROLE_ID){
							$tmp_where['a.mr'] = user('username');
						}

						//select sum plan call uniq dokter and mr
						// (select count(*) from trxvisit_'.get('tahun').'_'.get('bulan').' where mr = a.mr and produk_grup = "'.get('pgroup').'" and status = 3) as plan_percent_coverage,
						$mr = get_data('trxvisit_'.get('tahun').'_'.get('bulan').' a', [
							'select' => 'mr.nama as nama_mr, 
								(select sum(plan_call) from trxvisit_'.get('tahun').'_'.get('bulan').' where mr = a.mr and produk_grup = "'.get('pgroup').'" and status = 3) as plan_call, 
								(select count(*) from trxdfr_'.get('tahun').'_'.get('bulan').' where mr = a.mr and produk_grup = "'.get('pgroup').'" and status = 2) as actual_call,
								(select count(*) from trxvisit_'.get('tahun').'_'.get('bulan').' where plan_call > 0 and status = 3 and mr = a.mr and produk_grup = "'.get('pgroup').'") as plan_dokter_coverage,
								(select count(distinct dokter) from trxdfr_'.get('tahun').'_'.get('bulan').' where mr = a.mr and produk_grup = "'.get('pgroup').'" and status = 2)as actual_dokter_coverage,
								(select count(*) from trxvisit_'.get('tahun').'_'.get('bulan').' where plan_call > 0 and status = 3 and mr = a.mr and produk_grup = "'.get('pgroup').'") as plan_percent_coverage,
								(
									select count(*) from trxvisit_'.get('tahun').'_'.get('bulan').'
									where trxvisit_'.get('tahun').'_'.get('bulan').'.mr = a.mr and trxvisit_'.get('tahun').'_'.get('bulan').'.produk_grup = "'.get('pgroup').'" and trxvisit_'.get('tahun').'_'.get('bulan').'.status = 3 
									and plan_call <= (select count(*) from trxdfr_'.get('tahun').'_'.get('bulan').' where mr = a.mr and produk_grup = "'.get('pgroup').'" and status = 2 and dokter = trxvisit_'.get('tahun').'_'.get('bulan').'.dokter)
								) as actual_percent_coverage',
							'join' => [
								'tbl_user mr on mr.username = a.mr',
							],
							'where' => $tmp_where,
							'group_by' => 'a.mr',
							'sort_by' => 'mr.nama',
							'sort' => 'ASC',
						])->result_array();
						if(!$mr) continue;
						foreach($mr as $mkey => $mval){
							echo '<tr style="background-color:#b4d6c1">';
							echo '<td>'.($mkey+1).'</td>';
							echo '<td>'.$mval['nama_mr'].' (MR)</td>';
							echo '<td>'.$nama_region.'</td>';
							echo '<td>'.$mval['plan_call'].'</td>';
							echo '<td>'.$mval['actual_call'].'</td>';
							if($mval['actual_call'] > 0 && $mval['plan_call'] > 0){
								$percent_call = round(($mval['actual_call'] / $mval['plan_call']) * 100,1);
							} else {
								$percent_call = 0;
							}
							echo '<td>'.($percent_call > 100 ? 100 : $percent_call).' %</td>';
							echo '<td>'.$mval['plan_dokter_coverage'].'</td>';
							echo '<td>'.$mval['actual_dokter_coverage'].'</td>';
							if($mval['actual_dokter_coverage'] > 0 && $mval['plan_dokter_coverage'] > 0){
								$percent_dokter_coverage = round(($mval['actual_dokter_coverage']/$mval['plan_dokter_coverage'])*100, 1);
							} else {
								$percent_dokter_coverage = 0;
							}
							echo '<td>'.($percent_dokter_coverage > 100 ? 100 : $percent_dokter_coverage).' %</td>';
							echo '<td>'.$mval['plan_percent_coverage'].'</td>';
							echo '<td>'.$mval['actual_percent_coverage'].'</td>';
							if($mval['actual_percent_coverage'] > 0 && $mval['plan_percent_coverage'] > 0){
								$percent_percent_coverage = round(($mval['actual_percent_coverage']/$mval['plan_percent_coverage'])*100, 1);
							} else {
								$percent_percent_coverage = 0;
							}
							echo '<td>'.($percent_percent_coverage > 100 ? 100 : $percent_percent_coverage).' %</td>';
							echo '</tr>';
							$am_counter['plan_call'] += $mval['plan_call'];
							$am_counter['actual_call'] += $mval['actual_call'];
							$am_counter['plan_dokter_coverage'] += $mval['plan_dokter_coverage'];
							$am_counter['actual_dokter_coverage'] += $mval['actual_dokter_coverage'];
							$am_counter['plan_percent_coverage'] += $mval['plan_percent_coverage'];
							$am_counter['actual_percent_coverage'] += $mval['actual_percent_coverage'];
						}
						echo '<tr style="background-color:#8dc3a7">';
						echo '<td>'.($akey+1).'</td>';
						echo '<td>'.$aval['nama_am'].'(AM)</td>';
						echo '<td>'.$nama_region.'</td>';
						echo '<td>'.$am_counter['plan_call'].'</td>';
						echo '<td>'.$am_counter['actual_call'].'</td>';
						if($am_counter['actual_call'] > 0 && $am_counter['plan_call'] > 0){
							$percent_call = round(($am_counter['actual_call'] / $am_counter['plan_call']) * 100,1);
						} else {
							$percent_call = 0;
						}
						echo '<td>'.($percent_call > 100 ? 100 : $percent_call).' %</td>';
						echo '<td>'.$am_counter['plan_dokter_coverage'].'</td>';
						echo '<td>'.$am_counter['actual_dokter_coverage'].'</td>';
						if($am_counter['actual_dokter_coverage'] > 0 && $am_counter['plan_dokter_coverage'] > 0){
							$percent_dokter_coverage = round(($am_counter['actual_dokter_coverage']/$am_counter['plan_dokter_coverage'])*100, 1);
						} else {
							$percent_dokter_coverage = 0;
						}
						echo '<td>'.($percent_dokter_coverage > 100 ? 100 : $percent_dokter_coverage).' %</td>';
						echo '<td>'.$am_counter['plan_percent_coverage'].'</td>';
						echo '<td>'.$am_counter['actual_percent_coverage'].'</td>';
						if($am_counter['actual_percent_coverage'] > 0 && $am_counter['plan_percent_coverage'] > 0){
							$percent_percent_coverage = round(($am_counter['actual_percent_coverage']/$am_counter['plan_percent_coverage'])*100, 1);
						} else {
							$percent_percent_coverage = 0;
						}
						echo '<td>'.($percent_percent_coverage > 100 ? 100 : $percent_percent_coverage).' %</td>';
						echo '</tr>';
						$rm_counter['plan_call'] += $am_counter['plan_call'];
						$rm_counter['actual_call'] += $am_counter['actual_call'];
						$rm_counter['plan_dokter_coverage'] += $am_counter['plan_dokter_coverage'];
						$rm_counter['actual_dokter_coverage'] += $am_counter['actual_dokter_coverage'];
						$rm_counter['plan_percent_coverage'] += $am_counter['plan_percent_coverage'];
						$rm_counter['actual_percent_coverage'] += $am_counter['actual_percent_coverage'];
					}
					if($rm_counter['plan_call'] <= 0) continue;
					echo '<tr style="background-color:#6baf92">';
					echo '<td>'.($key+1).'</td>';
					echo '<td>'.$val['nama_rm'].' (RM)</td>';
					echo '<td>'.$nama_region.'</td>';
					echo '<td>'.$rm_counter['plan_call'].'</td>';
					echo '<td>'.$rm_counter['actual_call'].'</td>';
					if($rm_counter['actual_call'] > 0 && $rm_counter['plan_call'] > 0){
						$percent_call = round(($rm_counter['actual_call'] / $rm_counter['plan_call']) * 100,1);
					} else {
						$percent_call = 0;
					}
					echo '<td>'.($percent_call > 100 ? 100 : $percent_call).' %</td>';
					echo '<td>'.$rm_counter['plan_dokter_coverage'].'</td>';
					echo '<td>'.$rm_counter['actual_dokter_coverage'].'</td>';
					if($rm_counter['actual_dokter_coverage'] > 0 && $rm_counter['plan_dokter_coverage'] > 0){
						$percent_dokter_coverage = round(($rm_counter['actual_dokter_coverage']/$rm_counter['plan_dokter_coverage'])*100, 1);
					} else {
						$percent_dokter_coverage = 0;
					}
					echo '<td>'.($percent_dokter_coverage > 100 ? 100 : $percent_dokter_coverage).' %</td>';
					echo '<td>'.$rm_counter['plan_percent_coverage'].'</td>';
					echo '<td>'.$rm_counter['actual_percent_coverage'].'</td>';
					if($rm_counter['actual_percent_coverage'] > 0 && $rm_counter['plan_percent_coverage'] > 0){
						$percent_percent_coverage = round(($rm_counter['actual_percent_coverage']/$rm_counter['plan_percent_coverage'])*100, 1);
					} else {
						$percent_percent_coverage = 0;
					}
					echo '<td>'.($percent_percent_coverage > 100 ? 100 : $percent_percent_coverage).' %</td>';
					echo '</tr>';
					$nasional_counter['plan_call'] += $rm_counter['plan_call'];
					$nasional_counter['actual_call'] += $rm_counter['actual_call'];
					$nasional_counter['plan_dokter_coverage'] += $rm_counter['plan_dokter_coverage'];
					$nasional_counter['actual_dokter_coverage'] += $rm_counter['actual_dokter_coverage'];
					$nasional_counter['plan_percent_coverage'] += $rm_counter['plan_percent_coverage'];
					$nasional_counter['actual_percent_coverage'] += $rm_counter['actual_percent_coverage'];
				}
				echo '<tr>';
				echo '<th></th>';
				echo '<th>National</th>';
				echo '<th> -- </th>';
				echo '<th>'.$nasional_counter['plan_call'].'</th>';
				echo '<th>'.$nasional_counter['actual_call'].'</th>';
				if($nasional_counter['actual_call'] > 0 && $nasional_counter['plan_call'] > 0){
					$percent_call = round(($nasional_counter['actual_call'] / $nasional_counter['plan_call']) * 100,1);
				} else {
					$percent_call = 0;
				}
				echo '<th>'.($percent_call > 100 ? 100 : $percent_call).' %</th>';
				echo '<th>'.$nasional_counter['plan_dokter_coverage'].'</th>';
				echo '<th>'.$nasional_counter['actual_dokter_coverage'].'</th>';
				if($nasional_counter['actual_dokter_coverage'] > 0 && $nasional_counter['plan_dokter_coverage'] > 0){
					$percent_dokter_coverage = round(($nasional_counter['actual_dokter_coverage']/$nasional_counter['plan_dokter_coverage'])*100, 1);
				} else {
					$percent_dokter_coverage = 0;
				}
				echo '<th>'.($percent_dokter_coverage > 100 ? 100 : $percent_dokter_coverage).' %</th>';
				echo '<th>'.$nasional_counter['plan_percent_coverage'].'</th>';
				echo '<th>'.$nasional_counter['actual_percent_coverage'].'</th>';
				if($nasional_counter['actual_percent_coverage'] > 0 && $nasional_counter['plan_percent_coverage'] > 0){
					$percent_percent_coverage = round(($nasional_counter['actual_percent_coverage']/$nasional_counter['plan_percent_coverage'])*100, 1);
				} else {
					$percent_percent_coverage = 0;
				}
				echo '<th>'.($percent_percent_coverage > 100 ? 100 : $percent_percent_coverage).' %</th>';
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
			location.replace(base_url + 'report/call_activity_national?pgroup='+pgroup+'&bulan='+bulan+'&tahun='+tahun);
		}
	}
</script>