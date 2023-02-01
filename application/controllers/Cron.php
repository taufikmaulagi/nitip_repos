<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller {
    
    function backup($tipe = 'all') {
		ini_set('memory_limit', '-1');

        if(in_array($tipe, ['all','db'])) {
            $backupdir = FCPATH . 'assets/backup/backup_'.date('Y_m_d_h_i');
            if(!is_dir($backupdir)) mkdir($backupdir, 0777, true);
            
            $table = db_list_table();
            $this->load->dbutil();
            $this->load->helper('file');
            foreach($table as $t) {
                $prefs = array(
                    'tables'      => array($t),
                    'format'      => 'sql',
                    'filename'    => $t.'.sql'
                );
                $backup		= $this->dbutil->backup($prefs);
                $db_name 	= $t.'.sql';
                $save 		= $backupdir.'/'.$db_name;
                write_file($save, $backup);
            }
        }
        if(in_array($tipe, ['all','file'])) {
            $conf       = [
                'src'       => FCPATH . 'assets/uploads/',
                'dst'       => FCPATH . 'assets/backup/',
                'filename'  => 'backup_file_'.date('Y_m_d_h_i')
            ];
            $this->load->library('Rzip',$conf);
            $this->rzip->compress();
        }
    }


    function sync_prof_kosong(){

		ini_set('memomry_limit',-1);
		ini_set('max_execution_time', -1);

		$bulan = date('m');
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
            $cycle = active_cycle();
            $tahun = date('Y');
            if($bulan == '01'){
                $tahun--;
                $cycle = 3;
            } else if($bulan == '05'){
                $cycle = 1;
            } else if($bulan == '09'){
                $cycle = 2;
            }
			$table_indikasi_lama = 'trxprof_indikasi_'.$tahun.'_'.$cycle;
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
				$prev_prof = get_data('trxprof_'.$tahun.'_'.$cycle.' a', [
					'join'	=> [
						$table_indikasi_lama.' b on a.id = b.profiling and bulan = "'.(date('m') == '01' ? 12 : (sprintf('%02s',intval(date('m')-1)))).'"'
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
	}
}