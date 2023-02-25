<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tools extends BE_Controller
{

	private $id_tools = '';

	function __construct()
	{
		parent::__construct();
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', -1);
		$this->id_tools = post('id');
	}

	function __destruct()
	{
		update_data('tbl_tools', [
			'last_updated' => date('Y-m-d H:i:s'),
			'execution_time' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]
		], 'id', $this->id_tools);
	}

	function index()
	{
		render();
	}

	function delete_visit_plan_prof_not_apprv()
	{
		$bulan = '09';
		$tahun = '2022';

		$profiling 	= get_data('trxprof_' . $tahun . '_' . active_cycle())->result_array();
		foreach ($profiling as $v) {
			if ($v['status'] == 3 || $v['status'] == '') {
				delete_data('trxvisit_' . $tahun . '_' . $bulan, 'profiling', $v['id']);
			}
		}
	}

	function data()
	{
		$config = [
			'button' => [
				button_serverside('btn-sky', 'btn-process', ['fa-paper-plane', 'Proses', 'true'], 'act_process', [
					'is_active' => 1
				])
			]
		];
		$data = data_serverside($config);
		render($data, 'json');
	}

	function get_data()
	{
		$data = get_data('tbl_tools', 'id', post('id'))->row_array();
		render($data, 'json');
	}

	function save()
	{
		$response = save_data('tbl_tools', post(), post(':validation'));
		render($response, 'json');
	}

	function delete()
	{
		$response = destroy_data('tbl_tools', 'id', post('id'));
		render($response, 'json');
	}

	function template()
	{
		ini_set('memory_limit', '-1');
		$arr = ['nama' => 'nama', 'url' => 'url'];
		$config[] = [
			'title' => 'template_import_tools',
			'header' => $arr,
		];
		$this->load->library('simpleexcel', $config);
		$this->simpleexcel->export();
	}

	function import()
	{
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['nama', 'url'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
		foreach ($jml as $i => $k) {
			if ($i == 0) {
				for ($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i, $j);
					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');
					$save = insert_data('tbl_tools', $data);
					if ($save) $c++;
				}
			}
		}
		$response = [
			'status' => 'success',
			'message' => $c . ' ' . lang('data_berhasil_disimpan') . '.'
		];
		@unlink($file);
		render($response, 'json');
	}

	function export()
	{
		ini_set('memory_limit', '-1');
		$arr = ['nama' => 'Nama', 'url' => 'Url'];
		$data = get_data('tbl_tools')->result_array();
		$config = [
			'title' => 'data_tools',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel', $config);
		$this->simpleexcel->export();
	}

	// =========================================================================================================================== 

	function process()
	{
		$id = post('id');
		$data = get_data('tbl_tools', 'id', $id)->row_array();
		if ($data) {
			$url = $data['url'];
			render($this->$url(), 'json');
		}
	}

	function sync_dokter_lama()
	{
		switch_database('sfe');
		$dokter = get_data('tm_doctor')->result_array();
		switch_database();
		delete_data('dokter');
		foreach ($dokter as $val) {
			$data = [
				'id'			=> $val['id_tm_doctor'],
				'nama' 			=> $val['fullname'],
				'tanggal_lahir'	=> $val['birthdate'],
				'spesialist'	=> $val['id_tm_specialist'],
				'subspesialist'	=> $val['id_tm_subspecialist'],
				'is_active'		=> $val['is_delete'] == 1 ? 0 : 1
			];
			insert_data('dokter', $data);
		}
	}

	function sync_outlet_siap()
	{
		switch_database('siap');
		$outlet = get_data('tbl_m_outlet_oi')->result_array();
		switch_database();
		delete_data('outlet');
		foreach ($outlet as $val) {
			$data = [
				'id'		=> $val['id'],
				'nama'		=> $val['outlet'],
				'alamat'	=> $val['outlet'],
				'branch'	=> $val['id_branch_oi'],
				'is_active'	=> $val['status']
			];
			insert_data('outlet', $data);
		}
	}

	function sync_users_siap()
	{
		switch_database('siap');
		$users = get_data('tbl_user')->result_array();
		switch_database();
		foreach ($users as $val) {
			$user = get_data('tbl_user', ['id' => $val['id']])->row_array();
			if ($user) {
				$data = [
					'id_group'		=> $val['id_group'],
					'kode'			=> $val['username'],
					'nama'			=> $val['nama'],
					'username'		=> $val['username'],
					'email'			=> $val['email'],
					'telepon'		=> $val['telepon']
				];
				update_data('tbl_user', $data, 'id', $val['id']);
			} else {
				$data = [
					'id'			=> $val['id'],
					'id_group'		=> $val['id_group'],
					'kode'			=> $val['username'],
					'password'		=> password_hash(md5('otsuka'), PASSWORD_DEFAULT),
					'nama'			=> $val['nama'],
					'username'		=> $val['username'],
					'email'			=> $val['email'],
					'telepon'		=> $val['telepon']
				];
				insert_data('tbl_user', $data);
			}
		}
	}

	function sync_organogram_siap()
	{
		switch_database('siap');
		$organogram = get_data('tbl_organogram')->result_array();
		$organogram_pegawai = get_data('tbl_organogram_pegawai')->result_array();
		$history_organogram = get_data('tbl_history_organogram')->result_array();
		$history_organogram_detail = get_data('tbl_history_organogram_detail')->result_array();
		$history_organogram_list = get_data('tbl_history_organogram_list')->result_array();
		$history_organogram_pegawai = get_data('tbl_history_organogram_pegawai')->result_array();
		switch_database();
		delete_data('organogram');
		delete_data('organogram_pegawai');
		delete_data('history_organogram');
		delete_data('history_organogram_detail');
		delete_data('history_organogram_list');
		delete_data('history_organogram_pegawai');
		foreach ($organogram as $val) {
			insert_data('organogram', $val);
		}
		foreach ($organogram_pegawai as $val) {
			insert_data('organogram_pegawai', $val);
		}
		foreach ($history_organogram as $val) {
			insert_data('history_organogram', $val);
		}
		foreach ($history_organogram_detail as $val) {
			insert_data('history_organogram_detail', $val);
		}
		foreach ($history_organogram_list as $val) {
			insert_data('history_organogram_list', $val);
		}
		foreach ($history_organogram_pegawai as $val) {
			insert_data('history_organogram_pegawai', $val);
		}


		$tahun = date('Y');
		$trxprof = 'trxprof_' . $tahun . '_' . active_cycle();
		// $trxvisit = 'trxvisit_'.$tahun.'_'.active_cycle();
		// $trxdact = 'trxdact_'.$tahun.'_'.
		// foreach($trxprof as $k => $v){
		$hod = get_data('history_organogram_detail hod', [
			'select' => 'hod.*',
			'join' => [
				'history_organogram ho on ho.id = hod.id_organogram',
			],
			'where' => [
				'ho.tanggal_end' => '0000-00-00'
			],
			'group_by' => 'n_mr'
		])->result_array();

		foreach ($hod as $k => $v)

			// Profiiling
			update_data('trxprof_' . $tahun . '_' . active_cycle(), [
				'am' => $v['n_am'],
				'rm' => $v['n_rm'], //  KOSONG 
				'nsm' => $v['n_nsm'],
			], 'mr', $v['n_mr']);

		// Merubah struktur visit plan
		for ($i = 1; $i <= 12; $i++) {
			if (table_exists('trxvisit_' . $tahun . '_' . sprintf('%02d', $i))) {
				update_data('trxvisit_' . $tahun . '_' . sprintf('%02d', $i), [
					'am' => $v['n_am'],
					'rm' => $v['n_rm'],
					'nsm' => $v['n_nsm'],
				], 'mr', $v['n_mr']);
			}
		}

		// Merubah struktur DFR
		for ($i = 1; $i <= 12; $i++) {
			if (table_exists('trxdfr_' . $tahun . '_' . sprintf('%02d', $i))) {
				update_data('trxdfr_' . $tahun . '_' . sprintf('%02d', $i), [
					'am' => $v['n_am'],
					'rm' => $v['n_rm'],
					'nsm' => $v['n_nsm'],
				], 'mr', $v['n_mr']);
			}
		}

		// Merubah struktur Data Actual by Customer
		for ($i = 1; $i <= 12; $i++) {
			if (table_exists('trxdact_' . $tahun . '_' . sprintf('%02d', $i))) {
				update_data('trxdact_' . $tahun . '_' . sprintf('%02d', $i), [
					'am' => $v['n_am'],
					'rm' => $v['n_rm'],
					'nsm' => $v['n_nsm'],
				], 'mr', $v['n_mr']);
			}
		}
	}

	function generate_raw_data($tahun = '2022', $bulan = '12')
	{
		$bulan = sprintf("%02d", $bulan);
		if (in_array($bulan, ['01', '02', '03', '04'])) {
			$cycle = 1;
		} else if (in_array($bulan, ['05', '06', '07', '08'])) {
			$cycle = 2;
		} else if (in_array($bulan, ['09', '10', '11', '12'])) {
			$cycle = 3;
		}
		$produk_grup = get_data('produk_grup', [
			'where' => [
				'is_active' => 1,
			]
		])->result_array();

		foreach ($produk_grup as $produk_grup) {
			$data = [];
			$profiling = get_data('trxprof_' . $tahun . '_' . $cycle . ' a', [
				'select' => 'a.id, a.mr, a.dokter, tbl_user.nama as nama_mr, a.nama_dokter, a.nama_outlet, a.nama_spesialist, b.val_indikasi_1, b.val_indikasi_2, b.val_indikasi_3, b.val_indikasi_4, b.val_indikasi_5, b.val_indikasi_6, b.val_indikasi_7, b.val_indikasi_8, b.val_indikasi_9, b.val_indikasi_10,
				value_1 as value_1, 
				value_2 as value_2, 
				value_3 as value_3, 
				value_4 as value_4, 
				value_5 as value_5, 
				value_6 as value_6, 
				value_7 as value_7, 
				value_8 as value_8, 
				value_9 as value_9, 
				value_10 as value_10,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_1 end) as value_abi_1,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_2 end) as value_abi_2,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_3 end) as value_abi_3,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_4 end) as value_abi_4,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_5 end) as value_abi_5,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_6 end) as value_abi_6,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_7 end) as value_abi_7,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_8 end) as value_abi_8,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_9 end) as value_abi_9,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_10 end) as value_abi_10,
				sum(case when d.produk in ("145","146","147","148") then value_1 end) as value_rex_1,
				sum(case when d.produk in ("137","138") then value_1 end) as value_maintena_1,
				sum(case when d.produk in ("137","138") then value_2 end) as value_maintena_2',
				'join' => [
					'trxprof_indikasi_' . $tahun . '_' . $cycle . ' b on a.id = b.profiling type left',
					'trxdact_' . $tahun . '_' . $bulan . ' c on a.dokter = c.dokter type left',
					'trxdact_sku_' . $tahun . '_' . $bulan . ' d on d.data_sales = c.id type left',
					'tbl_user on tbl_user.username = a.mr',
				],
				'where' => [
					'b.bulan' => $bulan,
					'a.produk_grup' => $produk_grup['kode'],
					//'a.mr' => ['02553','02517','03293']
				],
				'group_by' => 'a.dokter,a.mr',
			])->result_array();
			//debug($profiling); die;
			$produk = get_data('produk', [
				'select' => 'produk.*',
				'join' => [
					'produk_subgrup on produk_subgrup.kode = produk.kode_subgrup',
				],
				'where' => [
					'kode_grup' => $produk_grup['kode'],
					'produk.is_active' => 1,
				],
			])->result_array();
			foreach ($profiling as $val) {

				$call = get_data('trxvisit_' . $tahun . '_' . $bulan . ' a', [
					'select' => '(if(a.status = 3, a.plan_call, 0)) as plan_call, 
						(if(a.status = 3 and b.status = 2, count(b.id), 0)) as actual_call,
						(if(a.plan_call > 0 and a.status = 3, 1, 0)) as plan_dokter_coverage,
						(if(b.status = 2, 1, 0)) as actual_dokter_coverage,
						if(a.plan_call = 0, 0, (if(a.status = 3, 1, 0))) as plan_percent_coverage,
						if(a.plan_call = 0, 0, (if(a.plan_call <= count(b.id), 1, 0))) as actual_percent_coverage,
						(if(b.call_type = 1, count(b.id), 0)) as call_type_a,
						(if(b.call_type = 2, count(b.id), 0)) as call_type_b,
						(if(b.call_type = 3, count(b.id), 0)) as call_type_c,',
					'join' => [
						'trxdfr_' . $tahun . '_' . $bulan . ' b on b.visit_plan = a.id type left'
					],
					'where' => [
						'a.mr' => $val['mr'],
						'a.dokter' => $val['dokter'],
					],
					'group_by' => 'a.dokter',
					'sort_by' => 'a.nama_dokter',
					'sort' => 'ASC',
				])->row_array();

				if (!$call) {
					$call = [
						'plan_call' => 0,
						'actual_call' => 0,
						'plan_dokter_coverage' => 0,
						'actual_dokter_coverage' => 0,
						'plan_percent_coverage' => 0,
						'actual_percent_coverage' => 0,
						'call_type_a' => 0,
						'call_type_b' => 0,
						'call_type_c' => 0,
					];
				}

				$sub_call_type_a = get_data('trxdfr_' . $tahun . '_' . $bulan . ' a', [
					'select' => 'a.sub_call_type',
					'where' => [
						'a.dokter' => $val['dokter'],
						'a.mr' => $val['mr'],
						'a.call_type' => 1,
					],
				])->result_array();

				if (!$sub_call_type_a) {
					$sub_call_type_a = [
						'sub_call_type' => [],
					];
				} else {
					$sub_call_type_a = [
						'sub_call_type' => array_column($sub_call_type_a, 'sub_call_type'),
					];
				}

				$sub_call_type_b = get_data('trxdfr_' . $tahun . '_' . $bulan . ' a', [
					'select' => 'a.sub_call_type',
					'where' => [
						'a.dokter' => $val['dokter'],
						'a.mr' => $val['mr'],
						'a.call_type' => 2,
					],
				])->result_array();

				if (!$sub_call_type_b) {
					$sub_call_type_b = [
						'sub_call_type' => [],
					];
				} else {
					$sub_call_type_b = [
						'sub_call_type' => array_column($sub_call_type_b, 'sub_call_type'),
					];
				}

				$sub_call_type_c = get_data('trxdfr_' . $tahun . '_' . $bulan . ' a', [
					'select' => 'a.sub_call_type',
					'where' => [
						'a.dokter' => $val['dokter'],
						'a.mr' => $val['mr'],
						'a.call_type' => 3,
					],
				])->result_array();

				if (!$sub_call_type_c) {
					$sub_call_type_c = [
						'sub_call_type' => [],
					];
				} else {
					$sub_call_type_c = [
						'sub_call_type' => array_column($sub_call_type_c, 'sub_call_type'),
					];
				}

				$jumlah_potensi = $val['val_indikasi_1'] + $val['val_indikasi_2'] + $val['val_indikasi_3'] + $val['val_indikasi_4'] + $val['val_indikasi_5'] + $val['val_indikasi_6'] + $val['val_indikasi_7'] + $val['val_indikasi_8'] + $val['val_indikasi_9'] + $val['val_indikasi_10'];
				$total_pasien = $val['value_1'] + $val['value_2'] + $val['value_3'] + $val['value_4'] + $val['value_5'] + $val['value_6'] + $val['value_7'] + $val['value_8'] + $val['value_9'] + $val['value_10'];
				$tmp_data = [
					'profiling' =>  $val['id'],
					'mr' => $val['mr'],
					'nama_mr' => $val['nama_mr'],
					'produk_grup' => $produk_grup['kode'],
					'nama_dokter' => $val['nama_dokter'],
					'nama_outlet' => $val['nama_outlet'] ? $val['nama_outlet'] : 'Regular',
					'nama_spesialist' => strtoupper($val['nama_spesialist']),
					'indikasi_1' => $val['val_indikasi_1'],
					'indikasi_2' => $val['val_indikasi_2'],
					'indikasi_3' => $val['val_indikasi_3'],
					'indikasi_4' => $val['val_indikasi_4'],
					'indikasi_5' => $val['val_indikasi_5'],
					'indikasi_6' => $val['val_indikasi_6'],
					'indikasi_7' => $val['val_indikasi_7'],
					'indikasi_8' => $val['val_indikasi_8'],
					'indikasi_9' => $val['val_indikasi_9'],
					'indikasi_10' => $val['val_indikasi_10'],
					'total_potensi' => $jumlah_potensi,
					'pasien_1' => $val['value_1'],
					'pasien_2' => $val['value_2'],
					'pasien_3' => $val['value_3'],
					'pasien_4' => $val['value_4'],
					'pasien_5' => $val['value_5'],
					'pasien_6' => $val['value_6'],
					'pasien_7' => $val['value_7'],
					'pasien_8' => $val['value_8'],
					'pasien_9' => $val['value_9'],
					'pasien_10' => $val['value_10'],
					'total_pasien' => $total_pasien,
					'plan_call' => $call['plan_call'] ? $call['plan_call'] : 0,
					'actual_call' => $call['actual_call'] ? $call['actual_call'] : 0,
					'percent_call' => $call['plan_call'] && $call['actual_call'] ? ($call['actual_call'] / $call['plan_call']) * 100 : 0,
					'plan_dokter_coverage' => $call['plan_dokter_coverage'] ? $call['plan_dokter_coverage'] : 0,
					'actual_dokter_coverage' => $call['actual_dokter_coverage'] ? $call['actual_dokter_coverage'] : 0,
					'percent_dokter_coverage' => $call['plan_dokter_coverage'] && $call['actual_dokter_coverage'] ? ($call['actual_dokter_coverage'] / $call['plan_dokter_coverage']) * 100 : 0,
					'plan_percent_coverage' => $call['plan_percent_coverage'] ? $call['plan_percent_coverage'] : 0,
					'actual_percent_coverage' => $call['actual_percent_coverage'] ? $call['actual_percent_coverage'] : 0,
					'percent_percent_coverage' => $call['plan_percent_coverage'] && $call['actual_percent_coverage'] ? ($call['actual_percent_coverage'] / $call['plan_percent_coverage']) * 100 : 0,
					'use_confirm' => get_status_dokter($produk_grup['kode'], $total_pasien),
					'customer_matrix' => get_customer_matrix(
						$produk_grup['kode'],
						get_status_dokter($produk_grup['kode'], $total_pasien),
						get_kriteria_potensi($produk_grup['kode'], $jumlah_potensi)
					),
					'call_type_a' => $call['call_type_a'] ? $call['call_type_a'] : 0,
					'call_type_b' => $call['call_type_b'] ? $call['call_type_b'] : 0,
					'call_type_c' => $call['call_type_c'] ? $call['call_type_c'] : 0,
					'sub_call_type_a' => json_encode($sub_call_type_a['sub_call_type']),
					'sub_call_type_b' => json_encode($sub_call_type_b['sub_call_type']),
					'sub_call_type_c' => json_encode($sub_call_type_c['sub_call_type']),
				];
				if ($produk_grup['kode'] == 'EH') {
					$tmp_data['pasien_1'] = $val['value_abi_1'] ? $val['value_abi_1'] : 0;
					$tmp_data['pasien_2'] = $val['value_abi_2'] ? $val['value_abi_2'] : 0;
					$tmp_data['pasien_3'] = $val['value_abi_3'] ? $val['value_abi_3'] : 0;
					$tmp_data['pasien_4'] = $val['value_abi_4'] ? $val['value_abi_4'] : 0;
					$tmp_data['pasien_5'] = $val['value_abi_5'] ? $val['value_abi_5'] : 0;
					$tmp_data['pasien_6'] = $val['value_abi_6'] ? $val['value_abi_6'] : 0;
					$tmp_data['pasien_7'] = $val['value_abi_7'] ? $val['value_abi_7'] : 0;
					$tmp_data['pasien_8'] = $val['value_abi_8'] ? $val['value_abi_8'] : 0;
					$tmp_data['pasien_9'] = $val['value_abi_9'] ? $val['value_abi_9'] : 0;
					$tmp_data['pasien_10'] = $val['value_abi_10'] ? $val['value_abi_10'] : 0;



					$tmp_data['pasien_maintena_1'] 	= $val['value_maintena_1'] ? $val['value_maintena_1'] : 0;
					$tmp_data['pasien_maintena_2'] 	= $val['value_maintena_2'] ? $val['value_maintena_2'] : 0;
					$tmp_data['pasien_rexulti_1'] 	= $val['value_rex_1'] ? $val['value_rex_1'] : 0;


					$tmp_data['use_confirm_maintena'] = get_status_dokter('MT', ($val['value_maintena_1'] + $val['value_maintena_2']));
					$tmp_data['use_confirm_rexulti'] = get_status_dokter('EO', ($val['value_rex_1']));
					$tmp_data['customer_matrix_maintena'] = get_customer_matrix(
						'MT',
						get_status_dokter('MT', ($val['value_maintena_1'] + $val['value_maintena_2'])),
						get_kriteria_potensi('MT', ($val['val_indikasi_1'] + $val['val_indikasi_2']))
					);
					$tmp_data['customer_matrix_rexulti'] = get_customer_matrix(
						'EO',
						get_status_dokter('EO', ($val['value_rex_1'])),
						get_kriteria_potensi('EO', ($val['val_indikasi_1']))
					);
				}
				foreach ($produk as $pval) {
					$data_sales = get_data('trxdact_' . $tahun . '_' . $bulan . ' a', [
						'select' => '(number_of_unit) as total_sales, b.price',
						'join' => [
							'trxdact_sku_' . $tahun . '_' . $bulan . ' b on a.id = b.data_sales',
						],
						'where' => [
							'a.mr' => $val['mr'],
							'a.dokter' => $val['dokter'],
							'produk' => $pval['id'],
						]
					])->row_array();
					$tmp_data[$pval['id'] . '_produk'] = $data_sales['total_sales'] ? $data_sales['total_sales'] : 0;
					$tmp_data[$pval['id'] . '_price'] = $data_sales['price'] ? $data_sales['price'] : 0;
				}
				$data[] = $tmp_data;
			}
			$this->load->helper('generate_trx_table');
			if ($this->db->table_exists('raw_data_' . $produk_grup['kode'] . '_' . $tahun . '_' . $bulan)) {
				$this->db->query('drop table raw_data_' . $produk_grup['kode'] . '_' . $tahun . '_' . $bulan);
			}
			init_table_raw_data($tahun, $bulan, $produk_grup['kode']);
			insert_batch('raw_data_' . $produk_grup['kode'] . '_' . $tahun . '_' . $bulan, $data);
		}
	}

	function sync_team_dari_siap()
	{
		switch_database('siap');
		$data = get_data('tbl_team')->result_array();

		switch_database('default');
		foreach ($data as $k => $v) {
			$check = get_data('tim', [
				'where' => [
					'kode' => $v['kode']
				]
			])->row_array();

			if ($check) {
				update_data('tim', [
					'is_active' => $v['status']
				], 'id', $check['id']);
			} else {
				insert_data('tim', [
					'kode' => $v['kode'],
					'nama' => $v['team'],
					'divisi' => $v['kode_divisi'],
					'is_active' => $v['status']
				]);
			}
		}
	}
}
