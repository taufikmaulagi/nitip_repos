<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Persepsi extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [
			'where' => [
				'produk_grup' => get('produk_grup')
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('persepsi_acara','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('persepsi_acara',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('persepsi_acara','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['produk_grup' => 'produk_grup','persepsi' => 'persepsi','tipe' => 'tipe','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_persepsi',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['produk_grup','persepsi','tipe','is_active'];
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
					$save = insert_data('persepsi_acara',$data);
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
		$arr = ['produk_grup' => 'Produk Grup','persepsi' => 'Persepsi','tipe' => 'Tipe','is_active' => 'Aktif'];
		$data = get_data('persepsi_acara')->result_array();
		$config = [
			'title' => 'data_persepsi',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}