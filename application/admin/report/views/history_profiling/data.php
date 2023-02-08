<?php if (!empty($profiling)) : 
    $indikasi = get_data('indikasi', [
        'where' => [
            'produk_grup' => $profiling[0]['produk_grup'],
            'is_active' => 1
        ]
    ])->result_array();
?>
    <div class="table-responsive p-2">
        <table class="table table-bordered table-app datatable">
            <thead>
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Doctor Name</th>
                    <th rowspan="2">Specialist</th>
                    <th colspan="<?=count($indikasi)+1?>" class="text-center">Jumlah Potensi</th>
                    <th rowspan="2" class="text-center">Pasien / Bulan</th>
                    <th rowspan="2" class="text-center">Status</th>
                    <th rowspan="2"></th>
                </tr>
                <tr>
                    <?php foreach($indikasi as $val): ?>
                        <th style="width: 1px; white-space:nowrap"><?=$val['nama']?></th>
                    <?php endforeach; ?>
                    <th>Total Potensi</th>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1;
                foreach ($profiling as $val) : ?>
                    <tr>
                        <td style="width: 1px; white-space:nowrap; background-color:white"><?= $index++ ?></td>
                        <td style="width: 1px; white-space:nowrap; background-color:white"><?= $val['nama_dokter'] ?></td>
                        <td><?= $val['nama_spesialist'] ?></td>
                        <?php for($i=1;$i<=count($indikasi);$i++){
                            echo '<td class="text-center">'.(isset($val['val_indikasi_'.$i]) ? $val['val_indikasi_'.$i] : 0).'</td>';
                        } ?>
                        <td class="text-center"><?= $val['val_indikasi_1'] + $val['val_indikasi_2'] + $val['val_indikasi_3'] + $val['val_indikasi_4'] + $val['val_indikasi_5'] + $val['val_indikasi_6'] + $val['val_indikasi_7'] + $val['val_indikasi_8'] + $val['val_indikasi_9'] + $val['val_indikasi_10']  ?></td>
                        <td class="text-center"><?= intval($val['jumlah_pasien_perbulan']) ?></td>
                        <td class="text-center">
                            <?php
                                if($val['status'] == 'APPROVED'){
                                    echo '<span class="badge badge-success" data-id="'.$val['id'].'">APPROVED</span>';
                                } else if($val['status'] == 'NOT APPROVED'){
                                    echo '<span class="badge badge-danger" data-id="'.$val['id'].'">NOT APPROVED</span>';
                                }
                            ?>
                        </td>
                        <td style="width: 1px; white-space:nowrap;"><button class="btn btn-sky btn-sm btn-detail" data-id="<?=$val['id']?>"><i class="fa-search"></i></button></td>
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