<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Approval_visit_plan extends BE_Controller {

	function __construct() {
		parent::__construct();
		if(!table_exists('trxvisit_'.date('Y').'_'.date('m'))){
			init_table_visit_plan(date('Y'), date('m'));
		}
	}

	function index() {
		render();
	}

	function data() {
		$config = [
			'select' => 'd.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet, (week1 + week2 + week3 + week4 + week5 + week6) as total_plan',
			'join' => [
				'trxprof_'.date('Y').'_'.active_cycle().' b on b.id = trxvisit_'.date('Y').'_'.date('m').'.profiling',
				'dokter d on d.id = b.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = b.outlet',
			],
			'where' => [
				'b.mr' => get('mr'),
				'b.produk_grup' => get('pgroup'),
			],
			'access_view' 	=> false,
			'access_edit' 	=> false,
			'access_delete' => false,
			'button' => [
				button_serverside('btn-sky','btn-input',['fa-search','Detail',true], 'act-detail')
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function approval(){

		$note = post('alasan_not_approve');
		if(!empty($note)){
			$resp = update_data('trxvisit_'.date('Y').'_'.date('m'), [
				'status' => 'REVISION',
				'note' => $note,
			], 'id', post('id'));
		} else {
			$resp = update_data('trxvisit_'.date('Y').'_'.date('m'), [
				'status' => 'APPROVED',
			],'id',post('id'));
		}
		render(['status' => $resp,'year'=>date('Y'),'month'=>date('m'),'id'=>post('id')],'json');

	}

	function submit(){
		$pgrup 	= post('pgroup');
		$mr 	= post('mr');

		$res['visit_plan'] = get_data('trxvisit_'.date('Y').'_'.date('m').' a', [
			'select' => 'a.*',
			'join' => [
				'trxprof_'.date('Y').'_'.active_cycle().' b on a.profiling = b.id'
			],
			'where' => [
				'b.mr' => $mr,
				'b.produk_grup' => $pgrup,
				'a.status' => 'WAITING'
			],
		])->result_array();

		foreach($res['visit_plan'] as $val){
			$updated = update_data('trxvisit_'.date('Y').'_'.date('m'), [
				'status' => 'APPROVED'
			], 'id', $val['id']);
		}

		render(['status' => $this->db->trans_status()], 'json');
	}

	function get_data() {
		$visit_plan = get_data('trxvisit_'.date('Y').'_'.date('m').' a', [
			'select' => 'a.*, c.nama as nama_dokter, d.nama as nama_spesialist, e.nama as nama_outlet, f.nama as nama_produk_grup',
			'join' => [
				'trxprof_'.date('Y').'_'.active_cycle().' b on a.profiling = b.id',
				'dokter c on c.id = b.dokter',
				'spesialist d on d.id = c.spesialist',
				'outlet e on e.id = b.outlet',
				'produk_grup f on f.kode = b.produk_grup'
			],
			'where' => [
				'a.id' => post('id')
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
