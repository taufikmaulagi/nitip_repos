<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History_profiling extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data(){

		$data['profiling'] = [];
		
		if(in_array(post('fbulan'), [1,2,3,4])){
			$cycle = 1;
		} elseif(in_array(post('fbulan'), [5,6,7,8])){
			$cycle = 2;
		} else {
			$cycle = 3;
		}

		if($this->db->table_exists('trxprof_'.post('ftahun').'_'.$cycle)){
			if(!table_exists('trxprof_indikasi_'.post('ftahun').'_'.$cycle)){
				$this->load->helper('generate_trx_table');
				init_table_prof_indikasi($cycle, post('ftahun'));
			}
			$data['profiling'] = get_data('trxprof_'.post('ftahun').'_'.$cycle, [
				'select' => 'trxprof_'.post('ftahun').'_'.$cycle.'.*, 
					sum(trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.potensi_tablet) as total_potensi_tablet, 
					sum(trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.val_indikasi_1) as value_indikasi_1,
					sum(trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.val_indikasi_2) as value_indikasi_2,
					sum(trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.val_indikasi_3) as value_indikasi_3,
					sum(trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.val_indikasi_4) as value_indikasi_4,
					sum(trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.val_indikasi_5) as value_indikasi_5,
					sum(trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.val_indikasi_6) as value_indikasi_6,
					sum(trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.val_indikasi_7) as value_indikasi_7,
					sum(trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.val_indikasi_8) as value_indikasi_8',
				'where' => [
					'produk_grup' => post('fpgroup'),
					'mr' => post('fmr'),
					// 'apprv_at !=' => null,
				],
				'where_in' => [
					'status' => [2,3]
				],
				'join' => [
					'trxprof_indikasi_'.post('ftahun').'_'.$cycle.' on trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.profiling = trxprof_'.post('ftahun').'_'.$cycle.'.id and trxprof_indikasi_'.post('ftahun').'_'.$cycle.'.bulan = \''.post('fbulan').'\' type left'
				],
				'group_by' => 'dokter',
				'sort_by' => 'nama_dokter',
				'sort' => 'ASC'
			])->result_array();
			// echo $this->db->last_query(); die;
		}

		

		render($data,'layout:false');
	}

	function get_all($bulan, $tahun, $mr){

		if(in_array($bulan, [1,2,3,4])){
			$cycle = 1;
		} elseif(in_array($bulan, [5,6,7,8])){
			$cycle = 2;
		} else {
			$cycle = 3;
		}
		$profiling = get_data('trxprof_'.$tahun.'_'.$cycle,[
			'where' => [
				'id' => get('id')
			]
		])->row_array();
		if(count($profiling)<=0){
			return 0;
		} else {
			$data['profiling'] = get_data('trxprof_'.$tahun.'_'.$cycle, [
				'select' => 'trxprof_'.$tahun.'_'.$cycle.'.*, 
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.potensi_tablet) as total_potensi_tablet, 
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_1) as val_indikasi_1,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_2) as val_indikasi_2,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_3) as val_indikasi_3,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_4) as val_indikasi_4,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_5) as val_indikasi_5,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_6) as val_indikasi_6,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_7) as val_indikasi_7,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_8) as val_indikasi_8,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_9) as val_indikasi_9,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_10) as val_indikasi_10',
				'where' => [
					'dokter' => $profiling['dokter'],
					// 'apprv_at !=' => null,
					'mr' => $mr
				],
				'where_in' => [
					'status' => [2,3]
				],
				'join' => [
					'trxprof_indikasi_'.$tahun.'_'.$cycle.' on trxprof_indikasi_'.$tahun.'_'.$cycle.'.profiling = trxprof_'.$tahun.'_'.$cycle.'.id and trxprof_indikasi_'.$tahun.'_'.$cycle.'.bulan = \''.$bulan.'\' type left'
				],
				'sort_by' => 'nama_outlet',
				'sort' => 'ASC'
			])->result_array();
			render($data['profiling'], 'json');
		}
	}
	
	function get_data($bulan, $tahun) {

		if(in_array($bulan, [1,2,3,4])){
			$cycle = 1;
		} elseif(in_array($bulan, [5,6,7,8])){
			$cycle = 2;
		} else {
			$cycle = 3;
		}

		$data = get_data('trxprof_'.$tahun.'_'.$cycle,[
			'select' => 'trxprof_'.$tahun.'_'.$cycle.'.*, 
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.potensi_tablet) as total_potensi_tablet, 
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_1) as val_indikasi_1,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_2) as val_indikasi_2,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_3) as val_indikasi_3,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_4) as val_indikasi_4,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_5) as val_indikasi_5,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_6) as val_indikasi_6,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_7) as val_indikasi_7,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_8) as val_indikasi_8,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_8) as val_indikasi_9,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.val_indikasi_8) as val_indikasi_10,
				sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.fee_patient) as fee_patient,
				trxprof_indikasi_'.$tahun.'_'.$cycle.'.ap_original as ap_original, sum(trxprof_indikasi_'.$tahun.'_'.$cycle.'.jumlah_pasien) as total_jumlah_pasien',
			'where' => [
				'trxprof_'.$tahun.'_'.$cycle.'.id' => post('id')
			],
			'join' => [
				'trxprof_indikasi_'.$tahun.'_'.$cycle.' on trxprof_indikasi_'.$tahun.'_'.$cycle.'.profiling = trxprof_'.$tahun.'_'.$cycle.'.id and trxprof_indikasi_'.$tahun.'_'.$cycle.'.bulan = \''.$bulan.'\' type left'
			],
		])->row_array();
		render($data,'json');
	}

	function get_produk_grup(){
		$data = get_data('produk_grup', [
			'where' => [
				'kode_team' => get('team'),
				'kode_divisi' => 'E'
			]
		])->result_array();
		render($data, 'json');
	}

	function get_am(){
		$where = [
			'history_organogram.kode_team' => get('team'),
			'nama_am !=' => '',
			'history_organogram.kode_divisi' => 'E',
			'history_organogram.tanggal_end' => '0000-00-00'
		];
		if(user('id_group') == MR_ROLE_ID){
			$where['n_mr'] = user('username');
		} else if(user('id_group') == AM_ROLE_ID){
			$where['n_am'] = user('username');
		}
		$data = get_data('history_organogram_detail', [
			'join' => [
				'history_organogram on history_organogram.id = history_organogram_detail.id_history_organogram'
			],
			'where' => $where,
			'group_by' => 'nama_am',
			'sort_by' => 'nama_am',
			'sort' => 'ASC'
		])->result_array();
		render($data, 'json');
	}

	function get_mr(){
		$where = [
			'history_organogram.kode_team' => get('team'),
			'n_am' => get('am'),
			'nama_mr !=' => '',
			'history_organogram.kode_divisi' => 'E',
			'history_organogram.tanggal_end' => '0000-00-00'
		];
		if(user('id_group') == MR_ROLE_ID){
			$where['n_mr'] = user('username');
		}
		$data = get_data('history_organogram_detail', [
			'join' => [
				'history_organogram on history_organogram.id = history_organogram_detail.id_history_organogram'
			],
			'where' => $where,
			'group_by' => 'nama_mr',
			'sort_by' => 'nama_mr',
			'sort' => 'ASC'
		])->result_array();
		render($data, 'json');
	}

	function get_indikasi(){
		$where = [];
		if(get('pgrup')){
			$where['produk_grup'] 	= get('pgrup');
			$where['is_active'] 	= 1;
		}
		$data = get_data('indikasi', [
			'where' => $where
		])->result_array();
		if(!empty($where)){
			render($data, 'json');
		}
	}

	function approval($bulan, $tahun){

		if(in_array($bulan, [1,2,3,4])){
			$cycle = 1;
		} elseif(in_array($bulan, [5,6,7,8])){
			$cycle = 2;
		} else {
			$cycle = 3;
		}

		$alasan_not_approve = post('alasan_not_approve');
		$this->db->trans_begin();
		$profiling = get_data('trxprof_'.$tahun.'_'.$cycle, 'id', post('id'))->row_array();
		if(!empty($alasan_not_approve)){
			update_data('trxprof_'.$tahun.'_'.$cycle, [
				'status' => '3',
				'alasan_not_approve' => $alasan_not_approve,
				'apprv_at' => NULL
			], 'id', post('id'));
		} else {
			update_data('trxprof_'.$tahun.'_'.$cycle, [
				'status' => '2',
			], 'id', post('id'));
		}
		if($this->db->trans_status()===TRUE){
			$this->db->trans_commit();
		} else {
			$this->db->trans_rollback();
		}

		render(['status' => $this->db->trans_status()],'json');

	}

	// function update($bulan, $tahun) {

	// 	if(in_array($bulan, [1,2,3,4])){
	// 		$cycle = 1;
	// 	} elseif(in_array($bulan, [5,6,7,8])){
	// 		$cycle = 2;
	// 	} else {
	// 		$cycle = 3;
	// 	}

	// 	// $data = post();
		
	// 	$data['ap_original'] = 0;
	// 	if(post('ap_original') == "on"){
	// 		$data['ap_original'] = 1;
	// 	}
	// 	for($i = 1; $i <= 10; $i++){
	// 		$data['indikasi_'.$i] = post('indikasi_'.$i);
	// 	}
	// 	$data['fee_patient'] 	= post('fee_patient');
	// 	$data['jumlah_pasien'] 	= post('ejumlah_pasien') ? post('ejumlah_pasien') : post('jumlah_pasien');

	// 	update_data('trxprof_'.$tahun.'_'.$cycle, $data,'id',post('id'));

	// 	if(!table_exists('trxprof_indikasi_'.$tahun.'_'.$cycle)){
	// 		$this->load->helper('generate_trx_table');
	// 		init_table_prof_indikasi($cycle, $tahun);
	// 	}

	// 	$data_indikasi = get_data('trxprof_indikasi_'.$tahun.'_'.$cycle, [
	// 		'where' => [
	// 			'profiling' => post('id'),
	// 			'bulan' => $bulan
	// 		]
	// 	])->result_array();
	// 	if($data_indikasi){
	// 		delete_data('trxprof_indikasi_'.$tahun.'_'.$cycle, [
				
	// 				'profiling' => post('id'),
	// 				'bulan' => $bulan
				
	// 		]);
	// 	}

	// 	$data['profiling'] = post('id');
	// 	$data['bulan'] = $bulan;
	// 	$data['potensi_tablet'] =  (
	// 		intval((post('indikasi_1') ? post('indikasi_1') : post('eindikasi_1'))) + 
	// 		intval((post('indikasi_2') ? post('indikasi_2') : post('eindikasi_2'))) + 
	// 		intval((post('indikasi_3') ? post('indikasi_3') : post('eindikasi_3'))) + 
	// 		intval((post('indikasi_4') ? post('indikasi_4') : post('eindikasi_4'))) + 
	// 		intval((post('indikasi_5') ? post('indikasi_5') : post('eindikasi_5'))) + 
	// 		intval((post('indikasi_6') ? post('indikasi_6') : post('eindikasi_6'))) + 
	// 		intval((post('indikasi_7') ? post('indikasi_7') : post('eindikasi_7'))) + 
	// 		intval((post('indikasi_8') ? post('indikasi_8') : post('eindikasi_8')))
	// 	);
	// 	insert_data('trxprof_indikasi_'.$tahun.'_'.$cycle, $data);

	// 	// if($cycle == 1){
	// 	// 	$bulan = ['01','02','03','04'];
	// 	// } else if($cycle == 2){
	// 	// 	$bulan = ['05','06','07','08'];
	// 	// } else if($cycle == 3){
	// 	// 	$bulan = ['09','10','11','12'];
	// 	// }

	// 	$profiling = get_data('trxprof_indikasi_'.$tahun.'_'.$cycle,[
	// 		'select' => '(indikasi_1+indikasi_2+indikasi_3+indikasi_4+indikasi_5+indikasi_6+indikasi_7+indikasi_8) as jumlah_pasien',
	// 		'where' => [
	// 			'profiling' => post('id'),
	// 			'bulan' =>  $bulan
	// 		]
	// 	])->row_array();
	// 	$data_profiling = get_data('trxprof_'.$tahun.'_'.$cycle, 'id', post('id'))->row_array();

	// 	// foreach($bulan as $val){
	// 		if($this->db->table_exists('trxdact_'.$tahun.'_'.$bulan)){
	// 			$data_actual = get_data('trxdact_'.$tahun.'_'.$bulan, [
	// 				'where' => [
	// 					'dokter' => $data_profiling['dokter'],
	// 					'mr' => $data_profiling['mr']
	// 				]
	// 			])->row_array();
	// 			if(!empty($data_actual)){
	// 				$kriteria_potensi_maintena	= '';
	// 				$kriteria_potensi_rexulti	= '';
	// 				$status_dokter_maintena 	= '';
	// 				$status_dokter_rexulti 		= '';
	// 				$customer_matrix_maintena 	= '';
	// 				$customer_matrix_rexulti 	= '';
	// 				if($data_profiling['produk_grup'] == 'EH'){	
	// 					$kriteria_potensi = get_kriteria_potensi($data_profiling['produk_grup'], $profiling['jumlah_pasien'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
	// 					$kriteria_potensi_maintena	= get_kriteria_potensi('MT', $profiling['jumlah_pasien'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
	// 					$kriteria_potensi_rexulti	= get_kriteria_potensi('EO', $profiling['jumlah_pasien'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
	// 					$status_dokter_maintena 	= get_status_dokter('MT', $data_actual['jumlah_pasien_maintena']);
	// 					$status_dokter_rexulti 		= get_status_dokter('EO', $data_actual['jumlah_pasien_rexulti']);
	// 					$customer_matrix_maintena 	= get_customer_matrix('MT', $status_dokter_maintena, $kriteria_potensi_maintena);
	// 					$customer_matrix_rexulti 	= get_customer_matrix('EO', $status_dokter_rexulti, $kriteria_potensi_rexulti);
	// 					// echo $this->db->last_query(); die;
	// 				} else {
	// 					$kriteria_potensi = get_kriteria_potensi($data_profiling['produk_grup'], $profiling['jumlah_pasien']);
	// 				}

	// 				$customer_matrix	= get_customer_matrix($data_profiling['produk_grup'], $data_actual['status_dokter'], $kriteria_potensi);
	// 				$status_dokter	= get_status_dokter($data_profiling['produk_grup'], $data_actual['jumlah_pasien']);
	// 				// echo $kriteria_potensi.' - '.$customer_matrix;
	// 				update_data('trxdact_'.$tahun.'_'.$bulan, [
	// 					'kriteria_potensi' 	=> $kriteria_potensi,
	// 					'kriteria_potensi_maintena' => $kriteria_potensi_maintena,
	// 					'kriteria_potensi_rexulti' => $kriteria_potensi_rexulti,
	// 					'customer_matrix'	=> $customer_matrix,
	// 					'customer_matrix_maintena'	=> $customer_matrix_maintena,
	// 					'customer_matrix_rexulti'	=> $customer_matrix_rexulti,
	// 					'status_dokter'	=> $status_dokter,
	// 					'status_dokter_maintena' => $status_dokter_maintena,
	// 					'status_dokter_rexulti' => $status_dokter_rexulti
	// 				], 'id', $data_actual['id']);
	// 			}
	// 		}
	// 	// }

	// 	render([
	// 		'status' => 'success',
	// 		'message'=> 'Perubahan tersimpan'
	// 	],'json');
	// }

	function update($bulan, $tahun) {
		$data = post();
		if($this->input->post('ap_original') == 'on'){
			$data['ap_original'] = 1;
		} else {
			$data['ap_original'] = 2;
		}
		
		$dokter_exs = '';
		$produk_exs = '';

		// $cycle = active_cycle();
		// $tahun = date('Y');

		if(in_array($bulan, ['01','02','03','04'])){
			$cycle = 1;
		} else if(in_array($bulan, ['05','06','07','08'])){
			$cycle = 2;
		} else {
			$cycle = 3;
		}

		$table = "trxprof_".$tahun."_".$cycle;
		$table_indikasi = "trxprof_indikasi_".$tahun."_".$cycle;

		if(!table_exists($table)){
			$this->load->helper('generate_trx_table');
			init_table_prof(active_cycle(), date('Y'));
		}

		if(!table_exists('trxprof_indikasi_'.date('Y').'_'.active_cycle())){
			$this->load->helper('generate_trx_table');
			init_table_prof_indikasi(active_cycle(), date('Y'));
		}

		$tmp_dp = get_data($table, [
			'where' => [
				'mr' 				=> user('username'),
				'produk_grup !=' 	=> post('produk_grup'),
				'dokter' 			=> post('dokter')
			]
		])->result_array();

		if(count($tmp_dp) > 0){
			$dokter_exs = $tmp_dp[0]['nama_dokter'];
		}

		if(!empty($dokter_exs)){
			$produk_exs = $tmp_dp[0]['nama_produk_grup'];
			$response = [
				'status' 	=> 'warning',
				'message' 	=> 'Oops! Anda tidak dapat menambahkan dokter '.$dokter_exs.' lagi, Karena dokter ini sudah tersimpan di Produk '.$produk_exs
			];
			render($response, 'json'); die;
		}

		$data['mr'] 		= $this->session->userdata('username');
		$data['am'] 		= $this->session->userdata('N_AM');
		$data['rm'] 		= $this->session->userdata('N_RM');
		$data['nsm'] 		= $this->session->userdata('N_NSM');
		$data['asdir'] 		= $this->session->userdata('N_ASDIR');
		$data['bud'] 		= $this->session->userdata('N_BUD');
		if(post('dokter')){
			$data['dokter']				= post('edokter') ? post('edokter') : post('dokter');
			$res['dokter'] 				= get_data('dokter', 'id', (post('edokter') ? post('edokter') : post('dokter')))->row_array();
			$data['nama_dokter'] 		= $res['dokter']['nama'];
			$res['spesialist'] 			= get_data('spesialist', 'id', $res['dokter']['spesialist'])->row_array();
			$res['sub_spesialist'] 		= get_data('sub_spesialist', 'id', $res['dokter']['subspesialist'])->row_array();
			$data['nama_spesialist'] 	= $res['spesialist']['nama'];
			$res['outlet'] 				= get_data('outlet', 'id', post('outlet'))->row_array();
			$data['nama_outlet'] 		= $res['outlet']['nama'];
			$res['produk_grup'] 		= get_data('produk_grup', 'kode', post('produk_grup'))->row_array();
			$data['nama_produk_grup'] 	= $res['produk_grup']['nama'];
			$res['branch'] 				= get_data('branch','id',post('branch'))->row_array();
			$data['nama_branch'] 		= $res['branch']['nama'];
			$data['spesialist'] 		= $res['dokter']['spesialist'];
			$data['jumlah_pasien']	 	= post('ejumlah_pasien') ? post('ejumlah_pasien') : post('jumlah_pasien');
		}

		$data['jumlah_pasien'] = post('ejumlah_pasien') ? post('ejumlah_pasien') : post('jumlah_pasien');
		$marketing_bulan_1 = post('marketing_bulan_1') ? post('marketing_bulan_1') : [];
		$marketing_bulan_2 = post('marketing_bulan_2') ? post('marketing_bulan_2') : [];
		$marketing_bulan_3 = post('marketing_bulan_3') ? post('marketing_bulan_3') : [];
		$marketing_bulan_4 = post('marketing_bulan_4') ? post('marketing_bulan_4') : [];
		$data['marketing_bulan_1'] = implode(',', $marketing_bulan_1);
		$data['marketing_bulan_2'] = implode(',', $marketing_bulan_2);
		$data['marketing_bulan_3'] = implode(',', $marketing_bulan_3);
		$data['marketing_bulan_4'] = implode(',', $marketing_bulan_4);
		//sementara
		// $response = save_data('draft_profiling', $data);
		// $data['status'] = 0;
		$data['apprv_at'] = date('Y-m-d H:i:s');
		$eind = [];
		if(!empty($data['id'])){
			// $data['dokter'] = post('edokter');
			unset($data['edokter']);
			// $data['jumlah_pasien'] = post('ejumlah_pasien');
			unset($data['ejumlah_pasien']);
			unset($data['indikasi_1']);
			unset($data['indikasi_2']);
			unset($data['indikasi_3']);
			unset($data['indikasi_4']);
			unset($data['indikasi_5']);
			unset($data['indikasi_6']);
			unset($data['indikasi_7']);
			unset($data['indikasi_8']);
			unset($data['indikasi_9']);
			unset($data['indikasi_10']);
			$eind['val_indikasi_1']  = isset($data['val_indikasi_1']) ? $data['val_indikasi_1'] : 0;
			$eind['val_indikasi_2']  = isset($data['val_indikasi_2']) ? $data['val_indikasi_2'] : 0;
			$eind['val_indikasi_3']  = isset($data['val_indikasi_3']) ? $data['val_indikasi_3'] : 0;
			$eind['val_indikasi_4']  = isset($data['val_indikasi_4']) ? $data['val_indikasi_4'] : 0;
			$eind['val_indikasi_5']  = isset($data['val_indikasi_5']) ? $data['val_indikasi_5'] : 0;
			$eind['val_indikasi_6']  = isset($data['val_indikasi_6']) ? $data['val_indikasi_6'] : 0;
			$eind['val_indikasi_7']  = isset($data['val_indikasi_7']) ? $data['val_indikasi_7'] : 0;
			$eind['val_indikasi_8']  = isset($data['val_indikasi_8']) ? $data['val_indikasi_8'] : 0;
			$eind['val_indikasi_9']  = isset($data['val_indikasi_9']) ? $data['val_indikasi_9'] : 0;
			$eind['val_indikasi_10'] = isset($data['val_indikasi_10']) ? $data['val_indikasi_10'] : 0;
			unset($data['val_indikasi_1']);
			unset($data['val_indikasi_2']);
			unset($data['val_indikasi_3']);
			unset($data['val_indikasi_4']);
			unset($data['val_indikasi_5']);
			unset($data['val_indikasi_6']);
			unset($data['val_indikasi_7']);
			unset($data['val_indikasi_8']);
			unset($data['val_indikasi_9']);
			unset($data['val_indikasi_10']);
			unset($data['fee_patient']);
			unset($data['ap_original']);
			update_data($table, $data, 'id', $data['id']);
			$response = [
				'status' => 'success',
				'message' => 'Data berhasil diperbarui',
			];
		} else {
			$response = save_data($table, $data);
			$id_profiling = $response['id'];
		}
		// debug($response); die;
		$pgroup = post('produk_grup');
		$jumlah_pasien = post('jumlah_pasien');
		if(!empty($data['id'])){
			$profiling = get_data($table, 'id', $data['id'])->row_array();
			$pgroup = $profiling['produk_grup'];
			$jumlah_pasien = post('ejumlah_pasien');
		}
		
		$indikasi = get_data('indikasi', [
			'where' => [
				'produk_grup' => $pgroup,
				'is_active' => 1
			]
		])->result_array();
		if($this->input->post('ap_original') == 'on'){
			$data['ap_original'] = 1;
		} else {
			$data['ap_original'] = 2;
		}
		$tmp_insert_indikasi = [
			'profiling' => isset($data['id']) ? $data['id'] : $id_profiling,
			'jumlah_pasien' => $jumlah_pasien,
			'fee_patient' => post('fee_patient'),
			'ap_original' => isset($data['ap_original']) ? $data['ap_original'] : 0,
			'bulan' => $bulan
		];

		$potensi_tablet = 0;
		$potensi_rexulti = 0;
		$potensi_maintena = 0;
		foreach($indikasi as $k => $v){
			$tmp_insert_indikasi['indikasi_'.($k+1)] = $v['id'];
			$tmp_insert_indikasi['val_indikasi_'.($k+1)] = isset($data['val_indikasi_'.($k+1)]) ? $data['val_indikasi_'.($k+1)] : (isset($eind['val_indikasi_'.($k+1)]) ? $eind['val_indikasi_'.($k+1)] : 0);
			$potensi_tablet += $tmp_insert_indikasi['val_indikasi_'.($k+1)] ? $tmp_insert_indikasi['val_indikasi_'.($k+1)] : 0;
			if($v['id'] == 1){
				$potensi_rexulti += $tmp_insert_indikasi['val_indikasi_'.($k+1)];
				$potensi_maintena += $tmp_insert_indikasi['val_indikasi_'.($k+1)];
			} else if($v['id'] == 2){
				$potensi_maintena += $tmp_insert_indikasi['val_indikasi_'.($k+1)];
			}
		}

		if(!empty($data['id'])){
			delete_data($table_indikasi, 'profiling', $data['id']);
		}
		$tmp_insert_indikasi['potensi_tablet'] = $potensi_tablet;
		insert_data($table_indikasi, $tmp_insert_indikasi);

		$data_actual = get_data('trxdact_'.$tahun.'_'.date('m'), [
			'where' => [
				'dokter' 		=> $profiling['dokter'],
				'mr'			=> $profiling['mr'],
				'produk_grup' 	=> $profiling['produk_grup'],
			]
		])->row_array();
		if(!empty($data_actual)){
			$kriteria_potensi_maintena	= '';
			$kriteria_potensi_rexulti	= '';
			$status_dokter_maintena 	= '';
			$status_dokter_rexulti 		= '';
			$customer_matrix_maintena 	= '';
			$customer_matrix_rexulti 	= '';

			if ($profiling['produk_grup'] == 'EH') {
				$kriteria_potensi 			= get_kriteria_potensi($profiling['produk_grup'], $potensi_tablet, 'B', post('fee_patient'), $data['ap_original']);
				$kriteria_potensi_maintena	= get_kriteria_potensi('MT', $potensi_maintena, 'B', post('fee_patient'), $data['ap_original']);
				$kriteria_potensi_rexulti	= get_kriteria_potensi('EO', $potensi_rexulti, 'B', post('fee_patient'), $data['ap_original']);
				$status_dokter_maintena 	= get_status_dokter('MT', $data_actual['jumlah_pasien_maintena']);
				$status_dokter_rexulti 		= get_status_dokter('EO', $data_actual['jumlah_pasien_rexulti']);
				$customer_matrix_maintena 	= get_customer_matrix('MT', $status_dokter_maintena, $kriteria_potensi_maintena);
				$customer_matrix_rexulti 	= get_customer_matrix('EO', $status_dokter_rexulti, $kriteria_potensi_rexulti);
				// echo $this->db->last_query(); die;
			} else {
				$kriteria_potensi = get_kriteria_potensi($profiling['produk_grup'], $profiling['jumlah_pasien']);
			}

			$customer_matrix	= get_customer_matrix($profiling['produk_grup'], $data_actual['status_dokter'], $kriteria_potensi);
			$status_dokter		= get_status_dokter($profiling['produk_grup'], $data_actual['jumlah_pasien']);
			// echo $kriteria_potensi.' - '.$customer_matrix;
			update_data('trxdact_' . $tahun . '_' . date('m'), [
				'kriteria_potensi' 			=> $kriteria_potensi,
				'kriteria_potensi_maintena' => $kriteria_potensi_maintena,
				'kriteria_potensi_rexulti' 	=> $kriteria_potensi_rexulti,
				'customer_matrix'			=> $customer_matrix,
				'customer_matrix_maintena'	=> $customer_matrix_maintena,
				'customer_matrix_rexulti'	=> $customer_matrix_rexulti,
				'status_dokter'				=> $status_dokter,
				'status_dokter_maintena' 	=> $status_dokter_maintena,
				'status_dokter_rexulti' 	=> $status_dokter_rexulti
			], 'id', $data_actual['id']);
		}
		// debug($eind); die;
		render($response,'json');
	}

	function get_marketing(){
		$pgroup = get('pgroup');
		$marketing = get_data('marketing_aktifitas',[
			'where' => [
				'produk_grup' => $pgroup,
				'is_active' => 1
			]
		])->result_array();
		render($marketing, 'json');
	}

}