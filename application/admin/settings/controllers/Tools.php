<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tools extends BE_Controller {

	private $id_tools = '';

	function __construct() {
		parent::__construct();
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', -1);
		$this->id_tools = post('id');
	}

	function __destruct()
	{
		update_data('tbl_tools', [
			'last_updated' => date('Y-m-d H:i:s'),
			'execution_time' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]
		],'id', $this->id_tools);
	}

	function index() {
		render();
	}

	function delete_visit_plan_prof_not_apprv(){
		$bulan = '09';
		$tahun = '2022';

		$profiling 	= get_data('trxprof_'.$tahun.'_'.active_cycle())->result_array();
		foreach($profiling as $v){
			if($v['status'] == 3 || $v['status'] == ''){
				delete_data('trxvisit_'.$tahun.'_'.$bulan, 'profiling', $v['id']);
			}
		}
	}

	function data() {
		$config = [
			'button' => [
				button_serverside('btn-sky','btn-process',['fa-paper-plane','Proses','true'], 'act_process', [
					'is_active' => 1
				])
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_tools','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_tools',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_tools','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['nama' => 'nama','url' => 'url'];
		$config[] = [
			'title' => 'template_import_tools',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['nama','url'];
		$this->load->library('simpleexcel');
		$this->simpleexcel->define_column($col);
		$jml = $this->simpleexcel->read($file);
		$c = 0;
		foreach($jml as $i => $k) {
			if($i==0) {
				for($j = 2; $j <= $k; $j++) {
					$data = $this->simpleexcel->parsing($i,$j);
					$data['create_at'] = date('Y-m-d H:i:s');
					$data['create_by'] = user('nama');
					$save = insert_data('tbl_tools',$data);
					if($save) $c++;
				}
			}
		}
		$response = [
			'status' => 'success',
			'message' => $c.' '.lang('data_berhasil_disimpan').'.'
		];
		@unlink($file);
		render($response,'json');
	}

	function export() {
		ini_set('memory_limit', '-1');
		$arr = ['nama' => 'Nama','url' => 'Url'];
		$data = get_data('tbl_tools')->result_array();
		$config = [
			'title' => 'data_tools',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	// =========================================================================================================================== 

	function process(){
		$id = post('id');
		$data = get_data('tbl_tools','id',$id)->row_array();
		if($data){
			$url = $data['url'];
			render($this->$url(), 'json');
		}
	}

	function sync_dokter_lama(){
		switch_database('sfe');
		$dokter = get_data('tm_doctor')->result_array();
		switch_database();
		delete_data('dokter');
		foreach($dokter as $val){
			$data = [
				'id'			=> $val['id_tm_doctor'],
				'nama' 			=> $val['fullname'],
				'tanggal_lahir'	=> $val['birthdate'],
				'spesialist'	=> $val['id_tm_specialist'],
				'subspesialist'	=> $val['id_tm_subspecialist'],
				'is_active'		=> $val['is_delete'] == 1 ? 0 : 1
			];
			insert_data('dokter', $data);
		}
	}

	function sync_outlet_siap(){
		switch_database('siap');
		$outlet = get_data('tbl_m_outlet_oi')->result_array();
		switch_database();
		delete_data('outlet');
		foreach($outlet as $val){
			$data = [
				'id'		=> $val['id'],
				'nama'		=> $val['outlet'],
				'alamat'	=> $val['outlet'],
				'branch'	=> $val['id_branch_oi'],
				'is_active'	=> $val['status']
			];
			insert_data('outlet', $data);
		}
	}

	function sync_users_siap(){
		switch_database('siap');
		$users = get_data('tbl_user')->result_array();
		switch_database();
		foreach($users as $val){
			$user = get_data('tbl_user', ['id' => $val['id']])->row_array();
			if($user){
				$data = [
					'id_group'		=> $val['id_group'],
					'kode'			=> $val['username'],
					'nama'			=> $val['nama'],
					'username'		=> $val['username'],
					'email'			=> $val['email'],
					'telepon'		=> $val['telepon']
				];
				update_data('tbl_user', $data, 'id', $val['id']);
			} else {
				$data = [
					'id'			=> $val['id'],
					'id_group'		=> $val['id_group'],
					'kode'			=> $val['username'],
					'password'		=> password_hash(md5('otsuka'), PASSWORD_DEFAULT),
					'nama'			=> $val['nama'],
					'username'		=> $val['username'],
					'email'			=> $val['email'],
					'telepon'		=> $val['telepon']
				];
				insert_data('tbl_user', $data);
			}
		}
	}

	function sync_organogram_siap(){
		switch_database('siap');
		$organogram = get_data('tbl_organogram')->result_array();
		$organogram_pegawai = get_data('tbl_organogram_pegawai')->result_array();
		$history_organogram = get_data('tbl_history_organogram')->result_array();
		$history_organogram_detail = get_data('tbl_history_organogram_detail')->result_array();
		$history_organogram_list = get_data('tbl_history_organogram_list')->result_array();
		$history_organogram_pegawai = get_data('tbl_history_organogram_pegawai')->result_array();
		switch_database();
		delete_data('organogram');
		delete_data('organogram_pegawai');	
		delete_data('history_organogram');
		delete_data('history_organogram_detail');
		delete_data('history_organogram_list');
		delete_data('history_organogram_pegawai');
		foreach($organogram as $val){
			insert_data('organogram', $val);
		}
		foreach($organogram_pegawai as $val){
			insert_data('organogram_pegawai', $val);
		}
		foreach($history_organogram as $val){
			insert_data('history_organogram', $val);
		}
		foreach($history_organogram_detail as $val){
			insert_data('history_organogram_detail', $val);
		}
		foreach($history_organogram_list as $val){
			insert_data('history_organogram_list', $val);
		}
		foreach($history_organogram_pegawai as $val){
			insert_data('history_organogram_pegawai', $val);
		}


		$tahun = date('Y');
		$trxprof = 'trxprof_'.$tahun.'_'.active_cycle();
		// $trxvisit = 'trxvisit_'.$tahun.'_'.active_cycle();
		// $trxdact = 'trxdact_'.$tahun.'_'.
		// foreach($trxprof as $k => $v){
		$hod = get_data('history_organogram_detail hod', [
			'select' => 'hod.*',
			'join' => [
				'history_organogram ho on ho.id = hod.id_organogram',
			],
			'where' => [
				'ho.tanggal_end' => '0000-00-00'
			],
			'group_by' => 'n_mr'
		])->result_array();

		foreach($hod as $k => $v)
		
			// Profiiling
			update_data('trxprof_'.$tahun.'_'.active_cycle(), [
				'am' => $v['n_am'],
				'rm' => $v['n_rm'], //  KOSONG 
				'nsm' => $v['n_nsm'],
			], 'mr', $v['n_mr']);

			// Merubah struktur visit plan
			for($i=1;$i<=12;$i++){
				if(table_exists('trxvisit_'.$tahun.'_'.sprintf('%02d',$i))){
					update_data('trxvisit_'.$tahun.'_'.sprintf('%02d',$i), [
						'am' => $v['n_am'],
						'rm' => $v['n_rm'],
						'nsm' => $v['n_nsm'],
					], 'mr', $v['n_mr']);
				}
			}
			
			// Merubah struktur DFR
			for($i=1;$i<=12;$i++){
				if(table_exists('trxdfr_'.$tahun.'_'.sprintf('%02d',$i))){
					update_data('trxdfr_'.$tahun.'_'.sprintf('%02d',$i), [
						'am' => $v['n_am'],
						'rm' => $v['n_rm'],
						'nsm' => $v['n_nsm'],
					], 'mr', $v['n_mr']);
				}
			}

			// Merubah struktur Data Actual by Customer
			for($i=1;$i<=12;$i++){
				if(table_exists('trxdact_'.$tahun.'_'.sprintf('%02d',$i))){
					update_data('trxdact_'.$tahun.'_'.sprintf('%02d',$i), [
						'am' => $v['n_am'],
						'rm' => $v['n_rm'],
						'nsm' => $v['n_nsm'],
					], 'mr', $v['n_mr']);
				}
			}
		}
		
		
	}

	function sync_profiling_lama(){

        $this->load->helper('generate_trx_table');
        for($i=2022;$i>=2022;$i--){
            for($j=1;$j>=1;$j--){
                if($this->db->table_exists('trxprof_'.$i.'_'.$j)){
                    $this->db->query('drop table trxprof_'.$i.'_'.$j);
                }
                init_table_prof($j, $i);
            }
        }
		switch_database('sfe');
        $profiling = get_data('tt_profiling', [
			'where' => [
				'cycle' => 1, 'tahun' => '2022',
			],
			'where_in' => [
				// 'id_tm_medrep' => ['3365'],
			] 	
		])->result_array();
		// 3129,'3319','3325'
		// debug($profiling); die;
		switch_database();
		$ids = 434634;
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
                // 'id'                => $val['id_tt_profiling'],
				'id'				=> $ids++,
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

	function sync_visit_plan_lama(){
		$this->load->helper('generate_trx_table');
        for($i=2022;$i>=2022;$i--){
            for($j=2;$j>=2;$j--){
                if($this->db->table_exists('trxvisit_'.$i.'_'.sprintf('%02d', $j))){
                    $this->db->query('drop table trxvisit_'.$i.'_'.sprintf('%02d', $j));
                    init_table_visit_plan($i,sprintf('%02d', $j));
                }
            }
        }
		init_table_visit_plan('2022','02');
		switch_database('sfe');
        $profiling = get_data('tt_visit_plan', [
			'where' => [
				'month' => '02',
				'year' => '2022'
			]
		])->result_array();
		switch_database();
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

	function delete_visit_plan_tidak_sesuai(){
		$tahun = date('Y');
		$bulan = date('m');	
		if(table_exists('trxvisit_'.$tahun.'_'.$bulan)){
			if(in_array(date('m'), ['01','02','03','04'])){
				$cycle = 1;
			} elseif (in_array(date('m'), ['05','06','07','08'])){
				$cycle = 2;
			} else {
				$cycle = 3;
			}
			$visit_plan = get_data('trxvisit_'.$tahun.'_'.$bulan.' a', [
				'select' => 'b.id',
				'join' => [
					'trxprof_'.$tahun.'_'.$cycle.' b on a.profiling = b.id'
				],
				'where' => [
					'b.status' => 3,
					// 'a.mr' => '02193'
				]
			])->result_array();
			foreach($visit_plan as $val){
				delete_data('trxvisit_'.$tahun.'_'.$bulan, 'id', $val['id']);
			}
		}
	}

	function sync_profiling_indikasi_perbulan(){
		$this->load->helper('generate_trx_table');
		for($tahun=2022;$tahun>=2022;$tahun--){
			for($cycle=1;$cycle<=1;$cycle++){

				if($cycle == 1){
					$bulan = [1,2,3,4];
				} else if($cycle == 2){
					$bulan = [5,6,7,8];
				} else {
					$bulan = [9,10,11,12];
				}

				if(!table_exists('trxprof_indikasi_'.$tahun.'_'.$cycle)){
					init_table_prof_indikasi($cycle, $tahun);
				}

				if(!table_exists('trxprof_'.$tahun.'_'.$cycle)){
					init_table_prof($cycle, $tahun);
				}
				$profiling = get_data('trxprof_'.$tahun.'_'.$cycle)->result_array();
				foreach($bulan as $bulan){
					foreach($profiling as $val){
						insert_data('trxprof_indikasi_'.$tahun.'_'.$cycle, [
							'profiling' => $val['id'],
							'indikasi_1' => $val['indikasi_1'] ? $val['indikasi_1'] : 0,
							'indikasi_2' => $val['indikasi_2'] ? $val['indikasi_2'] : 0,
							'indikasi_3' => $val['indikasi_3'] ? $val['indikasi_3'] : 0,
							'indikasi_4' => $val['indikasi_4'] ? $val['indikasi_4'] : 0,
							'indikasi_5' => $val['indikasi_5'] ? $val['indikasi_5'] : 0,
							'jumlah_pasien' => $val['jumlah_pasien'],
							'fee_patient' => $val['fee_patient'],
							'ap_original' => $val['ap_original'],
							'potensi_tablet' => intval($val['jumlah_pasien']) * ((intval($val['indikasi_1'])+intval($val['indikasi_2'])+intval($val['indikasi_3'])+intval($val['indikasi_4'])+intval($val['indikasi_5']))/100),
							'bulan' => sprintf('%02d', $bulan)
						]);
					}
				}

			}
		}
	}

	function convert_persen_jumlah_pasien_ke_biasa(){
		for($tahun=2022;$tahun>=2022;$tahun--){
			for($cycle=1;$cycle<=1;$cycle++){
				$profiling = get_data('trxprof_indikasi_'.$tahun.'_'.$cycle)->result_array();
				foreach($profiling as $val){
					update_data('trxprof_indikasi_'.$tahun.'_'.$cycle, [
						'indikasi_1' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_1'],
						'indikasi_2' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_2'],
						'indikasi_3' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_3'],
						'indikasi_4' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_4'],
						'indikasi_5' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_5'],
						'potensi_tablet' => (intval($val['indikasi_1'])+intval($val['indikasi_2'])+intval($val['indikasi_3'])+intval($val['indikasi_4'])+intval($val['indikasi_5']))
					],['profiling' => $val['profiling']]);

					update_data('trxprof_'.$tahun.'_'.$cycle, [
						'indikasi_1' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_1'],
						'indikasi_2' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_2'],
						'indikasi_3' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_3'],
						'indikasi_4' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_4'],
						'indikasi_5' => (intval($val['jumlah_pasien']) / 100) * $val['indikasi_5'],
						'potensi_tablet' => (intval($val['indikasi_1'])+intval($val['indikasi_2'])+intval($val['indikasi_3'])+intval($val['indikasi_4'])+intval($val['indikasi_5']))
					],['id' => $val['profiling']]);
				}
			}
		}
	}

	function menambah_limit_karakter_dfr(){
		for($tahun=2018;$tahun>=2022;$tahun--){
			for($cycle=1;$cycle<=3;$cycle++){

				if($cycle == 1){
					$bulan = [1,2,3,4];
				} else if($cycle == 2){
					$bulan = [5,6,7,8];
				} else {
					$bulan = [9,10,11,12];
				}

				foreach($bulan as $bulan){
					if(!table_exists('trxdfr_'.$tahun.'_'.$bulan)){
						$this->load->helper('generate_trx_table');
						init_table_dfr($tahun, $bulan);
					}
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `mr_talk` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `mr_talk2` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `mr_talk3` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `feedback_dokter` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `feedback_dokter2` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `feedback_dokter3` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `feedback_am` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `feedback_rm` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `call_object` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
					$this->db->query("ALTER TABLE `trxdfr_".$tahun."_".$bulan."` MODIFY `next_action` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
				}

			}
		}
	}

	function sync_dfr_lama(){
        ini_set('memory_limit','-1');
        ini_set('max_execution_time','-1');
        $this->load->helper('generate_trx_table');
        for($tahun=2022;$tahun>=2022;$tahun--){
            for($bulan=2;$bulan>=2;$bulan--){
                if(!table_exists('trxdfr_'.$tahun.'_'.sprintf('%02d', $bulan))){
                    init_table_dfr($tahun, sprintf('%02d', $bulan));
                }
				if(in_array($bulan, [1,2,3,4])){
					$cycle = 1;
				} else if(in_array($bulan, [5,6,7,8])){
					$cycle = 2;
				} else {
					$cycle = 3;
				}
                switch_database('sfe');
				$dfr = get_data('tt_dfr',[
					'where' => 
					[
						'year(date)' => $tahun, 'month(date)' => $bulan
					]
				])->result_array();
				switch_database('default');
				foreach($dfr as $val){
					$visit_plan = get_data('trxvisit_'.$tahun.'_'.sprintf('%02d', $bulan),'id' ,$val['id_tt_visit_plan'])->row_array();
					if($visit_plan){
						$profiling = get_data('trxprof_'.$tahun.'_'.$cycle)->row_array();
						if($profiling){
							$produk = get_data('produk', 'id', $val['id_tbl_m_produk_oi'])->row_array();
							insert_data('trxdfr_'.$tahun.'_'.sprintf('%02d', $bulan),[
								'visit_plan' => $val['id_tt_visit_plan'],
								'dokter' => $visit_plan['dokter'],
								'mr' => $visit_plan['mr'],
								'am' => $visit_plan['am'],
								'rm' => $visit_plan['rm'],
								'asdir' => $visit_plan['asdir'],
								'nsm' => $visit_plan['nsm'],
								'bud' => $visit_plan['bud'],
								'produk_grup' => $visit_plan['produk_grup'],
								'produk' => $val['id_tbl_m_produk_oi'],
								'channel_outlet' => $profiling['channel_outlet'],
								'outlet' => $profiling['outlet'],
								'nama_dokter' => $profiling['nama_dokter'],
								'nama_produk' => $produk ? $produk['nama'] : NULL,
								'nama_outlet' => $visit_plan['nama_outlet'],
								'kompetitor_diresepkan' => $val['id_tm_kompetitor_diresepkan'],
								'feedback_status' => $val['feedback_status'],
								'feedback_dokter' => $val['feedback_doctor'],
								'mr_talk' => $val['mr_talk'],
								'call_object' => $val['call_object'],
								'next_action' => $val['next_action_plan'],
								'call_type' => $val['id_tm_call_type'],
								'sub_call_type' => $val['id_tm_sub_call_type'],
								'key_message' => $val['id_tm_key_message'],
								'indikasi' => $val['indikasi_produk'],
								'penilaian' => $val['penilaian'],
								'status' => $val['flag'] == 1 ? 2 : 1,
							]);
						}
					}
				}
            }
        }
    }

	function sync_data_actual_lama(){
		for($tahun=2022;$tahun>=2022;$tahun--){
            for($bulan=2;$bulan>=2;$bulan--){
				if(!table_exists('trxdact_'.$tahun.'_'.sprintf('%02d', $bulan))){
					init_table_data_actual($tahun, sprintf('%02d', $bulan));
				}
				if(in_array($bulan, [1,2,3,4])){
					$cycle = 1;
				} else if(in_array($bulan, [5,6,7,8])){
					$cycle = 2;
				} else {
					$cycle = 3;
				}
				switch_database('sfe');
				$dact = get_data('tt_data_sales_by_product',[
					'join' => [
						'tm_datasales_indication on tm_datasales_indication.id_tt_data_sales_by_product = tt_data_sales_by_product.id_tt_data_sales_by_product type left'
					],
					'where' => 
					[
						'year' => $tahun, 'month' => sprintf('%02d', $bulan)
					]
				])->result_array();
				switch_database('default');
				foreach($dact as $val){
					$profiling = get_data('trxprof_'.$tahun.'_'.$cycle, 'id', $val['id_tt_profiling'])->row_array();
					if(!$profiling) continue;
					$jumlah_pasien_data_sales = intval($val['jumlah_pasien_indication_1'] ? $val['jumlah_pasien_indication_1'] : 0) +
									intval($val['jumlah_pasien_indication_2'] ? $val['jumlah_pasien_indication_2'] : 0) +
									intval($val['jumlah_pasien_indication_3'] ? $val['jumlah_pasien_indication_3'] : 0) + 
									intval($val['jumlah_pasien_indication_4'] ? $val['jumlah_pasien_indication_4'] : 0) +
									intval($val['jumlah_pasien_indication_5'] ? $val['jumlah_pasien_indication_5'] : 0);
					$jumlah_pasien_profiling = intval($profiling['indikasi_1'] ? $profiling['indikasi_1'] : 0) +
											intval($profiling['indikasi_2'] ? $profiling['indikasi_2'] : 0) +
											intval($profiling['indikasi_3'] ? $profiling['indikasi_3'] : 0) +
											intval($profiling['indikasi_4'] ? $profiling['indikasi_4'] : 0) +
											intval($profiling['indikasi_5'] ? $profiling['indikasi_5'] : 0);
					$kriteria_potensi = get_kriteria_potensi($profiling['produk_grup'], $jumlah_pasien_profiling);
					$status_dokter = get_status_dokter($profiling['produk_grup'], $jumlah_pasien_data_sales);
					$customer_matrix = get_customer_matrix($profiling['produk_grup'], $jumlah_pasien_data_sales, $kriteria_potensi);
					$data_sales = [
						'mr' => $profiling['mr'],
						'am' => $profiling['am'],
						'rm' => $profiling['rm'],
						'asdir' => $profiling['asdir'],
						'nsm' => $profiling['nsm'],
						'bud' => $profiling['bud'],
						'dokter' => $profiling['dokter'],
						'nama_dokter' => $profiling['nama_dokter'],
						'spesialist' => $profiling['spesialist'],
						'nama_spesialist' => $profiling['nama_spesialist'],
						'outlet' => $profiling['outlet'],
						'nama_outlet' => $profiling['nama_outlet'],
						// 'sub_spesialist' => $profiling['sub_spesialist'],
						// 'nama_sub_spesialist' => $profiling['nama_sub_spesiialist'],
						'produk_grup' => $profiling['produk_grup'],
						'nama_produk_grup' => $profiling['nama_produk_grup'],
						'customer_matrix' => $customer_matrix,
						'potensi' => $kriteria_potensi,
						'status_dokter' => $status_dokter,
						'jumlah_pasien' => $jumlah_pasien_data_sales,
						'total_value' => $val['potensi_value'],
						'hjp' => $val['hjp'],
						'bulan' => $val['month'],
						'tahun' => $val['year'],
						'unit' => $val['number_units'],
						'hjp' => $val['hjp'] ? $val['hjp'] : 0,
						'other_ap_original' => $val['other_ap_original'],
						'total_alai' => $val['total_alai'],
						'total_tlai' => $val['total_tlai'],
					];
					insert_data('trxdact_'.$tahun.'_'.sprintf("%02d", $bulan), $data_sales);
					$data_id = $this->db->insert_id();
					if($data_id){
						$produk = get_data('produk', 'id', $val['id_tbl_m_produk'])->row_array();
						if($produk){
							$data_sku = [
								'data_sales' => $data_id,
								'produk' => $val['id_tbl_m_produk_oi'],
								'nama_produk' => $produk['nama_produk'],
								'number_of_unit' => $val['number_units'],
								'value_1' => $val['jumlah_pasien_indication_1'],
								'value_2' => $val['jumlah_pasien_indication_2'],
								'value_3' => $val['jumlah_pasien_indication_3'],
								'value_4' => $val['jumlah_pasien_indication_4'],
								'value_5' => $val['jumlah_pasien_indication_5'],
								'price' => $val['selling_price'],
							];
							insert_data('trxdact_sku_'.$tahun.'_'.sprintf('%02d', $bulan), $data_sku);
						}
					}
				}
			}	
		}
	}

	function generate_raw_data($tahun = '2022', $bulan = '12'){
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

	function sync_sub_specialist_dact(){
		$bulan = '03';
		$tahun = '2022';
		if(in_array($bulan, ['01','02','03','04'])){
			$cycle = '1';
		} else if(in_array($bulan, ['05','06','07','08'])){
			$cycle = '2';
		} else if(in_array($bulan, ['09','10','11','12'])){
			$cycle = '3';
		}
		$dact = get_data('trxdact_'.$tahun.'_'.$bulan, [
			'select' => 'trxdact_'.$tahun.'_'.$bulan.'.id, trxprof_'.$tahun.'_'.$cycle.'.spesialist, trxprof_'.$tahun.'_'.$cycle.'.dokter',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' on trxvisit_'.$tahun.'_'.$bulan.'.id = trxdact_'.$tahun.'_'.$bulan.'.visit_plan',
				'trxprof_'.$tahun.'_'.$cycle.' on trxvisit_'.$tahun.'_'.$bulan.'.profiling = trxprof_'.$tahun.'_'.$cycle.'.id',
			]
		])->result_array();
		foreach($dact as $val){
			// $dokter = $val['dokter'];
			// $data_dokter = get_data('dokter', [
			// 	'where' => [
			// 		'id' => $dokter,
			// 		// 'subspesialist !=' => 0
			// 	]
			// ])->row_array();
			$data_spesialist = get_data('dokter', [
				'select' => 'spesialist.id as spesialist, spesialist.nama as nama_spesialist, sub_spesialist.id as sub_spesialist, sub_spesialist.nama as nama_sub_spesialist',
				'join' => [
					'spesialist on spesialist.id = dokter.spesialist type left',
					'sub_spesialist on dokter.subspesialist = sub_spesialist.id type left',
				],
				'where' => [
					'spesialist.id' => $val['spesialist'],
				]
			])->row_array();
			// debug($data_spesialist);
			if($data_spesialist){
				update_data('trxdact_'.$tahun.'_'.$bulan, [
					'sub_spesialist' => $data_spesialist['sub_spesialist'],
					'nama_subspesialist' => $data_spesialist['nama_sub_spesialist'],
				], 'id', $val['id']);
			}
		}
	}

	function delete_double_dokter($tahun, $cycle){
		$table = 'trxprof_'.$tahun.'_'.$cycle;
		$this->db->query('delete a from '.$table.' a join '.$table.' b on a.dokter = b.dokter and a.outlet = b.outlet and a.mr = b.mr and a.produk_grup = b.produk_grup and a.id < b.id');
	}

	function import_all_profiling($tahun, $cycle){
		
	}

	function copy_profiling($nip, $to_nip, $cycle){

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

	function  sync_id_prof_visit_plan($tahun, $bulan){
		$table = 'trxvisit_'.$tahun.'_'.$bulan;
		if(in_array($bulan, ['01','02','03','04'])){
			$cycle = '1';
		} else if(in_array($bulan, ['05','06','07','08'])){
			$cycle = '2';
		} else if(in_array($bulan, ['09','10','11','12'])){
			$cycle = '3';
		}
		$table_prof = 'trxprof_'.$tahun.'_'.$cycle;

		$visit_plan = get_data($table)->result_array();
		debug($visit_plan);
		foreach($visit_plan as $visit_plan){
			$profiling = get_data($table_prof, [
				'where' => [
					'id' => $visit_plan['profiling']
				]
			])->row_array();
			if(!$profiling){
				$new_profiling = get_data($table_prof, [
					'where' => [
						'dokter' => $visit_plan['dokter'],
						'mr' => $visit_plan['mr'],
						'produk_grup' => $visit_plan['produk_grup'],
						// 'outlet' => $visit_plan['outlet'],
					]
				])->row_array();
				if($new_profiling){
					update_data($table, [
						'profiling' => $new_profiling['id']
					], 'id', $visit_plan['id']);
				}
			}
		}
	}

	function sync_prof_kosong(){
		$bulan = '11';
		$table 			= 'trxprof_'.date('Y').'_'.active_cycle();
		$table_indikasi = 'trxprof_indikasi_'.date('Y').'_'.active_cycle();
		$this->db->having('total_indikasi > 0');
		$profiling = get_data($table.' a', [
			'select' => 'a.*, b.*, (b.val_indikasi_1+b.val_indikasi_2+b.val_indikasi_3+b.val_indikasi_4+b.val_indikasi_5+b.val_indikasi_6+b.val_indikasi_7+b.val_indikasi_8+b.val_indikasi_9+b.val_indikasi_10) as total_indikasi',
			'join'	 => [
				$table_indikasi.' b on a.id = b.profiling and bulan = '.$bulan.' type left'
			],
			'where' => [
			   //      'a.mr' => ['02553','02517','03293']
			]
			// 'where' => [
			// 	'a.mr'	=> '02357'
			// ]
		])->result_array();
		// echo count($profiling); die;
		$data_insert = 0;
		foreach($profiling as $v){
			$table_indikasi_lama = 'trxprof_indikasi_'.date('Y').'_3';
			// $val_indikasi_1 = $v['val_indikasi_1'];
			// $val_indikasi_2 = $v['val_indikasi_2'];
			// $val_indikasi_3 = $v['val_indikasi_3'];
			// $val_indikasi_4 = $v['val_indikasi_4'];
			// $val_indikasi_5 = $v['val_indikasi_5'];
			// $val_indikasi_6 = $v['val_indikasi_6'];
			// $val_indikasi_7 = $v['val_indikasi_7'];
			// $val_indikasi_8 = $v['val_indikasi_8'];
			// $val_indikasi_9 = $v['val_indikasi_9'];
			// $val_indikasi_10 = $v['val_indikasi_10'];
			$total_indikasi = 0;
			if($total_indikasi <= 0){
				$prev_prof = get_data('trxprof_2022_3 a', [
					'join'	=> [
						$table_indikasi_lama.' b on a.id = b.profiling and bulan = "10"'
					],
					'where' => [
						'a.dokter' => $v['dokter'],
						'a.mr' => $v['mr'],
						'a.produk_grup' => $v['produk_grup'],
						'a.outlet' => $v['outlet'],
					]
				])->row_array();
				if($prev_prof){
					$data_insert++;
					delete_data($table_indikasi, [
						'profiling' => $v['id'],
						'bulan' => $bulan
					]);
					insert_data($table_indikasi, [
						'profiling' => $v['id'],
						'bulan' => $bulan,
						'val_indikasi_1' => $prev_prof['val_indikasi_1'],
						'val_indikasi_2' => $prev_prof['val_indikasi_2'],
						'val_indikasi_3' => $prev_prof['val_indikasi_3'],
						'val_indikasi_4' => $prev_prof['val_indikasi_4'],
						'val_indikasi_5' => $prev_prof['val_indikasi_5'],
						'val_indikasi_6' => $prev_prof['val_indikasi_6'],
                        'val_indikasi_7' => $prev_prof['val_indikasi_7'],
                        'val_indikasi_8' => $prev_prof['val_indikasi_8'],
                        'val_indikasi_9' => $prev_prof['val_indikasi_9'],
                        'val_indikasi_10' => $prev_prof['val_indikasi_10'],
						'potensi_tablet' => $prev_prof['val_indikasi_1'] + $prev_prof['val_indikasi_2'] + $prev_prof['val_indikasi_3'] + $prev_prof['val_indikasi_4'] + $prev_prof['val_indikasi_5'] + $prev_prof['val_indikasi_6'] + $prev_prof['val_indikasi_7'] + $prev_prof['val_indikasi_8'] + $prev_prof['val_indikasi_9'] + $prev_prof['val_indikasi_10'],
						'jumlah_pasien'	=> $prev_prof['val_indikasi_1'] + $prev_prof['val_indikasi_2'] + $prev_prof['val_indikasi_3'] + $prev_prof['val_indikasi_4'] + $prev_prof['val_indikasi_5'] + $prev_prof['val_indikasi_6'] + $prev_prof['val_indikasi_7'] + $prev_prof['val_indikasi_8'] + $prev_prof['val_indikasi_9'] + $prev_prof['val_indikasi_10'],
						'fee_patient' =>  $prev_prof['fee_patient'],
						'ap_original' => $prev_prof['ap_original'],
					]);
				}
			}
		}
		// echo json_encode($profiling);
		echo 'Data			: '.count($profiling).'<br>';
		echo 'Data insert 	: '.$data_insert.'<br/>';

		echo round(((((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"])*1000)/1000)),3).' Seconds';
	}
