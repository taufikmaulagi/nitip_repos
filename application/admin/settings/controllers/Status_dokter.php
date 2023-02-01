<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Status_dokter extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config = [
			'join' => [
				'produk_grup on produk_grup.kode = rumus_status_dokter.produk_grup'
			]
		];
		$data = data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('rumus_status_dokter','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('rumus_status_dokter',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('rumus_status_dokter','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['produk_grup' => 'produk_grup','min_pasien' => 'min_pasien','max_pasien' => 'max_pasien','status' => 'status','is_active' => 'is_active'];
		$config[] = [
			'title' => 'template_import_status_dokter',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['produk_grup','min_pasien','max_pasien','status','is_active'];
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
					$save = insert_data('rumus_status_dokter',$data);
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
		$arr = ['produk_grup' => 'Produk Grup','min_pasien' => 'Min Pasien','max_pasien' => 'Max Pasien','status' => 'Status','is_active' => 'Aktif'];
		$data = get_data('rumus_status_dokter')->result_array();
		$config = [
			'title' => 'data_status_dokter',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}