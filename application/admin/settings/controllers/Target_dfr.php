<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Target_dfr extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$produk_grup = get_datA('produk_grup', [
			'where' => [
				'is_active' => 1
			]
		])->result_array();
		render([
			'produk_grup' => $produk_grup
		]);
	}

	function data() {
		$conf = [
			'select' => 'pg.nama as nama_produk_grup, tm.nama as nama_tim',
			'join' => [
				'produk_grup pg on pg.kode = target_dfr.produk_grup',
				'tim tm on tm.id = target_dfr.tim type left'
			]
		];
		$data = data_serverside($conf);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('target_dfr','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('target_dfr',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('target_dfr','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['produk_grup' => 'produk_grup','target' => 'target'];
		$config[] = [
			'title' => 'template_import_target_dfr',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['produk_grup','target'];
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
					$save = insert_data('target_dfr',$data);
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
		$arr = ['produk_grup' => 'Produk Grup','target' => 'Target'];
		$data = get_data('target_dfr')->result_array();
		$config = [
			'title' => 'data_target_dfr',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}