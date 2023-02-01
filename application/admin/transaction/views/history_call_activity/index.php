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
				<?php foreach (get_data('produk_grup', [
					'where' => [
						'kode_team' => $this->session->userdata('team')
					]
				])->result_array() as $val) {
					echo '<option value="' . $val['kode'] . '">' . $val['nama'] . '</option>';
				} ?>
			</select>
			<label>MR</label>
			<select class="select2" id="fmr" style="width: 150px;" onchange="filter()">
				<option value="">Pilih MR</option>
				<?php $this->db->having('total_visit_plan > 0');
				foreach (get_data('history_organogram_detail', [
					'select' => 'history_organogram_detail.*, (select count(*) from trxvisit_'.date('Y').'_'.date('m').' where dat is null and mr = history_organogram_detail.n_mr) as total_visit_plan',
					'where' => [
						'kode_team' => $this->session->userdata('team'),
						'n_am' => user('username'),
						'nama_mr !=' => ''
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
	table_open('',true,base_url('transaction/approval_visit_plan/data'),'trxvisit_'.date('Y').'_'.date('m'));
		thead();
			tr();
				th('#','text-center','width="30" data-content="id"');
				th('Dokter','','data-content="nama_dokter"');
				th('Specialist','','data-content="nama_spesialist"');
				th('Practice','','data-content="nama_outlet"');
				th('Plan Call','text-center','data-content="standard_call"');
				// th('Month','text-center','data-content="nama_bulan" data-custom="true"');
				// th('Year','text-center','data-content="tahun"');
				th('Approve?','text-center','data-type="boolean" data-content="status" data-boolean-text="APPROVE,NOT APPROVE" data-boolean-value="2,3"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>