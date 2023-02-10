<script src="<?=base_url('assets/js/sketchpad.js')?>"></script>
<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<select class="select2" id="fpgroup" style="width: 150px;" onchange="filter()">
				<option value="">All Product Group</option>
				<?php foreach ($this->session->userdata('produk_group') as $val) {
					echo '<option value="' . $val['kode'] . '">' . $val['nama'] . '</option>';
				} ?>
			</select>
			<?php echo access_button('export'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('transaction/new_call_activity/data'),'trxdfr_'.date('Y').'_'.date('m'));
		thead();
			tr();
				th('#','text-center','width="30" data-content="id"');
				th('Tanggal','text-center','data-content="cat" data-type="date"');
				th('Dokter','','data-content="nama_dokter" data-custom="true"');
				th('Outlet','','data-content="nama_outlet" data-custom="true"');
				th('Channel Outlet','','data-content="channel_outlet" data-custom="true"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php
modal_open('modal-form','New Call Activity','modal-xl','style="position:absolute"');
	modal_body();
	form_open(base_url('transaction/new_call_activity/save'),'post','form');
		if($this->db->table_exists('trxvisit_'.date('Y').'_'.date('m'))){
				col_init(3,9);
				echo '<div class="row" style="margin-bottom:10px">';
					echo '<div class="col-sm-10">';
						input('hidden','id','id');
						input('hidden','visit_plan','visit_plan');
						select2('Produk Group','produk_grup','required',$this->session->userdata('produk_group'),'kode','nama');
						select2('Dokter','dokter','required');
						select2('Produk','produk','required');
						input('text','Spesialist','spesialist','required','','disabled="disabled"');
						input('text','Outlet','outlet','required','','disabled="disabled"');
						input('text','Channel Outlet','channel_outlet','required','','disabled="disabled"');
						select2('Kompetitor yang di R/', 'kompetitor_diresepkan','required');
						echo '<div id="kompetitor_lainya_box" style="display:none; margin-bottom:10px">';
							textarea('Kompetitor yang di R/ Lainya','kompetitor_diresepkan_lainnya','max-length:150');
						echo '</div>';
						textarea('Circumstances','circumstances','required|max-length:150');
						textarea('Call Objective','call_object','required|max-length:150');
						select2('Indikasi Produk', 'indikasi','required');
						echo '<div id="indikasi_lainya_box" style="display:none; margin-bottom:10px">';
							textarea('Indikasi Produk Lainya','indikasi_lainnya','max-length:150');
						echo '</div>';
						select('Key Message', 'key_message','required');
						echo '<div class="row">';
						echo '</div>';
						echo '<div id="send_box" style="display:none">';
							select2('Call Type','call_type','',[
								['id' => 'A', 'nama' => 'A DFR'],
								['id' => 'B', 'nama' => 'B Short Detailing'],
								['id' => 'C', 'nama' => 'C Happy Call'],
							],'id','nama');
							select2('Sub Call Type','sub_call_type');
							textarea('MR Talk','mr_talk');
							echo '<div id="mr_talk2_box" style="margin-bottom:10px">';
								textarea('MR Talk 2','mr_talk2');
							echo '</div>';
							echo '<div id="mr_talk3_box" style="margin-bottom:10px">';
								textarea('MR Talk 3','mr_talk3');
							echo '</div>';
							select2('Feedback Status','feedback_status','',[
								'No Feedback' => 'No Feedback',
								'Positive' => 'Positive',
								'Negative' => 'Negative',
							]);
							echo '<div id="feedback_dokter_box" style="margin-bottom:10px; display:none">';
								textarea('Feedback Doctor','feedback_dokter','');
								textarea('Feedback Doctor 2','feedback_dokter2','');
								textarea('Feedback Doctor 3','feedback_dokter3','');
							echo '</div>';
							textarea('Next Action Plan','next_action','');
						echo '</div>';
					echo '</div>';
					echo '<div class="col-sm-2">';
						if($this->session->userdata('team') == 'ABILIFY'){
							input('text','Matrix Abilify','matrix','','','disabled="disabled"');
							input('text','Matrix Rexulti','matrix_rexulti','','','disabled="disabled"');
							input('text','Matrix Maintena','matrix_maintena','','','disabled="disabled"');
							select('Produk 2','produk2','',
								[
									'ABILIFY' => 'ABILIFY',
									'REXULTI' => 'REXULTI',
									'MAINTENA' => 'MAINTENA'
								]
							);
							select('Produk 3','produk3','',
								[
									'ABILIFY' => 'ABILIFY',
									'REXULTI' => 'REXULTI',
									'MAINTENA' => 'MAINTENA'
								]
							);
						} else {
							if($this->session->userdata('team') == 'ABILIFY'){
								input('text','Matrix Abilify','matrix','','','disabled="disabled"');
								input('text','Matrix Maintena','matrix_maintena','','','disabled="disabled"');
								input('text','Matrix Rexulti','matrix_rexulti','','','disabled="disabled"');
							} else {
								input('text','Matrix','matrix','','','disabled="disabled"');
							}
							if(count($this->session->userdata('produk_group'))>1){
								select('Produk2','produk2','',$this->session->userdata('produk_group'),'kode','nama');
								select('Produk3','produk3','',$this->session->userdata('produk_group'),'kode','nama');
							} else {
								if($this->session->userdata('team') == 'ABILIFY'){
									select('Produk2','produk2','', [
										'REXULTI' => 'REXULTI',
										'MAINTENA' => 'MAINTENA'
									]);
									select('Produk3','produk3','',[
										'REXULTI' => 'REXULTI',
										'MAINTENA' => 'MAINTENA'
									]);
								} else {
									select('Produk 2','produk2','',$this->session->userdata('produk_group'),'kode','nama');
								}
							}
						}
					echo '</div>';
				echo '</div>';
				form_button(lang('simpan'),lang('batal'));
			
		} else {
			echo '<div style="text-align:center">
				<img src="'.base_url('assets/images/no-data.svg').'" width="40%">
				<h3> Visit Plan Bulan '.strftime('%B', strtotime(date('m'))).' Tahun '.date('Y').' Belum dibuat atau belum disetujui. </h3>
			</div>';
		}
		form_close();
	modal_footer();
modal_close();
?>
<script>

	var data_tmp = {};

	function filter(){
		var pgroup = $('#fpgroup').val();
		var bulan = '<?=date('m')?>';
		var tahun = '<?=date('Y')?>';
		var table_name = '';
		if(pgroup != '' && bulan != '' && tahun != ''){
			$('[data-serverside]').attr('data-serverside', base_url + 'transaction/new_call_activity/data?pgroup='+pgroup);
			$.ajax({
				url: '<?=base_url('transaction/new_call_activity/check_data_exists/')?>'+bulan+'/'+tahun,
				success: function(resp){
					if(resp.status == true){
						table_name = 'trxdfr_'+tahun+'_'+bulan;
					}
				},
				complete: function(){
					if(table_name != ''){
						$('[data-serverside]').attr('data-table', table_name);
						var data = $('[data-field]');
						$.each(data, function(i,v){
							field = $(this).attr('data-field').split('.');
							if(field[1] != undefined){
								$(this).attr('data-field', table_name+'.'+field[1])
								$(this).attr('data-alias', table_name+'_'+field[1])
							}
						})
						var data = $('[data-filter]');
						$.each(data, function(i,v){
							field = $(this).attr('data-filter').split('.');
							if(field[1] != undefined){
								$(this).attr('data-filter', table_name+'.'+field[1])
							}
						})
					}
					refreshData();
				}
			})
		}
	}

	$(document).on('click','.btn-input', function(){
		$('#send_box').hide()
		$('#call_type').attr('data-validation','')
		$('#mr_talk').attr('data-validation','')
		$('#sub_call_type').attr('data-validation','')
		$('#feedback_dokter').attr('data-validation','')
		$('#feedback_status').attr('data-validation','')
		$('#next_action').attr('data-validation','')
		$('#call_type').val().trigger('change')
		$('#call_type').val('').trigger('change')
		$('#mr_talk').val('');
		$('#mr_talk2').val('');
		$('#mr_talk3').val('');
		$('#feedback_dokter').val('');
		$('#feedback_dokter2').val('');
		$('#feedback_dokter3').val('');
		$('#feedback_status').val('').trigger('change');
		$('#next_action').val('');
		$('button[type="submit"]').html('Simpan');
		$('button[type="submit"]').attr('class','btn btn-sky');
	});

	$('#produk_grup').on('change', function(){
		$.ajax({
			url: base_url+'transaction/new_call_activity/init_data/',
			type: 'post',
			data: {
				produk_grup: $(this).val()
			},
			success: function(resp){

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

				$.each(dokter, function(i,v){
					html_dokter += '<option value="'+v.id+'">'+v.nama+'</option>'
				})
				$.each(kompetitor_diresepkan, function(i,v){
					html_kompet += '<option value="'+v.id+'">'+v.nama+'</option>'
				})
				html_kompet += '<option value="lainya">Lainya</option>'
				$.each(indikasi_produk, function(i,v){
					html_indika += '<option value="'+v.id+'">'+v.nama+'</option>'
				})
				html_indika += '<option value="lainya">Lainya</option>'
				$.each(key_message, function(i,v){
					html_keymes += '<option value="'+v.id+'">'+v.nama+'</option>'
				})
				$.each(produk, function(i,v){
					html_produk += '<option value="'+v.id+'">'+v.nama+'</option>'
				})

				$('#dokter').html(html_dokter)
				$('#kompetitor_diresepkan').html(html_kompet)
				$('#indikasi').html(html_indika)
				$('#key_message').html(html_keymes)
				$('#produk').html(html_produk)

				if(data_tmp.dokter != ''){
					$('#dokter').val(data_tmp.dokter).trigger('change')
					
				}

				if(data_tmp.kompetitor_diresepkan != ''){
					$('#kompetitor_diresepkan').val(data_tmp.kompetitor_diresepkan).trigger('change')
				}

				if(data_tmp.indikasi != ''){
					$('#indikasi').val(data_tmp.indikasi).trigger('change')
				}

				if(data_tmp.produk != ''){
					$('#produk').val(data_tmp.produk).trigger('change')
				}

				if(data_tmp.produk2 != ''){
					$('#produk2').val(data_tmp.produk2).trigger('change')
				}

				if(data_tmp.produk3 != ''){
					$('#produk3').val(data_tmp.produk3).trigger('change')
				}

				if(data_tmp.key_message != ''){
					$('#key_message').val(data_tmp.key_message).trigger('change')
				}

				data_tmp = {};

			}
		})
	})

	$('#kompetitor_diresepkan').on('change', function(){
		if($(this).val() == 'lainya'){
			$('#kompetitor_lainya_box').show(1000)
		} else {
			$('#kompetitor_lainya_box').hide(1000)
		}
	})

	$('#indikasi').on('change', function(){
		if($(this).val() == 'lainya'){
			$('#indikasi_lainya_box').show(1000)
		} else {
			$('#indikasi_lainya_box').hide(1000)
		}
	})

	$('#key_message').on('change', function(){
		if($(this).val() == 'lainya'){
			$('#key_message_lainya_box').show(1000)
		} else {
			$('#key_message_lainya_box').hide(1000)
		}
	})

	$('#produk2').on('change', function(){
		if($(this).val() != ''){
			$('#mr_talk2_box').show();
		} else {
			$('#mr_talk2_box').hide();
		}
	})

	$('#produk3').on('change', function(){
		if($(this).val() != ''){
			$('#mr_talk3_box').show();
		} else {
			$('#mr_talk3_box').hide();
		}
	})

	$('#call_type').on('change', function(){
		var id = $(this).val()
		$.ajax({
			url: base_url + 'transaction/new_call_activity/init_call_data?id='+id,
			success: function(resp){
				var html_sub_call_type = '';
				$('#sub_call_type').html('');
				$.each(resp, function(i,v){
					html_sub_call_type += '<option value="'+v.id+'">'+v.nama+'</option>';
				})
				$('#sub_call_type').html(html_sub_call_type);
			},
			complete: function(){
				if(id == 1){
					$('#mr_talk').attr('data-validation','required');
					$('#feedback_dokter').attr('data-validation','required');
					$('#feedback_status').attr('data-validation','required');
					$('#next_action').attr('data-validation','required');
					$('#sub_call_type').attr('data-validation','required');
				} else {
					$('#mr_talk').attr('data-validation','');
					$('#feedback_dokter').attr('data-validation','');
					$('#feedback_status').attr('data-validation','');
					$('#next_action').attr('data-validation','');
					$('#sub_call_type').attr('data-validation','');
				}
			}
		})
	})

	$('#dokter').on('change', function(){
		$.ajax({
			url: base_url + 'transaction/new_call_activity/get_dokter_detail/'+$(this).val(),
			success: function(resp){
				$('#outlet').val(resp.nama_outlet ? resp.nama_outlet : 'Reguler')
				$('#channel_outlet').val(resp.channel_outlet)
				$('#spesialist').val(resp.nama_spesialist)
				$('#matrix').val(resp.customer_matrix)
				$('#matrix_rexulti').val(resp.customer_matrix_rexulti)
				$('#matrix_maintena').val(resp.customer_matrix_maintena)
				$('#visit_plan').val(resp.id)
			}
		})
	})

	$('#feedback_status').on('change', function(){
		if($(this).val() == 'No Feedback' || $(this).val() == ''){
			$('#feedback_dokter_box').hide(1000)
		} else {
			$('#feedback_dokter_box').show(1000)
		}
	})

	$(document).on('click','.btn-send',function(){
		var id = $(this).attr('data-id')
		$.ajax({
			url: base_url + 'transaction/new_call_activity/get_data?id='+id,
			success: function(resp){
				var data = resp
				data_tmp = {
					dokter: data.dokter,
					kompetitor_diresepkan: data.kompetitor_diresepkan,
					indikasi: data.indikasi,
					produk: data.produk,
					produk2: data.produk2,
					produk3: data.produk3,
					key_message: data.key_message
				}
				$('#id').val(data.id)
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
				$('button[type="submit"]').html('<i class="fa-paper-plane"></i> Send');
				$('button[type="submit"]').attr('class','btn btn-sky');
				$('#call_type').attr('data-validation','required')
				$('#mr_talk').attr('data-validation','')
				$('#sub_call_type').attr('data-validation','')
				$('#feedback_dokter').attr('data-validation','')
				$('#feedback_status').attr('data-validation','')
				$('#next_action').attr('data-validation','')
				$('#call_type').val('').trigger('change')
				$('#mr_talk').val('');
				$('#mr_talk2').val('');
				$('#mr_talk3').val('');
				$('#feedback_dokter').val('');
				$('#feedback_dokter2').val('');
				$('#feedback_dokter3').val('');
				$('#feedback_status').val('');
				$('#next_action').val('');
				$('#send_box').show()
				$('#modal-form').modal()
			}
		})
	})

</script>