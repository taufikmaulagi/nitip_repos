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

	function generate_rekap_call_activity(){
		
		ini_set('memory_limit', -1);

		$bulan = date('m');
		$tahun = date('Y');
		$cycle = cycle_by_month($bulan);

		if(!table_exists('rekap_call_activity_'.$tahun)){
			$this->load->helper('gen_report_table');
			init_table_rekap_call_activity($tahun, $bulan);
		}

		$data = get_data('trxvisit_'.$tahun.'_'.$bulan.' a', [
			'select' => 'b.mr, d.id as id_dokter, s.id as id_spesialist, o.id as id_outlet, ss.id as id_sub_spesialist, d.nama as nama_dokter, s.nama as nama_spesialist, ss.nama as nama_sub_spesialist, o.nama as nama_outlet, (week1+week2+week3+week4+week5+week6) as plan_call, count(if(e.status = "SENT", 1, 0)) as actual_call, IF((week1+week2+week3+week4+week5+week6) > 0, 1, 0) as dc_plan, IF(COUNT(IF(e.status = "SENT", 1, 0)) > 0, 1, 0) as dc_actual, IF((week1+week2+week3+week4+week5+week6) > 0, 1, 0) as pc_plan, IF((week1+week2+week3+week4+week5+week6) = COUNT(IF(e.status = "SENT", 1, 0)), 1, 0) as pc_actual',
			'join' => [
				'trxprof_'.$tahun.'_'.$cycle.' b on a.profiling = b.id',
				'dokter d on d.id = b.dokter',
				'spesialist s on s.id = d.spesialist',
				'sub_spesialist ss on ss.id = d.subspesialist type left',
				'outlet o on o.id = b.outlet',
				'trxdfr_'.$tahun.'_'.$bulan.' e on e.visit_plan = a.id type left'
			],
			'where' => [
				'a.status' => 'APPROVED'
			],
			'group_by' => 'd.id, o.id'
		])->result_array();

		$counter = 0;
		
		delete_data('rekap_call_activity_'.$tahun, 'bulan', date('m'));
		foreach($data as $v){
			$insert = insert_data('rekap_call_activity_'.$tahun, $v);
		}
		
		echo 'Good Job';

	}
}