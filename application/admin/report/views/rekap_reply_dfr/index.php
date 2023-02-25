<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
		<div class="float-right">
			<label for="fbulan"> Bulan </label>
			<select class="select2 infinity" id="fbulan" style="width: 100px;" onchange="filter()">
				<option value=""> Pilih Bulan </option>	
				<option value="01" <?= get('bulan') == '01' ? 'selected="selected"' : '' ?>>Januari</option>
				<option value="02" <?= get('bulan') == '02' ? 'selected="selected"' : '' ?>>Februari</option>
				<option value="03" <?= get('bulan') == '03' ? 'selected="selected"' : '' ?>>Maret</option>
				<option value="04" <?= get('bulan') == '04' ? 'selected="selected"' : '' ?>>April</option>
				<option value="05" <?= get('bulan') == '05' ? 'selected="selected"' : '' ?>>Mei</option>
				<option value="06" <?= get('bulan') == '06' ? 'selected="selected"' : '' ?>>Juni</option>
				<option value="07" <?= get('bulan') == '07' ? 'selected="selected"' : '' ?>>Juli</option>
				<option value="08" <?= get('bulan') == '08' ? 'selected="selected"' : '' ?>>Agustus</option>
				<option value="09" <?= get('bulan') == '09' ? 'selected="selected"' : '' ?>>September</option>
				<option value="10" <?= get('bulan') == '10' ? 'selected="selected"' : '' ?>>Oktober</option>
				<option value="11" <?= get('bulan') == '11' ? 'selected="selected"' : '' ?>>November</option>
				<option value="12" <?= get('bulan') == '12' ? 'selected="selected"' : '' ?>>Desember</option>
			</select>
			<label for="ftahun"> Tahun </label>
			<select class="select2 infinity" id="ftahun" style="width: 100px;" onchange="filter()">
				<?php
				for ($i = date('Y'); $i >= 2018; $i--) {
					echo '<option value="' . $i . '" ' . (get('tahun') == $i ? 'selected="selected"' : '') . '>' . $i . '</option>';
				}
				?>
			</select>
			<!-- <label for="fteam"> Team </label>
			<select class="select2 form-control" name="fteam" id="fteam">
				<?php
				$where = [];
				if (in_array(user('id_group'), [AM_ROLE_ID, MR_ROLE_ID])) {
					if ($this->session->userdata('team')) {
						$tmp_team = [];
						foreach ($this->session->userdata('team') as $val) {
							array_push($tmp_team, $val['kode_team']);
						}
						$where['kode'] = $tmp_team;
					}
				}
				$where['divisi'] = 'E';
				foreach (get_data('tim', [
					'where' => $where
				])->result_array() as $val) { ?>
					<option value="<?php echo $val['kode']; ?>" <?=get('team') == $val['kode'] ? 'selected="selected"' : ''?>><?php echo $val['nama'] ?></option>
				<?php } ?>
			</select> -->
			<label for="fpgroup"> Produk Group </label>
			<select class="select2 form-control" name="fpgroup" id="fpgroup" onchange="filter()">
				<?php
				if (user('id_group') == 1) {
					$produk = get_data('produk_grup', 'is_active', 1)->result_array();
				} else {
					$produk = $this->session->userdata('produk_group');
				}
				foreach ($produk as $val) {
					echo '<option value="' . $val['kode'] . '" '.($val['kode'] == get('pgroup') ? 'selected="selected"' : '').'>' . $val['nama'] . '</option>';
				} ?>
			</select>
			<button class="btn btn-fresh btn-export"><i class="fa-download mr-2"></i> Export</button>
		</div>
	</div>
</div>
<div class="content-body">
	<!-- <div class="main-container"> -->
		<?php
			if(get('bulan') != '' && get('tahun') != '' && get('pgroup') != '' && $data){ ?>
				<table class="table-app table-bordered table-sm table-hover" style="width: 100%; text-align:center">
					<thead>
						<tr>
							<th>No.</th>
							<th>Name</th>
							<th>Region</th>
							<th class="text-center">Reply DFR AM</th>
							<th class="text-center">Reply DFR RM</th>
						</tr>
						<tbody>
						<?php
							foreach($data['child'] as $k => $v){
								$region = 'EAST';
								if($v['nama'] == 'DERI SYOFYAN'){
									$region = 'WEST';
								}
								foreach($v['child'] as $ck => $cv){
									foreach($cv['child'] as $cck => $ccv){
										echo '<tr style="background-color:#b4d6c1">';
											echo '<td>'.($cck+1).'</td>';
											echo '<td>'.($ccv['nama']).' (MR) </td>';
											echo '<td>'.$region.'</td>';
											echo '<td>'.($ccv['reply_am']).' </td>';
											echo '<td>'.($ccv['reply_nsm']).' </td>';
										echo '</tr>';
									}
									echo '<tr style="background-color:#8dc3a7">';
										echo '<td>'.($ck+1).'</td>';
										echo '<td>'.($cv['nama']).' (AM) </td>';
										echo '<td>'.$region.'</td>';
										echo '<td>'.($cv['reply_am']).' </td>';
										echo '<td>'.($cv['reply_nsm']).' </td>';
									echo '</tr>';
								}
								echo '<tr style="background-color:#6baf92">';
									echo '<td>'.($k+1).'</td>';
									echo '<td>'.($v['nama']).' (NSM) </td>';
									echo '<td>'.$region.'</td>';
									echo '<td>'.($v['reply_am']).' </td>';
									echo '<td>'.($v['reply_nsm']).' </td>';
								echo '</tr>';
							}
						?>
						</tbody>
					</thead>
				</table>
		<?php
			} else {
				echo '<div class="text-center">
					<img src="'.base_url().'assets/images/no-data.svg" width="40%">
					<h3> Filter Untuk Melihat Hasil </h3> 
				</div>';
			}
		?>
	<!-- </div> -->
</div>
<script type="text/javascript">
	
	function filter(){
		if($('#fbulan').val() != '' && $('#ftahun').val() != '' && $('#fpgroup').val() != ''){
			location.replace('<?php echo base_url('report/rekap_reply_dfr'); ?>?bulan=' + $('#fbulan').val() + '&tahun=' + $('#ftahun').val() + '&pgroup=' + $('#fpgroup').val());
		}
	}

	$(document).ready(function() {
		var fteam = $('#fteam').val();
		get_produk_grup(fteam);
	})

	$(document).on('change', '#fteam', function() {
		get_produk_grup($(this).val());
	})

	function get_produk_grup(fteam) {
		$.ajax({
			url: base_url + 'report/rekap_reply_dfr/get_produk_grup/' + fteam,
			type: 'post',
			data: {
				fteam: fteam
			},
			success: function(resp) {
				var produk_grup = resp.produk_grup
				var html = '<option value=""></option>'
				$.each(produk_grup, function(i, v) {
					html += '<option value="' + v.id + '">' + v.nama + '</option>'
				})
				$('#produk_grup').html(html)
				if (data_tmp.produk_grup != '') {
					$('#produk_grup').val(data_tmp.produk_grup).trigger('change')
				}
			}
		})
	}

	$(document).on('click', '.btn-export', function(){
		let bulan = $('#fbulan').val()
		let tahun = $('#ftahun').val()
		let produk_group = $('#fpgroup').val()

		location.href = base_url + 'report/rekap_reply_dfr/export?bulan='+bulan+'&tahun='+tahun+'&produk_group='+produk_group
	})
</script>