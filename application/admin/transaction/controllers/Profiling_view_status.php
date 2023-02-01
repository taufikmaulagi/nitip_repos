<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profiling_view_status extends BE_Controller {

	function __construct() {
		parent::__construct();
		if(!table_exists('trxprof_'.date('Y').'_'.active_cycle())){
			$this->load->helper('gen_trx_table');
			init_table_prof(active_cycle(), date('Y'));
		}
	}

	function index() {
		render();
	}

	function data() {
		$table = 'trxprof_'.date('Y').'_'.active_cycle();
		$config = [
			'select' => '(IFNULL(val_indikasi_1, 0) + 
					IFNULL(val_indikasi_2, 0) + 
					IFNULL(val_indikasi_3, 0) + 
					IFNULL(val_indikasi_4, 0) + 
					IFNULL(val_indikasi_5, 0) + 
					IFNULL(val_indikasi_6, 0) + 
					IFNULL(val_indikasi_7, 0) + 
					IFNULL(val_indikasi_8, 0) + 
					IFNULL(val_indikasi_9, 0) + 
					IFNULL(val_indikasi_10, 0)) as jumlah_potensi, dokter.nama as nama_dokter, spesialist.nama as nama_spesialist, outlet.nama as nama_outlet, produk_grup.nama as nama_produk_group',
			'where' 		=> [
				'mr' 			=> user('username'),
				'produk_grup' 	=> get('grup'),
				'status !='		=> "UNSUBMITTED",
			],
			'join'			=> [
				'dokter on dokter.id = '.$table.'.dokter',
				'spesialist on spesialist.id = dokter.spesialist',
				'outlet on outlet.id = '.$table.'.outlet',
				'produk_grup on produk_grup.kode = '.$table.'.produk_grup'
			],
			'sort_by' 		=> 'nama_dokter',
			'sort' 			=> 'ASC',
			'access_view' 	=> false,
			'access_edit' 	=> false,
			'button'		=> [
				button_serverside('btn-sky','btn-input','fa-search','edit')
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('trxprof_'.date('Y').'_'.active_cycle(). ' a',[
			'select' => 'a.*, b.nama as nama_dokter, c.nama as nama_outlet',
			'where' => [
				'a.id' 		 => post('id'),
				'a.status !='=> 'UNSUBMITTED'
			],
			'join' => [
				'dokter b on a.dokter = b.id',
				'outlet c on a.outlet = c.id'
			]
		])->row_array();
		render($data,'json');
	}

}