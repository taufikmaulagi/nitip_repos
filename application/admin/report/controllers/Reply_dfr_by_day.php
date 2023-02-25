<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reply_dfr_by_day extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {

		render();

		// $tahun = get('tahun');
		// $bulan = get('bulan');
		// $team_group = get('team_group');

		// $data = [];
		// $data_users = [];

		// $arr_produk_group = get_data('produk_grup',[
		// 	'select' => 'produk_grup.*',
		// 	'join' => [
		// 		'tim on tim.kode = produk_grup.kode_team',
		// 		'tim_grup on tim_grup.id = tim.grup'
		// 	],
		// 	'where' => [
		// 		'tim_grup.id' => $team_group
		// 	]
		// ])->result_array();
		
		// if(empty($bulan) or empty($tahun)){
		// 	render();
		// } else {
		// 	$team = get_data('tim', [
		// 		'where' => [
		// 			'grup' => $team_group
		// 		]
		// 	])->result_array();
		// 	$team = array_column($team, 'kode');

		// 	$tmp_users = [];
		// 	$data_nsm = get_data('history_organogram_detail a', [
		// 		'select' => 'a.n_nsm as nip, a.nama_nsm as nama',
		// 		'join' => [
		// 			'history_organogram b on a.id_history_organogram = b.id',
		// 			'tim t on t.kode = a.kode_team',
		// 		],
		// 		'where' => [
		// 			'b.tanggal_end' => '0000-00-00',
		// 			'a.kode_divisi' => 'E',
		// 			'a.n_nsm != ' => '',
		// 			't.is_active' => 1,
		// 			'a.kode_team' => $team,
		// 			'n_nsm' => ['00525','00868']
		// 		],
		// 		'group_by' => 'a.n_nsm',
		// 	])->result_array();

		// 	foreach($data_nsm as $k => $v){

		// 		$data_am = get_data('history_organogram_detail a', [
		// 			'select' => 'a.n_am as nip, a.nama_am as nama',
		// 			'join' => [
		// 				'history_organogram b on a.id_history_organogram = b.id',
		// 				'tim t on t.kode = a.kode_team',
		// 			],
		// 			'where' => [
		// 				'b.tanggal_end' => '0000-00-00',
		// 				't.is_active' => 1,
		// 				'a.n_nsm' => $v['nip'],
		// 				'a.n_am != ' => '',
		// 				'a.kode_team' => $team,
		// 			],
		// 			'group_by' => 'a.n_am',
		// 		])->result_array();

		// 		foreach($data_am as $dv){
		// 			$tmp_users[] = $dv;
		// 		}

		// 		$v['child'] = $data_am;
		// 		$tmp_users[] = $v;
		// 		$data_users[] = $v;
		// 	}

		// 	for($i=date('Y-m-01', strtotime($tahun.'-'.$bulan.'-01')); $i<=date('Y-m-t', strtotime($tahun.'-'.$bulan.'-01')); $i=date('Y-m-d', strtotime($i.' +1 day'))){
		// 		$tmp_data = [];
		// 		foreach($tmp_users as $k => $v){
		// 			$select_if = '';
		// 			foreach($arr_produk_group as $ak => $av){
		// 				$select_if .= 'SUM(IF(kode_produk_grup="'.$av['kode'].'" and date(tanggal) = "'.$i.'", 1, 0)) as jumlah_'.$av['kode'].',';
		// 			}

		// 			$rekap_rep = get_data('rekap_reply_dfr_'.$tahun.' a', [
		// 				'select' => $select_if.' a.tanggal, a.nama_user',
		// 				'where' => [
		// 					'a.tanggal' => $i,
		// 					'a.username' => $v['nip']
		// 				],
		// 				'group_by' => 'date(a.tanggal), a.kode_produk_grup'
		// 			])->row_array();
		// 			if($rekap_rep){
		// 				array_push($tmp_data, $rekap_rep);
		// 			} else {
		// 				$tmp_prd = [];
		// 				foreach($arr_produk_group as $ak => $av){
		// 					$tmp_prd['jumlah_'.$av['kode']] = 0;
		// 				}
		// 				$tmp_prd['tanggal'] = $i;
		// 				$tmp_prd['nama_user'] = $v['nama'];
		// 				array_push($tmp_data, $tmp_prd);
		// 			}
		// 		}
		// 		$tmp_data['tanggal'] = $i;
		// 		$data[] = $tmp_data;
		// 	}

		// 	render([
		// 		'data' => $data,
		// 		'produk_group' => $arr_produk_group,
		// 		'data_user' => $data_users
		// 	]);
		// }
	}

}