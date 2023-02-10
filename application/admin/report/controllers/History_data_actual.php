<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History_data_actual extends BE_Controller {

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
			'select' => 'a.*, d.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet, (value_1 + value_2 + value_3 + value_4 + value_5 + value_6 + value_7 + value_8 + value_9 + value_10) as total_pasien',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on b.id = a.visit_plan',
				'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
				'dokter d on d.id = c.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = c.outlet',
				'trxdact_sku_'.$tahun.'_'.$bulan.' e on e.data_sales = a.id type left'
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

		$data_actual['sku'] = get_data('trxdact_sku_' . $tahun . '_' . $bulan, [
			'where' => [
				'data_sales' => $data_actual['id']
			]
		])->result_array();

		$sku_adt = get_data('trxdact_sku_adt_' . $tahun . '_' . $bulan, [
			'where' => [
				'data_sales' => $data_actual['id']
			],
			'order_by_array' => [
				['produk_grup','asc']
			]
		])->result_array();

		$produk = $this->session->userdata('produk_group');
		$data_actual['sku_adt'] = [];
		foreach($produk as $k => $v){
			$tmp_indikasi = get_data('indikasi', [
				'where' => [
					'produk_grup' => $v['kode'],
					'is_active' => 1
				]
			])->result_array();
			$tmp_produk = get_data('produk', [
				'select' => 'produk.*',
				'join' => [
					'produk_subgrup on produk_subgrup.kode = produk.kode_subgrup',
				],
				'where' => [
					'produk_subgrup.kode_grup' => $v['kode'],
					'produk.is_active' => 1
				]
			])->result_array();
			$v['indikasi'] = $tmp_indikasi;
			$v['produk'] = $tmp_produk;
			$v['sku'] = [];
			foreach($sku_adt as $sk => $sv){
				if($v['kode'] == $sv['produk_group']){
					$v['sku'][] = $sv;
				}
			}
			$data_actual['sku_adt'][] = $v;
		}

		$data_actual['indikasi'] = get_data('indikasi', [
			'where' => [
				'produk_grup' => $data_actual['produk_grup'],
				'is_active' => 1
			]
		])->result_array();

		$data_actual['produk'] = get_data('produk', [
			'select' => 'produk.*',
			'join' => [
				'produk_subgrup on produk_subgrup.kode = produk.kode_subgrup',
			],
			'where' => [
				'produk_subgrup.kode_grup' => $data_actual['produk_grup'],
				'produk.is_active' => 1
			]
		])->result_array();

		render($data_actual, 'json');
	}

	function export(){
		$tahun = get('tahun');
		$bulan = get('bulan');
		$mr = get('mr');
		$produk_group = get('produk_group');
		$cycle = cycle_by_month($bulan);
		$data_mr = get_data('tbl_user', 'username', $mr)->row_array();

		$data_sku = get_data('trxdact_'.$tahun.'_'.$bulan.' a', [
			'select' => 'a.id, d.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet, pr.nama as nama_produk, e.price, kriteria_potensi, status_dokter, customer_matrix, ifnull(e.number_of_unit, 0) as number_of_unit, (value_1 + value_2 + value_3 + value_4 + value_5 + value_6 + value_7 + value_8 + value_9 + value_10) as total_pasien, ifnull(value_1,0) as value_1,ifnull(value_2,0) as value_2,ifnull(value_3,0) as value_3,ifnull(value_4,0) as value_4,ifnull(value_5,0) as value_5,ifnull(value_6,0) as value_6,ifnull(value_7,0) as value_7,ifnull(value_8,0) as value_8,ifnull(value_9,0) as value_9,ifnull(value_10,0) as vaue_10, (ifnull(price,0) * ifnull(number_of_unit, 0)) as total_value',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on b.id = a.visit_plan',
				'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
				'dokter d on d.id = c.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = c.outlet',
				'trxdact_sku_'.$tahun.'_'.$bulan.' e on e.data_sales = a.id type left',
				'produk pr on pr.id = e.produk type left',
			],
			'where' => [
				'c.mr' => $mr,
				'c.produk_grup' => $produk_group
			],
			'group_by' => 'c.dokter, c.outlet, e.produk'
		])->result_array();

		$header = [
			'no' => 'No.',
			'nama_dokter' => 'Dokter',
			'nama_spesialist' => 'Spesialist',
			'nama_outlet' => 'Outlet',
			'nama_produk' => 'Produk',
			'number_of_unit' => 'Units'
		];

		$indikasi = get_data('indikasi', [
			'where' => [
				'is_active' => 1,
				'produk_grup' => $produk_group
			]
		])->result_array();

		$mrg_pasien = [];
		foreach($indikasi as $k => $v){
			$header['value_'.$k+1] = $v['nama'];
			$mrg_pasien[] = 'value_'.$k+1;
		}
		$header['total_pasien'] = 'Total Pasien';
		$header['kriteria_potensi'] = 'Kriteria Potensi';
		$header['status_dokter'] = 'Status Dokter';
		$header['customer_matrix'] = 'Customer Matrix';
		$header['price'] = 'Harga';
		$header['total_value'] = 'Total Value';
		
		$data = [];
		$tmp_nm_produk = '';
		$numberIndex = 1;
		foreach($data_sku as $k => $v){
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
			'title' => 'ACTUAL '.explode(' ',$data_mr['nama'])[0].' '.$tahun.'-'.$bulan,
			'data' => $data,
			'header' => $header,
			'group_header' => [
				'JUMLAH PASIEN' => $mrg_pasien
			]
		];
		$this->load->library('simpleexcel', $config);
		$this->simpleexcel->export();

	}

}