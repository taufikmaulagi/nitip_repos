<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contribution extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
        $produk_group = get('pgroup');
        $bulan = get('bulan');
        $tahun = get('tahun');
        $spesialist = [];
        if(!empty($produk_group) && !empty($bulan) && !empty($tahun)){

            $dact = get_data('trxdact_'.$tahun.'_'.$bulan, [
                'select' => 'spesialist.nama as nama_spesialist, sub_spesialist.nama as nama_subspesialist',
                'where' => [
                    'produk_grup' => $produk_group
                ],
                'join' => [
                    'dokter on dokter.id = trxdact_'.$tahun.'_'.$bulan.'.dokter',
                    'spesialist on spesialist.id = dokter.spesialist type left',
                    'sub_spesialist on sub_spesialist.id = dokter.subspesialist type left',
                ],
                'sort_by' => 'trxdact_'.$tahun.'_'.$bulan.'.status_dokter',
                'sort' => 'DESC'
            ])->result_array();
            
            foreach($dact as $val){
                $tmp_spesialist = '';
                if($val['nama_subspesialist']){
                    $tmp_spesialist = $val['nama_subspesialist'];
                } else {
                    $tmp_spesialist = $val['nama_spesialist'];
                }
                if(in_array($tmp_spesialist, $spesialist)){
                    continue;
                }
                $spesialist[] = $tmp_spesialist;
            }

        }
		render([
            'spesialist' => $spesialist,
        ]);
	} 

}