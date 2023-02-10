<?php if (!empty($data)) : ?>
    <div class="table-responsive">
        <table class="table table-bordered table-app">
            <thead>
                <th>#</th>
                <th>Doctor Name</th>
                <th>Specialist</th>
                <th>Outlet</th>
                <th class="text-center">Wk1</th>
                <th class="text-center">Wk2</th>
                <th class="text-center">Wk3</th>
                <th class="text-center">Wk4</th>
                <th class="text-center">Wk5</th>
                <th class="text-center">Wk6</th>
                <th class="text-center">Total</th>
                <th>Status</th>
                <th></th>
            </thead>
            <tbody>
                <?php $index = 1;
                $total_plan = 0;
                foreach ($data as $val) :
                    $total_plan += intval($val['plan_call']);
                ?>
                    <tr>
                        <td style="width: 1px; white-space:nowrap;"><?= $index++ ?></td>
                        <td><?= $val['nama_dokter'] ?></td>
                        <td><?= $val['nama_spesialist'] ?></td>
                        <td><?= $val['nama_outlet'] ? $val['nama_outlet'] : 'REGULER' ?></td>
                        <td class="text-center"><?= $val['week1'] ? $val['week1'] : 0 ?></td>
                        <td class="text-center"><?= $val['week2'] ? $val['week2'] : 0 ?></td>
                        <td class="text-center"><?= $val['week3'] ? $val['week3'] : 0 ?></td>
                        <td class="text-center"><?= $val['week4'] ? $val['week4'] : 0 ?></td>
                        <td class="text-center"><?= $val['week5'] ? $val['week5'] : 0 ?></td>
                        <td class="text-center"><?= $val['week6'] ? $val['week6'] : 0 ?></td>
                        <td class="text-center"><?= $val['plan_call']?></td>
                        <td>
                            <?php
                                if($val['status'] == 'APPROVED'){
                                    echo '<span class="badge badge-success" data-id="'.$val['id'].'">APPROVED</span>';
                                } else if($val['status'] == 'WAITING'){
                                    echo '<span class="badge badge-warning" data-id="'.$val['id'].'">WAITING</span>';
                                } else if($val['status'] == 'REVISION'){
                                    echo '<span class="badge badge-warning" data-id="'.$val['id'].'">WAITING</span>';
                                } else {
                                    echo '<span class="badge badge-danger" data-id="'.$val['id'].'">NOT APPROVED</span>';
                                }
                            ?>
                        </td>
                        <td style="width: 1px; white-space:nowrap;"><button class="btn btn-sky btn-sm btn-detail" data-id="<?=$val['id']?>"><i class="fa-search"></i></button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10" class="text-right">Total</td>
                    <td class="text-center"><?= $total_plan ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
<?php else : ?>
    <div class="text-center">
        <img src="<?= base_url('assets/images/no-data.svg') ?>" width="40%">
        <h3> Oops! Data Tidak Ditemukan :(</h3>
    </div>
<?php endif; ?>