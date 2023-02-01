<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="main-container">
		<div class="row">
			<div class="col-md-3 mb-3">
				<div class="card sticky-top">
					<div class="card-header">Filter</div>
					<div class="card-body">
						<form method="post" action="javascript:;" id="form-filter">
							<div class="form-group row">
								<label class="col-form-label col-12" for="periode"><?php echo lang('periode'); ?></label>
								<div class="col-7">
									<select class="select2 form-control" id="fbulan" name="fbulan">
										<option value="">Pilih Bulan</option>
										<option value="01" <?= (date('m') == '01' ?  'selected="selected"' : '') ?>>Januari</option>
										<option value="02" <?= (date('m') == '02' ?  'selected="selected"' : '') ?>>Februari</option>
										<option value="03" <?= (date('m') == '03' ?  'selected="selected"' : '') ?>>Maret</option>
										<option value="04" <?= (date('m') == '04' ?  'selected="selected"' : '') ?>>April</option>
										<option value="05" <?= (date('m') == '05' ?  'selected="selected"' : '') ?>>Mei</option>
										<option value="06" <?= (date('m') == '06' ?  'selected="selected"' : '') ?>>Juni</option>
										<option value="07" <?= (date('m') == '07' ?  'selected="selected"' : '') ?>>Juli</option>
										<option value="08" <?= (date('m') == '08' ?  'selected="selected"' : '') ?>>Agustus</option>
										<option value="09" <?= (date('m') == '09' ?  'selected="selected"' : '') ?>>September</option>
										<option value="10" <?= (date('m') == '10' ?  'selected="selected"' : '') ?>>Oktober</option>
										<option value="11" <?= (date('m') == '11' ?  'selected="selected"' : '') ?>>November</option>
										<option value="12" <?= (date('m') == '12' ?  'selected="selected"' : '') ?>>Desember</option>
									</select>
								</div>
								<div class="col-5">
									<select class="select2 form-control" name="ftahun" id="ftahun">
										<option value="">Pilih Tahun</option>
										<?php for ($i = date('Y'); $i >= 2018; $i--) { ?>
											<option value="<?php echo $i; ?>" <?php if ($i == date('Y')) echo ' selected'; ?>><?php echo $i; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-12" for="fteam">Team</label>
								<div class="col-12">
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
											<option value="<?php echo $val['kode']; ?>"><?php echo $val['nama'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-12" for="fpgroup">Product Group</label>
								<div class="col-12">
									<select class="select2 form-control" name="fpgroup" id="fpgroup">
										<?php
										if (user('id_group') == 1) {
											$produk = get_data('produk_grup', 'is_active', 1)->result_array();
										} else {
											$produk = $this->session->userdata('produk_group');
										}
										foreach ($produk as $val) {
											echo '<option value="' . $val['kode'] . '">' . $val['nama'] . '</option>';
										} ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-12" for="fam">Nama AM</label>
								<div class="col-12">
									<select class="select2 form-control" name="fam" id="fam">
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-12" for="fmr">Nama MR</label>
								<div class="col-12">
									<select class="select2 form-control" name="fmr" id="fmr">
									</select>
								</div>
							</div>
							<div class="form-group row mt-3">
								<div class="col-12">
									<button type="submit" class="btn btn-sky btn-block"><i class="fa fa-search"></i>&nbsp;Filter</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="card">
					<div class="card-header">History DFR</div>
					<div class="card-body" style="margin: 0; padding:0" id="result">
						<div class="text-center">
							<img src="<?= base_url('assets/images/no-data.svg') ?>" width="40%">
							<h3> Oops! Data Tidak Ditemukan :(</h3> 
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
modal_open('modal-form', 'Detail Call Activity', 'modal-lg');
modal_body();
// form_open(base_url('transaction/new_call_activity/save'),'post','form');
if ($this->db->table_exists('trxvisit_' . date('Y') . '_' . date('m'))) {
	col_init(4, 8);
	echo '<div class="row" style="margin-bottom:10px">';
	echo '<div class="col-sm-6">';

	select2('Produk Group', 'produk_grup', 'required', $this->session->userdata('produk_group'), 'kode', 'nama', '', 'disabled="disabled"');
	select2('Dokter', 'dokter', 'required', '', '', '', '', 'disabled="disabled"');
	select2('Produk', 'produk', 'required', '', '', '', '', 'disabled="disabled"');
	input('text', 'Spesialist', 'spesialist', 'required', '', 'disabled="disabled"');
	input('text', 'Outlet', 'outlet', 'required', '', 'disabled="disabled"');
	input('text', 'Channel Outlet', 'channel_outlet', 'required', '', 'disabled="disabled"');
	select2('Kompetitor yang di R/', 'kompetitor_diresepkan', 'required', '', '', '', '', 'disabled="disabled"');
	echo '<div id="kompetitor_lainya_box" style="display:none; margin-bottom:10px">';
	textarea('Kompetitor yang di R/ Lainya', 'kompetitor_diresepkan_lainnya', 'max-length:150');
	echo '</div>';
	textarea('Circumstances', 'circumstances', 'required', '', 'disabled="disabled"');
	textarea('Call Objective', 'call_object', 'required', '', 'disabled="disabled"');
	select2('Indikasi Produk', 'indikasi', 'required', '', '', '', '', 'disabled="disabled"');
	echo '<div id="indikasi_lainya_box" style="display:none; margin-bottom:10px">';
	textarea('Indikasi Produk Lainya', 'indikasi_lainnya', 'max-length:150', '', 'disabled="disabled"');
	echo '</div>';
	select2('Key Message', 'key_message', 'required', '', '', '', '', 'disabled="disabled"');
	echo '<div id="send_box" style="display:none">';
	textarea('MR Talk', 'mr_talk', '', '', 'disabled="disabled"');
	echo '<div id="mr_talk2_box" style="margin-bottom:10px">';
	textarea('MR Talk 2', 'mr_talk2', '', '', 'disabled="disabled"');
	echo '</div>';
	echo '<div id="mr_talk3_box" style="margin-bottom:10px">';
	textarea('MR Talk 3', 'mr_talk3', '', '', 'disabled="disabled"');
	echo '</div>';
	select2('Feedback Status', 'feedback_status', '', [
		'No Feedback' => 'No Feedback',
		'Positive' => 'Positive',
		'Negative' => 'Negative',
	], '', '', '', 'disabled="disabled"');
	echo '<div id="feedback_dokter_box" style="margin-bottom:10px; display:none">';
	textarea('Feedback Doctor', 'feedback_dokter', '', '', 'disabled="disabled"');
	textarea('Feedback Doctor 2', 'feedback_dokter2', '', '', 'disabled="disabled"');
	textarea('Feedback Doctor 3', 'feedback_dokter3', '', '', 'disabled="disabled"');
	echo '</div>';
	textarea('Next Action Plan', 'next_action', '', '', 'disabled="disabled"');
	echo '</div>';
	echo '</div>';
	echo '<div class="col-sm-6">';
	if ($this->session->userdata('team') == 'ABILIFY') {
		input('text', 'Matrix Abilify', 'matrix', '', '', 'disabled="disabled"');
		input('text', 'Matrix Maintena', 'matrix_maintena', '', '', 'disabled="disabled"');
		input('text', 'Matrix Rexulti', 'matrix_rexulti', '', '', 'disabled="disabled"');
	} else {
		input('text', 'Matrix', 'matrix', '', '', 'disabled="disabled"');
	}
	if (count($this->session->userdata('produk_group')) > 1) {
		select('Produk 2', 'produk2', '', $this->session->userdata('produk_group'), 'kode', 'nama', '', 'disabled="disabled"');
		select('Produk 3', 'produk3', '', $this->session->userdata('produk_group'), 'kode', 'nama', '', 'disabled="disabled"');
	} else {
		if ($this->session->userdata('team') == 'ABILIFY') {
			select('Produk 2', 'produk2', '', [
				'REXULTI' => 'REXULTI',
				'MAINTENA' => 'MAINTENA'
			], '', '', '', 'disabled="disabled"');
			select('Produk 3', 'produk3', '', [
				'REXULTI' => 'REXULTI',
				'MAINTENA' => 'MAINTENA'
			], '', '', '', 'disabled="disabled"');
		} else {
			select('Produk 2', 'produk2', '', $this->session->userdata('produk_group'), 'kode', 'nama', '', 'disabled="disabled"');
		}
	}
	echo '<hr/>';
	if (user('id_group') == MR_ROLE_ID) {
		select2('Call Type', 'call_type', '', [
			['id' => 1, 'nama' => 'A DFR'],
			['id' => 2, 'nama' => 'B Short Detailing'],
			['id' => 3, 'nama' => 'C Happy Call'],
		], 'id', 'nama', '', 'disabled="disabled"');
		select2('Sub Call Type', 'sub_call_type', '', '', '', '', '', 'disabled="disabled"');
	} else {
		form_open(base_url('report/history_dfr/update'), 'post', 'form');
		col_init(4, 8);
		input('hidden', 'id', 'id');
		input('hidden', 'tahun', 'tahun');
		input('hidden', 'bulan', 'bulan');
		select2('Call Type', 'call_type', '', [
			['id' => 1, 'nama' => 'A DFR'],
			['id' => 2, 'nama' => 'B Short Detailing'],
			['id' => 3, 'nama' => 'C Happy Call'],
		], 'id', 'nama');
		select2('Sub Call Type', 'sub_call_type', '', '', '', '', '', 'disabled="disabled"');
		select('Penilaian OPSS', 'penilaian', 'required', [
			'',
			'Sesuai dengan Tahapan OPSS',
			'Belum Sesuai dengan Tahapan OPSS'
		]);
		echo '<div id="belum_sesuai_box" style="display:none; margin-bottom:10px">';
		textarea('Alasan Belum Sesuai', 'alasan_belum_sesuai', '');
		echo '</div>';
		form_button(lang('simpan') . ' Feedback', lang('batal'));
	}
	echo '</div>';
	echo '</div>';
	// form_button(lang('simpan'),lang('batal'));

} else {
	echo '<div style="text-align:center">
			<img src="' . base_url('assets/images/no-data.svg') . '" width="40%">
			<h3> Visit Plan Bulan ' . strftime('%B', strtotime(date('m'))) . ' Tahun ' . date('Y') . ' Belum dibuat atau belum disetujui. </h3>
		</div>';
}
form_close();
modal_footer();
modal_close();
modal_open('feedback-modal', 'List Feedback DFR', 'modal-md');
modal_body();
echo '<div id="feedback_box"></div>';
modal_close();
?>
<script type="text/javascript">
	var data_tmp = {};

	$('#form-filter').submit(function(e) {
		e.preventDefault();
		getData();
	});

	$(document).ready(function() {
		
	});

	function getData() {
		$.ajax({
			url: base_url + 'report/history_dfr/data',
			data: $('#form-filter').serialize(),
			type: 'post',
			success: function(r) {
				document.querySelector('#result').innerHTML = r;
				$('.test-datatable').DataTable({
					"paging": true,
					"lengthChange": false,
					"searching": false,
					"ordering": true,
					"info": true,
					"autoWidth": false,
					"responsive": true,
					"pageLength": 10,
					"lengthMenu": [
						[10, 25, 50, -1],
						[10, 25, 50, "All"]
					],
					"language": {
						"lengthMenu": "Menampilkan _MENU_ data per halaman",
						"zeroRecords": "Tidak ada data",
						"info": "Data _PAGE_ dari _PAGES_",
						"infoEmpty": "Tidak ada data",
						"infoFiltered": "(filtered from _MAX_ total records)"
					}
				});
			}
		});
	}

	$(document).on('click', '.btn-feedback', function() {
		var id = $(this).attr('data-id')
		$.ajax({
			url: base_url + 'report/history_dfr/get_feedback/' + $('#ftahun').val() + '/' + $('#fbulan').val() + '?id=' + id,
			success: function(resp) {
				var html_feedback = '<table class="table-app"><thead>' +
					'<th>No.</th>' +
					'<th>Nama</th>' +
					'<th>Grup</th>' +
					'<th>Penilaian</th>' +
					'<th>Alasan Belum Sesuai</th>' +
					'</thead>'
				if (resp.length > 0) {
					$.each(resp, function(i, v) {
						html_feedback += '<tr>' +
							'<td>' + (i + 1) + '</td>' +
							'<td>' + v.nama_user + '</td>' +
							'<td>' + v.nama_grup + '</td>' +
							'<td>' + v.penilaian + '</td>' +
							'<td>' + v.alasan_belum_sesuai + '</td>' +
							'<tr>'
					})
					html_feedback += '</table>'
				} else {
					html_feedback = 'Tidak Ada Data';
				}

				$('#feedback_box').html(html_feedback);
			}
		})
		$('#feedback-modal').modal()
	})

	$('#penilaian').on('change', function() {
		if ($(this).val() == 'Belum Sesuai dengan Tahapan OPSS') {
			$('#belum_sesuai_box').show(500);
			$('#alasan_belum_sesuai').attr('data-validation', 'required')
		} else {
			$('#belum_sesuai_box').hide()
			$('#alasan_belum_sesuai').attr('data-validation', '');
		}
	})

	// $(document).on('dblclick','tbody td .badge',function(){
	// 	if('<?= user('id_group') ?>' == '<?= AM_ROLE_ID ?>'){
	// 		var data_id = $(this).attr('data-id');
	// 		if($(this).attr('class') == 'badge badge-danger'){
	// 			id_approve = data_id;
	// 			cConfirm.open('Apakah mau dikembalikan menjadi approve ?', 'approve');
	// 		} else {
	// 			$('#nid').val(data_id);
	// 			$('#not-approved-form').modal();
	// 		}
	// 	}
	// });

	// $(document).on('submit','#save_na', function(e){
	// 	if( $('#nreason').val() != ''){
	// 		e.preventDefault();
	// 		$.ajax({
	// 			url: "<?= base_url('transaction/approval_visit_plan/approval') ?>",
	// 			method: 'post',
	// 			data: {id: $('#nid').val(),alasan_not_approve: $('#nreason').val()},
	// 			success: function(resp){
	// 				if(resp.status==true){
	// 					cAlert.open('Sudah diubah menjadi Not Approve','success');
	// 					$('#not-approved-form').modal('toggle');
	// 					$(this).trigger("reset");
	// 					$('span[data-id="'+$('#nid').val()+'"]').attr('class','badge badge-danger');
	// 					$('span[data-id="'+$('#nid').val()+'"]').html('NOT APPROVED');
	// 				} else {
	// 					cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
	// 				}
	// 			}
	// 		})
	// 	}
	// });

	// function approve(){
	// 	$.ajax({
	// 		url: '<?= base_url('transaction/approval_visit_plan/approval') ?>',
	// 		method: 'post',
	// 		data: {id: id_approve},
	// 		success: function(resp){
	// 			if(resp.status==true){
	// 				cAlert.open('Sudah diubah kembali menjadi approve','success');
	// 				$('#form-filter').submit();
	// 				$('span[data-id="'+id_approve+'"]').attr('class','badge badge-success');
	// 				$('span[data-id="'+id_approve+'"]').html('APPROVED');
	// 			} else {
	// 				cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
	// 			}
	// 		}
	// 	})
	// }

	$(document).ready(function() {
		var fteam = $('#fteam').val();
		get_produk_grup(fteam);
	})

	$(document).on('change', '#fteam', function() {
		get_produk_grup($(this).val());
	})

	$(document).on('change', '#fam', function() {
		get_mr($('#fteam').val(), $(this).val());
	});

	$(document).on('change', '#fpgroup', function() {
		get_am($('#fteam').val());
	});

	function get_produk_grup(team = '') {
		// $.ajax({
		// 	url: base_url+'report/history_visit_plan/get_produk_grup?team='+team,
		// 	success: function(resp){
		// 		var html_pgroup = '';
		// 		$('#fpgroup').html('');
		// 		$.each(resp, function(i, val){
		// 			html_pgroup += '<option value="'+val.kode+'">'+val.nama+'</option>';
		// 		});
		// 		$('#fpgroup').html(html_pgroup);
		get_am(team);
		// 	}
		// });
	}

	function get_am(team) {
		$.ajax({
			url: base_url + 'report/history_visit_plan/get_am?team=' + team,
			success: function(resp) {
				var html_am = '';
				$('#fam').html('');
				$.each(resp, function(i, val) {
					html_am += '<option value="' + val.n_am + '">' + val.nama_am + '</option>';
				});
				$('#fam').html(html_am);
				get_mr(team, $('#fam').val());
			}
		});
	}

	function get_mr(team, am) {
		$.ajax({
			url: base_url + 'report/history_visit_plan/get_mr?team=' + team + '&am=' + am,
			success: function(resp) {
				var html_am = '';
				$('#fmr').html('');
				$.each(resp, function(i, val) {
					html_am += '<option value="' + val.n_mr + '">' + val.nama_mr + '</option>';
				});
				$('#fmr').html(html_am);
			}
		});
	}

	$('#produk_grup').on('change', function() {
		$.ajax({
			url: base_url + 'report/history_dfr/init_data/' + $('#fbulan').val() + '/' + $('#ftahun').val() + '/' + $('#fmr').val(),
			type: 'post',
			data: {
				produk_grup: $(this).val()
			},
			success: function(resp) {
				var dokter = resp.dokter
				var kompetitor_diresepkan = resp.kompetitor_diresepkan
				var indikasi_produk = resp.indikasi
				var key_message = resp.key_message
				var produk = resp.produk
				var html_dokter = '<option value=""></option>'
				var html_kompet = ''
				var html_indika = ''
				var html_keymes = ''
				var html_produk = ''

				$('#dokter').html('')
				$('#produk').html('')
				$('#kompetitor_diresepkan').html('')
				$('#indikasi').html('')
				$('#key_message').html('')

				$.each(dokter, function(i, v) {
					html_dokter += '<option value="' + v.id + '">' + v.nama + '</option>'
				})
				$.each(kompetitor_diresepkan, function(i, v) {
					html_kompet += '<option value="' + v.id + '">' + v.nama + '</option>'
				})
				html_kompet += '<option value="lainya">Lainya</option>'
				$.each(indikasi_produk, function(i, v) {
					html_indika += '<option value="' + v.id + '">' + v.nama + '</option>'
				})
				html_indika += '<option value="lainya">Lainya</option>'
				$.each(key_message, function(i, v) {
					html_keymes += '<option value="' + v.id + '">' + v.nama + '</option>'
				})
				$.each(produk, function(i, v) {
					html_produk += '<option value="' + v.id + '">' + v.nama + '</option>'
				})

				$('#dokter').html(html_dokter)
				$('#kompetitor_diresepkan').html(html_kompet)
				$('#indikasi').html(html_indika)
				$('#key_message').html(html_keymes)
				$('#produk').html(html_produk)

				if (data_tmp.dokter != '') {
					$('#dokter').val(data_tmp.dokter).trigger('change')

				}

				if (data_tmp.kompetitor_diresepkan != '') {
					$('#kompetitor_diresepkan').val(data_tmp.kompetitor_diresepkan).trigger('change')
				}

				if (data_tmp.indikasi != '') {
					$('#indikasi').val(data_tmp.indikasi).trigger('change')
				}

				if (data_tmp.produk2 != '') {
					$('#produk2').val(data_tmp.produk2).trigger('change')
				}

				if (data_tmp.produk3 != '') {
					$('#produk3').val(data_tmp.produk3).trigger('change')
				}

				if (data_tmp.key_message != '') {
					$('#key_message').val(data_tmp.key_message).trigger('change')
				}

				// data_tmp = {};

			}
		})
	})

	$('#kompetitor_diresepkan').on('change', function() {
		if ($(this).val() == 'lainya') {
			$('#kompetitor_lainya_box').show(1000)
		} else {
			$('#kompetitor_lainya_box').hide(1000)
		}
	})

	$('#indikasi').on('change', function() {
		if ($(this).val() == 'lainya') {
			$('#indikasi_lainya_box').show(1000)
		} else {
			$('#indikasi_lainya_box').hide(1000)
		}
	})

	$('#key_message').on('change', function() {
		if ($(this).val() == 'lainya') {
			$('#key_message_lainya_box').show(1000)
		} else {
			$('#key_message_lainya_box').hide(1000)
		}
	})

	$('#produk2').on('change', function() {
		if ($(this).val() != '') {
			$('#mr_talk2_box').show();
		} else {
			$('#mr_talk2_box').hide();
		}
	})

	$('#produk3').on('change', function() {
		if ($(this).val() != '') {
			$('#mr_talk3_box').show();
		} else {
			$('#mr_talk3_box').hide();
		}
	})

	$('#call_type').on('change', function() {
		var id = $(this).val()
		$.ajax({
			url: base_url + 'report/history_dfr/init_call_data?id=' + id,
			success: function(resp) {
				var html_sub_call_type = '';
				$('#sub_call_type').html('');
				$.each(resp, function(i, v) {
					html_sub_call_type += '<option value="' + v.id + '">' + v.nama + '</option>';
				})
				$('#sub_call_type').html(html_sub_call_type);
			},
			complete: function() {
				// console.log(data_tmp);
				// alert($(this).val())
				if (id == 1) {
					$('#mr_talk').attr('data-validation', 'required')
					$('#feedback_dokter').attr('data-validation', 'required')
					$('#feedback_status').attr('data-validation', 'required')
					$('#next_action').attr('data-validation', 'required')
					$('#sub_call_type').attr('data-validation', 'required')
				} else {
					$('#mr_talk').attr('data-validation', '')
					$('#feedback_dokter').attr('data-validation', '')
					$('#feedback_status').attr('data-validation', '')
					$('#next_action').attr('data-validation', '')
					$('#sub_call_type').attr('data-validation', '')
				}

				if (data_tmp.sub_call_type != '') {
					$('#sub_call_type').val(data_tmp.sub_call_type).trigger('change')
					data_tmp.sub_call_type = '';
				} else {
					if (id == 1) {
						$('#sub_call_type').val(1).trigger('change')
					} else if (id == 2) {
						$('#sub_call_type').val(5).trigger('change')
					} else if (id == 3) {
						$('#sub_call_type').val(11).trigger('change')
					}
				}
			}
		})
	})

	$('#dokter').on('change', function() {
		$.ajax({
			url: base_url + 'report/history_dfr/get_dokter_detail/' + $(this).val() + '/' + $('#fbulan').val() + '/' + $('#ftahun').val() + '/' + $('#fmr').val(),
			success: function(resp) {

				$('#outlet').val(resp.nama_outlet ? resp.nama_outlet : 'Reguler')
				$('#channel_outlet').val(resp.channel_outlet)
				$('#spesialist').val(resp.nama_spesialist)
				$('#matrix').val(resp.customer_matrix)
				$('#matrix_rexulti').val(resp.customer_matrix_rexulti)
				$('#matrix_maintena').val(resp.customer_matrix_maintena)

			}
		})
	})

	$('#feedback_status').on('change', function() {
		if ($(this).val() == 'No Feedback' || $(this).val() == '') {
			$('#feedback_dokter_box').hide(1000)
		} else {
			$('#feedback_dokter_box').show(1000)
		}
	})

	$(document).on('click', '.btn-detail', function() {
		var id = $(this).attr('data-id')
		$.ajax({
			url: base_url + 'report/history_dfr/get_data/' + $('#fbulan').val() + '/' + $('#ftahun').val() + '?id=' + id,
			success: function(resp) {
				console.log(resp);
				var data = resp
				data_tmp = {
					dokter: data.dokter,
					kompetitor_diresepkan: data.kompetitor_diresepkan,
					indikasi: data.indikasi,
					produk2: data.produk2,
					produk3: data.produk3,
					key_message: data.key_message,
					sub_call_type: data.sub_call_type,
				}
				$('#id').val(data.id)
				$('#tahun').val($('#ftahun').val())
				$('#bulan').val($('#fbulan').val())
				$('#produk_grup').val(data.produk_grup).trigger('change')
				$('#spesialist').val(data.nama_spesialist)
				$('#dokter').val(data.dokter).trigger('change')
				$('#produk').val(data.produk)
				$('#outlet').val(data.nama_outlet)
				$('#channel_outlet').val(data.channel_outlet)
				$('#kompetitor_diresepkan').val(data.kompetitor_diresepkan).trigger('change')
				$('#circumstances').val(data.circumstances)
				$('#call_object').val(data.call_object)
				$('#channel_outlet').val(data.channel_outlet)
				$('#indikasi').val(data.indikasi).trigger('change')
				$('#key_message').val(data.key_message)
				$('#kompetitor_diresepkan_lainnya').val(data.kompetitor_diresepkan_lainnya)
				$('#indikasi_lainnya').val(data.indikasi_lainnya)
				$('#produk2').val(data.produk2)
				$('#produk3').val(data.produk3)
				$('#matrix').val(data.customer_matrix)
				$('#call_type').attr('data-validation', 'required')
				$('#mr_talk').attr('data-validation', '')
				$('#sub_call_type').attr('data-validation', '')
				$('#feedback_dokter').attr('data-validation', '')
				$('#feedback_status').attr('data-validation', '')
				$('#next_action').attr('data-validation', '')
				$('#call_type').val(data.call_type).trigger('change')
				$('#mr_talk').val(data.mr_talk)
				$('#mr_talk2').val(data.mr_talk2)
				$('#mr_talk3').val(data.mr_talk3)
				$('#feedback_dokter').val(data.feedback_dokter)
				$('#feedback_dokter2').val(data.feedback_dokter2)
				$('#feedback_dokter3').val(data.feedback_dokter3)
				$('#feedback_status').val(data.feedback_status).trigger('change')
				$('#next_action').val(data.next_action)
				$('#penilaian').val(data.penilaian).trigger('change')
				$('#alasan_belum_sesuai').val(data.alasan_belum_sesuai)
				$('#send_box').show()
				$('#modal-form').modal()
			}
		})
	})
</script>