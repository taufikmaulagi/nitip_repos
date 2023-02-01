<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_group extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [];
		if(get('division')){
			$config['where']['kode_divisi'] = get('division');
		}
		if(get('team')){
			$config['where']['kode_team'] = get('team');
		}
		$config['join'] = [
			'tim on tim.kode = produk_grup.kode_team',
			'divisi on divisi.kode = produk_grup.kode_divisi'
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_all(){
		$where = [];
		if(get('id')){
			$where['id'] = get('id');
		}
		$data = get_data('produk_grup', [
			'where' => $where
		])->result_array();
		render($data, 'json');
	}

	function get_data() {
		$data = get_data('produk_grup','id',post('id'))->row_array();
		$data['spesialist'] = explode(',', $data['spesialist']);
		render($data,'json');
	}

	function save() {
		$data = post();
		$data['spesialist'] = implode(',',$this->input->post('spesialist'));
		$response = save_data('produk_grup',$data,post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('produk_grup','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['kode' => 'kode','kode_divisi' => 'kode_divisi','kode_team' => 'kode_team','nama' => 'nama','alias' => 'alias','urutan' => 'urutan','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_product_group',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['kode','kode_divisi','kode_team','nama','alias','urutan','is_active'];
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
					$save = insert_data('produk_grup',$data);
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
		$arr = ['kode' => 'Kode','kode_divisi' => 'Kode Divisi','kode_team' => 'Kode Team','nama' => 'Nama','alias' => 'Alias','urutan' => 'Urutan','is_active' => 'Aktif'];
		$data = get_data('produk_grup')->result_array();
		$config = [
			'title' => 'data_product_group',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}