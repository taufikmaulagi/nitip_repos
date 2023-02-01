<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_marketing extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$tahun = get('tahun');
		$bulan = get('bulan');
		$produk_group = get('produk_group');
		$table_name = 'trxdact_'.$tahun.'_'.$bulan;
		$data = ['status' => ''];

		if(in_array($bulan, ['01','02','03','04'])){
			$cycle = '1';
		} elseif (in_array($bulan, ['05','06','07','08'])){
			$cycle = '2';
		} else {
			$cycle = '3';
		}

		if($this->db->table_exists('trxvisit_'.$tahun.'_'.$bulan) == false){
			$data['status'] = 'no_visit';
		} else {
			$this->load->helper('generate_trx_table');
			if($this->db->table_exists($table_name) == false){
				init_table_data_actual($tahun, $bulan);
			}
			if(table_exists('trxdact_marketing_'.$tahun.'_'.$bulan) == false){
				init_table_marketing($tahun, $bulan);
			}
			$data_actual = get_data($table_name, [
				'where' => [
					'mr' => user('username'),
					'produk_grup' => $produk_group
				]
			])->num_rows();
			if($data_actual <= 0){
				$visit_plan = get_data('trxvisit_'.$tahun.'_'.$bulan, [
					'where' => [
						'mr' => user('username'),
						'status' => 3,
						'produk_grup' => $produk_group
					]
				])->result_array();
				if(count($visit_plan) <= 0){
					$data['status'] = 'no_data';
				} else {
					foreach($visit_plan as $val){

						$profiling = get_data('trxprof_'.$tahun.'_'.$cycle, 'id', $val['profiling'])->row_array();
						$status_dokter_maintena = NULL;
						$status_dokter_rexulti = NULL;
						$customer_matrix_maintena = NULL;
						$customer_matrix_rexulti = NULL;
						$kriteria_potensi_maintena = NULL;
						$kriteria_potensi_rexulti = NULL;
						// $kriteria_potensi 	= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien']);
						if($val['produk_grup'] == 'EH'){	
							$kriteria_potensi 			= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
							$kriteria_potensi_maintena	= get_kriteria_potensi('MT', $profiling['jumlah_pasien'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
							$kriteria_potensi_rexulti	= get_kriteria_potensi('EO', $profiling['jumlah_pasien'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
							$status_dokter_maintena 	= get_status_dokter('MT', $data_actual['jumlah_pasien_maintena']);
							$status_dokter_rexulti 		= get_status_dokter('EO', $data_actual['jumlah_pasien_rexulti']);
							$customer_matrix_maintena 	= get_customer_matrix('MT', $status_dokter_maintena, $kriteria_potensi_maintena);
							$customer_matrix_rexulti 	= get_customer_matrix('EO', $status_dokter_rexulti, $kriteria_potensi_rexulti);
						} else {
							$kriteria_potensi 	= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien']);
						}
						$status_dokter			= get_status_dokter($val['produk_grup'], 0);
						$customer_matrix		= get_customer_matrix($val['produk_grup'], $status_dokter, $kriteria_potensi);

						insert_data($table_name, [
							'visit_plan' 					=> $val['id'],
							'mr' 							=> $val['mr'],
							'am' 							=> $val['am'],
							'rm' 							=> $val['rm'],
							'nsm' 							=> $val['nsm'],
							'asdir' 						=> $val['asdir'],
							'bud'							=> $val['bud'],
							'dokter'						=> $val['dokter'],
							'nama_dokter' 					=> $val['nama_dokter'],
							'nama_spesialist' 				=> $val['nama_spesialist'],
							'outlet' 						=> $val['outlet'],
							'nama_outlet' 					=> $val['nama_outlet'],
							'produk_grup' 					=> $val['produk_grup'],
							'kriteria_potensi' 				=> $kriteria_potensi,
							'kriteria_potensi_maintena' 	=> $kriteria_potensi_maintena,
							'kriteria_potensi_rexulti' 		=> $kriteria_potensi_rexulti,
							'status_dokter'					=> $status_dokter,
							'status_dokter_maintena'		=> $status_dokter_maintena,
							'status_dokter_rexulti'			=> $status_dokter_rexulti,
							'customer_matrix' 				=> $customer_matrix,
							'customer_matrix_maintena' 		=> $customer_matrix_maintena,
							'customer_matrix_rexulti'		=> $customer_matrix_rexulti,
							'nama_produk_grup' 				=> $val['nama_produk_grup'],
						]);
					}
				}
			}
		}
		render($data);
	}

	function data(){
		$produk_group = get('produk_group');
		// if($produk_group == 'MT' || $produk_group == 'EO'){
		// 	$produk_group = 'EH';
		// }
		$config = [
			'select' => 'spesialist.nama as nama_spesialist, sub_spesialist.nama as nama_sub_spesialist',
			'access_delete' => false,
			'access_edit' => false,
			'access_view' => false,
			'button' => [
				button_serverside('btn-sunny','btn-edit',['fa-edit','Edit','true'],'act_edit')
			],
			'join' => [
				'dokter on dokter.id = trxdact_'.get('tahun').'_'.get('bulan').'.dokter',
				'spesialist on spesialist.id = dokter.spesialist',
				'sub_spesialist on sub_spesialist.id = dokter.subspesialist type left',
			],
			'where' => [
				'mr' => user('username'),
				'produk_grup' => $produk_group
			]
		];
		$data = data_serverside($config);
		render($data, 'json');
	}

	function get_detail_dokter($bulan = '', $tahun = ''){
		$id = get('id');
		$data_actual = get_data('trxdact_'.$tahun.'_'.$bulan, [
			'where' => [
				'id' => $id
			]
		])->row_array();
		if(!empty($data_actual)){
			$marketing = get_data('marketing_aktifitas',[
				'where' => [
					'produk_grup' => $data_actual['produk_grup'],
					'is_active' => 1
				]
			])->result_array();
			$sub_marketing = get_data('sub_marketing_aktifitas', 'is_active', '1')->result_array();
			$persepsi = get_data('persepsi_acara', 'is_active', '1')->result_array();
			$value_marketing = get_data('trxdact_marketing_'.$tahun.'_'.$bulan, [
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

	function update($tahun, $bulan){
		$data = $this->input->post();
		$id = $data['id'];
		$tipe_data = [];
		$sub_data = [];
		$tanggal_data = [];
		$persepsi_sebelum_data = [];
		$persepsi_setelah_data = [];
		$sebagai_data = [];
		$nama_pembicara_data = [];
		
		$data_actual = get_data('trxdact_'.$tahun.'_'.$bulan, 'id', $id)->row_array();

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
				'nama_marketing_aktifitas' => $marketing[$i]['nama'],
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