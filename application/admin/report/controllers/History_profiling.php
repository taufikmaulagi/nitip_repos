<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class History_profiling extends BE_Controller
{

	function __construct()
	{
		parent::__construct();
		$cycle = cycle_by_month(post('fbulan'));
		if (!table_exists('trxprof_' . post('ftahun') . '_' . $cycle)) {
			$this->load->helper('gen_trx_table');
			init_table_prof($cycle, post('ftahun'));
		}
	}

	function index()
	{
		render();
	}

	function data(){
		$tahun = post('ftahun');
		$cycle = cycle_by_month(post('fbulan'));
		$produk_group = post('fpgroup');

		$data['profiling'] = get_data('trxprof_' . $tahun . '_' . $cycle.' a', [
			'select' => 'a.*, d.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet',
			'where' => [
				'a.produk_grup' => $produk_group,
				'a.mr' => post('fmr'),
				'a.status' => ['APPROVED','NOT APPROVED']
			],
			'join' => [
				'dokter d on d.id = a.dokter',
				'spesialist s on s.id = d.spesialist',
				'outlet o on o.id = a.outlet',
			],
			'sort_by' => 'nama_dokter',
			'sort' => 'ASC'
		])->result_array();

		render($data, 'layout:false');
	}
	
	function get_data($bulan, $tahun) {
		$cycle = cycle_by_month($bulan);
		$data = get_data('trxprof_'.$tahun.'_'.$cycle. ' a',[
			'select' => 'a.*, b.nama as nama_dokter, c.nama as nama_outlet',
			'where' => [
				'a.id' 		=> post('id'),
			],
			'join' => [
				'dokter b on a.dokter = b.id',
				'outlet c on a.outlet = c.id'
			]
		])->row_array();
		render($data,'json');
	}

	function get_produk_grup()
	{
		$data = get_data('produk_grup', [
			'where' => [
				'kode_team' => get('team'),
				'kode_divisi' => 'E'
			]
		])->result_array();
		render($data, 'json');
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

	function get_mr()
	{
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

	function get_indikasi()
	{
		$where = [];
		if (get('pgrup')) {
			$where['produk_grup'] 	= get('pgrup');
			$where['is_active'] 	= 1;
		}
		$data = get_data('indikasi', [
			'where' => $where
		])->result_array();
		if (!empty($where)) {
			render($data, 'json');
		}
	}

	function export(){

		$mr = get('mr');
		$bulan = get('bulan');
		$tahun = get('tahun');
		$cycle = cycle_by_month($bulan);
		$produk_group = get('produk_group');

		$data_mr = get_data('tbl_user', 'username', $mr)->row_array();

		ini_set('memory_limit', '-1');
		$arr = [
			'no' => 'No.',
			'nama_dokter' => 'Nama Dokter',
			'nama_spesialist' => 'Speialist',
			'nama_sub_spesialist' => 'Sub Spesialist',
			'nama_branch' => 'Branch',
			'nama_outlet' => 'Outlet',
			'jumlah_pasien_perbulan' => 'Pasien/Bulan',
			'channel_outlet' => 'Channel Outlet',
			'tipe_pasien' => 'Patient Type',
		];

		
		$indikasi = get_data('indikasi', [
			'where' => [
				'produk_grup' => $produk_group,
				'is_active' => 1
			]
		])->result_array();
		$ind_mrg_first = 'val_indikasi_1';
		$ind_mrg_last = 'val_indikasi_'.count($indikasi);
		foreach($indikasi as $k => $v){
			$arr['val_indikasi_'.($k + 1)] = $v['nama'];
		}
		$arr['total_potensi'] = 'Total Potensi';
		
		$tmp_data = get_data('trxprof_'.$tahun.'_'.$cycle.' a', [
			'select' => 'd.nama as nama_dokter, s.nama as nama_spesialist, ss.nama as nama_sub_spesialist, o.nama as nama_outlet, b.nama as nama_branch,
				val_indikasi_1, val_indikasi_2, val_indikasi_3, val_indikasi_4, val_indikasi_6, val_indikasi_7, val_indikasi_8, val_indikasi_9, val_indikasi_10, val_indikasi_11, val_indikasi_12, val_indikasi_13, val_indikasi_14, val_indikasi_15, val_indikasi_16, val_indikasi_17, val_indikasi_18, val_indikasi_19, val_indikasi_20, 
				(val_indikasi_1 + val_indikasi_2 + val_indikasi_3 + val_indikasi_4 + val_indikasi_5 + val_indikasi_6 + val_indikasi_7 + val_indikasi_8 + val_indikasi_9 + val_indikasi_10 + val_indikasi_11 + val_indikasi_12 + val_indikasi_13 + val_indikasi_14 + val_indikasi_15 + val_indikasi_16 + val_indikasi_15 + val_indikasi_18 + val_indikasi_19 + val_indikasi_20 ) as total_potensi,
				jumlah_pasien_perbulan, channel_outlet, tipe_pasien',
			'where' => [
				'a.mr' => $mr,
				'a.produk_grup' => $produk_group
			],
			'join' => [
				'dokter d on d.id = a.dokter',
				'spesialist s on s.id = d.spesialist',
				'sub_spesialist ss on ss.id = d.subspesialist type left',
				'outlet o on o.id = a.outlet',
				'branch b on b.id = a.branch'
			],
			'order_by_array' => [
				'd.nama' => 'asc'
			]
		])->result_array();
	
		$data = [];
		foreach($tmp_data as $k => $v){
			$v['no'] = $k + 1;
			$data[] = $v;
		}
		$config = [
			// 'title' => 'Data Profiling '.$mr['nama'].' '.$tahun.' Bulan '.strftime('%B', strtotime($tahun.' - '.$bulan.' - 01')),
			'title' => 'PROFILING '.explode(' ',$data_mr['nama'])[0].' '.$tahun.'-'.$bulan,
			'data' => $data,
			'header' => $arr,
			'group_header' => [
				'TOTAL POTENSI' => [
					$ind_mrg_first, $ind_mrg_last
				]
			]
		];
		$this->load->library('simpleexcel', $config);
		$this->simpleexcel->export();

	}
}
