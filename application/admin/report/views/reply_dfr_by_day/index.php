<style>
	.table-responsive {
		height: 600px;
		overflow: scroll;
	}

	thead tr:nth-child(1) th {
		background: white;
		position: sticky;
		top: 0;
		z-index: 10;
	}
</style>
<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix float-right">
			<label>Team Group</label>
			<select class="select2 infinity" id="fteamgroup" style="width: 100px;" onchange="filter()">
				<?php
				foreach (get_data('tim_grup')->result_array() as $val) {
					echo '<option value="' . $val['id'] . '" ' . ($val['id'] == get('team_group') ? 'selected="selected"' : '') . '>' . $val['nama'] . '</option>';
				}
				?>
			</select>
			<label>Bulan</label>
			<select class="select2 infinity" id="fbulan" style="width: 100px;" onchange="filter()">
				<option value="">Pilih Bulan</option>
				<?php
				for ($i = 1; $i <= 12; $i++) {
					switch ($i) {
						case 1:
							$bulan = 'Januari';
							break;
						case 2:
							$bulan = 'Februari';
							break;
						case 3:
							$bulan = 'Maret';
							break;
						case 4:
							$bulan = 'April';
							break;
						case 5:
							$bulan = 'Mei';
							break;
						case 6:
							$bulan = 'Juni';
							break;
						case 7:
							$bulan = 'Juli';
							break;
						case 8:
							$bulan = 'Agustus';
							break;
						case 9:
							$bulan = 'September';
							break;
						case 10:
							$bulan = 'Oktober';
							break;
						case 11:
							$bulan = 'November';
							break;
						case 12:
							$bulan = 'Desember';
							break;
					}
					echo '<option value="' . $i . '" ' . ($i == get('bulan') ? 'selected="selected"' : '') . '>' . $bulan . '</option>';
				}
				?>
			</select>
			<label>Tahun</label>
			<select class="select2 infinity" id="ftahun" style="width: 100px;" onchange="filter()">
				<?php
				for ($i = date('Y'); $i >= 2018; $i--) {
					echo '<option value="' . $i . '" ' . ($i == get('tahun') ? 'selected="selected"' : '') . '>' . $i . '</option>';
				}
				?>
			</select>
		</div>
	</div>
</div>
<div class="content-body">
	<div class="table-responsive">
	<?php
	if (get('tahun') != '' && get('bulan') != '' && get('team_group') != '') { ?>
		<table class="table-app table-bordered table-hover">
			<thead>
				<tr>
					<th rowspan="2" class="bg-secondary text-white">Tanggal</th>
					<?php
					$data_user = [];
					$produk_group = get_data('produk_grup', [
						'select' => 'produk_grup.*',
						'join' => [
							'tim on tim.kode = produk_grup.kode_team',
							'tim_grup on tim_grup.id = tim.grup'
						],
						'where' => [
							'tim_grup.id' => get('team_group')
						]
					])->result_array();
					$team = get_data('tim', [
						'where' => [
							'grup' => get('team_group')
						]
					])->result_array();
					$rm = get_data('history_organogram_detail a', [
						'select' => 'a.n_nsm, a.nama_nsm, c.id as id_user',
						'join' => [
							'history_organogram b on a.id_history_organogram = b.id',
							'tbl_user c on a.n_nsm = c.username'
						],
						'where' => [
							'b.tanggal_end' => '0000-00-00',
							'a.n_nsm !=' => '',
							'a.kode_team' => array_column($team, 'kode'),
						],
						'group_by' =>  'n_nsm'
					])->result_array();

					foreach ($rm as $vrm) {
						$am = get_data('history_organogram_detail a', [
							'select' => 'a.n_am, a.nama_am, c.id as id_user',
							'join' => [
								'history_organogram b on a.id_history_organogram = b.id',
								'tbl_user c on a.n_am = c.username'
							],
							'where_in' => [
								'a.kode_team' => array_column($team, 'kode'),
							],
							'where' => [
								'b.tanggal_end' => '0000-00-00',
								'a.n_nsm' => $vrm['n_nsm'],
								'a.n_am !=' => '',
							],
							'group_by' =>  'n_am'
						])->result_array();
						foreach ($am as $vam) {
							array_push($data_user, [
								'type' => 'AM',
								'nip' => $vam['n_am'],
								'id' => $vam['id_user'],
							]);
							echo '<th class="bg-info" colspan="' . (count($produk_group) + 1) . '">' . $vam['nama_am'] . ' (AM) </th>';
						}
						echo '<th class="bg-secondary text-white" colspan="' . (count($produk_group) + 1) . '"	>' . $vrm['nama_nsm'] . ' (NSM) </th>';
						array_push($data_user, [
							'type' => 'NSM',
							'nip' => $vrm['n_nsm'],
							'id' => $vrm['id_user'],
						]);
					}

					?>
				</tr>
				<tr>
					<?php
					foreach ($data_user as $v) {
						foreach ($produk_group as $pv) {
							echo '<th class="bg-success">' . $pv['nama'] . '</th>';
						}
						echo '<th style="background-color:#b4b4b4">Total</th>';
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$user = $data_user;
				$feedback = get_data('trxdfr_' . get('tahun') . '_' . str_pad(get('bulan'), 2, '0', STR_PAD_LEFT) . ' b', [
					'select' => 'count(*) as jumlah, a.user, date(a.cat) as tanggal, d.produk_grup',
					'join' => [
						'trxdfr_feedback_' . get('tahun') . '_' . str_pad(get('bulan'), 2, '0', STR_PAD_LEFT) . ' a on a.dfr = b.id',
						'trxvisit_' . get('tahun') . '_' . str_pad(get('bulan'), 2, '0', STR_PAD_LEFT) . ' c on c.id = b.visit_plan',
						'trxprof_' . get('tahun') . '_' . cycle_by_month(get('bulan')) . ' d on c.profiling = d.id',
					],
					'where' => [
						'month(a.cat)' => get('bulan'),
						'year(a.cat)' => get('tahun'),
						'a.user' => array_column($user, 'id')
					],
					'group_by' => 'a.user, date(a.cat), d.produk_grup',
				])->result_array();
				for ($i = date('Y-m-01', strtotime(get('tahun') . '-' . get('bulan') . '-01')); $i <= date('Y-m-t', strtotime(get('tahun') . '-' . get('bulan') . '-01')); $i = date('Y-m-d', strtotime($i . ' +1 day'))) {
					echo '<tr>';
					echo '<td>' . date('d-M', strtotime($i)) . '</td>';
					foreach ($user as $v) {
						$total = 0;
						foreach ($produk_group as $pv) {
							$nodata = true;
							foreach ($feedback as $fv) {
								if ($i == $fv['tanggal'] && $fv['produk_grup'] == $pv['kode'] && $fv['user'] == $v['id']) {
									echo '<td class="text-center">' . $fv['jumlah'] . '</td>';
									$nodata = false;
									$total += $fv['jumlah'];
								}
							}
							if ($nodata) {
								echo '<td class="text-center"> 0 </td>';
							}
						}
						echo '<td class="text-center" style="background-color:#b4b4b4"> ' . $total . '</td>';
					}
					echo '</tr>';
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th>Total</th>
					<?php

					foreach ($user as $v) {
						$all_total = 0;
						foreach ($produk_group as $pv) {
							$total = 0;
							foreach ($feedback as $fv) {
								if ($v['id'] == $fv['user'] && $pv['kode'] == $fv['produk_grup']) {
									$total += $fv['jumlah'];
								}
							}
							echo '<td class="text-center">' . $total . '</td>';
							$all_total += $total;
						}
						echo '<td class="text-center">' . $all_total . '</td>';
					}
					?>
				</tr>
			</tfoot>
		</table>
	<?php
	} else {
		echo '<div class="text-center">
					<img src="' . base_url() . 'assets/images/no-data.svg" width="40%">
					<h3> Filter Untuk Melihat Hasil </h3> 
				</div>';
	}
	?>
	</div>
</div>
<script>
	function filter() {
		var team_group = $('#fteamgroup').val();
		var bulan = $('#fbulan').val();
		var tahun = $('#ftahun').val();
		if (team_group != '' && bulan != '' && tahun != '') {
			window.location = '<?php echo base_url(); ?>report/reply_dfr_by_day?team_group=' + team_group + '&bulan=' + bulan + '&tahun=' + tahun;
		}
	}
</script>