<?php

function get_potensi_value($pgrup, $produk, $potensi){

    $potensi_value = NULL;
    $id = NULL;

    $product_price = [];

    if($pgrup=='EH'){
        $product_price = get_price_data('MUP','001','6');
    }elseif($pgrup=='EJ'){
        $product_price = get_price_data('MUP','001',$produk);
    }elseif($pgrup=='EI'){
        $product_price = get_price_data('MUP','001',$produk);
    }elseif($pgrup=='EL'){
        $product_price = get_price_data('APP','001',$produk);
    }elseif($pgrup=='EG'){
        $product_price = get_price_data('MUP','001',$produk);
    }elseif($pgrup=='ED'){
        $product_price = get_price_data('MUP','001','5');
    }elseif($pgrup=='EK'){
        $product_price = get_price_data('MUP','001',$produk);
    }elseif($pgrup=='EA'){
        $product_price = get_price_data('MUP','001',$produk);
    }
    
    if(!empty($product_price) > 0){
        $potensi_value = $potensi * $product_price->hjp;
        $id = $product_price->id_pricelist;
    }

    return [
        'potensi_value' => $potensi_value,
        'id' => $id
    ];

}

function get_price_data($kode_distributor, $kode_sector, $produk){

    $CI =& get_instance();

    return get_data('pricelist_detail', [
        'where' => [
            'kode_distributor' => $kode_distributor,
            'kode_sector' => $kode_sector,
            'id_produk_oi' => $produk
        ]
    ])->row();

}


function get_price_detail($produk){

    $CI =& get_instance();

    $data = get_data('pricelist_detail', [
        'where' => [
            'kode_distributor' => 'MUP',
            'kode_sector' => '001',
            'id_produk_oi' => $produk
        ],
        'sort_by' => 'tanggal',
        'sort' => 'desc'
    ])->row_array();

    if(!empty($data)){
        return $data['hjp'];
    } else {
        return 0;
    }

}

function active_cycle(){

    $month = date('m');

    if(in_array($month, ['01','02','03','04'])){
        return 1;
    } else if(in_array($month, ['05','06','07','08'])){
        return 2;
    } else if(in_array($month, ['09','10','11','12'])){
        return 3;
    }

}

function get_kriteria_potensi($produk_grup, $jumlah_pasien, $type = 'A', $fee_patient = 0, $ap_original = 0){
    $CI =& get_instance();

    $jumlah_pasien = $jumlah_pasien ? $jumlah_pasien : 0;
    
    $CI->db->where('\''.$jumlah_pasien.'\' between min_pasien and max_pasien');
    if($type == 'B'){
        $CI->db->where(($fee_patient ? $fee_patient : '0').' between min_fee_patient and max_fee_patient');
        $CI->db->where('ap_original = '.($ap_original == NULL ? 0 : $ap_original));
    }
    $data = $CI->db->get_where('rumus_kriteria_potensi',
        [
            'produk_grup' => $produk_grup
        ]
    )->row_array();

    // echo $CI->db->last_query(); die;

    return !empty($data['potensi']) ? $data['potensi'] : 2;
}

function get_status_dokter($produk_grup, $jumlah_pasien){
    $CI =& get_instance();

    $jumlah_pasien = $jumlah_pasien ? $jumlah_pasien : 0;
    
    $CI->db->where($jumlah_pasien.' between min_pasien and max_pasien');
    $data = $CI->db->get_where('rumus_status_dokter', 
        [
            'produk_grup' => $produk_grup
        ]
    )->row_array();

    return !empty($data['status']) ? $data['status'] : 'NU';
}

function get_customer_matrix($produk_grup, $status_dokter, $kriteria_potensi){
    $CI =& get_instance();
    
    $data = get_data('rumus_customer_matrix', [
        'where' => [
            'produk_grup' => $produk_grup,
            'status_dokter' => $status_dokter,
            'potensi' => $kriteria_potensi
        ]
    ])->row_array();

    return !empty($data['matrix']) ? $data['matrix'] : 'D';
}