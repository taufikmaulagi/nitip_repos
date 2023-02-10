<?php

use function PHPSTORM_META\map;

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
		$produk_group = post('fpgroup');
		$mr = post('fmr');
		$cycle = cycle_by_month($bulan);

		$data = get_data('trxdfr_'.$tahun.'_'.$bulan.' a', [
			'select' => 'a.*, d.nama as nama_dokter, s.nama as nama_spesialist, e.penilaian',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on a.visit_plan = b.id',
				'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
				'dokter d on d.id = c.dokter',
				'spesialist s on s.id = d.spesialist',
				'trxdfr_feedback_'.$tahun.'_'.$bulan.' e on e.dfr = a.id type left'
			],
			'where' => [
				'c.produk_grup' => $produk_group,
				'c.mr' => $mr,
				'a.status' => 'SENT'
			],
			'order_by_array' => [
				'a.tanggal' => 'desc',
				'd.nama' => 'asc',
				'e.cat' => 'desc'
			]
 		])->result_array();

		render([
			'data' => $data
		],'layout:false');
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
			'select' => 'a.*, s.nama as nama_spesialist, b.channel_outlet, c.customer_matrix',
			'join' => [
				$table_profiling.' b on b.id = a.profiling',
				$table_data_actual.' c on c.visit_plan = a.id type left',
				'dokter d on d.id = b.dokter',
				'spesialist s on s.id = d.spesialist'
			],
			'where' => [
				'a.status' => 'APPROVED',
				'b.mr' => $mr,
				'b.dokter' => $dokter
			],
		])->row_array();

		render($dokter, 'json');
	}
	
	function get_data($bulan, $tahun) {	
		$id = get('id');
		$cycle = cycle_by_month($bulan);

		$data = get_data('trxdfr_'.$tahun.'_'.$bulan.' a',[
			'select' => 'a.*, c.dokter, c.produk_grup, d.nama as nama_dokter, s.nama as nama_spesialist, p.nama as nama_produk_grup, o.nama as nama_outlet, p.nama as nama_produk_grup',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on a.visit_plan = b.id',
				'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
				'dokter d on d.id = c.dokter',
				'spesialist s on s.id = d.spesialist',
				'produk_grup p on p.kode = c.produk_grup',
				'outlet o on o.id = c.outlet',
				'trxdfr_feedback_'.$tahun.'_'.$bulan.' e on e.dfr = a.id and e.id_group = '.user('id_group').' and e.user = '.user('id').' type left',
			],
			'where' => [
				'a.id' => $id
			],
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
		if($id == 'A'){
			$id = 1;
		} else if($id == 'B'){
			$id = 2;
		} else {
			$id = 3;
		}
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
		$cycle = cycle_by_month($bulan);

		$visit_plan = get_data('trxvisit_'.$tahun.'_'.$bulan.' a', [
			'select' => 'b.dokter as id, d.nama as nama',
			'join' => [
				'trxprof_'.$tahun.'_'.$cycle.' b on a.profiling = b.id',
				'dokter d on d.id = b.dokter'
			],
			'where' => [
				'b.produk_grup' => $id_produk_grup,
				'b.mr' => $mr,
				'a.status' => 'APPROVED',
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
			if($data['call_type'] == 'A'){
				$sub_call_type = 1;
			} elseif($data['call_type'] == 'B') {
				$sub_call_type = 5;
			} elseif($data['call_type'] == 'C') {
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

	function export(){
		$tahun = get('tahun');
		$bulan = get('bulan');
		$mr = get('mr');
		$produk_group = get('produk_group');
		$cycle = cycle_by_month($bulan);
		$data_mr = get_data('tbl_user', 'username', $mr)->row_array();

		$data_dfr = get_data('trxdfr_'.$tahun.'_'.$bulan.' a', [
			'select' => 'd.nama as nama_dokter, s.nama as nama_spesialist, pr.nama as nama_produk, o.nama as nama_outlet, channel_outlet, kd.nama as nama_kompetitor, circumstances, call_object, i.nama as nama_indikasi, km.nama as nama_key_message, mr_talk, mr_talk2, mr_talk3, feedback_status, feedback_dokter, feedback_dokter2, feedback_dokter3, next_action, p2.nama as nama_produk2, p3.nama as nama_produk3, a.call_type, sc.nama as nama_sub_call_type',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on a.visit_plan = b.id',
				'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
				'dokter d on d.id = c.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = c.outlet',
				'kompetitor_diresepkan kd on kd.id = a.kompetitor_diresepkan',
				'indikasi i on i.id = a.indikasi',
				'key_message km on km.id = a.key_message',
				'produk_grup p on p.kode = c.produk_grup',
				'produk_grup p2 on p2.kode = a.produk2 type left',
				'produk_grup p3 on p3.kode = a.produk3 type left',
				'sub_call_type sc on sc.id = a.sub_call_type type left',
				'produk pr on pr.id = a.produk'
			],
			'where' => [
				'c.mr' => $mr,
				'c.produk_grup' => $produk_group
			]
		])->result_array();

		$data = [];
		foreach($data_dfr as $k => $v){
			$v['no'] = $k + 1;
			$data[] = $v;
		}

		$header = [
			'no' => 'No.',
			'nama_dokter' => 'Dokter',
			'nama_spesialist' => 'Spesialist',
			'nama_outlet' => 'Outlet',
			'channel_outlet' => 'Channel Outlet',
			'nama_produk' => 'Produk',
			'call_type' => 'Call Type',
			'nama_sub_call_type' => 'Sub Call Type',
			'nama_kompetitor' => 'Kompetitor',
			'circumstances' => 'Circumstances',
			'call_object' => 'Call Objective',
			'nama_indikasi' => 'Indikasi',
			'nama_key_message' => 'Key Message',
			'mr_talk' => 'MR Talk',
			'mr_talk2' => 'MR Talk 2',
			'mr_talk3' => 'MR Talk 3',
			'feedback_status' => 'Feedback Status',
			'feedback_dokter' => 'Feedback Dokter',
			'feedback_dokter2' => 'Feedback Dokter 2',
			'feedback_dokter3' => 'Feedback Dokter 3',
			'next_action' => 'Next Action Plan',
			'nama_produk2' => 'Produk 2',
			'nama_produk3' => 'Produk 3',
		];

		$config = [
			'title' => 'DFR '.explode(' ',$data_mr['nama'])[0].' '.$tahun.'-'.$bulan,
			'data' => $data,
			'header' => $header,
		];
		$this->load->library('simpleexcel', $config);
		$this->simpleexcel->export();

	}

}