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
	<?php
		if(get('produk') != '' && get('bulan') != '' && get('tahun') != ''):
			$tim = get_data('tim', [
				'select' => 'tim.*',
				'join' => [
					'produk_grup on produk_grup.kode_team = tim.kode'
				],
				'where' => [
					'produk_grup.kode' => get('produk')
				]
			])->row_array();

			$produk = get_data('produk_grup', [
				'select' => 'produk_grup.*',
				'join' => [
					'tim on tim.kode = produk_grup.kode_team'
				],
				'where' => [
					'tim.grup' => $tim['grup'],
					'produk_grup.kode !=' => get('produk')
				]
			])->result_array();

			$current_produk = get_data('produk_grup', 'kode', get('produk'))->row_array();
	?>
	<table class="table-app table-bordered table-sm table-hover" style="width: 100%; text-align:center">
		<thead>
			<tr>
				<th rowspan="2">No.</th>
				<th rowspan="2">Name</th>
				<th rowspan="2">Region</th>
				<th class="text-center" colspan="1">P1</th>
                <th class="text-center" colspan="<?=count($produk)?>">P2</th>
                <th class="text-center" colspan="<?=count($produk)?>">P3</th>
            </tr>
            <tr>
				<th class="text-center"><?=$current_produk['nama']?></th>
				<?php for($i=2;$i<=3;$i++): ?>
					<?php foreach($produk as $v): ?>
						<th class="text-center"><?=$v['nama']?></th>
					<?php endforeach; ?>
				<?php endfor; ?>
            </tr>
			<tbody>
			<?php
				$nasional_counter = [
					1 => 0,
					2 => [],
					3 => [],
				];
				for($i=2;$i<=3;$i++):
					foreach($produk as $v):
						$nasional_counter[$i][$v['nama']] = 0;
					endforeach;
				endfor;

				$tmp_where = [
					'produk_grup' => get('produk'),
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
						1 => 0,
						2 => [],
						3 => [],
					];
					for($i=2;$i<=3;$i++):
						foreach($produk as $v):
							$rm_counter[$i][$v['nama']] = 0;
						endforeach;
					endfor;
					$tmp_where = [
						'produk_grup' => get('produk'),
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
							1 => 0,
							2 => [],
							3 => [],
						];
						for($i=2;$i<=3;$i++):
							foreach($produk as $v):
								$am_counter[$i][$v['nama']] = 0;
							endforeach;
						endfor;
						$tmp_where = [
							'a.am' => $aval['am'],
							'a.appvr_at !=' => null,
							'a.produk_grup' => get('produk'),
						];
						if(user('id_group') == MR_ROLE_ID){
							$tmp_where['a.mr'] = user('username');
						}

						//select sum plan call uniq dokter and mr
						$mr_select = 'SUM(CASE WHEN a.produk_grup = "'.get('produk').'" THEN 1 END) as P1,';
						for($i=2;$i<=3;$i++):
							foreach($produk as $v):
								$mr_select .= 'COUNT(CASE WHEN a.produk'.$i.' = "'.$v['kode'].'" THEN 1 END) as P'.$i.'_'.$v['nama'].',';
							endforeach;
						endfor;
						
						$mr = get_data('trxdfr_'.get('tahun').'_'.get('bulan'). ' a', [
							'select' => $mr_select.'mr.nama as nama_mr',
							'join' => [
								'tbl_user mr on mr.kode = a.mr',
							],
							'where' => [
								'produk_grup' => get('produk'),
								'a.am' => $aval['am'],
								'a.status' => 2
							],
							'group_by' => 'mr.kode',
							'sort_by' => 'mr.nama'
						])->result_array();
						if(!$mr) continue;
						foreach($mr as $mkey => $mval){
							echo '<tr style="background-color:#b4d6c1">';
							echo '<td>'.($mkey+1).'</td>';
							echo '<td>'.$mval['nama_mr'].' (MR)</td>';
							echo '<td>'.$nama_region.'</td>';
							echo '<td>'.$mval['P1'].'</td>';
							$am_counter[1] += $mval['P1'];
							for($i=2;$i<=3;$i++):
								foreach($produk as $v):
									echo '<td>'.$mval['P'.$i.'_'.$v['nama']].'</td>';
									$am_counter[$i][$v['nama']] += $mval['P'.$i.'_'.$v['nama']];
								endforeach;
							endfor;
							echo '</tr>';
						}
						echo '<tr style="background-color:#8dc3a7">';
						echo '<td>'.($akey+1).'</td>';
						echo '<td>'.$aval['nama_am'].'(AM)</td>';
						echo '<td>'.$nama_region.'</td>';
						echo '<td>'.$am_counter[1].'</td>';
							$rm_counter[1] += $am_counter[1];
							for($i=2;$i<=3;$i++):
								foreach($produk as $v):
									echo '<td>'.$am_counter[$i][$v['nama']].'</td>';
									$rm_counter[$i][$v['nama']] += $am_counter[$i][$v['nama']];
								endforeach;
							endfor;
						echo '</tr>';
					}
					echo '<tr style="background-color:#6baf92">';
					echo '<td>'.($key+1).'</td>';
					echo '<td>'.$val['nama_rm'].' (RM)</td>';
					echo '<td>'.$nama_region.'</td>';
					echo '<td>'.$rm_counter[1].'</td>';
						$nasional_counter[1] += $rm_counter[1];
						for($i=2;$i<=3;$i++):
							foreach($produk as $v):
								echo '<td>'.$rm_counter[$i][$v['nama']].'</td>';
								$nasional_counter[$i][$v['nama']] += $rm_counter[$i][$v['nama']];
							endforeach;
						endfor;
					echo '</tr>';
				}
				echo '<tr>';
				echo '<th></th>';
				echo '<th>National</th>';
				echo '<th> -- </th>';
				echo '<td>'.$nasional_counter[1].'</td>';
					for($i=2;$i<=3;$i++):
						foreach($produk as $v):
							echo '<td>'.$nasional_counter[$i][$v['nama']].'</td>';
						endforeach;
					endfor;
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
	
	function filter(){
		let produk_grup = $('#fproduk').val()
		let tahun = $('#ftahun').val()
		let bulan = $('#fbulan').val()
		if(produk_grup != '' && tahun != '' && bulan != ''){
			location.replace(base_url + 'report/share_of_voice?produk='+produk_grup+'&tahun='+tahun+'&bulan='+bulan)
		}
	}
</script>