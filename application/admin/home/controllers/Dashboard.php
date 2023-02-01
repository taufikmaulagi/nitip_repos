<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends BE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {

        // $produk_group = $this->session->userdata('produk_group');

        // $data_call = [];
        // $data_dfr  = [];
        // $data_uc   = [];
        // foreach($produk_group as $k => $v){
        //     $call = get_data('trxvisit_'.date('Y').'_'.date('m'). ' a', [
        //         'select' => 'plan_call, SUM(
        //             CASE WHEN b.status = 2 THEN 1 ELSE 0 END
        //         ) as total_call, 
        //         (
        //             CASE WHEN b.status = 2 THEN 1 ELSE 0 END
        //         ) as total_dokter, a.nama_produk_grup as nama',
        //         'join' => [
        //             'trxdfr_'.date('Y').'_'.date('m').' b on b.visit_plan = a.id type left'
        //         ],
        //         'where' => [
        //             'a.produk_grup' => $v['kode'],
        //             'a.mr' => user('username'),
        //             'a.status' => '3',
        //             'a.plan_call >' => 0
        //         ],
        //         'group_by' => 'a.dokter'
        //     ])->result_array();

        //     $tmp_call = [
        //         'plan_call' => 0,
        //         'total_call' => 0,
        //         'plan_dokter' => count($call),
        //         'actual_dokter' => 0,
        //         'plan_percent' => count($call),
        //         'actual_percent' => 0
        //     ];
        //     foreach($call as $cv){
        //         $tmp_call['plan_call'] += intval($cv['plan_call']);
        //         $tmp_call['total_call'] += intval($cv['total_call']);
        //         $tmp_call['actual_dokter'] += intval($cv['total_dokter']);
        //         if($cv['plan_call'] === $cv['total_call']){
        //             $tmp_call['actual_percent']++;
        //         }
        //     }

        //     $tmp_call['produk_grup'] = $v['nama'];
            
        //     $data_call[] = $tmp_call;

        //     $tmp_dfr = get_data('trxdfr_'.date('Y').'_'.date('m').' a', [
        //         'select' => '(SUM(CASE WHEN a.status = 2 and a.call_type = 1 THEN 1 ELSE 0 END) / IFNULL(b.jumlah, 20) / td.target) as total',
        //         'join' => [
        //             'jumlah_hari_kerja b on a.produk_grup = a.produk_grup and b.bulan = '.date('m').' and b.user = a.mr type left',
        //             'tbl_user mr on mr.username = a.mr',
        //             'target_dfr td on td.produk_grup = a.produk_grup and td.tim = mr.tim type left',
        //         ],
        //         'where' => [
        //             'a.produk_grup' => $v['kode'],
        //             'a.mr' => user('username')
        //         ]
        //     ])->row_array();
        //     $tmp_dfr['produk_grup'] = $v['nama'];
        //     $data_dfr[] = $tmp_dfr;

        //     $tmp_data_uc = get_data('trxdact_'.date('Y').'_10', [
        //         'select' => '
        //         SUM(CASE 
        //             WHEN status_dokter = "CONFIRM" THEN 1
        //             ELSE 0
        //         END) as confirm, 
        //         SUM(CASE 
        //             WHEN status_dokter = "USE" THEN 1
        //             ELSE 0
        //         END) as used,
        //         SUM(CASE 
        //             WHEN status_dokter = "NU" THEN 1
        //             ELSE 0
        //         END) as nu',
        //         'where' => [
        //             'produk_grup' => $v['kode'],
        //             'mr' => user('username')
        //         ],
        //     ])->row_array();
            
        //     $tmp_data_uc['produk_grup'] = $v['nama'];
        //     $data_uc[] = $tmp_data_uc;

        // }

        // render([
        //     'produk_group' => $produk_group,
        //     'data_call' => $data_call,
        //     'data_dfr' => $data_dfr,
        //     'data_uc' => $data_uc,
        //     'title' => 'Dashboard'
        // ]);
        
        render();

    }

}