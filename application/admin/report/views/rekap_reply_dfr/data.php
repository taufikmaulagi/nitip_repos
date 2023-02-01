<?php if (empty($dfr)) : ?>
    <div class="table-responsive">
        <table class="table table-bordered table-app">
            <thead>
                <th>#</th>
                <th>Nama AM</th>
                <th>Region</th>
                <th>Nama MR</th>
                <th>Reply DFR</th>
                <!-- <th>Total</th> -->
            </thead>
            <tbody>
                <?php 
                $index = 1;
                $total_dfr = 0;
                foreach ($rm as $val) : 
                $total_dfr += intval($val['jumlah']);
                ?>
                    <tr>
                        <td style="width: 1px; white-space:nowrap;"><?= $index++ ?></td>
                        <td style="width: 1px; white-space:nowrap;"><?=$val['nama_am']?></td>
                        <td class="text-center"><?=$val['nama_region']?></td>
                        <td><?=$val['nama_mr']?></td>
                        <td class="text-center"><?=$val['jumlah']?></td>
                        <!-- <td><?=$val['Total']?></td> -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total</th>
                    <th class="text-center"><?=$total_dfr?></th>
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