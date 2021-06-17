<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis" />
<?php echo DF_meta_tag_for_viewport; ?>
<title>確認ページ｜商品購入</title>
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
	
	// 戻るへ
	$('#btn_back').click(function(){
		$('#p_kind').val('payment');
		$(this.form).attr('action', './gift_order.php');
		$(this.form).submit();
	});

	$('#btn_next').click(function(){
		if (confirm('購入を確定します。よろしいですか？')) {
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
					<li class="breadcrumb-item"><a href="/">トップ</a></li>
					<li class="breadcrumb-item">カート</li>
				</ol>
			</nav>
		
			<div class="p-3">

			<h4 class="border-bottom border-danger mb-3">ご注文確認</h4>
				<div class="container mb-5">

					<div class="row">
					
						<dl class="trans">
							<dt>ログイン</dt>
							<dd>&#9654;</dd>
							<dt>カート</dt>
							<dd>&#9654;</dd>
							<dt>お届け先<br>お支払い</dt>
							<dd>&#9654;</dd>
							<dt class="here_now">確認</dt>
							<dd>&#9654;</dd>
							<dt>完了</dt>
						</dl>

						<div class="col shop_item_list mb-5 px-0">
						
						<div class="alert alert-danger w-100 red bg-white h4 py-4 mt-4 mb-5 text-center">このサイトは卸売業者の販売する個人向けサイトですので、<br>高品質の商品を安く販売するため梱包や包装は簡易的となることをご了承ください。</div>
						
						<div class="alert alert-danger w-100">
							<div style="float:left;width:2em;background:#c00;color:#fff;text-align:center;margin-right:1em;">
								ご<br>注<br>意
							</div>
							お支払いは、代金引換（チルド除く）・銀行振込（振込手数料はお客様のご負担になります）・カード決済<img src="https://www.c-joy.jp/view/img/card_mini_vmj.png" alt="カード決済可">になります。<br>
							ご注文は、お店毎にお願い致します。（配送料金は、各出店社毎に掛かります。）<br>
							ご注文などの<strong>【すべてメール】</strong>は、「マイページ」内の<strong>「メールボックス」</strong>に入ります。
						</div>
							<div class="alert alert-light w-100 px-0">
								<h4>お店は、<?php echo $_SESSION[DF_seller_info][$seller_id]['seller_shamei']?></h3>
								<!--<h6 class="text-right"><a href="#" target="_blank">このお店の商品をもっと見る</a></h6>-->

								<div class="table-responsive shop_item_list">
									<table class="table table-condensed">
									<thead>
									<tr>
										<th></th>
										<th style="width:300px;">商品</th>
										<th class="text-center">単価</th>
										<th class="text-center">注文数</th>
										<th class="text-center">価格</th>
									</tr>
									<?php foreach($_SESSION[DF_order_gift_cart][$seller_id] as $item_serial=>$item_arr){ ?>
										<tr>
											<td style="width:150px;max-height:150px;">
												<a href="/gift_item.php?moto_flg=<?php echo $item_arr['moto_flg']?>&type=<?php echo $item_arr['p_type']?>&p_serial=<?php echo $item_serial?>"><img src="/sel/upload_img/<?php echo $seller_id?>/<?php echo $item_arr['pic1']?>" alt="<?php echo $item_arr['product_name']; ?>" class="img-fluid"></a>
											</td>
											<td class="text-left"><a href="/gift_item.php?moto_flg=<?php echo $item_arr['moto_flg']?>&type=<?php echo $item_arr['p_type']?>&p_serial=<?php echo $item_serial?>"><?php echo $item_arr['product_name']; ?></a></td>
											<td class="text-right">￥<?php echo number_format($item_arr['price'])?></td>
											<td class="text-center"><?php echo $item_arr['quantity']; ?></td>
											<td class="text-right" id="amount_<?php echo $item_arr['product_serial']?>">￥<?php echo number_format($item_arr['price']*$item_arr['quantity']); ?></td>
										</tr>
									<?php } ?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">小計</td>
											<td class="text-right" id="sub_total_dis_<?php echo $seller_id?>">￥<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['sub_total']);?></td>
											<td class="text-center"></td>
										</tr>
										
										<tr>
											<td rowspan="2"></td>
											<td rowspan="2" class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">送料</td>
											<td class="text-right" id="delivery_fee_dis_<?php echo $seller_id?>" nowrap>
												<?php 
												if($_SESSION[DF_seller_info][$seller_id]['delivery_flg']=='not_match'){ 
													echo '出店社からお知らせ';
												}else{
													echo '￥'.number_format($_SESSION[DF_seller_info][$seller_id]['delivery_fee']+$_SESSION[DF_seller_info][$seller_id]['gift_delivery_fee_total']);
												}
												?>
											</td>
											<td rowspan="2" class="text-center"></td>
										</tr>
										<tr>
											<td class="text-right" colspan="3" style="border-top:none;font-size:12px;">
												沖縄・離島及びクール便は別途料金が必要です。<br>
												送料は、ご注文後の確認メールにて正しい送料をお知らせいたします。<br>
												配送方法（通常便とクール便が分かれるなど）で送料が変更される場合があります。
											</td>
										</tr>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">クール便</td>
											<td class="text-right" id="cool_bin_dis_<?php echo $seller_id?>">￥<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']+$_SESSION[DF_seller_info][$seller_id]['gift_cool_bin_fee_total']);?></td>
											<td class="text-center"></td>
										</tr>
										<?php if(@$_SESSION[DF_seller_info][$seller_id]['discount_amount']>0){?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right" colspan="2">オープンキャンペーン値引き（初回限定）</td>
											<td class="text-right" id="discount_dis_<?php echo $seller_id?>">-￥<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['discount_amount']);?></td>
											<td class="text-center"></td>
										</tr>
										<?php } ?>
										
										<?php if(@$_SESSION[DF_seller_info][$seller_id]['noshi_price']>0 || @$_SESSION[DF_seller_info][$seller_id]['wrapping_price']>0){?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right" colspan="2">ラッピング・のし</td>
											<td class="text-right" id="wrapping_dis_<?php echo $seller_id?>">￥<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['wrapping_price']+$_SESSION[DF_seller_info][$seller_id]['noshi_price']);?></td>
											<td class="text-center"></td>
										</tr>
										<?php } ?>
										
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">お支払合計金額</td>
											<td class="text-right" id="total_dis_<?php echo $seller_id?>"><h2>￥<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['gift_cool_bin_fee_total']+$_SESSION[DF_seller_info][$seller_id]['gift_delivery_fee_total']+$_SESSION[DF_seller_info][$seller_id]['noshi_price']+$_SESSION[DF_seller_info][$seller_id]['wrapping_price']+$_SESSION[DF_seller_info][$seller_id]['total']);?></h2></td>
											<td class="text-center"></td>
										</tr>
									</table>
								</div>
							</div>
							
							<?php if (!empty($D_mess)) { ?>
								<div id="d_errors"><?php echo $D_mess; ?></div>
							<?php } ?>
							
							<div class="mb-5">
								<h4 class="mb-3 border-bottom border-danger">お支払い方法</h4>

								<div id="payment_method">
								<?php echo $D_payment; ?>
								<br>
								<?php if($D_payment=='銀行振込'): ?>
									<span style="color:blue;">振込手数料は、ご負担いただきますようお願いします。</span><br>
									<span style="color:blue;">お振込み先は、出店社からの確認メールでお知らせします。</span><br>
								<?php endif; ?>
							    </div>
							</div>

							<div class="mb-5">
								<div id="destination">
								<?php if ($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_forhome']>0) { ?>
									<h4 class="mb-3 border-bottom border-danger">お届け先</h4>
									<table id="home_tbl" class="table mb-5">
										<tr>
											<td><?php echo $buyer_name ?></td>
											<td style="width:280px;">
												数量 <span class="quant"><?php echo $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity']; ?></span>
											</td>
										</tr>
										<tr>
											<td colspan="2"> 〒<?php echo FM_shape_zip($buyer_zip) ?>　<?php echo $buyer_add ?></td>
										</tr>
										<tr>
											<td colspan="2"> <?php echo substr($buyer_tel,0,-8).'-'.substr($buyer_tel,strlen(substr($buyer_tel,0,-8)),-4).'-'.substr($buyer_tel,-4); ?></td>
										</tr>
										<tr>
											<td colspan="2"> 送料：　<?php echo number_format(@$delivery_fee)?>円</td>
										</tr>
										<tr>
											<td colspan="2"> クール便：　<?php echo number_format(@$cool_bin_fee)?>円</td>
										</tr>
										<tr>
											<td colspan="2">  希望到着日：<?php echo $p_kibou_date; ?>	時間指定：<?php echo $p_kibou_time; ?>
											</td>
										</tr>
									</table>
								<?php } ?>
								
								<?php
								if (isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]) && is_array($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest])) {
								foreach ($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest] as $cnt => $dat) {
									
									if ($dat['edit'] == 'delete') continue;	// 削除フラグは表示しない
									if ($dat['need'] == 0) continue;		// 数量なしは表示しない
								?>
								<h4 class="mb-3 border-bottom border-danger">お届け先</h4>
								<table id="home_tbl" class="table mb-5">
									<tr>
										<td><?php echo $dat['dest_name'] ?></td>
										<td style="width:280px;">
											数量 <span class="quant"><?php echo $dat['need']; ?></span>
										</td>
									</tr>
									<tr>
										<td colspan="2"> 〒<?php echo FM_shape_zip($dat['dest_zip']) ?>　<?php echo $dat['dest_address1'].$dat['dest_address2'] ?></td>
									</tr>
									<tr>
										<td colspan="2"> <?php echo substr($dat['dest_tel'],0,-8).'-'.substr($dat['dest_tel'],strlen(substr($dat['dest_tel'],0,-8)),-4).'-'.substr($dat['dest_tel'],-4); ?></td>
									</tr>
									<tr>
										<td colspan="2"> 送料：　<?php echo number_format($dat['gift_delivery_fee'])?>円</td>
									</tr>
									<tr>
										<td colspan="2"> クール便：　<?php echo number_format($dat['gift_cool_bin_fee'])?>円</td>
									</tr>
									<tr>
										<td colspan="2"><?php echo $dat['wrapping_word'];?> ：　 <span class="quant"><?php echo number_format($dat['wrapping_price']?$dat['wrapping_price']:$dat['noshi_price']); ?>円</span>
										</td>
									</tr>
											
									<tr>
										<td colspan="2">  希望到着日：<?php echo $dat['kibou_datetime']['kibou_date']; ?>	時間指定：<?php echo $dat['kibou_datetime']['kibou_time']; ?>
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
								<h4 class="mb-3 border-bottom border-danger">販売店への要望など</h4>
								<div><?php echo @$p_order_comment; ?></div>
							</div>
							-->

								<!-- 必ずお読みください -->
								<h4 class="mb-3 border-bottom border-danger">必ずお読みください</h4>
									<table width="100%">
										<tr>
											<td width="33%" valign="top">
												<div style="padding:10px 15px 10px 0;">
													<h5>【キャンセル・返品・交換について】</h5>
													お客様のご都合によるキャンセル・返品・交換は、お受けしておりません。<br>
													上記以外、初期不良などを除き、商品の返品・交換はお受けできません。<br>
													販売店の過失があった場合でも、開封・ご使用されてしまった場合は、返品・交換を承れない場合もございます。
												</div>
											</td>
											<td width="33%" valign="top">
												<div style="padding:10px 15px;">
													<h5>【初期不良について】</h5>
													初期不良の対応は、直接販売店にご連絡いただきます。
													届いた商品に初期不良が認められる場合は、以下の期限内に不良が分かる写真を販売店に送付してお知らせください。
													不良対応は販売店の判断になります。
													<div style="border:solid #777;border-width:1px 0 1px 0;">
													生鮮食品：商品受取りから<span style="color:#c00;">24</span>時間以内<br>
													上記以外：商品受取りから<span style="color:#c00;">48</span>時間以内
													</div>
												</div>
												
											</td>
											<td width="33%" valign="top">
												<div style="padding:10px 0 10px 15px;">
													<h5>【クーリングオフ制度について】</h5>
													通信販売によって購入された商品は、特定商取引法によって規定されたクーリングオフ（無条件解約）の適用対象外となります。
												</div>
											</td>
										</tr>
									</table>
								<br><br>
								<!-- 必ずお読みください -->

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
								<input class="btn btn-secondary btn-lg mr-5" type="button" id="btn_back" value="戻る">
								<?php if (empty($D_mess)) { ?>
									<input class="btn btn-danger btn-lg" type="button" id="btn_next" value="購入完了">
								<?php }else{ ?>
									<input class="btn btn-danger btn-lg" type="button" id="btn_next" value="購入完了" disabled>
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
