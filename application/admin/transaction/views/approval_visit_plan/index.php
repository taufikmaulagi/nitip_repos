<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<label>Produk Group</label>
			<select class="select2" id="fpgroup" style="width: 150px;" onchange="filter()">
				<label> Product Group </label>
				<option value="">Select Product Group</option>
				<?php foreach($this->session->userdata('produk_group') as $val){
					echo '<option value="'.$val['kode'].'">'.$val['nama'].'</option>';
				} ?>
			</select>
			<label>MR</label>
			<select class="select2" id="fmr" style="width: 150px;" onchange="filter()">
				<option value="">Pilih MR</option>
				<?php 
				$tmp_team = [];
				foreach($this->session->userdata('team') as $val){
					array_push($tmp_team, $val['kode_team']);
				}
				$this->db->having('total_visit_plan > 0');
				foreach (get_data('history_organogram_detail', [
					'select' => 'history_organogram_detail.*, (select count(*) from trxvisit_'.date('Y').'_'.date('m').' where dat is null and mr = history_organogram_detail.n_mr) as total_visit_plan',
					'join' => [
						'history_organogram on history_organogram.id = history_organogram_detail.id_history_organogram'
					],
					'where' => [
						'n_am' => user('username'),
						'nama_mr !=' => '',
						'history_organogram.tanggal_end' => '0000-00-00'
					],
					'where_in' => [
						'history_organogram.kode_team' => $tmp_team
					],
					'group_by' => 'n_mr',
					'sort_by' => 'nama_mr',
					'sort' => 'ASC',
				])->result_array() as $val) {
					echo '<option value="' . $val['n_mr'] . '">' . $val['nama_mr'] . '</option>';
				} ?>
			</select>
			<span style="height: 50px; width:1px; border:1px solid; position: auto; margin-left:3px; margin-right:5px"></span>
			<button class="btn btn-fresh" id="bSubmit" onclick="popUpSubmit()"><i class="fa-paper-plane"></i>Submt Visit Plan</button>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	$date = date('m');
	table_open('',true,base_url('transaction/approval_visit_plan/data'),'trxvisit_'.date('Y').'_'.date('m'));
		thead();
			tr();
				th('#','text-center','width="30" data-content="id"');
				th('Dokter','','data-content="nama_dokter"');
				th('Specialist','','data-content="nama_spesialist"');
				th('Practice','','data-content="nama_outlet"');
				th('Plan Call','text-center','data-content="plan_call"');
				// th('Month','text-center','data-content="nama_bulan" data-custom="true"');
				// th('Year','text-center','data-content="tahun"');
				th('Approve?','text-center','data-type="boolean" data-content="status" data-boolean-text="APPROVED,NOT APPROVE,ON PROCESS" data-boolean-value="3,4,2"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php
modal_open('modal-detail','Detail Visit Plan');
	modal_body();
	// form_open(base_url('transaction/visit_plan/update'),'post','detail-form');
		col_init(3,9);
		input('hidden','dprofiling','dprofiling');
		label('A. Periode');
		select2('Bulan','dbulan','required',[
			[
				'id' => '1',
				'nama' => 'Januari'
			],
			[
				'id' => '2',
				'nama' => 'Februari'
			],
			[
				'id' => '3',
				'nama' => 'Maret'
			],
			[
				'id' => '4',
				'nama' => 'April'
			],
			[
				'id' => '5',
				'nama' => 'Mei'
			],
			[
				'id' => '6',
				'nama' => 'Juni'
			],
			[
				'id' => '7',
				'nama' => 'Juli'
			],
			[
				'id' => '8',
				'nama' => 'Agustus'
			],
			[
				'id' => '9',
				'nama' => 'September'
			],
			[
				'id' => '10',
				'nama' => 'Oktober'
			],
			[
				'id' => '11',
				'nama' => 'November'
			],
			[
				'id' => '12',
				'nama' => 'Desember'
			],
		],'id','nama','','disabled="disabled"');
		$year = [];
		for($i=date('Y');$i>=2018;$i--){
			$year[$i] = $i;
		}
		select2('Tahun','dtahun','required',$year,'','',date('Y'),'disabled="disabled"');
		label('B. Data Doctor');
		input('text','Product Group','dproduk_grup','required','','disabled="disabled"');
		input('text','Doctor','ddokter','required','','disabled="disabled"');
		input('text','Spesialist','dspesialist','required','','disabled="disabled"');
		input('text','Outlet','doutlet','required','','disabled="disabled"');
		label('C. Plan Kunjungan');
		input('number',lang('week1'),'dweek1','','','disabled="disabled"');
		input('number',lang('week2'),'dweek2','','','disabled="disabled"');
		input('number',lang('week3'),'dweek3','','','disabled="disabled"');
		input('number',lang('week4'),'dweek4','','','disabled="disabled"');
		input('number',lang('week5'),'dweek5','','','disabled="disabled"');
		input('number',lang('week6'),'dweek6','','','disabled="disabled"');
		// checkbox(lang('marketing_aktivitas'),'marketing_aktivitas');
		// form_button(lang('simpan'),lang('batal'));
	form_close();
	modal_footer();
modal_close();

modal_open('not-approved-form','Approval Profiling');
	modal_body();
		form_open('javascript:void(0)','post','save_na');
			col_init(4,8);
			input('hidden','nid','nid');
			textarea('Alasan Tidak Menyetujui','nreason','required');
			form_button(lang('simpan'),lang('batal'));
		form_close();
modal_close();
?>
<script>
	function filter(){
		$('[data-serverside]').attr('data-serverside', base_url + 'transaction/approval_visit_plan/data?pgroup='+$('#fpgroup').val()+'&mr='+$('#fmr').val());
		refreshData();
	}

	function get_marketing_program(id,type='add',value=''){
		$.ajax({	
			url: '<?=base_url('transaction/approval_visit_plan/get_marketing_program?pgroup=')?>'+id,
			success: function(resp){
				var opt_marketing_program = '<option value=""></option>';
				$.each(resp, function(i, val){
					opt_marketing_program += '<option value="'+val.id+'">'+val.nama+'</option>';
				});
				if(type=='add'){
					$('#marketing_program').html('');
					$('#marketing_program').html(opt_marketing_program);
				} else if(type=='edit') {
					$('#emarketing_program').html('');
					$('#emarketing_program').html(opt_marketing_program);
				} else {
					$('#dmarketing_program').html('');
					$('#dmarketing_program').html(opt_marketing_program);
				}
				if(value != ''){
					if(type == 'edit'){
						$('#emarketing_program').val(value).trigger('change');
					} else {
						$('#dmarketing_program').val(value).trigger('change');
					}
				}
			}
		});
	}

	function get_marketing_aktifitas(id,type='add',value=''){
		$.ajax({
			url: '<?=base_url('transaction/approval_visit_plan/get_marketing_aktifitas?pgroup=')?>'+id,
			success: function(resp){
				var opt_marketing_aktifitas = '';
				if(value != null){
					value = value.split(',');
				} else {
					value = [];
				}
				$.each(resp, function(i, val){
					if(type=='add'){
						opt_marketing_aktifitas += '<input type="checkbox" id="'+(val.nama).replace(' ','_')+'" name="marketing_aktifitas[]" value="'+val.id+'">&nbsp;&nbsp;<label for="'+(val.nama).replace(' ','_')+'">'+val.nama+'</label><br>';
					} else {
						var tmp_checked = '';
						if(value.length>0){
							for(var i=0;i<value.length;i++){
								if(value[i] == val.id){
									tmp_checked='checked="true"';
								}
							}
						}
						if(type == 'edit'){
							opt_marketing_aktifitas += '<input type="checkbox" id="'+(val.nama).replace(' ','_')+'" name="emarketing_aktifitas[]" value="'+val.id+'" '+tmp_checked+'>&nbsp;&nbsp;<label for="'+(val.nama).replace(' ','_')+'">'+val.nama+'</label><br>';
						} else {
							opt_marketing_aktifitas += '<input type="checkbox" id="'+(val.nama).replace(' ','_')+'" name="dmarketing_aktifitas[]" value="'+val.id+'" '+tmp_checked+' disabled="disabled">&nbsp;&nbsp;<label for="'+(val.nama).replace(' ','_')+'">'+val.nama+'</label><br>';
						}
					}
				});
				if(type=='add'){
					$('#marketing_aktifitas_box').html('');
					$('#marketing_aktifitas_box').html(opt_marketing_aktifitas);
				} else if(type == 'edit') {
					$('#emarketing_aktifitas_box').html('');
					$('#emarketing_aktifitas_box').html(opt_marketing_aktifitas);
				} else {
					$('#dmarketing_aktifitas_box').html('');
					$('#dmarketing_aktifitas_box').html(opt_marketing_aktifitas);
				}
			}
		})
	}

	function get_outlet(id){
		$('#dokter').attr('disabled',true);
		$('#outlet').html('<option value="">Please Wait..</option>');
		$('#outlet').attr('disabled',true);
		$.ajax({
			url: '<?=base_url('transaction/approval_visit_plan/get_outlet?dokter=')?>'+id+'&pgroup='+$('#fpgroup').val(),
			success: function(resp){
				var opt_outlet = '';
				$.each(resp, function(i, val){
					opt_outlet += '<option value="'+val.id+'">'+val.nama+'</option>';
				});
				if(resp.length > 0){
					$('#profiling').val(resp[0].id);
				}
				$('#outlet').html('');
				$('#outlet').html(opt_outlet);
				$('#outlet').attr('disabled',false);
				$('#dokter').attr('disabled',false);
			}
		})
	}

	$(document).on('dblclick','[data-serverside] tbody td .badge',function(){
		var data_id = $(this).closest('tr').find('.btn-detail').attr('data-id');
		if($(this).attr('class') == 'badge badge-danger'){
			id_approve = data_id;
			cConfirm.open('Apakah mau dikembalikan menjadi approve ?', 'approve');
		} else {
			$('#nid').val(data_id);
			$('#not-approved-form').modal();
		}
	});

	$(document).on('click','.btn-detail', function(){
		$.ajax({
			url: '<?=base_url('transaction/approval_visit_plan/get_data?id=')?>'+$(this).attr('data-id'),
			success: function(resp){
				$('#did').val(resp.id);
				$('#dprofiling').val(resp.profiling);
				$('#dbulan').val(resp.bulan).trigger('change');
				$('#dtahun').val(resp.tahun).trigger('change');
				$('#dproduk_grup').val(resp.nama_produk_grup);
				$('#ddokter').val(resp.nama_dokter);
				$('#dspesialist').val(resp.nama_spesialist);
				$('#doutlet').val(resp.nama_outlet);
				$('#dstandard_call').val(resp.standard_call);
				$('#dweek1').val(resp.week1);
				$('#dweek2').val(resp.week2);
				$('#dweek3').val(resp.week3);
				$('#dweek4').val(resp.week4);
				$('#dweek5').val(resp.week5);
				$('#dweek6').val(resp.week6);
				get_outlet(resp.dokter);
				get_marketing_aktifitas(resp.produk_grup,'detail',resp.marketing_aktifitas);
				get_marketing_program(resp.produk_grup,'detail',resp.marketing_program);
				$('#modal-detail').modal();
			}
		})
	});

	$(document).on('submit','#save_na', function(e){
		e.preventDefault();
		$.ajax({
			url: "<?=base_url('transaction/approval_visit_plan/approval')?>",
			method: 'post',
			data: {id: $('#nid').val(),alasan_not_approve: $('#nreason').val()},
			success: function(resp){
				if(resp.status==true){
					cAlert.open('Sudah diubah menjadi Not Approve','success');
					$('#not-approved-form').modal();
					$(this).trigger("reset");
					$('#nreason').val('')
					refreshData();
				} else {
					cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
				}
			}
		})
	});

	function approve(){
		$.ajax({
			url: '<?=base_url('transaction/approval_visit_plan/approval')?>',
			method: 'post',
			data: {id: id_approve},
			success: function(resp){
				if(resp.status==true){
					cAlert.open('Sudah diubah kembali menjadi approve','success');
					refreshData();
				} else {
					cAlert.open('Oops! Ada kesalahan. Silahkan coba lagi.', 'error');
				}
			}
		})
	}

	function popUpSubmit(){
		var pgroup = $('#fpgroup').val();
		var fmr = $('#fmr').val();
		if(pgroup != '' && fmr != ''){
			cConfirm.open('Pastikan data visit plan sudah fix dalam approve atau tidak approve','submit');
		}
	}

	function submit(){
		var pgroup = $('#fpgroup').val();
		var mr = $('#fmr').val();
		$.ajax({
			url: '<?=base_url('transaction/approval_visit_plan/submit')?>',
			method: 'post',
			data: {pgroup: pgroup, mr:mr},
			success: function(resp){
				if(resp.status == true){
					cAlert.open('Data visit plan selesai disubmit','success');
					refreshData();
				} else {
					cAlert.open('Oops! Ada Kesalahan Silahkan coba lagi','error');
				}
			}
		});
	}
</script>