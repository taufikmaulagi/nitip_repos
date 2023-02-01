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
                    <div class="alert">
                        <i class="fa-info mr-2"></i> <b>Informasi </b>
                        <br/> Dashboard ini masih dalam tahap pengembangan yaa~ 
                        <br/> Good Luck 😄
                    </div>
                    <img src="<?php echo user('foto'); ?>" class="rounded-circle float-left mr-3" style="width:60px">
                    <h4 style="font-weight:600;"><?= user('nama') ?></h4>
                    <i style="font-size:15px; font-weight:300; color:white">Have a nice <?= strftime('%A', strtotime(date('Y-m-d'))) ?></i> !
                </div>
            </div>
            
            <h1> Development Server </h1>
            
            <!-- <h5 style="font-weight: 700"> Use Confirm - Bulan <?=strftime('%B', strtotime(date('Y-m-d')))?> </h5> -->
            
        </div>
        <div class="col-sm-3 p-0">
            <div class="card" style="background-color:#081b4b; color:white">
                <div class="card-body">
                    <h4 style="font-weight:600" class="mb-4">My Task</h4>
                    <div class="mt-5 text-center">
                        <img src="<?= base_url('assets/images/no-data.svg') ?>">
                        <br/><br/>
                        <h4> Belum ada tugas baru.</h4>
                    </div>
                    <div class="alert alert-info mb-3">
                        <i class="fa-info mr-2"></i>Disini akan berisi tugas karyawan yang dapat dikerjakan tanpa perlu klik2 menu 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>