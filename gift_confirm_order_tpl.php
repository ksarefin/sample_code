<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis" />
<?php echo DF_meta_tag_for_viewport; ?>
<title>�m�F�y�[�W�b���i�w��</title>
<link rel="stylesheet" type="text/css" media="all" href="view/css/reset.css" />
<link rel="stylesheet" type="text/css" media="all" href="view/css/style.css" />
<link rel="stylesheet" href="/view/lib/jquery_ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<!--Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<!--Font Awesome5-->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
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
#payment_method {
		margin: 15px;
		padding: 5px;
		font-size: 14px;
}
.btn > input {
		margin-top: 15px;
		padding: 5px;
		min-width: 120px;
		font-size: 14px;
	}
.btn {
		margin-bottom: 15px;
	}
#home_tbl {
		margin-top: 15px;
		margin-bottom: 15px;
	}
.shipping_tbl{
		width: 100%;
		margin-bottom: 15px;
	}
.b_total {
		border-bottom: 3px double #CCC;
	}
.no_side td {
		border-left: 0px none #FFF;
		border-right: 0px none #FFF;
	}
.small {
		font-size:11px;
		color:#C00 ;
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
</style>
<script type="text/javascript" src="/view/lib/jquery-1.11.3.js"></script>
<script type="text/javascript">
$(function(){
	
	// �߂��
	$('#btn_back').click(function(){
		$('#p_kind').val('payment');
		$(this.form).attr('action', './gift_order.php');
		$(this.form).submit();
	});

	$('#btn_next').click(function(){
		if (confirm('�w�����m�肵�܂��B��낵���ł����H')) {
			$('#p_kind').val('complete');
			$(this.form).submit();
		}
	});
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

			<h4 class="border-bottom border-danger mb-3">�������m�F</h4>
				<div class="container mb-5">

					<div class="row">
					
						<dl class="trans">
							<dt>���O�C��</dt>
							<dd>&#9654;</dd>
							<dt>�J�[�g</dt>
							<dd>&#9654;</dd>
							<dt>���͂���<br>���x����</dt>
							<dd>&#9654;</dd>
							<dt class="here_now">�m�F</dt>
							<dd>&#9654;</dd>
							<dt>����</dt>
						</dl>

						<div class="col shop_item_list mb-5 px-0">
						
						<div class="alert alert-danger w-100 red bg-white h4 py-4 mt-4 mb-5 text-center">���̃T�C�g�͉����Ǝ҂̔̔�����l�����T�C�g�ł��̂ŁA<br>���i���̏��i�������̔����邽�ߍ�����͊ȈՓI�ƂȂ邱�Ƃ����������������B</div>
						
						<div class="alert alert-danger w-100">
							<div style="float:left;width:2em;background:#c00;color:#fff;text-align:center;margin-right:1em;">
								��<br>��<br>��
							</div>
							���x�����́A��������i�`���h�����j�E��s�U���i�U���萔���͂��q�l�̂����S�ɂȂ�܂��j�E�J�[�h����<img src="https://www.c-joy.jp/view/img/card_mini_vmj.png" alt="�J�[�h���ω�">�ɂȂ�܂��B<br>
							�������́A���X���ɂ��肢�v���܂��B�i�z�������́A�e�o�X�Ж��Ɋ|����܂��B�j<br>
							�������Ȃǂ�<strong>�y���ׂă��[���z</strong>�́A�u�}�C�y�[�W�v����<strong>�u���[���{�b�N�X�v</strong>�ɓ���܂��B
						</div>
							<div class="alert alert-light w-100 px-0">
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
											<td class="text-center"><?php echo $item_arr['quantity']; ?></td>
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
											<td class="text-right" colspan="3" style="border-top:none;font-size:12px;">
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
											<td class="text-right">���x�����v���z</td>
											<td class="text-right" id="total_dis_<?php echo $seller_id?>"><h2>��<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['gift_cool_bin_fee_total']+$_SESSION[DF_seller_info][$seller_id]['gift_delivery_fee_total']+$_SESSION[DF_seller_info][$seller_id]['noshi_price']+$_SESSION[DF_seller_info][$seller_id]['wrapping_price']+$_SESSION[DF_seller_info][$seller_id]['total']);?></h2></td>
											<td class="text-center"></td>
										</tr>
									</table>
								</div>
							</div>
							
							<?php if (!empty($D_mess)) { ?>
								<div id="d_errors"><?php echo $D_mess; ?></div>
							<?php } ?>
							
							<div class="mb-5">
								<h4 class="mb-3 border-bottom border-danger">���x�������@</h4>

								<div id="payment_method">
								<?php echo $D_payment; ?>
								<br>
								<?php if($D_payment=='��s�U��'): ?>
									<span style="color:blue;">�U���萔���́A�����S���������܂��悤���肢���܂��B</span><br>
									<span style="color:blue;">���U���ݐ�́A�o�X�Ђ���̊m�F���[���ł��m�点���܂��B</span><br>
								<?php endif; ?>
							    </div>
							</div>

							<div class="mb-5">
								<div id="destination">
								<?php if ($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_forhome']>0) { ?>
									<h4 class="mb-3 border-bottom border-danger">���͂���</h4>
									<table id="home_tbl" class="table mb-5">
										<tr>
											<td><?php echo $buyer_name ?></td>
											<td style="width:280px;">
												���� <span class="quant"><?php echo $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity']; ?></span>
											</td>
										</tr>
										<tr>
											<td colspan="2"> ��<?php echo FM_shape_zip($buyer_zip) ?>�@<?php echo $buyer_add ?></td>
										</tr>
										<tr>
											<td colspan="2"> <?php echo substr($buyer_tel,0,-8).'-'.substr($buyer_tel,strlen(substr($buyer_tel,0,-8)),-4).'-'.substr($buyer_tel,-4); ?></td>
										</tr>
										<tr>
											<td colspan="2"> �����F�@<?php echo number_format(@$delivery_fee)?>�~</td>
										</tr>
										<tr>
											<td colspan="2"> �N�[���ցF�@<?php echo number_format(@$cool_bin_fee)?>�~</td>
										</tr>
										<tr>
											<td colspan="2">  ��]�������F<?php echo $p_kibou_date; ?>	���Ԏw��F<?php echo $p_kibou_time; ?>
											</td>
										</tr>
									</table>
								<?php } ?>
								
								<?php
								if (isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]) && is_array($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest])) {
								foreach ($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest] as $cnt => $dat) {
									
									if ($dat['edit'] == 'delete') continue;	// �폜�t���O�͕\�����Ȃ�
									if ($dat['need'] == 0) continue;		// ���ʂȂ��͕\�����Ȃ�
								?>
								<h4 class="mb-3 border-bottom border-danger">���͂���</h4>
								<table id="home_tbl" class="table mb-5">
									<tr>
										<td><?php echo $dat['dest_name'] ?></td>
										<td style="width:280px;">
											���� <span class="quant"><?php echo $dat['need']; ?></span>
										</td>
									</tr>
									<tr>
										<td colspan="2"> ��<?php echo FM_shape_zip($dat['dest_zip']) ?>�@<?php echo $dat['dest_address1'].$dat['dest_address2'] ?></td>
									</tr>
									<tr>
										<td colspan="2"> <?php echo substr($dat['dest_tel'],0,-8).'-'.substr($dat['dest_tel'],strlen(substr($dat['dest_tel'],0,-8)),-4).'-'.substr($dat['dest_tel'],-4); ?></td>
									</tr>
									<tr>
										<td colspan="2"> �����F�@<?php echo number_format($dat['gift_delivery_fee'])?>�~</td>
									</tr>
									<tr>
										<td colspan="2"> �N�[���ցF�@<?php echo number_format($dat['gift_cool_bin_fee'])?>�~</td>
									</tr>
									<tr>
										<td colspan="2"><?php echo $dat['wrapping_word'];?> �F�@ <span class="quant"><?php echo number_format($dat['wrapping_price']?$dat['wrapping_price']:$dat['noshi_price']); ?>�~</span>
										</td>
									</tr>
											
									<tr>
										<td colspan="2">  ��]�������F<?php echo $dat['kibou_datetime']['kibou_date']; ?>	���Ԏw��F<?php echo $dat['kibou_datetime']['kibou_time']; ?>
										</td>
									</tr>
								</table>
								
							<?php
							}
							}
							?>
							</div>
							</div>
							<!--
							<div class="mb-5">
								<h4 class="mb-3 border-bottom border-danger">�̔��X�ւ̗v�]�Ȃ�</h4>
								<div><?php echo @$p_order_comment; ?></div>
							</div>
							-->

								<!-- �K�����ǂ݂������� -->
								<h4 class="mb-3 border-bottom border-danger">�K�����ǂ݂�������</h4>
									<table width="100%">
										<tr>
											<td width="33%" valign="top">
												<div style="padding:10px 15px 10px 0;">
													<h5>�y�L�����Z���E�ԕi�E�����ɂ��āz</h5>
													���q�l�̂��s���ɂ��L�����Z���E�ԕi�E�����́A���󂯂��Ă���܂���B<br>
													��L�ȊO�A�����s�ǂȂǂ������A���i�̕ԕi�E�����͂��󂯂ł��܂���B<br>
													�̔��X�̉ߎ����������ꍇ�ł��A�J���E���g�p����Ă��܂����ꍇ�́A�ԕi�E����������Ȃ��ꍇ���������܂��B
												</div>
											</td>
											<td width="33%" valign="top">
												<div style="padding:10px 15px;">
													<h5>�y�����s�ǂɂ��āz</h5>
													�����s�ǂ̑Ή��́A���ڔ̔��X�ɂ��A�����������܂��B
													�͂������i�ɏ����s�ǂ��F�߂���ꍇ�́A�ȉ��̊������ɕs�ǂ�������ʐ^��̔��X�ɑ��t���Ă��m�点���������B
													�s�ǑΉ��͔̔��X�̔��f�ɂȂ�܂��B
													<div style="border:solid #777;border-width:1px 0 1px 0;">
													���N�H�i�F���i���肩��<span style="color:#c00;">24</span>���Ԉȓ�<br>
													��L�ȊO�F���i���肩��<span style="color:#c00;">48</span>���Ԉȓ�
													</div>
												</div>
												
											</td>
											<td width="33%" valign="top">
												<div style="padding:10px 0 10px 15px;">
													<h5>�y�N�[�����O�I�t���x�ɂ��āz</h5>
													�ʐM�̔��ɂ���čw�����ꂽ���i�́A���菤����@�ɂ���ċK�肳�ꂽ�N�[�����O�I�t�i���������j�̓K�p�ΏۊO�ƂȂ�܂��B
												</div>
											</td>
										</tr>
									</table>
								<br><br>
								<!-- �K�����ǂ݂������� -->

							<form id="f_confirm_frm" name="confirm_frm" method="post" action="./gift_purchase.php">

							<input type="hidden" name="p_kind" id="p_kind" value="<?php echo $p_kind; ?>">
							<input type="hidden" name="seller_id" id="seller_id" value="<?php echo $seller_id; ?>">
							<input type="hidden" name="p_payment" id="p_payment" value="<?php echo $p_payment; ?>">
							<input type="hidden" name="p_select_point" id="p_select_point" value="<?php echo $p_select_point; ?>">
							<input type="hidden" name="p_use_point" id="p_use_point" value="<?php echo $p_use_point; ?>">
							<input type="hidden" name="p_forhome" id="p_forhome" value="<?php echo $p_forhome; ?>">
							<input type="hidden" name="p_home_quantity" id="p_home_quantity" value="<?php echo $p_home_quantity; ?>">
							<input type="hidden" name="p_kibou_date" id="p_kibou_date" value="<?php echo $p_kibou_date; ?>">
							<input type="hidden" name="p_kibou_time" id="p_kibou_time" value="<?php echo $p_kibou_time; ?>">
							<input type="hidden" name="p_order_comment" id="p_order_comment" value="<?php echo @$p_order_comment; ?>">
							<div class="text-center">
								<input class="btn btn-secondary btn-lg mr-5" type="button" id="btn_back" value="�߂�">
								<?php if (empty($D_mess)) { ?>
									<input class="btn btn-danger btn-lg" type="button" id="btn_next" value="�w������">
								<?php }else{ ?>
									<input class="btn btn-danger btn-lg" type="button" id="btn_next" value="�w������" disabled>
								<?php } ?>
							</div>
							
							</form>
							</div>
						</div>
					</div><!-- /row -->
				</div>
			</div>
		</div><!-- /p-5 -->

	</div><!-- /col-md-10 -->
</div>
</div>
<!-- Footer -->
<?php include_once('./footer.php');?>
<!-- / Footer -->
</div>
</body>


</html>
