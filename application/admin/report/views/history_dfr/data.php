<?php if (!empty($dfr)) : ?>
    <style>
        .bg-yellow {
            background-color: #f5ea92 !important;
        }
    </style>
    <div class="table-responsive">
        <table class="table table-bordered table-app test-datatable">
            <thead>
                <th>#</th>
                <th>Tanggal</th>
                <th>Dokter</th>
                <th>Call Type</th>
                <th></th>
            </thead>
            <tbody>
                <?php $index = 1;
                foreach ($dfr as $val) : ?>
                    <tr>
                        <td style="width: 1px; white-space:nowrap;"><?= $index++ ?></td>
                        <td style="width: 1px; white-space:nowrap;"><?=strftime('%e %B %Y', strtotime($val['cat']))?></td>
                        <td><?=$val['nama_dokter']?></td>
    
                        <td><?php 
                            if($val['call_type'] == '1'){
                                echo 'A DFR';
                            } else if($val['call_type'] == '2'){
                                echo 'B Detailing';
                            } else {
                                echo 'C Happy Call';
                            }
                        ?></td>
                        
                        <td style="width: 1px; white-space:nowrap;" <?php
                            if(!empty($val['penilaian'])){
                                if($val['penilaian'] == 'Belum Sesuai dengan Tahapan OPSS'){
                                    echo 'class="bg-yellow"';
                                } else if($val['penilaian'] == 'Sesuai dengan Tahapan OPSS'){
                                    echo 'class="bg-success"';
                                }
                            }
                        ?>>    
                            <button class="btn btn-fresh btn-sm btn-feedback" data-id="<?=$val['id']?>"><i class="fa-inbox"></i>&nbsp; List Feedback</button>
                            <button class="btn btn-sky btn-sm btn-detail" data-id="<?=$val['id']?>"><i class="fa-search"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div class="text-center">
        <img src="<?= base_url('assets/images/no-data.svg') ?>" width="40%">
        <h3> Oops! Data Tidak Ditemukan :(</h3>
    </div>
<?php endif; ?>