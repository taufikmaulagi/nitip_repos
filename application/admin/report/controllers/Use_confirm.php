<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Use_confirm extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data(){

		$bulan = post('fbulan');
		$tahun = post('ftahun');
		$mr = post('fmr');
		$produk_group = post('fpgroup');

		if(!table_exists('rekap_call_activity_'.$tahun)){
			$this->load->helper('gen_report_table');
			init_table_rekap_call_activity($tahun);
		}

		$data = get_data('rekap_call_activity_'.$tahun, [
			'where' => [
				'bulan' => $bulan,
				'mr' => $mr,
				'kode_produk_grup' => $produk_group
			]
		])->result_array();
		
		render([
			'data' => $data
		],'layout:false');
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

	function export(){
		
		$tahun = get('tahun');
		$bulan = get('bulan');
		$mr = get('mr');
		$produk_group = get('produk_group');

		$data_mr = get_data('tbl_user','username', $mr)->row_array();
		$data_ca = get_data('rekap_call_activity_'.$tahun, [
			'select' => 'nama_dokter, nama_spesialist, nama_sub_spesialist, nama_outlet, plan_call, actual_call, dc_plan, dc_actual, pc_plan, pc_actual',
			'where' => [
				'bulan' => $bulan,
				'mr' => $mr,
				'kode_produk_grup' => $produk_group
			]
		])->result_array();

		$header = [
			'no' => 'No.',
			'nama_dokter'  =>  'Dokter',
			'nama_spesialist' => 'Spesialist',
			'nama_sub_spesialist' => 'Sub Spesialist',
			'nama_outlet' => 'Outlet',
			'plan_call' => 'PLAN',
			'actual_call' => 'ACTUAL',
			'percent_call' => '%',
			'dc_plan' => 'PLAN',
			'dc_actual' => 'ACTUAL',
			'percent_dc' => '%',
			'pc_plan' => 'PLAN',
			'pc_actual' => 'ACTUAL',
			'percent_pc' => '%',
 		];

		$data = [];
		$total_plan_call = 0;
		$total_actual_call = 0;
		$total_dc_plan = 0;
		$total_dc_actual = 0;
		$total_pc_plan = 0;
		$total_pc_actual = 0;

		foreach($data_ca as $k => $v){
			$v['no'] = $k + 1;
			$v['percent_call'] = ($v['actual_call'] > 0 && $v['plan_call'] > 0 ? round($v['actual_call'] / $v['plan_call'],2) : 0).'%';
			$v['percent_dc'] = ($v['dc_actual'] > 0 && $v['dc_plan'] > 0 ? round($v['dc_actual'] / $v['dc_plan'],2) : 0).'%';
			$v['percent_pc'] = ($v['pc_actual'] > 0 && $v['pc_plan'] > 0 ? round($v['pc_actual'] / $v['pc_plan'],2) : 0).'%';

			$total_plan_call += $v['plan_call'];
			$total_actual_call += $v['actual_call'];
			$total_dc_plan += $v['dc_plan'];
			$total_dc_actual += $v['dc_actual'];
			$total_pc_plan += $v['pc_plan'];
			$total_pc_actual += $v['pc_actual'];

			$data[] = $v;
		}

		$total_percent_call = ($total_plan_call > 0 && $total_actual_call > 0 ? round($total_actual_call / $total_plan_call, 2) : 0).'%';
		$total_percent_dc = ($total_dc_plan > 0 && $total_dc_actual > 0 ? round($total_dc_actual / $total_dc_plan, 2) : 0).'%';
		$total_percent_pc = ($total_pc_plan > 0 && $total_pc_actual > 0 ? round($total_pc_actual / $total_pc_plan, 2) : 0).'%';

		$data[] = [
			'plan_call' => $total_plan_call,
			'actual_call' => $total_actual_call,
			'percent_call' =>  $total_percent_call,
			'dc_plan' => $total_dc_plan,
			'dc_actual' => $total_dc_actual,
			'percent_dc' => $total_percent_dc,
			'pc_plan' => $total_pc_plan,
			'pc_actual' => $total_pc_actual,
			'percent_pc' => $total_percent_pc
		];

		$conf = [
			'title' => 'CALL ACTIVITY '.explode(' ',$data_mr['nama'])[0].' '.$tahun.'-'.$bulan,
			'header' => $header,
			'data' => $data,
			'group_header' => [
				'TOTAL CALL' => [
					'plan_call','actual_call','percent_call'
				],
				'DOCTOR COVERAGE' => [
					'dc_plan','dc_actual','percent_dc'
				],
				'PERCENT COVERAGE' => [
					'pc_plan','pc_actual','percent_pc'
				]
			]
 		];
		
		$this->load->library('simpleexcel', $conf);
		$this->simpleexcel->export();

	}
}
