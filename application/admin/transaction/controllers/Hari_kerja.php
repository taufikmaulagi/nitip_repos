<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hari_kerja extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$bulan = get('bulan');
		$hari_kerja_ku = get_data('jumlah_hari_kerja', [
			'where' => [
				'user' => user('username'),
				'bulan' => $bulan
			]
		])->row_array();
		render([
			'hari_kerja_ku'=> $hari_kerja_ku ? $hari_kerja_ku['jumlah'] : 0
		]);
	}

	function save(){
		$data = get_data('jumlah_hari_kerja',[
			'where' => [
				'user' => post('nip'),
				'bulan' => post('bulan'),
			]
		])->row_array();
		if($data){
			update_data('jumlah_hari_kerja', [
				'jumlah' => post('val')
			], 'id', $data['id']);
		} else {
			insert_data('jumlah_hari_kerja', [
				'user' => post('nip'),
				'bulan' => post('bulan'),
				'jumlah' => post('val')
			]);
		}
	}

}