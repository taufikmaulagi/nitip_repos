<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [
			'join' => [
				'divisi on divisi.id = tim.divisi'
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tim','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tim',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tim','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['kode' => 'kode','nama' => 'nama','divisi' => 'divisi','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_team',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['kode','nama','divisi','is_active'];
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
					$save = insert_data('tim',$data);
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
		$arr = ['kode' => 'Kode','nama' => 'Nama','divisi' => 'Divisi','is_active' => 'Aktif'];
		$data = get_data('tim')->result_array();
		$config = [
			'title' => 'data_team',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}