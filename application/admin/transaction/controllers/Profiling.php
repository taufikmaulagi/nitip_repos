<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profiling extends BE_Controller {

	function __construct() {
		parent::__construct();
		if(!table_exists('trxprof_'.date('Y').'_'.active_cycle())){
			init_table_prof(active_cycle(), date('Y'));
		}
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
				'mr' 			=> user('username'),
				'produk_grup' 	=> get('grup'),
				'status' 		=> "UNSUBMITTED",
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
		];
		$data = data_serverside($config);
		render($data,'json');
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

	function get_indikasi(){
		$where = [];
		if(get('pgrup')){
			$where['produk_grup'] 	= get('pgrup');
			$where['is_active'] 	= 1;
		}
		$data = get_data('indikasi', [
			'where' => $where
		])->result_array();
		if(!empty($where)){
			render($data, 'json');
		}
	}

	function get_data() {
		$data = get_data('trxprof_'.date('Y').'_'.active_cycle(). ' a',[
			'select' => 'a.*, b.nama as nama_dokter, c.nama as nama_outlet',
			'where' => [
				'a.id' 		=> post('id'),
				'a.status' 	=> 'UNSUBMITTED'
			],
			'join' => [
				'dokter b on a.dokter = b.id',
				'outlet c on a.outlet = c.id'
			]
		])->row_array();
		render($data,'json');
	}

	function save() {
		$data = post();
		if($this->input->post('ap_original') == "on"){
			$data['ap_original'] = 1;
		} else {
			$data['ap_original'] = 2;
		}
		
		$table = "trxprof_".date('Y')."_".active_cycle();
		$data['mr'] 		= $this->session->userdata('username');
		$data['am'] 		= $this->session->userdata('N_AM');
		$data['rm'] 		= $this->session->userdata('N_RM');
		$data['nsm'] 		= $this->session->userdata('N_NSM');
		$data['asdir'] 		= $this->session->userdata('N_ASDIR');
		$data['bud'] 		= $this->session->userdata('N_BUD');
		$response = save_data($table, $data, post(':validation'));

		render($response,'json');
	}

	function submit(){
		$produk_group = post('grup');
		$this->db->trans_begin();

		update_data('trxprof_'.date('Y').'_'.active_cycle(), ['status' => 'WAITING'], ['mr' => user('username'), 'produk_grup' => $produk_group]);

		if($this->db->trans_status() === TRUE){
			$this->db->trans_commit();
		} else {
			$this->db->trans_rollback();
		}

		render(['status' => $this->db->trans_status(),'err_data' => ''],'json');
	}

	function delete() {
		$table = 'trxprof_'.date('Y').'_'.active_cycle();
		$table_indikasi = 'trxprof_indikasi_'.date('Y').'_'.active_cycle();
		$response = destroy_data($table,'id',post('id'));
		delete_data($table_indikasi, 'profiling', post('id'));
		render($response,'json');
	}


	function import(){

		ini_set('memory_limit',-1);
		ini_set('max_execution_time',-1);

		$cycle 	= post('cycle');
		$tahun 	= post('tahun');
		$mr 	= post('mr');

		$table = 'trxprof_'.$tahun.'_'.$cycle;

		$profiling = get_data($table, [
			'where' => [
				'mr' => $mr
			]
		])->result_array();

		$this->db->trans_begin();
		foreach($profiling as $v){
			unset($v['id']);
			$v['mr'] = user('username');
			$v['status'] = 'UNSUBMITTED';
			insert_data('trxprof_'.date('Y').'_'.active_cycle(), $v);
		}
		if($this->db->trans_status()){
			$this->db->trans_commit();
			render(['status'=>'1'],'json');
		} else {
			$this->db->trans_rollback();
			// error
		}
	}

	function ac_dokter(){
		$query = get('query');
		$data['suggestions']	= get_data('dokter',[
			'select'		=> 'dokter.id AS data, concat(dokter.nama," - ",spesialist.nama) AS value',
			'join'			=> [
				'spesialist on spesialist.id = dokter.spesialist type left'
			],
			'where'			=> 'concat(dokter.nama," - ",spesialist.nama) LIKE "%'.$query.'%"',
		])->result_array();
		render($data,'json');
	}

	function get_dokter(){
		$q = get('q');
		$data = get_data('dokter',[
			'select'		=> 'dokter.id, concat(dokter.nama," - ",spesialist.nama) AS text',
			'join'			=> [
				'spesialist on spesialist.id = dokter.spesialist type left'
			],
			'where' => [
				'dokter.nama like' => '%'.$q.'%',
			]
		])->result_array();
		render($data, 'json');
	}

	function get_outlet($branch = ''){

		ini_set('memory_limit', -1);
		$where = [];
		$where['branch'] = $branch;
		$where['nama like'] = '%'.get('q').'%';
		$data = get_data('outlet', [
			'select' => 'id, concat(nama," - ",alamat) as text',
			'where' => $where
		])->result_array();
		if(!empty($where)){
			render($data, 'json');
		}	
	}

}
