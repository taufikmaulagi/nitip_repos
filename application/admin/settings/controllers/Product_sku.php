<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_sku extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [];
		if(get('subgroup'))
			$config['where']['kode_subgrup'] = get('subgroup');
		if(get('group'))
			$config['where']['produk_grup.kode'] = get('group');
		$config['join'] = [
			'produk_subgrup on produk_subgrup.kode = produk.kode_subgrup',
			'produk_grup on produk_grup.kode = produk_subgrup.kode_grup',
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('produk','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('produk',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('produk','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['kode' => 'kode','nama' => 'nama','alias' => 'alias','kode_subgrup' => 'kode_subgrup','urutan' => 'urutan','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_product_sku',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['kode','nama','alias','kode_subgrup','urutan','is_active'];
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
					$save = insert_data('produk',$data);
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
		$arr = ['kode' => 'Kode','nama' => 'Nama','alias' => 'Alias','kode_subgrup' => 'Kode Subgrup','urutan' => 'Urutan','is_active' => 'Aktif'];
		$data = get_data('produk')->result_array();
		$config = [
			'title' => 'data_product_sku',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}