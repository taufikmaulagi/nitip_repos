<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			Pilih Produk : 
			<select class="select2 infinity"  style="width:200px" id="produk" onchange="filter()">
				<option value=""></option>
				<?php foreach($this->session->userdata('produk_group') as $v): ?>
					<option value="<?=$v['kode']?>" <?=get('produk') == $v['kode'] ? 'selected="selected"' : ''?>><?=$v['nama']?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="main-container p-0">
		<table class="table table-app table-bordered">
			<thead>
				<th style="width: 1px; white-space:nowrap"> No. </th>
				<th> Marketing </th>
				<th style="width: 1px; white-space:nowrap"> Opsi </th>
			</thead>
			<?php foreach($marketing as $k => $v): ?>
				<tr>
					<td> <?=($k+1)?> </td>
					<td> <?=$v['nama']?> </td>
					<td> <button class="btn btn-sky btn-sm btn-detail" data-id="<?=$v['id']?>"> Detail </button> </td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>
<?php
	modal_open('modal-detail','Detail Plan Marketing','modal-lg');
	modal_body('modal-lg');
		col_init(4, 8);
		input('hidden','marketing','marketing');
		input('number','Target marketing','target');
		select2('Dokter','dokter[]','required',get_data('trxprof_'.date('Y').'_'.active_cycle().' a', [
			'select' => 'd.id, d.nama',
			'where' => [
				'a.mr' => user('username'),
				'a.produk_grup' => get('produk'),
				'a.status' => 2
			],
			'join' => [
				'dokter d on d.id = a.dokter'
			]
		])->result_array(),'id','nama','','multiple');
		echo '<hr/>';
	echo '<div class="text-right"><button class="btn btn-sky btn-sm btn-save">Simpan</button></div>';
	modal_close();
?>
<script>
	function filter(){
		let produk = $('#produk').val()
		location.replace(base_url + 'transaction/plan_marketing_activity?produk='+produk)
	}

	$(document).on('click','.btn-detail',function(){
		let id = $(this).attr('data-id')
		$('#marketing').val(id)
		$('#dokter').prop('selectedIndex', -1).trigger('change')
		$.ajax({
			url: base_url + 'transaction/plan_marketing_activity/get_detail?id='+id,
			success: function(r){
				$('#target').val(r.target)
				// $('select[name="dokter[]"]').val(r.dokter)
				if(r.dokter){
					$.each(JSON.parse(r.dokter), function(i,e){
						$("#dokter option[value='" + e + "']").prop("selected", true).trigger('change');
					});
				}
				$('#modal-detail').modal('show')
			}
		})
	})


	$(document).on('click','.btn-save', function(){
		$.ajax({
			url: base_url + 'transaction/plan_marketing_activity/save',
			data: {
				target: $('#target').val(),
				dokter: $("select[name='dokter[]']").map(function (idx, ele) {
							return $(ele).val();
						}).get(),
				id: $('#marketing').val()
			},
			type: 'post',
			success: function(r){
				cAlert.open(r.message, r.status)
			}
		})
	})


</script>
