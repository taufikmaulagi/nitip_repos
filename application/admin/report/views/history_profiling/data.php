<?php if (!empty($profiling)) : 
    $indikasi = get_data('indikasi', [
        'where' => [
            'produk_grup' => $profiling[0]['produk_grup'],
            'is_active' => 1
        ]
    ])->result_array();
?>
    <div class="table-responsive">
        <table class="table table-bordered table-app">
            <thead>
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Doctor Name</th>
                    <th rowspan="2">Specialist</th>
                    <th colspan="<?=count($indikasi)+1?>" class="text-center">Jumlah Pasien</th>
                    <th rowspan="2" class="text-center">Pasien / Bulan</th>
                    <th rowspan="2" class="text-center">Status</th>
                    <th rowspan="2"></th>
                </tr>
                <tr>
                    <?php foreach($indikasi as $val): ?>
                        <th><?=$val['nama']?></th>
                    <?php endforeach; ?>
                    <th>Total Potensi</th>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1;
                foreach ($profiling as $val) : ?>
                    <tr>
                        <td style="width: 1px; white-space:nowrap;"><?= $index++ ?></td>
                        <td><?= $val['nama_dokter'] ?></td>
                        <td><?= $val['nama_spesialist'] ?></td>
                        <?php for($i=1;$i<=count($indikasi);$i++){
                            echo '<td class="text-center">'.(isset($val['value_indikasi_'.$i]) ? $val['value_indikasi_'.$i] : 0).'</td>';
                        } ?>
                        <td class="text-center"><?= $val['value_indikasi_1'] + $val['value_indikasi_2'] + $val['value_indikasi_3'] + $val['value_indikasi_4'] + $val['value_indikasi_5'] + $val['value_indikasi_6'] + $val['value_indikasi_7'] + $val['value_indikasi_8'] ?></td>
                        <td class="text-center"><?= intval($val['jumlah_pasien']) ?></td>
                        <td class="text-center">
                            <?php
                                if($val['status'] == 2){
                                    echo '<span class="badge badge-success">APPROVED</span>';
                                } else if($val['status'] == 3){
                                    echo '<span class="badge badge-danger">NOT APPROVED</span>';
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