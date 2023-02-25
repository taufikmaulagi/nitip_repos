
<?php if (!empty($data)) : ?>
    <div class="table-responsive">
        <table class="table table-bordered table-app table-hover table-sm" id="datatables">
            <thead>
                <tr>
                    <th class="text-center" rowspan="2">#</th>
                    <th class="text-center" rowspan="2">Doctor Name</th>
                    <th class="text-center" rowspan="2">Specialist</th>
                    <th class="text-center" rowspan="2">Sub Specialist</th>
                    <th class="text-center" rowspan="2">Outlet</th>
                    <th class="text-center" colspan="3">Total Call</th>
                    <th class="text-center" colspan="3">Doctor Coverage</th>
                    <th class="text-center" colspan="3">Percent Coverage</th>
                </tr>
                <tr>
                    <th class="text-center">PLAN</th>
                    <th class="text-center">ACTUAL</th>
                    <th class="text-center">%</th>
                    <th class="text-center">PLAN</th>
                    <th class="text-center">ACTUAL</th>
                    <th class="text-center">%</th>
                    <th class="text-center">PLAN</th>
                    <th class="text-center">ACTUAL</th>
                    <th class="text-center">%</th>
                </tr>
            </thead>
            <tbody>
                <?php $index = 1; 

                $total_plan_call = 0;
                $total_actual_call = 0;
                $total_dc_plan = 0;
                $total_dc_actual = 0;
                $total_pc_plan = 0;
                $total_pc_actual = 0;

                foreach ($data as $val) : 

                    $val['actual_call'] = $val['actual_call'] > $val['plan_call'] ? $val['plan_call'] : $val['actual_call'];
                    $val['dc_actual'] = $val['dc_actual'] > $val['dc_plan'] ? $val['dc_plan'] : $val['dc_actual'];
                    $val['pc_actual'] = $val['pc_actual'] > $val['pc_plan'] ? $val['pc_plan'] : $val['pc_actual'];

                    $val['dc_actual'] = $val['actual_call'] > 0 ? 1 : 0;
                    $val['pc_actual'] = $val['plan_call'] == $val['actual_call'] && $val['actual_call'] > 0 ? 1 : 0;

                    $total_plan_call += $val['plan_call'];
                    $total_actual_call += $val['actual_call'];
                    $total_dc_plan += $val['dc_plan'];
                    $total_dc_actual += $val['dc_actual'];
                    $total_pc_plan += $val['pc_plan'];
                    $total_pc_actual += $val['pc_actual'];

                    $val['percent_call'] = $val['actual_call'] > 0 ?  round($val['actual_call'] / $val['plan_call'], 3) : 0;
                    $val['percent_dokter'] = $val['dc_actual'] > 0 ?  round($val['dc_actual'] / $val['dc_plan'],3) : 0;
                    $val['percent_precent'] = $val['pc_actual'] > 0 && $val['pc_plan'] > 0 ?  round($val['pc_actual'] / $val['pc_plan'],3) : 0;
                ?>
                    <tr>
                        <td style="width: 1px; white-space:nowrap;"><?= $index++ ?></td>
                        <td style="width:1px; white-space:nowrap"><?= $val['nama_dokter'] ?></td>
                        <td style="width:1px; white-space:nowrap"><?= $val['nama_spesialist'] ?></td>
                        <td style="width:1px; white-space:nowrap"><?= $val['nama_sub_spesialist'] ?></td>
                        <td style="width:1px; white-space:nowrap"><?= $val['nama_outlet'] ? $val['nama_outlet'] : 'REGULER' ?></td>
                        <td style="width:1px; white-space:nowrap" class="text-center"><?= $val['plan_call'] ?></td>
                        <td style="width:1px; white-space:nowrap" class="text-center"><?= $val['actual_call'] ?></td>
                        <td style="width:1px; white-space:nowrap" class="text-center"><?= $val['percent_call'] * 100 ?> %</td>
                        <td style="width:1px; white-space:nowrap" class="text-center"><?= $val['dc_plan'] ?></td>
                        <td style="width:1px; white-space:nowrap" class="text-center"><?= $val['dc_actual']?></td>
                        <td style="width:1px; white-space:nowrap" class="text-center"><?= $val['percent_dokter']  * 100 ?> %</td>
                        <td style="width:1px; white-space:nowrap" class="text-center"><?= $val['pc_plan'] ?></td>
                        <td style="width:1px; white-space:nowrap" class="text-center"><?= $val['pc_actual'] ?></td>
                        <td style="width:1px; white-space:nowrap" class="text-center"><?= $val['percent_precent'] * 100 ?> %</td>
                    </tr>
                <?php endforeach; 
                    $percent_total_call = $total_actual_call > 0 ? $total_actual_call / $total_plan_call * 100 : 0;
                    $percent_total_dokter = $total_dc_actual > 0 ? $total_dc_actual / $total_dc_plan * 100 : 0;
                    $percent_total_percent = $total_pc_plan > 0 ? $total_pc_actual / $total_pc_plan * 100 : 0;
                ?>
                <tfoot>
                    <th colspan="5"> TOTAL </th>
                    <th style="text-align:center"> <?=$total_plan_call?> </th>
                    <th style="text-align:center"> <?=$total_actual_call?> </th>
                    <th style="text-align:center"> <?=round($percent_total_call,2)?> % </th>
                    <th style="text-align:center"> <?=$total_dc_plan?> </th>
                    <th style="text-align:center"> <?=$total_dc_actual?> </th>
                    <th style="text-align:center"> <?=round($percent_total_dokter,2)?> % </th>
                    <th style="text-align:center"> <?=$total_pc_plan?> </th>
                    <th style="text-align:center"> <?=$total_pc_actual?> </th>
                    <th style="text-align:center"> <?=round($percent_total_percent,2)?> % </th>
                </tfoot>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div class="text-center">
        <img src="<?= base_url('assets/images/no-data.svg') ?>" width="40%">
        <h3> Oops! Data Tidak Ditemukan :(</h3>
    </div>
<?php endif; ?>