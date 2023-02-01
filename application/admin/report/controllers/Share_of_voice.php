<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Share_of_voice extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$produk = get('produk');
		$tahun = get('tahun');
		$bulan = get('bulan');

		$sov = [];

		if($produk && $tahun && $bulan){

		$sov = get_data('trxdfr_'.$tahun.'_'.$bulan, [
			'select' => '
				nama_dokter,
				spesialist.nama as nama_spesialist,
				mr.nama as nama_mr,
				p1.nama as nama_p1,
				p2.nama as nama_p2,
				p3.nama as nama_p3,
			',
			'join' => [
				'tbl_user mr on mr.kode = trxdfr_'.$tahun.'_'.$bulan.'.mr',
				'produk_grup p1 on p1.kode = trxdfr_'.$tahun.'_'.$bulan.'.produk_grup',
				'produk_grup p2 on p2.kode = trxdfr_'.$tahun.'_'.$bulan.'.produk2 type left',
				'produk_grup p3 on p3.kode = trxdfr_'.$tahun.'_'.$bulan.'.produk3 type left',
				'dokter on dokter.id = trxdfr_'.$tahun.'_'.$bulan.'.dokter',
				'spesialist on spesialist.id = dokter.spesialist type left',
			],
			'where' => [
				'produk_grup' => $produk,
				'status' => 2
			]
		])->result_array();
			}
		render([
			'sov' => $sov
		]);
	}

}