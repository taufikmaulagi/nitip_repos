<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<button class="btn btn-sky btn-submit"> <i class="fa-check mr-2"></i> Submit </button>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
		table_open('',true,base_url('transaction/visit_plan/data/'.get('produk_group')),'trxvisit_'.date('Y').'_'.date('m'));
			thead();
				tr();
					th('No.','text-center','width="50" data-content="id"');
					th('Dokter','','data-content="nama_dokter" data-custom="true"');
					th('Specialist','','data-content="nama_spesialist" data-custom="true"');
					th('Practice','','data-content="nama_outlet" data-custom="true"');
					th('Plan Call','text-center','data-content="total_plan" data-custom="true"');
					th('Status','text-center','data-content="status" data-boolean-text="APPROVED,REVISE,WAITING,UNSUBMITTED" data-boolean-value="APPROVED,REVISE,WAITING,UNSUBMITTED" data-type="boolean"');
					th('&nbsp;','','width="30" data-content="action_button"');
		table_close();
	?>
</div>
<div class="filter-panel">
	<div class="filter-header bg-primary text-white">
		<i class="fa-search mr-2"></i> Search Data
	</div>
	<div class="filter-body">
		<?php
			col_init(12,12);
				select2('Produk Group','fpgroup','',$this->session->userdata('produk_group'),'kode','nama',get('produk_group'));
		?>
	</div>
</div>
<?php 

	modal_open('modal-form','Edit Visit Plan');
		modal_body();
			form_open(base_url('transaction/visit_plan/save'),'post','edit-form');
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
				form_button(lang('simpan'),lang('batal'));
			form_close();
		modal_footer();
	modal_close();
	
modal_open('modal-new-doctor','Add New');
	modal_body();
		form_open(base_url('transaction/visit_plan/add_new_doctor'),'post','form-new-doctor');
			col_init(3,9);
			select2('Dokter','new_dokter','required');
			input('hidden','new_pgroup','new_pgroup');
			form_button('Simpan',lang('batal'));
		form_close();
modal_close();
?>
<script>

	$('#new_pgroup').val('<?=get('produk_group')?>');

	function submit(){
		cConfirm.open('Pastikan visit plan yang dipilih untuk disubmit sudah benar !','submitVisitPlan');
	}
	
	$(document).on('change','#fpgroup', function(){
		var pgroup = $('#fpgroup').val()
		$('[data-serverside]').attr('data-serverside', base_url + 'transaction/visit_plan/data?group='+pgroup)
		refreshData()
	})

	$(document).on('click','.btn-submit',function(){
		let btn_html = $(this).html()
		$(this).html('<i class="fa-spinner-third spin mr-2"></i> Please Wait..')
		$.ajax({
			url: base_url + 'transaction/visit_plan/submit',
			success: function(r){
				cAlert.open(r.message, r.status)
				$('.btn-submit').html(btn_html)
				refreshData()
			}
		})
	})

</script>