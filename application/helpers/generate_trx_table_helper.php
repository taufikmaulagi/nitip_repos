<?php

function init_table_prof($cycle, $tahun)
{

  $ci = &get_instance();

  $ci->db->query('create table ' . 'trxprof_' . $tahun . '_' . $cycle . ' (
        id mediumint unsigned PRIMARY KEY AUTO_INCREMENT,
        dokter mediumint unsigned NOT NULL,
        outlet mediumint unsigned DEFAULT NULL,
        tempat_praktek smallint(6) DEFAULT NULL,
        produk varchar(10) default NULL,
        produk_grup varchar(10) NOT NULL,
        produk_subgrup varchar(10) default NULL,
        price mediumint(8) UNSIGNED DEFAULT NULL,
        mr varchar(7) NOT NULL,
        am varchar(7) DEFAULT NULL,
        rm varchar(7) DEFAULT NULL,
        nsm varchar(7) DEFAULT NULL,
        asdir varchar(7) DEFAULT NULL,
        bud varchar(7) DEFAULT NULL,
        nama_dokter varchar(55) DEFAULT NULL,
        spesialist smallint unsigned default null,
        nama_spesialist varchar(35) DEFAULT NULL,
        sub_spesialist smallint unsigned default null,
        nama_subspesialist varchar(55) default null,
        nama_produk_sub_grup varchar(35) DEFAULT NULL,
        nama_produk varchar(45) DEFAULT NULL,
        nama_outlet varchar(50) DEFAULT NULL,
        nama_produk_grup varchar(35) DEFAULT NULL,
        nama_branch varchar(30) DEFAULT NULL,
        channel_outlet enum("Goverment Hospital","Private Hospital","Apotek") DEFAULT NULL,
        tipe_pasien enum("Regular","Non Regular") DEFAULT NULL,
        jumlah_pasien smallint(6) NOT NULL,
        status enum("1","2","3") DEFAULT "1",
        apprv_at datetime DEFAULT NULL,
        alasan_not_approve varchar(150) DEFAULT NULL,
        cycle tinyint(4) NOT NULL,
        tahun year(4) NOT NULL,
        keterangan varchar(250) DEFAULT NULL,
        marketing_bulan_1 varchar(75) DEFAULT NULL,
        marketing_bulan_2 varchar(75) DEFAULT NULL,
        marketing_bulan_3 varchar(75) DEFAULT NULL,
        marketing_bulan_4 varchar(75) DEFAULT NULL,
        cat datetime NOT NULL DEFAULT current_timestamp(),
        uat datetime DEFAULT NULL,
        dat datetime DEFAULT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;');
}

function init_table_visit_plan($tahun, $bulan){

  $ci = &get_instance();

  $ci->db->query('create table trxvisit_' . $tahun . '_' . sprintf('%02d', $bulan) . ' (
            id mediumint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
            profiling mediumint unsigned NOT NULL,
            outlet mediumint unsigned DEFAULT NULL,
            mr varchar(7) NOT NULL,
            am varchar(7) DEFAULT NULL,
            rm varchar(7) DEFAULT NULL,
            nsm varchar(7) DEFAULT NULL,
            asdir varchar(7) DEFAULT NULL,
            bud varchar(7) DEFAULT NULL,
            week1 tinyint(4) DEFAULT NULL,
            week2 tinyint(4) DEFAULT NULL,
            week3 tinyint(4) DEFAULT NULL,
            week4 tinyint(4) DEFAULT NULL,
            week5 tinyint(4) DEFAULT NULL,
            week6 tinyint(4) DEFAULT NULL,
            standard_call tinyint(4) DEFAULT NULL,
            plan_call tinyint(4) DEFAULT NULL,
            dokter mediumint unsigned default null,
            nama_dokter varchar(55) DEFAULT NULL,
            nama_spesialist varchar(35) DEFAULT NULL,
            nama_produk_sub_grup varchar(35) DEFAULT NULL,
            nama_produk varchar(45) DEFAULT NULL,
            nama_outlet varchar(50) DEFAULT NULL,
            produk_grup varchar(25) DEFAULT NULL,
            produk varchar(25) DEFAULT NULL,
            spesialist smallint unsigned default null,
            produk_subgrup varchar(25) DEFAULT NULL,
            nama_produk_grup varchar(35) DEFAULT NULL,
            nama_branch varchar(30) DEFAULT NULL,
            appvr_at date DEFAULT NULL,
            bulan mediumint(9) NOT NULL,
            tahun year(4) NOT NULL,
            status enum("1","2","3","4","5") DEFAULT NULL,
            keterangan varchar(250) DEFAULT NULL,
            marketing_program varchar(75) DEFAULT NULL,
            marketing_aktifitas varchar(75) DEFAULT NULL,
            alasan_not_approve varchar(150) DEFAULT NULL,
            cat datetime NOT NULL DEFAULT current_timestamp(),
            uat datetime DEFAULT NULL,
            dat datetime DEFAULT NULL
    )  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;');
}

function init_table_dfr($tahun, $bulan)
{

  $ci = &get_instance();

  $ci->db->query('CREATE TABLE trxdfr_' . $tahun . '_' . $bulan . ' (
        id mediumint NOT NULL PRIMARY KEY AUTO_INCREMENT,
        visit_plan int(11) NOT NULL,
        mr varchar(7) NOT NULL,
        am varchar(7) DEFAULT NULL,
        rm varchar(7) DEFAULT NULL,
        nsm varchar(7) DEFAULT NULL,
        asdir varchar(7) DEFAULT NULL,
        bud varchar(7) DEFAULT NULL,
        produk_grup varchar(15) DEFAULT NULL,
        produk varchar(15) DEFAULT NULL,
        produk2 varchar(15) DEFAULT NULL,
        produk3 varchar(15) DEFAULT NULL,
        outlet mediumint unsigned default null,
        dokter mediumint unsigned default null,
        nama_dokter varchar(75) default null,
        nama_produk_group varchar(25) default null,
        nama_produk varchar(35) default null,
        nama_outlet varchar(75) default null,
        channel_outlet varchar(30) default null,
        nama_kompetitor_diresepkan varchar(35) default null,
        circumstances varchar(500) default null,
        feedback_status varchar(75) default NULL,
        feedback_dokter varchar(500) default NULL,
        feedback_dokter2 varchar(500) default NULL,
        feedback_dokter3 varchar(500) default NULL,
        feedback_am varchar(500) default NULL,
        feedback_rm varchar(500) default NULL,
        mr_talk varchar(500) default NULL,
        mr_talk2 varchar(500) default NULL,
        mr_talk3 varchar(500) default NULL,
        kriteria_potensi varchar(5) default null,
        status_dokter varchar(5) default null,
        matrix varchar(5) default null,
        matrix_rexulti varchar(5) default null,
        matrix_maintena varchar(5) default null,
        call_object varchar(500) DEFAULT NULL,
        next_action varchar(500) DEFAULT NULL,
        call_type tinyint(3) DEFAULT NULL,
        nama_call_type varchar(30) default null,
        sub_call_type smallint(6) DEFAULT NULL,
        nama_sub_call_type varchar(75) default null,
        key_message smallint(6) DEFAULT NULL,
        nama_key_message varchar(300) default null,
        kompetitor_diresepkan smallint(6) DEFAULT NULL,
        kompetitor_diresepkan_lainnya varchar(300) DEFAULT NULL,
        indikasi smallint(6) DEFAULT NULL,
        nama_indikasi varchar(35) default null,
        indikasi_lainnya varchar(300) DEFAULT NULL,
        penilaian tinyint(4) DEFAULT NULL,
        nip varchar(10) NOT NULL,
        status tinyint(4) DEFAULT NULL,
        marketing_program mediumint(9) DEFAULT NULL,
        cat datetime NOT NULL DEFAULT current_timestamp(),
        uat datetime DEFAULT NULL,
        dat datetime DEFAULT NULL
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;');
}

function init_table_data_actual($tahun, $bulan){
  $ci = &get_instance();
  $ci->db->query('CREATE TABLE trxdact_' . $tahun . '_' . $bulan . ' (
        id mediumint unsigned NOT NULL primary key auto_increment,
        visit_plan mediumint unsigned default NULL,
        mr varchar(7) NOT NULL,
        am varchar(7) DEFAULT NULL,
        rm varchar(7) DEFAULT NULL,
        nsm varchar(7) DEFAULT NULL,
        asdir varchar(7) DEFAULT NULL,
        bud varchar(7) DEFAULT NULL,
        dokter mediumint unsigned default null,
        nama_dokter varchar(55) default null,
        spesialist smallint unsigned default null,
        nama_spesialist varchar(55) default null,
        sub_spesialist smallint unsigned default null,
        nama_subspesialist varchar(55) default null,
        outlet mediumint unsigned default null,
        nama_outlet varchar(55) default null,
        produk_grup varchar(15) default null,
        nama_produk_grup varchar(35) default null,
        produk_subgrup varchar(15) default null,
        nama_produk_subgrup varchar(50) default null,
        customer_matrix varchar(5) default \'D\',
        customer_matrix_rexulti varchar(5) default \'D\',
        customer_matrix_maintena varchar(5) default \'D\',
        potensi tinyint unsigned default null,
        status_dokter varchar(10) default \'NU\',
        kriteria_potensi varchar(3) default null,
        potensi_maintena tinyint unsigned default null,
        status_dokter_maintena varchar(10) default \'NU\',
        kriteria_potensi_maintena varchar(3) default null,
        potensi_rexulti tinyint unsigned default null,
        status_dokter_rexulti varchar(10) default \'NU\',
        kriteria_potensi_rexulti varchar(3) default null,
        jumlah_pasien smallint unsigned default 0,
        total_value int unsigned default 0,
        jumlah_pasien_maintena smallint unsigned default 0,
        total_value_maintena int unsigned default 0,
        jumlah_pasien_rexulti smallint unsigned default 0,
        total_value_rexulti int unsigned default 0,
        hjp int(11) NOT NULL,
        bulan tinyint(4) DEFAULT NULL,
        tahun year(4) DEFAULT NULL,
        unit smallint(6) DEFAULT NULL,
        other_ap_original smallint(6) DEFAULT NULL,
        total_alai smallint(6) DEFAULT NULL,
        total_tlai smallint(6) DEFAULT NULL,
        cat datetime NOT NULL DEFAULT current_timestamp(),
        uat datetime DEFAULT NULL,
        dat datetime DEFAULT NULL
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1');

  $ci->db->query('CREATE TABLE trxdact_sku_' . $tahun . '_' . $bulan . ' (
        data_sales mediumint unsigned not null,
        produk varchar(20) default NULL,
        nama_produk varchar(55) default null,
        number_of_unit smallint unsigned default null,
        value_1 smallint unsigned default null,
        value_2 smallint unsigned default null,
        value_3 smallint unsigned default null,
        value_4 smallint unsigned default null,
        value_5 smallint unsigned default null,
        price int unsigned default null
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1');

  $ci->db->query('CREATE TABLE trxdact_marketing_' . $tahun . '_' . $bulan . ' (
        data_sales mediumint unsigned not null,
        sub_marketing_aktifitas smallint default null,
        nama_sub_marketing_aktifitas varchar(75) default null,
        marketing_aktifitas smallint unsigned default NULL,
        nama_marketing_aktifitas varchar(55) default null,
        tanggal date default null,
        tipe enum("Online","Offline") default "Offline",
        sebagai enum("Pembicara","Peserta") default "Pembicara",
        nama_pembicara varchar(150) default null,
        persepsi_sebelum varchar(150) default null,
        persepsi_setelah varchar(150) default null
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1');
}

function init_table_marketing($tahun, $bulan)
{
  $ci = &get_instance();
  $ci->db->query('CREATE TABLE trxdact_marketing_' . $tahun . '_' . $bulan . ' (
      data_sales mediumint unsigned not null,
      sub_marketing_aktifitas smallint default null,
      nama_sub_marketing_aktifitas varchar(75) default null,
      marketing_aktifitas smallint unsigned default NULL,
      nama_marketing_aktifitas varchar(55) default null,
      tanggal date default null,
      tipe enum("Online","Offline") default "Offline",
      sebagai enum("Pembicara","Peserta") default "Pembicara",
      nama_pembicara varchar(150) default null,
      persepsi_sebelum varchar(150) default null,
      persepsi_setelah varchar(150) default null
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1');
}

function init_table_dfr_feedback($tahun, $bulan)
{
  $ci = &get_instance();
  $ci->db->query('CREATE TABLE trxdfr_feedback_' . $tahun . '_' . $bulan . ' (
    dfr mediumint unsigned not null,
    id_group tinyint unsigned not null,
    user int unsigned not null,
    penilaian varchar(50) not null,
    alasan_belum_sesuai varchar(150) default null,
    cat datetime default current_timestamp
  )');
}

function init_table_prof_indikasi($cycle, $tahun)
{
  $ci = &get_instance();
  $ci->db->query('CREATE TABLE trxprof_indikasi_' . $tahun . '_' . $cycle . ' (
    profiling mediumint(8) UNSIGNED NOT NULL,
    indikasi_1 smallint(5) DEFAULT NULL,
    indikasi_2 smallint(5) DEFAULT NULL,
    indikasi_3 smallint(5) DEFAULT NULL,
    indikasi_4 smallint(5) DEFAULT NULL,
    indikasi_5 smallint(5) DEFAULT NULL,
    indikasi_6 smallint(5) DEFAULT NULL,
    indikasi_7 smallint(5) DEFAULT NULL,
    indikasi_8 smallint(5) DEFAULT NULL,
    indikasi_9 smallint(5) DEFAULT NULL,
    indikasi_10 smallint(5) DEFAULT NULL,
    indikasi_11 smallint(5) DEFAULT NULL,
    indikasi_12 smallint(5) DEFAULT NULL,
    indikasi_13 smallint(5) DEFAULT NULL,
    indikasi_14 smallint(5) DEFAULT NULL,
    indikasi_15 smallint(5) DEFAULT NULL,
    indikasi_16 smallint(5) DEFAULT NULL,
    indikasi_17 smallint(5) DEFAULT NULL,
    indikasi_18 smallint(5) DEFAULT NULL,
    indikasi_19 smallint(5) DEFAULT NULL,
    indikasi_20 smallint(5) DEFAULT NULL,
    val_indikasi_1 smallint(5) DEFAULT NULL,
    val_indikasi_2 smallint(5) DEFAULT NULL,
    val_indikasi_3 smallint(5) DEFAULT NULL,
    val_indikasi_4 smallint(5) DEFAULT NULL,
    val_indikasi_5 smallint(5) DEFAULT NULL,
    val_indikasi_6 smallint(5) DEFAULT NULL,
    val_indikasi_7 smallint(5) DEFAULT NULL,
    val_indikasi_8 smallint(5) DEFAULT NULL,
    val_indikasi_9 smallint(5) DEFAULT NULL,
    val_indikasi_10 smallint(5) DEFAULT NULL,
    val_indikasi_11 smallint(5) DEFAULT NULL,
    val_indikasi_12 smallint(5) DEFAULT NULL,
    val_indikasi_13 smallint(5) DEFAULT NULL,
    val_indikasi_14 smallint(5) DEFAULT NULL,
    val_indikasi_15 smallint(5) DEFAULT NULL,
    val_indikasi_16 smallint(5) DEFAULT NULL,
    val_indikasi_17 smallint(5) DEFAULT NULL,
    val_indikasi_18 smallint(5) DEFAULT NULL,
    val_indikasi_19 smallint(5) DEFAULT NULL,
    val_indikasi_20 smallint(5) DEFAULT NULL,
    potensi_tablet smallint(5) UNSIGNED NOT NULL,
    jumlah_pasien smallint(5) UNSIGNED NOT NULL,
    fee_patient int unsigned default null,
    ap_original tinyint(1) unsigned default null,
    marketing_aktifitas varchar(75) DEFAULT NULL,
    bulan varchar(2) NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;');
}

function init_table_raw_data($tahun, $bulan, $produk_grup)
{
  $ci = &get_instance();

  // $produk_grup = get_data('produk_grup',[
  //   'where' => [
  //     'is_active' => 1,
  //     'kode_divisi' => 'E'
  //   ]
  // ])->result_array();
  // foreach($produk_grup as $produk_grup){

  $produk = get_data('produk', [
    'select' => 'produk.*',
    'join' => [
      'produk_subgrup on produk_subgrup.kode = produk.kode_subgrup',
    ],
    'where' => [
      'kode_grup' => $produk_grup,
      'produk.is_active' => 1,
    ]
  ])->result_array();

  $adt_pasien = '';
  if ($produk_grup == 'EH') {
    $adt_pasien .= 'pasien_maintena_1 smallint(5) unsigned default null,
                      pasien_maintena_2 smallint(5) unsigned default null,
                      pasien_rexulti_1 smallint(5) unsigned default null,
                      use_confirm_maintena varchar(10) default null,
                      use_confirm_rexulti varchar(10) default null,
                      customer_matrix_maintena varchar(10) default null,
                      customer_matrix_rexulti varchar(10) default null,';
  }

  $query_produk = '';
  $count = 1;
  foreach ($produk as $key => $value) {
    $query_produk .= $value['id'] . '_produk mediumint unsigned default null,';

    if ($count != count($produk)) {
      $query_produk .= $value['id'] . '_price mediumint unsigned default null,';
    } else {
      $query_produk .= $value['id'] . '_price mediumint unsigned default null';
    }
    $count++;
  }
  $query_produk = $query_produk ? ', ' . $query_produk : '';
  if (!table_exists('raw_data_' . $produk_grup . '_' . $tahun . '_' . $bulan)) {
    $ci->db->query('drop table if exists raw_data_' . $produk_grup . '_' . $tahun . '_' . $bulan);
  }
  $ci->db->query('create table raw_data_' . $produk_grup . '_' . $tahun . '_' . $bulan . ' (
      id int unsigned not null primary key auto_increment,
      profiling mediumint unsigned not null,
      mr varchar(10) default null,
      produk_grup varchar(10) default null,
      nama_mr varchar(55) default null,
      nama_dokter varchar(55) default null,
      nama_outlet varchar(55) default null,
      nama_spesialist varchar(55) default null,
      indikasi_1 smallint(5) unsigned default null,
      indikasi_2 smallint(5) unsigned default null,
      indikasi_3 smallint(5) unsigned default null,
      indikasi_4 smallint(5) unsigned default null,
      indikasi_5 smallint(5) unsigned default null,
      indikasi_6 smallint(5) unsigned default null,
      indikasi_7 smallint(5) unsigned default null,
      indikasi_8 smallint(5) unsigned default null,
      indikasi_9 smallint(5) unsigned default null,
      indikasi_10 smallint(5) unsigned default null,
      total_potensi smallint(5) unsigned default null,
      pasien_1 smallint(5) unsigned default null,
      pasien_2 smallint(5) unsigned default null,
      pasien_3 smallint(5) unsigned default null,
      pasien_4 smallint(5) unsigned default null,
      pasien_5 smallint(5) unsigned default null,
      pasien_6 smallint(5) unsigned default null,
      pasien_7 smallint(5) unsigned default null,
      pasien_8 smallint(5) unsigned default null,
      pasien_9 smallint(5) unsigned default null,
      pasien_10 smallint(5) unsigned default null,
      ' . $adt_pasien . '
      total_pasien smallint(5) unsigned default null,
      plan_call smallint(5) unsigned default null,
      actual_call smallint(5) unsigned default null,
      percent_call smallint(5) unsigned default null,
      plan_dokter_coverage smallint(5) unsigned default null,
      actual_dokter_coverage smallint(5) unsigned default null,
      percent_dokter_coverage smallint(5) unsigned default null,
      plan_percent_coverage smallint(5) unsigned default null,
      actual_percent_coverage smallint(5) unsigned default null,
      percent_percent_coverage smallint(5) unsigned default null,
      use_confirm varchar(10) default null,
      customer_matrix varchar(10) default null,
      call_type_a smallint(5) unsigned default null,
      call_type_b smallint(5) unsigned default null,
      call_type_c smallint(5) unsigned default null,
      sub_call_type_a varchar(200) default null,
      sub_call_type_b varchar(200) default null,
      sub_call_type_c varchar(200) default null
      ' . $query_produk . '
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1');
  // }
}

function create_lcv($tahun, $cycle)
{
  $ci = &get_instance();
  $ci->db->query('create table trxlcv_' . $tahun . '_' . $cycle . ' (
    id MEDIUMINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    tipe ENUM("DOKTER","KPDM") DEFAULT "DOKTER" NOT NULL,
    am varchar(10) NOT NULL,
    nsm varchar(10) NOT NULL,
    produk_grup varchar(10) NOT NULL,
    status tinyint default 1 not null,
    cat datetime NOT NULL DEFAULT current_timestamp(),
      uat datetime DEFAULT NULL,
      dat datetime DEFAULT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1');
}
