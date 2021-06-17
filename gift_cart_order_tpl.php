<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis" />
<?php echo DF_meta_tag_for_viewport; ?>
<title>���͂���y�[�W�b���i�w��</title>
<link rel="stylesheet" type="text/css" media="all" href="/view/css/reset.css" />
<link rel="stylesheet" type="text/css" media="all" href="/view/css/style.css" />
<link rel="stylesheet" href="/view/lib/jquery_ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<!--Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<!--Font Awesome5-->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
<style type="text/css">
 <!--

		html,
		body {
			height: 100%;
		}

		header {border-bottom:.1rem solid #c00;}

		#page-content {
			flex: 1 0 auto;
		}

		#sticky-footer {
			flex-shrink: none;
		}


		#accordion .card-header a[data-toggle="collapse"].collapsed::after {
			font-family: 'Font Awesome 5 Free';
			font-weight: 900;
			content: "\f078";
			float: right;
		}
		#accordion .card-header a[data-toggle="collapse"]::after {
			font-family: 'Font Awesome 5 Free';
			font-weight: 900;
			content: "\f077";
			float: right;
		}
		.card-block img {
			max-width:100%;
			min-height:200px;
			max-height:200px;
			object-fit:contain;
		}
 		.card > a > img {
			max-width:100%;
			height:200px;
			object-fit:cover;
		}

		.item_list .card {border:none;margin-bottom:2rem;}
		.item_list .card-block {border-bottom:1px solid #ccc;}
		
		.item_list .card-block img {
			max-width:100%;
			min-height:200px;
			max-height:200px;
			object-fit:cover;
		}
		
		.collapse > .card-body {padding:0;}
		.carousel-control-next, .carousel-control-prev {
			width: 5%;
			opacity:0.2;
		}
		.side-cus .card-header h5 a {
			font-size:0.7em !important;
			}
		.side-cus li a {
			font-size:0.8em !important;
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

        -->
		@media (max-width: 767.98px) {
		html {
			font-size: 0.9rem;
		}
		dl.trans dt {
			padding: .7em 0.5em .7em .2em ;
		}
		select {
			font-size: 1.2rem;
		}
		.table td, .table th {
			border-top: none;
		}
		.table .cancel{
			border-bottom: 1px solid #dee2e6;
		}
	}
</style>
<script type="text/javascript" src="/view/lib/jquery-1.11.3.js"></script>
<script type="text/javascript" src="/view/lib/jquery_ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript">
$(function(){
	//$('#discount_tr').hide();
	<?php if(@$_SESSION[DF_seller_info][$seller_id]['discount_amount']>0){?>
	//$('#discount_tr').show();
	<?php }?>
	
	<?php foreach($_SESSION[DF_order_gift_cart] as $seller_id=>$seller_arr){?>
			var seller_id='<?php echo $seller_id?>';
		<?php if($_SESSION[DF_seller_info][$seller_id]['until_free_shipping']>0){ ?>
				$('.until_free_shipping_'+seller_id).show();
				$('.free_shipping_'+seller_id).hide();
				$('.seller_item_add_'+seller_id).show();
			<?php }else{ ?>
				$('.until_free_shipping_'+seller_id).hide();
				$('.free_shipping_'+seller_id).show();
				$('.seller_item_add_'+seller_id).hide();
	<?php 	} 
		} 
	?>
	
	$('.home_quantity').spinner({
		min: 1,
		step: 1,
		spin: function(event, ui) {
			var quantity=ui.value,
				price=$(this).attr('price'),
				seller_id=$(this).attr('seller_id'),
				item_serial=$(this).attr('item_serial'),
				amount=price*quantity;
			
			$('#pay_form_'+seller_id).prop('disabled', true);
			
			if(parseInt(quantity)==0 ) {
			   alert('��������0�s�ł��B');
			}else if(isNaN(Number(quantity))  || quantity != Math.floor(quantity) ) {
			   alert('�������͔��p�����������͂��Ă��������B');
			}else{
				$('#amount_'+item_serial).html('��'+separate(amount));
				$.ajax({
					method: "POST",
					url: "gift_order.php",
					data: { p_kind: "update_item_quantity", seller_id: seller_id, item_serial:item_serial, quantity:quantity },
					success: function(data, dataType){
						if(data==null){
							alert('�������X�V�G���[');
						}else{
							data = $.parseJSON(data);
							$('#sub_total_dis_'+seller_id).html('��'+Number(data[0]).toLocaleString());
							$('#total_dis_'+seller_id).html('<h2>��'+Number(data[2]).toLocaleString()+'</h2>');
							$('#cool_bin_dis_'+seller_id).html('��'+Number(data[3]).toLocaleString());
							if(parseInt(data[4])>0){
								$('.until_free_shipping_'+seller_id).show();
								$('.free_shipping_'+seller_id).hide();
								$('.until_amount_'+seller_id).html(separate(parseInt(data[4])));
								$('.seller_item_add_'+seller_id).show();
							}else{
								$('.until_free_shipping_'+seller_id).hide();
								$('.free_shipping_'+seller_id).show();
								$('.seller_item_add_'+seller_id).hide();
							}
							if(data[5]=='not_match'){
								$('#delivery_fee_dis_'+seller_id).html('�o�X�Ђ��炨�m�点');
							}else{
								$('#delivery_fee_dis_'+seller_id).html('��'+Number(data[1]).toLocaleString());
							}
							if(data[6]){
								location.href='/gift_order.php?p_kind=login_check';
							}
						}
						$('#pay_form_'+seller_id).prop('disabled', false);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown){
						alert('�G���[���������܂��� #1 : ' + errorThrown);
					}
				})
			}
			
		},
		change: function(event, ui) {
			var quantity=$(this).attr('aria-valuenow'),
				price=$(this).attr('price'),
				max=$(this).attr('max'),
				seller_id=$(this).attr('seller_id'),
				item_serial=$(this).attr('item_serial'),
				amount=0;
			
			$('#pay_form_'+seller_id).prop('disabled', true);
			
			if(parseInt(quantity)==0 ) {
			   alert('��������0�s�ł��B');
			}else if(isNaN(Number(quantity)) || quantity != Math.floor(quantity) ) {
			   alert('�������͔��p�����������͂��Ă��������B');
			}else{
				if(parseInt(quantity)>parseInt(max)){
					alert(quantity+'���ʂ�'+max+'�܂łł�');
					quantity=max;
					$(this).spinner("stepUp", max);
				}
				
				amount=price*quantity;
				
				$('#amount_'+item_serial).html('��'+separate(amount));
				$.ajax({
					method: "POST",
					url: "gift_order.php",
					data: { p_kind: "update_item_quantity", seller_id: seller_id, item_serial:item_serial, quantity:quantity },
					success: function(data, dataType){
						if(data==null){
							alert('�������X�V�G���[');
						}else{
							data = $.parseJSON(data);
							$('#sub_total_dis_'+seller_id).html('��'+Number(data[0]).toLocaleString());
							$('#total_dis_'+seller_id).html('<h2>��'+Number(data[2]).toLocaleString()+'</h2>');
							$('#cool_bin_dis_'+seller_id).html('��'+Number(data[3]).toLocaleString());
							if(parseInt(data[4])>0){
								$('.until_free_shipping_'+seller_id).show();
								$('.free_shipping_'+seller_id).hide();
								$('.until_amount_'+seller_id).html(separate(parseInt(data[4])));
								$('.seller_item_add_'+seller_id).show();
							}else{
								$('.until_free_shipping_'+seller_id).hide();
								$('.free_shipping_'+seller_id).show();
								$('.seller_item_add_'+seller_id).hide();
							}
							if(data[5]=='not_match'){
								$('#delivery_fee_dis_'+seller_id).html('�o�X�Ђ��炨�m�点');
							}else{
								$('#delivery_fee_dis_'+seller_id).html('��'+Number(data[1]).toLocaleString());
							}
							if(data[6]){
								location.href='/gift_order.php?p_kind=login_check';
							}
						}
						$('#pay_form_'+seller_id).prop('disabled', false);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown){
						alert('�G���[���������܂��� #2 : ' + errorThrown);
					}
				})
			}
			
			
		}
	});
	
		
	// �����p�Ɏg��
	$('#btn_gift').click(function(){
		$('#p_kind').val('shipping');
		$('#gift_flg').val('1');
		$(this.form).submit();
	});

	// ���x������
	$('.btn_next').click(function(){
		var frm_id=$(this).attr('frm_id');
		$('#'+frm_id).submit();
	});
	
	$('.item_delete').click(function(){
		var seller_id=$(this).attr('seller_id'),
			item_serial=$(this).attr('item_serial');
		
		$.ajax({
		  method: "POST",
		  url: "gift_order.php",
		  data: { p_kind: "delete_cart_item", seller_id: seller_id, item_serial:item_serial }
		})
		.success(function(msg){
			location.href='/gift_order.php?p_kind=login_check';
		});
		
	});
	
	// 3�����J���}��؂�ɂ���
	separate = function(num){
		return String(num).replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
	}

});

</script>
<?php include_once("/var/www/vhosts/c-joy.co.jp/ga_code/analyticstracking_order.php") ?>
</head>

<body class="d-flex flex-column">
<div style="width:1340px;margin:0 auto;">
<!-- #header -->

<?php include("./header.php"); ?>

<!-- end #header -->

<div class="container-fluid mx-0">
	
	<div class="row">
		
		
		<div class="col w-auto px-0">

			<nav aria-label="breadcrumb mb-5">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/">�g�b�v</a></li>
					<li class="breadcrumb-item">�J�[�g</li>
				</ol>
			</nav>
		
			<div class="p-3">

			<h4 class="border-bottom border-danger mb-3">�J�[�g</h4>
				<div class="container mb-5">

					<div class="row">
					<?php if (empty($member_regist_arr)) { ?>
						<dl class="trans">
							<dt>���O�C��</dt>
							<dd>&#9654;</dd>
							<dt class="here_now">�J�[�g</dt>
							<dd>&#9654;</dd>
							<dt>���͂���<br>���x����</dt>
							<dd>&#9654;</dd>
							<dt>�m�F</dt>
							<dd>&#9654;</dd>
							<dt>����</dt>
						</dl>
					<?php } else { ?>
						<dl class="trans">
							<dt>����o�^</dt>
							<dd>&#9654;</dd>
							<dt>����o�^<br>�m�F</dt>
							<dd>&#9654;</dd>
							<dt class="here_now">���͂���<br>���x����</dt>
							<dd>&#9654;</dd>
							<dt>�m�F</dt>
							<dd>&#9654;</dd>
							<dt>����</dt>
						</dl>
					<?php }  ?>
					<!--<div class="col shop_item_list mb-5">-->
						
					<div class="alert alert-danger w-100 red bg-white h4 py-4 mt-4 mb-5 text-center">���̃T�C�g�͉����Ǝ҂̔̔�����l�����T�C�g�ł��̂ŁA<br>���i���̏��i�������̔����邽�ߍ�����͊ȈՓI�ƂȂ邱�Ƃ����������������B</div>

						<div class="alert alert-danger w-100">
							<div style="float:left;width:2em;background:#c00;color:#fff;text-align:center;margin-right:1em;">
								��<br>��<br>��
							</div>
							���x�����́A��������i�`���h�����j�E��s�U���i�U���萔���͂��q�l�̂����S�ɂȂ�܂��j�E�J�[�h����<img src="https://www.c-joy.jp/view/img/card_mini_vmj.png" alt="�J�[�h���ω�">�ɂȂ�܂��B<br>
							�������́A���X���ɂ��肢�v���܂��B�i�z�������́A�e�o�X�Ж��Ɋ|����܂��B�j<br>
							�������Ȃǂ�<strong>�y���ׂă��[���z</strong>�́A�u�}�C�y�[�W�v����<strong>�u���[���{�b�N�X�v</strong>�ɓ���܂��B
						</div>

						<?php 
							foreach($_SESSION[DF_order_gift_cart] as $seller_id=>$seller_arr){
								if(empty($seller_id)) continue;
						?>
							<div class="alert alert-light border border-secondary w-100">
							
							<form id="f_shipping_frm_<?php echo $seller_id?>" name="shipping_frm" method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
							
							<h4>
								<span>���X�́A<?php echo $_SESSION[DF_seller_info][$seller_id]['seller_yagou']?></span>
								<span class="until_free_shipping_nakayamaya0021 float-right">
									<span class='until_free_shipping_<?php echo $seller_id?>' style="display:none;">�y���������܂ł���<span style="color:blue" class="until_amount_<?php echo $seller_id?>"><?php echo number_format($_SESSION[DF_seller_info][$seller_id]['until_free_shipping'])?></span>�~�z</span>
								</span>
							</h4>
							<h6 class="text-right"><a href="/seller_item_list.php?seller_id=<?php echo$seller_id;?>" target="_blank">���̂��X�̏��i�������ƌ���</a></h6>

								<div class="table-responsive shop_item_list">
									<table class="table table-condensed">
									<thead>
									<tr>
										<th></th>
										<th style="width:300px;">���i</th>
										<th class="text-center">�P��</th>
										<th class="text-center">����</th>
										<th class="text-center">���i</th>
										<th class="text-center">������</th>
									</tr>
										<?php foreach($seller_arr as $item_serial=>$item_arr){ ?>

											<tr>
												<td style="width:150px;max-height:150px;">
													<a href="/gift_item.php?moto_flg=<?php echo $item_arr['moto_flg']?>&type=<?php echo $item_arr['p_type']?>&p_serial=<?php echo $item_serial?>"><img src="/sel/upload_img/<?php echo $seller_id?>/<?php echo $item_arr['pic1']?>" alt="<?php echo $item_arr['product_name']; ?>" class="img-fluid"></a>
												</td>
												<td class="text-left"><a href="/gift_item.php?moto_flg=<?php echo $item_arr['moto_flg']?>&type=<?php echo $item_arr['p_type']?>&p_serial=<?php echo $item_serial?>"><?php echo $item_arr['product_name']; ?></a></td>
												<td class="text-right">��<?php echo number_format($item_arr['price'])?></td>
												<td class="text-center"><input type="text" name="quantity_<?php echo $item_serial?>" class="home_quantity" seller_id="<?php echo $seller_id?>" item_serial="<?php echo $item_arr['product_serial']?>" max="<?php echo ($item_arr['zaiko_flg']==1?$item_arr['zaiko']:'')?>" value="<?php echo $item_arr['quantity']; ?>" price="<?php echo $item_arr['price']?>" size="2"></td>
												<td class="text-right" id="amount_<?php echo $item_arr['product_serial']?>">��<?php echo number_format($item_arr['price']*$item_arr['quantity']); ?></td>
												<td class="text-center"><input type="button" class="item_delete btn btn-outline-dark" seller_id="<?php echo $seller_id?>" item_serial="<?php echo $item_arr['product_serial']?>" value="������"></td>
											</tr>										
														
										<?php } ?>
										
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">���v</td>
											<td class="text-right" id="sub_total_dis_<?php echo $seller_id?>">��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['sub_total']);?></td>
											<td class="text-center"></td>
										</tr>
										
										<tr>
											<td rowspan="2"></td>
											<td rowspan="2" class="text-left"></td>
											<td class="text-left" nowrap>
												<span class='until_free_shipping_<?php echo $seller_id?>' style="display:none;">�y���������܂ł���<span style="color:blue" class="until_amount_<?php echo $seller_id?>"><?php echo number_format($_SESSION[DF_seller_info][$seller_id]['until_free_shipping'])?></span>�~�z</span>
											</td>
											<td class="text-right" nowrap>
												<span class="seller_item_add_<?php echo $seller_id?>" style="display:none;"><a href="/seller_item_list.php?seller_id=<?php echo$seller_id;?>" target="_blank">���i��ǉ�����</a></span>
												<span style="color:blue; display:none;" class='free_shipping_<?php echo $seller_id?>'>�y���������z</span>
											</td>
											<td class="text-right">����</td>
											<td class="text-right" id="delivery_fee_dis_<?php echo $seller_id?>" nowrap>
												<?php 
												if(isset($_SESSION[DF_seller_info][$seller_id]['delivery_flg']) && $_SESSION[DF_seller_info][$seller_id]['delivery_flg']=='not_match'){ 
													echo '�o�X�Ђ��炨�m�点';
												}else{
													echo '��'.number_format($_SESSION[DF_seller_info][$seller_id]['delivery_fee']);
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
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">�N�[����</td>
											<!--<td class="text-right" id="cool_bin_dis_<?php echo $seller_id?>">��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']);?></td>-->
											<td class="text-right">���͂���w���Ɍv�Z����܂�</td>
											<td class="text-center"></td>
										</tr>
										<?php if(@$_SESSION[DF_seller_info][$seller_id]['discount_amount']>0){?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-left"></td>
											<td class="text-right" colspan="2">�I�[�v���L�����y�[���l�����i�������j</td>
											<td class="text-right" id="discount_dis_<?php echo $seller_id?>">-��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['discount_amount']);?></td>
											<td class="text-center"></td>
										</tr>
										<?php } ?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">���v</td>
											<td class="text-right" id="total_dis_<?php echo $seller_id?>"><h2>��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['total']-$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']);?></h2></td>
											<td class="text-center"></td>
										</tr>
									</table>
							
									<input type="hidden" name="p_gift_flg" value="0">
									<input type="hidden" name="p_forhome" value="1">
									<input type="hidden" name="p_kind" value="payment">
									<input type="hidden" name="seller_id" value="<?php echo $seller_id?>">
									<input type="hidden" name="p_payment" value="<?php echo $p_payment; ?>">
									<input type="hidden" name="p_select_point" value="<?php echo $p_select_point; ?>">
									<input type="hidden" name="p_use_point" value="<?php echo $p_use_point; ?>">									
								</div>
								<div class="text-right mt-2">��L���i�́A<?php echo $_SESSION[DF_seller_info][$seller_id]['seller_yagou']?>���z���v���܂��B
									<input type="button" id="pay_form_<?php echo $seller_id?>" class="btn_next btn btn-danger btn-lg" frm_id="f_shipping_frm_<?php echo $seller_id?>" value="���͂���E���x�������@��">
								</div>
							</form>
							</div>							
							<?php } ?>
					
					<!--</div>-->
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
