<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event_marketing extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$mr = get_data('tbl_user', 'id_group', 9)->result_array();

		render([
			'mr' => $mr
		]);
	}

	function data(){
		$bulan = get('bulan');
		$tahun = get('tahun');
		$conf = [
			'select' 	=> 'a.tanggal as tanggal_realisasi, nama_marketing_aktifitas as nama_event', 
			'join' 		=> [
				'trxdact_marketing_'.$tahun.'_'.$bulan.' a on a.data_sales = trxdact_'.$tahun.'_'.$bulan.'.id'
			],
			'where' 	=> [
				'a.tanggal != '	=> '0000-00-00',
				'mr'			=> get('mr')
			],
			'group_by' => 'a.tanggal, a.marketing_aktifitas',
			'access_view' => false,
			'button' => [
				button_serverside('btn-sky','btn-detail',['fa-search','Edit',true], 'act-detail'),
				'<button class="btn btn-sky">Test</button>'
			]
		];
		$resp = data_serverside($conf);
		render($resp, 'json');
	}

	function get_data(){

		$data_sales = get('data_sales');
		$bulan		= sprintf('%02d',get('bulan'));
		$tahun		= get('tahun');

		$prev_bulan = sprintf('%02d',get('bulan'));
		$prev_tahun = get('tahun');

		if(intval($bulan) == 1){
			$prev_bulan = 12;
			$prev_tahun = intval(get('tahun')) - 1;
		} else {
			$prev_bulan = sprintf('%02d',(intval($bulan) - 1));
		}

		// $data_sales = get_data('trxdact_'.$tahun.'_'.$bulan, 'id', $data_sales)->row_array();

		$data 		= get_data('trxdact_'.$tahun.'_'.$bulan.' a', [
			'select' => 'a.nama_dokter, a.nama_spesialist, a.nama_outlet, mr.nama as nama_mr, a.customer_matrix as current_matrix, c.customer_matrix as prev_matrix, b.nama_marketing_aktifitas as nama_event, b.nama_pembicara as nama_speaker, b.tanggal, d.nama as nama_sub',
			'join' => [
				'trxdact_marketing_'.$tahun.'_'.$bulan.' b on a.id = b.data_sales',
				'trxdact_'.$prev_tahun.'_'.$prev_bulan.' c on a.mr = c.mr and a.dokter = c.dokter and a.produk_grup = c.produk_grup type left',
				'tbl_user mr on mr.kode = a.mr',
				'sub_marketing_aktifitas d on d.id = b.sub_marketing_aktifitas type left'
			],
			'where' => [
				'b.tanggal' 						=> get('tanggal'),
				'b.nama_marketing_aktifitas'		=> get('marketing'),
				'a.nama_produk_grup'				=> get('produk_grup')
			]
		])->result_array();

		// echo $this->db->last_query();

		if($data){
			render([
				'detail' => [
					'nama_event' 	=> $data[0]['nama_event'],
					'nama_speaker' 	=> $data[0]['nama_speaker'],
					'tanggal' 		=> $data[0]['tanggal'],
					'nama_sub'		=> $data[0]['nama_sub']
				],
				'data' => $data
			], 'json');
		} else {
			render([
				'detail' => [],
				'data'	 => []
			],'json');
		}

	}

}