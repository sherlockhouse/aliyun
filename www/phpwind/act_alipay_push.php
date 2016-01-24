<?php
define('SCR','act_alipay_push');
require_once('global.php');

S::gp(array('action'));
require_once(R_P.'lib/activity/alipay.php');

$service = $action;
$AlipayInterface = new AlipayInterface($service);

if ($action == 'user_authentication') {//�����֤
	$param = array(
		/* ҵ����� */
		'return_url'		=> "{$db_bbsurl}/act_alipay_receive.php?action=$action",
	);
	ObHeader($AlipayInterface->alipayurl($param));
} elseif ($action == 'confirm_aa_detail_payment') {//����֧��

	S::gp(array('actuid','tid','fromuid','actmid'),GP,2);
	
	$memberdb = $db->get_one("SELECT am.uid,am.username,am.ifpay,am.isrefund,am.out_trade_no,am.totalcash,am.ifanonymous,t.authorid FROM pw_activitymembers am LEFT JOIN pw_threads t USING(tid) WHERE am.actuid=".S::sqlEscape($actuid));

	L::loadClass('ActivityForBbs', 'activity', false);
	$postActForBbs = new PW_ActivityForBbs($data);

	$data = array();

	$memberdb['authorid'] == $winduid && Showmsg('act_toalipay_authorid');//�������޷������֧��
	$isAdminright = $postActForBbs->getAdminRight($memberdb['authorid']);
	if ($memberdb['isrefund'] || $memberdb['ifanonymous'] && !$isAdminright && $memberdb['uid'] != $winduid) {//�˿���޷�֧����������û��Ȩ�޵��޷�֧��
		Showmsg('act_toalipay_error');
	}

	$memberdb['ifpay'] != 0 && Showmsg('act_toalipay_payed');//ֻ��δ֧��״̬�ſ���֧��
	if (!$memberdb['totalcash'] || !preg_match("/^(([1-9]\d*)|0)(\.\d{0,2})?$/", $memberdb['totalcash'])) {//���ô���
		Showmsg('act_toalipay_cash_error');
	}
	$memberdb['totalcash'] = number_format($memberdb['totalcash'], 2, '.', '');//֧�����
	$out_trade_no = $memberdb['out_trade_no'] ? $memberdb['out_trade_no'] : $db_sitehash.'_'.$tid.'_'.$actuid.'_'.generatestr(6);

	$defaultValueTableName = getActivityValueTableNameByActmid();
	$defaultValue = $db->get_one("SELECT out_biz_no,paymethod,iscancel,endtime FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
	$defaultValue['paymethod'] != 1 && Showmsg('act_toalipay_paymethod');//ֻ��֧����ʽΪ֧�����ſ���֧��
	$defaultValue['endtime'] + 30*86400 < $timestamp && Showmsg('act_endtime_toolong');//����ʱ���һ����,>0 ����Բ���,< 0�޷�����
	$defaultValue['iscancel'] == 1 && Showmsg('act_iscancelled_y');//���ȡ���޷�֧��

	$param = array(
		/* ҵ����� */
		'buyer_name'	=> $memberdb['username'],
		'out_biz_no'	=> $defaultValue['out_biz_no'],
		'out_trade_no'	=> $out_trade_no,
		'amount'		=> $memberdb['totalcash'],
		'notify_url'	=> "{$db_bbsurl}/act_alipay_receive.php",
		'return_url'	=> "{$db_bbsurl}/read.php?tid=$tid",
	);
	
	if ($fromuid != '-1') {//�Ƿ����
		$fromusername = $db->get_value("SELECT username FROM pw_members WHERE uid=".S::sqlEscape($fromuid));
		$issubstitute = 1;
	} else {
		$fromuid = $issubstitute = 0;
		$fromusername = '';
	}
	$sqlarray = array(
		'out_trade_no'	=> $out_trade_no,//�ⲿ�������׺�
		'issubstitute'	=> $issubstitute,//�Ƿ����
		'fromuid'		=> $fromuid,//����id
		'fromusername'	=> $fromusername,//�����û���
	);

	$db->update("UPDATE pw_activitymembers SET " . S::sqlSingle($sqlarray)." WHERE actuid=".S::sqlEscape($actuid));
	ObHeader($AlipayInterface->alipayurl($param));
} elseif ($action == 'refund_aa_payment') {//�˿�
	S::gp(array('tid','actuid','actmid'),GP,2);

	$memberdb = $db->get_one("SELECT am.ifpay,am.isrefund,am.username,am.totalcash,am.out_trade_no,am.refundcost,t.authorid FROM pw_activitymembers am LEFT JOIN pw_threads t USING(tid) WHERE am.actuid=".S::sqlEscape($actuid));
	$tempcost = $db->get_value("SELECT SUM(totalcash) as sum FROM pw_activitymembers WHERE isrefund=1 AND fupid=".S::sqlEscape($actuid));//���˷���

	if ($memberdb['isrefund'] || $memberdb['authorid'] != $winduid) {//�˿���޷����������Ƿ������޷�����
		Showmsg('act_refund_noright');
	}

	$memberdb['ifpay'] != 1 && Showmsg('act_refund_error');//֧����֧���ɹ������˿�
	if (!$memberdb['refundcost'] || !preg_match("/^(([1-9]\d*)|0)(\.\d{0,2})?$/", $memberdb['refundcost']) || $memberdb['refundcost'] > number_format(($memberdb['totalcash'] - $tempcost), 2, '.', '')) {//���ô��󡢳���ʣ�����
		Showmsg('act_refund_cash_error');
	}
	$refundcost = number_format($memberdb['refundcost'], 2, '.', '');//�˿���

	$defaultValueTableName = getActivityValueTableNameByActmid();
	$defaultValue = $db->get_one("SELECT user_id,paymethod,endtime FROM $defaultValueTableName WHERE tid=".S::sqlEscape($tid));
	$defaultValue['endtime'] + 30*86400 < $timestamp && Showmsg('act_endtime_toolong');//����ʱ���һ����,>0 ����Բ���,< 0�޷�����
	$defaultValue['paymethod'] != 1 && Showmsg('act_toalipay_paymethod');//֧����֧�������˿�

	$param = array(
		/* ҵ����� */
		'out_trade_no'	=> $memberdb['out_trade_no'],
		'operator_id'	=> $defaultValue['user_id'],
		'refund_fee'	=> $refundcost,
		'notify_url'	=> "{$db_bbsurl}/act_alipay_receive.php",
		'return_url'	=> "{$db_bbsurl}/read.php?tid=$tid",
	);
	ObHeader($AlipayInterface->alipayurl($param));
}

/**
 * ���������
 * @param int $len λ��
 * @param string �����
 */
function generatestr($len) {
	mt_srand((double)microtime()*1000000);
	$keychars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWYXZ";
	$maxlen = strlen($keychars)-1;
	$str = '';
	for ($i=0;$i<$len;$i++){
		$str .= $keychars[mt_rand(0,$maxlen)];
	}
	return substr(md5($str.microtime().$GLOBALS['HTTP_HOST'].$GLOBALS['pwServer']["HTTP_USER_AGENT"].$GLOBALS['db_hash']),0,$len);
}
?>