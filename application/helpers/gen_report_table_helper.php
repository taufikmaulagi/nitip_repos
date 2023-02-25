<?php

function init_table_rekap_call_activity($tahun){

    $ci = &get_instance();
    $ci->db->query('CREATE TABLE rekap_call_activity_' . $tahun . ' (
        id mediumint unsigned primary key auto_increment,
        mr varchar(7) not null,
        kode_produk_grup varchar(10) not null,
        nama_produk_grup varchar(50) not null,
        bulan varchar(5) not null,
        id_dokter mediumint unsigned not null,
        nama_dokter varchar(75) not null,
        id_spesialist mediumint unsigned not null,
        nama_spesialist varchar(75) not null,
        id_sub_spesialist mediumint unsigned default null,
        nama_sub_spesialist varchar(75) default null,
        id_outlet mediumint unsigned not null,
        nama_outlet varchar(150) not null,
        plan_call smallint unsigned not null,
        actual_call smallint unsigned not null,
        dc_plan smallint unsigned not null,
        dc_actual smallint unsigned not null,
        pc_plan smallint unsigned not null,
        pc_actual smallint unsigned not null
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1');

}

function init_table_rekap_reply_dfr($tahun){

    $ci = &get_instance();
    $ci->db->query('CREATE TABLE rekap_reply_dfr_' . $tahun . ' (
        id mediumint unsigned primary key auto_increment,
        username varchar(7) not null,
        nama_user varchar(55) not null,
        kode_produk_grup varchar(10) not null,
        nama_produk_grup varchar(50) not null,
        bulan varchar(5) not null,
        dfr mediumint unsigned not null,
        tanggal datetime not null
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1');

}

function init_table_share_of_voice($tahun){
    $ci = &get_instance();
    $ci->db->query('CREATE TABLE rekap_share_of_voice_' . $tahun . ' (
        id mediumint unsigned primary key auto_increment,
        bulan varchar(2) not null,
        username varchar(7) not null,
        nama_user varchar(55) not null,
        p1 smallint unsigned not null,
        p2_1 smallint unsigned default 0,
        p2_2 smallint unsigned default 0,
        p2_3 smallint unsigned default 0,
        p2_4 smallint unsigned default 0,
        p2_5 smallint unsigned default 0,
        p3_1 smallint unsigned default 0,
        p3_2 smallint unsigned default 0,
        p3_3 smallint unsigned default 0,
        p3_4 smallint unsigned default 0,
        p3_5 smallint unsigned default 0
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1');
}