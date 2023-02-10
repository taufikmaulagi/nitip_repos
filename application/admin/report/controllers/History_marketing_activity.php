<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History_marketing_activity extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function get_am()
	{
		$where = [
			'history_organogram.kode_team' => get('team'),
			'nama_am !=' => '',
			'history_organogram.kode_divisi' => 'E',
			'history_organogram.tanggal_end' => '0000-00-00'
		];
		if (user('id_group') == MR_ROLE_ID) {
			$where['n_mr'] = user('username');
		} else if (user('id_group') == AM_ROLE_ID) {
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
		if (user('id_group') == MR_ROLE_ID) {
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

	function data(){

		$tahun = post('ftahun');
		$bulan = post('fbulan');
		$produk_group = post('fpgroup');
		$mr = post('fmr');
		$cycle = cycle_by_month($bulan);

		$this->session->set_userdata([
			'sess_bulan' => $bulan,
			'sess_tahun' => $tahun,
			'sess_produk_grup' => $produk_group,
			'mr' => $mr
		]);

		$data = get_data('trxdact_'.$tahun.'_'.$bulan.' a', [
			'select' => 'a.*, d.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on b.id = a.visit_plan',
				'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
				'dokter d on d.id = c.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = c.outlet',	
				'trxdact_marketing_'.$tahun.'_'.$bulan.' e on e.data_sales = a.id type left'
			],
			'where' => [
				'c.mr' => $mr,
				'c.produk_grup' => $produk_group
			],
			'group_by' => 'c.dokter, c.outlet'
		])->result_array();

		render([
			'data' => $data
		], 'layout:false');

	}

	function get_data(){

		$bulan = $this->session->userdata('sess_bulan');
		$tahun = $this->session->userdata('sess_tahun');
		$cycle = cycle_by_month($bulan);
		$id = post('id');

		$data_actual = get_data('trxdact_' . $tahun . '_' . $bulan.' a', [
			'select' => 'a.*, c.produk_grup, d.nama as nama_dokter, s.nama as nama_spesialist, ss.nama as nama_sub_spesialist, o.nama as nama_outlet, p.nama as nama_produk_grup',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on b.id = a.visit_plan',
				'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
				'dokter d on d.id = c.dokter',
				'outlet o on o.id = c.outlet',
				'spesialist s on s.id = d.spesialist',
				'sub_spesialist ss on ss.id = d.subspesialist type left',
				'produk_grup p on p.kode = c.produk_grup'
			],
			'where' => [
				'a.id' => $id
			]
		])->row_array();

		if(!empty($data_actual)){
			$marketing = get_data('marketing_aktifitas',[
				'where' => [
					'produk_grup' => $data_actual['produk_grup'],
					'is_active' => 1
				]
			])->result_array();
			$sub_marketing 		= get_data('sub_marketing_aktifitas', 'is_active', '1')->result_array();
			$persepsi 			= get_data('persepsi_acara', 'is_active', '1')->result_array();
			$value_marketing 	= get_data('trxdact_marketing_'.$tahun.'_'.$bulan, [
				'where' => [
					'data_sales' => $data_actual['id']
				]
			])->result_array();

			render([
				'data_actual' => $data_actual,
				'marketing' => $marketing,
				'value_marketing' => $value_marketing,
				'sub_marketing' => $sub_marketing,
				'persepsi' => $persepsi
			], 'json');
		}
	}

	function export(){
		$tahun = get('tahun');
		$bulan = get('bulan');
		$mr = get('mr');
		$produk_group = get('produk_group');
		$cycle = cycle_by_month($bulan);
		$data_mr = get_data('tbl_user', 'username', $mr)->row_array();

		$data_ma = get_data('trxdact_'.$tahun.'_'.$bulan.' a', [
			'select' => 'a.id, d.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet, sm.nama as nama_sub_marketing_aktifitas, ma.nama as nama_marketing_aktifitas, e.tanggal, e.tipe, e.sebagai, e.nama_pembicara',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on b.id = a.visit_plan',
				'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
				'dokter d on d.id = c.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = c.outlet',
				'trxdact_marketing_'.$tahun.'_'.$bulan.' e on e.data_sales = a.id type left',
				'sub_marketing_aktifitas sm on sm.id = e.sub_marketing_aktifitas',
				'marketing_aktifitas ma on ma.id = e.marketing_aktifitas',
			],
			'where' => [
				'c.mr' => $mr,
				'c.produk_grup' => $produk_group
			],
			'order_by_array' => [
				'd.nama' => 'asc'
			]
		])->result_array();

		$header = [
			'no' => 'No.',
			'nama_dokter' => 'Dokter',
			'nama_spesialist' => 'Spesialist',
			'nama_outlet' => 'Outlet',
			'nama_marketing_aktifitas' => 'Nama Marketing Aktifitas',
			'nama_sub_marketing_aktifitas'  => 'Nama Sub Marketing Aktifitas',
			'tanggal' => 'Tanggal',
			'tipe' => 'Tipe',
			'sebagai' => 'Sebagai',
			'nama_pembicara' => 'Nama Pembicara'
		];

		$data = [];
		$tmp_nm_produk = '';
		$numberIndex = 1;
		foreach($data_ma as $k => $v){
			if($v['tanggal'] == '0000-00-00') continue;
			if($v['nama_dokter'].' '.$v['nama_spesialist'].' '.$v['nama_outlet'] == $tmp_nm_produk){
				$v['no'] = '';
				$v['nama_dokter'] = '';
				$v['nama_spesialist'] = '';
				$v['nama_outlet'] = '';
			} else {
				$v['no'] = $numberIndex++;
				$tmp_nm_produk = $v['nama_dokter'].' '.$v['nama_spesialist'].' '.$v['nama_outlet'];
			}

			$data[] = $v;
		}

		$config = [
			'title' => 'MARKETING '.explode(' ',$data_mr['nama'])[0].' '.$tahun.'-'.$bulan,
			'data' => $data,
			'header' => $header
		];
		$this->load->library('simpleexcel', $config);
		$this->simpleexcel->export();

	}

}