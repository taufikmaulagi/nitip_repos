<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lcv extends BE_Controller {

	function __construct() {
		parent::__construct();
		$check = table_exists('trxlcv_'.date('Y').'_'.active_cycle());
		if(!$check){
			$this->load->helper('generate_trx_table');
			create_lcv(date('Y'), active_cycle());
		}
	}

	function index() {
		$kpdm = get_data('kpdm')->result_array();
		$dokter = get_data('trxprof_'.date('Y').'_'.active_cycle(),
			[
				'select' => 'dokter, nama_dokter',
				'where' => [
					'am' => user('username')
				],
				'group_by' => 'dokter'
			]
		)->result_array();
		render([
			'dokter' => $dokter,
			'kpdm' => $kpdm
		]);
	}

	function data(){
		$config = [
			'select' => 'IF(tipe = "DOKTER", dokter.nama, kpdm.nama) as nama, pg.nama as nama_produk_grup',
			'join' => [
				'dokter on dokter.id = trxlcv_'.date('Y').'_'.active_cycle().'.dokter type left',
				'kpdm on kpdm.id = trxlcv_'.date('Y').'_'.active_cycle().'.kpdm type left',
				'produk_grup pg on pg.kode = trxlcv_'.date('Y').'_'.active_cycle().'.produk_grup type left',
			],
			'where' => [
				'pg.kode' => get('produk_group'),
				'am' => user('username')
			],
			'access_view' => false
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function save(){
		$data = post();

		$hod = get_data('history_organogram_detail hod', [
			'select' => 'hod.n_nsm',
			'join' => [
				'history_organogram ho on hod.id_history_organogram = ho.id',
			],
			'where' => [
				'ho.tanggal_end' => '0000-00-00',
				'hod.n_am' => user('username'),
			]
		])->row_array();

		$data['am'] = user('username');
		$data['nsm'] = $hod['n_nsm'];
		$save = save_data('trxlcv_'.date('Y').'_'.active_cycle(), $data);

		render($save, 'json');
	}

	function get_data(){
		$id = post('id');

		$data = get_data('trxlcv_'.date('Y').'_'.active_cycle(), 'id', $id)->row_array();
		render($data, 'json');
	}

	function submit(){
		
		$produk_group = post('produk');
		update_data('trxlcv_'.date('Y').'_'.active_cycle(), [
			'status' => 2
		], [
			'status' => 1,
			'am' => user('username'),
			'produk_grup' => $produk_group
		]);

		render([
			'status' => 'success'
		], 'json');

	}

	function delete(){
		$id = post('id');

		delete_data('trxlcv_'.date('Y').'_'.active_cycle(), 'id', $id);

		render([
			'status' => 'success',
			'message' => 'Data Telah terhapus'
		], 'json');
	}

}