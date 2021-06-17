<?php
/*******************************
���i��������

2015/08/05	�V�K�쐬	����	
2015/09/07	�@�\�C��	����	�L�����y�[�����e�ύX�Ή�
2015/09/14	�@�\�ǉ�	����	��]���������Ή�
2015/09/17	�@�\�C��	����	�|�C���g�ŃG���[�̍ۂ̎x�������@�s��C��
2015/10/01	�@�\�ǉ�	����	����̎g���Ȃ���БΉ�
2015/10/19	�@�\�ǉ�	����	������ɂ���]���������Ή�
2015/10/28	�ύX		����	������g���Ȃ���Ƃ��烊�u�u���b�W�ƃI�t�B�X�A�����폜
2015/12/02	�@�\�ǉ�	����	�o�X�Ђ��Ƃɓ������w��\�c�Ɠ���ݒ肷��
2015/12/04	�@�\�ǉ�	����	�|�C���g5�{�Ή�
2020/08/24	�@�\�ǉ�	kha	jcb�J�[�h���ϒǉ�
2020/11/02	�@�\�C��	kha	jcb�J�[�h���ώ��̏��i��200�����܂�
2020/12/09  �@�\�ǉ�	���c    �y�̖{20-2675�z�u���b�N���X�g�΍�i�b��j
2020/12/10	�@�\�ǉ�	���R	��]�������ԑI�������o�X�Ђ��ݒ肵�����ԂőI���ł���悤�Ή�

********************************/
session_start();

// ���ʊ֐��ǂݍ���
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_general.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_validate.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_login.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_order.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_buyer.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/function/function_seller.php");
// ���ʒ�`�ǂݍ���
require_once("/var/www/vhosts/c-joy.co.jp/common/define/d_general.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/define/d_buyer.php");
require_once("/var/www/vhosts/c-joy.co.jp/common/define/d_session_key.php");
// �N���X�ǂݍ���
require_once('/var/www/vhosts/c-joy.co.jp/common/class/class_chakubi.php');

//�J�[�h���ρF�ݒ�t�@�C���ǂݍ���
require_once('/var/www/vhosts/c-joy.co.jp/common/class/class_credit_card.php');
require_once('/var/www/vhosts/c-joy.co.jp/common/define/d_creditcard.php');				//�J�[�h���ϑΉ�����defin
require_once('/var/www/vhosts/c-joy.co.jp/common/function/f_creditcard.php');				//�J�[�h���ϑΉ����ʊ֐�

F_check_access();

//-----------------------------------------------
// ��������
//-----------------------------------------------

// ��DB�N���X
include_once("/var/www/vhosts/c-joy.co.jp/common/class/class_db.php");
$c_db=new G_class_db;	//DB�N���X���̉�
$db_name='cjoy';		//DB��
$table_arr = array();
$table_arr['product_gift']	 		= 'cjoy.product_gift';				// ���i�ڍ�
$table_arr['product_b_gift']	 	= 'cjoy.product_b_gift';
$table_arr['buyer_member'] 			= 'cjoy.buyer_member';			// ���������o�^���
$table_arr['buyer_payment'] 		= 'cjoy.buyer_payment';			// ���������x�����
$table_arr['buyer_dest'] 			= 'cjoy.buyer_destination';		// �����������t����
$table_arr['buyer_point'] 			= 'cjoy.buyer_point';			// ������ۗL�|�C���g���
$table_arr['common_campaign']		= 'cjoy.common_campaign';		// �L�����y�[�����
$table_arr['order_campain']			= 'cjoy.order_campain';
$table_arr['delivery_shitei']		= 'cjoy.delivery_shitei';
//-----------------------------------------------
// �ϐ��E�萔
//-----------------------------------------------

//���������ɂȂ����
define('DF_delivery_order_limit', 3000);
define('DF_discount_amout', 500);
define('DF_cool_bin', 500);

// �ϐ�
$D_main = '';			// ���C���\��
$D_mess = '';			// ��ƃ��b�Z�[�W

// ����o�^��ʗp
$D_option = '';
$D_button = '';

// �J�[�h�p
$D_card_table = '';

$no_seller_flg = 1;
// �o���f�[�V�����p��`
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

// post�l�̓��{�ꖼ�i�o���f�[�V�����p�j
$A_post_name = array(
	'p_dest_name'		=> '���O',
	'p_dest_zip'		=> '�X�֔ԍ�',
	'p_dest_add2'		=> '�s�撬����',
	'p_dest_add3'		=> '�Ԓn�A������',
	'p_dest_tel'		=> '�d�b�ԍ�',
);
$A_post_name_wrap = array(
	'p_noshi_up'		=> '�̂���i(�\����)',
	'p_noshi_btm'		=> '�̂����i',
);

// ����̎g���Ȃ������ID
$A_unavailable_yamato = array(
);

$D_order_notice = '
	<div style="color:#C00; margin-top:15px; font-size:13px;">
		�u���E�U�� [�߂�] �� [�X�V] �{�^���͎g�p���Ȃ��ł��������B<br />
		�\�����Ȃ��G���[�̌����ɂȂ�܂��B
	</div>
';

//XSS�΍� $_REQUEST $_GET $_POST�̒l���G�X�P�[�v����
isset($_REQUEST) && $_REQUEST = F_change_safe_array($_REQUEST);
isset($_GET) && $_GET = F_change_safe_array($_GET);
isset($_POST) && $_POST = F_change_safe_array($_POST);
//-----------------------------------------------
// ���N�G�X�g�l�󂯓n��
//-----------------------------------------------
FM_array_trim($_POST);

//��Ǝ� 
$p_kind = 'top';		
isset($_REQUEST['p_kind']) && $p_kind = $_REQUEST['p_kind'];
// ���i�V���A��
$p_serial = 0;
isset($_REQUEST['p_serial']) && $p_serial = $_REQUEST['p_serial'];
// ���O�C��ID
$p_buyer_id = '';
isset($_POST['p_buyer_id']) && $p_buyer_id = $_POST['p_buyer_id'];
// ���O�C���p�X���[�h
$p_buyer_password = '';
isset($_POST['p_buyer_password']) && $p_buyer_password = $_POST['p_buyer_password'];
// �����i�ݒ�
$p_gift_flg = 0;
isset($_POST['p_gift_flg']) && $p_gift_flg = (int)$_POST['p_gift_flg'];

// ����o�^�l
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
$p_buyer_age = '�I�����Ă�������';
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

// ���ʊ֘A
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

// �x��
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

// �̔��X�ւ̗v�]�Ȃ�
$p_order_comment = '';
isset($_POST['p_order_comment']) && $p_order_comment = $_POST['p_order_comment'];


//-----------------------------------------------
// BL�`�F�b�N
//-----------------------------------------------
$err_message = '';
if ($p_kind == 'order_confirm') {
	$bl_array = [
		['�d�b�ԍ�','�Z��'],
		['07040816098','��ΌS���ɓ������c451-1'],
		['xxxx','���s�S��1461-1�I�[�x������R406'],
		['xxxx','���R�s�t����4-21'],
		['xxxx','���l�s����˕���2-18-5���C�v���q���Y21-302'],
		['08075465244','���V�S���V������342-197'],
		['xxxx','���s���831�Z�U�[�������K�[�f��505'],
		['xxxx','�n�c�擌����5-41-18���C�I���Y�K�[�f��������907'],
		['xxxx','�ɓ�s�h��2-38�Z���g�����n�C�c302'],
		['xxxx','���n������u3-7-1-1206'],
		['07042951401','������s�l����15-34-506'],
		['xxxx','���Ð�s���쒬3-61']
	];

	foreach($bl_array as $koko){
		if(($koko[0] == $_SESSION[DF_sessionkey_member_info]['buyer_tel'])||
		   (false !== strpos($_SESSION[DF_sessionkey_member_info]['buyer_address2'], $koko[1]))){
			$err_message = '���݁A���i�����w�����邱�Ƃ��ł��܂���B�ڍׂɂ��Ă͂��₢���킹�y�[�W��肨�₢���킹�肢�܂��B';
			$p_kind = 'payment';
			break;
		}
	}
}

//-----------------------------------------------
// ��Ǝ�ʏ�����
//-----------------------------------------------
/*********** �T�v�N�G�� ***********/
if ($p_kind == 'subquery') {
	header("Content-type: text/plain; charset=shift_jis");
	// UTF8��POST�����̂�shift_jis�ɖ߂�
	foreach ($_POST as $key => $val) {
		$_POST[$key] = mb_convert_encoding($val, 'SJIS', 'UTF-8');
	}
	
	$buyer_serial=$_SESSION[DF_sessionkey_member_info]['buyer_serial'];
	
	if ($_POST['p_dest_kind'] == 'new') {
		
		$count = count(@$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
		// ����`�F�b�N
		if ($count >= DF_member_dest_count) {
			$D_mess = '�E�o�^�\�Ȃ��͂���́A'.DF_member_dest_count.'�܂łƂȂ�܂��B';
			echo $D_mess;
			exit;
		}
		
		$error_arr = FM_validate($_POST, $A_validate, $A_post_name);
		// �G���[
		if (! empty($error_arr)) {
			$D_mess = implode("<br>", $error_arr);
			echo $D_mess;
			exit;
		}
		
		$serial=FM_ins_buyer_destination($buyer_serial, $_POST);
		if($serial===false){
			echo '�V�X�e���G���[�������܂����B���P';
			exit;
		}
		
		// ���`���ăZ�b�V�����ɒǉ�
		$new_dest = array(
			'serial' => $serial,
			'dest_name' => $_POST['dest_name'], 
			'dest_zip' => $_POST['dest_zip'], 
			'dest_address1' => $_POST['dest_add1'], 
			'dest_address2' => $_POST['dest_add2'].$_POST['dest_add3'], 
			'dest_tel' => $_POST['dest_tel'], 
			'need' => 1, 
			'edit' => '',
			'wrapping_word' => '���b�s���O�E�̂�����',
			'wrapping_set' => array(
									'wrapping' => 0,
									'noshi' => 3, 
									'noshi_upper' => '',
									'noshi_bottom' => '',
								),
			'arrival_word' => '��]������: �w��Ȃ��@���Ԏw��: �w��Ȃ�',
			'kibou_datetime' => array(
									'kibou_date' => '�w��Ȃ�',
									'kibou_time' => '�w��Ȃ�',
								),
		);
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$serial] = $new_dest;
		
		echo 'OK';
		exit;
		
	} else if ($_POST['p_dest_kind'] == 'edit') {
		$error_arr = FM_validate($_POST, $A_validate, $A_post_name);
		// �G���[
		if (! empty($error_arr)) {
			$D_mess = implode("<br>", $error_arr);
			echo $D_mess;
			exit;
		}
		
		$rtn=FM_update_buyer_destination($_POST);
		if($rtn===false){
			echo '�V�X�e���G���[�������܂����B���Q';
			exit;
		}
		
		$num = (int)$_POST['serial'];
		// ���큨�Z�b�V�����C��
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
			echo '�V�X�e���G���[�������܂����B���R';
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
		
	//===========���b�s���O�ݒ�ҏW============//
	} else if ($_POST['p_dest_kind'] == 'wrap') {
		
		$error_arr = FM_validate($_POST, $A_validate_wrap, $A_post_name_wrap);
		if ((int)$_POST['p_wrapping_set'] == 2 && (int)$_POST['p_noshi_set'] == 3 && empty($_POST['p_noshi_up'])) {
			$error_arr[] = '�E��i�̂̂��ݒ����͂��Ă��������B';
		}
		// �G���[
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
		// ���큨�Z�b�V�����C��
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_word'] = $A_wrapping_word[$p_wrapping_set];
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['wrapping'] = $p_wrapping_set;
		// �̂��ݒ肠��
		if ( $p_wrapping_set == 2) {
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi'] = $p_noshi_set;
			if ($p_noshi_set == 1) {
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'] = '���Ε�';
			} else if ($p_noshi_set == 2) {
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'] = '������';
			} else if ($p_noshi_set == 3) {
				$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'] = $p_noshi_up;
			}
			// �����ǉ�
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_word'] .= '�i��i�F'.$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'];
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_bottom'] = $p_noshi_btm;
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_word'] .= '�@���i�F'.($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_bottom']?$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_bottom']:'���L��').'�j';		
		// �̂��Ȃ��A������
		} else {
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_upper'] = '';
			$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['wrapping_set']['noshi_bottom'] = '';
		}
		echo 'OK';
		exit;
		
	//===========��]���������ύX============//
	} else if ($_POST['p_dest_kind'] == 'arrival') {
		
		$num = (int)$_POST['p_arrival_number'];
		$dst_kibou_date = $_POST['p_dest_kibou_date'];
		$dst_kibou_time = $_POST['p_dest_kibou_time'];
		
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['kibou_datetime']['kibou_date'] = $dst_kibou_date;
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['kibou_datetime']['kibou_time'] = $dst_kibou_time;
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$num]['arrival_word'] = '��]������: '.$dst_kibou_date.'�@���Ԏw��: '.$dst_kibou_time;

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
/*********** �ŏI�m�F ***********/
if ($p_kind == 'order_confirm') {
	
	$seller_id = $_POST['seller_id'];
	if(empty($_SESSION[DF_order_gift_cart][$seller_id])){
		header('Location: /gift_order.php?p_kind=login_check');
		exit;
	}
	
	/*
	//������͂���ύX�̏ꍇ
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
	// ����p�ݒ�
	if (isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]) && ! isset($_POST['p_forhome'])) {
		$p_forhome = $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_forhome'];
		$p_home_quantity = $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_home_setting]['p_home_quantity'];
	}
	$total_count = 0;
	$noshi_count = 0;
	$wrap_count = 0;
	// ���ʊm�F
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
	
	// �̂��E���b�s���O�A���v���z���v�Z
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
	
	//�M�t�g�̈���
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
	
	// �ŏI�݌Ƀ`�F�b�N	
	foreach($_SESSION[DF_order_gift_cart][$seller_id] as $item_serial=>$item_arr){
		$p_type=$item_arr['p_type'];
		$A_item_new = FM_get_product_item_gift($item_serial, $p_type);
		$total_count += $item_arr['quantity'];
		if ($item_arr['zaiko_flg']==1 && (int)$A_item_new['zaiko'] < $item_arr['quantity']) {
			$D_mess .= '<span style="color:red;">���݂̍݌ɐ��������߂̐��ʂ������܂����B���݂̍݌ɁF���� '.$A_item_new['zaiko'].'��</span>';
		}
	}
	
	/*
	// �|�C���g���擾
	$buyer_point = FM_get_buyer_point($buyer_id);
	// �g�p�\�Ȃ��ׂẴ|�C���g���g��
	if ($p_select_point == 1) {
		$p_use_point = $buyer_point;
		if ($sum_price < $p_use_point) $p_use_point = $sum_price;
	// �g���|�C���g�����߂�
	} else {
		if ($buyer_point < $p_use_point) $p_use_point = $buyer_point;
		if ($sum_price < $p_use_point) $p_use_point = $sum_price;
	}*/
	
	// �ŏI�݌Ƀ`�F�b�N
	/*$A_item_new = FM_get_product_item($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['product_serial']);
	if ((int)$A_item_new['zaiko'] < $total_count) {
		$D_mess = '<span style="color:red;">���݂̍݌ɐ��������߂̐��ʂ������܂����B���݂̍݌ɁF���� '.$A_item_new['zaiko'].'��</span>';
		$p_kind = 'shipping';
	} else if ($p_use_point % 500 != 0) {
		$p_use_point -= ($p_use_point % 500);
		$D_mess = '<span style="color:red;">���g�p�ɂȂ��|�C���g��500�|�C���g�P�ʂƂȂ�܂��B</span>';
		$p_kind = 'payment';
	} else if ($p_payment == 0 && $sum_price <= $p_use_point) {
		$D_mess = '<span style="color:red;">�N���W�b�g�J�[�h���ς̏ꍇ�A�S�z���|�C���g�ł��x�����ɂ͂Ȃ�܂���B</span>';
		$p_kind = 'payment';
	}else {
		
		// �擾�|�C���g�������Ōv�Z����
		$t_add_point = 0;
		$move_kind = '';
		$target_amount = $sum_price - $p_use_point;
		$magnification = 1;
		// ����o�^���i�K�p�L�����y�[�����擾����֐����g���Ȃ��j
		if (!empty($member_regist_arr)) {
			// �Ώۋ��z�����������
			$target_amount -= DF_member_price;
			// ���Ҏ҂��ǂ�������
			$t_campaign = FM_get_campaign_data();
			// �Љ�L�����y�[�����ŁA���҃R�[�h�̓��͂������(������w���ɓK�p�����̂̓L�����y�[�����ԓ�����)
			if (isset($t_campaign[DF_invite_campaign]) && !empty($member_regist_arr['p_invited'])) {
				// �Љ�����T���āA�Y��������A�o�^���L�����y�[�����ԓ��Ȃ�
				$t_invite_member = FM_get_buyer_from_invcode($member_regist_arr['p_invited']);
				if (! empty($t_invite_member) && (date('Y-m-d') <= $t_campaign[DF_invite_campaign]['campaign_end'])) {
					$t_add_point = floor($target_amount / 100) * $t_campaign[DF_invite_campaign]['percentage'];
					$move_kind = $t_campaign[DF_invite_campaign]['move_kind'];
					$magnification = $t_campaign[DF_invite_campaign]['percentage'];
				}
			}
		} else {
			// �Y���L�����y�[���擾
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
		
		// �Z�b�V�����ɃI�[�_�[�ɕK�v�Ȑ��l�i�[
		$_SESSION[DF_ichiba_name]['post'] = array();
		$_SESSION[DF_ichiba_name]['post']['p_count'] = $total_count;			// �w�������v
		$_SESSION[DF_ichiba_name]['post']['p_price'] = $sum_price;				// ���z���v(�N���A�̂��A���b�s���O�܂�)
		$_SESSION[DF_ichiba_name]['post']['p_usepoint'] = $p_use_point;			// �g�p�|�C���g
		$_SESSION[DF_ichiba_name]['post']['p_getpoint'] = $t_add_point;			// �擾�|�C���g
		$_SESSION[DF_ichiba_name]['post']['noshi_price'] = $noshi_price;		// �̂��̉��i
		$_SESSION[DF_ichiba_name]['post']['wrapping_price'] = $wrapping_price;	// ���b�s���O�̉��i
		$_SESSION[DF_ichiba_name]['post']['move_kind'] = $move_kind;			// �擾�|�C���g���
		$_SESSION[DF_ichiba_name]['post']['percentage'] = $magnification;		// �擾�|�C���g����(�w�����z��?%)
		
		if ($p_payment == 0) {
			// �J�[�h���ϊm�F
			$D_payment = FM_make_cardtable($buyer_id, 0, DF_ichiba_name);
		} else {
			$D_payment = $DF_payment[$p_payment];
		}

		require_once './tpl/confirm_order_tpl.php';
		exit;
	}*/
	
	// �Z�b�V�����ɃI�[�_�[�ɕK�v�Ȑ��l�i�[
	$_SESSION[DF_ichiba_name]['post'] = array();
	$_SESSION[DF_ichiba_name]['post']['p_count'] = $total_count;			// �w�������v
	$_SESSION[DF_ichiba_name]['post']['item_price'] = $_SESSION[DF_seller_info][$seller_id]['sub_total'];
	$_SESSION[DF_ichiba_name]['post']['p_price'] = $sum_price;				// ���z���v(�N���A�̂��A���b�s���O�܂�)
	$_SESSION[DF_ichiba_name]['post']['p_usepoint'] = 0;			// �g�p�|�C���g
	$_SESSION[DF_ichiba_name]['post']['p_getpoint'] = 0;			// �擾�|�C���g
	$_SESSION[DF_ichiba_name]['post']['noshi_price'] = $noshi_price;		// �̂��̉��i
	$_SESSION[DF_ichiba_name]['post']['wrapping_price'] = $wrapping_price;	// ���b�s���O�̉��i
	$_SESSION[DF_ichiba_name]['post']['move_kind'] = '';			// �擾�|�C���g���
	$_SESSION[DF_ichiba_name]['post']['percentage'] = 1;		// �擾�|�C���g����(�w�����z��?%)
	$_SESSION[DF_ichiba_name]['post']['postage_total']=$_SESSION[DF_seller_info][$seller_id]['delivery_fee']+$gift_delivery_fee_total;
	$_SESSION[DF_ichiba_name]['post']['cool_bin_fee']=$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']+$gift_cool_bin_fee_total;
	$_SESSION[DF_ichiba_name]['post']['discount_amount']=$_SESSION[DF_seller_info][$seller_id]['discount_amount'];
	$_SESSION[DF_ichiba_name]['post']['p_order_comment']=$p_order_comment;
	
	/*if ($p_payment == 0) {
		// �J�[�h���ϊm�F
		$D_payment = FM_make_cardtable($buyer_id, 0, DF_ichiba_name);
	} else {
		$D_payment = $DF_payment[$p_payment];
	}*/
	
	$D_payment = $DF_payment[$p_payment];
	require_once './tpl/gift_confirm_order_tpl.php';
	exit;
	
}

/*********** ���x���m�F ***********/
if ($p_kind == 'payment') {
	
	$seller_id = $_REQUEST['seller_id'];
	
	/*if(empty($_SESSION[DF_order_gift_cart][$seller_id])){
		header('Location: /gift_order.php?p_kind=login_check');
		exit;
	}*/
	
	$member_regist_arr = array();
	$buyer_name = '�Q�X�g';
	$current_seller_id = $_REQUEST['seller_id'];
	// ���
	$buyer_name = $_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'];
	$buyer_id = $_SESSION[DF_sessionkey_member_info]['buyer_id'];
	$buyer_serial = $_SESSION[DF_sessionkey_member_info]['buyer_serial'];
	$buyer_info = $_SESSION[DF_sessionkey_member_info];
	if (empty($buyer_info)) {
		// �G���[
		FM_view_err_page('�Z�b�V�����G���[���������܂����B<br>�u���E�U�̖߂�{�^���͎g�p���Ȃ��ł��������B<br>���萔�ł���������x���葱�������肢�������܂��B', '���i�ڍ׃y�[�W');
		exit;
	}
	$buyer_tel = $buyer_info['buyer_tel'];
	$buyer_zip = $buyer_info['buyer_zip'];
	$buyer_pref = $buyer_info['buyer_address1'];
	$buyer_add = $buyer_info['buyer_address2'];
	
	//unset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
	
	// ���t��󋵎擾
	/*if (! isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest])) {
		$buyer_dest = FM_get_buyer_destination($buyer_serial);
		$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest] = $buyer_dest;
	}*/
	//print_r($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
	
	// �����h���b�v�_�E��
	$D_prefectures = FM_make_droplist($DF_ken_arr, 'buyer_pref', $buyer_pref, ' id="buyer_pref"');
	$D_prefectures2 = FM_make_droplist($DF_ken_arr, 'new_dest_add1', "", ' id="edit_dest_add1"');
	
	// �����h���b�v�_�E�����X�g
	$c_chakubi = new G_class_chakubi($_SESSION[DF_seller_info][$seller_id]['shipping_prepare']);
	$t_chakustart_date = $c_chakubi->F_class_chakubi_get_shonichi();
	
	$_SESSION[DF_seller_info][$seller_id]['kibou_date'] = $c_chakubi->F_class_chakubi_make_chaku_ddl($t_chakustart_date, 'p_kibou_date', $p_kibou_date);
	$delivery_shitei_info = F_get_delivery_time($_SESSION['seller_info'][$seller_id]['seller_id']);
	if(!empty($delivery_shitei_info)){
		// �w��\������]���ԑ�
		$A_kibou_time = array();
		$A_kibou_time[]='�w��Ȃ�';
		foreach($delivery_shitei_info as $key => $value){
			$A_kibou_time[] = $value['shitei'];
		}
	}
	$_SESSION[DF_seller_info][$seller_id]['kibou_time'] = FM_make_droplist($A_kibou_time, 'p_kibou_time', $p_kibou_time, ' id="p_kibou_time"');	
	
	
	// �����h���b�v�_�E�����X�g
	$D_kibou_date_dest = $c_chakubi->F_class_chakubi_make_chaku_ddl($t_chakustart_date, 'edit_kibou_date', '�w��Ȃ�');
	$D_kibou_time_dest = FM_make_droplist($A_kibou_time, 'edit_kibou_time', "�w��Ȃ�", ' id="edit_kibou_time"');		
		
	
	// �����p�łȂ��Ȃ�A���t��̓��Z�b�g
	/*if ($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_gift_setting] == 0) {
		unset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
	}*/
	for(;;) {
		$total_count = 0;
		$noshi_count = 0;
		$wrap_count = 0;
		// ���ʊm�F
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
		
		// JCB�̏��i���A���i�R�[�h
		$product_name=join(',',$temp_item);
		$product_code=join(',',$temp_code);
		$goods_name=mb_strlen($item_arr['product_name'], 'sjis')>200?mb_substr($item_arr['product_name'],0,190,'sjis'):$item_arr['product_name'];
		if(count($_SESSION[DF_order_gift_cart][$seller_id])>1){
			$goods_name .='�@��';
		}
		
		//�M�t�g�̈���
		$dest_arr = array();
		if (isset($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest])) {
			$dest_arr = $_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest];
			$p_dest_size = count($_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest]);
		}
		
		//�M�t�g�̑����A�N�[���ցA���b�s���O�E�̂���
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
				
				// ����
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
				
				// �N�[����
				if($item_arr['cool_bin_flg']==1){
					@$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['gift_cool_bin_fee']=DF_cool_bin;
					$gift_cool_bin_fee_total +=DF_cool_bin;
				}
				
				//���b�s���O
				if ($dat['wrapping_set']['wrapping'] == 1) {
					$_SESSION[DF_sessionkey_order_gift_cart][DF_sessionkey_buyer_dest][$key]['wrapping_price']=$item_arr['wrapping_price'];
					$gift_wrapping_price_total +=$item_arr['wrapping_price'];
				}
				
				//�̂�
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
		
		
		
		//�����莩��̏��
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
		
		//�����著��
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
			
			// ������N�[����
			if($item_arr['cool_bin_flg']==1){
				$_SESSION[DF_seller_info][$seller_id]['cool_bin_fee']=DF_cool_bin;
				$_SESSION[DF_seller_info][$seller_id]['total']+=DF_cool_bin;
			}
			
		}
		
		//�����m�F
		if($delivery_flg=='not_match'){
			$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='not_match';
		}else{
			$_SESSION[DF_seller_info][$seller_id]['delivery_flg']='';
		}
		
		if ($total_count < 1) {
			$D_mess = '<span style="color:red;">�����߂̐��ʂ��m�F���Ă��������B</span>';
			$p_kind = 'shipping';
			break;
		}
		//$A_item_new = FM_get_product_item($_SESSION[DF_order_gift_cart][$seller_id][$item_serial]['product_serial']);
		/*if ((int)$A_item_new['zaiko'] < $total_count) {
			$D_mess = '<span style="color:red;">���݂̍݌ɐ��������߂̐��ʂ������܂����B���݂̍݌ɁF���� '.$A_item_new['zaiko'].'��</span>';
			$p_kind = 'shipping';
			break;
		}*/
		
		
		// �J�[�h���ύ��v���z
		$sum_price = 0;
		$item_price = $_SESSION[DF_seller_info][$seller_id]['total'];
		$delivery_fee=$_SESSION[DF_seller_info][$seller_id]['delivery_fee'];
		$sum_price = (int)$item_price + (int)$gift_noshi_price_total + (int)$gift_wrapping_price_total+ (int)$gift_delivery_fee_total+ (int)$gift_cool_bin_fee_total;
		/*if (!empty($member_regist_arr)) {                                                            
			$sum_price += (int)DF_member_price;
		}*/
		
		// �J�[�h���ϊm�F
		$credit_card = new G_class_credit_card();
		$credit_card->FC_set_member($buyer_id);	//������id���Z�b�g

		$flg_card_member = $credit_card->FC_is_card_member();//KaiinId���Z�b�g����A�Ԃ�l�Ɋւ��Ă͋C�ɂ��Ȃ�����
		$D_card_table = FM_make_cardtable($buyer_id, 1, DF_ichiba_name);
		
		// jcb�J�[�h���ς̏ꍇ
		$flg_card_member_jcb = true;
		$authentication_key=substr(md5($buyer_id),0,8);
		$order_no=time();
		
		//jcb�J�[�h������m�F
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
		
		// �|�C���g���擾
		$buyer_point = FM_get_buyer_point($buyer_id);
		require_once './tpl/gift_payment_order_tpl.php';
		exit;
	}
}
/*********** ��� ���O�C������ ***********/
if ($p_kind == 'member_login') {
	// �蓮���O�C���`�F�b�N
	$FB_is_auto = false;
	if (isset($_POST['p_buyer_id']) && isset($_POST['p_buyer_password'])) {
		$ret = FM_auth($p_buyer_id, $p_buyer_password, $tmp_name);
		if ($ret === true) {
			// �Z�b�V�����l�ɔ������������
			$_SESSION[DF_sessionkey_member_info]['buyer_id'] = $p_buyer_id;
			$_SESSION[DF_sessionkey_member_info]['buyer_name'] = $tmp_name;
			$_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'] = $tmp_name;
			// �������O�C���Z�b�g
			FM_setup_login($p_buyer_id);
			$FB_is_auto = true;
		}
	}
	if ($FB_is_auto) {
		$A_member_info = FM_get_buyer_member($p_buyer_id);
		
		// ������擾
		foreach ($A_member_info as $key => $info) {
			if (!isset($_SESSION[DF_sessionkey_member_info][$key])) {
				$_SESSION[DF_sessionkey_member_info][$key] = $info;
			}
		}
		$_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'] = $_SESSION[DF_sessionkey_member_info]['buyer_name'];
		
		if ((int)$A_member_info['buyer_reg_status'] == 0) {
			$p_kind = 'cart_summary';
		} else {
			$D_mess = '<span style="color:red;">���N���̂��x�������m�F�����܂œ�x�ڂ̂��w���͂ł��܂���B</span>';
			// ��ʕ\��
			require_once './tpl/gift_login_order_tpl.php';
			exit;
		}
	} else {
		$D_mess = '<span style="color:red;">�����O�C��ID�܂��̓p�X���[�h���Ⴂ�܂��B</span>';
		// ��ʕ\��
		require_once './tpl/gift_login_order_tpl.php';
		exit;
	}
}



/*********** �����g�b�v ���O�C�� ***********/
if ($p_kind == 'top') {
	if(@$_SESSION[DF_sessionkey_member_info]['buyer_id']){
		$A_member_info = FM_get_buyer_member($_SESSION[DF_sessionkey_member_info]['buyer_id']);
		// ������擾
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
		// �G���[
		FM_view_err_page('���T���̏��i��������܂���B', '���i�ڍ׃y�[�W', 'notfound');
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
	
	//�J�[�g�Z�b�V����
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
	
	// ���O�C���`�F�b�N
	$FB_is_auto = false;
	if (isset($_SESSION[DF_sessionkey_member_info]['buyer_name'])) {
		$FB_is_auto = true;
	} else {
		// ���O�C�������`�F�b�N
		if (!empty($_COOKIE['auto_login'])) {
			
			$ret = FM_check_auto_login($_COOKIE['auto_login'], $tmp_id, $tmp_name);
			if ($ret === true) {
				$FB_is_auto = true;
				// �Z�b�V�����l�ɔ������������
				$_SESSION[DF_sessionkey_member_info]['buyer_id'] = $tmp_id;
				$_SESSION[DF_sessionkey_member_info]['buyer_name'] = $tmp_name;
				$_SESSION[DF_sessionkey_member_info]['delivery_buyer_name'] = $tmp_name;
				// �������O�C���Z�b�g
				FM_setup_login($tmp_id, $_COOKIE['auto_login']);
				
				$A_member_info = FM_get_buyer_member($_SESSION[DF_sessionkey_member_info]['buyer_id']);
				// ������擾
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
			$D_mess = '<span style="color:red;">���N���̂��x�������m�F�����܂œ�x�ڂ̂��w���͂ł��܂���B</span>';
			// ��ʕ\��
			require_once './tpl/gift_login_order_tpl.php';
			exit;
		}
	} else {
		
		// ��ʕ\��
		require_once './tpl/gift_login_order_tpl.php';
		exit;
	}
}


/*********** �J�[�g ***********/
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
// �֐���`
//-----------------------------------------------

//XSS�΍� �A�z�z���value���G�X�P�[�v
function F_change_safe_array($array) { 
    $_array = array();
    foreach ($array as $key => $val) {
        $_array[$key] = FM_change_safe_html($val); //FM_H() ��htmlspecialchars�֐���������
    }
    return $_array;
}

/*****************************************
��]�������Ԉꗗ�擾
	$f_id	= ID
	$f_time	= ��]��������

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
		!$res && die('������]���ԍX�V�G���[#');
		while ($row = mysqli_fetch_assoc($res)) {
			$arr[] = $row;
		}
		$c_db->DB_off() ;
		return $arr;
	}

//-----------------------------------------------
// HTML�i�\���j
//-----------------------------------------------
?>
