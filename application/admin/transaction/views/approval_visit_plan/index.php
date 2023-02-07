<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<button class="btn btn-sky" id="bSubmit" onclick="popUpSubmit()"><i class="fa-check mr-2"></i>Approve All WAITING Status</button>
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
				th('Dokter','','data-content="nama_dokter" data-custom="true"');
				th('Specialist','','data-content="nama_spesialist" data-custom="true"');
				th('Practice','','data-content="nama_outlet" data-custom="true"');
				th('Plan Call','text-center','data-content="total_plan" data-custom="true"');
				th('Approve?','text-center','data-type="boolean" data-content="status" data-boolean-text="APPROVED,REVISION,WAITING" data-boolean-value="APPROVED,REVISION,WAITING"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<div class="filter-panel">
	<div class="filter-header">
		<i class="fa-search mr-2"></i> Pencarian
	</div>
	<div class="filter-body">
		<div class="form-group">
			<label>Produk Group</label>
			<select class="select2" id="fpgroup" style="width: 150px;" onchange="filter()">
				<label> Product Group </label>
				<option value="">Select Product Group</option>
				<?php foreach($this->session->userdata('produk_group') as $val){
					echo '<option value="'.$val['kode'].'">'.$val['nama'].'</option>';
				} ?>
			</select>
		</div>
		<div class="form-group">
			<label>MR</label>
			<select class="select2" id="fmr" style="width: 150px;" onchange="filter()">
				<option value="">Pilih MR</option>
				<?php 
				foreach (get_active_mr_by_user(user('username')) as $val) {
					echo '<option value="' . $val['n_mr'] . '">' . $val['nama_mr'] . '</option>';
				} ?>
			</select>
		</div>
	</div>
</div>
<?php
modal_open('modal-form','Edit Visit Plan');
modal_body();
	form_open(base_url('transaction/approval_visit_plan/save'),'post','edit-form');
		col_init(3,9);
		input('hidden','id','id');
		input('hidden','profiling','profiling');
		label('A. Data Doctor');
		input('text','Product Group','nama_produk_grup','','','disabled="disabled"');
		input('text','Doctor','nama_dokter','required','','disabled="disabled"');
		input('text','Spesialist','nama_spesialist','','','disabled="disabled"');
		input('text','Outlet','nama_outlet','','','disabled="disabled"');
		label('B. Plan Kunjungan');
		input('number','Week 1','week1');
		input('number','Week 2','week2');
		input('number','Week 3','week3');
		input('number','Week 4','week4');
		input('number','Week 5','week5');
		input('number','Week 6','week6');
	form_close();
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

	$(document).on('click','[data-serverside] tbody td .badge',function(){
		var data_id = $(this).closest('tr').find('.btn-input').attr('data-id');
		if($(this).attr('class') == 'badge badge-danger'){
			id_approve = data_id;
			cConfirm.open('Apakah mau dikembalikan menjadi approve ?', 'approve');
		} else {
			$('#nid').val(data_id);
			$('#not-approved-form').modal();
		}
	});

	$(document).on('submit','#save_na', function(e){
		e.preventDefault();
		$.ajax({
			url: "<?=base_url('transaction/approval_visit_plan/approval')?>",
			method: 'post',
			data: {id: $('#nid').val(),alasan_not_approve: $('#nreason').val()},
			success: function(resp){
				if(resp.status==true){
					cAlert.open('Visit Plan Telah Dikirim Ke MR Untuk Di Revisi','success');
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