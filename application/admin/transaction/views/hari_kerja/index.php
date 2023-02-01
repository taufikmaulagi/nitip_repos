<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
		<div class="float-right">
			<select class="select2" id="bulan" onchange="filter()">
				<option value="">Pilih Bulan</option>
				<?php for($i=1;$i<=12;$i++): 
					$nama_bulan = '';
					switch ($i) {
						case 1:
							$nama_bulan = 'Januari';
							break;
						case 2:
							$nama_bulan = 'Februari';
							break;
						case 3:
							$nama_bulan = 'Maret';
							break;
						case 4:
							$nama_bulan = 'April';
							break;
						case 5:
							$nama_bulan = 'Mei';
							break;
						case 6:
							$nama_bulan = 'Juni';
							break;
						case 7:
							$nama_bulan = 'Juli';
							break;
						case 8:
							$nama_bulan = 'Agustus';
							break;
						case 9:
							$nama_bulan = 'September';
							break;
						case 10:
							$nama_bulan = 'Oktober';
							break;
						case 11:
							$nama_bulan = 'November';
							break;
						case 12:
							$nama_bulan = 'Desember';
							break;
					}
					echo '<option value="'.sprintf('%02d',$i).'" '.(get('bulan') == sprintf('%02d', $i) ? 'selected="selected"' : '').'>'.$nama_bulan.'</option>';
				endfor; ?>
			</select>
		</div>
	</div>
</div>
<div class="content-body">
	<table class="table-app table-bordered table-sm table-hover" style="width: 100%">
		<thead>
			<tr>
				<th style="width: 20%">Name</th>
				<th>Hari kerja</th>
            </tr>
			<tbody>
				<?php $bulan = get('bulan') ? get('bulan') : date('m'); ?>
			<?php if(user('id_group') == 8):?>
				<tr> 
					<th> HARI KERJA AM </th>
					<th> <input type ="text" class="form-control input-hari-kerja" value="<?=$hari_kerja_ku?>" data-user="<?=user('username')?>"></th>
				</tr>
				<?php
					
					$user = get_data('history_organogram_detail hod', [
						'select' => 'hod.nama_mr as nama, hod.n_mr, hk.jumlah',
						'join' => [
							'history_organogram ho on ho.id = hod.id_history_organogram',
							'jumlah_hari_kerja hk on hk.user = hod.n_mr and hk.bulan = '.$bulan.' type left'
						],
						'where' => [
							'ho.tanggal_end' => '0000-00-00',
							'hod.n_am' => user('username'),
							'hod.n_mr !=' => '',
						],
						'group_by' => 'hod.n_mr'
					])->result_array();
					// debug($user); die;
					foreach($user as $v){
						echo '<tr>';
							echo '<td>'.$v['nama'].'</td>';
							echo '<td><input type ="number" class="form-control input-hari-kerja" value="'.$v['jumlah'].'" data-user="'.$v['n_mr'].'"></td>';
						echo '</tr>';
					}
				?>
			<?php elseif(user('id_group') == 7): ?>
				<tr> 
					<th> HARI KERJA RM </th>
					<th> <input type ="text" class="form-control input-hari-kerja" value="<?=$hari_kerja_ku?>"  data-user="<?=user('username')?>"></th>
				</tr>
				<?php
					$user = get_data('history_organogram_detail hod', [
						'select' => 'hod.nama_am as nama, hod.n_am, hk.jumlah',
						'join' => [
							'history_organogram ho on ho.id = hod.id_history_organogram',
							'jumlah_hari_kerja hk on hk.user = hod.n_am and hk.bulan = '.$bulan.' type left'
						],
						'where' => [
							'ho.tanggal_end' => '0000-00-00',
							'hod.n_rm' => user('username'),
							'hod.n_am !=' => ''
						],
						'group_by' => 'hod.n_am'
					])->result_array();
					foreach($user as $rv){
						echo '<tr>';
							echo '<th>'.$rv['nama'].'</th>';
							echo '<th><input type ="text" class="form-control input-hari-kerja" value="'.$rv['jumlah'].'" readonly></th>';
						echo '</tr>';
						$user_2 = get_data('history_organogram_detail hod', [
							'select' => 'hod.nama_mr as nama, hod.n_mr, hk.jumlah',
							'join' => [
								'history_organogram ho on ho.id = hod.id_history_organogram',
								'jumlah_hari_kerja hk on hk.user = hod.n_mr and hk.bulan = '.$bulan.' type left'
							],
							'where' => [
								'ho.tanggal_end' => '0000-00-00',
								'hod.n_am' => $rv['n_am'],
								'hod.n_am !=' => '',
								'hod.n_mr !=' => ''
							],
							'group_by' => 'hod.n_mr'
						])->result_array();
						foreach($user_2 as $v){
							echo '<tr>';
								echo '<td>'.$v['nama'].'</td>';
								echo '<td><input type ="text" class="form-control input-hari-kerja" value="'.$v['jumlah'].'" readonly></td>';
							echo '</tr>';
						}
					}
					?>
			<?php endif; ?>
			</tbody>
		</thead>
	</table>
</div>

<script>
	function filter(){
		let bulan = $('#bulan').val()
		location.replace(base_url + 'transaction/hari_kerja?bulan='+bulan)
	}

	$(document).on('keyup','.input-hari-kerja', function(){
		let val = $(this).val()
		let nip = $(this).attr('data-user')

		$.ajax({
			url: base_url + 'transaction/hari_kerja/save',
			type: 'post',
			data: {
				nip: nip,
				val: val,
				bulan: '<?=$bulan?>'
			},
			success: function(r){
				console.log('its okay u dont have to worry about this')
			}
		})
	})
</script>