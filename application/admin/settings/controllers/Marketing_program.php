<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Marketing_program extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [
			'join' => [
				'produk_grup on marketing_program.produk_grup = produk_grup.id'
			]
		];
		if(get('pgroup'))
			$config['where']['produk_grup'] = get('pgroup');
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('marketing_program','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('marketing_program',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('marketing_program','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['nama' => 'nama','produk_grup' => 'produk_grup','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_marketing_program',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['nama','produk_grup','is_active'];
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
					$save = insert_data('marketing_program',$data);
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
		$arr = ['nama' => 'Nama','produk_grup' => 'Produk Grup','is_active' => 'Aktif'];
		$data = get_data('marketing_program')->result_array();
		$config = [
			'title' => 'data_marketing_program',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}