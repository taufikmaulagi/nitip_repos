<?php

function kp_maintena($pasien, $fee_pasien, $ap_original){
    if($pasien >= 10 && $fee_pasien > 200000 && $ap_original == 1){
        return 1;
    }
    return 2;
}

function sd_maintena($pasien){
    if($pasien <= 0){
        return 'NU';
    } else if($pasien <= 4){
        return 'USE';
    } else {
        return 'CONFIRM';
    }
}

function cm_maintena($potensi, $status){
    if($potensi == 1 && $status == 'CONFIRM'){
        return 'A';
    } else if($potensi == 1 && $status == 'USE'){
        return 'B1';
    } else if($potensi == 1 && $status == 'NU'){
        return 'B0';
    } else if($potensi == 2 && $status == 'CONFIRM'){
        return 'C';
    } else if($potensi == 2 && $status == 'USE'){
        return 'D';
    } else if($potensi == 2 && $status == 'NU'){
        return 'D';
    }
}

function kp_rexulti($pasien, $fee_pasien, $ap_original){
    if($pasien >= 10 && $fee_pasien > 200000 && $ap_original == 1){
        return 1;
    }
    return 2;
}

function sd_rexulti($pasien){
    if($pasien <= 0){
        return 'NU';
    } else if($pasien <= 2){
        return 'USE';
    } else {
        return 'CONFIRM';
    }
}

function cm_rexulti($potensi, $status){
    if($potensi == 1 && $status == 'CONFIRM'){
        return 'A';
    } else if($potensi == 1 && $status == 'USE'){
        return 'B1';
    } else if($potensi == 1 && $status == 'NU'){
        return 'B0';
    } else if($potensi == 2 && $status == 'CONFIRM'){
        return 'C';
    } else if($potensi == 2 && $status == 'USE'){
        return 'D';
    } else if($potensi == 2 && $status == 'NU'){
        return 'D';
    }
}