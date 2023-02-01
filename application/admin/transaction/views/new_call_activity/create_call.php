<script src="<?=base_url('assets/js/sketchpad.js')?>"></script>
<div class="content-body" style="padding-left:50px; padding-right:50px">
<?php
form_open(base_url('transaction/new_call_activity/save'),'post','form');
	col_init(3,9);
	// echo '<div class="row" style="margin-bottom:10px">';
	// 	echo '<div class="col-sm-10">';
			input('hidden','id','id');
			select2('Produk Group','produk_grup','required',$this->session->userdata('produk_group'),'kode','nama');
			select2('Dokter','dokter','required');
			select2('Produk','produk','required');
			input('text','Spesialist','spesialist','required','','disabled="disabled"');
			input('text','Outlet','outlet','required','','disabled="disabled"');
			input('text','Channel Outlet','channel_outlet','required','','disabled="disabled"');
			select2('Kompetitor yang di R/', 'kompetitor_diresepkan','required');
			echo '<div id="kompetitor_lainya_box" style="display:none; margin-bottom:10px">';
				textarea('Kompetitor yang di R/ Lainya','kompetitor_diresepkan_lainnya','max-length:150');
			echo '</div>';
			textarea('Circumstances','circumstances','required|max-length:150');
			textarea('Call Objective','call_object','required|max-length:150');
			select2('Indikasi Produk', 'indikasi','required');
			echo '<div id="indikasi_lainya_box" style="display:none; margin-bottom:10px">';
				textarea('Indikasi Produk Lainya','indikasi_lainnya','max-length:150');
			echo '</div>';
			select('Key Message', 'key_message','required');
			echo '<div class="row">';
			echo '<div class="col-sm-3">';
				echo 'Signature';
			echo '</div>';
			echo '<div class="col-sm-9">';
			input('hidden','signature','signature');
			echo '<canvas id="sketchpad" style="border:1px solid #cfcfcf"></canvas>';
			echo '<br/>';
			echo '<p><button type="button" class="btn btn-fresh btn-sm" onclick="sketchpad.undo();" class="btn">Undo</button>
			<button type="button" class="btn btn-fresh btn-sm" onclick="sketchpad.redo();" class="btn">Redo</button>
			<button type="button" class="btn btn-fresh btn-sm" onclick="sketchpad.animate(10);" class="btn">Animate</button></p>';
			echo '</div>';
			echo '</div>';
			echo '<div id="send_box" style="display:none">';
				select2('Call Type','call_type','',[
					['id' => 1, 'nama' => 'A DFR'],
					['id' => 2, 'nama' => 'B Short Detailing'],
					['id' => 3, 'nama' => 'C Happy Call'],
				],'id','nama');
				select2('Sub Call Type','sub_call_type');
				textarea('MR Talk','mr_talk');
				echo '<div id="mr_talk2_box" style="margin-bottom:10px">';
					textarea('MR Talk 2','mr_talk2');
				echo '</div>';
				echo '<div id="mr_talk3_box" style="margin-bottom:10px">';
					textarea('MR Talk 3','mr_talk3');
				echo '</div>';
				select2('Feedback Status','feedback_status','',[
					'No Feedback' => 'No Feedback',
					'Positive' => 'Positive',
					'Negative' => 'Negative',
				]);
				echo '<div id="feedback_dokter_box" style="margin-bottom:10px; display:none">';
					textarea('Feedback Doctor','feedback_dokter','');
					textarea('Feedback Doctor 2','feedback_dokter2','');
					textarea('Feedback Doctor 3','feedback_dokter3','');
				echo '</div>';
				textarea('Next Action Plan','next_action','');
			echo '</div>';
		echo '</div>';
		// echo '<div class="col-sm-2">';
		// 	if($this->session->userdata('team') == 'ABILIFY'){
		// 		input('text','Matrix Abilify','matrix','','','disabled="disabled"');
		// 		input('text','Matrix Maintena','matrix_maintena','','','disabled="disabled"');
		// 		input('text','Matrix Rexulti','matrix_rexulti','','','disabled="disabled"');
		// 	} else {
		// 		input('text','Matrix','matrix','','','disabled="disabled"');
		// 	}
		// 	if(count($this->session->userdata('produk_group'))>1){
		// 		select('Produk2','produk2','',$this->session->userdata('produk_group'),'kode','nama');
		// 		select('Produk3','produk3','',$this->session->userdata('produk_group'),'kode','nama');
		// 	} else {
		// 		if($this->session->userdata('team') == 'ABILIFY'){
		// 			select('Produk2','produk2','', [
		// 				'REXULTI' => 'REXULTI',
		// 				'MAINTENA' => 'MAINTENA'
		// 			]);
		// 			select('Produk3','produk3','',[
		// 				'REXULTI' => 'REXULTI',
		// 				'MAINTENA' => 'MAINTENA'
		// 			]);
		// 		} else {
		// 			select('Produk 2','produk2','',$this->session->userdata('produk_group'),'kode','nama');
		// 		}
		// 	}
		// echo '</div>';
	// echo '</div>';
	// form_button(lang('simpan'),lang('batal'));
?>
</div>
<script>
    var sketchpad = new Sketchpad({
		element: '#sketchpad',
		width: 300,
		height: 300,
	});
	
	function recover(event) {
		var settings = sketchpad.toObject();
		settings.element = '#sketchpad2';
		var otherSketchpad = new Sketchpad(settings);
		$('#recover-button').hide();
	}

	$(document).on('change','#produk_grup',function(){	
		__get_dokter()
		__get_produk()
	})

	function __get_dokter(){
		$.ajax({
			url : base_url + 'transaction/new_call_activity/get_dokter_by_produk?produk='+$('#produk_grup').val(),
			success: function(r){
				let html = '<option value=""> Pilih Dokter </option>'
				$.each(r, function(i, v){
					html += '<option value="'+ v.id +'">'+ v.nama +'</option>'
				})
				$('#dokter').html(html)
			}
		})
	}

	function __get_produk(){
		$.ajax({
			url : base_url + 'transaction/new_call_activity/get_produk_by_grup?produk='+$('#produk_grup').val(),
			success: function(r){
				let html = '<option value=""> Pilih Produk </option>'
				$.each(r, function(i, v){
					html += '<option value="'+ v.id +'">'+ v.nama +'</option>'
				})
				$('#produk').html(html)
			}
		})
	}

</script>