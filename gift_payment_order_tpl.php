<!DOCTYPE html>
<html lang="ja">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=shift_jis" />
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-script-type" content="text/javascript" />
	<?php echo DF_meta_tag_for_viewport; ?>
<title>���x�����y�[�W�b���i�w��</title>
<meta name="description" content="C-joy(�V�B�W���C)�͍��i���ȐH�ނ�ቿ�i�Ŕ̔����������T�C�g�ł��B">
	<meta name="keywords" content="�V�[�W���C,�V�B�W���C,cjoy,c-joy,�H��,�H�i,���i��,����,����,����">
	<?php include_once("ga_code/analyticstracking.php") ?>
	<link rel="stylesheet" href="/view/lib/jquery_ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
    <!--Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!--Font Awesome5-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
	<link rel="stylesheet" type="text/css" media="all" href="view/css/style.css" />
	<script type="text/javascript" src="/view/js/ajaxzip3.js" charset="utf-8"></script>
<style type="text/css">
<!--
.space{
	margin-right:5em;
}
#d_errors {
	margin : 10px;
	width  : 600px;
	border-style :solid;
	color: red;
	border-color : #ff0000;
}
#dialog {
	font-size:12px;
}
#dialog_wrap {
	font-size:80%;
}
.btn > input {
		margin-top: 15px;
		padding: 5px;
		min-width: 120px;
		font-size: 14px;
	}
.choice_fm {
		margin-top: 15px;
		margin-bottom: 15px;
		padding: 5px;
		font-size: 14px;
	}
.small {
	margin-top: 10px;
	color:red;
	font-size:10px;
	}
dl.trans {
	display:table;
	width: 100%;
	margin: 1em auto;
	padding: 0;
	table-layout:fixed;
}
dl.trans dt {
	padding: .7em 1em .7em 1em;
	display:table-cell;
	background: #ddd;
	position: relative;
	border-radius: .4em;
	text-align:center;
	vertical-align:middle;
}
dl.trans dd {
	display:table-cell;
	width:3em;
	text-align:center;
	vertical-align:middle;
	}
dl.trans dt.here_now {
	background:#c00;
	color:#fff;
	font-weight:bold;
}

#btn_ins_dest {
		margin-top: 15px;
		padding: 5px;
		min-width: 220px;
		font-size: 14px;
}
#dest_add_err {
	color:red;
}
.kakkoi {
	border:solid 1px #ccc;
	padding:5px 10px;
}
.upper {
	border-top:solid 1px #ccc;
}
.bottomer {
	border-bottom:solid 1px #ccc;
}
.lefter {
	border-left:solid 1px #ccc;
}
.righter {
	border-right:solid 1px #ccc;
}
#home_tbl {
	margin-bottom:10px;
}
.shipping_tbl {
	width: 100%;
	border-collapse: collapse;
	margin-top:5px;
	padding:5px 10px;
}
.shipping_tbl input {
	padding:2px 2px;
}
.ui-dialog .ui-dialog-title {
	font-size:140%;
}
.small {
	color:red;
	font-size:10px;
}
.alert {
	color:#C00;
	font-size:12px;
	margin-left: 15px;
}
.wrapping_word, 
.arrival_word {
	margin-left: 15px;
}
.c_input input {
	margin-right: 3px;
}
-->
</style>
<script type="text/javascript" src="/view/lib/jquery-1.11.3.js"></script>
<script type="text/javascript" src="/view/lib/jquery_ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript">
$(function(){
	
	$('#ins_dest').hide();
	
	// �z����̕ύX�_�C�A���O�ݒ�
	$('#dialog').dialog({ 
		autoOpen: false, 
		modal: true,
		width: 'auto',
	});
	
	// ���b�s���O�ݒ�̃_�C�A���O
	$('#dialog_wrap').dialog({ 
		autoOpen: false, 
		modal: true,
		width: 'auto',
	});
	
	// ������]�����̃_�C�A���O
	$('#dialog_arrival').dialog({ 
		autoOpen: false, 
		modal: true,
		width: 'auto',
	});
	
	$('input[name=select_pay]').val([<?php echo $p_payment; ?>]);
	$('#p_payment').val([<?php echo $p_payment; ?>]);

	// ���x�������@�ύX�l�`�F�b�N
	$('input[name=select_pay]').click(function(){
//		alert($(this).val());
		$('#p_payment').val($(this).val());
	});
	// �߂��
	$('#btn_back').click(function(){
		$('#p_kind').val('cart_summary');
		$(this.form).submit();
	});

	// �ŏI�m�F��ʂ�
	$('#btn_next').click(function(){
		var sel_p = $('input[name=p_select_point]:checked').val();
		if (sel_p == 2) {
			var u_point = $('#buyer_point').html();
			var u_point = Number(u_point) || 0; 
			var input_point = $('input[name=p_use_point]').val();
			var input_point = Number(input_point) || 0; 
			var payment = $('#p_payment').val();
			if (input_point > u_point) {
				alert('�g�p�|�C���g���ۗL���Ă���|�C���g���𒴂��Ă��܂��B');
				return false;
			}
			if (input_point % 500 != 0) {
				alert('���g�p�ɂȂ��|�C���g��500�|�C���g�P�ʂƂȂ�܂��B');
				return false;
			}
			if ((input_point != 0 && input_point < 500) || (input_point > 50000)) {
				alert('���g�p�ɂȂ��|�C���g��500�|�C���g�ȏ�A50000�|�C���g�܂łƂȂ�܂��B');
				return false;
			}
			if (payment == 0 && input_point >= <?php echo $sum_price; ?>) {
				alert('�N���W�b�g�J�[�h���ς̏ꍇ�A�S�z���|�C���g�ł��x�����ɂ͂Ȃ�܂���B');
				return false;
			}
		}
		if ($('input[name=select_pay]:checked').val() == undefined) {
			alert('���x�������@��I�����Ă��������B\n�������i�̏ꍇ�̓N���W�b�g�J�[�h�̓o�^�����肢���܂��B');
			return false;
		}
		
		var entry_err='';
		if ($('#buyer_name').val() == '') {
			entry_err +="���͂���̖��O���͂��Ă��������B\n";
		}
		
		if ($('#buyer_zip').val() == '') {
			entry_err +="���͂���̗X�֔ԍ����͂��Ă��������B\n";
		}
		
		if ($('#buyer_address').val() == '') {
			entry_err +="���͂���̏Z�����͂��Ă��������B\n";
		}
		
		if ($('#buyer_tel').val() == '') {
			entry_err +="���͂���̓d�b�ԍ����͂��Ă��������B\n";
		}
		
		var total_cnt=0,
			quantity_total=parseInt($('#quantity_total').html());
		$('.item_quantity').each(function() {
			total_cnt+=parseInt($(this).val());
		});
		
		if(total_cnt<quantity_total){
			entry_err +='�������Ƃ��͂��搔�ʂ̍��v���Ⴂ�܂��B';
		}
		
		if(entry_err!=''){
			alert(entry_err);
			return false;
		}
		
		$('#p_kind').val('order_confirm');
		$(this.form).submit();
	});
	
	//�N���W�b�g�J�[�h�x���ꍇ
	var save_card_flg=$('#save_card_flg').val(),
		card_btn='<input type="button" value="�J�[�h�����̓t�H�[��" id="create-token-launch"><br /><br />';
	$('#btn_next').prop('disabled', true);
	<?php if(@$_SESSION['jcb_card_request_data']['webcollectToken']) {?>
		$('#btn_next').prop('disabled', false);
	<?php }else{?>
		$('#card_pay').prop('checked', false);
	<?php } ?>
	
	if($('#delivery_pay').prop('checked')){
		$('#btn_next').prop('disabled', false);
	}
	
	if($('#bank_transfer_pay').prop('checked')){
		$('#btn_next').prop('disabled', false);
	}
	
	$('.select_pay').change(function(){
		$('#have_token').css('display','none');
		$('#bank_text').css('display','none');
		
		if($('#card_pay').prop('checked')){
			$('#btn_next').prop('disabled', true);
			$('#jcb_card_form').html(card_btn);
			$('#card_info_div').css('display','inline');
			
			if(save_card_flg==0){
				/*$('input:radio[name=card_use_type]').each(function(){ 
					$(this).prop('checked', false);
				});*/
				$('#new_card_radio_set').css('display','inline');
				$('#jcb_card_form').css('display','inline');
				$.ajax({
				  method: "POST",
				  url: "ajax_card.php",
				  data: { p_kind: "save_card"}
				})
				.success(function(src) {
					$('#modal_src').replaceWith(src);
				});
			}else if(save_card_flg==1){
				$('#new_card_radio_set').css('display','none');
				$('#btn_save_card').css('display','inline');
				$('#use_saved_card_div').css('display','inline');
				
				/*var r = confirm("���a������폜���܂��B��낵�ł��傤���H");
				if (r == true) {
					$('#jcb_card_form').css('display','inline');
					$.ajax({
					  method: "POST",
					  url: "ajax_card.php",
					  data: { p_kind: "save_card"}
					})
					.success(function(src) {
						$('#modal_src').replaceWith(src);
					});
				}*/
				$('#use_saved_card').prop('checked', true);
				$.ajax({
					method: "POST",
					url: "ajax_card.php",
					data: { p_kind: "use_saved_card"}
				})
				.success(function(src) {
					$('#modal_src').replaceWith(src);
				});
				$('#jcb_card_form').css('display','inline');
			}
		}else{
			$('#btn_next').prop('disabled', false);
			$('#card_info_div').css('display','none');
			$('#jcb_card_form').css('display','none');
			$('#new_card_radio_set').css('display','none');
		}

		if($('#bank_transfer_pay').prop('checked')){
			$('#bank_text').css('display','block');
		}
	});
	
	$('#use_saved_card').click(function(){
		$('#jcb_card_form').css('display','inline');
		$('#btn_save_card').css('display','none');
		$.ajax({
		  method: "POST",
		  url: "ajax_card.php",
		  data: { p_kind: "use_saved_card"}
		})
		.success(function(src) {
			$('#modal_src').replaceWith(src);
		});
	});
	
	$('#save_card').click(function(){
		$('#jcb_card_form').css('display','inline');
		$.ajax({
		  method: "POST",
		  url: "ajax_card.php",
		  data: { p_kind: "save_card"}
		})
		.success(function(src) {
			$('#modal_src').replaceWith(src);
		});		
	});
	
	$('#purchase_only').click(function(){
		$('#jcb_card_form').css('display','inline');
		$.ajax({
		  method: "POST",
		  url: "ajax_card.php",
		  data: { p_kind: "purchase_only"}
		})
		.success(function(src) {
			$('#modal_src').replaceWith(src);
		});
	});
	
	$('#new_card').click(function(){
		/*$('input:radio[name=card_use_type]').each(function(){ 
			$(this).prop('checked', false);
		});*/
		$('#new_card_radio_set').css('display','inline');
		$('#btn_save_card').css('display','none');
		$('#use_saved_card_div').css('display','none');

		$('#jcb_card_form').html(card_btn);
		$('#jcb_card_form').css('display','inline');
		$.ajax({
		  method: "POST",
		  url: "ajax_card.php",
		  data: { p_kind: "save_card"}
		})
		.success(function(src) {
			$('#modal_src').replaceWith(src);
		});
		
	});
	
	// �u�V�������͂����o�^�v�̋���
	$('#btn_ins_dest').click(function(){
		$('#dest_add_err').html('');
		$('#ins_dest').show();
	});
	
	// ���t��̕ύX
	$('.dest_edit').click(function(){
		f_edit_dest(this);
	});
	
	// ���t��̍폜
	$('.dest_del').click(function(){
		f_del_dest(this);
	});
	
	// ���b�s���O�̕ύX
	$('.dest_wrapping').click(function(){
		f_edit_wrap(this);
	});

	// ������]�����̕ύX
	$('.dest_arrival').click(function(){
		f_edit_arrival(this);
	});
	
	// ���b�s���O�̕ҏW
	function f_edit_wrap(tgt) {
		var serial_txt = $(tgt).attr('name');
		serial_txt = serial_txt.replace("dest_wrapping_", "");
		var t_setting = $('#wrapping_flg_'+serial_txt).val();
		$('input[name=wrapping_set]').val([t_setting]);
			$('input[name=wrapping_set]:checked').focus();
		var tmp_noshi_top = $('#noshi_top_'+serial_txt).val();
		if (tmp_noshi_top == '���Ε�') {
			$('input[name=noshi_set]').val(['1']);
		} else if (tmp_noshi_top == '������') {
			$('input[name=noshi_set]').val(['2']);
		} else {
			$('input[name=noshi_set]').val(['3']);
			$('input[name=noshi_upper]').val(tmp_noshi_top);
		}
		$('input[name=noshi_bottom]').val($('#noshi_bottom_'+serial_txt).val());
		$('#wrap_set_number').val(serial_txt);
		noshi_radio();
		$('#dialog_wrap').dialog('open');
	}
	
	// ��]���������ҏW�_�C�A���O���J��
	function f_edit_arrival(tgt) {
		var serial_txt = $(tgt).attr('name');
		serial_txt = serial_txt.replace("dest_arrival_", "");
		$('#arrival_set_number').val(serial_txt);
		$('#edit_kibou_date').val($('#kibou_date_'+serial_txt).val());
		$('#edit_kibou_time').val($('#kibou_time_'+serial_txt).val());
		$('#dialog_arrival').dialog('open');
	}

	// ���t��̍폜
	function f_del_dest(tgt) {
		var serial_txt = $(tgt).attr('name');
		serial_txt = serial_txt.replace("dest_del_", "");
		$('#shipping_tbl_'+serial_txt).hide();
		$.post(
			"<?php echo $_SERVER['SCRIPT_NAME'] ?>?p_kind=subquery",//script url
			{
				'p_dest_kind': 'delete', 
				'p_del_number': serial_txt,
				'seller_id': '<?php echo $seller_id;?>',
			},//�L�[:�l
			function(data) {
				if (data == 'OK') {
					location.href='/gift_order.php?p_kind=payment&seller_id=<?php echo $seller_id?>&p_scroll='+$(window).scrollTop();
				} else {
					$('#dest_add_err').html(data);
				}
			}
		);
	}
	
	// ���t��̕ҏW
	function f_edit_dest(tgt) {
		var serial_txt = $(tgt).attr('name');
		serial_txt = serial_txt.replace("dest_edit_", "");
		$('#edit_dest_name').val($('#dest_name_'+serial_txt).html());
		$('#edit_dest_zip').val(f_del_Hyphen($('#dest_zip_'+serial_txt).html()));
		$('#edit_dest_add1').val($('#dest_add1_'+serial_txt).html());
		$('#edit_dest_add2').val($('#dest_add2_'+serial_txt).html());
		$('#edit_dest_tel').val($('#dest_tel_'+serial_txt).html());
		$('#edit_dest_number').val(serial_txt);
		$('#dialog').dialog('open');
	}
	
	// �V�������t���ǉ�
	$('#new_dest_add').click(function(){
		
		var dest_name= $('#new_dest_name').val(), 
			dest_zip=$('#new_dest_zip').val(), 
			dest_add1=$('#buyer_pref').val(), 
			dest_add2=$('#new_dest_add2').val(), 
			dest_add3=$('#new_dest_add3').val(), 
			dest_tel=$('#new_dest_tel').val(),
			err_txt='';
			
		if(dest_name==''){
			err_txt +="�V�������͂���̖��O����͂��Ă��������B\n";
		}
		
		if(dest_zip==''){
			err_txt +="�V�������͂���̗X�֔ԍ�����͂��Ă��������B\n";
		}
		
		if(dest_add1==''){
			err_txt +="�V�������͂���̌���I�����Ă��������B\n";
		}
		
		if(dest_add2==''){
			err_txt +="�V�������͂���̎s�撬������͂��Ă��������B\n";
		}
		
		if(dest_add3==''){
			err_txt +="�V�������͂���̔Ԓn�A����������͂��Ă��������B\n";
		}
		
		if(dest_tel==''){
			err_txt +="�V�������͂���̓d�b����͂��Ă��������B\n";
		}
		
		if(err_txt){
			alert(err_txt);
		}else{
			$.post(
				"<?php echo $_SERVER['SCRIPT_NAME'] ?>?p_kind=subquery",//script url
				{
					'p_dest_kind': 'new', 
					'dest_name': $('#new_dest_name').val(), 
					'dest_zip': $('#new_dest_zip').val(), 
					'dest_add1': $('#buyer_pref').val(), 
					'dest_add2': $('#new_dest_add2').val(), 
					'dest_add3': $('#new_dest_add3').val(), 
					'dest_tel': $('#new_dest_tel').val(),
				},//�L�[:�l
				function(data) {
					if (data == 'OK') {
						var size = parseInt($('#p_dest_size').val(), 10);
						size++;
						$('#p_dest_size').val(size);
						//f_add_shipping_tbl(size);
						// �l�̃N���A
						$('#new_dest_name').val('');
						$('#new_dest_zip').val('');
						$('#buyer_pref').val('�k�C��');
						$('#new_dest_add2').val('');
						$('#new_dest_add3').val('');
						$('#new_dest_tel').val('');
						$('#dest_add_err').html('');
						$('#ins_dest').hide();
						/*location.reload();*/
						
						location.href='/gift_order.php?p_kind=payment&seller_id=<?php echo $seller_id?>&p_scroll='+$(window).scrollTop();
						
					} else {
						$('#dest_add_err').html(data);
					}
				}
			);
		}
	});
	
	// �ҏW�̎��s
	$('#edit_dest_up').click(function(){
		$.post(
			"<?php echo $_SERVER['SCRIPT_NAME'] ?>?p_kind=subquery",//script url
			{
				'p_dest_kind': 'edit', 
				'serial': $('#edit_dest_number').val(), 
				'dest_name': $('#edit_dest_name').val(), 
				'dest_zip': $('#edit_dest_zip').val(), 
				'dest_add1': $('#edit_dest_add1').val(), 
				'dest_add2': $('#edit_dest_add2').val(), 
				'dest_tel': $('#edit_dest_tel').val() 
			},//�L�[:�l
			function(data) {
				if (data == 'OK') {
					$num = $('#edit_dest_number').val();
					f_edit_shipping_tbl($num);
					// �l�̃N���A
					$('#edit_dest_name').val('');
					$('#edit_dest_zip').val('');
					$('#edit_dest_add1').val('�k�C��');
					$('#edit_dest_add2').val('');
					$('#edit_dest_tel').val('');
					$('#edit_dest_number').val('');
					$('#dest_edit_err').html('');
					$('#dialog').dialog('close');
				} else {
					$('#dest_edit_err').html(data);
				}
			}
		);
	});
	
	// �z����̕ύX�𔽉f����
	function f_edit_shipping_tbl(num) {
		$('#dest_name_'+num).html($('#edit_dest_name').val());
		$('#dest_zip_'+num).html($('#edit_dest_zip').val());
		$('#dest_add1_'+num).html($('#edit_dest_add1').val());
		$('#dest_add2_'+num).html($('#edit_dest_add2').val());
		$('#dest_tel_'+num).html($('#edit_dest_tel').val());
	}
	
	// ���b�s���O�̐ݒ�𑗐M
	$('#dest_wrap_set').click(function(){

		$.post(
			"<?php echo $_SERVER['SCRIPT_NAME'] ?>?p_kind=subquery",//script url
			{
				'p_dest_kind': 'wrap', 
				'p_wrap_number': $('#wrap_set_number').val(), 
				'p_wrapping_set': $('input[name=wrapping_set]:checked').val(),
				'p_noshi_set': $('input[name=noshi_set]:checked').val(), 
				'p_noshi_up': $('input[name=noshi_upper]').val(), 
				'p_noshi_btm': $('input[name=noshi_bottom]').val(),
			},//�L�[:�l
			function(data) {
				if (data == 'OK') {
					$num = $('#wrap_set_number').val();
					f_edit_wrapping($num);
					// �l�̃N���A
					$('input[name=wrapping_set]').val(['0']);
					$('input[name=noshi_set]').val(['3']);
					$('input[name=noshi_upper]').val('');
					$('input[name=noshi_bottom]').val('');
					$('#wrap_set_number').val('');
					$('#wrap_edit_err').html('');
					$('#dialog_wrap').dialog('close');
					
					location.href='/gift_order.php?p_kind=payment&seller_id=<?php echo $seller_id?>&p_scroll='+$(window).scrollTop();
					
				} else {
					$('#wrap_edit_err').html(data);
				}
			}
		);
	});
	
	
	// ���b�s���O�̐ݒ�𔽉f����
	function f_edit_wrapping(num) {
		$t_wrapping_word = '';
		$t_wrapping_set = $('input[name=wrapping_set]:checked').val();
		$('#wrapping_flg_'+num).val($t_wrapping_set);
		$t_noshi_set = $('input[name=noshi_set]:checked').val();
		$t_noshi_word = '';
		if ($t_noshi_set == 1) {
			$t_noshi_word = '���Ε�';
		} else if ($t_noshi_set == 2) {
			$t_noshi_word = '������';
		} else if ($t_noshi_set == 3) {
			$t_noshi_word = $('input[name=noshi_upper]').val();
		}			
		$('#noshi_top_'+num).val($t_noshi_word);
		$('#noshi_bottom_'+num).val($('input[name=noshi_bottom]').val());
		if ($t_wrapping_set == 0) {
			$t_wrapping_word = '���b�s���O�E�̂�����';
		} else if ($t_wrapping_set == 1) {
			$t_wrapping_word = '���b�s���O';
		} else {
			$t_wrapping_word = '�̂�';
			$t_wrapping_word += '����i��' + $t_noshi_word;
			$t_wrapping_word += '�����i��' + $('input[name=noshi_bottom]').val();
		}
		// �̂��ݒ菉����
		if ($t_wrapping_set != 2) {
			$('input[name=noshi_set]:checked').val(['3']);
			$('input[name=noshi_upper]').val('');
			$('input[name=noshi_bottom]').val('');
		}
		$('#dest_wrapping_'+num).html($t_wrapping_word);
	}
	
	// ���b�s���O�̐ݒ��ς������ɂ̂��̐ݒ��L���ɂ���
	$('input[name=wrapping_set]:radio').change(function() {
		noshi_radio();
	});
	
	// ������]�����̐ݒ�𑗐M
	$('#edit_arrival_up').click(function(){
		$.post(
			"<?php echo $_SERVER['SCRIPT_NAME'] ?>?p_kind=subquery",//script url
			{
				'p_dest_kind': 'arrival', 
				'p_arrival_number': $('#arrival_set_number').val(), 
				'p_dest_kibou_date': $('#edit_kibou_date').val(),
				'p_dest_kibou_time': $('#edit_kibou_time').val(), 
			},//�L�[:�l
			function(data) {
				if (data == 'OK') {
					$num = $('#arrival_set_number').val();
					f_edit_arriving($num);
					// �l�̃N���A
					$('#edit_kibou_date').val('');
					$('#edit_kibou_time').val('');
					$('#arrival_set_number').val('');
					$('#arrival_edit_err').html('');
					$('#dialog_arrival').dialog('close');
				} else {
					$('#arrival_edit_err').html(data);
				}
			}
		);
		
	});
	
	// ���ʕύX
	$('.item_quantity').change(function(){
		var total_cnt=0,
			quantity_total=parseInt($('#quantity_total').html()),
			select_pay=false;
		$('.item_quantity').each(function() {
			total_cnt+=parseInt($(this).val());
		});
		$('.select_pay').each(function() {
			if($(this).prop('selected')===true){
				select_pay=true;
			}
		});
		
		if(total_cnt<=quantity_total){
			var serial_txt = $(this).attr('name'),
				quantity=$(this).val();
			serial_txt = serial_txt.replace("quantity_", "");
			if(serial_txt=='home'){
				serial_txt=0;
			}
			$.post(
				"<?php echo $_SERVER['SCRIPT_NAME'] ?>?p_kind=subquery",//script url
				{
					'p_dest_kind': 'quantity',
					'p_quantity_number': serial_txt,
					'quantity': quantity,
				},//�L�[:�l
				function(data) {
					if (data == 'OK') {
						location.href='/gift_order.php?p_kind=payment&seller_id=<?php echo $seller_id?>&p_scroll='+$(window).scrollTop();
					} else {
						$('#dest_add_err').html(data);
					}
				}
			);
		}	
		
		if(total_cnt>quantity_total){
			$('#btn_next').prop('disabled', true);
			alert('�������Ƃ��͂��搔�ʂ̍��v���Ⴂ�܂��B');
		}else if(select_pay===true){
			$('#btn_next').prop('disabled', false);
		}
	});
	
	// ������]�����̕ύX�𔽉f����
	function f_edit_arriving(num) {
		$t_arrival_word = '';
		$t_arrival_date = ' ';
		$t_arrival_time = '���Ԏw��: ';
		$t_arrival_date += $('#edit_kibou_date').val();
		$t_arrival_time += $('#edit_kibou_time').val();
		$t_arrival_word = $t_arrival_date + '�@' + $t_arrival_time;
		$('#dest_arrival_'+num).html($t_arrival_word);
		$('#kibou_date_'+num).val($('#edit_kibou_date').val());
		$('#kibou_time_'+num).val($('#edit_kibou_time').val());
	}
	
	
	function f_del_Hyphen(str) {
		return str.split('-').join('');
	}
	
	function noshi_radio() {
		$t_wrapping_set = $('input[name=wrapping_set]:checked').val();
		if ($t_wrapping_set == 2) {
			$('input[name=noshi_set]:radio').removeAttr("disabled");
			$('input[name=noshi_upper]').removeAttr("disabled");
			$('input[name=noshi_bottom]').removeAttr("disabled");
		} else {
			$('input[name=noshi_set]:radio').attr("disabled", "disabled");
			$('input[name=noshi_upper]').attr("disabled", "disabled");
			$('input[name=noshi_bottom]').attr("disabled", "disabled");
		}
	}
	
});

function after(){
	$('#new_card_radio_set').hide();
	$('#btn_next').prop('disabled', false);
	$('#jcb_card_form').html('�J�[�h���o�^�ς�');
	$('#modal_src').replaceWith('<span id="modal_src"></span>');
}

function f_scroll($f_jumpno){
	if($f_jumpno > 0){
		var p = $f_jumpno;
		$('html,body').animate({ scrollTop: p }, 'fast');
		return false;
	}
}

  </script>
</head>

<body class="d-flex flex-column" onLoad="f_scroll(<?php echo $p_scroll;?>);">

<!-- ���͂���ύX�_�C�A���O -->
<div id="dialog" title="���͂���ύX">
	<div id="dest_edit_err" style="color:red;"></div>
	<table class="kyotsu_tbl">
	<tr>
		<th>���O</th><td colspan="2"><input type="text" id="edit_dest_name" size="15"></td>
	</tr>
	<tr>
		<th width="300px">��(�n�C�t�������œ��͂��Ă�������)</th><td colspan="2"><input type="text" id="edit_dest_zip" size="15"></td>
	</tr>
	<tr>
		<th>��</th><td colspan="2"><?php echo $D_prefectures2; ?></td>
	</tr>
	<tr>
		<th>�s�撬���ȉ��̂��Z��</th><td colspan="2"><input type="text" id="edit_dest_add2" size="20"></td>
	</tr>
	<tr>
		<th>�d�b(�n�C�t�������œ��͂��Ă�������)</th><td><input type="text" id="edit_dest_tel" size="20"></td>
		<input type="hidden" id="edit_dest_number" value="">
		<td><input type="button" id="edit_dest_up" value="�ύX����"></td>
	</tr>
	</table>
</div>
<!-- ������]�����̐ݒ�_�C�A���O -->
<div id="dialog_arrival" title="������]�����ݒ�">
	<div id="arrival_edit_err" style="color:red;"></div>
	<table>
		<tr>
			<td colspan="2">�@��]�������F�@<?php echo $D_kibou_date_dest; ?>�@���Ԏw��F�@<?php echo $D_kibou_time_dest; ?><br />
				<div class="alert">
				����]�������͖{�����<?php echo ($_SESSION[DF_seller_info][$seller_id]['shipping_prepare'] + 1) ?>�c�Ɠ��ȍ~����ݒ肪�ł��܂��B<br />
				���x�Ɠ���V��s�ǂȂǂ̓s���ɂ��A�������̂���]��<br />
				�Y���Ȃ��ꍇ���������܂��B���炩���߂��������������B
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="button" id="edit_arrival_up" value="�ݒ肷��">
				<input type="hidden" id="arrival_set_number" value="">
			</td>
		</tr>
	</table>
</div>


<!-- ���b�s���O�̐ݒ�_�C�A���O -->
<div id="dialog_wrap" title="�̂��A���b�s���O�ݒ�">
	<div id="wrap_edit_err" style="color:red;"></div>
	<table>
	<tr>
<?php if ($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['wrapping_flg'] == 1) { ?>
		<td style="vertical-align: top;" class="kakkoi">
			<input type="radio" name="wrapping_set" value="1">���b�s���O<br >
			<span style="color:red">(<?php if ($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['wrapping_price'] > 0) { ?>1�� <?php } echo FM_price_format($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['wrapping_price'], '�~'); ?>)</span>
		</td>
<?php }
	  if ($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['noshi_flg'] == 1) {
?>
		<td style="vertical-align: top;" class="kakkoi">
			<input type="radio" name="wrapping_set" value="2">�̂�<br >
			<span style="color:red">(<?php if ($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['noshi_price'] > 0) { ?>1�� <?php } echo FM_price_format($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['noshi_price'], '�~'); ?>)<br ></span>
			<div style="margin-left:10px;">
				<��i><br >
				<div style="margin-left:5px;">
					<input type="radio" name="noshi_set" value="1" checked="checked">���Ε�<br>
					<input type="radio" name="noshi_set" value="2" >������<br>
					<input type="radio" name="noshi_set" value="3" ><input type="text" name="noshi_upper" value=""><br>
				</div>
				<���i><br >
				<div style="margin-left:5px;">
					<input type="text" name="noshi_bottom" value=""><br>
				</div>
			</div>
		</td>
<?php } ?>
		<td style="vertical-align: top;" class="kakkoi">
			<input type="radio" name="wrapping_set" value="0">���b�s���O�E�̂�����<br >
		</td>
	</tr>
	</table>
	<div class="btn" style="text-align:right;">
	<input type="hidden" id="wrap_set_number" value="">
	<input type="button" id="dest_wrap_set" value="�ݒ肷��">
	</div>
</div>


<div style="width:1340px;margin:0 auto;">

<!-- #header -->
<?php 
include("./header.php"); 
?>

<!-- end #header -->

<div class="container-fluid mx-0">
	
	<div class="row">
		
		
		<div class="col w-auto px-0">

			<nav aria-label="breadcrumb mb-5">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/">�g�b�v</a></li>
					<li class="breadcrumb-item">���W</li>
				</ol>
			</nav>
		
			<div class="p-3">

			<h4 class="border-bottom border-danger mb-3">���͂���Ƃ��x�����̂��w��</h4>
				<div class="container mb-5">

					<div class="row">
						<dl class="trans">
							<dt>���O�C��</dt>
							<dd>&#9654;</dd>
							<dt>�J�[�g</dt>
							<dd>&#9654;</dd>
							<dt class="here_now">���͂���<br>���x����</dt>
							<dd>&#9654;</dd>
							<dt>�m�F</dt>
							<dd>&#9654;</dd>
							<dt>����</dt>
						</dl>	
						<div class="col shop_item_list mb-5">
							
						<div class="alert alert-danger w-100 red bg-white h4 py-4 mt-4 mb-5 text-center">���̃T�C�g�͉����Ǝ҂̔̔�����l�����T�C�g�ł��̂ŁA<br>���i���̏��i�������̔����邽�ߍ�����͊ȈՓI�ƂȂ邱�Ƃ����������������B</div>
						
						<div class="alert alert-danger w-100">
							<div style="float:left;width:2em;background:#c00;color:#fff;text-align:center;margin-right:1em;">
								��<br>��<br>��
							</div>
							���x�����́A��������i�`���h�����j�E��s�U���i�U���萔���͂��q�l�̂����S�ɂȂ�܂��j�E�J�[�h����<img src="https://www.c-joy.jp/view/img/card_mini_vmj.png" alt="�J�[�h���ω�">�ɂȂ�܂��B<br>
							�������́A���X���ɂ��肢�v���܂��B�i�z�������́A�e�o�X�Ж��Ɋ|����܂��B�j<br>
							�������Ȃǂ�<strong>�y���ׂă��[���z</strong>�́A�u�}�C�y�[�W�v����<strong>�u���[���{�b�N�X�v</strong>�ɓ���܂��B
						</div>
								<div class="alert alert-light border border-secondary w-100">
								<h4>���X�́A<?php echo $_SESSION[DF_seller_info][$seller_id]['seller_shamei']?></h3>
								<!--<h6 class="text-right"><a href="#" target="_blank">���̂��X�̏��i�������ƌ���</a></h6>-->
								<div class="table-responsive shop_item_list">
									<table class="table table-condensed">
									<thead>
									<tr>
										<th></th>
										<th style="width:300px;">���i</th>
										<th class="text-center">�P��</th>
										<th class="text-center">������</th>
										<th class="text-center">���i</th>
									</tr>
									<?php foreach($_SESSION[DF_order_gift_cart][$seller_id] as $item_serial=>$item_arr){ ?>
										<tr>
											<td style="width:150px;max-height:150px;">
												<a href="/gift_item.php?moto_flg=<?php echo $item_arr['moto_flg']?>&type=<?php echo $item_arr['p_type']?>&p_serial=<?php echo $item_serial?>"><img src="/sel/upload_img/<?php echo $seller_id?>/<?php echo $item_arr['pic1']?>" alt="<?php echo $item_arr['product_name']; ?>" class="img-fluid"></a>
											</td>
											<td class="text-left"><a href="/gift_item.php?moto_flg=<?php echo $item_arr['moto_flg']?>&type=<?php echo $item_arr['p_type']?>&p_serial=<?php echo $item_serial?>"><?php echo $item_arr['product_name']; ?></a></td>
											<td class="text-right">��<?php echo number_format($item_arr['price'])?></td>
											<td class="text-center" id='quantity_total'><?php echo $item_arr['quantity']; ?></td>
											<td class="text-right" id="amount_<?php echo $item_arr['product_serial']?>">��<?php echo number_format($item_arr['price']*$item_arr['quantity']); ?></td>
										</tr>
									<?php } ?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">���v</td>
											<td class="text-right" id="sub_total_dis_<?php echo $seller_id?>">��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['sub_total']);?></td>
											<td class="text-center"></td>
										</tr>
										<tr>
											<td rowspan="2"></td>
											<td rowspan="2" class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">����</td>
											<td class="text-right" id="delivery_fee_dis_<?php echo $seller_id?>" nowrap>
												<?php 
												if($_SESSION[DF_seller_info][$seller_id]['delivery_flg']=='not_match'){ 
													echo '�o�X�Ђ��炨�m�点';
												}else{
													echo '��'.number_format($_SESSION[DF_seller_info][$seller_id]['delivery_fee']+$_SESSION[DF_seller_info][$seller_id]['gift_delivery_fee_total']);
												}
												?>
											</td>
											<td rowspan="2" class="text-center"></td>
										</tr>
										<tr>
											<td class="text-right" colspan="4" style="border-top:none;font-size:12px;">
												����E�����y�уN�[���ւ͕ʓr�������K�v�ł��B<br>
												�����́A��������̊m�F���[���ɂĐ��������������m�点�������܂��B<br>
												�z�����@�i�ʏ�ւƃN�[���ւ��������Ȃǁj�ő������ύX�����ꍇ������܂��B
											</td>
										</tr>

										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">�N�[����</td>
											<td class="text-right" id="cool_bin_dis_<?php echo $seller_id?>">��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']+$_SESSION[DF_seller_info][$seller_id]['gift_cool_bin_fee_total']);?></td>
											<td class="text-center"></td>
										</tr>
										<?php if(@$_SESSION[DF_seller_info][$seller_id]['discount_amount']>0){?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right" colspan="2">�I�[�v���L�����y�[���l�����i�������j</td>
											<td class="text-right" id="discount_dis_<?php echo $seller_id?>">-��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['discount_amount']);?></td>
											<td class="text-center"></td>
										</tr>
										<?php } ?>
										
										<?php if(@$_SESSION[DF_seller_info][$seller_id]['noshi_price']>0 || @$_SESSION[DF_seller_info][$seller_id]['wrapping_price']>0){?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right" colspan="2">���b�s���O�E�̂�</td>
											<td class="text-right" id="wrapping_dis_<?php echo $seller_id?>">��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['wrapping_price']+$_SESSION[DF_seller_info][$seller_id]['noshi_price']);?></td>
											<td class="text-center"></td>
										</tr>
										<?php } ?>
										
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">���v</td>
											<td class="text-right" id="total_dis_<?php echo $seller_id?>"><h2>��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['gift_cool_bin_fee_total']+$_SESSION[DF_seller_info][$seller_id]['gift_delivery_fee_total']+$_SESSION[DF_seller_info][$seller_id]['noshi_price']+$_SESSION[DF_seller_info][$seller_id]['wrapping_price']+$_SESSION[DF_seller_info][$seller_id]['total']);?></h2></td>
											<td class="text-center"></td>
										</tr>
									</table>
								</div>
							</div>
							
			<form id="f_payment_frm" name="payment_frm" method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
					<div class="mb-5" style="margin-bottom:8px;">
						<h4 class="mb-3 border-bottom border-danger">
							<input type="hidden" class="p_destination" name="p_forhome" value="<?php echo $p_home_quantity>0?1:0;?>">
							���͂���
						</h4>
							<table id="home_tbl" class="table">
								
								<tr>
									<th style="width:140px;" class="text-right">����</th>
									<td><?php echo $buyer_name ?>&nbsp;�l
									<input type="hidden" name="buyer_name" id="buyer_name" size="40" maxlength="100" value="<?php echo $buyer_name ?>"></td>
									<td class="text-center">����
									<select class="item_quantity" name="quantity_home">
										<option value='0'>0</option>
										<?php for($i=1; $i<=$item_arr['quantity']; $i++){
											if($p_home_quantity==$i){
												echo "<option value='$i' selected >$i</option>";
											}else{
												echo "<option value='$i'>$i</option>";
											}
										} ?>
									</select>
									</td>
								</tr>
								<tr>
									<th class="text-right">�Z��</th>
									<td colspan="2">
										��<?php echo substr($buyer_zip,0,3)."-".substr($buyer_zip,3) ?><input type="hidden" name="buyer_zip" id="buyer_zip" class="mid_txt" maxlength="7" size="10" value="<?php echo $buyer_zip ?>" onKeyUp="AjaxZip3.zip2addr(this,'','buyer_pref','buyer_address');"><br />
										�@<?php echo $buyer_pref ?><?php echo $buyer_add ?>
										�@<input type="hidden" name="buyer_address" id="buyer_address" size="40" maxlength="100" value="<?php echo $buyer_add ?>">										
									</td>									
								</tr>
								<tr>
									<th class="text-right">�A����</th>
									<td colspan="2">
									�@<?php echo substr($buyer_tel, 0, -8)."-".substr($buyer_tel, -8, 4)."-".substr($buyer_tel, -4) ?><input type="hidden" name="buyer_tel" id="buyer_tel" size="12" maxlength="15" value="<?php echo $buyer_tel ?>">
									</td>
								</tr>
								
								<tr>
									<th class="text-right">��]������</th>
									<td colspan="2">�@<?php echo $_SESSION[DF_seller_info][$seller_id]['kibou_date']; ?>�@���Ԏw��F�@<?php echo $_SESSION[DF_seller_info][$seller_id]['kibou_time']; ?><br />
										<div class="alert">
										����]�������͖{�����<?php echo ($_SESSION[DF_seller_info][$seller_id]['shipping_prepare'] + 1) ?>�c�Ɠ��ȍ~����ݒ肪�ł��܂��B<br />
										���x�Ɠ���V��s�ǂȂǂ̓s���ɂ��A�������̂���]��<br />
										�Y���Ȃ��ꍇ���������܂��B���炩���߂��������������B
										</div>
									</td>
								</tr>
							</table>
							<br />
							
						
							<?php
								if (isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]) && is_array($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest])) {
									foreach ($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest] as $cnt => $dat) {
							?>
							
							<h4 class="mb-3 border-bottom border-danger">
								<!--<input type="checkbox" class="p_destination" name="dest_need_<?php echo $dat['serial']; ?>" value="1"<?php if ((int)$dat['need'] > 0) { ?> checked="checked"<?php } ?>>-->
								���͂���
							</h4>
							<table id="home_tbl" class="table">
								<tr>
									<th style="width:155px;" class="text-right">����</th>
									<td><span id="dest_name_<?php echo $dat['serial']; ?>"><?php echo $dat['dest_name']; ?></span>&nbsp;�l</td>
									<td>
										<input type="button" class="dest_edit" name="dest_edit_<?php echo $dat['serial']; ?>" value="�ύX">
										<input type="button" class="dest_del" name="dest_del_<?php echo $dat['serial']; ?>" value="�폜">
									</td>
									<td class="text-center">����
									<select class="item_quantity"  name="quantity_<?php echo $dat['serial']; ?>" >
										<option value='0'>0</option>
										<?php for($i=1; $i<=$item_arr['quantity']; $i++){
											if($dat['quantity']==$i){
												echo "<option value='$i' selected >$i</option>";
											}else{
												echo "<option value='$i'>$i</option>";
											}
										} ?>
									</select>
									<!--<span class="quant" id="quant_<?php echo $dat['serial']; ?>"><?php echo $dat['need']; ?></span>-->
									</td>
								</tr>
								
								<tr>
									<th class="text-right">�Z��</th>
									<td colspan="3">
										��<span id="dest_zip_<?php echo $dat['serial']; ?>"><?php echo FM_shape_zip($dat['dest_zip']); ?></span>
										<span id="dest_add1_<?php echo $dat['serial']; ?>"><?php echo $dat['dest_address1']; ?></span>
										<span id="dest_add2_<?php echo $dat['serial']; ?>"><?php echo $dat['dest_address2']; ?></span>
									</td>
								</tr>
								<tr>
									<th class="text-right">�A����</th>
									<td colspan="3">
									�@<span id="dest_tel_<?php echo $dat['serial']; ?>"><?php echo $dat['dest_tel']; ?></span>
									</td>
								</tr>
								
								<tr>
									<th class="text-right">���b�s���O�E�̂�</th>
									<td colspan="2">
										<span class="wrapping_word" id="dest_wrapping_<?php echo $dat['serial']; ?>"><?php echo $dat['wrapping_word']; ?></span>
										<input type="hidden" id="wrapping_flg_<?php echo $dat['serial']; ?>" value="<?php echo $dat['wrapping_set']['wrapping'] ?>">
										<input type="hidden" id="noshi_top_<?php echo $dat['serial']; ?>" value="<?php echo $dat['wrapping_set']['noshi_upper'] ?>">
										<input type="hidden" id="noshi_bottom_<?php echo $dat['serial']; ?>" value="<?php echo $dat['wrapping_set']['noshi_bottom'] ?>">
									</td>
									<td>
										<input type="button" class="dest_wrapping" name="dest_wrapping_<?php echo $dat['serial']; ?>" value="�̂��A���b�s���O�ݒ�">
									</td>
								</tr>
								<tr>
									<th class="text-right">��]������</th>
									<td colspan="2"><span class="arrival_word" id="dest_arrival_<?php echo $dat['serial']; ?>"><?php echo $dat['kibou_datetime']['kibou_date'] ?>�@���Ԏw��F�@<?php echo $dat['kibou_datetime']['kibou_time'] ?></span><br />
										<input type="hidden" name="kibou_date_<?php echo $dat['serial']; ?>" id="kibou_date_<?php echo $dat['serial']; ?>" value="<?php echo $dat['kibou_datetime']['kibou_date'] ?>">
										<input type="hidden" name="kibou_time_<?php echo $dat['serial']; ?>" id="kibou_time_<?php echo $dat['serial']; ?>" value="<?php echo $dat['kibou_datetime']['kibou_time'] ?>">	
									</td>
									<td>
										<input type="button" class="dest_arrival" name="dest_arrival_<?php echo $dat['serial']; ?>" value="��]���������ݒ�">
									</td>
								</tr>
							</table>
							<br />
							
							<?php
								}
							}
							?>
							
							<input type="hidden" name="p_dest_size" id ="p_dest_size" value="<?php echo @$p_dest_size ?>">
							<div id="d_shipping_tbl">
								<table>
									<tr><td colspan="3"><input type="button" id="btn_ins_dest" value="�V�������͂����o�^����"><td><tr>
								</table>
								<div id="ins_dest">
									<div id="dest_add_err"></div>
									<table class="kyotsu_tbl">
										<tr>
											<td rowspan="7">�ǉ�</td>
											<th>���O</th><td colspan="2"><input type="text" id="new_dest_name" size="15"></td>
										</tr>
										<tr>
											<th>��<br><span class="small">(�n�C�t�������œ��͂��Ă�������)</span></th><td colspan="2"><input type="text" id="new_dest_zip" size="15"></td>
										</tr>
										<tr>
											<th>��</th><td colspan="2"><?php echo $D_prefectures; ?></td>
										</tr>
										<tr>
											<th>�s�撬��</th><td colspan="2"><input type="text" id="new_dest_add2" size="20"></td>
										</tr>
										<tr>
											<th>�Ԓn�A������</th><td colspan="2"><input type="text" id="new_dest_add3" size="20"></td>
										</tr>
										<tr>
											<th>�d�b<br><span class="small">(�n�C�t�������œ��͂��Ă�������)</span></th><td colspan="2"><input type="text" id="new_dest_tel" size="20"></td>
										</tr>
										<tr>
											<td colspan="3" style="text-align:right;"><input type="button" id="new_dest_add" value="�ǉ�����"></td>
										</tr>
									</table>
								</div>
							</div>
						<div class="mb-5">
							<h4 class="mb-3 border-bottom border-danger">���x�����@�I��</h4>
								<div id="payment_method" class="choice_fm">
									<?php if (!empty($D_mess)) { ?>
									<div id="d_errors">
									<?php echo $D_mess; ?>
									</div>
									<?php } ?>
									
							
									<label><input type="radio" name="select_pay" id="card_pay" class="select_pay" value="5">�N���W�b�g�J�[�h<img src="/view/img/card_mini_vmj.png" alt="VISA/master/JCB"></label>
									<br />
									<div id="card_info_div" style="display:none;">
										<?php if(@$card_info['cardData']['cardKey']){?>
											<div id="use_saved_card_div" style="display:inline">
											�J�[�h�ԍ��F<?php echo $card_info['cardData']['maskingCardNo']?><br />
											�J�[�h���`�l�F<?php echo $card_info['cardData']['cardOwner']?><br />
											�L������:<?php echo $card_info['cardData']['cardExp']?><br />
											</div>
											<div style="margin-left:15px;" id="btn_save_card">
												<label style="margin-right:15px;"><input type="radio" name="card_type" id="use_saved_card">��L�̃J�[�h�Ō��ς���</label>
												<label><input type="radio" name="card_type" id="new_card">�ʃJ�[�h�Ō��ς���</label><br />
											</div>
											<input type="hidden" id="save_card_flg" value="1">
										<?php }else{ ?>
											<input type="hidden" id="save_card_flg" value="0">
										<?php } ?>	
									</div>
									
									<div style="margin-left:15px; display:none;" id="new_card_radio_set">
										<label style="margin-right:15px;"><input type="radio" name="card_use_type" checked="checked" id="save_card" value="save_card">��������̃J�[�h���g��</label>
										<label><input type="radio" name="card_use_type" id="purchase_only" value="this_time">����݂̂��̃J�[�h���g��</label><br />
									</div>
									
									<div style="margin-left:15px; display:none;" id="jcb_card_form">
										<input type="button" value="�J�[�h�����̓t�H�[��" id="create-token-launch"><br /><br />
									</div>
									<p style="margin-left:15px;color:blue;">
										�J�[�h���ς́A�u���}�g�t�B�i���V����������Ёv�̃N���W�b�g�J�[�h���σT�[�r�X�ŏ�������܂��B<br>
										���Ђł́A�N���W�b�g�J�[�h������ؕێ����܂���B
									</p>
									<script id="modal_src"></script>
									
									<?php if(@$_SESSION['jcb_card_request_data']['webcollectToken']) echo '<span id="have_token">�J�[�h���o�^�ς�</span><br />';?>
									
									<?php if($daibiki_flg==true){?>
									<br />
									<label><input type="radio" name="select_pay" class="select_pay" id="delivery_pay" value="0">�������</label>
									<div style="margin-left:15px;color:blue">
										������萔���́A�̔��X�����S���܂��B
									</div>
									<?php } ?>
									
									<br />
									<br />
									<label><input type="radio" name="select_pay" class="select_pay" id="bank_transfer_pay" value="6">��s�U��</label></br>
									<div style="margin-left:15px;color:blue">
										�U���萔���́A�����S���������܂��悤���肢���܂��B<br>
										���U���ݐ�́A�o�X�Ђ���̊m�F���[���ł��m�点���܂��B<br>
										��s�����m�F��ɔ������܂��B
									</div>
								</div>
						</div>

			
						<!--<div class="mb-5">
							<h4 class="mb-3 border-bottom border-danger">�̔��X�ւ̗v�]�Ȃ�</h4>
							<textarea id="p_order_comment" name="p_order_comment" rows="4" style="width:350px;"><?php echo @$p_order_comment; ?></textarea>
						</div>-->
						<input type="hidden" name="p_order_comment" value="">


								<input type="hidden" name="p_kind" id="p_kind" value="<?php echo $p_kind; ?>">
								<input type="hidden" name="seller_id" id="seller_id" value="<?php echo $seller_id; ?>">
								<input type="hidden" name="p_gift_flg" id="p_gift_flg" value="<?php echo @$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_gift_setting]; ?>">
								<input type="hidden" name="p_payment" id="p_payment" value="">
								<input type="hidden" name="p_type" id="p_type" value="<?php echo $item_arr['p_type']; ?>">
								
								<?php
									if($err_message !== ''){
			 							echo "<div class='alert alert-danger w-100 red bg-white h4 py-4 mt-4 mb-5 text-center'>";
										echo $err_message;
										echo "</div>";
									}
								?>
								<div class="text-center">
									<input class="btn btn-secondary btn-lg mr-5" type="button" id="btn_back" value="�߂�">
									<input class="btn btn-danger btn-lg" type="button" id="btn_next" value="�m�F��ʂ�">
								</div>
								</form>
							</div>


						</div><!-- /col-md-5 -->
					</div><!-- /row -->
				</div>
			</div>
		</div><!-- /p-5 -->

	</div><!-- /col-md-10 -->
	</div>
<!-- Footer -->
<?php include_once('./footer.php');?>
<!-- / Footer -->
</div>
</body>
</html>
