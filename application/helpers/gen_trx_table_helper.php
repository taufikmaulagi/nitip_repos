<?php

function init_table_prof($cycle, $tahun)
{
    $CI = &get_instance();

    $CI->db->query(
        '
        create table ' . 'trxprof_' . $tahun . '_' . $cycle . ' (
        id mediumint unsigned PRIMARY KEY AUTO_INCREMENT,
        dokter mediumint unsigned NOT NULL,
        outlet mediumint unsigned DEFAULT NULL,
        tempat_praktek smallint(6) DEFAULT NULL,
        produk_grup varchar(10) NOT NULL,
        mr varchar(7) NOT NULL,
        branch smallint UNSIGNED DEFAULT NULL,
        channel_outlet enum("Goverment Hospital","Private Hospital","Apotek") DEFAULT NULL,
        tipe_pasien enum("Regular","Non Regular") DEFAULT NULL,
        jumlah_pasien_perbulan smallint(6) NOT NULL,
        status enum("UNSUBMITTED","WAITING","APPROVED","NOT APPROVED") DEFAULT "UNSUBMITTED",
        indikasi_1 smallint unsigned default NULL,
        indikasi_2 smallint unsigned default NULL,
        indikasi_3 smallint unsigned default NULL,
        indikasi_4 smallint unsigned default NULL,
        indikasi_5 smallint unsigned default NULL,
        indikasi_6 smallint unsigned default NULL,
        indikasi_7 smallint unsigned default NULL,
        indikasi_8 smallint unsigned default NULL,
        indikasi_9 smallint unsigned default NULL,
        indikasi_10 smallint unsigned default NULL,
        indikasi_11 smallint unsigned default NULL,
        indikasi_12 smallint unsigned default NULL,
        indikasi_13 smallint unsigned default NULL,
        indikasi_14 smallint unsigned default NULL,
        indikasi_15 smallint unsigned default NULL,
        indikasi_16 smallint unsigned default NULL,
        indikasi_17 smallint unsigned default NULL,
        indikasi_18 smallint unsigned default NULL,
        indikasi_19 smallint unsigned default NULL,
        indikasi_20 smallint unsigned default NULL,
        val_indikasi_1 smallint unsigned default 0,
        val_indikasi_2 smallint unsigned default 0,
        val_indikasi_3 smallint unsigned default 0,
        val_indikasi_4 smallint unsigned default 0,
        val_indikasi_5 smallint unsigned default 0,
        val_indikasi_6 smallint unsigned default 0,
        val_indikasi_7 smallint unsigned default 0,
        val_indikasi_8 smallint unsigned default 0,
        val_indikasi_9 smallint unsigned default 0,
        val_indikasi_10 smallint unsigned default 0,
        val_indikasi_11 smallint unsigned default 0,
        val_indikasi_12 smallint unsigned default 0,
        val_indikasi_13 smallint unsigned default 0,
        val_indikasi_14 smallint unsigned default 0,
        val_indikasi_15 smallint unsigned default 0,
        val_indikasi_16 smallint unsigned default 0,
        val_indikasi_17 smallint unsigned default 0,
        val_indikasi_18 smallint unsigned default 0,
        val_indikasi_19 smallint unsigned default 0,
        val_indikasi_20 smallint unsigned default 0,
        cat datetime NOT NULL DEFAULT current_timestamp(),
        uat datetime DEFAULT NULL ON UPDATE current_timestamp(),
        dat datetime DEFAULT NULL)'
    );
}

    function init_table_visit_plan($tahun, $bulan)
    {
        $CI = &get_instance();

        $CI->db->query('create table trxvisit_' . $tahun . '_' . $bulan . ' (
            id mediumint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
            profiling mediumint unsigned NOT NULL,
            week1 tinyint(4) UNSIGNED DEFAULT NULL,
            week2 tinyint(4) UNSIGNED DEFAULT NULL,
            week3 tinyint(4) UNSIGNED DEFAULT NULL,
            week4 tinyint(4) UNSIGNED DEFAULT NULL,
            week5 tinyint(4) UNSIGNED DEFAULT NULL,
            week6 tinyint(4) UNSIGNED DEFAULT NULL,
            status enum("UNSUBMITTED","WAITING","APPROVED","REVISION") DEFAULT "UNSUBMITTED",
            note text DEFAULT NULL,
            cat datetime NOT NULL DEFAULT current_timestamp(),
            uat datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
            dat datetime DEFAULT NULL)');
    }

    function init_table_dfr($tahun, $bulan)
    {

        $CI = &get_instance();

        $CI->db->query(
            'CREATE TABLE trxdfr_' . $tahun . '_' . $bulan . ' (
            id mediumint NOT NULL PRIMARY KEY AUTO_INCREMENT,
            visit_plan mediumint unsigned NOT NULL,
            produk varchar(15) DEFAULT NULL,
            produk2 varchar(15) DEFAULT NULL,
            produk3 varchar(15) DEFAULT NULL,
            circumstances varchar(500) default null,
            feedback_status varchar(75) default NULL,
            feedback_dokter varchar(500) default NULL,
            feedback_dokter2 varchar(500) default NULL,
            feedback_dokter3 varchar(500) default NULL,
            mr_talk varchar(500) default NULL,
            mr_talk2 varchar(500) default NULL,
            mr_talk3 varchar(500) default NULL,
            kriteria_potensi varchar(5) default null,
            status_dokter varchar(5) default null,
            matrix varchar(5) default null,
            call_object varchar(500) DEFAULT NULL,
            next_action varchar(500) DEFAULT NULL,
            call_type enum("A","B","C") DEFAULT NULL,
            sub_call_type smallint(6) DEFAULT NULL,
            key_message smallint(6) DEFAULT NULL,
            kompetitor_diresepkan smallint(6) DEFAULT NULL,
            kompetitor_diresepkan_lainnya varchar(300) DEFAULT NULL,
            indikasi smallint(6) DEFAULT NULL,
            indikasi_lainnya varchar(300) DEFAULT NULL,
            status enum("CREATE","SENT") DEFAULT NULL,
            cat datetime NOT NULL DEFAULT current_timestamp(),
            uat datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
            dat datetime DEFAULT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;'
        );
    }

    function init_table_data_actual($tahun, $bulan)
    {
        $ci = &get_instance();
        $ci->db->query('CREATE TABLE trxdact_' . $tahun . '_' . $bulan . ' (
                id mediumint unsigned NOT NULL primary key auto_increment,
                visit_plan mediumint unsigned default NULL,
                customer_matrix varchar(5) default \'D\',
                status_dokter varchar(10) default \'NU\',
                kriteria_potensi varchar(3) default 2,
                jumlah_pasien smallint unsigned default 0,
                hjp int(11) NOT NULL,
                unit smallint(6) DEFAULT NULL,
                other_ap_original smallint(6) DEFAULT NULL,
                total_alai smallint(6) DEFAULT NULL,
                total_tlai smallint(6) DEFAULT NULL,
                cat datetime NOT NULL DEFAULT current_timestamp(),
                uat datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
                dat datetime DEFAULT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1');

        $ci->db->query('CREATE TABLE trxdact_sku_' . $tahun . '_' . $bulan . ' (
              data_sales mediumint unsigned not null,
              produk varchar(20) default NULL,
              number_of_unit smallint unsigned default null,
              value_1 smallint unsigned default null,
              value_2 smallint unsigned default null,
              value_3 smallint unsigned default null,
              value_4 smallint unsigned default null,
              value_5 smallint unsigned default null,
              value_6 smallint unsigned default null,
              value_7 smallint unsigned default null,
              value_8 smallint unsigned default null,
              value_9 smallint unsigned default null,
              value_10 smallint unsigned default null,
              price int unsigned default null
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1');

        $ci->db->query('CREATE TABLE trxdact_sku_adt_' . $tahun . '_' . $bulan . ' (
              data_sales mediumint unsigned not null,
              produk_group varchar(20) default NULL,
              produk varchar(20) default NULL,
              number_of_unit smallint unsigned default null,
              value_1 smallint unsigned default null,
              value_2 smallint unsigned default null,
              value_3 smallint unsigned default null,
              value_4 smallint unsigned default null,
              value_5 smallint unsigned default null,
              value_6 smallint unsigned default null,
              value_7 smallint unsigned default null,
              value_8 smallint unsigned default null,
              value_9 smallint unsigned default null,
              value_10 smallint unsigned default null,
              price int unsigned default null
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1');


        $ci->db->query('CREATE TABLE trxdact_marketing_' . $tahun . '_' . $bulan . ' (
              data_sales mediumint unsigned not null,
              sub_marketing_aktifitas smallint default null,
              marketing_aktifitas smallint unsigned default NULL,
              tanggal date default null,
              tipe enum("Online","Offline") default "Offline",
              sebagai enum("Pembicara","Peserta") default "Pembicara",
              nama_pembicara varchar(150) default null,
              persepsi_sebelum varchar(150) default null,
              persepsi_setelah varchar(150) default null
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1');
    }

    function init_table_dfr_feedback($tahun, $bulan){
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
