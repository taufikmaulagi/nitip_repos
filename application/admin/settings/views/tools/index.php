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
	table_open('',true,base_url('settings/tools/data'),'tbl_tools');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('nama'),'','data-content="nama"');
				th(lang('url'),'','data-content="url"');
				th('Last Updated','','data-content="last_updated" data-type="date"');
				th('Perkiraan Waktu Proses','','data-content="execution_time"');
				th('Aktif?','','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/tools/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('nama'),'nama','required');
			textarea('URL','url','required');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/tools/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
<script>
	$(document).on('click','.btn-process', function(){
		var c_icon = $(this).find('i');
		c_icon.attr('data-class',c_icon.attr('class'));
		c_icon.attr('class','d-block fa-spinner fa-spin');
		$.ajax({
			url: base_url + 'settings/tools/process',
			type: 'post',
			data: {id: $(this).attr('data-id')},
			success: function(resp){
				cAlert.open('Selesai diproses.', 'success');
				c_icon.attr('data-class',c_icon.attr('class'));
				c_icon.attr('class','fa-paper-plane fa-spin');
			}
		})
	})
</script>
