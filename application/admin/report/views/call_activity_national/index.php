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
					echo '<option value="' . $val['kode'] . '" ' . (get('produk_grup') == $val['kode'] ? 'selected="selected"' : '') . '>' . $val['nama'] . '</option>';
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
			<button type="button" class="btn btn-fresh btn-sm btn-export"><i class="fa-download mr-2"></i> Export</button>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	if (get('produk_grup') != '' && get('bulan') != '' && get('tahun') != '') :
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
				<?php if ($data) : ?>
					<?php foreach ($data['child'] as $knsm => $nsm) : 
						$region = '';
						if($nsm['nama'] == 'SUKISTYOWATI')	{
							$region = 'EAST';
						} else {
							$region = 'WEST';
						}
					?>
						<?php foreach ($nsm['child'] as $kam => $am) : ?>
							<?php foreach ($am['child'] as $kmr => $mr) : ?>
								<tr style="background-color:#b4d6c1">
									<td><?= $kmr + 1 ?></td>
									<td><?= $mr['nama'] ?> (MR)</td>
									<td><?= $region ?></td>
									<td><?= $mr['plan_call'] ?></td>
									<td><?= $mr['actual_call'] ?></td>
									<td><?= ($mr['plan_call'] > 0 && $mr['actual_call'] > 0 ? round($mr['actual_call'] / $mr['plan_call'], 2) : 0) . ' %' ?></td>
									<td><?= $mr['dc_plan'] ?></td>
									<td><?= $mr['dc_actual'] ?></td>
									<td><?= ($mr['dc_plan'] > 0 && $mr['dc_actual'] > 0 ? round($mr['dc_actual'] / $mr['dc_plan'], 2) : 0) . ' %' ?></td>
									<td><?= $mr['pc_plan'] ?></td>
									<td><?= $mr['pc_actual'] ?></td>
									<td><?= ($mr['pc_plan'] > 0 && $mr['pc_actual'] > 0 ? round($mr['pc_actual'] / $mr['pc_plan'], 2) : 0) . ' %' ?></td>
								</tr>
							<?php endforeach; ?>
							<tr style="background-color:#8dc3a7">
								<td><?= $kam + 1 ?></td>
								<td><?= $am['nama'] ?> (AM)</td>
								<td><?= $region ?></td>
								<td><?= $am['plan_call'] ?></td>
								<td><?= $am['actual_call'] ?></td>
								<td><?= ($am['plan_call'] > 0 && $am['actual_call'] > 0 ? round($am['actual_call'] / $am['plan_call'], 2) : 0) . ' %' ?></td>
								<td><?= $am['dc_plan'] ?></td>
								<td><?= $am['dc_actual'] ?></td>
								<td><?= ($am['dc_plan'] > 0 && $am['dc_actual'] > 0 ? round($am['dc_actual'] / $am['dc_plan'], 2) : 0) . ' %' ?></td>
								<td><?= $am['pc_plan'] ?></td>
								<td><?= $am['pc_actual'] ?></td>
								<td><?= ($am['pc_plan'] > 0 && $am['pc_actual'] > 0 ? round($am['pc_actual'] / $am['pc_plan'], 2) : 0) . ' %' ?></td>
							</tr>
						<?php endforeach; ?>
						<tr style="background-color:#6baf92">
							<td><?= $knsm + 1 ?></td>
							<td><?= $nsm['nama'] ?> (NSM)</td>
							<td><?= $region ?></td>
							<td><?= $nsm['plan_call'] ?></td>
							<td><?= $nsm['actual_call'] ?></td>
							<td><?= ($nsm['plan_call'] > 0 && $nsm['actual_call'] > 0 ? round($nsm['actual_call'] / $nsm['plan_call'], 2) : 0) . ' %' ?></td>
							<td><?= $nsm['dc_plan'] ?></td>
							<td><?= $nsm['dc_actual'] ?></td>
							<td><?= ($nsm['dc_plan'] > 0 && $nsm['dc_actual'] > 0 ? round($nsm['dc_actual'] / $nsm['dc_plan'], 2) : 0) . ' %' ?></td>
							<td><?= $nsm['pc_plan'] ?></td>
							<td><?= $nsm['pc_actual'] ?></td>
							<td><?= ($nsm['pc_plan'] > 0 && $nsm['pc_actual'] > 0 ? round($nsm['pc_actual'] / $nsm['pc_plan'], 2) : 0) . ' %' ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				<tr>
					<td colspan="3" class="text-right"> NATIONAL </td>
					<td><?= $data['plan_call'] ?></td>
					<td><?= $data['actual_call'] ?></td>
					<td><?= ($data['plan_call'] > 0 && $data['actual_call'] > 0 ? round($data['actual_call'] / $data['plan_call'], 2) : 0) . ' %' ?></td>
					<td><?= $data['dc_plan'] ?></td>
					<td><?= $data['dc_actual'] ?></td>
					<td><?= ($data['dc_plan'] > 0 && $data['dc_actual'] > 0 ? round($data['dc_actual'] / $data['dc_plan'], 2) : 0) . ' %' ?></td>
					<td><?= $data['pc_plan'] ?></td>
					<td><?= $data['pc_actual'] ?></td>
					<td><?= ($data['pc_plan'] > 0 && $data['pc_actual'] > 0 ? round($data['pc_actual'] / $data['pc_plan'], 2) : 0) . ' %' ?></td>
				</tr>
			</tbody>
			</thead>
		</table>
	<?php else : ?>
		<div class="text-center">
			<img src="<?= base_url('assets/images/no-data.svg') ?>" width="40%">
			<h3> Filter untuk melihat data </h3>
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
			location.replace(base_url + 'report/call_activity_national?produk_grup=' + pgroup + '&bulan=' + bulan + '&tahun=' + tahun);
		}
	}

	$(document).on('click', '.btn-export', function(){
		let produk_group = $('#fpgroup').val()
		let bulan = $('#fbulan').val()
		let tahun = $('#ftahun').val()

		location.href = base_url + 'report/call_activity_national/export?produk_grup=' + produk_group + '&bulan=' + bulan + '&tahun=' + tahun

	})

</script>