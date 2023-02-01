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
            <h5 style="font-weight: 700"> Call Activity - Bulan <?=strftime('%B', strtotime(date('Y-m-d')))?> </h5>
            <div class="row">
                <div class="col-sm-12">
                    <canvas id="chartCall" height="100" width="350"></canvas>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-6 mb-3">
                    <h5 style="font-weight: 700"> DFR / Working Day % - Bulan <?=strftime('%B', strtotime(date('Y-m-d')))?> </h5>
                    <div class="row">
                        <div class="col-sm-12">
                            <canvas id="chartDFR" height="300" width="350"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <h5 style="font-weight: 700"> Use Confirm - Bulan Oktober </h5>
                    <div class="row">
                        <div class="col-sm-12">
                            <canvas id="chartConfirm" height="300" width="350"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
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
<script src="<?=base_url('assets/plugins/chartjs/Chart.js')?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chart.piecelabel.js/0.15.0/Chart.PieceLabel.min.js" integrity="sha512-pLEKa6g1uR205lfWRPuxwUa/aw1Yge1jOCvYr5WCL68gh3FoLi0eqMsIEtCvIXgZY0LwiRoMgiTfrpX7pK1HFA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>

    var serialize_color = [
        '#404E67',
        '#22C2DC',
        '#ff6384',
        '#ff9f40',
        '#ffcd56',
        '#4bc0c0',
        '#9966ff',
        '#36a2eb',
        '#848484',
        '#e8b892',
        '#bcefa0',
        '#4dc9f6',
        '#a0e4ef',
        '#c9cbcf',
        '#00A5A8',
        '#10C888'
    ];

   let tahun = [];
    let curr_tahun = parseInt('<?=date('Y')?>')

    for(i=(curr_tahun-5);i<=curr_tahun;i++){
        tahun.push(i)
    }

    let siJsonxD = JSON.parse('<?=json_encode($data_call)?>')
    let nilai_department = []
    let nilai_user       = []

    $.each(siJsonxD, function(i, v){
        nilai_department.push(v.department)
        nilai_user.push(v.pribadi)
    })

    let all_produk = JSON.parse('<?=json_encode($this->session->userdata('produk_group'))?>')
    let produk = []
    $.each(all_produk, function(i, v){
        produk.push(v.nama)
    })

    let data_call = JSON.parse('<?=json_encode($data_call)?>')
    let data_dfr = JSON.parse('<?=json_encode($data_dfr)?>')
    let data_uc = JSON.parse('<?=json_encode($data_uc)?>')

    let plan_call = []
    let total_call = []
    let plan_dokter = []
    let total_dokter = []
    let plan_percent = []
    let total_percent = []

    let color = []

    $.each(data_call, function(i, v){
        plan_call.push(v['plan_call'])
        total_call.push(v['total_call'])
        plan_dokter.push(v['plan_dokter'])
        total_dokter.push(v['actual_dokter'])
        plan_percent.push(v['plan_percent'])
        total_percent.push(v['actual_percent'])
    })

    const ctx = document.getElementById('chartCall').getContext('2d')
    const myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: produk,
			datasets: [{
				label: 'Plan TC',
				data: plan_call,
                backgroundColor: '#ED3419',
			}, 
            {
				label: 'Actual TC',
				data: total_call,
                backgroundColor: '#FF8164',
			}, 
            {
				label: 'Plan DC',
				data: plan_dokter,
                backgroundColor: '#4F77AA',
			}, 
            {
				label: 'Actual DC',
				data: total_dokter,
				backgroundColor: '#78AED3',
			},
            {
				label: 'Plan % Cov',
				data: plan_percent,
				backgroundColor: '#00AB41',
			}, 
            {
				label: 'Actual % Cov',
				data: total_percent,
				backgroundColor: '#83F28F',
			}],
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				},
			},
			responsive: true,
			indexAxis: 'x',
			legend: {
				position: 'top'
			},
		}
	})

    let percent_dfr = []
    let dataset_dfr = [];

    $.each(data_dfr, function(i, v){
        percent_dfr.push(Math.round(v.total * 100).toFixed(2))

        
        // let tmp_dfr = v.dfr
        // let tmp_wkr = 20

        // percent_dfr_work.push(tmp_dfr / tmp_wkr)
    })

    const ctx2 = document.getElementById('chartDFR').getContext('2d')
    const myChart2 = new Chart(ctx2, {
		type: 'bar',
		data: {
			labels: produk,
			datasets: [{
				label: 'DFR',
				data: percent_dfr,
                backgroundColor: serialize_color,
			}]
		},
		options: {
			responsive: true,
			legend: {
				display: false
			},
            pieceLabel: {
                render: 'value',
                fontSize: 12,
                fontColor: '#fff',
                fontStyle: 'bold',
            }
		},
	})

    let confirm = []
    let nu = []
    let use = []

    $.each(data_uc, function(i, v){
        confirm.push(v.confirm)
        nu.push(v.nu)
        use.push(v.used)
    })

    const ctx3 = document.getElementById('chartConfirm').getContext('2d')
    const myChart3 = new Chart(ctx3, {
		type: 'bar',
		data: {
			labels: produk,
			datasets: [{
				label: 'Confirm',
				data: confirm,
                backgroundColor: '#bcefa0',
			},
            {
				label: 'Use',
				data: use,
				backgroundColor: '#22C2DC',
			}, 
            {
				label: 'Not Use',
				data: nu,
				backgroundColor: '#ff9f40',
			},],
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				},
			},
			responsive: true,
			indexAxis: 'x',
			legend: {
				position: 'top'
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true
					}
				}]
			}
		}
	})

    

    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
</script>