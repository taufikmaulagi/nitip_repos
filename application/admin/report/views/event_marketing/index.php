<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<select class="select2 infinity" style="width:150px" id="fbulan" onchange="filter()">
				<?php for($i=1;$i<=12;$i++){
					echo '<option value="'.$i.'" '.(get('bulan') == $i ? 'selected="selected"' : ($i == intval(date('m') && !get('bulan')) ? 'selected="selected"' : '')).'>'.nama_bulan($i).'</option>';
				} ?>
			</select>
			<select class="select2 infinity" style="width:100px" id="ftahun" onchange="filter()">
				<?php for($i=2019;$i<=date('Y');$i++){
					echo '<option value="'.$i.'" '.(get('tahun') == $i ? 'selected="selected"' : ($i == intval(date('Y') && !get('tahun')) ? 'selected="selected"' : '')).'>'.$i.'</option>';
				} ?>
			</select>
			<select class="select2" style="width:200px" id="fmr" onchange="filter()">
				<?php if(user('id_group') == 9): ?>
					<option value="<?=user('id')?>"><?=user('nama')?></option>
				<?php else: ?>
					<option value="">Pilih MR</option>
					<?php foreach($mr as $v){
						echo '<option value="'.$v['kode'].'" '.(get('mr') == $v['kode'] ? 'selected="selected"' : '').'>'.$v['nama'].'</option>';
					} ?>
				<?php endif; ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
		if(get('mr')){
			$tahun = get('tahun') ? get('tahun') : date('Y');
			$bulan = get('bulan') ? sprintf('%02d',get('bulan')) : date('m');
			table_open('',true,base_url('report/event_marketing/data?mr='.get('mr').'&bulan='.$bulan.'&tahun='.$tahun),'trxdact_'.$tahun.'_'.$bulan);
			thead();
				tr();
					th('#','text-center','width="30" data-content="id"');
					th('Event','','data-content="nama_event" data-custom="true"');
					th('Tanggal','','data-content="tanggal_realisasi" data-custom="true"');
					th('Produk Grup','','data-content="nama_produk_grup"');
					th('&nbsp;','','width="30" data-content="action_button"');
			table_close();
		}
	?>
</div>
<?php
	modal_open('modal-form','Detail Event','modal-lg');
		modal_body('p-0'); ?>
		<pre class="p-3">
			Event 			: <span id="nEvent"></span>
			Sub Marketing		: <span id="nSub"></span>
			Nama Speaker		: <span id="nSpeaker"></span>
			Tanggal 		: <span id="nTanggal"></span>
			
		</pre>
		<table class="table table-app" id="dTable">
			<thead>
				<th> No. </th>
				<th> Doctor Name </th>
				<th> Spesialist </th>
				<th> Practice Address </th>
				<th> MR Name </th>
				<th> Baseline Matrix </th>
				<th> Matrix After Event </th>
			</thead>
			<tbody>
			</tbody>
		</table>
		
	<?php modal_footer();
	modal_close();
?>

<script>
	function filter(){

		let bulan 	= $('#fbulan').val()
		let tahun 	= $('#ftahun').val()
		let mr		= $('#fmr').val()

		location.replace(base_url + 'report/event_marketing?mr='+mr+'&tahun='+tahun+'&bulan='+bulan)
	}

	$(document).on('click', '.btn-detail', function(){
		let id 			= $(this).attr('data-id')
		let produk_grup	= $(this).closest('td').prev().text()
		let tanggal 	= $(this).closest('td').prev().prev().text()
		let marketing	= $(this).closest('td').prev().prev().prev().text()
		let bulan 		= $('#fbulan').val()
		let tahun 		= $('#ftahun').val()
		$.ajax({
			url		: base_url + 'report/event_marketing/get_data?data_sales='+id+'&bulan='+bulan+'&tahun='+tahun+'&marketing='+marketing+'&produk_grup='+produk_grup+'&tanggal='+tanggal,
			success	: function(r){
				let detail 	= r.detail
				let data	= r.data

				$('#nEvent').html(detail.nama_event)
				$('#nSpeaker').html(detail.nama_speaker)
				$('#nTanggal').html(detail.tanggal)
				$('#nSub').html(detail.nama_sub)

				let html_table = ''
				$.each(data, function(k,v){
					html_table += '<tr>'
						html_table += '<td>'+(k+1)+'</td>'
						html_table += '<td>'+(v.nama_dokter)+'</td>'
						html_table += '<td>'+(v.nama_spesialist)+'</td>'
						html_table += '<td>'+(v.nama_outlet == null ? '--' : v.nama_outlet)+'</td>'
						html_table += '<td>'+(v.nama_mr)+'</td>'
						html_table += '<td>'+(v.prev_matrix)+'</td>'
						html_table += '<td>'+(v.current_matrix)+'</td>'
					html_table += '</tr>'
				})
				$('#dTable > tbody').html(html_table)
				$('#modal-form').modal('show')
			}
		})
	})
</script>
