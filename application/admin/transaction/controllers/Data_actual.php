<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Data_actual extends BE_Controller
{

	function __construct()
	{
		parent::__construct();
		if ($this->db->table_exists('trxdact_' . date('Y') . '_' . date('m')) == false) {
			$this->load->helper('gen_trx_table');
			init_table_data_actual(date('Y'), date('m'));
		}
	}

	function index()
	{
		$tahun 			= get('tahun') ? get('tahun') : date('Y');
		$bulan 			= get('bulan') ? get('bulan') : date('m');
		$produk_group 	= get('produk_group');
		$cycle 			= cycle_by_month($bulan);

		$visit_plan = get_data('trxvisit_'.$tahun.'_'.$bulan.' a', [
			'select' => 'a.id, (val_indikasi_1 + val_indikasi_2 + val_indikasi_3 + val_indikasi_6 + val_indikasi_7 + val_indikasi_8 + val_indikasi_9 + val_indikasi_10 + val_indikasi_11 + val_indikasi_12 + val_indikasi_13 + val_indikasi_14 + val_indikasi_15 + val_indikasi_16 + val_indikasi_17 + val_indikasi_18 + val_indikasi_19 + val_indikasi_20) as jumlah_pasien',
			'join' => [
				'trxprof_'.$tahun.'_'.$cycle.' b on a.profiling = b.id',
			],
			'where' => [
				'b.mr' => user('username'),
				'b.produk_grup' => $produk_group
			]
		])->result_array();
		foreach($visit_plan as $k => $v){
			$check = get_data('trxdact_'.$tahun.'_'.$bulan, [
				'where' => [
					'visit_plan' => $v['id']
				]
			])->row_array();
			if(!$check){
				$kriteria_potensi = get_kriteria_potensi($produk_group, $v['jumlah_pasien']);
				$status_dokter = get_status_dokter($produk_group, 0);
				$customer_matrix = get_customer_matrix($produk_group, $status_dokter, $kriteria_potensi);

				insert_data('trxdact_'.$tahun.'_'.$bulan, [
					'visit_plan' => $v['id'],
					'kriteria_potensi' => $kriteria_potensi,
					'status_dokter' => $status_dokter,
					'customer_matrix' => $customer_matrix
				]);
			}
		}

		render();
	}

	function data()
	{
		$produk_group 	= get('produk_group');
		$bulan 			= get('bulan');
		$tahun 			= get('tahun');

		$this->session->set_userdata([
			'dact_bulan' => $bulan,
			'dact_tahun' => $tahun
		]);

		$cycle			= cycle_by_month($bulan);

		$config = [
			'select' => 'd.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet, (SUM(IFNULL(value_1,0)) + SUM(IFNULL(value_2,0)) + SUM(IFNULL(value_3,0)) + SUM(IFNULL(value_4,0)) + SUM(IFNULL(value_5,0)) + SUM(IFNULL(value_6,0)) + SUM(IFNULL(value_8,0)) + SUM(IFNULL(value_9,0)) + SUM(IFNULL(value_10,0))) as jumlah_pasien, (unit * hjp) as total_value',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' a on a.id = trxdact_'.$tahun.'_'.$bulan.'.visit_plan',
				'trxprof_'.$tahun.'_'.$cycle.' b on b.id = a.profiling',
				'trxdact_sku_'.$tahun.'_'.$bulan.' c on c.data_sales = trxdact_'.$tahun.'_'.$bulan.'.id type left',
				'dokter d on d.id = b.dokter',
				'outlet o on o.id = b.outlet',
				'spesialist s on s.id = d.spesialist'
			],
			'where' => [
				'b.mr' 			=> user('username'),
				'b.produk_grup' => $produk_group
			],
			'group_by' => 'a.id',
			'access_delete' => false,
			'access_view' => false,
		];
		$data = data_serverside($config);
		render($data, 'json');
	}

	function get_data()
	{
		$bulan = $this->session->userdata('dact_bulan');
		$tahun = $this->session->userdata('dact_tahun');
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

	function save()
	{
		$data = $this->input->post();
		$bulan = $this->session->userdata('dact_bulan');
		$tahun = $this->session->userdata('dact_tahun');
		$cycle = cycle_by_month($bulan);

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
				'a.id' => $data['id']
			]
		])->row_array();

		$sku		= $data['sku'];
		$units 		= $data['units'];
		$jumlah_pasien = 0;
		$value_1 	= !empty($data['value_1']) ? $data['value_1'] : [];
		$value_2 	= !empty($data['value_2']) ? $data['value_2'] : [];
		$value_3 	= !empty($data['value_3']) ? $data['value_3'] : [];
		$value_4 	= !empty($data['value_4']) ? $data['value_4'] : [];
		$value_5 	= !empty($data['value_5']) ? $data['value_5'] : [];
		$value_6 	= !empty($data['value_6']) ? $data['value_6'] : [];
		$value_7 	= !empty($data['value_7']) ? $data['value_7'] : [];
		$value_8 	= !empty($data['value_8']) ? $data['value_8'] : [];
		$value_9 	= !empty($data['value_9']) ? $data['value_9'] : [];
		$value_10 	= !empty($data['value_10']) ? $data['value_10'] : [];

		delete_data('trxdact_sku_' . $tahun . '_' . $bulan, 'data_sales', $data['id']);

		for ($i = 0; $i < count($sku); $i++) {
			$price = intval(get_price_detail($sku[$i]));
			insert_data('trxdact_sku_' . $tahun . '_' . $bulan, [
				'data_sales' => $data['id'],
				'produk' => $sku[$i],
				'number_of_unit' => $units[$i],
				'value_1' => !empty($value_1[$i]) ? $value_1[$i] : 0,
				'value_2' => !empty($value_2[$i]) ? $value_2[$i] : 0,
				'value_3' => !empty($value_3[$i]) ? $value_3[$i] : 0,
				'value_4' => !empty($value_4[$i]) ? $value_4[$i] : 0,
				'value_5' => !empty($value_5[$i]) ? $value_5[$i] : 0,
				'value_6' => !empty($value_6[$i]) ? $value_6[$i] : 0,
				'value_7' => !empty($value_7[$i]) ? $value_7[$i] : 0,
				'value_8' => !empty($value_8[$i]) ? $value_8[$i] : 0,
				'value_9' => !empty($value_9[$i]) ? $value_9[$i] : 0,
				'value_10' => !empty($value_10[$i]) ? $value_10[$i] : 0,
				'price' => $price
			]);
			$value_1[$i] = !empty($value_1[$i]) ? $value_1[$i] : 0;
			$value_2[$i] = !empty($value_2[$i]) ? $value_2[$i] : 0;
			$value_3[$i] = !empty($value_3[$i]) ? $value_3[$i] : 0;
			$value_4[$i] = !empty($value_4[$i]) ? $value_4[$i] : 0;
			$value_5[$i] = !empty($value_5[$i]) ? $value_5[$i] : 0;
			$value_6[$i] = !empty($value_6[$i]) ? $value_6[$i] : 0;
			$value_7[$i] = !empty($value_7[$i]) ? $value_7[$i] : 0;
			$value_8[$i] = !empty($value_8[$i]) ? $value_8[$i] : 0;
			$value_9[$i] = !empty($value_9[$i]) ? $value_9[$i] : 0;
			$value_10[$i] = !empty($value_10[$i]) ? $value_10[$i] : 0;

			$jumlah_pasien += ($value_1[$i] + $value_2[$i] + $value_3[$i] + $value_4[$i] + $value_5[$i] + $value_6[$i] + $value_7[$i] + $value_8[$i] + $value_9[$i] + $value_10[$i]);
		}

		$kriteria_potensi 	= $data_actual['kriteria_potensi'];
		$status_dokter 		= get_status_dokter($data_actual['produk_grup'], $jumlah_pasien);

		$update_actual = [
			'kriteria_potensi' 			=> $kriteria_potensi,
			'status_dokter' 			=> $status_dokter,
			'customer_matrix' 			=> get_customer_matrix($data_actual['produk_grup'], $status_dokter, $kriteria_potensi),
			'jumlah_pasien' 			=> $jumlah_pasien,
		];
		// debug($data); die;
		//Additional Product Group SKU
		$produk_grup = $this->session->userdata('produk_group');
		foreach($produk_grup as $k => $v){

			if(!isset($data['sku_adt_'.$v['kode']])) continue;

			$sku			= $data['sku_adt_'.$v['kode']];
			$units 			= $data['units_adt_'.$v['kode']];
			$value_1 		= !empty($data['value_adt_'.$v['kode'].'1']) ? $data['value_adt_'.$v['kode'].'1'] : [];
			$value_2 		= !empty($data['value_adt_'.$v['kode'].'2']) ? $data['value_adt_'.$v['kode'].'2'] : [];
			$value_3 		= !empty($data['value_adt_'.$v['kode'].'3']) ? $data['value_adt_'.$v['kode'].'3'] : [];
			$value_4 		= !empty($data['value_adt_'.$v['kode'].'4']) ? $data['value_adt_'.$v['kode'].'4'] : [];
			$value_5 		= !empty($data['value_adt_'.$v['kode'].'5']) ? $data['value_adt_'.$v['kode'].'5'] : [];
			$value_6 		= !empty($data['value_adt_'.$v['kode'].'6']) ? $data['value_adt_'.$v['kode'].'6'] : [];
			$value_7 		= !empty($data['value_adt_'.$v['kode'].'7']) ? $data['value_adt_'.$v['kode'].'7'] : [];
			$value_8 		= !empty($data['value_adt_'.$v['kode'].'8']) ? $data['value_adt_'.$v['kode'].'8'] : [];
			$value_9 		= !empty($data['value_adt_'.$v['kode'].'9']) ? $data['value_adt_'.$v['kode'].'9'] : [];
			$value_10 		= !empty($data['value_adt_'.$v['kode'].'10']) ? $data['value_adt_'.$v['kode'].'10'] : [];

			delete_data('trxdact_sku_adt_' . $tahun . '_' . $bulan, [
				'data_sales' => $data['id'],
				'produk_group' => $v['kode']
			]);
			// debug($units);

			for ($i = 0; $i < count($sku); $i++) {
				$price = intval(get_price_detail($sku[$i]));
				insert_data('trxdact_sku_adt_' . $tahun . '_' . $bulan, [
					'data_sales' => $data['id'],
					'produk' => $sku[$i],
					'produk_group' => $v['kode'],
					'number_of_unit' => $units[$i],
					'value_1' => !empty($value_1[$i]) ? $value_1[$i] : 0,
					'value_2' => !empty($value_2[$i]) ? $value_2[$i] : 0,
					'value_3' => !empty($value_3[$i]) ? $value_3[$i] : 0,
					'value_4' => !empty($value_4[$i]) ? $value_4[$i] : 0,
					'value_5' => !empty($value_5[$i]) ? $value_5[$i] : 0,
					'value_6' => !empty($value_6[$i]) ? $value_6[$i] : 0,
					'value_7' => !empty($value_7[$i]) ? $value_7[$i] : 0,
					'value_8' => !empty($value_8[$i]) ? $value_8[$i] : 0,
					'value_9' => !empty($value_9[$i]) ? $value_9[$i] : 0,
					'value_10' => !empty($value_10[$i]) ? $value_10[$i] : 0,
					'price' => $price
				]);
			}

		}

		update_data('trxdact_' . $tahun . '_' . $bulan, $update_actual, 'id', $data['id']);

		render([
			'status' => 'success',
			'message' => 'Simpan selesai'
		], 'json');
	}
}
