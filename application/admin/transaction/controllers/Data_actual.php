<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_actual extends BE_Controller {

	function __construct() {
		parent::__construct();
		if($this->db->table_exists('trxdact_'.date('Y').'_'.date('m')) == false){
			$this->load->helper('gen_trx_table');
			init_table_data_actual(date('Y'), date('m'));
		}
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
			$data_actual = get_data($table_name, [
				'where' => [
					'produk_grup' => $produk_group
				]
			])->num_rows();
			if($data_actual <= 0){
				$visit_plan = get_data('trxvisit_'.$tahun.'_'.$bulan, [
					'where' => [
						'produk_grup' => $produk_group
					]
				])->result_array();
				if(count($visit_plan) <= 0){
					$data['status'] = 'no_data';
				} else {
					foreach($visit_plan as $val){

						$profiling = get_data('trxprof_'.$tahun.'_'.$cycle,[
							'select' => 'jumlah_pasien, ap_original, fee_patient, val_indikasi_1 as jumlah_pasien_rexulti, (val_indikasi_1+val_indikasi_2) as jumlah_pasien_maintena',
							'where' => [
								'id' => $val['profiling'],
							]
						])->row_array();

						$status_dokter_maintena = NULL;
						$status_dokter_rexulti = NULL;
						$customer_matrix_maintena = NULL;
						$customer_matrix_rexulti = NULL;
						$kriteria_potensi_maintena = NULL;
						$kriteria_potensi_rexulti = NULL;
						// $kriteria_potensi 	= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien']);
						if($val['produk_grup'] == 'EH'){	
							$kriteria_potensi 			= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
							$kriteria_potensi_maintena	= get_kriteria_potensi('MT', $profiling['jumlah_pasien_rexulti'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
							$kriteria_potensi_rexulti	= get_kriteria_potensi('EO', $profiling['jumlah_pasien_maintena'], 'B', $profiling['fee_patient'], $profiling['ap_original']);

							$status_dokter_maintena 	= get_status_dokter('MT', 0);
							$status_dokter_rexulti 		= get_status_dokter('EO', 0);
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
			} else {
				$data_actual = get_data('trxvisit_'.$tahun.'_'.$bulan.' a', [
					'select'	=> 'a.*',
					'where' 	=> [
						'a.mr' 			=> user('username'),
						'a.produk_grup' => $produk_group,
						'a.status' 		=> '3',
					]
				])->result_array();
				foreach($data_actual as $val){
					$tmp_data = get_data('trxdact_'.$tahun.'_'.$bulan, [
						// 'select' => 'trxdact_'.$tahun.'_'.$bulan.'.*',
						'where' => [
							'mr' => $val['mr'],
							'produk_grup' => $val['produk_grup'],
							'dokter' => $val['dokter']
						]
					])->row_array();
					if($tmp_data){

						$profiling = get_data('trxprof_indikasi_'.$tahun.'_'.$cycle, [
							'select' => 'jumlah_pasien, ap_original, fee_patient, val_indikasi_1 as jumlah_pasien_rexulti, (val_indikasi_1+val_indikasi_2) as jumlah_pasien_maintena',
							'where' => [
								'profiling' => $val['profiling'],
								'bulan' =>  $bulan
							]
						])->row_array();


						$sku = get_data('trxdact_sku_'.$tahun.'_'.$bulan, [
							'select' => 'SUM(IFNULL(value_1, 0) + IFNULL(value_2, 0) + IFNULL(value_3, 0) + IFNULL(value_4, 0) + IFNULL(value_5, 0) + IFNULL(value_6, 0) + IFNULL(value_7, 0) + IFNULL(value_8, 0) + IFNULL(value_9, 0) + IFNULL(value_10, 0)) as sku',
							'where' => [
								'data_sales' => $tmp_data['id']
							]
						])->row_array();

						$status_dokter_maintena = NULL;
						$status_dokter_rexulti = NULL;
						$customer_matrix_maintena = NULL;
						$customer_matrix_rexulti = NULL;
						$kriteria_potensi_maintena = NULL;
						$kriteria_potensi_rexulti = NULL;
						// $kriteria_potensi 	= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien']);
						if($val['produk_grup'] == 'EH'){	
							$kriteria_potensi 			= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
							$kriteria_potensi_maintena	= get_kriteria_potensi('MT', $profiling['jumlah_pasien_rexulti'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
							$kriteria_potensi_rexulti	= get_kriteria_potensi('EO', $profiling['jumlah_pasien_maintena'], 'B', $profiling['fee_patient'], $profiling['ap_original']);

							$status_dokter_maintena 	= get_status_dokter('MT', 0);
							$status_dokter_rexulti 		= get_status_dokter('EO', 0);
							$customer_matrix_maintena 	= get_customer_matrix('MT', $status_dokter_maintena, $kriteria_potensi_maintena);
							$customer_matrix_rexulti 	= get_customer_matrix('EO', $status_dokter_rexulti, $kriteria_potensi_rexulti);
						} else {
							$kriteria_potensi 	= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien']);
						}
						$status_dokter			= get_status_dokter($val['produk_grup'], $sku['sku']);
						$customer_matrix		= get_customer_matrix($val['produk_grup'], $status_dokter, $kriteria_potensi);

						update_data($table_name, [
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
						], 'id', $tmp_data['id']);

					} else {
						$profiling = get_data('trxprof_indikasi_'.$tahun.'_'.$cycle, [
							'select' => 'jumlah_pasien, ap_original, fee_patient, val_indikasi_1 as jumlah_pasien_rexulti, (val_indikasi_1+val_indikasi_2) as jumlah_pasien_maintena',
							'where' => [
								'profiling' => $val['profiling'],
								'bulan' =>  $bulan
							]
						])->row_array();
						$status_dokter_maintena = NULL;
						$status_dokter_rexulti = NULL;
						$customer_matrix_maintena = NULL;
						$customer_matrix_rexulti = NULL;
						$kriteria_potensi_maintena = NULL;
						$kriteria_potensi_rexulti = NULL;
						// $kriteria_potensi 	= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien']);
						if($val['produk_grup'] == 'EH'){	
							$kriteria_potensi 			= get_kriteria_potensi($val['produk_grup'], $profiling['jumlah_pasien'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
							$kriteria_potensi_maintena	= get_kriteria_potensi('MT', $profiling['jumlah_pasien_rexulti'], 'B', $profiling['fee_patient'], $profiling['ap_original']);
							$kriteria_potensi_rexulti	= get_kriteria_potensi('EO', $profiling['jumlah_pasien_maintena'], 'B', $profiling['fee_patient'], $profiling['ap_original']);

							$status_dokter_maintena 	= get_status_dokter('MT', 0);
							$status_dokter_rexulti 		= get_status_dokter('EO', 0);
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
			'access_delete' => false,
			'access_edit' => false,
			'access_view' => false,
			'button' => [
				button_serverside('btn-sunny','btn-edit',['fa-edit','Edit','true'],'act_edit')
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
			$indikasi = get_data('indikasi', [
				'where' => [
					'produk_grup' => $data_actual['produk_grup'],
					'is_active' => 1
				]
			])->result_array();
			$produk = get_data('produk',[
				'select' => 'produk.*',
				'join' => [
					'produk_subgrup on produk_subgrup.kode = produk.kode_subgrup',
				],
				'where' => [
					'produk_subgrup.kode_grup' => $data_actual['produk_grup'],
					'produk.is_active' => 1
				]
			])->result_array();
			$value_sku = get_data('trxdact_sku_'.$tahun.'_'.$bulan, [
				'where' => [
					'data_sales' => $data_actual['id']
				]
			])->result_array();
			render([
				'data_actual' => $data_actual,
				'indikasi' => $indikasi,
				'produk' => $produk,
				'value_sku' => $value_sku
			], 'json');
		}
	}

	function update($tahun, $bulan){
		$data = $this->input->post();
		$id = $data['id'];
			
		$this->db->trans_begin();

		$data_actual = get_data('trxdact_'.$tahun.'_'.$bulan,'id',$id)->row_array();

		$sku = $data['sku'];
		$units = $data['units'];
		$value_1 = !empty($data['value_1']) ? $data['value_1'] : [];
		$value_2 = !empty($data['value_2']) ? $data['value_2'] : [];
		$value_3 = !empty($data['value_3']) ? $data['value_3'] : [];
		$value_4 = !empty($data['value_4']) ? $data['value_4'] : [];
		$value_5 = !empty($data['value_5']) ? $data['value_5'] : [];
		$value_6 = !empty($data['value_6']) ? $data['value_6'] : [];
		$value_7 = !empty($data['value_7']) ? $data['value_7'] : [];
		$value_8 = !empty($data['value_8']) ? $data['value_8'] : [];
		$value_9 = !empty($data['value_9']) ? $data['value_9'] : [];
		$value_10 = !empty($data['value_10']) ? $data['value_10'] : [];

		delete_data('trxdact_sku_'.$tahun.'_'.$bulan,'data_sales',$id);
		$jumlah_pasien = 0;
		$jumlah_pasien_rexulti = 0;
		$jumlah_pasien_maintena = 0;
		$total_value = 0;
		for($i=0;$i<count($sku);$i++){
			$price = intval(get_price_detail($sku[$i]));
			insert_data('trxdact_sku_'.$tahun.'_'.$bulan, [
				'data_sales' => $id,
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
			if($data_actual['produk_grup'] == 'EH' && !in_array($sku[$i], ['137','138','145','146','147','148'])){
				$jumlah_pasien += ($value_1[$i]+$value_2[$i]+$value_3[$i]+$value_4[$i]+$value_5[$i]);
			} else if($data_actual['produk_grup'] != 'EH'){
				$jumlah_pasien += ($value_1[$i]+$value_2[$i]+$value_3[$i]+$value_4[$i]+$value_5[$i]);
			} else {
				if(in_array($sku[$i], ['137','138'])){
					//maintena
					$jumlah_pasien_maintena += ($value_1[$i]+$value_2[$i]);
				} else {
					//rexulti
					$jumlah_pasien_rexulti += ($value_1[$i]);
				}
			}
			$total_value += ($price * $units[$i]);
		}

		// $cycle = 1;
		// if(in_array($bulan, [5,6,7,8])){
		// 	$cycle = 2;
		// } else if(in_array($bulan, [9,10,11,12])){
		// 	$cycle = 3;
		// }

		// $kriteria_potensi = '';
		// $profiling = get_data('trxprof_'.$tahun.'_'.$cycle, [
		// 	'select' => '((indikasi_1+indikasi_2+indikasi_3+indikasi_4+indikasi_5)/100)*jumlah_pasien as jumlah_pasien, fee_patient, ap_original',
		// 	'where' => [
		// 		'dokter' => $data_actual['dokter']
		// 	]
		// ])->row_array();
		$kriteria_potensi = $data_actual['kriteria_potensi'];
		$kriteria_potensi_maintena = $data_actual['kriteria_potensi_maintena'];
		$kriteria_potensi_rexulti = $data_actual['kriteria_potensi_rexulti'];
		// if($data_actual['produk_grup'] == 'EH'){	
		// 	$kriteria_potensi = get_kriteria_potensi($data_actual['produk_grup'], $profiling['jumlah_pasien'], 'A', $profiling['fee_patient'], $profiling['ap_original']);
		// } else {
		// 	$kriteria_potensi = get_kriteria_potensi($data_actual['produk_grup'], $profiling['jumlah_pasien']);
		// }
		$status_dokter = get_status_dokter($data_actual['produk_grup'], $jumlah_pasien);
		$status_dokter_rexulti = get_status_dokter('EO', $jumlah_pasien_rexulti);
		$status_dokter_maintena = get_status_dokter('MT', $jumlah_pasien_maintena);
		$update_actual = [
			'total_alai' 				=> !empty($data['total_alai']) ? $data['total_alai'] : 0,
			'total_tlai' 				=> !empty($data['total_tlai']) ? $data['total_tlai'] : 0,
			'other_ap_original' 		=> !empty($data['other_ap_original']) ? $data['other_ap_original'] : 0,
			'kriteria_potensi' 			=> $kriteria_potensi,
			'status_dokter' 			=> $status_dokter,
			'status_dokter_rexulti' 	=> $status_dokter_rexulti,
			'status_dokter_maintena' 	=> $status_dokter_maintena,
			'customer_matrix' 			=> get_customer_matrix($data_actual['produk_grup'], $status_dokter, $kriteria_potensi),
			'customer_matrix_rexulti'	=> get_customer_matrix('EO', $status_dokter_rexulti, $kriteria_potensi_rexulti),
			'customer_matrix_maintena'	=> get_customer_matrix('MT', $status_dokter_maintena, $kriteria_potensi_maintena),
			'jumlah_pasien' 			=> $jumlah_pasien,
			'jumlah_pasien_rexulti' 	=> $jumlah_pasien_rexulti,
			'jumlah_pasien_maintena' 	=> $jumlah_pasien_maintena,
			'total_value' 				=> $total_value
		];
		$response = update_data('trxdact_'.$tahun.'_'.$bulan, $update_actual, 'id', $id);

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