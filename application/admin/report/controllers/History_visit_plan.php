<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History_visit_plan extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data(){

		$data['visit_plan'] = [];

		if($this->db->table_exists('trxvisit_'.post('ftahun').'_'.post('fbulan'))){
			$data['visit_plan'] = get_data('trxvisit_'.post('ftahun').'_'.post('fbulan'), [
				'where' => [
					'mr' => post('fmr'),
					'appvr_at !=' => null,
					'produk_grup' => post('fpgroup')
				],
				'where_in' => [
					'status' => [3,4]
				]
			])->result_array();
		}
		render($data,'layout:false');
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
		$data = get_data('trxvisit_'.$tahun.'_'.$bulan,[
			'where' => [
				'id' => post('id')
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
}