<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_sub_group extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [];
		if(get('product_group')){
			$config['where']['kode_grup'] = get('product_group');
		}
		$config['join'] = [
			'produk_grup on produk_grup.kode = produk_subgrup.kode_grup',
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_all(){
		$where = [];
		if(get('produk_group')){
			$where['kode_grup'] = get('produk_group');
		}
		$data = get_data('produk_subgrup', [
					'where' => $where
				])->result_array();
		render($data, 'json');
	}

	function get_data() {
		$data = get_data('produk_subgrup','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('produk_subgrup',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('produk_subgrup','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['kode' => 'kode','kode_grup' => 'kode_grup','nama' => 'nama','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_product_sub_group',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['kode','kode_grup','nama','is_active'];
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
					$save = insert_data('produk_subgrup',$data);
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
		$arr = ['kode' => 'Kode','kode_grup' => 'Kode Grup','nama' => 'Nama','is_active' => 'Aktif'];
		$data = get_data('produk_subgrup')->result_array();
		$config = [
			'title' => 'data_product_sub_group',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}