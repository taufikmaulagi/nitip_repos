<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Call_activity_national extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {

		$tahun = get('tahun');
		$bulan = get('bulan');
		$produk_grup = get('produk_grup');

		if(!empty($tahun) && !empty($bulan) && !empty($produk_grup)){
			$data = $this->__get_data($tahun, $bulan, $produk_grup);
		} else {
			$data = [];
		}

		render([
			'data' => $data
		]);

	}

	function export(){

		$bulan = get('bulan');
		$tahun = get('tahun');
		$produk_grup = get('produk_grup');

		$data_actual = $this->__get_data($tahun, $bulan, $produk_grup);

		$headers = [
			'no' => 'No.',
			'nip' => 'NIP',
			'nama' => 'Nama',
			'region' => 'REGION',
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

		$region = '';

		foreach($data_actual['child'] as $knsm => $nsm){

			if($nsm['nama'] == 'SUKISTYOWATI'){
				$region = 'EAST';
			} else {
				$region = 'WEST';
			}

			foreach($nsm['child'] as $kam => $am){

				foreach($am['child'] as $kmr => $mr){

					$mr['bgcolor'] = '#b4d6c1';
					$mr['no'] = $kmr + 1;
					$mr['region'] = $region;
					$mr['percent_call'] = ($mr['plan_call'] > 0 && $mr['actual_call'] > 0 ? round($mr['actual_call'] / $mr['plan_call'],2) : 0).'%';
					$mr['percent_dc'] = ($mr['dc_plan'] > 0 && $mr['dc_actual'] > 0 ? round($mr['dc_actual'] / $mr['dc_plan'],2) : 0).'%';
					$mr['percent_pc'] = ($mr['pc_plan'] > 0 && $mr['pc_actual'] > 0 ? round($mr['pc_actual'] / $mr['pc_plan'],2) : 0).'%';
					$data[] = $mr;

				}

				$am['bgcolor'] = '#8dc3a7';
				$am['no'] = $kam + 1;
				$am['region'] = $region;
				$am['percent_call'] = ($am['plan_call'] > 0 && $am['actual_call'] > 0 ? round($am['actual_call'] / $am['plan_call'],2) : 0).'%';
				$am['percent_dc'] = ($am['dc_plan'] > 0 && $am['dc_actual'] > 0 ? round($am['dc_actual'] / $am['dc_plan'],2) : 0).'%';
				$am['percent_pc'] = ($am['pc_plan'] > 0 && $am['pc_actual'] > 0 ? round($am['pc_actual'] / $am['pc_plan'],2) : 0).'%';
				$data[] = $am;

			}

			$nsm['bgcolor'] = '#6baf92';
			$nsm['no'] = $knsm + 1;
			$nsm['region'] = $region;
			$nsm['percent_call'] = ($nsm['plan_call'] > 0 && $nsm['actual_call'] > 0 ? round($nsm['actual_call'] / $nsm['plan_call'],2) : 0).'%';
			$nsm['percent_dc'] = ($nsm['dc_plan'] > 0 && $nsm['dc_actual'] > 0 ? round($nsm['dc_actual'] / $nsm['dc_plan'],2) : 0).'%';
			$nsm['percent_pc'] = ($nsm['pc_plan'] > 0 && $nsm['pc_actual'] > 0 ? round($nsm['pc_actual'] / $nsm['pc_plan'],2) : 0).'%';
			$data[] = $nsm;

		}

		$data[] = [
			'nama' => 'NATIONAL',
			'plan_call' => $data_actual['plan_call'],
			'actual_call' => $data_actual['actual_call'],
			'percent_call' => ($data_actual['plan_call'] > 0 && $data_actual['actual_call'] > 0 ? round($data_actual['actual_call'] / $data_actual['plan_call'],2) : 0).'%',
			'dc_plan' => $data_actual['dc_plan'],
			'dc_actual' => $data_actual['dc_actual'],
			'percent_dc' => ($data_actual['dc_plan'] > 0 && $data_actual['dc_actual'] > 0 ? round($data_actual['dc_actual'] / $data_actual['dc_plan'],2) : 0).'%',
			'pc_plan' => $data_actual['pc_plan'],
			'pc_actual' => $data_actual['pc_actual'],
			'percent_pc' => ($data_actual['pc_plan'] > 0 && $data_actual['pc_actual'] > 0 ? round($data_actual['pc_actual'] / $data_actual['pc_plan'],2) : 0).'%',
		];

		$conf = [
			'title' => 'CALL ACTIVITY NATIONAL '.strftime('%B', strtotime($tahun.' - '.$bulan.' - 01')),
			'header' => $headers,
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

	private function __get_data($tahun, $bulan, $produk_grup){
		$data_nsm = get_data('history_organogram_detail a', [
			'select' => 'a.n_nsm as nip, a.nama_nsm as nama',
			'join' => [
				'history_organogram b on a.id_history_organogram = b.id',
				'tim t on t.kode = a.kode_team',
			],
			'where' => [
				'b.tanggal_end' => '0000-00-00',
				'a.kode_divisi' => 'E',
				'a.n_nsm != ' => '',
				't.is_active' => 1,
				'n_nsm' => ['00525','00868']
			],
			'group_by' => 'a.n_nsm',
		])->result_array();

		$data = [
			'plan_call' => 0,
			'actual_call' => 0,
			'dc_plan' => 0,
			'dc_actual' => 0,
			'pc_plan' => 0,
			'pc_actual' => 0,
			'child' => []
		];
		foreach($data_nsm as $k => $v){

			$data_am = get_data('history_organogram_detail a', [
				'select' => 'a.n_am as nip, a.nama_am as nama',
				'join' => [
					'history_organogram b on a.id_history_organogram = b.id',
					'tim t on t.kode = a.kode_team',
				],
				'where' => [
					'b.tanggal_end' => '0000-00-00',
					't.is_active' => 1,
					'a.n_nsm' => $v['nip'],
					'a.n_am != ' => ''
				],
				'group_by' => 'a.n_am',
			])->result_array();
			$v['plan_call'] = 0;
			$v['actual_call'] = 0;
			$v['dc_plan'] = 0;
			$v['dc_actual'] = 0;
			$v['pc_plan'] = 0;
			$v['pc_actual'] = 0;
			$v['child'] = [];
			foreach($data_am as $kam => $vam){
				$data_mr = get_data('history_organogram_detail a', [
					'select' => 'a.n_mr, a.nama_mr',
					'join' => [
						'history_organogram b on a.id_history_organogram = b.id',
						'tim t on t.kode = a.kode_team',
					],
					'where' => [
						'b.tanggal_end' => '0000-00-00',
						't.is_active' => 1,
						'a.n_am' => $vam['nip'],
						'a.n_mr != ' => ''
					],
					'group_by' => 'a.n_mr',
				])->result_array();

				$mr = array_column($data_mr, 'n_mr');
				$data_call = get_data('tbl_user mr', [
					'select' => 'mr.username as nip, mr.nama as nama, IFNULL(sum(plan_call),0) as plan_call, IFNULL(sum(actual_call),0) as actual_call, IFNULL(sum(dc_plan),0) as dc_plan, IFNULL(sum(dc_actual),0) as dc_actual, IFNULL(sum(pc_plan),0) as pc_plan, IFNULL(sum(pc_actual),0) as pc_actual',
					'where' => [
						'mr.username' => $mr
					],
					'join' => [
						'rekap_call_activity_'.$tahun.' a on a.mr = mr.username and bulan = "'.$bulan.'" and kode_produk_grup = "'.$produk_grup.'" type left'
					],
					'group_by' => 'mr.nama'
				])->result_array();

				$vam['plan_call'] = 0;
				$vam['actual_call'] = 0;
				$vam['dc_plan'] = 0;
				$vam['dc_actual'] = 0;
				$vam['pc_plan'] = 0;
				$vam['pc_actual'] = 0;
				$vam['child'] = [];
				
				foreach($data_call as $kmr => $vmr){
					$vam['plan_call'] += $vmr['plan_call'];
					$vam['actual_call'] += $vmr['actual_call'];
					$vam['dc_plan'] += $vmr['dc_plan'];
					$vam['dc_actual'] += $vmr['dc_actual'];
					$vam['pc_plan'] += $vmr['pc_plan'];
					$vam['pc_actual'] += $vmr['pc_actual'];
					$vam['child'][] = $vmr;
				}
				$v['plan_call'] += $vam['plan_call'];
				$v['actual_call'] += $vam['actual_call'];
				$v['dc_plan'] += $vam['dc_plan'];
				$v['dc_actual'] += $vam['dc_actual'];
				$v['pc_plan'] += $vam['pc_plan'];
				$v['pc_actual'] += $vam['pc_actual'];
				$v['child'][] = $vam;

			}
			$data['plan_call'] += $v['plan_call'];
			$data['actual_call'] += $v['actual_call'];
			$data['dc_plan'] += $v['dc_plan'];
			$data['dc_actual'] += $v['dc_actual'];
			$data['pc_plan'] += $v['pc_plan'];
			$data['pc_actual'] += $v['pc_actual'];
			$data['child'][] = $v;
		}

		return $data;
	}

}