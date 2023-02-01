<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History_dfr extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data(){

		$data['dfr'] = [];
		
		$tahun = post('ftahun');
		$bulan = post('fbulan');	

		if($this->db->table_exists('trxdfr_'.$tahun.'_'.$bulan)){
			if(!$this->db->table_exists('trxdfr_feedback_'.$tahun.'_'.$bulan)){
				$this->load->helper('generate_trx_table');
				init_table_dfr_feedback($tahun, $bulan);
			}
			$data['dfr'] = get_data('trxdfr_'.$tahun.'_'.$bulan, [
				'select' => 'trxdfr_'.$tahun.'_'.$bulan.'.*, trxdfr_feedback_'.$tahun.'_'.$bulan.'.penilaian',
				'where' => [
					'produk_grup' => post('fpgroup'),
					'mr' => post('fmr'),
					'status' => 2,
				],
				'join' => [
					'trxdfr_feedback_'.$tahun.'_'.$bulan.' on trxdfr_feedback_'.$tahun.'_'.$bulan.'.dfr = trxdfr_'.$tahun.'_'.$bulan.'.id type left'
				],
				'sort_by' => 'trxdfr_'.$tahun.'_'.$bulan.'.cat',
				'sort' => 'ASC',
				'group_by' => 'trxdfr_'.$tahun.'_'.$bulan.'.id'
			])->result_array();
			// debug($data['dfr']); die;
		}

		render($data,'layout:false');
	}
	

	function get_all($cycle, $tahun, $mr){
		$profiling = get_data('trxprof_'.$tahun.'_'.$cycle,[
			'where' => [
				'id' => get('id')
			]
		])->row_array();
		if(count($profiling)<=0){
			return 0;
		} else {
			$data['profiling'] = get_data('trxprof_'.$tahun.'_'.$cycle, [
				'where' => [
					'dokter' => $profiling['dokter'],
					'apprv_at !=' => null,
					'mr' => $mr
				],
				'where_in' => [
					'status' => [2,3]
				],
				'sort_by' => 'nama_outlet',
				'sort' => 'ASC'
			])->result_array();
			render($data['profiling'], 'json');
		}
	}

	function get_dokter_detail($dokter, $bulan, $tahun, $mr){

		$cycle = 3;
		if(in_array($bulan, [1,2,3,4])){
			$cycle = 1;
		} else if(in_array($bulan, [5,6,7,8])){
			$cycle = 2;
		}
		
		$table_visit_plan = 'trxvisit_'.$tahun.'_'.$bulan;
		$table_profiling = 'trxprof_'.$tahun.'_'.$cycle;
		$table_data_actual = 'trxdact_'.$tahun.'_'.$bulan;

		if($this->db->table_exists($table_data_actual) == false){
			$this->load->helper('generate_trx_table');
			init_table_data_actual($tahun, $bulan);
		}

		$dokter = get_data($table_visit_plan.' a', [
			'select' => 'a.*, b.nama_spesialist, b.channel_outlet, c.customer_matrix,  c.customer_matrix_rexulti,  c.customer_matrix_maintena',
			'join' => [
				$table_profiling.' b on b.id = a.profiling',
				$table_data_actual.' c on c.dokter = a.dokter type left'
			],
			'where' => [
				'a.status' => 3,
				'a.mr' => $mr,
				'a.dokter' => $dokter
			],
		])->row_array();

		render($dokter, 'json');
	}
	
	function get_data($bulan, $tahun) {	
		$data = get_data('trxdfr_'.$tahun.'_'.$bulan,[
			'join' => [
				'trxdfr_feedback_'.$tahun.'_'.$bulan.' on trxdfr_feedback_'.$tahun.'_'.$bulan.'.dfr = trxdfr_'.$tahun.'_'.$bulan.'.id and trxdfr_feedback_'.$tahun.'_'.$bulan.'.user = '.user('id').' type left'
			],
			'where' => [
				'trxdfr_'.$tahun.'_'.$bulan.'.id' => get('id'),
			]
		])->row_array();
		render($data,'json');
	}

	function get_feedback($tahun, $bulan){
		$data = get_data('trxdfr_feedback_'.$tahun.'_'.$bulan, [
			'select' => 'trxdfr_feedback_'.$tahun.'_'.$bulan.'.*, tbl_user.nama as nama_user, tbl_user_group.nama as nama_grup',
			'where' => [
				'dfr' => get('id')
			],
			'join' => [
				'tbl_user on tbl_user.id = trxdfr_feedback_'.$tahun.'_'.$bulan.'.user',
				'tbl_user_group on tbl_user_group.id = trxdfr_feedback_'.$tahun.'_'.$bulan.'.id_group',
			]
		])->result_array();
		render($data, 'json');
	}

	function init_call_data(){
		$id = get('id');
		$data = get_data('sub_call_type','call_type',$id)->result_array();
		render($data, 'json');
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
			$where['produk_grup'] = get('pgrup');
		}
		$data = get_data('indikasi', [
			'where' => $where
		])->result_array();
		if(!empty($where)){
			render($data, 'json');
		}
	}

	function approval($cycle, $tahun){

		$alasan_not_approve = post('alasan_not_approve');
		$this->db->trans_begin();
		$profiling = get_data('trxprof_'.$tahun.'_'.$cycle, 'id', post('id'))->row_array();
		if(!empty($alasan_not_approve)){
			update_data('trxprof_'.$tahun.'_'.$cycle, [
				'status' => '3',
				'alasan_not_approve' => $alasan_not_approve
			], [
				'dokter' => $profiling['dokter'],
				'mr' => $profiling['mr']
			]);
		} else {
			update_data('trxprof_'.$tahun.'_'.$cycle, [
				'status' => '2',
			], [
				'dokter' => $profiling['dokter'],
				'mr' => $profiling['mr']
			]);
		}
		if($this->db->trans_status()===TRUE){
			$this->db->trans_commit();
		} else {
			$this->db->trans_rollback();
		}

		render(['status' => $this->db->trans_status()],'json');

	}

	function init_data($bulan, $tahun, $mr){
		$id_produk_grup = post('produk_grup');
		$visit_plan = get_data('trxvisit_'.$tahun.'_'.$bulan, [
			'select' => 'dokter as id, nama_dokter as nama',
			'where' => [
				'produk_grup' => $id_produk_grup,
				'mr' => $mr,
				'status' => 3,
				'appvr_at != '=> NULL,
			]
		])->result_array();

		$data = [
			'dokter' => $visit_plan,
			'indikasi' => get_data('indikasi', [
				'where' => [
					'produk_grup' => $id_produk_grup,
					'is_active' => 1
				]
			])->result_array(),
			'kompetitor_diresepkan' => get_data('kompetitor_diresepkan', [
				'where' => [
					'produk_grup' => $id_produk_grup,
					'is_active' => 1
				]
			])->result_array(),
			'key_message' => get_data('key_message', [
				'where' => [
					'produk_grup' => $id_produk_grup,
					'is_active' => 1
				]
			])->result_array(),
			'produk' => get_data('produk', [
				'select' => 'produk.*',
				'join' => [
					'produk_subgrup on produk_subgrup.kode = produk.kode_subgrup',
					'produk_grup on produk_grup.kode = produk_subgrup.kode_grup'
				],
				'where' => [
					'produk_grup.kode' => $id_produk_grup,
					'produk.is_active' => 1
				]
			])->result_array()
		];
		render($data,'json');
	}

	function update(){
		$data = post();
		if(!$this->db->table_exists('trxdfr_feedback_'.$data['tahun'].'_'.$data['bulan'])){
			$this->load->helper('generate_trx_table');
			init_table_dfr_feedback($data['tahun'], $data['bulan']);
		}

		$feedback = get_data('trxdfr_feedback_'.$data['tahun'].'_'.$data['bulan'], [
			'where' => [
				'user' 	=> user('id'),
				'dfr'	=> $data['id']
			]
		])->num_rows();
		if($feedback > 0){
			$this->db->delete('trxdfr_feedback_'.$data['tahun'].'_'.$data['bulan'], [
				'user' 	=> user('id'),
				'dfr'	=> $data['id']
			]);
		}
		insert_data('trxdfr_feedback_'.$data['tahun'].'_'.$data['bulan'], [
			'user' 					=> user('id'),
			'dfr' 					=> $data['id'],
			'id_group'				=> user('id_group'),
			'penilaian'				=> $data['penilaian'],
			'alasan_belum_sesuai'	=> $data['alasan_belum_sesuai']
		]);
		$dfr = get_data('trxdfr_'.$data['tahun'].'_'.$data['bulan'], 'id', $data['id'])->row_array();
		if($dfr['call_type'] != $data['call_type']){
			if($data['call_type'] == 1){
				$sub_call_type = 1;
			} elseif($data['call_type'] == 2) {
				$sub_call_type = 5;
			} elseif($data['call_type'] == 3) {
				$sub_call_type = 11;
			}
			update_data('trxdfr_'.$data['tahun'].'_'.$data['bulan'], [
				'call_type' => $data['call_type'],
				'sub_call_type' => $sub_call_type
			], 'id', $data['id']);
		}

		$response = [
			'status' => 'success',
			'message' => 'Feedback Anda telah tersimpan'
		];

		render($response, 'json');
	}

}