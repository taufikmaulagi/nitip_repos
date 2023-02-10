<?php if (!empty($data)) : ?>
    <style>
        .bg-yellow {
            background-color: #f5ea92 !important;
        }
    </style>
    
    <div class="table-responsive">
        <table class="table table-bordered table-app datatable">
            <thead>
                <th>#</th>
                <th>Dokter</th>
                <th>Speialist</th>
                <th>Practice</th>
                <th>Krit. Potensi</th>
                <th>Status Dokter</th>
                <th>Cust. Matrix</th>
                <th></th>
            </thead>
            <tbody>
                <?php $index = 1;
                foreach ($data as $v) : ?>
                    <tr>
                        <td style="width: 1px; white-space:nowrap;"><?= $index++ ?></td>
                        <td style="white-space: nowrap;"><?=$v['nama_dokter']?></td>
                        <td> <?=$v['nama_spesialist']?></td>
                        <td><?=$v['nama_outlet']?></td>
                        <td><?=$v['kriteria_potensi']?></td>
                        <td><?=$v['status_dokter']?></td>
                        <td><?=$v['customer_matrix']?></td>
                        <td>
                            <button class="btn btn-sky btn-sm btn-detail" data-id="<?=$v['id']?>"><i class="fa-search"></i></button>
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