<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History_visit_plan extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data(){

		$mr = post('fmr');
		$tahun = post('ftahun');
		$bulan = post('fbulan');
		$produk_group = post('fpgroup');
		$cycle = cycle_by_month($bulan);

		$data = get_data('trxvisit_'.$tahun.'_'.$bulan.' a', [
			'select' => 'a.*, d.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet, (week1 + week2 + week3 + week4 + week5 + week6) as plan_call',
			'join' => [
				'trxprof_'.$tahun.'_'.$cycle.' b on b.id = a.profiling',
				'dokter d on d.id = b.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = b.outlet'
			],
			'where' => [
				'b.mr' => $mr,
				'b.produk_grup' => $produk_group
			]
		])->result_array();
		
		render([
			'data' => $data
		],'layout:false');
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

	function get_data($bulan, $tahun) {

		$cycle = cycle_by_month($bulan);
		$data = get_data('trxvisit_'.$tahun.'_'.$bulan.' a',[
			'select' => 'a.*, d.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet, p.nama as nama_produk_group',
			'join' => [
				'trxprof_'.$tahun.'_'.$cycle.' b on a.profiling = b.id',
				'dokter d on d.id = b.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = b.outlet',
				'produk_grup p on p.kode = b.produk_grup'
			],
			'where' => [
				'a.id' => post('id')
			],
		])->row_array();
		render($data,'json');
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

	function export(){

		ini_set('memory_limit', '-1');
		
		$bulan = get('bulan');
		$tahun = get('tahun');
		$mr = get('mr');
		$produk_group = get('produk_group');
		$cycle = cycle_by_month($bulan);

		$data_mr = get_data('tbl_user', 'username', $mr)->row_array();

		$data_visit = get_data('trxvisit_'.$tahun.'_'.$bulan.' a', [
			'select' => 'a.*, d.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet',
			'join' => [
				'trxprof_'.$tahun.'_'.$cycle.' b on b.id = a.profiling',
				'dokter d on d.id = b.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = b.outlet',
			],
			'where' => [
				'b.mr' => $mr,
				'b.produk_grup' => $produk_group
			]
		])->result_array();
		
		$header = [
			'no' => 'No',
			'nama_dokter' => 'Dokter',
			'nama_spesialist' => 'Spesialist',
			'nama_outlet' => 'Outlet',
			'week1' => 'Week1',
			'week2' => 'Week2',
			'week3' => 'Week3',
			'week4' => 'Week4',
			'week5' => 'Week5',
			'week6' => 'Week6',
			'jumlah' => 'Total',
			'status' => 'Status'
		];

		$data = [];
		$total = 0;
		foreach($data_visit as $k => $v){
			$v['no'] = $k + 1;
			$v['jumlah'] = $v['week1'] + $v['week2'] + $v['week3'] + $v['week4'] + $v['week5'] + $v['week6'];
			$total += intval($v['jumlah']);
			$data[] = $v;
		}

		$config = [
			'title' => 'VISIT PLAN '.explode(' ',$data_mr['nama'])[0].' '.$tahun.'-'.$bulan,
			'data' => $data,
			'header' => $header,
			'group_header' => [
				'VISIT PLAN' => [
					'week1', 'week2', 'week3', 'week4', 'week5', 'week6'
				],
			]
		];
		$this->load->library('simpleexcel', $config);
		$this->simpleexcel->export();

	}
}