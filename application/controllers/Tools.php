<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tools extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

    function sync_doctor_branch(){

        die('NOT WORKING');

        ini_set('memory_limit',-1);
        ini_set('max_execution_time',-1);
        // echo 'Loading.... Tunggu yah <br/>';
        $dBranch = get_data('tm_doctor_branch_oi')->result_array();
        foreach($dBranch as $val){
            update_data('dokter', ['branch'=>$val['id_tbl_m_branch_oi']],  'id', $val['id_tm_doctor']);
        }
        echo 'Selesai :) ';
    }

    function sync_nip_profiling(){

        die('DEPRECATED');

        ini_set('memory_limit',-1);
        ini_set('max_execution_time',-1);
        $profiling = get_data('profiling')->result_array();
        foreach($profiling as $val){
            $user = get_data('tbl_user', [
                'where' => [
                    'id' => $val['mr']
                ]
            ])->row_array();
            $thd = get_data('history_organogram_detail', [
                'where' => [
                    'n_mr' => $user['username']
                ],
                'sort_by' => 'tanggal',
                'sort' => 'desc'
            ])->row_array();
            update_data('profiling', [
                'mr' => $thd['n_mr'],
                'am' => $thd['n_am'],
                'rm' => $thd['n_rm'],
                'asdir' => $thd['n_asdir'],
                'bud' => $thd['n_bud'],
            ], 'id', $val['id']);
            
        }

        render(['status'=>'done'],'json');
    }

    function sync_pernamaan_profiling(){

        die('DEPRECATED');

        ini_set('memory_limit',-1);
        ini_set('max_execution_time',-1);
        $profiling = get_data('profiling')->result_array();
        foreach($profiling as $val){
            $dokter = get_data('dokter', 'id', $val['dokter'])->row_array();
            $val['nama_dokter'] = $dokter['nama'];
            $spesialsit = get_data('spesialist', 'id', $dokter['spesialist'])->row_array();
            if(count($spesialsit) > 0){
                $val['nama_spesialist'] = $spesialsit['nama'];
            }
            $produk = get_data('produk','kode',$val['produk'])->row_array();
            if(!empty($produk) > 0){
                $val['nama_produk'] = $produk['nama'];
            }
            $produk_subgrup = get_data('produk_subgrup','kode',$val['produk_subgrup'])->row_array();
            if(count($produk_subgrup) > 0){
                $val['nama_produk_subgrup'] = $produk_subgrup['nama'];
            }
            $produk_grup = get_data('produk_grup','kode',$val['produk_grup'])->row_array();
            $val['nama_produk_grup'] = $produk_grup['nama'];
            $outlet = get_data('outlet', 'id',$val['outlet'])->row_array();
            if(!empty($outlet) > 0){
                $val['nama_outlet'] = $outlet['nama'];
            }
            update_data('profiling', $val, 'id', $val['id']);
        }
        render(['status' => true],'json');
    }

    function name2code(){
        $data = get_data('key_message')->result_array();
        foreach($data as $val){
            $produk = get_data('produk_grup','nama',$val['produk_grup'])->row_array();
            update_data('key_message', ['produk_grup' => $produk['kode']], 'id', $val['id']);
        }
    }

    function sync_profiling(){
        die;
        ini_set('memory_limit',-1);
        ini_set('max_execution_time',-1);

        $this->load->helper('generate_trx_table');
        for($i=2022;$i>=2012;$i--){
            for($j=3;$j>=1;$j--){
                if($this->db->table_exists('trxprof_'.$i.'_'.$j)){
                    $this->db->query('drop table trxprof_'.$i.'_'.$j);
                }
                init_table_prof($j, $i);
            }
        }

        $profiling = get_data('tt_profiling')->result_array();
        foreach($profiling as $val){
            $cycle = $val['cycle'];
            $tahun = $val['tahun'];
            if(empty($cycle) || empty($tahun) || empty($val['id_tm_medrep'])){
                continue;
            }
            switch($val['status_approval']){
                case 3:
                    $status = 1;
                break;
                case 2:
                    $status = 3;
                break;
                case 1:
                    $status = 2;
                break;
                default:
                    $status = 1;
                break;
            }
            $data = [
                'id'                => $val['id_tt_profiling'],
                'outlet'            => $val['id_tbl_m_outlet_oi'],
                'dokter'            => $val['id_tm_doctor'],
                'produk_grup'       => $val['id_tbl_m_produk_group'],
                'price'             => $val['id_tbl_m_pricelist_detail'],
                'channel_outlet'    => $val['channel_outlet'],
                'tipe_pasien'       => $val['patient'],
                'jumlah_pasien'     => $val['jumlah_pasien'],
                'indikasi_1'        => $val['value_indicator_1'],
                'indikasi_2'        => $val['value_indicator_2'],
                'indikasi_3'        => $val['value_indicator_3'],
                'indikasi_4'        => $val['value_indicator_4'],
                'indikasi_5'        => $val['value_indicator_5'],
                'potensi_tablet'    => $val['potensi_tablet'],
                'potensi_value'     => $val['potensi_value'],
                'status'            => $status,
                'apprv_at'          => $val['date_approval'],
                'alasan_not_approve'=> $val['alasan_not_approved'],
                'cycle'             => $val['cycle'],
                'tahun'             => $val['tahun'],
            ];

            $user = get_data('tbl_user','id',$val['id_tm_medrep'])->row_array();
            $hod = get_data('history_organogram_detail',[
                'where' => [
                    'n_mr' => $user['username']
                ],
                'sort_by' => 'tanggal',
                'sort' => 'desc'
            ])->row_array();
            // if(empty($hod))
            //     continue;
            $data['mr'] = $hod['n_mr'];
            $data['am'] = $hod['n_am'];
            $data['rm'] = $hod['n_rm'];
            $data['asdir'] = $hod['n_asdir'];
            $data['nsm'] = $hod['n_nsm'];
            $data['bud'] = $hod['n_bud'];

            $dokter = get_data('dokter', 'id', $val['id_tm_doctor'])->row_array();
            if(empty($dokter))
                continue;
            $data['nama_dokter'] = $dokter['nama'];

            $spesialist = get_data('spesialist', 'id', $dokter['spesialist'])->row_array();
            $data['spesialist'] = $spesialist['id'];
            $data['nama_spesialist'] = $spesialist['nama'];

            $produk_grup = get_data('produk_grup', 'kode', $val['id_tbl_m_produk_group'])->row_array();
            $data['nama_produk_grup'] = $produk_grup['nama'];

            $outlet = get_data('outlet', 'id', $val['id_tbl_m_outlet_oi'])->row_array();
            if(!empty($outlet))
                $data['nama_outlet'] = $outlet['nama'];

            insert_data('trxprof_'.$tahun.'_'.$cycle, $data);
            
        }
    }

    function sync_visit_plan(){
        die;
        ini_set('memory_limit',-1);
        ini_set('max_execution_time',-1);

        $this->load->helper('generate_trx_table');
        for($i=2022;$i>=2018;$i--){
            for($j=12;$j>=1;$j--){
                // if($this->db->table_exists('trxvisit_'.$i.'_'.$j)){
                //     $this->db->query('drop table trxvisit_'.$i.'_'.$j);
                // }
                // if($this->db->table_exists('trxvisit_'.sprintf('%02d', $i).'_'.$j)){
                    // $this->db->query('drop table trxvisit_'.sprintf('%02d', $i).'_'.$j);
                // }
                if($this->db->table_exists('trxvisit_'.$i.'_'.sprintf('%02d', $j))){
                    $this->db->query('drop table trxvisit_'.$i.'_'.sprintf('%02d', $j));
                    init_table_visit_plan($i,sprintf('%02d', $j));
                }
            }
        }

        $profiling = get_data('tt_visit_plan')->result_array();
        foreach($profiling as $val){
            $bulan = $val['month'];
            $tahun = $val['year'];
            if(empty($bulan) || empty($tahun) || empty($val['id_tt_profiling'])){
                continue;
            }
            switch($val['status_approval']){
                case 3:
                    $status = 2;
                break;
                case 2:
                    $status = 4;
                break;
                case 1:
                    $status = 3;
                break;
                default:
                    $status = 2;
                break;
            }

            if(in_array($bulan,['1','2','3','4'])){
                $cycle = 1;
            } else if(in_array($bulan,['5','6','7','8'])){
                $cycle = 2;
            } else {
                $cycle = 3;
            }

            $prof = get_data('trxprof_'.$tahun.'_'.$cycle, 'id', $val['id_tt_profiling'])->row_array();
            if(empty($prof)) continue;

            $data = [
                'id'                => $val['id_tt_visit_plan'],
                'profiling'         => $val['id_tt_profiling'],
                'outlet'            => $prof['outlet'],
                'dokter'            => $prof['dokter'],
                'mr'                => $prof['mr'],
                'am'                => $prof['am'],
                'rm'                => $prof['rm'],
                'asdir'             => $prof['asdir'],
                'nsm'               => $prof['nsm'],
                'bud'               => $prof['bud'],
                'week1'             => $val['wk1'],
                'week2'             => $val['wk2'],
                'week3'             => $val['wk3'],
                'week4'             => $val['wk4'],
                'week5'             => $val['wk5'],
                'week6'             => $val['wk6'],
                'standard_call'     => $val['standard_call'],
                'plan_call'         => $val['plan_call_by_outlet'],
                'dokter'            => $prof['dokter'],
                'nama_dokter'       => $prof['nama_dokter'],
                'nama_spesialist'   => $prof['nama_spesialist'],
                'nama_outlet'       => $prof['nama_outlet'],
                'produk_grup'       => $prof['produk_grup'],
                'nama_produk_grup'  => $prof['nama_produk_grup'],
                'spesialist'        => $prof['spesialist'],
                'nama_branch'       => $prof['nama_branch'],
                'appvr_at'          => $val['date_approval'],
                'bulan'             => $val['month'],
                'tahun'             => $val['year'],
                'status'            => $status,
                'marketing_program' => $val['id_tm_marketing_program'] ? $val['id_tm_marketing_program'] : NULL,
                'marketing_aktifitas' => $val['id_tm_marketing_aktifitas'],
                'alasan_not_approve'=> $val['alasan_not_approved'],
                'cat'               => $val['visit_plan_date']
            ];

            insert_data('trxvisit_'.$tahun.'_'.sprintf('%02d', $bulan), $data);
            
        }
    }

    function change_to_default_pass(){
        die('Non aktif');
        ini_set('memory_limit','-1');
        ini_set('max_execution_time','-1');
        $user = get_data('tbl_user',[
            'where' => [
                'id !=' => 1 
            ]
        ])->result_array();
        foreach($user as $val){
            update_data('tbl_user', [
                'password' => password_hash(md5('otsuka'),PASSWORD_DEFAULT),
            ], 'id', $val['id']);
        }
    }

    function sync_nama_outlet_profiling(){
        die('Non Active');
        ini_set('memory_limit','-1');
        ini_set('max_execution_time','-1');
        $this->load->helper('generate_trx_table');
        for($i=2022;$i>=20;$i--){
            for($j=12;$j>=1;$j--){
                if($this->db->table_exists('trxvisit_'.$i.'_'.sprintf('%02d', $j))){
                    $prof = get_data('trxvisit_'.$i.'_'.sprintf('%02d', $j))->result_array();
                    foreach($prof as $val){
                        if($val['outlet'] != NULL || $val['outlet'] != 0){
                            $outlet = get_data('outlet', 'id', $val['outlet'])->row_array();
                            if($outlet){
                                update_data('trxvisit_'.$i.'_'.sprintf('%02d', $j), [
                                    'nama_outlet' => $outlet['nama']
                                ],'id', $val['id']);
                            }
                        }
                    }
                }
            }
        }

    }

    function generate_raw_data($tahun = '2022', $bulan = '12'){

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);

		$bulan = sprintf("%02d", $bulan);
		if(in_array($bulan, ['01','02','03','04'])){
			$cycle = 1;
		} else if(in_array($bulan, ['05','06','07','08'])){
			$cycle = 2;
		} else if(in_array($bulan, ['09','10','11','12'])){
			$cycle = 3;
		}
		$produk_grup = get_data('produk_grup', [
			'where' => [
				'is_active' => 1,
				//			'kode'		=> 'EH'
			]
		])->result_array();
		
		foreach($produk_grup as $produk_grup){
			$data = [];
			$profiling = get_data('trxprof_'.$tahun.'_'.$cycle.' a', [
				'select' => 'a.id, a.mr, a.dokter, tbl_user.nama as nama_mr, a.nama_dokter, a.nama_outlet, a.nama_spesialist, b.val_indikasi_1, b.val_indikasi_2, b.val_indikasi_3, b.val_indikasi_4, b.val_indikasi_5, b.val_indikasi_6, b.val_indikasi_7, b.val_indikasi_8, b.val_indikasi_9, b.val_indikasi_10,
				value_1 as value_1, 
				value_2 as value_2, 
				value_3 as value_3, 
				value_4 as value_4, 
				value_5 as value_5, 
				value_6 as value_6, 
				value_7 as value_7, 
				value_8 as value_8, 
				value_9 as value_9, 
				value_10 as value_10,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_1 end) as value_abi_1,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_2 end) as value_abi_2,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_3 end) as value_abi_3,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_4 end) as value_abi_4,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_5 end) as value_abi_5,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_6 end) as value_abi_6,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_7 end) as value_abi_7,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_8 end) as value_abi_8,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_9 end) as value_abi_9,
				sum(case when d.produk in ("8","9","10","11","12","13","26") then value_10 end) as value_abi_10,
				sum(case when d.produk in ("145","146","147","148") then value_1 end) as value_rex_1,
				sum(case when d.produk in ("137","138") then value_1 end) as value_maintena_1,
				sum(case when d.produk in ("137","138") then value_2 end) as value_maintena_2',
				'join' => [
					'trxprof_indikasi_'.$tahun.'_'.$cycle.' b on a.id = b.profiling type left',
					'trxdact_'.$tahun.'_'.$bulan.' c on a.dokter = c.dokter type left',
					'trxdact_sku_'.$tahun.'_'.$bulan.' d on d.data_sales = c.id type left',
					'tbl_user on tbl_user.username = a.mr',
				],
				'where' => [
					'b.bulan' => $bulan,
					'a.produk_grup' => $produk_grup['kode'],
					//'a.mr' => ['02553','02517','03293']
				],
				'group_by' => 'a.dokter,a.mr',
			])->result_array(); 
			//debug($profiling); die;
			$produk = get_data('produk', [
				'select' => 'produk.*',
				'join' => [
					'produk_subgrup on produk_subgrup.kode = produk.kode_subgrup',
				],
				'where' => [
					'kode_grup' => $produk_grup['kode'],
					'produk.is_active' => 1,
				],
			])->result_array();
			foreach($profiling as $val) {

				$call = get_data('trxvisit_'.$tahun.'_'.$bulan.' a', [
					'select' => '(if(a.status = 3, a.plan_call, 0)) as plan_call, 
						(if(a.status = 3 and b.status = 2, count(b.id), 0)) as actual_call,
						(if(a.plan_call > 0 and a.status = 3, 1, 0)) as plan_dokter_coverage,
						(if(b.status = 2, 1, 0)) as actual_dokter_coverage,
						if(a.plan_call = 0, 0, (if(a.status = 3, 1, 0))) as plan_percent_coverage,
						if(a.plan_call = 0, 0, (if(a.plan_call <= count(b.id), 1, 0))) as actual_percent_coverage,
						(if(b.call_type = 1, count(b.id), 0)) as call_type_a,
						(if(b.call_type = 2, count(b.id), 0)) as call_type_b,
						(if(b.call_type = 3, count(b.id), 0)) as call_type_c,',
					'join' => [
						'trxdfr_'.$tahun.'_'.$bulan.' b on b.visit_plan = a.id type left'
					],
					'where' => [
						'a.mr' => $val['mr'],
						'a.dokter' => $val['dokter'],
					],
					'group_by' => 'a.dokter',
					'sort_by' => 'a.nama_dokter',
					'sort' => 'ASC',
				])->row_array();

				if(!$call){
					$call = [
						'plan_call' => 0,
						'actual_call' => 0,
						'plan_dokter_coverage' => 0,
						'actual_dokter_coverage' => 0,
						'plan_percent_coverage' => 0,
						'actual_percent_coverage' => 0,
						'call_type_a' => 0,
						'call_type_b' => 0,
						'call_type_c' => 0,
					];
				}

				$sub_call_type_a = get_data('trxdfr_'.$tahun.'_'.$bulan.' a', [
					'select' => 'a.sub_call_type',
					'where' => [
						'a.dokter' => $val['dokter'],
						'a.mr' => $val['mr'],
						'a.call_type' => 1,
					],
				])->result_array();
				
				if(!$sub_call_type_a){
					$sub_call_type_a = [
						'sub_call_type' => [],
					];
				} else {
					$sub_call_type_a = [
						'sub_call_type' => array_column($sub_call_type_a, 'sub_call_type'),
					];
				}

				$sub_call_type_b = get_data('trxdfr_'.$tahun.'_'.$bulan.' a', [
					'select' => 'a.sub_call_type',
					'where' => [
						'a.dokter' => $val['dokter'],
						'a.mr' => $val['mr'],
						'a.call_type' => 2,
					],
				])->result_array();

				if(!$sub_call_type_b){
					$sub_call_type_b = [
						'sub_call_type' => [],
					];
				} else {
					$sub_call_type_b = [
						'sub_call_type' => array_column($sub_call_type_b, 'sub_call_type'),
					];
				}

				$sub_call_type_c = get_data('trxdfr_'.$tahun.'_'.$bulan.' a', [
					'select' => 'a.sub_call_type',
					'where' => [
						'a.dokter' => $val['dokter'],
						'a.mr' => $val['mr'],
						'a.call_type' => 3,
					],
				])->result_array();

				if(!$sub_call_type_c){
					$sub_call_type_c = [
						'sub_call_type' => [],
					];
				} else {
					$sub_call_type_c = [
						'sub_call_type' => array_column($sub_call_type_c, 'sub_call_type'),
					];
				}
				
				$jumlah_potensi = $val['val_indikasi_1'] + $val['val_indikasi_2'] + $val['val_indikasi_3'] + $val['val_indikasi_4'] + $val['val_indikasi_5'] + $val['val_indikasi_6'] + $val['val_indikasi_7'] + $val['val_indikasi_8'] + $val['val_indikasi_9'] + $val['val_indikasi_10'];
				$total_pasien = $val['value_1'] + $val['value_2'] + $val['value_3'] + $val['value_4'] + $val['value_5'] + $val['value_6'] + $val['value_7'] + $val['value_8'] + $val['value_9'] + $val['value_10'];
				$tmp_data = [
					'profiling' =>  $val['id'],
					'mr' => $val['mr'],
					'nama_mr' => $val['nama_mr'],
					'produk_grup' => $produk_grup['kode'],
					'nama_dokter' => $val['nama_dokter'],
					'nama_outlet' => $val['nama_outlet'] ? $val['nama_outlet'] : 'Regular',
					'nama_spesialist' => strtoupper($val['nama_spesialist']),
					'indikasi_1' => $val['val_indikasi_1'],
					'indikasi_2' => $val['val_indikasi_2'],
					'indikasi_3' => $val['val_indikasi_3'],
					'indikasi_4' => $val['val_indikasi_4'],
					'indikasi_5' => $val['val_indikasi_5'],
					'indikasi_6' => $val['val_indikasi_6'],
					'indikasi_7' => $val['val_indikasi_7'],
					'indikasi_8' => $val['val_indikasi_8'],
					'indikasi_9' => $val['val_indikasi_9'],
					'indikasi_10' => $val['val_indikasi_10'],
					'total_potensi' => $jumlah_potensi,
					'pasien_1' => $val['value_1'],
					'pasien_2' => $val['value_2'],
					'pasien_3' => $val['value_3'],
					'pasien_4' => $val['value_4'],
					'pasien_5' => $val['value_5'],
					'pasien_6' => $val['value_6'],
					'pasien_7' => $val['value_7'],
					'pasien_8' => $val['value_8'],
					'pasien_9' => $val['value_9'],
					'pasien_10' => $val['value_10'],
					'total_pasien' => $total_pasien,
					'plan_call' => $call['plan_call'] ? $call['plan_call'] : 0,
					'actual_call' => $call['actual_call'] ? $call['actual_call'] : 0,
					'percent_call' => $call['plan_call'] && $call['actual_call'] ? ($call['actual_call'] / $call['plan_call']) * 100 : 0,
					'plan_dokter_coverage' => $call['plan_dokter_coverage'] ? $call['plan_dokter_coverage'] : 0,
					'actual_dokter_coverage' => $call['actual_dokter_coverage'] ? $call['actual_dokter_coverage'] : 0,
					'percent_dokter_coverage' => $call['plan_dokter_coverage'] && $call['actual_dokter_coverage'] ? ($call['actual_dokter_coverage'] / $call['plan_dokter_coverage']) * 100 : 0,
					'plan_percent_coverage' => $call['plan_percent_coverage'] ? $call['plan_percent_coverage'] : 0,
					'actual_percent_coverage' => $call['actual_percent_coverage'] ? $call['actual_percent_coverage'] : 0,
					'percent_percent_coverage' => $call['plan_percent_coverage'] && $call['actual_percent_coverage'] ? ($call['actual_percent_coverage'] / $call['plan_percent_coverage']) * 100 : 0,
					'use_confirm' => get_status_dokter($produk_grup['kode'], $total_pasien),
					'customer_matrix' => get_customer_matrix($produk_grup['kode'], 
						get_status_dokter($produk_grup['kode'], $total_pasien), get_kriteria_potensi($produk_grup['kode'], $jumlah_potensi)
					),
					'call_type_a' => $call['call_type_a'] ? $call['call_type_a'] : 0,
					'call_type_b' => $call['call_type_b'] ? $call['call_type_b'] : 0,
					'call_type_c' => $call['call_type_c'] ? $call['call_type_c'] : 0,
					'sub_call_type_a' => json_encode($sub_call_type_a['sub_call_type']),
					'sub_call_type_b' => json_encode($sub_call_type_b['sub_call_type']),
					'sub_call_type_c' => json_encode($sub_call_type_c['sub_call_type']),				
				];
				if($produk_grup['kode'] == 'EH'){
					$tmp_data['pasien_1'] = $val['value_abi_1'] ? $val['value_abi_1'] : 0;
					$tmp_data['pasien_2'] = $val['value_abi_2'] ? $val['value_abi_2'] : 0;
					$tmp_data['pasien_3'] = $val['value_abi_3'] ? $val['value_abi_3'] : 0;
					$tmp_data['pasien_4'] = $val['value_abi_4'] ? $val['value_abi_4'] : 0;
					$tmp_data['pasien_5'] = $val['value_abi_5'] ? $val['value_abi_5'] : 0;
					$tmp_data['pasien_6'] = $val['value_abi_6'] ? $val['value_abi_6'] : 0;
					$tmp_data['pasien_7'] = $val['value_abi_7'] ? $val['value_abi_7'] : 0;
					$tmp_data['pasien_8'] = $val['value_abi_8'] ? $val['value_abi_8'] : 0;
					$tmp_data['pasien_9'] = $val['value_abi_9'] ? $val['value_abi_9'] : 0;
					$tmp_data['pasien_10'] = $val['value_abi_10'] ? $val['value_abi_10'] : 0;
					
					

					$tmp_data['pasien_maintena_1'] 	= $val['value_maintena_1'] ? $val['value_maintena_1'] : 0;
					$tmp_data['pasien_maintena_2'] 	= $val['value_maintena_2'] ? $val['value_maintena_2'] : 0;
					$tmp_data['pasien_rexulti_1'] 	= $val['value_rex_1'] ? $val['value_rex_1'] : 0;

					
					$tmp_data['use_confirm_maintena'] = get_status_dokter('MT', ($val['value_maintena_1'] + $val['value_maintena_2']));
					$tmp_data['use_confirm_rexulti'] = get_status_dokter('EO', ($val['value_rex_1']));
					$tmp_data['customer_matrix_maintena'] = get_customer_matrix('MT', 
						get_status_dokter('MT', ($val['value_maintena_1'] + $val['value_maintena_2'])), get_kriteria_potensi('MT', ($val['val_indikasi_1'] + $val['val_indikasi_2']))
					);
					$tmp_data['customer_matrix_rexulti'] = get_customer_matrix('EO', 
						get_status_dokter('EO', ($val['value_rex_1'])), get_kriteria_potensi('EO', ($val['val_indikasi_1']))
					);
				}
				foreach($produk as $pval){
					$data_sales = get_data('trxdact_'.$tahun.'_'.$bulan.' a', [
						'select' => '(number_of_unit) as total_sales, b.price',
						'join' => [
							'trxdact_sku_'.$tahun.'_'.$bulan.' b on a.id = b.data_sales',
						],
						'where' => [
							'a.mr' => $val['mr'],
							'a.dokter' => $val['dokter'],
							'produk' => $pval['id'],
						]
					])->row_array();
					$tmp_data[$pval['id'].'_produk'] = $data_sales['total_sales'] ? $data_sales['total_sales'] : 0;
					$tmp_data[$pval['id'].'_price'] = $data_sales['price'] ? $data_sales['price'] : 0;
				}
				$data[] = $tmp_data;
			}
			$this->load->helper('generate_trx_table');
			if($this->db->table_exists('raw_data_'.$produk_grup['kode'].'_'.$tahun.'_'.$bulan)){
				$this->db->query('drop table raw_data_'.$produk_grup['kode'].'_'.$tahun.'_'.$bulan);
			}
			init_table_raw_data($tahun, $bulan, $produk_grup['kode']);
			insert_batch('raw_data_'.$produk_grup['kode'].'_'.$tahun.'_'.$bulan, $data);
		}
	}

    function copy_profiling($nip, $to_nip, $cycle){

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);

		$table = 'trxprof_'.date('Y').'_'.$cycle;
		$profiling = get_data($table, [
			'where' => [
				'mr' => $nip
			]
		])->result_array();
		$hod = get_data('history_organogram_detail a', [
			'select' => 'a.*',
			'join' => [
				'history_organogram b on a.id_history_organogram = b.id'
			],
			'where' => [
				'b.tanggal_end != ' => '0000-00-00',
			]
		])->row_array();
		foreach($profiling as $k => $v){

			$id_lama = $v['id'];

			unset($v['id']);
			unset($v['apprv_at']);
			unset($v['sub_spesialist']);
			unset($v['nama_subspesialist']);

			$v['mr'] 	= $to_nip;
			$v['am'] 	= $hod['n_am'];
			$v['rm'] 	= $hod['n_rm'];
			$v['asdir'] = $hod['n_asdir'];
			$v['bud'] 	= $hod['n_bud'];
			$v['nsm'] 	= $hod['n_nsm'];
			$v['cat'] 	= date('Y-m-d H:i:s');

			if($cycle == 1){
				unset($v['indikasi_6']);
				unset($v['indikasi_7']);
				unset($v['indikasi_8']);
				unset($v['indikasi_9']);
				unset($v['indikasi_10']);
			}

			$v['status'] = 1;
			$id = insert_data('trxprof_'.date('Y').'_'.active_cycle(), $v);
			$pot_profiling = get_data('trxprof_indikasi_'.date('Y').'_'.$cycle, [
				'where' => [
					'profiling' => $id_lama
				]
			])->result_array();

			foreach($pot_profiling as $pk => $pv){
				$pv['profiling'] = $id;
				insert_data('trxprof_indikasi_'.date('Y').'_'.active_cycle(), $pv);
			}

		}

		render(['status'=>'success', 'message'=>'Data berhasil di copy'],'json');
	}
}