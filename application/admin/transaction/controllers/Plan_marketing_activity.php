<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plan_marketing_activity extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$marketing = get_data('marketing_aktifitas', [
			'where' => [
				'is_active' => 1,
				'produk_grup' => get('produk')
			]
		])->result_array();
		render([
			'marketing' => $marketing
		]);
	}

	function get_detail(){
		$bulan 	= date('m');
		$tahun 	= date('Y');
		$mr 	= user('username');
		$marketing = get('id');

		$data = get_data('plan_marketing', [
			'where' => [
				'bulan' => $bulan,
				'tahun' => $tahun,
				'mr' => $mr, 
				'marketing_aktifitas' => $marketing
			]
		])->row_array();

		render($data,'json');
	}

	function save(){
		$bulan 	= date('m');
		$tahun 	= date('Y');
		$mr 	= user('username');
		$marketing = post('id');

		// debug($this->input->post()); die;

		$data = get_data('plan_marketing', [
			'where' => [
				'bulan' => $bulan,
				'tahun' => $tahun,
				'mr' => $mr, 
				'marketing_aktifitas' => $marketing
			]
		])->row_array();

		if($data){
			update_data('plan_marketing', [
				'target' => post('target'),
				'dokter' => json_encode($this->input->post('dokter'))
			], 'id', $data['id']);
		} else {
			insert_data('plan_marketing', [
				'bulan' => $bulan,
				'tahun' => $tahun,
				'mr' => $mr, 
				'marketing_aktifitas' => $marketing,
				'target' => post('target'),
				'dokter' => json_encode($this->input->post('dokter'))
			]);
		}

		render(['status'=>'success','message'=>'Data plan marketing telah tersimpan'], 'json');
	}

}