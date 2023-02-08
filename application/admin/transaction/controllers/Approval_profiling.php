<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Approval_profiling extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$table = 'trxprof_'.date('Y').'_'.active_cycle();
		$config = [
			'select' => '(IFNULL(val_indikasi_1, 0) + 
					IFNULL(val_indikasi_2, 0) + 
					IFNULL(val_indikasi_3, 0) + 
					IFNULL(val_indikasi_4, 0) + 
					IFNULL(val_indikasi_5, 0) + 
					IFNULL(val_indikasi_6, 0) + 
					IFNULL(val_indikasi_7, 0) + 
					IFNULL(val_indikasi_8, 0) + 
					IFNULL(val_indikasi_9, 0) + 
					IFNULL(val_indikasi_10, 0)) as jumlah_potensi, dokter.nama as nama_dokter, spesialist.nama as nama_spesialist, outlet.nama as nama_outlet, produk_grup.nama as nama_produk_group',
			'where' 		=> [
				'mr' 			=> get('mr'),
				'produk_grup' 	=> get('grup'),
				'status' 		=> [
					"NOT APPROVED",
					"WAITING"
				],
			],
			'join'			=> [
				'dokter on dokter.id = '.$table.'.dokter',
				'spesialist on spesialist.id = dokter.spesialist',
				'outlet on outlet.id = '.$table.'.outlet',
				'produk_grup on produk_grup.kode = '.$table.'.produk_grup'
			],
			'sort_by' 		=> 'nama_dokter',
			'sort' 			=> 'ASC',
			'access_view' 	=> false,
			'access_edit'	=> false,
			'access_delete'	=> false,
			'button'		=> [
				button_serverside('btn-sky','btn-input','fa-search','edit')
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('trxprof_'.date('Y').'_'.active_cycle(). ' a',[
			'select' => 'a.*, b.nama as nama_dokter, c.nama as nama_outlet',
			'where' => [
				'a.id' 		 => post('id'),	
			],
			'join' => [
				'dokter b on a.dokter = b.id',
				'outlet c on a.outlet = c.id'
			]
		])->row_array();
		render($data,'json');
	}

	function approval($cycle, $tahun){

		$alasan_not_approve = post('alasan_not_approve');
		$this->db->trans_begin();
		$profiling = get_data('trxprof_'.$tahun.'_'.$cycle, 'id', post('id'))->row_array();
		if(!empty($alasan_not_approve)){
			update_data('trxprof_'.$tahun.'_'.$cycle, [
				'status' => 'NOT APPROVED',
			], 'id', $profiling['id']);
		} else {
			$type = post('type_approved');
			if($type == 'approved'){
				update_data('trxprof_'.$tahun.'_'.$cycle, [
					'status' => 'APPROVED',
				], 'id', $profiling['id']);
			} else {
				update_data('trxprof_'.$tahun.'_'.$cycle, [
					'status' => 'WAITING',
				], 'id', $profiling['id']);
			}
		}
		if($this->db->trans_status()===TRUE){
			$this->db->trans_commit();
		} else {
			$this->db->trans_rollback();
		}

		render(['status' => $this->db->trans_status()],'json');

	}

	function get_indikasi(){
		$where = [];
		if(get('pgrup')){
			$where['produk_grup'] = get('pgrup');
		}
		$where['is_active'] = 1;
		$data = get_data('indikasi', [
			'where' => $where
		])->result_array();
		if(!empty($where)){
			render($data, 'json');
		}
	}

	function getMRPerCycle($cycle, $tahun){

		$team = $this->session->userdata('team');
		$tmp_team = [];
		foreach($team as $val){
			array_push($tmp_team, $val['kode_team']);
		}


		if($this->db->table_exists('trxprof_'.$tahun.'_'.$cycle)){
			$this->db->having('total_profiling > 0');
			$data = get_data('history_organogram_detail', [
				'select' => 'history_organogram_detail.*, (select count(*) from trxprof_'.$tahun.'_'.$cycle.' where dat is null and mr = history_organogram_detail.n_mr) as total_profiling',
				'join' => [
					'history_organogram on history_organogram.id = history_organogram_detail.id_history_organogram'
				],
				'where' => [
					'n_am' => user('username'),
					'nama_mr !=' => '',
					'history_organogram.tanggal_end' => '0000-00-00',
				],
				'where_in' => [
					'history_organogram.kode_team' => $tmp_team,
					
				],
				'group_by' => 'n_mr',
				'sort_by' => 'nama_mr',
				'sort' => 'ASC',
			])->result_array();
		} else {
			$data = [];
		}

		render($data, 'json');
	}

	function submit($cycle, $tahun){
		$pgrup = post('pgroup');
		$mr = post('mr');

		$produk = get_data('produk_grup', 'kode', $pgrup)->row_array();
		$profiling = get_data('trxprof_'.$tahun.'_'.$cycle.' a', [
			'select' => 'a.*, c.id as spesialist',
			'where' => [
				'a.status' => ['WAITING','APPROVED']
			],
			'join' => [
				'dokter b on a.dokter = b.id',
				'spesialist c on c.id = b.spesialist'
			]
		])->result_array();

		if(intval($produk['jumlah_profiling']) < 255){

			if(count($profiling) < $produk['jumlah_profiling']){
				render([
					'status' => 'info',
					'message' => 'Jumlah profiling kurang, yang harus di approve untuk produk group '.$produk['nama'].' adalah '.$produk['jumlah_profiling']
				], 'json'); die;
			} else if(count($profiling) > $produk['jumlah_profiling']){
				render([
					'status' => 'info',
					'message' => 'Jumlah profiling telah melebihi batas, yang harus di approve untuk produk group '.$produk['nama'].' adalah '.$produk['jumlah_profiling']
				], 'json'); die;
			}
		} 

		$exspe = explode(',', $produk['spesialist']);
		
		foreach($profiling as $pv){
			$spes_exists = true;
			foreach($exspe as $sv){
				if($sv == $pv['spesialist']){
					$spes_exists = false;
				}
			}

			if($spes_exists){
				$spesialist = get_data('spesialist', [
					'where' => [
						'id' => $exspe
					]
				])->result_array();

				render([
					'status' => 'info',
					'message' => 'Pastikan dokter yang akan diapprove memilki spesialist '.implode(', ', array_column($spesialist, 'nama'))
				], 'json'); die;
			}
		}
		
		$this->db->trans_begin();

		foreach($profiling as $v){
			update_data('trxprof_'.$tahun.'_'.$cycle, [
				'status' => 'APPROVED'
			], [
				'id' => $v['id'],
				'status' => 'WAITING'
			]);
		}

		if($this->db->trans_status() === TRUE){
			render([
				'status' => 'success',
				'message' => 'Approval Profiling Selesai'
			], 'json');
			$this->db->trans_commit();
		} else {
			render([
				'status' => 'error',
				'message' => 'Approval Profiling Gagal, Silahkan coba lagi.'
			], 'json');
			$this->db->trans_rollback();
		}
	}

	function get_detail_dokter(){
		
		$data = get_data('dokter', [
			'select' => 'dokter.*, spesialist.nama as nama_spesialist, sub_spesialist.nama as nama_sub_spesialist',
			'where' => [
				'dokter.id' => get('id')
			],
			'join' => [
				'spesialist on spesialist.id = dokter.spesialist',
				'sub_spesialist on sub_spesialist.id = dokter.subspesialist type left'
			]
		])->row_array();
		render($data, 'json');
	}

}