<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info d-none d-sm-block">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/price_list/data'),'pricelist_detail');
		thead();
			tr();
				th(lang('id_pricelist'),'','data-content="id_pricelist"');
				th(lang('id_produk_oi'),'','data-content="id_produk_oi"');
				th(lang('id_produk'),'','data-content="id_produk"');
				th(lang('kode_distributor'),'','data-content="kode_distributor"');
				th(lang('kode_sector'),'','data-content="kode_sector"');
				th(lang('tanggal'),'','data-content="tanggal" data-type="daterange"');
				th(lang('hjp'),'','data-content="hjp"');
				th(lang('hna'),'','data-content="hna"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/price_list/save'),'post','form');
			col_init(3,9);
			input('text',lang('id_pricelist'),'id_pricelist');
			input('text',lang('id_produk_oi'),'id_produk_oi');
			input('text',lang('id_produk'),'id_produk');
			input('text',lang('kode_distributor'),'kode_distributor');
			input('text',lang('kode_sector'),'kode_sector');
			input('date',lang('tanggal'),'tanggal');
			input('text',lang('hjp'),'hjp');
			input('text',lang('hna'),'hna');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/price_list/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
