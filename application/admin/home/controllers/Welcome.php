<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends BE_Controller {

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
        //         'select' => '(SUM(CASE WHEN a.status = 2 and a.call_type = 1 THEN 1 ELSE 0 END) / IFNULL(b.jumlah, 20) / IFNULL(td.target,0)) as total',
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

        //     $tmp_data_uc = get_data('trxdact_2022_11', [
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

        // $data_sales = $this->__data_sales();

        // render([
        //     'produk_group' => $produk_group,
        //     'data_call' => $data_call,
        //     'data_dfr' => $data_dfr,
        //     'data_uc' => $data_uc,
        //     'html_data_sales' => $data_sales['data_sales'],
        //     'achievment' => $data_sales['achievment'],
        //     'growth' => $data_sales['growth'],
        //     'target_data' => $data_sales['target_data'],
        //     'actual_data' => $data_sales['actual_data'],
        //     'title' => 'Dashboard'
        // ]);

        render();
    }

    private function __data_sales()
    {
        switch_database('siap');
        $html_data_sales = '';
        $achievment = [];
        $growth = [];
        $target_data = [];
        $actual_data = [];
        $html_data_sales .=  '<table class="table table-app table-bordered">';
        $html_data_sales .=  '<thead><th rowspan="3"></th>';
	        $html_data_sales .=  '</tr><tr>';
	        // for($i=1;$i<=12;$i++) {
	        //     $html_data_sales .=  '<th class="text-center" colspan="5">'.$i.'</th>';
	        // }
            // $html_data_sales .=  '<th class="text-center" colspan="5">Current Month</th>';
	        // $html_data_sales .=  '<th class="text-center" colspan="5">Total</th>';
	        $html_data_sales .=  '</tr><tr>';
	        $html_data_sales .=  '<th class="text-center">'.'Target'.'</th>';
	        $html_data_sales .=  '<th class="text-center">'.'Actual'.'</th>';
            $html_data_sales .=  '<th class="text-center">'.'Achievment'.'</th>';
	        $html_data_sales .=  '<th class="text-center">'.strftime('%b',strtotime(date('Y', strtotime('-1 Year')))).' '.(date('Y', strtotime('-1 Year'))).'</th>';
            $html_data_sales .=  '<th class="text-center">'.'Growth'.'</th>';
            $html_data_sales .=  '<th class="text-center">'.'YTD 2022'.'</th>';
	        $html_data_sales .=  '</thead>';
        

        $select = 'SUM(hjp * ((`qty` - `bonus`) * `faktor_kali`)) AS jml';

        $dt = [
            'kode_team' => []
        ];

        $org = get_data('tbl_history_organogram_detail a',[
            'select' => 'a.kode_team',
            'join' => [
                'tbl_history_organogram b on a.id_history_organogram = b.id'
            ],
            'where' => [
                'b.tanggal_end' => '0000-00-00',
                'a.n_mr' => user('username')
            ],
            'group_by' => 'a.kode_team'
        ])->result_array();

        $dt['kode_team'] = array_column($org,'kode_team');
        
        $arr_team    = array(
            'select'       => '`kode`,`team`',
            'where'  => array(
                'kode_divisi'  => 'E',
                'status'       => 1
            ),
            'sort_by'  => 'team'
        );
        if(count($dt['kode_team']) > 0) {
            $arr_team['where_in']['kode']    = $dt['kode_team'];
        }
        $team    = get_data('tbl_team',$arr_team)->result();
        foreach($team as $b) {
            $html_data_sales .=  '<tr>';
            $html_data_sales .=  '<th>'.strtoupper($b->team).'</th>';
            $nip   = array();
            if(user('id_group') >= 4 && user('id_group') <= 9) {
                $arr_mr    = array(
                    'select'       => 'DISTINCT(n_mr) AS n_mr',
                    'where'  => array(
                        'n_mr !='      => ''
                    ),
                    '__m'      => '`tanggal` = (SELECT MAX(`tanggal`) FROM '.'tbl_history_organogram_detail'.' WHERE `tanggal` <= "'.(date('Y')).'-'.date('m').'-01" AND `kode_team` = "'.$b->kode.'")',
                    'sort'     => 'DESC',
                    'sort_by'  => 'tanggal'
                );
                if(user('id_group') == 4) $arr_mr['where']['n_bud']         = user('username');
                else if(user('id_group') == 5) $arr_mr['where']['n_asdir']  = user('username');
                else if(user('id_group') == 6) $arr_mr['where']['n_nsm']    = user('username');
                else if(user('id_group') == 7) $arr_mr['where']['n_rm']     = user('username');
                else if(user('id_group') == 8) $arr_mr['where']['n_am']     = user('username');
                else if(user('id_group') == 9) $arr_mr['where']['n_mr']     = user('username');
                $mr    = get_data('tbl_history_organogram_detail',$arr_mr)->result();
                foreach($mr as $m) {
                    $nip[] = $m->n_mr;
                }
            }
            $target    = array();
            $total     = array();
            $last      = array();
            for($j=1;$j<=12;$j++) {
                $select_target = 'SUM(`val_'.sprintf('%02d',$j).'`) AS jml';
                $arr_target    = array(
                    'select'       => $select_target,
                    'where'  => array(
                        'tahun'        => date('Y'),
                        'kode_team'    => $b->kode,
                        'kode_divisi'  => 'E'
                    )
                );
                if(count($nip) > 0) {
                    $arr_target['where_in']['nip'] = $nip;
                }
                $tar   = get_data('tbl_target_field_force',$arr_target)->row();
                $target[$j]    = $tar->jml;

                if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-right">'.number_format($target[$j]).'</td>';
                array_push($target_data, $target[$j]);
                $arr   = array(
                    'select'        => $select,
                    'where'         => array(
                        'kode_divisi'  => 'E',
                        'kode_team'    => $b->kode,
                        'missing_facno'    => 0
                    )
                );
                $arr_dist = [];
                if(count($arr_dist) > 0) $arr['not_like']['outlet']         = $arr_dist;
                if(user('id_group') == 4) {
                    $arr['where']['nip_bud']      = user('username');
                }elseif(user('id_group') == 5) {
                    $arr['where']['nip_asdir']    = user('username');
                }elseif(user('id_group') == 6) {
                    $arr['where']['nip_nsm']      = user('username');
                }elseif(user('id_group') == 7) {
                    $arr['where']['nip_rm']       = user('username');
                }elseif(user('id_group') == 8) {
                    $arr['where']['nip_am']       = user('username');
                }elseif(user('id_group') == 9) {
                    $arr['where']['nip']          = user('username');
                }else{
                    $arr['where']['parent_id']    = 0;
                }
                
                $table = 'tbl_transaksi'.'_'.(date('Y')).sprintf('%02d',$j);
                if(table_exists($table)) {
                    $jumlah    = get_data($table,$arr)->row();
                
                    $total[$j]  = $jumlah->jml;
                    if($total[$j] >= 0) {
                        if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-right">'.number_format($total[$j]).'</td>';
                        array_push($actual_data, $total[$j]);
                    } else {
                        if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-right" style="color: #933;">('.number_format($total[$j]).')</td>';
                    }
                } else {
                    $total[$j] = 0;
                    if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-center">-</td>';
                }
                $table = 'tbl_transaksi'.'_'.(date('Y') - 1).sprintf('%02d',$j);
                if($target[$j] == 0) {
                    if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-center">-</td>';
                    array_push($achievment, 0);
                } else {
                    $ach   = ( $total[$j] / $target[$j] )  * 100;
                    if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-center">'.$this->__set_color_percentage(number_format($ach,1,'.','')).'%</td>';
                    array_push($achievment, number_format($ach,1,'.',''));
                }
                if(table_exists($table)) {
                    $jumlah    = get_data($table,$arr)->row();
                
                    $last[$j]  = $jumlah->jml;
                    if($last[$j] >= 0) {
                        if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-right">'.number_format($last[$j]).'</td>';
                    } else {
                        if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-right" style="color: #933;">('.number_format($last[$j]).')</td>';
                    }
                } else {
                    $last[$j] = 0;
                    if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-center">-</td>';
                }
                if($last[$j] == 0) {
                    array_push($growth, 0);
                    if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-center">-</td>';
                } else {
                    $grw   = ( ( $total[$j] - $last[$j] ) / $last[$j] )  * 100;
                    array_push($growth, number_format($grw,1,'.',''));
                    if($j == intval(date('m'))) $html_data_sales .=  '<td class="text-center">'.$this->__set_color_percentage(number_format($grw,1,'.','')).'%</td>';
                }
            }
            
            //total

            // $html_data_sales .=  '<td class="text-right">'.number_format(array_sum($target)).'</td>';
            // $html_data_sales .=  '<td class="text-right">'.number_format(array_sum($total)).'</td>';
            // $html_data_sales .=  '<td class="text-right">'.number_format(array_sum($last)).'</td>';
            if(array_sum($target) == 0) {
                $html_data_sales .=  '<td class="text-center">-</td>';
            } else {
                $ach   = ( array_sum($total) / array_sum($target) )  * 100;
                $html_data_sales .=  '<td class="text-center">'.$this->__set_color_percentage(number_format($ach,1,'.','')).'%</td>';
            }
            // if(array_sum($last) == 0) {
            //     $html_data_sales .=  '<td class="text-center">-</td>';
            // } else {
            //     $grw   = ( (array_sum($total) - array_sum($last)) / array_sum($last) )  * 100;
            //     $html_data_sales .=  '<td class="text-center">'.number_format($grw,1,'.','').'%</td>';
            // }
            $html_data_sales .=  '</tr>';
        }

        $html_data_sales .=  '</table>';

        switch_database('default');
        return [
            'data_sales' => $html_data_sales,
            'achievment' => $achievment,
            'growth' => $growth,
            'actual_data' => $actual_data,
            'target_data' => $target_data
        ];
    }

    private function __set_color_percentage($number){
        if($number < 80){
            return '<b class="text-danger">'.$number.'</b>';
        } else if($number < 90){
            return '<b class="text-warning">'.$number.'</b>';
        } else if($number < 100){
            return '<b class="text-success">'.$number.'</b>';
        } else {
            return '<b class="text-primary">'.$number.'</b>';
        }
    }

}
