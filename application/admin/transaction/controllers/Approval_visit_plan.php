<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Approval_visit_plan extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [
			'select' => '(
				case 
					when bulan = 1 then "Januari"
					when bulan = 2 then "Februari"
					when bulan = 3 then "Maret"
					when bulan = 4 then "April"
					when bulan = 5 then "Mei"
					when bulan = 6 then "Juni"
					when bulan = 7 then "Juli"
					when bulan = 8 then "Agustus"
					when bulan = 9 then "September"
					when bulan = 10 then "Oktober"
					when bulan = 11 then "November"
					when bulan = 12 then "Desember"
				end
			) as nama_bulan',
			'where' => [
				'mr' => get('mr'),
				'produk_grup' => get('pgroup'),
				// 'appvr_at' => null,
				'status != ' => 1
			],
			'access_view' => false,
			'access_edit' => false,
			'access_delete' => false,
			'button' => [
				button_serverside('btn-sky','btn-detail',['fa-search','Detail',true], 'act-detail')
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function approval(){

		$alasan_not_approve = post('alasan_not_approve');
		// $this->db->trans_begin();
		if(!empty($alasan_not_approve)){
			$resp = update_data('trxvisit_'.date('Y').'_'.date('m'), [
				'status' => '4',
				'alasan_not_approve' => $alasan_not_approve,
				'appvr_at' => NULL
			], 'id', post('id'));
		} else {
			$resp = update_data('trxvisit_'.date('Y').'_'.date('m'), [
				'status' => '3',
			],'id',post('id'));
		}
		// if($this->db->trans_status()===TRUE){
		// 	$this->db->trans_commit();
		// } else {
		// 	$this->db->trans_rollback();
		// }

		render(['status' => $resp,'year'=>date('Y'),'month'=>date('m'),'id'=>post('id')],'json');

	}

	function submit(){
		$pgrup = post('pgroup');
		$mr = post('mr');

		$res['visit_plan'] = get_data('trxvisit_'.date('Y').'_'.date('m'), [
			'where' => [
				'mr' => $mr,
				'produk_grup' => $pgrup,
			],
			'where_in' => [
				'status' => [2,4]
			]
		])->result_array();
		$this->db->trans_status();
		
		foreach($res['visit_plan'] as $val){

			// $draft = $val;
			// unset($draft['id']);
			
			$val['appvr_at'] = date('Y-m-d H:i:s');
			$val['status'] = $val['status'] == 2 ? 3 : $val['status'];

			update_data('trxvisit_'.date('Y').'_'.date('m'), $val, 'id', $val['id']);
		}
		if($this->db->trans_status() === TRUE){
			$this->db->trans_commit();
		} else {
			$this->db->trans_rollback();
		}

		render(['status' => $this->db->trans_status()], 'json');
	}

	function get_data() {
		$visit_plan = get_data('trxvisit_'.date('Y').'_'.date('m'), [
			'where' => [
				'id' => get('id')
			],
		])->row_array();
		render($visit_plan,'json');
	}

	function get_marketing_program(){
		$pgroup = get('pgroup');
		$marketing_program = get_data('marketing_program', [
			'where' => [
				'produk_grup' => $pgroup,
				'is_active' =>  1
			]
		])->result_array();
		render($marketing_program,'json');
	}

	function get_marketing_aktifitas(){
		$pgroup = get('pgroup');
		$marketing_aktifitas = get_data('marketing_aktifitas', [
			'where' => [
				'produk_grup' => $pgroup,
				'is_active' => 1
			]
		])->result_array();
		render($marketing_aktifitas,'json');
	}

	function get_outlet(){
		$dokter = get('dokter');
		$outlet = get_data('trxprof_'.date('Y').'_'.active_cycle(), [
			'select' => 'outlet as id, nama_outlet as nama',
			'where' => [
				'dokter' => $dokter,
				'produk_grup' => get('pgroup'),
				'status' => 2,
				'apprv_at !=' => null,
				'mr' => user('username')
			],
			'group_by' => 'outlet'
		])->result_array();
		render($outlet,'json');
	}

}
