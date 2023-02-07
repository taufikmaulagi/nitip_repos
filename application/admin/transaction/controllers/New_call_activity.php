<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class New_call_activity extends BE_Controller {

	function __construct() {
		parent::__construct();
		if($this->db->table_exists('trxdfr_'.date('Y').'_'.date('m')) == false){
			$this->load->helper('gen_trx_table');
			init_table_dfr(date('Y'), date('m'));
		}

		if($this->db->table_exists('trxdact_'.date('Y').'_'.date('m')) == false){
			$this->load->helper('gen_trx_table');
			init_table_data_actual(date('Y'), date('m'));
		}
	}

	function index() {
		render();
	}

	function data(){
		$config = [
			'select' => 'd.nama as nama_dokter, e.nama as nama_outlet, c.channel_outlet',
			'where' => [
				'c.produk_grup' => get('pgroup'),
				'c.mr' => user('username'),
				'trxdfr_'.date('Y').'_'.date('m').'.status' => 'CREATE'
			],
			'join' => [
				'trxvisit_'.date('Y').'_'.date('m').' b on trxdfr_'.date('Y').'_'.date('m').'.visit_plan = b.id',
				'trxprof_'.date('Y').'_'.active_cycle().' c on b.profiling = c.id',
				'dokter d on d.id = c.dokter',
				'outlet e on e.id = c.outlet'
			],
 			'access_view' 	=> false,
			'access_edit' 	=> false,
			'access_delete' => false,
			'button' => [
				button_serverside('btn-sky','btn-send', ['fa-paper-plane','Send',true],'act_send',[
					// 'trxdfr_'.date('Y').'_'.date('m').'.status' => 'CREATE'
				]),
				button_serverside('btn-hot','btn-delete', ['fa-trash','Delete',true],'act_delete',[
					// 'trxdfr_'.date('Y').'_'.date('m').'.status' => 'CREATE'
				]),
			]
		];
		$data = data_serverside($config);
		render($data, 'json');
	}

	function get_data(){
		$tahun = date('Y');
		$bulan = date('m');
		$id = get('id');

		$data = get_data('trxdfr_'.$tahun.'_'.$bulan.' a', [
			'select' => 'a.*, b.id as visit_plan, c.produk_grup, c.dokter',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on a.visit_plan = b.id',
				'trxprof_'.$tahun.'_'.active_cycle().' c on c.id = b.profiling',
			],
			'where' => [
				'a.id' => $id
			]
		])->row_array();
		render($data, 'json');
	}

	function delete() {
		$response = destroy_data('trxdfr_'.date('Y').'_'.date('m'),'id',post('id'));
		render($response,'json');
	}
	
	function init_data(){
		$tahun = date('Y');
		$bulan = date('m');
		$id_produk_grup = post('produk_grup');
		$visit_plan = get_data('trxvisit_'.$tahun.'_'.$bulan.' a', [
			'select' => 'b.dokter as id, c.nama',
			'join' => [
				'trxprof_'.$tahun.'_'.active_cycle().' b on a.profiling = b.id',
				'dokter c on c.id = b.dokter'
			],
			'where' => [
				'b.produk_grup' => $id_produk_grup,
				'b.mr' => user('username'),
				'b.status' => 'APPROVED',
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

	function get_dokter_detail($dokter = ''){

		$tahun = date('Y');
		$bulan = date('m');

		$table_visit_plan = 'trxvisit_'.$tahun.'_'.$bulan;
		$table_profiling = 'trxprof_'.$tahun.'_'.active_cycle();
		$table_data_actual = 'trxdact_'.$tahun.'_'.$bulan;

		$dokter = get_data($table_visit_plan.' a', [
			'select' => 'a.*, e.nama as nama_spesialist, b.channel_outlet, c.customer_matrix',
			'join' => [
				$table_profiling.' b on b.id = a.profiling',
				$table_data_actual.' c on a.id = c.visit_plan type left',
				'dokter d on d.id = b.dokter',
				'spesialist e on e.id = d.spesialist'
			],
			'where' => [
				'b.mr' => user('username'),
				'b.dokter' => $dokter
			],
		])->row_array();

		render($dokter, 'json');
	}

	function detail_data($dokter, $bulan, $tahun){
		
		if(in_array($bulan, ['01','02','03','04'])){
			$cycle = 1;
		} else if(in_array($bulan, ['05','06','07','08'])){
			$cycle = 2;
		} else {
			$cycle = 3;
		}

		$table_visit_plan = 'trxvisit_'.$tahun.'_'.$bulan;
		$table_profiling = 'trxprof_'.$tahun.'_'.$cycle;
		$table_data_actual = 'trxdact_'.$tahun.'_'.$bulan;

		$data = get_data($table_visit_plan.' a', [
			'select' => 'a.*, b.nama_spesialist, b.channel_outlet, c.customer_matrix, c.kriteria_potensi, c.status_dokter',
			'join' => [
				$table_profiling.' b on b.id = a.profiling',
				$table_data_actual.' c on c.dokter = a.dokter type left'
			],
			'where' => [
				'a.status' => 3,
				'a.mr' => user('username'),
				'a.dokter' => $dokter
			],
		])->row_array();
		return $data;
	}

	function save(){

		$limit = false;
		$data = post();

		if(!empty($data['id'])){
			$data['status'] = 2;
			$dfr = get_data('trxdfr_'.date('Y').'_'.date('m'), [
				'where' => [
					'status' => 'SENT',
					'date(cat)' => date('Y-m-d'),
				]
			])->num_rows();
			$dfr_a = get_data('trxdfr_'.date('Y').'_'.date('m'), [
				'where' => [
					'status' => "SENT",
					'call_type' => "A",
					'date(cat)' => date('Y-m-d'),	
				]
			])->num_rows();
			if($dfr >=  setting('max_dfr')){
				render([
					'status' => 'info',
					'message' => 'Oops! Pembuatan Call sudah melebihi limit yaitu '.setting('max_dfr')
				], 'json');
				$limit = true;
			} else {
				if($data['call_type'] == 1){
					if($dfr_a >= setting('max_dfr_a')){
						render([
							'status' => 'info',
							'message' => 'Oops! Pembuatan Call sudah melebihi limit yaitu '.setting('max_dfr_a')
						], 'json');
						$limit = true;
					}
				}
			}
		} else {
			$data['call_type'] = NULL;
			$data['status'] = "CREATE";
		}
		if($limit == false){
			$response = save_data('trxdfr_'.date('Y').'_'.date('m'),$data, post(':validation'));
			render($response, 'json');
		}

	}

	function init_call_data(){
		$id = get('id');
		$data = get_data('sub_call_type','call_type',$id)->result_array();
		render($data, 'json');
	}

	function create_call(){
		render();
	}

}
