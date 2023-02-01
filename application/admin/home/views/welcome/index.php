<div class="content-body body-home p-3">
    <div class="float-right d-none d-sm-block">
        
    </div>
    <h2 style="margin-bottom:0px; font-weight:700"> Dashboard </h2>
    <span class="text-muted" style="font-size:.78rem">
        <i><?= strftime('%A, %e %B %Y', strtotime(date('Y-m-d'))) ?></i>
    </span>
    <div class="row pt-4">
        <div class="col-sm-9">
            <div class="card text-white mb-3" style="border:none; background-color:#78AED3; background-image:url('<?= base_url('assets/images/d1.png') ?>'); background-repeat: no-repeat; background-size: cover;">
                <div class="card-body pt-4 pl-3">
                    <!-- <img src="" class="mb-3"> -->
                    <h3 style="font-weight:600">Hai, Selamat
                        <?php
                        $Hour = date('G');

                        if ($Hour >= 5 && $Hour <= 10) {
                            echo 'Pagi <i class="fa-cloud"></i>';
                        } else if ($Hour >= 11 && $Hour <= 15) {
                            echo "Siang <i class='fa-sun' style='color:yellow'></i>";
                        } else if ($Hour >= 16 || $Hour <= 19) {
                            echo "Sore <i class='fa-cloud-sun' style='color:aqua'></i>";
                        } else {
                            echo "Malam <i class='fa-cloud-moon' style='color:grey'></i>";
                        }
                        ?>
                    </h3>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <img src="<?php echo user('foto'); ?>" class="rounded-circle float-left mr-3" style="width:60px">
                    <h4 style="font-weight:600;"><?= user('nama') ?></h4>
                    <i style="font-size:15px; font-weight:300; color:white">Have a nice <?= strftime('%A', strtotime(date('Y-m-d'))) ?></i> !
                </div>
            </div>
        </div>
    </div>
</div>