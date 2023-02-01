<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Price_list extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data = get_data('pricelist_detail','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('pricelist_detail',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('pricelist_detail','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['id_pricelist' => 'id_pricelist','id_produk_oi' => 'id_produk_oi','id_produk' => 'id_produk','kode_distributor' => 'kode_distributor','kode_sector' => 'kode_sector','tanggal' => 'tanggal','hjp' => 'hjp','hna' => 'hna'];
		$config[] = [
			'title' => 'template_import_price_list',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['id_pricelist','id_produk_oi','id_produk','kode_distributor','kode_sector','tanggal','hjp','hna'];
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
					$save = insert_data('pricelist_detail',$data);
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
		$arr = ['id_pricelist' => 'Id Pricelist','id_produk_oi' => 'Id Produk Oi','id_produk' => 'Id Produk','kode_distributor' => 'Kode Distributor','kode_sector' => 'Kode Sector','tanggal' => '-dTanggal','hjp' => 'Hjp','hna' => 'Hna'];
		$data = get_data('pricelist_detail')->result_array();
		$config = [
			'title' => 'data_price_list',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}