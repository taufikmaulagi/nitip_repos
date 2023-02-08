<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_marketing extends BE_Controller {

	function __construct() {
		parent::__construct();
		if ($this->db->table_exists('trxdact_' . date('Y') . '_' . date('m')) == false) {
			$this->load->helper('gen_trx_table');
			init_table_data_actual(date('Y'), date('m'));
		}
	}

	function index() {
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
				$kriteria_potensi 	= get_kriteria_potensi($produk_group, $v['jumlah_pasien']);
				$status_dokter 		= get_status_dokter($produk_group, 0);
				$customer_matrix 	= get_customer_matrix($produk_group, $status_dokter, $kriteria_potensi);

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

	function data(){
		$produk_group 	= get('produk_group');
		$bulan 			= get('bulan');
		$tahun 			= get('tahun');

		$this->session->set_userdata([
			'dact_bulan' => $bulan,
			'dact_tahun' => $tahun
		]);

		$cycle			= cycle_by_month($bulan);

		$config = [
			'select' => 'd.nama as nama_dokter, s.nama as nama_spesialist, o.nama as nama_outlet',
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

	function get_data(){

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

	function save(){
		$data = $this->input->post();
		$bulan = $this->session->userdata('dact_bulan');
		$tahun = $this->session->userdata('dact_tahun');
		$cycle = cycle_by_month($bulan);
		$id = $data['id'];
		$tipe_data = [];
		$sub_data = [];
		$tanggal_data = [];
		$persepsi_sebelum_data = [];
		$persepsi_setelah_data = [];
		$sebagai_data = [];
		$nama_pembicara_data = [];
		
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

		foreach($data as $key => $val){
			$eval = explode('|',$key);
			switch($eval[0]){
				case 'type':
					$tipe_data[] = $val;
				break;
				case 'sub':
					$sub_data[] = $val;
				break;
				case 'tanggal':
					$tanggal_data[] = $val;
				break;
				case 'persepsi_sebelum':
					$persepsi_sebelum_data[] = $val;
				break;
				case 'persepsi_setelah':
					$persepsi_setelah_data[] = $val;
				break;
				case 'sebagai':
					$sebagai_data[] = $val;
				break;
				case 'nama_pembicara':
					$nama_pembicara_data[] = $val;
				break;
			}
		}
		$marketing = get_data('marketing_aktifitas', [
			'where' => [
				'produk_grup' => $data_actual['produk_grup'],
				'is_active' => 1
			]
		])->result_array();
		delete_data('trxdact_marketing_'.$tahun.'_'.$bulan,'data_sales',$id);
		for($i=0;$i<count($marketing);$i++){
			insert_data('trxdact_marketing_'.$tahun.'_'.$bulan, [
				'data_sales' => $id,
				'tipe' => $tipe_data[$i],
				'sub_marketing_aktifitas' => $sub_data[$i],
				'marketing_aktifitas' => $marketing[$i]['id'],
				'tanggal' => $tanggal_data[$i],
				// 'persepsi_sebelum' => $persepsi_sebelum_data[$i],
				// 'persepsi_setelah' => $persepsi_setelah_data[$i],
				'sebagai' => $sebagai_data[$i],
				'nama_pembicara' => $nama_pembicara_data[$i]
				
			]);
		}

		if($this->db->trans_status()){
			$this->db->trans_commit();
		} else {
			$this->db->trans_rollback();
		}

		render([
			'status' => 'success',
			'message' => 'Simpan selesai'
		], 'json');
	}

}