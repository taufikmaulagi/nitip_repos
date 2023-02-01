<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
		<div class="float-right">
			<select class="select2" style="width: 150px;" onchange="filter()" id="fpgroup">
				<option value=""> Pilih Produk </option>
				<?php foreach($this->session->userdata('produk_group') as $k => $v){
					echo '<option value="'.$v['kode'].'">'.$v['nama'].'</option>';
				} ?>
			</select>
			<?php echo access_button(); ?>
			<button class="btn btn-fresh" id="submitLcv"><i class="fa-paper-plane mr-2"></i> Submit</button>
		</div>
	</div>
</div>
<div class="content-body">
		<?php
			table_open('', true, base_url('transaction/lcv/data'), 'trxlcv_' . date('Y') . '_' . active_cycle());
			thead();
				tr();
					th('No.', 'text-center', 'width="30" data-content="id"');
					th('Nama', '', 'data-content="nama" data-custom="true"');
					th('Tipe', 'text-center', 'data-content="tipe"');
					th('Produk Group', '', 'data-content="nama_produk_grup" data-custom="true"');
					th('Status','text-center','data-content="status" data-type="boolean" data-boolean-text="UNSUBMITTED,NOT APPROVED,SUBMITTED,APPROVED" data-boolean-value="1,4,2,3" data-type="boolean"');
					th('&nbsp;', '', 'width="30" data-content="action_button"');
			table_close();
		?>
</div>
<?php 

modal_open('modal-form');
	modal_body();
		form_open(base_url('transaction/lcv/save'), 'post', 'form');
			col_init(3, 8);
				select2('Produk Grup','produk_grup','required',$this->session->userdata('produk_group'),'kode','nama');
				radio('Tipe','tipe', [
					'DOKTER' => 'DOKTER', 'KPDM' => 'KPDM'
				]);
				echo '<div id="boxDokter" style="display:none" class="mt-3 mb-3">';
					select2('Dokter','dokter','',$dokter,'dokter','nama_dokter');
				echo '</div>';
				echo '<div id="boxKPDM" style="display:none" class="mt-3 mb-3">';
					select2('KPDM','kpdm','',$kpdm,'id','nama');
				echo '</div>';
				form_button(lang('simpan'), lang('batal'));
		form_close();
modal_close()	

?>
<script>
	function filter(){
		let produk = $('#fpgroup').val()
		$('[data-serverside]').attr('data-serverside', base_url + 'transaction/lcv/data?produk_group=' + produk)
		refreshData()
	}

	$(document).on('change', 'input[name="tipe"]', function(){

		let tipe = $(this).val()

		if(tipe == 'DOKTER'){
			$('#boxDokter').attr('style','');
			$('#boxKPDM').attr('style','display:none');
		} else {
			$('#boxDokter').attr('style','display:none');
			$('#boxKPDM').attr('style','');
		}
	})

	$('#modal-form').on('shown.bs.modal', function (e) {
		let tipe = $('input[name="tipe"]').val()

		if(tipe == 'DOKTER'){
			$('#boxDokter').attr('style','');
			$('#boxKPDM').attr('style','display:none');
		} else {
			$('#boxDokter').attr('style','display:none');
			$('#boxKPDM').attr('style','');
		}
	})

	$(document).on('click','#submitLcv',function(){
		cConfirm.open('Apakah anda yakin ?', 'submit')
	})

	function submit(){
		$.ajax({
			url: base_url + 'transaction/lcv/submit',
			type: 'post',
			data: {
				produk: $('#fpgroup').val()
			},
			success: function(r){
				cAlert.open('List Costumer Visit Telah Di Submit','success')
				refreshData()
			}
		})
	}
</script>