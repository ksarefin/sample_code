<?php
/*******************************
商品注文処理

2015/08/05	新規作成	中家	
2015/09/07	機能修正	中家	キャンペーン内容変更対応
2015/09/14	機能追加	中家	希望到着日時対応
2015/09/17	機能修正	中家	ポイントでエラーの際の支払い方法不具合修正
2015/10/01	機能追加	中家	代引の使えない会社対応
2015/10/19	機能追加	中家	贈答先にも希望到着日時対応
2015/10/28	変更		中家	代引が使えない企業からリブブリッジとオフィスアンを削除
2015/12/02	機能追加	中家	出店社ごとに到着日指定可能営業日を設定する
2015/12/04	機能追加	中家	ポイント5倍対応
2020/08/24	機能追加	kha	jcbカード決済追加
2020/11/02	機能修正	kha	jcbカード決済時の商品名200文字まで
2020/12/09  機能追加	小田    【販本20-2675】ブラックリスト対策（暫定）
2020/12/10	機能追加	奥山	希望到着時間選択肢を出店社が設定した時間で選択できるよう対応

********************************/
session_start();

// 共通関数読み込み
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_general.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_validate.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_login.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_order.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_buyer.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_seller.php");
// 共通定義読み込み
require_once("/var/www/vhosts/c-joy.co.jp/common/define/d_general.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/define/d_buyer.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/define/d_session_key.php");
// クラス読み込み
require_once('/var/www/vhosts/c-joy.co.jp/common/class/class_chakubi.php');

//カード決済：設定ファイル読み込み
require_once('/var/www/vhosts/c-joy.co.jp/common/class/class_credit_card.php');
require_once('/var/www/vhosts/c-joy.co.jp/common/define/d_creditcard.php');				//カード決済対応共通defin
require_once('/var/www/vhosts/c-joy.co.jp/common/function/f_creditcard.php');				//カード決済対応共通関数

F_check_access();

//-----------------------------------------------
// 初期処理
//-----------------------------------------------

// ■DBクラス
include_once("/var/www/vhosts/c-joy.co.jp/common/class/class_db.php");
$c_db=new G_class_db;	//DBクラス実体化
$db_name='cjoy';		//DB名
$table_arr = array();
$table_arr['product_gift']	 		= 'cjoy.product_gift';				// 商品詳細
$table_arr['product_b_gift']	 	= 'cjoy.product_b_gift';
$table_arr['buyer_member'] 			= 'cjoy.buyer_member';			// 買い手会員登録情報
$table_arr['buyer_payment'] 		= 'cjoy.buyer_payment';			// 買い手会員支払情報
$table_arr['buyer_dest'] 			= 'cjoy.buyer_destination';		// 買い手会員送付先情報
$table_arr['buyer_point'] 			= 'cjoy.buyer_point';			// 買い手保有ポイント情報
$table_arr['common_campaign']		= 'cjoy.common_campaign';		// キャンペーン情報
$table_arr['order_campain']			= 'cjoy.order_campain';
$table_arr['delivery_shitei']		= 'cjoy.delivery_shitei';
//-----------------------------------------------
// 変数・定数
//-----------------------------------------------

//送料無料になる条件
define('DF_delivery_order_limit', 3000);
define('DF_discount_amout', 500);
define('DF_cool_bin', 500);

// 変数
$D_main = '';			// メイン表示
$D_mess = '';			// 作業メッセージ

// 会員登録画面用
$D_option = '';
$D_button = '';

// カード用
$D_card_table = '';

$no_seller_flg = 1;
// バリデーション用定義
$A_validate = array(
	'p_dest_name'		=> array('not_empty', 'except_special_symbol'),
	'p_dest_zip'		=> array('not_empty', 'only_num','length'=>array('specified'=>7),),
	'p_dest_add2'		=> array('not_empty', 'except_special_symbol'),
	'p_dest_add3'		=> array('not_empty', 'except_special_symbol'),
	'p_dest_tel'		=> array('not_empty', 'only_num','length'=>array('max'=>12),)
);

$A_validate_wrap = array(
	'p_noshi_up'		=> array('except_special_symbol','length'=>array('max'=>20),),
	'p_noshi_btm'		=> array('except_special_symbol','length'=>array('max'=>20),),
);

// post値の日本語名（バリデーション用）
$A_post_name = array(
	'p_dest_name'		=> '名前',
	'p_dest_zip'		=> '郵便番号',
	'p_dest_add2'		=> '市区町村名',
	'p_dest_add3'		=> '番地、建物名',
	'p_dest_tel'		=> '電話番号',
);
$A_post_name_wrap = array(
	'p_noshi_up'		=> 'のし上段(表書き)',
	'p_noshi_btm'		=> 'のし下段',
);

// 代引の使えない売り手ID
$A_unavailable_yamato = array(
);

$D_order_notice = '
	<div style="color:#C00; margin-top:15px; font-size:13px;">
		ブラウザの [戻る] や [更新] ボタンは使用しないでください。<br />
		予期しないエラーの原因になります。
	</div>
';

//XSS対策 $_REQUEST $_GET $_POSTの値をエスケープ処理
isset($_REQUEST) && $_REQUEST = F_change_safe_array($_REQUEST);
isset($_GET) && $_GET = F_change_safe_array($_GET);
isset($_POST) && $_POST = F_change_safe_array($_POST);
//-----------------------------------------------
// リクエスト値受け渡し
//-----------------------------------------------
FM_array_trim($_POST);

//作業種 
$p_kind = 'top';		
isset($_REQUEST['p_kind']) && $p_kind = $_REQUEST['p_kind'];
// 商品シリアル
$p_serial = 0;
isset($_REQUEST['p_serial']) && $p_serial = $_REQUEST['p_serial'];
// ログインID
$p_buyer_id = '';
isset($_POST['p_buyer_id']) && $p_buyer_id = $_POST['p_buyer_id'];
// ログインパスワード
$p_buyer_password = '';
isset($_POST['p_buyer_password']) && $p_buyer_password = $_POST['p_buyer_password'];
// 贈答品設定
$p_gift_flg = 0;
isset($_POST['p_gift_flg']) && $p_gift_flg = (int)$_POST['p_gift_flg'];

// 会員登録値
$p_buyer_name1 = '';
isset($_POST['p_buyer_name1']) && $p_buyer_name1 = $_POST['p_buyer_name1'];
$p_buyer_name2 = '';
isset($_POST['p_buyer_name2']) && $p_buyer_name2 = $_POST['p_buyer_name2'];
$p_buyer_name_kana1 = '';
isset($_POST['p_buyer_name_kana1']) && $p_buyer_name_kana1 = $_POST['p_buyer_name_kana1'];
$p_buyer_name_kana2 = '';
isset($_POST['p_buyer_name_kana2']) && $p_buyer_name_kana2 = $_POST['p_buyer_name_kana2'];
$p_buyer_zip = '';
isset($_POST['p_buyer_zip']) && $p_buyer_zip = $_POST['p_buyer_zip'];
$p_buyer_address1 = '';
isset($_POST['p_buyer_address1']) && $p_buyer_address1 = $_POST['p_buyer_address1'];
$p_buyer_address2 = '';
isset($_POST['p_buyer_address2']) && $p_buyer_address2 = $_POST['p_buyer_address2'];
$p_buyer_address3 = '';
isset($_POST['p_buyer_address3']) && $p_buyer_address3 = $_POST['p_buyer_address3'];
$p_buyer_tel = '';
isset($_POST['p_buyer_tel']) && $p_buyer_tel = $_POST['p_buyer_tel'];
$p_buyer_sex = 0;
isset($_POST['p_buyer_sex']) && $p_buyer_sex = $_POST['p_buyer_sex'];
$p_buyer_age = '選択してください';
isset($_POST['p_buyer_age']) && $p_buyer_age = $_POST['p_buyer_age'];
$p_buyer_from = '';
isset($_POST['p_buyer_from']) && $p_buyer_from = $_POST['p_buyer_from'];
$p_buyer_from_txt = '';
isset($_POST['p_buyer_from_txt']) && $p_buyer_from_txt = $_POST['p_buyer_from_txt'];
$p_kessai = 0;
isset($_POST['p_kessai']) && $p_kessai = $_POST['p_kessai'];
$p_invited = '';
isset($_POST['p_invited']) && $p_invited = $_POST['p_invited'];
$p_errmess = '';
isset($_POST['p_errmess']) && $p_errmess = $_POST['p_errmess'];

// 数量関連
$p_forhome = 0;
isset($_POST['p_forhome']) && $p_forhome = (int)$_POST['p_forhome'];
$p_home_quantity = 1;
isset($_POST['p_home_quantity']) && $p_home_quantity = (int)$_POST['p_home_quantity'];
$p_kibou_date = '';
isset($_POST['p_kibou_date']) && $p_kibou_date = $_POST['p_kibou_date'];
$p_kibou_time = '';
isset($_POST['p_kibou_time']) && $p_kibou_time = $_POST['p_kibou_time'];
$p_type = '';
isset($_POST['p_type']) && $p_type = $_POST['p_type'];

// 支払
$p_select_point = 2;
isset($_POST['p_select_point']) && $p_select_point = (int)$_POST['p_select_point'];
$p_use_point = 0;
isset($_POST['p_use_point']) && $p_use_point = (int)$_POST['p_use_point'];
$p_payment = 0;
isset($_POST['p_payment']) && $p_payment = (int)$_POST['p_payment'];

$p_scroll = 0;
isset($_REQUEST['p_scroll']) && $p_scroll = $_REQUEST['p_scroll'];

//
$moto_flg = 'normal';
isset($_POST['moto_flg']) && $moto_flg = $_POST['moto_flg'];

// 販売店への要望など
$p_order_comment = '';
isset($_POST['p_order_comment']) && $p_order_comment = $_POST['p_order_comment'];


//-----------------------------------------------
// BLチェック
//-----------------------------------------------
$err_message = '';
if ($p_kind == 'order_confirm') {
	$bl_array = [
		['電話番号','住所'],
		['07040816098','賀茂郡東伊豆町白田451-1'],
		['xxxx','稲城市百村1461-1オーベル稲城南山406'],
		['xxxx','松山市春美町4-21'],
		['xxxx','横浜市西区戸部町2-18-5メイプルヒルズ21-302'],
		['08075465244','白老郡白老町萩野342-197'],
		['xxxx','稲城市大丸831セザール多摩ガーデン505'],
		['xxxx','墨田区東向島5-41-18ライオンズガーデン東向島907'],
		['xxxx','碧南市栄町2-38セントラルハイツ302'],
		['xxxx','練馬区光が丘3-7-1-1206'],
		['07042951401','茅ヶ崎市浜見平15-34-506'],
		['xxxx','中津川市中川町3-61']
	];

	foreach($bl_array as $koko){
		if(($koko[0] == $_SESSION[DF_sessionkey_member_info]['buyer_tel'])||
		   (false !== strpos($_SESSION[DF_sessionkey_member_info]['buyer_address2'], $koko[1]))){
			$err_message = '現在、商品をご購入することができません。詳細についてはお問い合わせページよりお問い合わせ願います。';
			$p_kind = 'payment';
			break;
		}
	}
}

//-----------------------------------------------
// 作業種別処理部
//-----------------------------------------------
/*********** サプクエリ ***********/
if ($p_kind == 'subquery') {
	header("Content-type: text/plain; charset=shift_jis");
	// UTF8でPOSTされるのでshift_jisに戻す
	foreach ($_POST as $key => $val) {
		$_POST[$key] = mb_convert_encoding($val, 'SJIS', 'UTF-8');
	}
	
	$buyer_serial=$_SESSION[DF_sessionkey_member_info]['buyer_serial'];
	
	if ($_POST['p_dest_kind'] == 'new') {
		
		$count = count(@$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
		// 上限チェック
		if ($count >= DF_member_dest_count) {
			$D_mess = '・登録可能なお届け先は、'.DF_member_dest_count.'までとなります。';
			echo $D_mess;
			exit;
		}
		
		$error_arr = FM_validate($_POST, $A_validate, $A_post_name);
		// エラー
		if (! empty($error_arr)) {
			$D_mess = implode("<br>", $error_arr);
			echo $D_mess;
			exit;
		}
		
		$serial=FM_ins_buyer_destination($buyer_serial, $_POST);
		if($serial===false){
			echo 'システムエラー発生しました。＃１';
			exit;
		}
		
		// 整形してセッションに追加
		$new_dest = array(
			'serial' => $serial,
			'dest_name' => $_POST['dest_name'], 
			'dest_zip' => $_POST['dest_zip'], 
			'dest_address1' => $_POST['dest_add1'], 
			'dest_address2' => $_POST['dest_add2'].$_POST['dest_add3'], 
			'dest_tel' => $_POST['dest_tel'], 
			'need' => 1, 
			'edit' => '',
			'wrapping_word' => 'ラッピング・のし無し',
			'wrapping_set' => array(
									'wrapping' => 0,
									'noshi' => 3, 
									'noshi_upper' => '',
									'noshi_bottom' => '',
								),
			'arrival_word' => '希望到着日: 指定なし　時間指定: 指定なし',
			'kibou_datetime' => array(
									'kibou_date' => '指定なし',
									'kibou_time' => '指定なし',
								),
		);
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$serial] = $new_dest;
		
		echo 'OK';
		exit;
		
	} else if ($_POST['p_dest_kind'] == 'edit') {
		$error_arr = FM_validate($_POST, $A_validate, $A_post_name);
		// エラー
		if (! empty($error_arr)) {
			$D_mess = implode("<br>", $error_arr);
			echo $D_mess;
			exit;
		}
		
		$rtn=FM_update_buyer_destination($_POST);
		if($rtn===false){
			echo 'システムエラー発生しました。＃２';
			exit;
		}
		
		$num = (int)$_POST['serial'];
		// 正常→セッション修正
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['dest_name'] = $_POST['dest_name'];
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['dest_zip'] = $_POST['dest_zip'];
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['dest_address1'] = $_POST['dest_add1'];
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['dest_address2'] = $_POST['dest_add2'];
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['dest_tel'] = $_POST['dest_tel'];
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['edit'] = 'edited';
		
		echo 'OK';
		exit;
		
	} else if ($_POST['p_dest_kind'] == 'delete') {
		
		$num = (int)$_POST['p_del_number'];
		$rtn=FM_del_buyer_destination($num);
		if($rtn===false){
			echo 'システムエラー発生しました。＃３';
			exit;
		}
		$dest_arr=$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num];
		
		$seller_id = $_REQUEST['seller_id'];
		$_SESSION[DF_seller_info][$seller_id]['gift_delivery_fee_total'] -=$dest_arr['gift_delivery_fee'];
		$_SESSION[DF_seller_info][$seller_id]['gift_cool_bin_fee_total'] -=$dest_arr['gift_cool_bin_fee'];
		@$_SESSION[DF_seller_info][$seller_id]['wrapping_price'] -=$dest_arr['wrapping_price'];
		@$_SESSION[DF_seller_info][$seller_id]['noshi_price'] -=$dest_arr['noshi_price'];
		
		unset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]);
		
		echo 'OK';
		exit;
		
	//===========ラッピング設定編集============//
	} else if ($_POST['p_dest_kind'] == 'wrap') {
		
		$error_arr = FM_validate($_POST, $A_validate_wrap, $A_post_name_wrap);
		if ((int)$_POST['p_wrapping_set'] == 2 && (int)$_POST['p_noshi_set'] == 3 && empty($_POST['p_noshi_up'])) {
			$error_arr[] = '・上段ののし設定を入力してください。';
		}
		// エラー
		if (! empty($error_arr)) {
			$D_mess = implode("<br>", $error_arr);
			echo $D_mess;
			exit;
		}
		$num = (int)$_POST['p_wrap_number'];
		$p_wrapping_set = (int)$_POST['p_wrapping_set'];
		$p_noshi_set = (int)@$_POST['p_noshi_set'];
		$p_noshi_up = @$_POST['p_noshi_up'];
		$p_noshi_btm = @$_POST['p_noshi_btm'];
		// 正常→セッション修正
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_word'] = $A_wrapping_word[$p_wrapping_set];
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['wrapping'] = $p_wrapping_set;
		// のし設定あり
		if ( $p_wrapping_set == 2) {
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi'] = $p_noshi_set;
			if ($p_noshi_set == 1) {
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'] = 'お歳暮';
			} else if ($p_noshi_set == 2) {
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'] = 'お中元';
			} else if ($p_noshi_set == 3) {
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'] = $p_noshi_up;
			}
			// 文言追加
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_word'] .= '（上段：'.$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'];
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_bottom'] = $p_noshi_btm;
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_word'] .= '　下段：'.($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_bottom']?$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_bottom']:'無記名').'）';		
		// のしなし、初期化
		} else {
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'] = '';
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_bottom'] = '';
		}
		echo 'OK';
		exit;
		
	//===========希望到着日時変更============//
	} else if ($_POST['p_dest_kind'] == 'arrival') {
		
		$num = (int)$_POST['p_arrival_number'];
		$dst_kibou_date = $_POST['p_dest_kibou_date'];
		$dst_kibou_time = $_POST['p_dest_kibou_time'];
		
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['kibou_datetime']['kibou_date'] = $dst_kibou_date;
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['kibou_datetime']['kibou_time'] = $dst_kibou_time;
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['arrival_word'] = '希望到着日: '.$dst_kibou_date.'　時間指定: '.$dst_kibou_time;

		echo 'OK';
		exit;

	} else if($_POST['p_dest_kind'] == 'quantity'){
		$num = (int)$_POST['p_quantity_number'];
		$quantity = (int)$_POST['quantity'];
		if($num==0){
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity'] = $quantity;
		}else{
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['quantity'] = $quantity;
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['need'] = $quantity;
		}
		
		echo 'OK';
		exit;
	}
}
/*********** 最終確認 ***********/
if ($p_kind == 'order_confirm') {
	
	$seller_id = $_POST['seller_id'];
	if(empty($_SESSION[DF_order_gift_cart][$seller_id])){
		header('Location: /gift_order.php?p_kind=login_check');
		exit;
	}
	
	/*
	//買い手届け先変更の場合
	if($_SESSION[DF_sessionkey_member_info]['buyer_address1']!=$_POST['buyer_pref']){		
		$delivery_fee=$_SESSION[DF_seller_info][$seller_id]['delivery_fee'];
		$total=$_SESSION[DF_seller_info][$seller_id]['total']-$delivery_fee;
		if($_SESSION[DF_seller_info][$seller_id]['sub_total']<DF_delivery_order_limit){
			if(isset($_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$_POST['buyer_pref']])){
				$fee = $_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$_POST['buyer_pref']];
			}else{
				$fee =0;
				$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='not_match';
			}
			$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=$fee;
		}
		
		$_SESSION[DF_seller_info][$seller_id]['total']= $total+$_SESSION[DF_seller_info][$seller_id]['delivery_fee'];
		
		$_SESSION['jcb_card_request_data']['settle_price']=$_SESSION[DF_seller_info][$seller_id]['total'];
	}
	$_SESSION[DF_sessionkey_member_info]['delivery_buyer_name']=$_POST['buyer_name'];
	$_SESSION[DF_sessionkey_member_info]['buyer_zip']=$_POST['buyer_zip'];
	$_SESSION[DF_sessionkey_member_info]['buyer_address1']=$_POST['buyer_pref'];
	$_SESSION[DF_sessionkey_member_info]['buyer_address2']=$_POST['buyer_address'];
	$_SESSION[DF_sessionkey_member_info]['buyer_tel']=$_POST['buyer_tel'];
	$_SESSION['jcb_card_request_data']['buyer_name_kanji']=$_POST['buyer_name'];
	$_SESSION['jcb_card_request_data']['buyer_tel']=$_POST['buyer_tel'];
	*/
	
	
	if($_POST['p_payment']==5 && !empty($_POST['webcollectToken'])){
		$_SESSION['jcb_card_request_data']['webcollectToken']=$_POST['webcollectToken'];
	}
	if($_POST['card_use_type']=='save_card'){
		$_SESSION['jcb_card_request_data']['card_use_kind']='save_card';
	}
		
	$buyer_id = $_SESSION[DF_sessionkey_member_info]['buyer_id'];
	$buyer_name = $_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'];
	$buyer_zip = $_SESSION[DF_sessionkey_member_info]['buyer_zip'];
	$buyer_add = $_SESSION[DF_sessionkey_member_info]['buyer_address1'].$_SESSION[DF_sessionkey_member_info]['buyer_address2'];
	$buyer_tel = $_SESSION[DF_sessionkey_member_info]['buyer_tel'];
	$delivery_fee = $_SESSION[DF_seller_info][$seller_id]['delivery_fee'];
	$cool_bin_fee = $_SESSION[DF_seller_info][$seller_id]['cool_bin_fee'];
	$p_home_quantity=$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity'];
	$item_price=$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['item_price'];
	
	$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['price'] = $item_price+$delivery_fee+$cool_bin_fee;
	$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['delivery_fee'] = $delivery_fee;
	$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['cool_bin_fee'] = $cool_bin_fee;
	$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_forhome'] = $p_forhome;
	$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_kibou_date'] = $p_kibou_date;
	$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_kibou_time'] = $p_kibou_time;
	
	/*
	// 自宅用設定
	if (isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]) && ! isset($_POST['p_forhome'])) {
		$p_forhome = $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_forhome'];
		$p_home_quantity = $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity'];
	}
	$total_count = 0;
	$noshi_count = 0;
	$wrap_count = 0;
	// 数量確認
	if ($p_forhome == 1) {
		$total_count += $p_home_quantity;
	}
	$dest_arr = array();
	if (isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest])) {
		$dest_arr = $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest];
	}
	foreach ($dest_arr as $key => $dat) {
		if ($dat['edit'] == 'delete') continue;
		$total_count += $dat['need'];
		if ($dat['need'] > 0) {
			if ($dat['wrapping_set']['wrapping'] == 1) {
				$wrap_count++;
			} else if ($dat['wrapping_set']['wrapping'] == 2) {
				$noshi_count++;
			}
		}
	}*/
	
	$total_count = 0;
	$noshi_count = 0;
	$wrap_count = 0;
	
	// のし・ラッピング、合計金額を計算
	$sum_price = 0;
	$item_price = $_SESSION[DF_seller_info][$seller_id]['total'];
	$gift_delivery_fee_total=$_SESSION[DF_seller_info][$seller_id]['gift_delivery_fee_total'];
	$gift_cool_bin_fee_total=$_SESSION[DF_seller_info][$seller_id]['gift_cool_bin_fee_total'];
	$noshi_price = $_SESSION[DF_seller_info][$seller_id]['noshi_price'];
	$wrapping_price = $_SESSION[DF_seller_info][$seller_id]['wrapping_price'];
	
	$sum_price = (int)$item_price + (int)$noshi_price + (int)$wrapping_price + (int)$gift_delivery_fee_total + (int)$gift_cool_bin_fee_total ;
	/*if (!empty($member_regist_arr)) {
		$sum_price += (int)DF_member_price;
	}*/
	
	//ギフトの宛先
	/*$dest_arr = array();
	if (isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest])) {
		$dest_arr = $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest];
	}
	foreach ($dest_arr as $key => $dat) {
		if ($dat['edit'] == 'delete') continue;
		if(@$dat['quantity']>0){
			$total_count +=$dat['quantity'];
		}
	}*/
	
	// 最終在庫チェック	
	foreach($_SESSION[DF_order_gift_cart][$seller_id] as $item_serial=>$item_arr){
		$p_type=$item_arr['p_type'];
		$A_item_new = FM_get_product_item_gift($item_serial, $p_type);
		$total_count += $item_arr['quantity'];
		if ($item_arr['zaiko_flg']==1 && (int)$A_item_new['zaiko'] < $item_arr['quantity']) {
			$D_mess .= '<span style="color:red;">現在の在庫数がお求めの数量を下回りました。現在の在庫：あと '.$A_item_new['zaiko'].'個</span>';
		}
	}
	
	/*
	// ポイント数取得
	$buyer_point = FM_get_buyer_point($buyer_id);
	// 使用可能なすべてのポイントを使う
	if ($p_select_point == 1) {
		$p_use_point = $buyer_point;
		if ($sum_price < $p_use_point) $p_use_point = $sum_price;
	// 使うポイントを決める
	} else {
		if ($buyer_point < $p_use_point) $p_use_point = $buyer_point;
		if ($sum_price < $p_use_point) $p_use_point = $sum_price;
	}*/
	
	// 最終在庫チェック
	/*$A_item_new = FM_get_product_item($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['product_serial']);
	if ((int)$A_item_new['zaiko'] < $total_count) {
		$D_mess = '<span style="color:red;">現在の在庫数がお求めの数量を下回りました。現在の在庫：あと '.$A_item_new['zaiko'].'個</span>';
		$p_kind = 'shipping';
	} else if ($p_use_point % 500 != 0) {
		$p_use_point -= ($p_use_point % 500);
		$D_mess = '<span style="color:red;">ご使用になれるポイントは500ポイント単位となります。</span>';
		$p_kind = 'payment';
	} else if ($p_payment == 0 && $sum_price <= $p_use_point) {
		$D_mess = '<span style="color:red;">クレジットカード決済の場合、全額をポイントでお支払いにはなれません。</span>';
		$p_kind = 'payment';
	}else {
		
		// 取得ポイントをここで計算する
		$t_add_point = 0;
		$move_kind = '';
		$target_amount = $sum_price - $p_use_point;
		$magnification = 1;
		// 会員登録中（適用キャンペーンを取得する関数が使えない）
		if (!empty($member_regist_arr)) {
			// 対象金額から会員費引く
			$target_amount -= DF_member_price;
			// 招待者かどうかだけ
			$t_campaign = FM_get_campaign_data();
			// 紹介キャンペーン中で、招待コードの入力があれば(※入会時購入に適用されるのはキャンペーン期間内だけ)
			if (isset($t_campaign[DF_invite_campaign]) && !empty($member_regist_arr['p_invited'])) {
				// 紹介元会員を探して、該当があり、登録がキャンペーン期間内なら
				$t_invite_member = FM_get_buyer_from_invcode($member_regist_arr['p_invited']);
				if (! empty($t_invite_member) && (date('Y-m-d') <= $t_campaign[DF_invite_campaign]['campaign_end'])) {
					$t_add_point = floor($target_amount / 100) * $t_campaign[DF_invite_campaign]['percentage'];
					$move_kind = $t_campaign[DF_invite_campaign]['move_kind'];
					$magnification = $t_campaign[DF_invite_campaign]['percentage'];
				}
			}
		} else {
			// 該当キャンペーン取得
			$t_campaign = FM_get_suitable_campaign($_SESSION[DF_sessionkey_member_info]['buyer_serial'], $_SESSION[DF_sessionkey_member_info]['subscription_date']);
			if (! empty($t_campaign)) {
				$t_add_point = floor($target_amount / 100) * $t_campaign['percentage'];
				$move_kind = $t_campaign['move_kind'];
				$magnification = $t_campaign['percentage'];
			}
		}
		if ($t_add_point == 0) {
			$t_add_point = floor($target_amount / 100) * DF_ordinary_point;
			$magnification = DF_ordinary_point;
			$move_kind = 'plus_buy';
		}
		
		// セッションにオーダーに必要な数値格納
		$_SESSION[DF_ichiba_name]['post'] = array();
		$_SESSION[DF_ichiba_name]['post']['p_count'] = $total_count;			// 購入個数合計
		$_SESSION[DF_ichiba_name]['post']['p_price'] = $sum_price;				// 金額合計(年会費、のし、ラッピング含む)
		$_SESSION[DF_ichiba_name]['post']['p_usepoint'] = $p_use_point;			// 使用ポイント
		$_SESSION[DF_ichiba_name]['post']['p_getpoint'] = $t_add_point;			// 取得ポイント
		$_SESSION[DF_ichiba_name]['post']['noshi_price'] = $noshi_price;		// のしの価格
		$_SESSION[DF_ichiba_name]['post']['wrapping_price'] = $wrapping_price;	// ラッピングの価格
		$_SESSION[DF_ichiba_name]['post']['move_kind'] = $move_kind;			// 取得ポイント種別
		$_SESSION[DF_ichiba_name]['post']['percentage'] = $magnification;		// 取得ポイント割合(購入金額の?%)
		
		if ($p_payment == 0) {
			// カード決済確認
			$D_payment = FM_make_cardtable($buyer_id, 0, DF_ichiba_name);
		} else {
			$D_payment = $DF_payment[$p_payment];
		}

		require_once './tpl/confirm_order_tpl.php';
		exit;
	}*/
	
	// セッションにオーダーに必要な数値格納
	$_SESSION[DF_ichiba_name]['post'] = array();
	$_SESSION[DF_ichiba_name]['post']['p_count'] = $total_count;			// 購入個数合計
	$_SESSION[DF_ichiba_name]['post']['item_price'] = $_SESSION[DF_seller_info][$seller_id]['sub_total'];
	$_SESSION[DF_ichiba_name]['post']['p_price'] = $sum_price;				// 金額合計(年会費、のし、ラッピング含む)
	$_SESSION[DF_ichiba_name]['post']['p_usepoint'] = 0;			// 使用ポイント
	$_SESSION[DF_ichiba_name]['post']['p_getpoint'] = 0;			// 取得ポイント
	$_SESSION[DF_ichiba_name]['post']['noshi_price'] = $noshi_price;		// のしの価格
	$_SESSION[DF_ichiba_name]['post']['wrapping_price'] = $wrapping_price;	// ラッピングの価格
	$_SESSION[DF_ichiba_name]['post']['move_kind'] = '';			// 取得ポイント種別
	$_SESSION[DF_ichiba_name]['post']['percentage'] = 1;		// 取得ポイント割合(購入金額の?%)
	$_SESSION[DF_ichiba_name]['post']['postage_total']=$_SESSION[DF_seller_info][$seller_id]['delivery_fee']+$gift_delivery_fee_total;
	$_SESSION[DF_ichiba_name]['post']['cool_bin_fee']=$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']+$gift_cool_bin_fee_total;
	$_SESSION[DF_ichiba_name]['post']['discount_amount']=$_SESSION[DF_seller_info][$seller_id]['discount_amount'];
	$_SESSION[DF_ichiba_name]['post']['p_order_comment']=$p_order_comment;
	
	/*if ($p_payment == 0) {
		// カード決済確認
		$D_payment = FM_make_cardtable($buyer_id, 0, DF_ichiba_name);
	} else {
		$D_payment = $DF_payment[$p_payment];
	}*/
	
	$D_payment = $DF_payment[$p_payment];
	require_once './tpl/gift_confirm_order_tpl.php';
	exit;
	
}

/*********** お支払確認 ***********/
if ($p_kind == 'payment') {
	
	$seller_id = $_REQUEST['seller_id'];
	
	/*if(empty($_SESSION[DF_order_gift_cart][$seller_id])){
		header('Location: /gift_order.php?p_kind=login_check');
		exit;
	}*/
	
	$member_regist_arr = array();
	$buyer_name = 'ゲスト';
	$current_seller_id = $_REQUEST['seller_id'];
	// 会員
	$buyer_name = $_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'];
	$buyer_id = $_SESSION[DF_sessionkey_member_info]['buyer_id'];
	$buyer_serial = $_SESSION[DF_sessionkey_member_info]['buyer_serial'];
	$buyer_info = $_SESSION[DF_sessionkey_member_info];
	if (empty($buyer_info)) {
		// エラー
		FM_view_err_page('セッションエラーが発生しました。<br>ブラウザの戻るボタンは使用しないでください。<br>お手数ですがもう一度お手続きをお願いいたします。', '商品詳細ページ');
		exit;
	}
	$buyer_tel = $buyer_info['buyer_tel'];
	$buyer_zip = $buyer_info['buyer_zip'];
	$buyer_pref = $buyer_info['buyer_address1'];
	$buyer_add = $buyer_info['buyer_address2'];
	
	//unset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
	
	// 送付先状況取得
	/*if (! isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest])) {
		$buyer_dest = FM_get_buyer_destination($buyer_serial);
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest] = $buyer_dest;
	}*/
	//print_r($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
	
	// 県名ドロップダウン
	$D_prefectures = FM_make_droplist($DF_ken_arr, 'buyer_pref', $buyer_pref, ' id="buyer_pref"');
	$D_prefectures2 = FM_make_droplist($DF_ken_arr, 'new_dest_add1', "", ' id="edit_dest_add1"');
	
	// 着日ドロップダウンリスト
	$c_chakubi = new G_class_chakubi($_SESSION[DF_seller_info][$seller_id]['shipping_prepare']);
	$t_chakustart_date = $c_chakubi->F_class_chakubi_get_shonichi();
	
	$_SESSION[DF_seller_info][$seller_id]['kibou_date'] = $c_chakubi->F_class_chakubi_make_chaku_ddl($t_chakustart_date, 'p_kibou_date', $p_kibou_date);
	$delivery_shitei_info = F_get_delivery_time($_SESSION['seller_info'][$seller_id]['seller_id']);
	if(!empty($delivery_shitei_info)){
		// 指定可能到着希望時間帯
		$A_kibou_time = array();
		$A_kibou_time[]='指定なし';
		foreach($delivery_shitei_info as $key => $value){
			$A_kibou_time[] = $value['shitei'];
		}
	}
	$_SESSION[DF_seller_info][$seller_id]['kibou_time'] = FM_make_droplist($A_kibou_time, 'p_kibou_time', $p_kibou_time, ' id="p_kibou_time"');	
	
	
	// 着日ドロップダウンリスト
	$D_kibou_date_dest = $c_chakubi->F_class_chakubi_make_chaku_ddl($t_chakustart_date, 'edit_kibou_date', '指定なし');
	$D_kibou_time_dest = FM_make_droplist($A_kibou_time, 'edit_kibou_time', "指定なし", ' id="edit_kibou_time"');		
		
	
	// 贈答用でないなら、送付先はリセット
	/*if ($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_gift_setting] == 0) {
		unset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
	}*/
	for(;;) {
		$total_count = 0;
		$noshi_count = 0;
		$wrap_count = 0;
		// 数量確認
		/*if ($p_forhome == 1) {
			$total_count += $p_home_quantity;
		}*/
		
		$temp_item=array();
		$temp_code=array();
		$daibiki_flg=true;
		foreach($_SESSION[DF_order_gift_cart][$seller_id] as $item_serial=>$item_arr){
			$temp_item[]=$item_arr['product_name'].' '.$item_arr['price'].'X'.$item_arr['quantity'];
			$temp_code[]=$item_arr['product_code'];
			$total_count += $item_arr['quantity'];
			if($daibiki_flg===true && ($item_arr['cate1_serial']==4 || @$item_arr['seisen_flg']==1)){
				$daibiki_flg=false;
			}
		}
		
		// JCBの商品名、商品コード
		$product_name=join(',',$temp_item);
		$product_code=join(',',$temp_code);
		$goods_name=mb_strlen($item_arr['product_name'], 'sjis')>200?mb_substr($item_arr['product_name'],0,190,'sjis'):$item_arr['product_name'];
		if(count($_SESSION[DF_order_gift_cart][$seller_id])>1){
			$goods_name .='　等';
		}
		
		//ギフトの宛先
		$dest_arr = array();
		if (isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest])) {
			$dest_arr = $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest];
			$p_dest_size = count($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
		}
		
		//ギフトの送料、クール便、ラッピング・のし金
		$git_item_total=0;
		$gift_delivery_fee_total=0;
		$gift_cool_bin_fee_total=0;
		$gift_wrapping_price_total=0;
		$gift_noshi_price_total=0;
		$delivery_flg='';
		foreach ($dest_arr as $key => $dat) {
			if ($dat['edit'] == 'delete') continue;
			
			if(@$dat['quantity']>0){
				
				$git_item_total +=$dat['quantity'];
				
				// 送料
				if(($item_arr['price']*$dat['quantity'])<DF_delivery_order_limit){
					if(isset($_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$dat['dest_address1']])){
						$fee = $_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$dat['dest_address1']];
						$delivery_flg='match';
					}else{
						$fee =0;
						if($delivery_flg!='match'){
							$delivery_flg='not_match';
						}
					}
					@$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['gift_delivery_fee']=$fee;
					$gift_delivery_fee_total +=$fee;
				}else{
					@$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['gift_delivery_fee']=0;
				}
				
				// クール便
				if($item_arr['cool_bin_flg']==1){
					@$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['gift_cool_bin_fee']=DF_cool_bin;
					$gift_cool_bin_fee_total +=DF_cool_bin;
				}
				
				//ラッピング
				if ($dat['wrapping_set']['wrapping'] == 1) {
					$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['wrapping_price']=$item_arr['wrapping_price'];
					$gift_wrapping_price_total +=$item_arr['wrapping_price'];
				}
				
				//のし
				if ($dat['wrapping_set']['wrapping'] == 2) {
					$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['wrapping_price']=$item_arr['noshi_price'];
					$gift_noshi_price_total +=$item_arr['noshi_price'];
				}
				
			}else{
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['gift_delivery_fee']=0;
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['gift_cool_bin_fee']=0;				
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['wrapping_price']=0;
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['noshi_price']=0;
			}
			
		}
		$_SESSION[DF_seller_info][$seller_id]['gift_delivery_fee_total']=$gift_delivery_fee_total;
		$_SESSION[DF_seller_info][$seller_id]['gift_cool_bin_fee_total']=$gift_cool_bin_fee_total;
		@$_SESSION[DF_seller_info][$seller_id]['wrapping_price']=$gift_wrapping_price_total;
		@$_SESSION[DF_seller_info][$seller_id]['noshi_price']=$gift_noshi_price_total;
		
		
		
		//買い手自宅の情報
		$A_member_info = $_SESSION[DF_sessionkey_member_info];
		if(!isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_forhome'])){
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_forhome'] = $p_forhome;
		}
		$p_home_quantity=0;
		if(!isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity'])){
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity'] = $total_count;
			$p_home_quantity=$total_count;
		}else{
			$p_home_quantity=$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity'];
		}
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['item_price'] = $item_arr['price']*$p_home_quantity;
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_kibou_date'] = $p_kibou_date;
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_kibou_time'] = $p_kibou_time;
		
		//買い手送料
		$_SESSION[DF_seller_info][$seller_id]['total']-=$_SESSION[DF_seller_info][$seller_id]['delivery_fee'];
		$_SESSION[DF_seller_info][$seller_id]['total']-=$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee'];
		if($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity']<=0 && $git_item_total>0){
			$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=0;
			$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='';
			$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=0;
		}else{
			if(($item_arr['price']*$p_home_quantity)<DF_delivery_order_limit){
				if(isset($_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']])){
					$fee = $_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']];
					$delivery_flg='match';
				}else{
					$fee =0;
					if($delivery_flg!='match'){
						$delivery_flg='not_match';
					}
				}
				$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=$fee;
				$_SESSION[DF_seller_info][$seller_id]['total']+=$fee;
			}else{
				$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=0;
			}
			
			// 買い手クール便
			if($item_arr['cool_bin_flg']==1){
				$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=DF_cool_bin;
				$_SESSION[DF_seller_info][$seller_id]['total']+=DF_cool_bin;
			}
			
		}
		
		//送料確認
		if($delivery_flg=='not_match'){
			$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='not_match';
		}else{
			$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='';
		}
		
		if ($total_count < 1) {
			$D_mess = '<span style="color:red;">お求めの数量を確認してください。</span>';
			$p_kind = 'shipping';
			break;
		}
		//$A_item_new = FM_get_product_item($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['product_serial']);
		/*if ((int)$A_item_new['zaiko'] < $total_count) {
			$D_mess = '<span style="color:red;">現在の在庫数がお求めの数量を下回りました。現在の在庫：あと '.$A_item_new['zaiko'].'個</span>';
			$p_kind = 'shipping';
			break;
		}*/
		
		
		// カード決済合計金額
		$sum_price = 0;
		$item_price = $_SESSION[DF_seller_info][$seller_id]['total'];
		$delivery_fee=$_SESSION[DF_seller_info][$seller_id]['delivery_fee'];
		$sum_price = (int)$item_price + (int)$gift_noshi_price_total + (int)$gift_wrapping_price_total+ (int)$gift_delivery_fee_total+ (int)$gift_cool_bin_fee_total;
		/*if (!empty($member_regist_arr)) {                                                            
			$sum_price += (int)DF_member_price;
		}*/
		
		// カード決済確認
		$credit_card = new G_class_credit_card();
		$credit_card->FC_set_member($buyer_id);	//買い手idをセット

		$flg_card_member = $credit_card->FC_is_card_member();//KaiinIdをセットする、返り値に関しては気にしないこと
		$D_card_table = FM_make_cardtable($buyer_id, 1, DF_ichiba_name);
		
		// jcbカード決済の場合
		$flg_card_member_jcb = true;
		$authentication_key=substr(md5($buyer_id),0,8);
		$order_no=time();
		
		//jcbカード買い手確認
		$params = array(
			'function_div'	=> 'A03'
			,'trader_code'	=> DF_jcb_trader_code
			,'member_id'	=> $buyer_id
			,'authentication_key'=>$authentication_key
			,'check_sum'=>hash('sha256', ($buyer_id.(substr(md5($buyer_id),0,8)).DF_jcb_access_key))
		);
		$response = FM_request($jcb_request_url['credit_info_request'], 'POST', $params);
		//echo htmlspecialchars($response);
		$xml   = simplexml_load_string($response);
		$ret_array = json_decode(json_encode((array) $xml), true);
		$ret_array = array($xml->getName() => $ret_array);
		$card_info = $ret_array['return'];
		
		$webcollectToken=@$_SESSION['jcb_card_request_data']['webcollectToken'];
		unset($_SESSION['jcb_card_request_data']);
		$_SESSION['jcb_card_request_data']=array(
			 'order_no'=>$order_no
			,'goods_name'=>$goods_name
			,'product_code'=>$product_code
			,'product_name'=>$product_name
			,'settle_price'=>$sum_price
			,'buyer_name_kanji'=>$buyer_name
			,'buyer_tel'=>$buyer_tel
			,'buyer_email'=>$buyer_id
			,'member_id'=>$buyer_id
			,'authentication_key'=>$authentication_key
			,'ta_souryou'=>$delivery_fee
			,'webcollectToken'=>$webcollectToken
		);
		
		// ポイント数取得
		$buyer_point = FM_get_buyer_point($buyer_id);
		require_once './tpl/gift_payment_order_tpl.php';
		exit;
	}
}
/*********** 会員 ログイン処理 ***********/
if ($p_kind == 'member_login') {
	// 手動ログインチェック
	$FB_is_auto = false;
	if (isset($_POST['p_buyer_id']) && isset($_POST['p_buyer_password'])) {
		$ret = FM_auth($p_buyer_id, $p_buyer_password, $tmp_name);
		if ($ret === true) {
			// セッション値に買い手情報を持つ
			$_SESSION[DF_sessionkey_member_info]['buyer_id'] = $p_buyer_id;
			$_SESSION[DF_sessionkey_member_info]['buyer_name'] = $tmp_name;
			$_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'] = $tmp_name;
			// 自動ログインセット
			FM_setup_login($p_buyer_id);
			$FB_is_auto = true;
		}
	}
	if ($FB_is_auto) {
		$A_member_info = FM_get_buyer_member($p_buyer_id);
		
		// 会員情報取得
		foreach ($A_member_info as $key => $info) {
			if (!isset($_SESSION[DF_sessionkey_member_info][$key])) {
				$_SESSION[DF_sessionkey_member_info][$key] = $info;
			}
		}
		$_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'] = $_SESSION[DF_sessionkey_member_info]['buyer_name'];
		
		if ((int)$A_member_info['buyer_reg_status'] == 0) {
			$p_kind = 'cart_summary';
		} else {
			$D_mess = '<span style="color:red;">※年会費のお支払いが確認されるまで二度目のご購入はできません。</span>';
			// 画面表示
			require_once './tpl/gift_login_order_tpl.php';
			exit;
		}
	} else {
		$D_mess = '<span style="color:red;">※ログインIDまたはパスワードが違います。</span>';
		// 画面表示
		require_once './tpl/gift_login_order_tpl.php';
		exit;
	}
}



/*********** 注文トップ ログイン ***********/
if ($p_kind == 'top') {
	if(@$_SESSION[DF_sessionkey_member_info]['buyer_id']){
		$A_member_info = FM_get_buyer_member($_SESSION[DF_sessionkey_member_info]['buyer_id']);
		// 会員情報取得
		foreach ($A_member_info as $key => $info) {
			if (!isset($_SESSION[DF_sessionkey_member_info][$key])) {
				$_SESSION[DF_sessionkey_member_info][$key] = $info;
			}
		}
		$_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'] = $_SESSION[DF_sessionkey_member_info]['buyer_name'];
	}
	
	$A_item = FM_get_product_item_gift($p_serial, $p_type);
	
	$zaiko_flg = $A_item['zaiko_flg'];
	$A_item['p_type'] = $p_type;
	$A_item['moto_flg'] = $moto_flg;
	if (empty($A_item)) {
		// エラー
		FM_view_err_page('お探しの商品が見つかりません。', '商品詳細ページ', 'notfound');
		exit;
	}
	if ((int)$A_item['zaiko'] <= 0 && $zaiko_flg == 1) {
		header('Location: /gift_item.php?type='.$p_type.'&p_serial='.$p_serial.'&moto_flg='.$moto_flg);
		exit;
	}
	
	$A_seller = FM_get_seller_info($A_item['seller_id']);
	$A_item['seller_name'] = $A_seller['seller_yagou'];
	$_SESSION[DF_seller_info][$A_item['seller_id']]=$A_seller;
	
	$_SESSION[DF_sessionkey_order_gift_cart] = array();
	
	//カートセッション
	$p_home_quantity +=@$_SESSION[DF_order_gift_cart][$A_item['seller_id']][$A_item['product_serial']]['quantity'];
	if($zaiko_flg==1 && $p_home_quantity>$A_item['zaiko']){
		$p_home_quantity=$A_item['zaiko'];
	}
	if($A_item['seller_id'] && $A_item['product_serial']){
		unset($_SESSION[DF_order_gift_cart]);
		$_SESSION[DF_order_gift_cart] = array();
		$_SESSION[DF_order_gift_cart][$A_item['seller_id']][$A_item['product_serial']] = $A_item;
		@$_SESSION[DF_order_gift_cart][$A_item['seller_id']][$A_item['product_serial']]['quantity'] = $p_home_quantity;
	}
	
	header('Location: /gift_order.php?p_kind=login_check');
	exit;
}



if($p_kind == 'login_check'){
	
	// ログインチェック
	$FB_is_auto = false;
	if (isset($_SESSION[DF_sessionkey_member_info]['buyer_name'])) {
		$FB_is_auto = true;
	} else {
		// ログイン中かチェック
		if (!empty($_COOKIE['auto_login'])) {
			
			$ret = FM_check_auto_login($_COOKIE['auto_login'], $tmp_id, $tmp_name);
			if ($ret === true) {
				$FB_is_auto = true;
				// セッション値に買い手情報を持つ
				$_SESSION[DF_sessionkey_member_info]['buyer_id'] = $tmp_id;
				$_SESSION[DF_sessionkey_member_info]['buyer_name'] = $tmp_name;
				$_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'] = $tmp_name;
				// 自動ログインセット
				FM_setup_login($tmp_id, $_COOKIE['auto_login']);
				
				$A_member_info = FM_get_buyer_member($_SESSION[DF_sessionkey_member_info]['buyer_id']);
				// 会員情報取得
				foreach ($A_member_info as $key => $info) {
					if (!isset($_SESSION[DF_sessionkey_member_info][$key])) {
						$_SESSION[DF_sessionkey_member_info][$key] = $info;
					}
				}
			}
		}
	}
	
	if(isset($_SESSION[DF_order_gift_cart])){
		foreach($_SESSION[DF_order_gift_cart] as $seller_id=>$seller_arr){
			$_SESSION[DF_seller_info][$seller_id]['sub_total']=0;
			$_SESSION[DF_seller_info][$seller_id]['until_free_shipping']=0;
			$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=0;
			$_SESSION[DF_seller_info][$seller_id]['total']=0;
			$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=0;
			$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='';
			foreach($seller_arr as $item_serial=>$item_arr){
				$_SESSION[DF_seller_info][$seller_id]['sub_total']+=$item_arr['price']*$item_arr['quantity'];
				if($item_arr['cool_bin_flg']==1){
					$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=DF_cool_bin;
				}
			}
			
			$_SESSION[DF_seller_info][$seller_id]['total']=$_SESSION[DF_seller_info][$seller_id]['sub_total']+$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee'];;
		}
	}
	
	if ($FB_is_auto) {
		$A_member_info = $_SESSION[DF_sessionkey_member_info];
		
		foreach($_SESSION[DF_order_gift_cart] as $seller_id=>$seller_arr){
			
			if($_SESSION[DF_seller_info][$seller_id]['sub_total']<DF_delivery_order_limit){
				$_SESSION[DF_seller_info][$seller_id]['until_free_shipping'] =DF_delivery_order_limit-$_SESSION[DF_seller_info][$seller_id]['sub_total'];
				if(isset($_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']])){
					$fee = $_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']];
				}else{
					$fee =0;
					$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='not_match';
				}
				$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=$fee;
			}
			$_SESSION[DF_seller_info][$seller_id]['total']+=$_SESSION[DF_seller_info][$seller_id]['delivery_fee'];
		}
		
		if ((int)$A_member_info['buyer_reg_status'] == 0) {
			$p_kind = 'cart_summary';
		} else {
			$D_mess = '<span style="color:red;">※年会費のお支払いが確認されるまで二度目のご購入はできません。</span>';
			// 画面表示
			require_once './tpl/gift_login_order_tpl.php';
			exit;
		}
	} else {
		
		// 画面表示
		require_once './tpl/gift_login_order_tpl.php';
		exit;
	}
}


/*********** カート ***********/
if ($p_kind == 'shipping' || $p_kind == 'cart_summary') {
	
	$A_member_info = $_SESSION[DF_sessionkey_member_info];
	$order_discount_flg=false;
	
	if(FM_order_campain_check($_SESSION[DF_sessionkey_member_info]['buyer_id'],$A_member_info['buyer_address1'].$A_member_info['buyer_address2'])){
		$order_discount_flg=true;
	}
	
	foreach($_SESSION[DF_order_gift_cart] as $seller_id=>$seller_arr){
		if(empty($seller_id)) continue;
		$_SESSION[DF_seller_info][$seller_id]['sub_total']=0;
		$_SESSION[DF_seller_info][$seller_id]['until_free_shipping']=0;
		$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=0;
		$_SESSION[DF_seller_info][$seller_id]['total']=0;
		$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=0;
		$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='';
		
		foreach($seller_arr as $item_serial=>$item_arr){
			$_SESSION[DF_seller_info][$seller_id]['sub_total']+=$item_arr['price']*$item_arr['quantity'];
			if($item_arr['cool_bin_flg']==1){
				$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=DF_cool_bin;
			}
		}
		
		if($_SESSION[DF_seller_info][$seller_id]['sub_total']<DF_delivery_order_limit){
			$_SESSION[DF_seller_info][$seller_id]['until_free_shipping'] =DF_delivery_order_limit-$_SESSION[DF_seller_info][$seller_id]['sub_total'];
			if(isset($_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']])){
				$fee = $_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']];
			}else{
				$fee =0;
				$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='not_match';
			}
			//$fee = @$_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']]?@$_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']]:0;
			$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=$fee;
		}
		
		$_SESSION[DF_seller_info][$seller_id]['total']=$_SESSION[DF_seller_info][$seller_id]['sub_total']+$_SESSION[DF_seller_info][$seller_id]['delivery_fee']+$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee'];
		$_SESSION[DF_seller_info][$seller_id]['discount_amount'] = 0;
		if($order_discount_flg==true && $_SESSION[DF_seller_info][$seller_id]['sub_total']>=DF_delivery_order_limit){
			$_SESSION[DF_seller_info][$seller_id]['discount_amount'] = DF_discount_amout;
			$_SESSION[DF_seller_info][$seller_id]['total'] -= DF_discount_amout;
			$order_discount_flg=false;
		}
	}

	require_once './tpl/gift_cart_order_tpl.php';

	exit;
}

if($p_kind == 'update_item_quantity'){
	
	$A_member_info = $_SESSION[DF_sessionkey_member_info];
	$seller_id=$_POST['seller_id'];
	$item_serial=$_POST['item_serial'];
	$quantity=$_POST['quantity'];
	$_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['quantity']=$quantity;
	
	$_SESSION[DF_seller_info][$seller_id]['sub_total']=0;
	$_SESSION[DF_seller_info][$seller_id]['until_free_shipping']=0;
	$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=0;
	$_SESSION[DF_seller_info][$seller_id]['total']=0;
	$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=0;
	$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='';
	foreach($_SESSION[DF_order_gift_cart][$seller_id] as $item_serial=>$item_arr){
		$_SESSION[DF_seller_info][$seller_id]['sub_total']+=$item_arr['price']*$item_arr['quantity'];
		if($item_arr['cool_bin_flg']==1){
			$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=DF_cool_bin;
		}
	}
	if($_SESSION[DF_seller_info][$seller_id]['sub_total']<DF_delivery_order_limit){
		$_SESSION[DF_seller_info][$seller_id]['until_free_shipping'] =DF_delivery_order_limit-$_SESSION[DF_seller_info][$seller_id]['sub_total'];
		if(isset($_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']])){
			$fee = $_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']];
		}else{
			$fee =0;
			$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='not_match';
		}
		$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=$fee;
	}
	
	$_SESSION[DF_seller_info][$seller_id]['total']=$_SESSION[DF_seller_info][$seller_id]['sub_total']+$_SESSION[DF_seller_info][$seller_id]['delivery_fee']+$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee'];
	
	$order_discount_flg=false;
	if(FM_order_campain_check($_SESSION[DF_sessionkey_member_info]['buyer_id'],$A_member_info['buyer_address1'].$A_member_info['buyer_address2'])){
		$order_discount_flg=true;
	}
	$rtn_arr=array(
		$_SESSION[DF_seller_info][$seller_id]['sub_total']
		,$_SESSION[DF_seller_info][$seller_id]['delivery_fee']
		,$_SESSION[DF_seller_info][$seller_id]['total']
		,$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']
		,$_SESSION[DF_seller_info][$seller_id]['until_free_shipping']
		,$_SESSION[DF_seller_info][$seller_id]['delivery_flg']
	);
	

	
	if($order_discount_flg==true){
		
		$prv_discount_seller='';
		foreach($_SESSION[DF_order_gift_cart] as $prv_s_id=>$seller_arr){
			if($_SESSION[DF_seller_info][$prv_s_id]['discount_amount']>0){
				$prv_discount_seller=$prv_s_id;
				break;
			}
		}
		
		$discount_sid='';
		foreach($_SESSION[DF_order_gift_cart] as $s_id=>$seller_arr){
			if($_SESSION[DF_seller_info][$s_id]['sub_total']>=DF_delivery_order_limit){
				$_SESSION[DF_seller_info][$seller_id]['total'] -= DF_discount_amout;
				$discount_sid=$s_id;
				$order_discount_flg=false;
				break;
			}
		}
		
		if($discount_sid != $prv_discount_seller){
			$rtn_arr=array(
				$_SESSION[DF_seller_info][$seller_id]['sub_total']
				,$_SESSION[DF_seller_info][$seller_id]['delivery_fee']
				,$_SESSION[DF_seller_info][$seller_id]['total']
				,$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']
				,$_SESSION[DF_seller_info][$seller_id]['until_free_shipping']
				,$_SESSION[DF_seller_info][$seller_id]['delivery_flg']
				,'changed_discount'
			);
		}else{
			$rtn_arr=array(
				$_SESSION[DF_seller_info][$seller_id]['sub_total']
				,$_SESSION[DF_seller_info][$seller_id]['delivery_fee']
				,$_SESSION[DF_seller_info][$seller_id]['total']
				,$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']
				,$_SESSION[DF_seller_info][$seller_id]['until_free_shipping']
				,$_SESSION[DF_seller_info][$seller_id]['delivery_flg']
			);
		}
	}
	
	echo json_encode($rtn_arr);
	exit;
}
if($p_kind == 'delete_cart_item'){
	$A_member_info = $_SESSION[DF_sessionkey_member_info];
	$seller_id=$_POST['seller_id'];
	$item_serial=$_POST['item_serial'];
	unset($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]);
	
	$order_discount_flg=false;
	if(FM_order_campain_check($_SESSION[DF_sessionkey_member_info]['buyer_id'],$A_member_info['buyer_address1'].$A_member_info['buyer_address2'] )){
		$order_discount_flg=true;
	}
	
	if(empty($_SESSION[DF_order_gift_cart][$seller_id])){
		unset($_SESSION[DF_order_gift_cart][$seller_id]);
	}else{
		$_SESSION[DF_seller_info][$seller_id]['sub_total']=0;
		$_SESSION[DF_seller_info][$seller_id]['until_free_shipping']=0;
		$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=0;
		$_SESSION[DF_seller_info][$seller_id]['total']=0;
		$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=0;
		$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='';
		foreach($_SESSION[DF_order_gift_cart][$seller_id] as $item_serial=>$item_arr){
			$_SESSION[DF_seller_info][$seller_id]['sub_total']+=$item_arr['price']*$item_arr['quantity'];
			if($item_arr['cool_bin_flg']==1){
				$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=DF_cool_bin;
			}
		}
		if($_SESSION[DF_seller_info][$seller_id]['sub_total']<DF_delivery_order_limit){
			$_SESSION[DF_seller_info][$seller_id]['until_free_shipping'] =DF_delivery_order_limit-$_SESSION[DF_seller_info][$seller_id]['sub_total'];
			if(isset($_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']])){
				$fee = $_SESSION[DF_seller_info][$seller_id]['delivery_fee_arr'][$A_member_info['buyer_address1']];
			}else{
				$fee =0;
				$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='not_match';
			}
			$_SESSION[DF_seller_info][$seller_id]['delivery_fee']=$fee;
		}
		$_SESSION[DF_seller_info][$seller_id]['total']=$_SESSION[DF_seller_info][$seller_id]['sub_total']+$_SESSION[DF_seller_info][$seller_id]['delivery_fee']+$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee'];
	}
	
	echo 'delete_cart_item';
	exit;
}
//-----------------------------------------------
// 関数定義
//-----------------------------------------------

//XSS対策 連想配列のvalueをエスケープ
function F_change_safe_array($array) { 
    $_array = array();
    foreach ($array as $key => $val) {
        $_array[$key] = FM_change_safe_html($val); //FM_H() でhtmlspecialchars関数をかける
    }
    return $_array;
}

/*****************************************
希望到着時間一覧取得
	$f_id	= ID
	$f_time	= 希望到着時間

*****************************************/
function F_get_delivery_time( $f_id ){
	global $c_db,$table_arr;
	$arr = array();	
	$c_db->DB_on(DF_db_name) ;
			$sql=sprintf('
					SELECT serial, seller_id, shitei, created 
						from %1$s
					WHERE seller_id like %2$s
						AND del_flg = 0
				;'
					, $table_arr['delivery_shitei']
					, $c_db->DB_q( $f_id )
				) ;
		$res=$c_db->F_mysql_query($c_db->G_DB, $sql);
		!$res && die('到着希望時間更新エラー#');
		while ($row = mysqli_fetch_assoc($res)) {
			$arr[] = $row;
		}
		$c_db->DB_off() ;
		return $arr;
	}

//-----------------------------------------------
// HTML（表示）
//-----------------------------------------------
?>
