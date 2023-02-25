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
			'select' => 'b.mr, d.id as id_dokter, s.id as id_spesialist, pg.kode as kode_produk_grup, pg.nama as nama_produk_grup, o.id as id_outlet, ss.id as id_sub_spesialist, d.nama as nama_dokter, s.nama as nama_spesialist, ss.nama as nama_sub_spesialist, o.nama as nama_outlet, (week1+week2+week3+week4+week5+week6) as plan_call, count(if(e.status = "SENT", 1, 0)) as actual_call, IF((week1+week2+week3+week4+week5+week6) > 0, 1, 0) as dc_plan, IF(COUNT(IF(e.status = "SENT", 1, 0)) > 0, 1, 0) as dc_actual, IF((week1+week2+week3+week4+week5+week6) > 0, 1, 0) as pc_plan, IF((week1+week2+week3+week4+week5+week6) = COUNT(IF(e.status = "SENT", 1, 0)), 1, 0) as pc_actual',
			'join' => [
				'trxprof_'.$tahun.'_'.$cycle.' b on a.profiling = b.id',
				'dokter d on d.id = b.dokter',
				'spesialist s on s.id = d.spesialist',
				'sub_spesialist ss on ss.id = d.subspesialist type left',
				'outlet o on o.id = b.outlet',
				'trxdfr_'.$tahun.'_'.$bulan.' e on e.visit_plan = a.id type left',
				'produk_grup pg on pg.kode = b.produk_grup',
			],
			'where' => [
				'a.status' => 'APPROVED'
			],
			'group_by' => 'd.id, o.id'
		])->result_array();

		$counter_inserted = 0;
		$counter_updated = 0;
	
		foreach($data as $v){
			$check = get_data('rekap_call_activity_'.$tahun, [
				'where' => [
					'bulan' => $bulan,
					'mr' => $v['mr'],
					'id_dokter' => $v['id_dokter'],
					'id_outlet' => $v['id_outlet']
				]
			])->row_array();
			
			if($check){
				$update = update_data('rekap_call_activity_'.$tahun, $v, 'id', $check['id']);
				if($update) $counter_updated++;
			} else {
				$v['bulan'] = $bulan;
				$insert = insert_data('rekap_call_activity_'.$tahun, $v);
				if($insert) $counter_inserted++;
			}
		}
		
		echo 'Data Inserted '. $counter_inserted.'<br/>';
		echo 'Data Updated '. $counter_updated;

	}

	function generate_rekap_reply_dfr(){

		$tahun = date('Y');
		$bulan = date('m');
		$cycle = cycle_by_month($bulan);

		if(!table_exists('rekap_reply_dfr_'.$tahun)){
			$this->load->helper('gen_report_table');
			init_table_rekap_reply_dfr($tahun);
		}

		$reply_dfr = get_data('trxdfr_feedback_'.$tahun.'_'.$bulan.' a', [
			'select' => 'u.username, u.nama as nama_user, pg.kode as kode_produk_grup, pg.nama as nama_produk_grup, "'.$bulan.'" as bulan, a.dfr, a.cat as tanggal',
			'join' => [
				'trxdfr_'.$tahun.'_'.$bulan.' b on a.dfr = b.id',
				'trxvisit_'.$tahun.'_'.$bulan.' c on c.id = b.visit_plan',
				'trxprof_'.$tahun.'_'.$cycle.' d on d.id = c.profiling',
				'tbl_user u on u.id = a.user',
				'produk_grup pg on pg.kode = d.produk_grup'
			]
		])->result_array();

		foreach($reply_dfr as $k => $v){
			$check = get_data('rekap_reply_dfr_'.$tahun, [
				'where' => [
					'username' => $v['username'],
					'tanggal' => $v['tanggal'],
					'dfr' => $v['dfr'],
				]
			])->row_array();

			$inserted = 0;
			$updated = 0;
			if($check){
				$proc = update_data('rekap_reply_dfr_'.$tahun, $v, 'id', $check['id']);
				if($proc) $updated++;
			} else {
				$proc = insert_data('rekap_reply_dfr_'.$tahun, $v);
				if($proc) $inserted++;
			}

		}

		echo 'Data Inserted '. $inserted.'<br/>';
		echo 'Data Updated '. $updated;

	}

	function rekap_share_by_voice(){
		$tahun = date('Y');
		$bulan = date('m');
		$cycle = cycle_by_month($bulan);

		if(!table_exists('rekap_share_of_voice_'.$tahun)) {
			$this->load->helper('gen_report_table');
			init_table_share_of_voice($tahun);
		}
		
		$data_dfr = get_data('trxdfr_'.$tahun.'_'.$bulan.' a', [
			'select' => 'mr.username, mr.nama as nama_user, count(*) as p1, c.produk_grup',
			'join' => [
				'trxvisit_'.$tahun.'_'.$bulan.' b on a.visit_plan = b.id',
				'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
				'tbl_user mr on mr.username = c.mr'
			],
			'group_by' => 'c.produk_grup'
		])->result_array();
		$data = [];
		foreach($data_dfr as $k => $v){
			$tim = get_data('tim', [
				'select' => 'tim.*',
				'join' => [
					'produk_grup on produk_grup.kode_team = tim.kode'
				],
				'where' => [
					'produk_grup.kode' => $v['produk_grup']
				]
			])->row_array();
	
			$produk = get_data('produk_grup', [
				'select' => 'produk_grup.*',
				'join' => [
					'tim on tim.kode = produk_grup.kode_team'
				],
				'where' => [
					'tim.grup' => $tim['grup'],
					'produk_grup.kode !=' => $v['produk_grup']
				]
			])->result_array();

			foreach($produk as $pk => $pv){
				$produk_2 = get_data('trxdfr_'.$tahun.'_'.$bulan.' a', [
					'join' => [
						'trxvisit_'.$tahun.'_'.$bulan.' b on a.visit_plan = b.id',
						'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
						'tbl_user mr on mr.username = c.mr'
					],
					'where' => [
						'mr.username' => $v['username'],
						'c.produk_grup' => $v['produk_grup'],
						'a.produk2' => $pv['id']
					],
					'group_by' => 'a.produk2'
				])->num_rows();

				$produk_3 = get_data('trxdfr_'.$tahun.'_'.$bulan.' a', [
					'join' => [
						'trxvisit_'.$tahun.'_'.$bulan.' b on a.visit_plan = b.id',
						'trxprof_'.$tahun.'_'.$cycle.' c on c.id = b.profiling',
						'tbl_user mr on mr.username = c.mr'
					],
					'where' => [
						'mr.username' => $v['username'],
						'c.produk_grup' => $v['produk_grup'],
						'a.produk3' => $pv['id']
					],
					'group_by' => 'a.produk3'
				])->num_rows();

				$v['p2_'.($pk+1)] = $produk_2;
				$v['p3_'.($pk+1)] = $produk_3;
			}

			$data[] = $v;
		}

		foreach($data as $k => $v){
			// insert_data('tbl_')
		}
	}
}