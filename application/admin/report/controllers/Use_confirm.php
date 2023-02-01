<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Use_confirm extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data(){

		$data['visit_plan'] = [];

		if($this->db->table_exists('trxvisit_'.post('ftahun').'_'.post('fbulan'))){
			if(!$this->db->table_exists('trxdfr_'.post('ftahun').'_'.post('fbulan'))){
				$this->load->helper('generate_trx_table');
				init_table_dfr(post('ftahun'), post('fbulan'));
			}
			$data['visit_plan'] = get_data('trxvisit_'.post('ftahun').'_'.post('fbulan').' a', [
				'select' => 
				//	'a.nama_dokter, d.nama as nama_spesialist, a.nama_outlet, e.nama as nama_sub_spesialist, 
				//	(if(a.status = 3, a.plan_call, 0)) as plan_call, 
				//	(select count(*) from trxdfr_'.post('ftahun').'_'.post('fbulan').' where mr = a.mr and produk_grup = "'.post('fpgroup').'" and status = 2 and dokter = a.dokter) as actual_call,
				//	(if(a.plan_call > 0 and a.status = 3, 1, 0)) as plan_dokter_coverage,
				//	if((select count(*) from trxdfr_'.post('ftahun').'_'.post('fbulan').' where mr = a.mr and produk_grup = "'.post('fpgroup').'" and status = 2 and dokter = a.dokter) >= a.plan_call,1,0) as actual_dokter_coverage,
				//	(if(a.status = 3 && a.plan_call > 0, 1, 0)) as plan_percent_coverage,
				//	(if(a.plan_call <= count(case when b.status = 2 then 1 end), 1, 0) + 1) as actual_percent_coverage',
					'a.nama_dokter, d.nama as nama_spesialist, a.nama_outlet, e.nama as nama_sub_spesialist, 
					(if(a.status = 3, a.plan_call, 0)) as plan_call, 
					(select count(*) from trxdfr_'.post('ftahun').'_'.post('fbulan').' where mr = a.mr and produk_grup = "'.post('fpgroup').'" and status = 2 and dokter = a.dokter) as actual_call,
					(if(a.plan_call > 0 and a.status = 3, 1, 0)) as plan_dokter_coverage,
					(if(b.status = 2, 1, 0)) as actual_dokter_coverage,
					(if(a.status = 3 && a.plan_call > 0, 1, 0)) as plan_percent_coverage,
					(if(a.plan_call <= count(case when b.status = 2 then 1 end), 1, 0)) as actual_percent_coverage',
				'join' => [
					'trxdfr_'.post('ftahun').'_'.post('fbulan').' b on b.visit_plan = a.id type left',
					'dokter c on c.id = a.dokter type left',
					'spesialist d on d.id = c.spesialist type left',
					'sub_spesialist e on e.id = c.subspesialist type left',
				],
				'where' => [
					'a.mr' => post('fmr'),
					'a.status' => '3',
					'a.produk_grup' => post('fpgroup')
				],
				'group_by' => 'a.dokter',
				'sort_by' => 'a.nama_dokter',
				'sort' => 'ASC',
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
			'kode_team' => get('team'),
			'nama_am !=' => '',
			'kode_divisi' => 'E'
		];
		if(user('id_group') == MR_ROLE_ID){
			$where['n_mr'] = user('username');
		} else if(user('id_group') == AM_ROLE_ID){
			$where['n_am'] = user('username');
		}
		$data = get_data('history_organogram_detail', [
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
