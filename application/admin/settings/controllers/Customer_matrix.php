<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer_matrix extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [
			'join' => [
				'produk_grup on produk_grup.kode = rumus_customer_matrix.produk_grup'
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('rumus_customer_matrix','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('rumus_customer_matrix',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('rumus_customer_matrix','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['potensi' => 'potensi','status_dokter' => 'status_dokter','matrix' => 'matrix','standard_call' => 'standard_call','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_customer_matrix',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['potensi','status_dokter','matrix','standard_call','is_active'];
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
					$save = insert_data('rumus_customer_matrix',$data);
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
		$arr = ['potensi' => 'Potensi','status_dokter' => 'Status Dokter','matrix' => 'Matrix','standard_call' => 'Standard Call','is_active' => 'Aktif'];
		$data = get_data('rumus_customer_matrix')->result_array();
		$config = [
			'title' => 'data_customer_matrix',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}