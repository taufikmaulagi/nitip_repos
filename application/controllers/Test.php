<?php

class Test extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $data = get_data('history_organogram_detail', [
            'select' => 'history_organogram.*',
            'join' => [
                'history_organogram on history_organogram.id = history_organogram_detail.id_history_organogram',
            ],
            'where' => [
                'n_am' => '02000',
                'tanggal_end' => '0000-00-00',
            ],
            'group_by' => 'history_organogram.id',
        ])->result_array();
        debug($data);
    }

    function data_sales()
    {
        $html_data_sales = '';
        $html_data_sales .=  '<table class="table" border="1">';
        $html_data_sales .=  '<tr><th rowspan="3"></th>';
	        $html_data_sales .=  '<th class="text-center" colspan="65">2022</th>';
	        $html_data_sales .=  '</tr><tr>';
	        for($i=1;$i<=12;$i++) {
	            $html_data_sales .=  '<th class="text-center" colspan="5">'.$i.'</th>';
	        }
	        $html_data_sales .=  '<th class="text-center" colspan="5">Total</th>';
	        $html_data_sales .=  '</tr><tr>';
	        for($i=1;$i<=13;$i++) {
	            $html_data_sales .=  '<th class="text-center">'.'Target'.'</th>';
	            $html_data_sales .=  '<th class="text-center">'.'Actual'.'</th>';
	            $html_data_sales .=  '<th class="text-center">'.(2022 - 1).'</th>';
	            $html_data_sales .=  '<th class="text-center">'.'Achievment'.'</th>';
	            $html_data_sales .=  '<th class="text-center">'.'Growth'.'</th>';
	        }
	        $html_data_sales .=  '</tr>';
        switch_database('siap');

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
                'b.tanggal_end' => '0000-00-00'
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
            $html_data_sales .=  '<td><a href="'.$b->kode.'" class="usr">'.strtoupper($b->team).'</a></td>';
            $nip   = array();
            if(user('id_group') >= 4 && user('id_group') <= 9) {
                $arr_mr    = array(
                    'select'       => 'DISTINCT(n_mr) AS n_mr',
                    'where'  => array(
                        'n_mr !='      => ''
                    ),
                    'where_field'      => '`tanggal` = (SELECT MAX(`tanggal`) FROM '.'tbl_history_organogram_detail'.' WHERE `tanggal` <= "'.(2022).'-'.date('m').'-01" AND `kode_team` = "'.$b->kode.'")',
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
                        'tahun'        => 2022,
                        'kode_team'    => $b->kode,
                        'kode_divisi'  => 'E'
                    )
                );
                if(count($nip) > 0) {
                    $arr_target['where_in']['nip'] = $nip;
                }
                $tar   = get_data('tbl_target_field_force',$arr_target)->row();
                $target[$j]    = $tar->jml;
                $html_data_sales .=  '<td class="text-right">'.number_format($target[$j]).'</td>';
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
                
                $table = 'tbl_transaksi'.'_'.(2022).sprintf('%02d',$j);
                if(table_exists($table)) {
                    $jumlah    = get_data($table,$arr)->row();
                
                    $total[$j]  = $jumlah->jml;
                    if($total[$j] >= 0) {
                        $html_data_sales .=  '<td class="text-right">'.number_format($total[$j]).'</td>';
                    } else {
                        $html_data_sales .=  '<td class="text-right" style="color: #933;">('.number_format($total[$j]).')</td>';
                    }
                } else {
                    $total[$j] = 0;
                    $html_data_sales .=  '<td class="text-center">-</td>';
                }
                $table = 'tbl_transaksi'.'_'.(2022 - 1).sprintf('%02d',$j);
                if(table_exists($table)) {
                    $jumlah    = get_data($table,$arr)->row();
                
                    $last[$j]  = $jumlah->jml;
                    if($last[$j] >= 0) {
                        $html_data_sales .=  '<td class="text-right">'.number_format($last[$j]).'</td>';
                    } else {
                        $html_data_sales .=  '<td class="text-right" style="color: #933;">('.number_format($last[$j]).')</td>';
                    }
                } else {
                    $last[$j] = 0;
                    $html_data_sales .=  '<td class="text-center">-</td>';
                }
                if($target[$j] == 0) {
                    $html_data_sales .=  '<td class="text-center">-</td>';
                } else {
                    $ach   = ( $total[$j] / $target[$j] )  * 100;
                    $html_data_sales .=  '<td class="text-center">'.number_format($ach,2,'.','').'%</td>';
                }
                if($last[$j] == 0) {
                    $html_data_sales .=  '<td class="text-center">-</td>';
                } else {
                    $grw   = ( ( $total[$j] - $last[$j] ) / $last[$j] )  * 100;
                    $html_data_sales .=  '<td class="text-center">'.number_format($grw,2,'.','').'%</td>';
                }
            }
            $html_data_sales .=  '<td class="text-right">'.number_format(array_sum($target)).'</td>';
            $html_data_sales .=  '<td class="text-right">'.number_format(array_sum($total)).'</td>';
            $html_data_sales .=  '<td class="text-right">'.number_format(array_sum($last)).'</td>';
            if(array_sum($target) == 0) {
                $html_data_sales .=  '<td class="text-center">-</td>';
            } else {
                $ach   = ( array_sum($total) / array_sum($target) )  * 100;
                $html_data_sales .=  '<td class="text-center">'.number_format($ach,2,'.','').'%</td>';
            }
            if(array_sum($last) == 0) {
                $html_data_sales .=  '<td class="text-center">-</td>';
            } else {
                $grw   = ( (array_sum($total) - array_sum($last)) / array_sum($last) )  * 100;
                $html_data_sales .=  '<td class="text-center">'.number_format($grw,2,'.','').'%</td>';
            }
            $html_data_sales .=  '</tr>';
        }

        $html_data_sales .=  '</table>';

        return $html_data_sales;
    }


}
