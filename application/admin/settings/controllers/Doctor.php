<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Doctor extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [
			'join' => [
				'spesialist on spesialist.id = dokter.spesialist',
				'sub_spesialist on sub_spesialist.id = dokter.subspesialist type left',
				'branch on branch.id = dokter.branch type left'
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('dokter','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('dokter',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('dokter','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['nama' => 'nama','tanggal_lahir' => 'tanggal_lahir','spesialist' => 'spesialist','subspesialist' => 'subspesialist','branch' => 'branch','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_doctor',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['nama','tanggal_lahir','spesialist','subspesialist','branch','is_active'];
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
					$save = insert_data('dokter',$data);
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
		$arr = ['nama' => 'Nama','tanggal_lahir' => '-dTanggal Lahir','spesialist' => 'Spesialist','subspesialist' => 'Subspesialist','branch' => 'Branch','is_active' => 'Aktif'];
		$data = get_data('dokter')->result_array();
		$config = [
			'title' => 'data_doctor',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}