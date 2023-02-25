<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rekap_reply_dfr extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		
		$tahun = get('tahun');
		$bulan = get('bulan');
		$produk_group = get('pgroup');
		

		if(!empty($tahun) && !empty($bulan) && !empty($produk_group)){
			$data = $this->__get_data($bulan, $tahun, $produk_group);
		} else {
			$data = [];
		}

		render([
			'data' => $data
		]);

	}

	function export(){
		$tahun = get('tahun');
		$bulan = get('bulan');
		$produk_group = get('produk_group');
		$header = [
			'no' => 'No.',
			'nama' => 'Name',
			'region' => 'Region',
			'reply_am' => 'Reply DFR AM',
			'reply_nsm' => 'Reply DFR NSM',
		];

		$data = [];
		$data_feedback = $this->__get_data($bulan, $tahun, $produk_group);
		foreach($data_feedback['child'] as $k => $v){
			$region = 'EAST';
			if($v['nama'] == 'DERI SYOFYAN'){
				$region = 'WEST';
			}
			foreach($v['child'] as $ck => $cv){
				foreach($cv['child'] as $cck => $ccv){
					$ccv['bgcolor'] = '#b4d6c1';
					$ccv['nama'] = $ccv['nama'].'(MR)';
					$ccv['region'] = $region;
					$ccv['no'] = $cck + 1;
					$data[] = $ccv;
				}
				$cv['bgcolor'] = '#8dc3a7';
				$ccv['nama'] = $ccv['nama'].'(AM)';
				$cv['region'] = $region;
				$cv['no'] = $ck + 1;
				$data[] = $cv;
			}
			$v['bgcolor'] = '#6baf92';
			$ccv['nama'] = $ccv['nama'].'(NSM)';
			$v['region'] = $region;
			$v['no'] = $k + 1;
			$data[] = $v;
		}

		$conf = [
			'title' => 'DFR '.$bulan.' '.$tahun,
			'header' => $header,
			'data' => $data,
		];

		$this->load->library('simpleexcel', $conf);
		$this->simpleexcel->export();
	}

	private function __get_data($bulan, $tahun, $produk_group){
		
		$cycle = cycle_by_month($bulan);

		$parameter_search = '';
		if(user('id_group') == NSM_ROLE_ID){
			$parameter_search = 'n_nsm';
		} else if(user('id_group') == AM_ROLE_ID){
			$parameter_search = 'n_am';
		} else if(user('id_group') == MR_ROLE_ID){
			$parameter_search = 'n_mr';
		}

		$tmp_where = [
			'b.tanggal_end' => '0000-00-00',
			'a.kode_divisi' => 'E',
			'a.n_nsm != ' => '',
			't.is_active' => 1,
			'n_nsm' => ['00525','00868']
		];
		
		if(!empty($parameter_search)) $tmp_where[$parameter_search] = user('username');

		$data_nsm = get_data('history_organogram_detail a', [
			'select' => 'a.n_nsm as nip, a.nama_nsm as nama',
			'join' => [
				'history_organogram b on a.id_history_organogram = b.id',
				'tim t on t.kode = a.kode_team',
			],
			'where' => $tmp_where,
			'group_by' => 'a.n_nsm',
		])->result_array();

		$data = [
			'reply_am' => 0,
			'reply_nsm' => 0,
			'child' => []
		];
		foreach($data_nsm as $k => $v){
			
			$v['reply_am'] = 0;
			$v['reply_nsm'] = 0;
			$c['child'] = [];

			$tmp_where = [
				'b.tanggal_end' => '0000-00-00',
				'a.kode_divisi' => 'E',
				'a.n_am != ' => '',
				't.is_active' => 1,
				'n_nsm' => $v['nip']
			];
			if(!empty($parameter_search)) $tmp_where[$parameter_search] = user('username');
			$data_am = get_data('history_organogram_detail a', [
				'select' => 'a.n_am as nip, a.nama_am as nama',
				'join' => [
					'history_organogram b on a.id_history_organogram = b.id',
					'tim t on t.kode = a.kode_team',
				],
				'where' => $tmp_where,
				'group_by' => 'a.n_am',
			])->result_array();

			foreach($data_am as $kam => $vam){

				$vam['reply_am'] = 0;
				$vam['reply_nsm'] = 0;

				$tmp_where = [
					'b.tanggal_end' => '0000-00-00',
					't.is_active' => 1,
					'a.n_am' => $vam['nip'],
					'a.n_mr != ' => ''
				];
				if(!empty($parameter_search)) $tmp_where[$parameter_search] = user('username');

				$data_mr = get_data('history_organogram_detail a', [
					'select' => 'a.n_mr, a.nama_mr',
					'join' => [
						'history_organogram b on a.id_history_organogram = b.id',
						'tim t on t.kode = a.kode_team',
					],
					'where' => $tmp_where,
					'group_by' => 'a.n_mr',
				])->result_array();

				$mr = array_column($data_mr, 'n_mr');

				$data_feedback = get_data('tbl_user mr', [
					'select' => 'mr.username as nip, mr.nama as nama, SUM(IF(a.id_group = 8, 1, 0)) as reply_am, SUM(IF(a.id_group = 6,1,0)) as reply_nsm',
					'where' => [
						'mr.username' => $mr,
					],
					'join' => [
						'trxprof_'.$tahun.'_'.$cycle.' c on c.mr = mr.username and c.produk_grup = "'.$produk_group.'" type left',
						'trxvisit_'.$tahun.'_'.$bulan.' b on b.profiling = c.id type left',
						'trxdfr_'.$tahun.'_'.$bulan.' d on d.visit_plan = b.id type left',
						'trxdfr_feedback_'.$tahun.'_'.$bulan.' a on a.dfr = d.id and month(a.cat) = "'.$bulan.'" and year(a.cat) = "'.$tahun.'" type left',
					],
					'group_by' => 'mr.nama'
				])->result_array();

				$vam['child'] = $data_feedback;

				foreach($data_feedback as $kf => $vf){
					$vam['reply_am'] += $vf['reply_am'];
					$vam['reply_nsm'] += $vf['reply_nsm'];
					$v['reply_am'] += $vf['reply_am'];
					$v['reply_nsm'] += $vf['reply_nsm'];

				}

				$v['child'][] = $vam;
			}
			$data['child'][] = $v;
		}
		return $data;
	}

}