<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cut_off_period extends BE_Controller {

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
		$data = get_data('cutoff_period','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('cutoff_period',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('cutoff_period','id',post('id'));
		render($response,'json');
	}

	function template() {
		ini_set('memory_limit', '-1');
		$arr = ['tahun' => 'tahun','bulan' => 'bulan','start_date' => 'start_date','end_date' => 'end_date'];
		$config[] = [
			'title' => 'template_import_cut_off_period',
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$file = post('fileimport');
		$col = ['tahun','bulan','start_date','end_date'];
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
					$save = insert_data('cutoff_period',$data);
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
		$arr = ['tahun' => 'Tahun','bulan' => 'Bulan','start_date' => '-dStart Date','end_date' => '-dEnd Date'];
		$data = get_data('cutoff_period')->result_array();
		$config = [
			'title' => 'data_cut_off_period',
			'data' => $data,
			'header' => $arr,
		];
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

}