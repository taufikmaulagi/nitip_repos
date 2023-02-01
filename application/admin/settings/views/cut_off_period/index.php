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
	table_open('',true,base_url('settings/cut_off_period/data'),'cutoff_period');
		thead();
			tr();
				th(lang('tahun'),'','data-content="tahun"');
				th(lang('bulan'),'','data-content="bulan"');
				th(lang('start_date'),'','data-content="start_date" data-type="daterange"');
				th(lang('end_date'),'','data-content="end_date" data-type="daterange"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/cut_off_period/save'),'post','form');
			col_init(3,9);
			input('text',lang('tahun'),'tahun');
			input('text',lang('bulan'),'bulan');
			input('date',lang('start_date'),'start_date');
			input('date',lang('end_date'),'end_date');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/cut_off_period/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
