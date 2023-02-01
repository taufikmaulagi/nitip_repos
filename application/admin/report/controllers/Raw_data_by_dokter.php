<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Raw_data_by_dokter extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
        // ini_set('memory_limit', '-1');
		render();
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('call_type','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('call_type',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('call_type','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tipe' => 'tipe','keterangan' => 'keterangan','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_call_type',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['tipe','keterangan','is_active'];
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
					$save = insert_data('call_type',$data);
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
		$arr = ['tipe' => 'Tipe','keterangan' => 'Keterangan','is_active' => 'Aktif'];
		$data = get_data('call_type')->result_array();
		$config = [
			'title' => 'data_call_type',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}