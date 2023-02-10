<?php

function init_table_rekap_call_activity($tahun, $bulan){

    $ci = &get_instance();
    $ci->db->query('CREATE TABLE rekap_call_activity_' . $tahun . ' (
        id mediumint unsigned primary key auto_increment,
        mr varchar(7) not null,
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