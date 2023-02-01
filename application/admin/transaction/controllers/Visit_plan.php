<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Visit_plan extends BE_Controller {

	function __construct() {
		parent::__construct();
		if(!table_exists('trxvisit_'.date('Y').'_'.date('m'))){
			init_table_visit_plan(date('Y'), date('m'));
		}
	}

	function index() {
		$this->__generate_visit_plan();
		render();
	}

	private function __generate_visit_plan(){
		$profiling = get_data('trxprof_'.date('Y').'_'.active_cycle(), [
			'where' => [
				'mr' => user('username'),
				'status' => 'APPROVED'
			]
		])->result_array();

		foreach($profiling as $v){
			$check = get_data('trxvisit_'.date('Y').'_'.date('m'), [
				'where' => [
					'profiling' => $v['id']
				]
			])->row_array();
			if(!$check){
				insert_data('trxvisit_'.date('Y').'_'.date('m'), [
					'profiling' => $v['id']
				]);
			}
		}
	}

	function data() {
		$table = 'trxvisit_'.date('Y').'_'.date('m');
		$conf = [
			'select' => '(IFNULL(week1,0) + IFNULL(week2,0) + IFNULL(week3,0) + IFNULL(week4,0) + IFNULL(week5,0) + IFNULL(week6,0)) as total_plan, c.nama as nama_dokter, d.nama as nama_spesialist, e.nama as nama_outlet',
			'join' => [
				'trxprof_'.date('Y').'_'.active_cycle().' b on '.$table.'.profiling = b.id',
				'dokter c on c.id = b.dokter',
				'spesialist d on d.id = c.spesialist',
				'outlet e on e.id = b.outlet'
			],
			'access_view' => false,
			'access_delete' => false,
		];
		$data = data_serverside($conf);
		render($data,'json');
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

	function save() {
		$response = save_data('trxvisit_'.date('Y').'_'.date('m'),post(),post(':validation'));
		render($response,'json');
	}

	function submit(){

		$visit_plan = get_data('trxvisit_'.date('Y').'_'.date('m').' a', [
			'select' => 'a.*',
			'join' => [
				'trxprof_'.date('Y').'_'.active_cycle().' b on a.profiling = b.id'
			],
			'where' => [
				'a.status' => [
					'UNSUBMITTED'
				],
				'b.mr' => user('username')
			]
		])->result_array();

		foreach($visit_plan as $k => $v){
			update_data('trxvisit_'.date('Y').'_'.date('m'), [
				'status' => 'WAITING',
			], 'id', $v['id']);
		}

		render([
			'status' => 'success',
			'message' => 'Visit plan telah disubmit dan akan di proses oleh AM'
		], 'json');
	}

}
