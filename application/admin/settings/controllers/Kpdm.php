<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kpdm extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$conf = [
			'select' => 'branch.nama as nama_branch, IF(jenis_kelamin = "P", "Perempuan", "Laki-laki") as jenis_kelamin, outlet.nama as nama_rumah_sakit',
			'join' => [
				'branch on branch.id = kpdm.branch',
				'outlet on outlet.id = kpdm.rumah_sakit type left'
			],
			'access_view' => false,
		];
		$data = data_serverside($conf);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('kpdm','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('kpdm',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('kpdm','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['nama' => 'nama','jabatan' => 'jabatan','branch' => 'branch','no_hp' => 'no_hp','alamat' => 'alamat','tanggal_lahir' => 'tanggal_lahir','jenis_kelamin' => 'jenis_kelamin','keterangan' => 'keterangan','rumah_sakit' => 'rumah_sakit'];
		$config[] = [
			'title' => 'template_import_kpdm',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['nama','jabatan','branch','no_hp','alamat','tanggal_lahir','jenis_kelamin','keterangan','rumah_sakit'];
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
					$save = insert_data('kpdm',$data);
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
		$arr = ['nama' => 'Nama','jabatan' => 'Jabatan','branch' => 'Branch','no_hp' => 'No Hp','alamat' => 'Alamat','tanggal_lahir' => '-dTanggal Lahir','jenis_kelamin' => 'Jenis Kelamin','keterangan' => 'Keterangan','rumah_sakit' => 'Rumah Sakit'];
		$data = get_data('kpdm')->result_array();
		$config = [
			'title' => 'data_kpdm',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function get_outlet($branch){
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

	function get_detail_rs(){
		$id = get('id');
		$outlet = get_data('outlet', 'id', $id)->row_array();
		render($outlet, 'json');
	}

}