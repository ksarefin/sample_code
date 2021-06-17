<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis" />
<?php echo DF_meta_tag_for_viewport; ?>
<title>お届け先ページ｜商品購入</title>
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
			   alert('注文数は0不可です。');
			}else if(isNaN(Number(quantity))  || quantity != Math.floor(quantity) ) {
			   alert('注文数は半角数字だけ入力してください。');
			}else{
				$('#amount_'+item_serial).html('￥'+separate(amount));
				$.ajax({
					method: "POST",
					url: "gift_order.php",
					data: { p_kind: "update_item_quantity", seller_id: seller_id, item_serial:item_serial, quantity:quantity },
					success: function(data, dataType){
						if(data==null){
							alert('注文数更新エラー');
						}else{
							data = $.parseJSON(data);
							$('#sub_total_dis_'+seller_id).html('￥'+Number(data[0]).toLocaleString());
							$('#total_dis_'+seller_id).html('<h2>￥'+Number(data[2]).toLocaleString()+'</h2>');
							$('#cool_bin_dis_'+seller_id).html('￥'+Number(data[3]).toLocaleString());
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
								$('#delivery_fee_dis_'+seller_id).html('出店社からお知らせ');
							}else{
								$('#delivery_fee_dis_'+seller_id).html('￥'+Number(data[1]).toLocaleString());
							}
							if(data[6]){
								location.href='/gift_order.php?p_kind=login_check';
							}
						}
						$('#pay_form_'+seller_id).prop('disabled', false);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown){
						alert('エラーが発生しました #1 : ' + errorThrown);
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
			   alert('注文数は0不可です。');
			}else if(isNaN(Number(quantity)) || quantity != Math.floor(quantity) ) {
			   alert('注文数は半角数字だけ入力してください。');
			}else{
				if(parseInt(quantity)>parseInt(max)){
					alert(quantity+'数量は'+max+'までです');
					quantity=max;
					$(this).spinner("stepUp", max);
				}
				
				amount=price*quantity;
				
				$('#amount_'+item_serial).html('￥'+separate(amount));
				$.ajax({
					method: "POST",
					url: "gift_order.php",
					data: { p_kind: "update_item_quantity", seller_id: seller_id, item_serial:item_serial, quantity:quantity },
					success: function(data, dataType){
						if(data==null){
							alert('注文数更新エラー');
						}else{
							data = $.parseJSON(data);
							$('#sub_total_dis_'+seller_id).html('￥'+Number(data[0]).toLocaleString());
							$('#total_dis_'+seller_id).html('<h2>￥'+Number(data[2]).toLocaleString()+'</h2>');
							$('#cool_bin_dis_'+seller_id).html('￥'+Number(data[3]).toLocaleString());
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
								$('#delivery_fee_dis_'+seller_id).html('出店社からお知らせ');
							}else{
								$('#delivery_fee_dis_'+seller_id).html('￥'+Number(data[1]).toLocaleString());
							}
							if(data[6]){
								location.href='/gift_order.php?p_kind=login_check';
							}
						}
						$('#pay_form_'+seller_id).prop('disabled', false);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown){
						alert('エラーが発生しました #2 : ' + errorThrown);
					}
				})
			}
			
			
		}
	});
	
		
	// 贈答用に使う
	$('#btn_gift').click(function(){
		$('#p_kind').val('shipping');
		$('#gift_flg').val('1');
		$(this.form).submit();
	});

	// お支払いへ
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
	
	// 3桁ずつカンマ区切りにする
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
					<li class="breadcrumb-item"><a href="/">トップ</a></li>
					<li class="breadcrumb-item">カート</li>
				</ol>
			</nav>
		
			<div class="p-3">

			<h4 class="border-bottom border-danger mb-3">カート</h4>
				<div class="container mb-5">

					<div class="row">
					<?php if (empty($member_regist_arr)) { ?>
						<dl class="trans">
							<dt>ログイン</dt>
							<dd>&#9654;</dd>
							<dt class="here_now">カート</dt>
							<dd>&#9654;</dd>
							<dt>お届け先<br>お支払い</dt>
							<dd>&#9654;</dd>
							<dt>確認</dt>
							<dd>&#9654;</dd>
							<dt>完了</dt>
						</dl>
					<?php } else { ?>
						<dl class="trans">
							<dt>会員登録</dt>
							<dd>&#9654;</dd>
							<dt>会員登録<br>確認</dt>
							<dd>&#9654;</dd>
							<dt class="here_now">お届け先<br>お支払い</dt>
							<dd>&#9654;</dd>
							<dt>確認</dt>
							<dd>&#9654;</dd>
							<dt>完了</dt>
						</dl>
					<?php }  ?>
					<!--<div class="col shop_item_list mb-5">-->
						
					<div class="alert alert-danger w-100 red bg-white h4 py-4 mt-4 mb-5 text-center">このサイトは卸売業者の販売する個人向けサイトですので、<br>高品質の商品を安く販売するため梱包や包装は簡易的となることをご了承ください。</div>

						<div class="alert alert-danger w-100">
							<div style="float:left;width:2em;background:#c00;color:#fff;text-align:center;margin-right:1em;">
								ご<br>注<br>意
							</div>
							お支払いは、代金引換（チルド除く）・銀行振込（振込手数料はお客様のご負担になります）・カード決済<img src="https://www.c-joy.jp/view/img/card_mini_vmj.png" alt="カード決済可">になります。<br>
							ご注文は、お店毎にお願い致します。（配送料金は、各出店社毎に掛かります。）<br>
							ご注文などの<strong>【すべてメール】</strong>は、「マイページ」内の<strong>「メールボックス」</strong>に入ります。
						</div>

						<?php 
							foreach($_SESSION[DF_order_gift_cart] as $seller_id=>$seller_arr){
								if(empty($seller_id)) continue;
						?>
							<div class="alert alert-light border border-secondary w-100">
							
							<form id="f_shipping_frm_<?php echo $seller_id?>" name="shipping_frm" method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
							
							<h4>
								<span>お店は、<?php echo $_SESSION[DF_seller_info][$seller_id]['seller_yagou']?></span>
								<span class="until_free_shipping_nakayamaya0021 float-right">
									<span class='until_free_shipping_<?php echo $seller_id?>' style="display:none;">【送料無料まであと<span style="color:blue" class="until_amount_<?php echo $seller_id?>"><?php echo number_format($_SESSION[DF_seller_info][$seller_id]['until_free_shipping'])?></span>円】</span>
								</span>
							</h4>
							<h6 class="text-right"><a href="/seller_item_list.php?seller_id=<?php echo$seller_id;?>" target="_blank">このお店の商品をもっと見る</a></h6>

								<div class="table-responsive shop_item_list">
									<table class="table table-condensed">
									<thead>
									<tr>
										<th></th>
										<th style="width:300px;">商品</th>
										<th class="text-center">単価</th>
										<th class="text-center">数量</th>
										<th class="text-center">価格</th>
										<th class="text-center">取り消し</th>
									</tr>
										<?php foreach($seller_arr as $item_serial=>$item_arr){ ?>

											<tr>
												<td style="width:150px;max-height:150px;">
													<a href="/gift_item.php?moto_flg=<?php echo $item_arr['moto_flg']?>&type=<?php echo $item_arr['p_type']?>&p_serial=<?php echo $item_serial?>"><img src="/sel/upload_img/<?php echo $seller_id?>/<?php echo $item_arr['pic1']?>" alt="<?php echo $item_arr['product_name']; ?>" class="img-fluid"></a>
												</td>
												<td class="text-left"><a href="/gift_item.php?moto_flg=<?php echo $item_arr['moto_flg']?>&type=<?php echo $item_arr['p_type']?>&p_serial=<?php echo $item_serial?>"><?php echo $item_arr['product_name']; ?></a></td>
												<td class="text-right">￥<?php echo number_format($item_arr['price'])?></td>
												<td class="text-center"><input type="text" name="quantity_<?php echo $item_serial?>" class="home_quantity" seller_id="<?php echo $seller_id?>" item_serial="<?php echo $item_arr['product_serial']?>" max="<?php echo ($item_arr['zaiko_flg']==1?$item_arr['zaiko']:'')?>" value="<?php echo $item_arr['quantity']; ?>" price="<?php echo $item_arr['price']?>" size="2"></td>
												<td class="text-right" id="amount_<?php echo $item_arr['product_serial']?>">￥<?php echo number_format($item_arr['price']*$item_arr['quantity']); ?></td>
												<td class="text-center"><input type="button" class="item_delete btn btn-outline-dark" seller_id="<?php echo $seller_id?>" item_serial="<?php echo $item_arr['product_serial']?>" value="取り消し"></td>
											</tr>										
														
										<?php } ?>
										
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">小計</td>
											<td class="text-right" id="sub_total_dis_<?php echo $seller_id?>">￥<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['sub_total']);?></td>
											<td class="text-center"></td>
										</tr>
										
										<tr>
											<td rowspan="2"></td>
											<td rowspan="2" class="text-left"></td>
											<td class="text-left" nowrap>
												<span class='until_free_shipping_<?php echo $seller_id?>' style="display:none;">【送料無料まであと<span style="color:blue" class="until_amount_<?php echo $seller_id?>"><?php echo number_format($_SESSION[DF_seller_info][$seller_id]['until_free_shipping'])?></span>円】</span>
											</td>
											<td class="text-right" nowrap>
												<span class="seller_item_add_<?php echo $seller_id?>" style="display:none;"><a href="/seller_item_list.php?seller_id=<?php echo$seller_id;?>" target="_blank">商品を追加する</a></span>
												<span style="color:blue; display:none;" class='free_shipping_<?php echo $seller_id?>'>【送料無料】</span>
											</td>
											<td class="text-right">送料</td>
											<td class="text-right" id="delivery_fee_dis_<?php echo $seller_id?>" nowrap>
												<?php 
												if(isset($_SESSION[DF_seller_info][$seller_id]['delivery_flg']) && $_SESSION[DF_seller_info][$seller_id]['delivery_flg']=='not_match'){ 
													echo '出店社からお知らせ';
												}else{
													echo '￥'.number_format($_SESSION[DF_seller_info][$seller_id]['delivery_fee']);
												}
												?>
											</td>
											<td rowspan="2" class="text-center"></td>
										</tr>
										<tr>
											<td class="text-right" colspan="4" style="border-top:none;font-size:12px;">
												沖縄・離島及びクール便は別途料金が必要です。<br>
												送料は、ご注文後の確認メールにて正しい送料をお知らせいたします。<br>
												配送方法（通常便とクール便が分かれるなど）で送料が変更される場合があります。
											</td>
										</tr>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">クール便</td>
											<!--<td class="text-right" id="cool_bin_dis_<?php echo $seller_id?>">￥<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']);?></td>-->
											<td class="text-right">お届け先指定後に計算されます</td>
											<td class="text-center"></td>
										</tr>
										<?php if(@$_SESSION[DF_seller_info][$seller_id]['discount_amount']>0){?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-left"></td>
											<td class="text-right" colspan="2">オープンキャンペーン値引き（初回限定）</td>
											<td class="text-right" id="discount_dis_<?php echo $seller_id?>">-￥<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['discount_amount']);?></td>
											<td class="text-center"></td>
										</tr>
										<?php } ?>
										<tr>
											<td></td>
											<td class="text-left"></td>
											<td class="text-left"></td>
											<td class="text-right"></td>
											<td class="text-right">合計</td>
											<td class="text-right" id="total_dis_<?php echo $seller_id?>"><h2>￥<?php echo number_format($_SESSION[DF_seller_info][$seller_id]['total']-$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']);?></h2></td>
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
								<div class="text-right mt-2">上記商品は、<?php echo $_SESSION[DF_seller_info][$seller_id]['seller_yagou']?>より配送致します。
									<input type="button" id="pay_form_<?php echo $seller_id?>" class="btn_next btn btn-danger btn-lg" frm_id="f_shipping_frm_<?php echo $seller_id?>" value="お届け先・お支払い方法へ">
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
